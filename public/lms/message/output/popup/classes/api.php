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
 * Contains class used to return information to display for the message popup.
 *
 * @package    message_popup
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_popup;

defined('MOODLE_INTERNAL') || die();

/**
 * Class used to return information to display for the message popup.
 *
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api
{
    /**
     * Get popup notifications for the specified users. Nothing is returned if notifications are disabled.
     *
     * @param int $useridto the user id who received the notification
     * @param string $sort the column name to order by including optionally direction
     * @param int $limit limit the number of result returned
     * @param int $offset offset the result set by this amount
     * @return array notification records
     * @throws \moodle_exception
     * @since 3.2
     */
    public static function get_popup_notifications($useridto = 0, $sort = 'DESC', $limit = 0, $offset = 0)
    {
        global $DB, $USER;

        $sort = strtoupper($sort);
        if ($sort != 'DESC' && $sort != 'ASC') {
            throw new \moodle_exception('invalid parameter: sort: must be "DESC" or "ASC"');
        }

        if (empty($useridto)) {
            $useridto = $USER->id;
        }

        // Is notification enabled ?
        if ($useridto == $USER->id) {
            $disabled = $USER->emailstop;
        } else {
            $user = \core_user::get_user($useridto, "emailstop", MUST_EXIST);
            $disabled = $user->emailstop;
        }
        if ($disabled) {
            // Notifications are disabled.
            return array();
        }

        // $sql = "SELECT n.id, n.useridfrom, n.useridto,
        //                n.subject, n.fullmessage, n.fullmessageformat,
        //                n.fullmessagehtml, n.smallmessage, n.contexturl,
        //                n.contexturlname, n.timecreated, n.component,
        //                n.eventtype, n.timeread, n.customdata
        //           FROM {notifications} n
        //          WHERE n.useridto = ?
        //       ORDER BY timecreated $sort, timeread $sort, id $sort";
        // [VinhPT] Modify notification moodle
        $sql = "SELECT
                    	TNL.id,
                        TNL.createdby as useridfrom,
                        TNL.content as fullmessage,
                        TNL.sendto as useridto,
                        TNL.target as subject,
                        TNL.created_at as timecreated,
                        null as fullmessagehtml,
                        null as contexturlname,
                        null as eventtype,
                        null as smallmessage,
                        null as timeread,
                        null as fullmessageformat,
                        null as contexturl,
                        null as component,
                        null as customdata,
                        2 as fullmessageformat,
                        TNL.is_read as is_read
                FROM
                    tms_nofitication_logs as TNL
                WHERE
                    sendto = ? and action = 'update'
                ORDER BY
                    created_at $sort";
        $notifications = [];

        $records = $DB->get_recordset_sql($sql, [$useridto], $offset, $limit);
        foreach ($records as $record) {
            if ($record->timecreated != null) {
                $record->timecreated = strtotime($record->timecreated);
            }
            switch ($record->subject) {
                #region New cases
                case 'remind_expire_required_course':
                    $record->subject = 'Remind Expire Required Coure';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '<p>This email is to notify you that we have noticed that you have not yet completed the following assigned course(s) before the expiry dates.</p>';
                    $record->fullmessagehtml .= '
                    <table class="tb-notification">
                        <thead>
                            <tr class="tr-notification">
                                <th>COURSE CODE</th>
                                <th>COURSE NAME</th>
                                <th>START DATE</th>
                                <th>END DATE</th>
                                <th>PLACE</th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach ($content as $contents) {
                        //convert startdate
                        $startdate = $contents->startdate;
                        if($startdate > 0){
                            $startdate = date('m/d/Y', $contents->startdate);
                        }else{
                            $startdate = '';
                        }

                        //convert enddate
                        $enddate = $contents->enddate;
                        if($enddate > 0){
                            $enddate = date('m/d/Y', $contents->enddate);
                        }else{
                            $enddate = '';
                        }


                        $record->fullmessagehtml .= '<tr class="tr-notification">
                            <td><p>'. $contents->course_code .'</p></td>
                            <td><p>'.$contents->course_name.'</p></td>
                            <td><p>'. $startdate .'</p></td>
                            <td><p>'. $enddate .'</p></td>
                            <td><p>'.$contents->course_place.'</p></td>
                            </tr>';
                    }
                    $record->fullmessagehtml .= '</tbody></table>';
                    break;
                case 'assigned_competency':

                    $record->subject = 'Assigned Competency';
                    $content = json_decode($record->fullmessage);

                    //convert startdate
                    $startdate = $content->start_date;
                    if($startdate > 0){
                        $startdate = date('m/d/Y', $content->start_date);
                    }else{
                        $startdate = '';
                    }

                    //convert enddate
                    $enddate = $content->end_date;
                    if($enddate > 0){
                        $enddate = date('m/d/Y', $content->end_date);
                    }else{
                        $enddate = '';
                    }

                    $record->fullmessagehtml = 'This email is to notify you that you have been assigned to study courses according to the competency framework as follows,<br />';
                    $record->fullmessagehtml .= '<br /><strong>Name:&nbsp;<strong>'.$content->object_name.'</strong><br />';
                    $record->fullmessagehtml .= '<strong>Code:&nbsp;<strong>'.$content->code.'</strong><br />';
                    $record->fullmessagehtml .= '<strong>Starting time:&nbsp;<strong>'.$startdate.'</strong><br />';
                    $record->fullmessagehtml .= '<strong>Ending time:&nbsp;<strong>'.$enddate.'</strong><br />';
                    break;
                case 'remind_exam':
                    $record->subject = 'Remind Exam';
                    $content = json_decode($record->fullmessage);

                    //convert startdate
                    $start_time = $content->start_time;
                    if($start_time > 0){
                        $start_time = date('m/d/Y', $content->start_time);
                    }else{
                        $start_time = '';
                    }

                    //convert enddate
                    $end_time = $content->end_time;
                    if($end_time > 0){
                        $end_time = date('m/d/Y', $content->end_time);
                    }else{
                        $end_time = '';
                    }

                    $record->fullmessagehtml = '<p>This email is to notify you that,</p>';
                    $record->fullmessagehtml .= '<p>The Annual PHH Academy TOEIC TEST is ready for you to complete,</p>';
                    $record->fullmessagehtml .= '<p>From <strong>'.$start_time.' to '.$end_time.' </strong></p>';
                    $record->fullmessagehtml .= '<p>Please log in to the PHH Academy using this link <a href="https://academy.phh-group.com">https://academy.phh-group.com</a> to do the test by the required time.</p>';
                    $record->fullmessagehtml .= '<p>Note: This test must be taken continuously for 120 minutes (without pausing or stopping), so please arrange your time &amp; workload to take the test in such a way that you can focus on achieving the best score possible.</p>';
                    break;
                case 'suggest':
                    $record->subject = 'Suggest Optional Coure';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '<p>This email is to notify you that,</p>';
                    $record->fullmessagehtml .= '<p>There are some relevant (but not compulsory) courses that you may be interested in studying, as listed below: &nbsp;</p>';
                    $record->fullmessagehtml .= '
                    <table class="tb-notification">
                        <thead>
                            <tr class="tr-notification">
                                <th>COURSE CODE</th>
                                <th>COURSE NAME</th>
                                <th>START DATE</th>
                                <th>END DATE</th>
                                <th>PLACE</th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach ($content as $contents) {
                        //convert startdate
                        $start_date = $contents->start_date;
                        if($start_date > 0){
                            $start_date = date('m/d/Y', $contents->start_date);
                        }else{
                            $start_date = '';
                        }

                        //convert enddate
                        $end_date = $contents->end_date;
                        if($end_date > 0){
                            $end_date = date('m/d/Y', $contents->end_date);
                        }else{
                            $end_date = '';
                        }


                        $record->fullmessagehtml .= '<tr class="tr-notification">
                            <td><p>'. $contents->object_name .'</p></td>
                            <td><p>'.$contents->code.'</p></td>
                            <td><p>'. $start_date .'</p></td>
                            <td><p>'. $end_date .'</p></td>
                            <td><p>'.$contents->room.'</p></td>
                            </tr>';
                    }
                    $record->fullmessagehtml .= '</tbody></table>';
                    break;
                case 'remind_certificate':
                    $record->subject = 'Notice of certificate issued';
                    $record->fullmessage = 'You are eligible for certification';
                    break;
                case 'enrol':
                    $record->subject = 'Assigned Course';
                    $content = json_decode($record->fullmessage);

                    //convert startdate
                    $start_date = $content->start_date;
                    if($start_date > 0){
                        $start_date = date('m/d/Y', $content->start_date);
                    }else{
                        $start_date = '';
                    }

                    //convert enddate
                    $end_date = $content->end_date;
                    if($end_date > 0){
                        $end_date = date('m/d/Y', $content->end_date);
                    }else{
                        $end_date = '';
                    }

                    $record->fullmessagehtml = '<p>This is to notify you that <strong>you </strong>have been assigned to study the course:<br /><br />';
                    $record->fullmessagehtml .= '<strong>'.$content->object_name.'</strong><br />';
                    $record->fullmessagehtml .= 'Starting date:&nbsp;<strong>'.$start_date.'</strong><br />';
                    $record->fullmessagehtml .= 'Ending date:&nbsp;<strong>'.$end_date.'</strong></p>';
                    break;
                #endregion
                #region Old cases
                case 'quiz_start':
                    $record->subject = 'Start the Exam';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = 'Start to do the exam<br>Course: <b>' . $content->parent_name . '</b><br>Exam: <b>' . $content->object_name . '</b><br>Time: <b>' . $content->end_date . '</b>';
                    break;
                case 'quiz_end':
                    $record->subject = 'End of Exam';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = 'End to do the exam<br>Course: <b>' . $content->parent_name . '</b><br>Exam: <b>' . $content->object_name . '</b><br>Time: <b>' . $content->end_date . '</b>';
                    break;
                case 'quiz_completed':
                    $record->subject = 'Exam results';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = 'Complete the Exam<br>Course: <b>' . $content->parent_name . '</b><br>Exam: <b>' . $content->object_name . '</b><br>Time: <b>' . $content->end_date . '</b><br>Result: <b>' . $content->grade . '</b>';
                    break;
                case 'remind_login':
                    $record->subject = 'Login reminder';
                    $record->fullmessage = 'You have not logged into the system for a long time';
                    break;
                case 'remind_education_schedule':
                    $record->subject = 'Reminder to complete the training route';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Course code</th>
                                <th>Course name</th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach ($content as $contents) {
                        $record->fullmessagehtml .= '<tr><td>' . $contents->code . '</td><td>' . $contents->object_name . '</td></tr>';
                    }
                    break;
                case 'remind_access_course':
                    $record->subject = 'Interactive reminders with courses';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Course code</th>
                                <th>Course name</th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach ($content as $contents) {
                        $record->fullmessagehtml .= '<tr><td>' . $contents->code . '</td><td>' . $contents->object_name . '</td></tr>';
                    }

                    $record->fullmessagehtml .= '<tbody></table>';
                    break;
                case 'remind_upcoming_course':
                    $record->subject = 'Announce the course is about to start';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Course code</th>
                                <th>Course name</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach ($content as $contents) {
                        $record->fullmessagehtml .= '<tr><td>' . $contents->code . '</td><td>' . $contents->object_name . '</td><td>' . $contents->end_date . '</td></tr>';
                    }
                    break;
                #endregions
            }
            $notifications[] = (object)$record;
        }
        $records->close();

        return $notifications;
    }

    /**
     * Count the unread notifications for a user.
     *
     * @param int $useridto the user id who received the notification
     * @return int count of the unread notifications
     * @since 3.2
     */
    public static function count_unread_popup_notifications($useridto = 0)
    {
        global $USER, $DB;

        if (empty($useridto)) {
            $useridto = $USER->id;
        }

        return $DB->count_records_sql(
            "SELECT count(id)
               FROM {notifications}
              WHERE id IN (SELECT notificationid FROM {message_popup_notifications})
                AND useridto = ?
                AND timeread is NULL",
            [$useridto]
        );
    }
}
