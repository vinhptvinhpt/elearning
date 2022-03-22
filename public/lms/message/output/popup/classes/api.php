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
                    $record->subject = 'Remind expire required coure';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '<p>E-learning system notify you that we have noticed that you have not yet completed the following assigned course(s) before the expiry dates.</p>';
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
                    foreach ($content as $contents) { //Loop khóa trong content cua log

                        //convert startdate
                        $startdate = $contents->start_date;
                        if ($startdate > 0) {
                            $startdate = date('m/d/Y', $startdate);
                        } else {
                            $startdate = '';
                        }

                        //convert enddate
                        $enddate = $contents->end_date;
                        if ($enddate > 0) {
                            $enddate = date('m/d/Y', $enddate);
                        } else {
                            $enddate = '';
                        }

                        $record->fullmessagehtml .= '<tr class="tr-notification">
                            <td><p>' . $contents->code . '</p></td>
                            <td><p>' . $contents->object_name . '</p></td>
                            <td><p>' . $startdate . '</p></td>
                            <td><p>' . $enddate . '</p></td>
                            <td><p>' . $contents->room . '</p></td>
                            </tr>';
                    }
                    $record->fullmessagehtml .= '</tbody></table>';
                    break;
                case 'assigned_competency':
                    $record->subject = 'Competency Assigned';
                    $content = json_decode($record->fullmessage);

                    //convert startdate
                    $startdate = $content->start_date;
                    if ($startdate > 0) {
                        $startdate = date('m/d/Y', $content->start_date);
                    } else {
                        $startdate = 'N/A';
                    }

                    //convert enddate
                    $enddate = $content->end_date;
                    if ($enddate > 0) {
                        $enddate = date('m/d/Y', $content->end_date);
                    } else {
                        $enddate = 'N/A';
                    }

                    $record->fullmessagehtml = 'E-learning system notify you that you have been assigned to study courses according to the competency framework as follows,<br />';
                    $record->fullmessagehtml .= '<br />Name:&nbsp;<strong>' . $content->object_name . '</strong><br />';
                    $record->fullmessagehtml .= 'Code:&nbsp;<strong>' . $content->code . '</strong><br />';
                    $record->fullmessagehtml .= 'Starting time:&nbsp;<strong>' . $startdate . '</strong><br />';
                    $record->fullmessagehtml .= 'Ending time:&nbsp;<strong>' . $enddate . '</strong><br />';
                    break;
                case 'remind_exam':
                    $record->subject = 'Remind Exam';
                    $content = json_decode($record->fullmessage);

                    //convert startdate
                    $start_time = $content->start_date;
                    if ($start_time > 0) {
                        $start_time = date('m/d/Y H:i:s', $start_time);
                    } else {
                        $start_time = 'N/A';
                    }

                    //convert enddate
                    $end_time = $content->end_date;
                    if ($end_time > 0) {
                        $end_time = date('m/d/Y H:i:s', $end_time);
                    } else {
                        $end_time = 'N/A';
                    }

                    $record->fullmessagehtml = '<p>E-learning system notify you that,</p>';
                    $record->fullmessagehtml .= '<p>The exam: <strong>' . $content->object_name . '</strong> is ready for you to complete,</p>';
                    $record->fullmessagehtml .= '<p>From <strong>' . $start_time . ' to ' . $end_time . ' </strong></p>';
                    $record->fullmessagehtml .= '<p>Please log in to the PHH Academy using this link <a href="https://academy.phh-group.com">https://academy.phh-group.com</a> to do the test by the required time.</p>';
                    $record->fullmessagehtml .= '<p>Note: This test must be taken continuously for 120 minutes (without pausing or stopping), so please arrange your time &amp; workload to take the test in such a way that you can focus on achieving the best score possible.</p>';
                    break;
                case 'suggest':
                    $record->subject = 'Suggested optional courses';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '<p>E-learning system notify you that,</p>';
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
                        if ($start_date > 0) {
                            $start_date = date('m/d/Y', $contents->start_date);
                        } else {
                            $start_date = 'N/A';
                        }

                        //convert enddate
                        $end_date = $contents->end_date;
                        if ($end_date > 0) {
                            $end_date = date('m/d/Y', $contents->end_date);
                        } else {
                            $end_date = 'N/A';
                        }


                        $record->fullmessagehtml .= '<tr class="tr-notification">
                            <td><p>' . $contents->object_name . '</p></td>
                            <td><p>' . $contents->code . '</p></td>
                            <td><p>' . $start_date . '</p></td>
                            <td><p>' . $end_date . '</p></td>
                            <td><p>' . $contents->room . '</p></td>
                            </tr>';
                    }
                    $record->fullmessagehtml .= '</tbody></table>';
                    break;
                case 'remind_certificate':
                    $record->subject = 'Notice of certificate issued';
                    $content = json_decode($record->fullmessage);
                    //
                    $record->fullmessagehtml = '<p>Thank you for participate and complete competency framwork <strong>' . $content->object_name . '.</strong></p>';
                    $record->fullmessagehtml .= '<p>Congratulations, you have got the certificate for your efforts.</p>';
                    $record->fullmessagehtml .= '<p>Here is your certificate code: <strong>' . $content->code . '</strong></p>';
                    $record->fullmessagehtml .= '<p>You will receive the certificate with in 3 days from now</p>';
                    $record->fullmessagehtml .= '<p>Best Regards</p>';
                    break;
                case 'enrol':
                    $record->subject = 'Course Assigned';
                    $content = json_decode($record->fullmessage);

                    //convert startdate
                    $start_date = $content->start_date;
                    if ($start_date > 0) {
                        $start_date = date('m/d/Y', $content->start_date);
                    } else {
                        $start_date = 'N/A';
                    }

                    //convert enddate
                    $end_date = $content->end_date;
                    if ($end_date > 0) {
                        $end_date = date('m/d/Y', $content->end_date);
                    } else {
                        $end_date = 'N/A';
                    }

                    $record->fullmessagehtml = '<p>This is to notify you that you have been assigned to study the course:<br /><br />';
                    $record->fullmessagehtml .= '<strong>' . $content->object_name . '</strong><br />';
                    $record->fullmessagehtml .= 'Starting date:&nbsp;<strong>' . $start_date . '</strong><br />';
                    $record->fullmessagehtml .= 'Ending date:&nbsp;<strong>' . $end_date . '</strong></p>';
                    break;
                case'fail_exam':
                    $record->subject = 'Did not pass test @100%';
                    $content = json_decode($record->fullmessage);
                    //
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                    $lms_base_url = $protocol.$_SERVER['HTTP_HOST'] . '/lms';
                    $attempt = $content->attempt;
                    //
                    $record->fullmessagehtml = '<p>Unfortunately, after all available attempts you have not passed the final test for this course with 100% .</p>';
                    $record->fullmessagehtml .= '<p>Prior to unlocking the test to allow you another attempt, your line manager will discuss with you your knowledge gap and why you did not pass.</p>';
                    $record->fullmessagehtml .= '<p>You can also click on the link to review which part(s) of the test you did not pass.</p>';
                    $record->fullmessagehtml .= '<p>Course: <strong>' . $content->parent_name . '</strong></p>';
                    $record->fullmessagehtml .= '<p>Review: <strong><a href="' . $lms_base_url . '/mod/quiz/review.php?attempt=' . $attempt . '">LINK</a></strong></p> <p>&nbsp;</p>';
                    $record->fullmessagehtml .= '<p>Best Regards</p>';
                    break;
                case'request_more_attempt':
                    $record->subject = 'Request to allow learner to retake final test';
                    $content = json_decode($record->fullmessage);
                    //
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                    $tms_base_url = $protocol.$_SERVER['HTTP_HOST'];
                    $lms_base_url = $tms_base_url . '/lms';
                    $attempt = $content->attempt;
                    $url_review = $lms_base_url . '/mod/quiz/review.php?attempt=' . $attempt;
//                    $url_unlock = $tms_base_url . '/page/notification/unlock/' . $content->parent_id;
                    //
                    $record->fullmessagehtml = '<p>The following learner has not passed a final test with a 100% pass rate and will need to make another attempt.</p>';
                    $record->fullmessagehtml .= '<p>Prior to unlocking the test to allow them another attempt, please review their result and give them feedback about their knowledge gap.</p>';
                    $record->fullmessagehtml .= '<p>Learner: <strong>' . $content->object_name . '</strong></p>';
                    $record->fullmessagehtml .= '<p>Review learner&rsquo;s test result: <strong><a href="' . $url_review . '">LINK</a></strong></p>';
//                    $record->fullmessagehtml .= '<p>Unlock test to allow more attempts: <strong><a href="' . $url_unlock . '">LINK</a></strong></p>';
                    $record->fullmessagehtml .= '<p>&nbsp;</p>';
                    $record->fullmessagehtml .= '<p>Best Regards</p>';
                    break;
                case'calculate_toeic_grade':
                    $record->subject = 'TOEIC Test Result';
                    $content = json_decode($record->fullmessage);
                    //
                    $record->fullmessagehtml = '<p>Thank you for your completion of our online TOEIC TEST.<br />Your test result is great input for us to understand the current overall English level of PHH Group staff.<br />Besides that, there is no pass or fail on this test, just your annual score so it can help you understand your current level and check your improvement each year.</p>';
                    $record->fullmessagehtml .= '<p>Remember too, your result is private and is not publicly shared to other staff, only your team leader/line manager will have access to your results.</p>';
                    $record->fullmessagehtml .= '<p>To compare your results to other common levels and tests please refer to the conversion table below.</p>';
                    $record->fullmessagehtml .= '<table cellspacing="0" style="border-collapse:collapse">
                                                <tbody>
                                                    <tr>
                                                        <td style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:1px solid black; vertical-align:top; width:96px">
                                                        <p><strong>TOEIC Score</strong></p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; vertical-align:top; width:54px">
                                                        <p><strong>CEFR </strong></p>

                                                        <p><strong>Level</strong></p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; vertical-align:top; width:96px">
                                                        <p><strong>IELTS Bandscore</strong></p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; vertical-align:top; width:90px">
                                                        <p><strong>TOEFL iBT</strong></p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; vertical-align:top; width:90px">
                                                        <p><strong>Cambridge Exam Level</strong></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>911 &amp; Above</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:54px">
                                                        <p>C2</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>7.5 or Above</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>111 - 120</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>CPE</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>701 - 910</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:54px">
                                                        <p>C1</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>6.5 - 7</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>96 - 110</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>CAE</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>541 - 700</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:54px">
                                                        <p>B2</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>5 - 6</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>66 - 95</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>FCE</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>381 - 540</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:54px">
                                                        <p>B1</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>3.5 &ndash; 4.5</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>41 - 65</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>PET</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>246 - 380</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:54px">
                                                        <p>A2</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>3</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>31 - 40</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>KET</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>Below 245</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:54px">
                                                        <p>A1</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:96px">
                                                        <p>1 - 2</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>0 to 30</p>
                                                        </td>
                                                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; vertical-align:top; width:90px">
                                                        <p>Starter</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>';
                    $record->fullmessagehtml .= '<p>Congratulations, here are the results of your efforts.</p>';
                    $record->fullmessagehtml .= '<p>Listening: <strong>' . $content->listening . '</strong></p>';
                    $record->fullmessagehtml .= '<p>Reading: <strong>' . $content->reading . '</strong></p>';
                    $record->fullmessagehtml .= '<p>Total: <strong>' . $content->total . '</strong></p>';
                    $record->fullmessagehtml .= '<p>&nbsp;</p>';
                    $record->fullmessagehtml .= '<p>Best Regards</p>';
                    break;
                case 'completed_competency_framework':
                    $record->subject = 'Completed competency framework';
                    $content = json_decode($record->fullmessage);

                    //convert startdate
                    $startdate = $content->start_date;
                    if ($startdate > 0) {
                        $startdate = date('m/d/Y', $content->start_date);
                    } else {
                        $startdate = 'N/A';
                    }

                    //convert enddate
                    $enddate = $content->end_date;
                    if ($enddate > 0) {
                        $enddate = date('m/d/Y', $content->end_date);
                    } else {
                        $enddate = 'N/A';
                    }

                    $record->fullmessagehtml = '<p>E-learning system notify you that you have been finished the competency framework as follows,</p>';
                    $record->fullmessagehtml .= '<p>Name:&nbsp;<strong>' . $content->object_name . '</strong><br />';
                    $record->fullmessagehtml .= 'Code:&nbsp;<strong>' . $content->code . '</strong><br />';
                    $record->fullmessagehtml .= 'Starting date:&nbsp;<strong>' . $startdate . '</strong><br />';
                    $record->fullmessagehtml .= 'Ending date:&nbsp;<strong>' . $enddate . '</strong></p>';
                    $record->fullmessagehtml .= '<p>Best Regards</p>';
                    break;
                case 'retake_exam':
                    $record->subject = 'Retake Final Test';
                    $content = json_decode($record->fullmessage);
                    //
                    $record->fullmessagehtml = '<p>You have been given one more attempt to retake the test of <strong>' . $content->parent_name . '</strong></p>';
                    $record->fullmessagehtml .= '<p>Click to the link below which will take you back the test. Good luck!</p>';
                    $record->fullmessagehtml .= '<p>Test: <strong><a href="' . $content->url . '">LINK</a></strong></p>';
                    $record->fullmessagehtml .= '<p>Best Regards</p>';
                    break;
                #endregion
                #region Old cases
                case 'quiz_start':
                    $record->subject = 'Start the exam';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = 'Start to do the exam<br>Course: <b>' . $content->parent_name . '</b><br>Exam: <b>' . $content->object_name . '</b><br>Time: <b>' . $content->end_date . '</b>';
                    break;
                case 'quiz_end':
                    $record->subject = 'End of exam';
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
