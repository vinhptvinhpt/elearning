<?php
require_once('../config.php');

$training = isset($_POST['training']) ? $_POST['training'] : 0;
$txtSearch = isset($_POST['txtSearch']) ? $_POST['txtSearch'] : null;
$current = isset($_POST['current']) ? $_POST['current'] : 1;
$recordPerPage = isset($_POST['recordPerPage']) ? $_POST['recordPerPage'] : 5;
$txtSearch = trim($txtSearch);


$sql = 'select
mc.id,
mc.shortname,
mc.fullname,
( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 AND cm.completion <> 0  and cm.course = mc.id) as numofmodule,
( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 AND cm.completion <> 0  and cmc.completionstate in (1, 2) and cm.course = mc.id and cmc.userid = mue.userid) as numoflearned
from mdl_user_enrolments mue
inner join mdl_user mu on mu.id = mue.userid
inner join mdl_enrol me on me.id = mue.enrolid AND me.roleid = 5
inner join mdl_course mc on mc.id = me.courseid
inner join tms_trainning_courses ttc on mc.id = ttc.course_id
inner join tms_traninning_programs ttp on ttc.trainning_id = ttp.id
where me.enrol = "manual"
and ttp.deleted = 0
and mc.deleted = 0
and mc.visible = 1
and mc.category NOT IN (2,7)
and ttc.deleted <> 1
and ttp.deleted = 0
and mue.userid = ' . $USER->id;

if ($training > 0) {
    $sql .= ' and ttc.trainning_id = ' . $training;
}

$total = count(array_values($DB->get_records_sql($sql)));

$start_index = $current * $recordPerPage - $recordPerPage;

$sql .= ' limit ' . $recordPerPage;
$sql .= ' offset ' . $start_index;

$list = array_values($DB->get_records_sql($sql)); //Auto group by moodle

$response = json_encode([
    'courses' => $list,
    'totalPage' => ceil($total / $recordPerPage),
    'totalRecords' => $total
]);

echo $response;
