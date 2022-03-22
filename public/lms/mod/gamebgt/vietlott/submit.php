<?php

require_once("../../../config.php");

$data = json_decode(file_get_contents("php://input"), true);

// [VinhPT] Save game directly to DB
// $url = 'https://bgt.tinhvan.com/lms/course/save_game.php';
// if (!empty($data['userid']) && !empty($data['itemid']) && isset($data['finalgrade'])) {
//     $ch = curl_init($url);
//     $params = [
//         'userid' => $data['userid'],
//         'itemid' => $data['itemid'],
//         'finalgrade' => $data['finalgrade']
//     ];
	
// 	$url_parse = parse_url($url);

//     if ($url_parse['scheme'] == 'https') {
//         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//     }
	
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_POST, count($params));
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
// 	$result = curl_exec($ch);
//     curl_close($ch);
//     echo $result;
// }

// Get current time
$time = time();
global $DB;

ob_start();
// Check not Null parameter
if (!empty($data['userid']) && !empty($data['itemid']) && isset($data['finalgrade'])) {
    $itemid = $data['itemid'];
    $userid = $data['userid'];
    $finalgrade = $data['finalgrade'];
    // Check record if exist in table mdl_grade_grades
    $sql_check = 'SELECT id FROM mdl_grade_grades WHERE itemid = ? AND userid = ?';
    $result = array_values($DB->get_records_sql($sql_check, array($itemid, $userid)));
    try {
        if ($result) {
            // If exist -> update record with grade from game
            $update = 'UPDATE {grade_grades} SET finalgrade = ?, timemodified = ?  where (itemid = ? and userid = ?)';
            $DB->execute($update, array($finalgrade, $time, $itemid, $userid));
            ob_end_clean();
            echo 1;
        } else {
            // If exist -> insert record with grade from game
            $sql_query = 'INSERT INTO {grade_grades} (itemid, userid, finalgrade, timecreated) VALUES (?,?,?,?)';
            $DB->execute($sql_query, array($itemid, $userid, $finalgrade, $time));
            ob_end_clean();
            echo 1;
        }
    } catch (Exception $e) {
        ob_end_clean();
        echo 0;
        die;
    }
} else {
    ob_end_clean();
    echo 0;
    die;
}