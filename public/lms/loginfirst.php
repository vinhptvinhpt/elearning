<?php
use core\session\exception as CoreException;
use Horde\Socket\Client\Exception;

require_once('config.php');
require_once('authen_api.php');
if (isset($_POST['data'])) {
    $data = $_POST['data'];
    if($data){
        $decode_data = decodeJWT($data);
        if($decode_data){
            $json_data = json_decode($decode_data);
            if(encrypt_key(API_KEY_SEC) == $json_data->data->app_key){
                echo cus_login($json_data->data->user_id);
            }
        }
    }
}

function cus_login($idorusername)
{
    global $SESSION, $DB;
    try {
        $u = $DB->get_record('user', ['id' => $idorusername]);
        if (!empty($u)) {
            $id = $u->id;
        } else {
            return 0;
        }
        $_user = get_complete_user_data('id', $id);

        $result = complete_user_login($_user);
        $SESSION->userkey = true;
        if ($result) {
            session_start();
            $sqlGetOrganization = 'SELECT f.id, f.level, f.code 
            FROM (SELECT @id AS _id, (SELECT @id := parent_id FROM tms_organization WHERE id = _id) 
            FROM (SELECT @id := (select organization_id from tms_organization_employee where user_id= '.$id.')) tmp1 
            JOIN tms_organization ON @id IS NOT NULL) tmp2 
            JOIN tms_organization f ON tmp2._id = f.id 
            where f.level = 2 limit 1';
            $organnizationCode = array_values($DB->get_records_sql($sqlGetOrganization))[0];
            $_SESSION["organizationCode"] = $organnizationCode->code;
            return 1;
        }
        return 0;
    } catch (Exception $e) {
        return 0;
    }
}
