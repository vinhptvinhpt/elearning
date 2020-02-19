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
            }
        }
    }
}
try {
    accesslib_clear_role_cache($roleid);
} catch (Exception $e) {
    ob_clean();
    echo 0;
    die;
}
ob_clean();
echo '1';
die;

?>