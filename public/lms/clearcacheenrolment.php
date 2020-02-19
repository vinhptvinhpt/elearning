<?php

require_once('config.php');
require_once('authen_api.php');

if (isset($_POST['data'])) {
    $data = $_POST['data'];
    if ($data) {
        $decode_data = decodeJWT($data);
        if ($decode_data) {
            $json_data = json_decode($decode_data);
            if (encrypt_key(API_KEY_SEC) == $json_data->data->app_key) {
                $roleid = $json_data->data->roleid;
                $userid = $json_data->data->userid;
            }
        }
    }
}

$context = context::instance_by_id(1, MUST_EXIST);

try {
    mark_user_dirty($userid);
    core_course_category::role_assignment_changed($roleid, $context);
} catch (Exception $e) {
    ob_clean();
    echo 0;
    die;
}
ob_clean();
echo 1;
die;

?>