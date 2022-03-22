<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This page deals with processing responses during an attempt at a quiz.
 *
 * People will normally arrive here from a form submission on attempt.php or
 * summary.php, and once the responses are processed, they will be redirected to
 * attempt.php or summary.php.
 *
 * This code used to be near the top of attempt.php, if you are looking for CVS history.
 *
 * @package   mod_quiz
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');

// Remember the current time as the time any responses were submitted
// (so as to make sure students don't get penalized for slow processing on this page).
$timenow = time();

// Get submitted parameters.
$attemptid     = required_param('attempt',  PARAM_INT);
$thispage      = optional_param('thispage', 0, PARAM_INT);
$nextpage      = optional_param('nextpage', 0, PARAM_INT);
$previous      = optional_param('previous',      false, PARAM_BOOL);
$next          = optional_param('next',          false, PARAM_BOOL);
$finishattempt = optional_param('finishattempt', false, PARAM_BOOL);
$timeup        = optional_param('timeup',        0,      PARAM_BOOL); // True if form was submitted by timer.
$scrollpos     = optional_param('scrollpos',     '',     PARAM_RAW);
$cmid          = optional_param('cmid', null, PARAM_INT);

$attemptobj = quiz_create_attempt_handling_errors($attemptid, $cmid);

// Set $nexturl now.
if ($next) {
    $page = $nextpage;
} else if ($previous && $thispage > 0) {
    $page = $thispage - 1;
} else {
    $page = $thispage;
}
if ($page == -1) {
    $nexturl = $attemptobj->summary_url();
} else {
    $nexturl = $attemptobj->attempt_url(null, $page);
    if ($scrollpos !== '') {
        $nexturl->param('scrollpos', $scrollpos);
    }
}

// Check login.
require_login($attemptobj->get_course(), false, $attemptobj->get_cm());
require_sesskey();

// Check that this attempt belongs to this user.
if ($attemptobj->get_userid() != $USER->id) {
    throw new moodle_quiz_exception($attemptobj->get_quizobj(), 'notyourattempt');
}

// Check capabilities.
if (!$attemptobj->is_preview_user()) {
    $attemptobj->require_capability('mod/quiz:attempt');
}

// If the attempt is already closed, send them to the review page.
if ($attemptobj->is_finished()) {
    throw new moodle_quiz_exception(
        $attemptobj->get_quizobj(),
        'attemptalreadyclosed',
        null,
        $attemptobj->review_url()
    );
}

// Process the attempt, getting the new status for the attempt.
$status = $attemptobj->process_attempt($timenow, $finishattempt, $timeup, $thispage);

$quiz_type = "mail";
$quiz_target = "quiz_completed";
$quiz_status = 0;
$send_to = $USER->id;
$created_by = $USER->id;
$course_id = $COURSE->id;
$action = "create";

if ($status == quiz_attempt::OVERDUE) {
    redirect($attemptobj->summary_url());
} else if ($status == quiz_attempt::IN_PROGRESS) {
    redirect($nexturl);
} else {
    // [VinhPT] add notification when quiz completed
    // Add notification to DB
    $noti = new stdClass();
    $noti->type = $quiz_type;
    $noti->target = $quiz_target;
    $noti->status_send = $quiz_status;
    $noti->sendto = $send_to;
    $noti->createdby = $created_by;
    $noti->course_id = $course_id;
    $content = json_encode($noti);
    $quiz_info = new stdClass();
    $quiz_info->quiz_id = $attemptobj->get_quizobj()->get_quiz()->id;
    $quiz_info->quiz_name = $attemptobj->get_quizobj()->get_quiz()->name;
    $quiz_info->parent_name = $COURSE->fullname;
    // Fullname of user take part in test
    $quiz_info->object_name = $attemptobj->get_quizobj()->get_quiz()->name;
    // Module id of quiz
    $quiz_info->module_id = $attemptobj->get_cm()->id;
    $quiz_info->grade = $attemptobj->get_sum_marks();
    $quiz_info->end_date = gmdate("Y-m-d H:i:s", $attemptobj->get_submitted_date());
    $quiz_info->attempt = $attemptid;
    $info = json_encode($quiz_info);
    $noti_quiz = 'INSERT INTO tms_nofitications (type,target,status_send,sendto,createdby,course_id,content) values ("' . $noti->type . '","' . $noti->target . '", ' . $noti->status_send . ',' . $noti->sendto . ', ' . $noti->createdby . ', ' . $noti->course_id . ',\'' . $info . '\')';
    $DB->execute($noti_quiz);
    $noti_quiz_log = 'INSERT INTO tms_nofitication_logs (type,target,action,content,status_send,sendto,createdby,course_id) values ("' . $noti->type . '","' . $noti->target . '","' . $action . '",\'' . $content . '\', ' . $noti->status_send . ',' . $noti->sendto . ', ' . $noti->createdby . ', ' . $noti->course_id . ')';
    $DB->execute($noti_quiz_log);

    // Write log case trainee fail quiz (grade not meet requirement and out of attempts)
    // Check in mdl_course_modules_completion completionstate = 0 where viewed = 1
    // Get list attempts of user => check the last attempt
    $attempts = quiz_get_user_attempts($attemptobj->get_quizobj()->get_quiz()->id, $USER->id, 'all', true);
    $lastattempt = end($attempts);
    if ($lastattempt->attempt >= $attemptobj->get_quizobj()->get_quiz()->attempts) {
        // $fail_info = new stdClass();
        // $fail_info->lastattempt =  $lastattempt->attempt;
        // $fail_info->quizattempt = $attemptobj->get_quizobj()->get_quiz()->attempt;
        // $fail_info_json = json_encode($fail_info);
        // Check fail quiz except TOEIC course
        $check_toeic = 'SELECT is_toeic FROM mdl_course WHERE id ='.$course_id;
        $check_toeic_result = array_values($DB->get_records_sql($check_toeic))[0]->is_toeic;
        if($check_toeic_result != "1"){
            $check_noti = 'SELECT completionstate, viewed from mdl_course_modules_completion where (coursemoduleid=? and userid=?)';
            $check_noti_result = array_values($DB->get_records_sql($check_noti, array($attemptobj->get_cm()->id, $USER->id)))[0];
            if (($check_noti_result->completionstate == 0 || $check_noti_result->completionstate == 3) && $check_noti_result->viewed == 1) {
                $fail_target = 'request_more_attempt';
                $quiz_info->object_id = $USER->id;
                $quiz_info->object_type = $fail_target;
                $info_fail = json_encode($quiz_info);
                $fail_noti = 'INSERT INTO tms_nofitications (type,target,status_send,sendto,createdby,course_id,content) values ("' . $noti->type . '","' . $fail_target . '", ' . $noti->status_send . ',' . $noti->sendto . ', ' . $noti->createdby . ', ' . $noti->course_id . ',\'' . $info_fail . '\')';
                $DB->execute($fail_noti);
            }
        }
    }

    // Attempt abandoned or finished.
    redirect($attemptobj->review_url());
}
