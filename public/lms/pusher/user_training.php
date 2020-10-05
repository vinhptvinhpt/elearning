<?php

use App\Role;
use Illuminate\Support\Facades\DB;

require_once('../config.php');

/*
 *         $data = DB::table('mdl_user_enrolments as mu')
            ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
            //->join('mdl_course_completion_criteria as ccc', 'ccc.course', '=', 'c.id') //cause duplicate
            ->join('tms_trainning_courses as ttc', 'ttc.course_id', '=', 'c.id')
            ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttc.trainning_id')
            ->where('ttc.deleted', '=', 0)
            ->where('c.deleted', '=', 0)
            ->where('u.id', '=', $user_id)
            ->where('e.roleid', '=', Role::ROLE_STUDENT)
            ->select(
                'c.id as course_id',
                'c.shortname',
                'c.fullname',
                'ttp.name as trainning_name',
                //'ccc.gradepass as gradepass',
                DB::raw('(select count(cmc.coursemoduleid) as course_learn from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cmc.completionstate in (1,2) and cmc.userid = ' . $user_id . ' and cm.course = c.id and cm.completion <> 0) as user_course_completionstate'), //Đã học
                DB::raw('(select count(cm.id) as course_learn from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = c.id and cm.completion <> 0) as user_course_learn'),//Tổng số module trong khóa
                //DB::raw('(select `g`.`finalgrade` from mdl_grade_items as gi join mdl_grade_grades as g on g.itemid = gi.id where gi.courseid = c.id and gi.itemtype = "course" and g.userid = ' . $user_id . ' ) as finalgrade')
                DB::raw('IF( EXISTS(select cc.id from mdl_course_completions as cc where cc.userid = ' . $user_id . ' and cc.course = c.id and cc.timecompleted is not null ), "1", "0") as status_user')
            );

        $data = $data->orderBy('c.id', 'desc');//->distinct();;
 *
 * */


$sql = 'select
ttp.id,
ttp.name
from mdl_user_enrolments mue
inner join mdl_user mu on mu.id = mue.userid
inner join mdl_enrol me on me.id = mue.enrolid AND me.roleid = 5
inner join mdl_course mc on mc.id = me.courseid
inner join tms_trainning_courses ttc on mc.id = ttc.course_id
inner join tms_traninning_programs ttp on ttc.trainning_id = ttp.id
where me.enrol = "manual"
and ttp.deleted = 0
and mue.userid = ' . $USER->id;

$list = array_values($DB->get_records_sql($sql)); //Auto group by moodle

$response = json_encode(['list' => $list]);

echo $response;
