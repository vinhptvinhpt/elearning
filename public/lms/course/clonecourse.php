<?php

require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../authen_api.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot .'/course/externallib.php');

if (isset($_POST['data'])) {
    $data = $_POST['data'];
    if($data){
        $decode_data = decodeJWT($data);
        if($decode_data){
            $json_data = json_decode($decode_data);
            if(encrypt_key(API_KEY_SEC) == $json_data->data->app_key){
                $importcourseid = $json_data->data->importcourseid;
                $restoretarget = $json_data->data->targetcourseid;
            }
        }

    }
}

// Bypass require login by login as admin
global $SESSION, $DB;
$u = $DB->get_record('user', ['id' => 2]);
if (!empty($u)) {
    $id = $u->id;
} else {
    return 0;
}
$_user = get_complete_user_data('id', $id);

$result = complete_user_login($_user);
$SESSION->userkey = true;

try{
    $importid = $DB->get_record('course', array('id' => $importcourseid), '*', MUST_EXIST);
    $targetid = $DB->get_record('course', array('id' => $targetcourseid), '*', MUST_EXIST);
    core_course_external::import_course($importid->id, $targetid->id, 0);
    echo 1;
    die;
}catch (Exception $e) {
    echo 0;
    die;
}

exit;
