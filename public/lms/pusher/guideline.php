<?php
require_once(__DIR__ . '/../config.php');

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'get';
$getContent = isset($_REQUEST['content']) ? $_REQUEST['content'] : '';
//
$content = '';
$status = true;
$msg = '';
if($type == 'get'){
    $sql = "select content from tms_configs where target = 'guideline'";
    $guide_line = array_values($DB->get_records_sql($sql))[0]->content;
}else{
    try {
        $DB->execute('update tms_configs set content = :content where target = :target', ['content' => $getContent, 'target' => 'guideline']);
        $msg = 'update guideline successful';
    }catch (Exception $e){
        $status = false;
        $msg = 'Error! An error occurred while updating the guideline. Please try again later';
    }

}

//get list name of line manager
echo json_encode([
    'content'=> $guide_line,
    'status' => $status,
    'msg' => $msg
]);
