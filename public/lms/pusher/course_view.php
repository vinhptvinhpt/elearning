<?php
require_once(__DIR__ . '/../config.php');

$courseid = isset($_REQUEST['courseid']) ? $_REQUEST['courseid'] : '0';
$current = isset($_POST['current']) ? $_POST['current'] : 1;
//    $pageCount = isset($_POST['pageCount']) ? $_POST['pageCount']:1;
$recordPerPage = isset($_POST['recordPerPage']) ? $_POST['recordPerPage'] : 1;
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'get';
//
$content = '';
$status = true;
$msg = '';
if($type == 'get'){
    if($courseid != '0'){
        $sql = "select tud.fullname, tqg.email, tqg.listening, tqg.reading, tqg.total from tms_quiz_grades tqg inner join tms_user_detail tud on tqg.userid = tud.user_id where courseid = ".$courseid;
        $toeicScore = array_values($DB->get_records_sql($sql));

        $start_index = $current * $recordPerPage - $recordPerPage;

        $result = array_slice($toeicScore, $start_index, $recordPerPage);

        $total = count($toeicScore);

    }else{

    }
}


//get list name of line manager
echo json_encode([
    'toeicScore' => $result,
    'totalPage' => ceil($total / $recordPerPage),
    'totalRecords' => $total
]);
