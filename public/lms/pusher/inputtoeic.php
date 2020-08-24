<?php

use Illuminate\Http\Request;

require_once(__DIR__ . '/../config.php');
global $CFG;
require_once("$CFG->libdir/phpexcel/PHPExcel.php");

// Get params
$courseid = isset($_REQUEST['courseid']) ? $_REQUEST['courseid'] : '0';
if (!$courseid) {
    $status = false;
    $msg = 'Invalid CourseID';
}

$msg = '';

try {
    if (isset($_FILES['file'])) {
        // Read file excel
        $tmpfname = $_FILES['file']['tmp_name'];
        $excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
        $excelObj = $excelReader->load($tmpfname);
        $worksheet = $excelObj->getSheet(0);
        $lastRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestDataColumn();
        $listGrade = [];
        for ($row = 2; $row <= $lastRow; $row++) {
            // Skip empty row data
            $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            if (isEmptyRow(reset($rowData))) {
                continue;
            }
            $validatecheck = true;
            // Get cell value
            $name = $worksheet->getCell('B' . $row)->getValue();
            $email = $worksheet->getCell('D' . $row)->getValue();
            $listening = $worksheet->getCell('E' . $row)->getValue();
            $reading = $worksheet->getCell('F' . $row)->getValue();
            $total = $worksheet->getCell('G' . $row)->getCalculatedValue();
            // Validate cell data
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $status = false;
                $msg = 'Invalid email format cell D' . $row;
                $validatecheck = false;
                break;
            }
            if (!validate_grade($listening, "skill")) {
                $status = false;
                $msg = 'Invalid listening grade format cell E' . $row;
                $validatecheck = false;
                break;
            }
            if (!validate_grade($reading, "skill")) {
                $status = false;
                $msg = 'Invalid reading grade format cell F' . $row;
                $validatecheck = false;
                break;
            }
            if (!validate_grade($total, "total")) {
                $status = false;
                $msg = 'Invalid total grade format cell G' . $row;
                $validatecheck = false;
                break;
            }
            // Insert/Update if pass validation
            if ($validatecheck) {
                // Check exist record in tms_quiz_grades
                $check_grade = 'SELECT * from tms_quiz_grades where (courseid=? and email=?)';
                $result = array_values($DB->get_records_sql($check_grade, array($courseid, $email)));
                // Check valid user
                $check_user = 'SELECT user_id from tms_user_detail where (email=?)';
                $result_check_user = array_values($DB->get_records_sql($check_user, array($email)))[0]->user_id;
                // Case valid user
                if ($result_check_user) {
                    if ($result) {
                        // Update grade of TOEIC test for user in course
                        $update = 'UPDATE tms_quiz_grades set listening=?, reading=?, total=?  where (courseid=? and userid=?)';
                        $DB->execute($update, array($listening, $reading, $total, $courseid, $result_check_user));
                    } else {
                        // Insert grade of TOEIC test for user in course
                        $insert = 'INSERT INTO tms_quiz_grades (userid, email, courseid, listening, reading, total) VALUES (?,?,?,?,?,?)';
                        $DB->execute($insert, array($result_check_user, $email, $courseid, $listening, $reading, $total));
                    }
                } else {
                    // Invalid User
                    $status = false;
                    $msg = 'Invalid user at row ' . $row;
                    break;
                }
            }
        }
        if ($validatecheck){
            $status = true;
            $msg = 'Import TOEIC grade successfully!';
        }
    }
} catch (Exception $e) {
    $status = false;
    $msg = 'Error. Please try again later';
}

echo json_encode([
    'status' => $status,
    'msg' => $msg
]);

/**
 *    Validate TOEIC grade (skill grade 0-495, total grade 0-990)
 *
 *    @return   Boolean 
 */
function validate_grade($grade, $type)
{
    $validate = true;
    switch ($type) {
        case "skill":
            if (!filter_var(
                $grade,
                FILTER_VALIDATE_INT,
                array(
                    'options' => array(
                        'min_range' => 0,
                        'max_range' => 495
                    )
                )
            ))
            $validate = false;
            break;
        case "total":
            if (!filter_var(
                $grade,
                FILTER_VALIDATE_INT,
                array(
                    'options' => array(
                        'min_range' => 0,
                        'max_range' => 990
                    )
                )
            ))
            $validate = false;
            break;
    }
    return $validate;
}

/**
 *    Check if empty row
 *
 *    @return   Boolean 
 */
function isEmptyRow($row)
{
    foreach ($row as $cell) {
        if (null !== $cell) return false;
    }
    return true;
}
