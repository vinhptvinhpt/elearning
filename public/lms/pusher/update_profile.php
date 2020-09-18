<?php

use Illuminate\Http\Request;

require_once(__DIR__ . '/../config.php');

$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : $USER->id;
$fullname = isset($_REQUEST['fullname']) ? $_REQUEST['fullname'] : "''";
$dob = isset($_REQUEST['dob']) ? strtotime($_REQUEST['dob']) : strtotime(0);
$address = isset($_REQUEST['address']) ? $_REQUEST['address'] : "''";
$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : "''";
$phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : strtotime(0);
$sex = isset($_REQUEST['sex']) ? $_REQUEST['sex'] : 0;

$filename = $_FILES['file']['name'];

$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : "";
$re_password = isset($_REQUEST['re_password']) ? $_REQUEST['re_password'] : "";

$btnType = isset($_REQUEST['btnType']) ? $_REQUEST['btnType'] : "updateProfile";

$status = true;

$msg = '';

$is_avatar = false;

switch ($btnType) {
    case "password":
        {
            try {
                if (!validate_password_func($password)){
                    $status = false;
                    $msg = 'The password has not been met yet. The password should include uppercase letters, numbers and special characters';
                }
                if (!validate_password_func($re_password))
                {
                    $status = false;
                    $msg = 'The password has not been met yet. The password should include uppercase letters, numbers and special characters';
                }
                if ($password != $re_password)
                {
                    $status = false;
                    $msg = 'Password not match';
                }

                if($status){
                    $new_pass = password_hash($password, PASSWORD_BCRYPT);
                    $DB->execute('update mdl_user set password = :password where id = :userid', ['password' => $new_pass, 'userid' => $user_id]);
                    $msg = 'Update password successful';
                }

            }catch (Exception $e){
                $status = false;
                $msg = 'Error! An error occurred while updating the password. Please try again later';
            }
        }
        break;
    default:
        {
            try {
                if(!is_null($filename)) {
                    $path_image = $CFG->dirstorage. DIRECTORY_SEPARATOR . 'user'. DIRECTORY_SEPARATOR. "avatar_".$user_id.".png";
                    if(!move_uploaded_file($_FILES['file']['tmp_name'], $path_image)){
                        $status=false;
                        $msg = 'Error! An error occurred while updating the avatar. Please try again later';
                    }else{
                        $is_avatar = true;
                    }
                }

                //regex phone
                preg_match('/^[0-9\+\.\-\s]*$/i', $phone, $matches, PREG_OFFSET_CAPTURE);
                if(count($matches) == 0)
                {
                    $status = false;
                    $msg = 'Phone number incorrect format. Please try again later';
                }else{
                    if($status){
                        $sql = "update tms_user_detail set fullname = N'".$fullname."', ".
                            " address = N'".$address."', ".
                            " dob = '".$dob."', ".
                            " email = N'".$email."', ".
                            " phone = '".$phone."', ".
                            " sex = ".$sex;
                        if($is_avatar)
                            $sql .= ", avatar = "."'/storage/upload/user/avatar_".$user_id.".png'";
                        $sql .= " where user_id = ".$user_id;
                        $DB->execute($sql);
                        $msg = 'Update profile successful';
                    }
                }
            }catch (Exception $e) {
                $status = false;
                $msg = 'Error! An error occurred while updating the profile. Please try again later';
            }

//            $DB->execute($sql);
        }
        break;
}

echo json_encode([
    'status'=> $status,
    'msg' => $msg
]);




function validate_password_func($password)
{
    $validate = true;
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    //if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
    if (strlen($password) < 8) {
        $validate = false;
    }
    return $validate;
}
