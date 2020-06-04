<?php

require_once(__DIR__ . '/../config.php');

$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : $USER->id;
$fullname = isset($_REQUEST['fullname']) ? $_REQUEST['fullname'] : "''";
$dob = isset($_REQUEST['dob']) ? strtotime($_REQUEST['dob']) : strtotime(0);
$address = isset($_REQUEST['address']) ? $_REQUEST['address'] : "''";
$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : "''";
$phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : strtotime(0);
$sex = isset($_REQUEST['sex']) ? $_REQUEST['sex'] : 0;

$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : "";
$re_password = isset($_REQUEST['re_password']) ? $_REQUEST['re_password'] : "";

$btnType = isset($_REQUEST['btnType']) ? $_REQUEST['btnType'] : "updateProfile";

$status = true;

switch ($btnType) {
    case "password":
        {
            try {
                $new_pass = password_hash($password, PASSWORD_BCRYPT);
                $sql = "";
//                echo $sql;
//                die;
                $DB->execute('update mdl_user set password = :password where id = :userid', ['password' => $new_pass, 'userid' => $user_id]);
            }catch (Exception $e){
                $status = false;
            }
        }
        break;
    default:
        {
            try {
                $sql = "update tms_user_detail set fullname = N'".$fullname."', ".
                    " address = N'".$address."', ".
                    " dob = '".$dob."', ".
                    " email = N'".$email."', ".
                    " phone = '".$phone."', ".
                    " sex = ".$sex.
                    " where user_id = ".$user_id;
                $DB->execute($sql);
            }catch (Exception $e) {
                $status = false;
            }

//            $DB->execute($sql);
        }
        break;
}

echo json_encode([
    'status'=> $status
]);




