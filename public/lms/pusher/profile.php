<?php
require_once(__DIR__ . '/../config.php');
$sqlGetInfoUser = 'select tud.fullname as fullname, SUBSTR(tud.avatar, 2) as avatar, tud.address, toe.position, toe.description as exactlypostion , tmso.name as departmentname, (YEAR(NOW())-YEAR(FROM_UNIXTIME(tud.start_time))) as yearworking, tmso.id as organization_id from tms_user_detail tud left join tms_organization_employee toe on tud.user_id = toe.user_id join tms_organization tmso on toe.organization_id = tmso.id where tud.user_id = '.$USER->id;
$profile = array_values($DB->get_records_sql($sqlGetInfoUser))[0];
    //get list name of line manager
$sqlGetLineManagers = 'select fullname from tms_user_detail where user_id in (select user_id from tms_organization_employee where position=\'manager\' and organization_id = '.$profile->organization_id.')';
$linemanagers = array_values($DB->get_records_sql($sqlGetLineManagers));

session_start();
$currentcourses = $_SESSION["courses_current"];
$requiredcourses = $_SESSION["courses_all_required"];
$totalCourse = $_SESSION["totalCourse"];

echo json_encode(['profile'=> $profile, 'linemanagers' => $linemanagers, 'currentcourses' => $currentcourses, 'requiredcourses' => $requiredcourses, 'totalCourse' => $totalCourse]);
