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
            where f.level = 2 or f.level = 1 limit 1';
            $organization = array_values($DB->get_records_sql($sqlGetOrganization))[0];
            $organizationCodeGet = "";
            if(strpos(strtolower($organization->code), 'bg') === 0 || strpos(strtolower($organization->code), 'begodi') === 0){
                $organizationCodeGet = "BG";
                $_SESSION["pathLogo"] = 'images/begodi.png';
            }
            else if(strpos(strtolower($organization->code),'ea') === 0 || strpos(strtolower($organization->code),'easia') === 0){
                $organizationCodeGet = "EA";
                $_SESSION["pathLogo"] = 'images/logo-black.png';
            }
            else if(strpos(strtolower($organization->code), 'ev') === 0 || strpos(strtolower($organization->code), 'exotic') === 0){
                $organizationCodeGet = "EV";
                $_SESSION["pathLogo"] = 'images/exoticvoyages.png';
            }else if(strpos(strtolower($organization->code), 'AV') === 0 || strpos(strtolower($organization->code), 'avana') === 0){
                $organizationCodeGet = "AV";
                $_SESSION["pathLogo"] = 'images/avana.png';
            }else{
                $organizationCodeGet = "PH";
                $_SESSION["pathLogo"] = 'images/phh.png';
            }
            $_SESSION["organizationCode"] = $organizationCodeGet;
            return 1;
        }
        return 0;
    } catch (Exception $e) {
        return 0;
    }
}
