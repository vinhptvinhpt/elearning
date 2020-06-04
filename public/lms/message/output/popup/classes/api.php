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
                case 'enrol':
                    $record->subject = 'Ghi danh khóa học';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = 'Ghi danh khoá học thành công<br>Khoá học: <b>' . $content->object_name . '</b>';
                    break;
                case 'suggest':
                    $record->subject = 'Giới thiệu khóa học kĩ năng mềm';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Mã khoá học</th>
                                <th>Tên khoá học</th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach ($content as $contents) {
                        $record->fullmessagehtml .= '<tr><td>' . $contents->code . '</td><td>' . $contents->object_name . '</td></tr>';
                    }
                    break;
                case 'quiz_start':
                    $record->subject = 'Bắt đầu bài kiểm tra';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = 'Bắt đầu làm bài kiểm tra<br>Khoá học: <b>' . $content->parent_name . '</b><br>Bài kiểm tra: <b>' . $content->object_name . '</b><br>Thời gian: <b>' . $content->end_date . '</b>';
                    break;
                case 'quiz_end':
                    $record->subject = 'Kết thúc bài kiểm tra';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = 'Kết thúc bài kiểm tra<br>Khoá học: <b>' . $content->parent_name . '</b><br>Bài kiểm tra: <b>' . $content->object_name . '</b><br>Thời gian: <b>' . $content->end_date . '</b>';
                    break;
                case 'quiz_completed':
                    $record->subject = 'Kết quả kiểm tra';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = 'Hoàn thành bài kiểm tra<br>Khoá học: <b>' . $content->parent_name . '</b><br>Bài kiểm tra: <b>' . $content->object_name . '</b><br>Thời gian: <b>' . $content->end_date . '</b><br>Kết quả: <b>' . $content->grade . '</b>';
                    break;
                case 'remind_login':
                    $record->subject = 'Nhắc nhở đăng nhập';
                    $record->fullmessage = 'Đã lâu bạn chưa đăng nhập vào hệ thống';
                    break;
                case 'remind_expire_required_course':
                    $record->subject = 'Nhắc nhở khóa học bắt buộc sắp hết hạn';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Mã khoá học</th>
                                <th>Tên khoá học</th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach ($content as $contents) {
                        $record->fullmessagehtml .= '<tr><td>' . $contents->code . '</td><td>' . $contents->object_name . '</td></tr>';
                    }
                    break;
                case 'remind_education_schedule':
                    $record->subject = 'Nhắc nhở hoàn thành lộ trình đào tạo';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Mã khoá học</th>
                                <th>Tên khoá học</th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach ($content as $contents) {
                        $record->fullmessagehtml .= '<tr><td>' . $contents->code . '</td><td>' . $contents->object_name . '</td></tr>';
                    }
                    break;
                case 'remind_access_course':
                    $record->subject = 'Nhắc nhở tương tác với các khóa học';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Mã khoá học</th>
                                <th>Tên khoá học</th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach ($content as $contents) {
                        $record->fullmessagehtml .= '<tr><td>' . $contents->code . '</td><td>' . $contents->object_name . '</td></tr>';
                    }

                    $record->fullmessagehtml .= '<tbody></table>';
                    break;
                case 'remind_upcoming_course':
                    $record->subject = 'Thông báo khóa học sắp bắt đầu';
                    $content = json_decode($record->fullmessage);
                    $record->fullmessagehtml = '
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Mã khoá học</th>
                                <th>Tên khoá học</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach ($content as $contents) {
                        $record->fullmessagehtml .= '<tr><td>' . $contents->code . '</td><td>' . $contents->object_name . '</td><td>' . $contents->end_date . '</td></tr>';
                    }
                    break;
                case 'remind_certificate':
                    $record->subject = 'Thông báo về việc cấp chứng chỉ';
                    $record->fullmessage = 'Bạn đã đủ điều kiện để được cấp chứng chỉ';
                    break;
            }
            $notifications[] = (object) $record;
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
