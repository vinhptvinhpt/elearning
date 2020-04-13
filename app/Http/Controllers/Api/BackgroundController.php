<?php

namespace App\Http\Controllers\Api;

use App\Exports\ListMismatchData;
use App\Http\Controllers\Controller;
use App\Imports\DataImport;
use App\MdlEnrol;
use App\MdlGradeGrade;
use App\MdlGradeItem;
use App\MdlRole;
use App\MdlRoleAssignments;
use App\MdlUser;
use App\ModelHasRole;
use App\Role;
use App\StudentCertificate;
use App\TmsBranch;
use App\TmsBranchSaleRoom;
use App\TmsCity;
use App\TmsCityBranch;
use App\TmsDepartments;
use App\TmsNotification;
use App\TmsNotificationLog;
use App\TmsOrganization;
use App\TmsRoleOrganize;
use App\TmsSaleRooms;
use App\TmsSaleRoomUser;
use App\TmsTrainningCategory;
use App\TmsTrainningUser;
use App\TmsUserDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel;
use Mockery\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;

set_time_limit(0);

class BackgroundController extends Controller
{
    public function test()
    {
        //path to files folder contain excel files
        $dir = storage_path() . DIRECTORY_SEPARATOR . "import";
        //return files or folders in directory above
        $files = scandir($dir);
        $files = array_diff($files, array('.', '..'));
        return response()->json($files);
    }

    public function import()
    {
        set_time_limit(0);

        $dir = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "import";
        //return files or folders in directory above
        $files = scandir($dir);
        $files = array_diff($files, array('.', '..'));
        $files = array_slice($files, 0, 1);

        foreach ($files as $file_path) {

            //check file is xlsx, xls
            $extension = pathinfo($file_path, PATHINFO_EXTENSION);

            if ($extension != 'xls' && $extension != 'xlsx') {
                return response()->json([
                    'extension' => 'error'
                ]);
            }

            //$file_path = "1_CSDL NVBH CN BRVT.xlsx";
            $departmentId = explode("_", $file_path)[0];

            if (!is_numeric($departmentId)) {
                return response()->json(self::status_message('error', "Missing departmentId"));
            }
            $checkDepartment = TmsDepartments::find($departmentId);
            if (!isset($checkDepartment)) {
                return response()->json(self::status_message('error', "Department does not exist"));
            }


            $file_path = "import" . DIRECTORY_SEPARATOR . $file_path;
            //$file_path =  storage_path('import' . DIRECTORY_SEPARATOR . $file_path);
            //use relative with parent folder is /storage/app/

            $inputFileName = pathinfo($file_path, PATHINFO_FILENAME);

            //Khởi tạo tỉnh thành, đại lý, điểm bán unknown theo cho nhánh
            //lấy thành phố đầu tiên của chi nhánh
            $cityUnknow = DB::table('tms_city as tc')
                ->join('tms_department_citys as tdc', 'tdc.city_id', '=', 'tc.id')
                ->where('tdc.department_id', '=', $departmentId)
                ->first();

            $unknownCityId = $cityUnknow->id;

            $createBranchUnknow = $this->CreateBranch('unknowbranch', 'unknowbranch', null, $unknownCityId, null);
            $unknownBranchId = $createBranchUnknow['code'];
            $createSaleRoomUnknow = $this->CreateSaleRoom('unknowsaleroom', 'unknowsaleroom', $unknownBranchId, null, null);
            $unknownSaleroomId = $createSaleRoomUnknow['code'];


            try {
                set_time_limit(0);
                $array = (new DataImport)->toArray($file_path, '', '');

                $this->importOutput['userOuput'] = [];
                $this->importOutput['agencyOuput'] = [];
                $this->importOutput['posHaveOuput'] = [];
                $this->importOutput['posNoOuput'] = [];

                $listEmployees = [];
                $listAgencies = [];
                $listPointOfSalesHaveCertificate = [];
                $listPointOfSalesNoCertificate = [];

                //set value
                if (isset($array['NhanVien'])) {
                    $listEmployees = $array['NhanVien'];
                }
                if (isset($array['DaiLy'])) {
                    $listAgencies = $array['DaiLy'];
                }
                if (isset($array['DiemBanHang co giay chung nhan'])) {
                    $listPointOfSalesHaveCertificate = $array['DiemBanHang co giay chung nhan'];
                }
                if (isset($array['DiemBanHang k giay chung nhan'])) {
                    $listPointOfSalesNoCertificate = $array['DiemBanHang k giay chung nhan'];
                }

                \DB::beginTransaction();

                //insert đại lý

                array_shift($listAgencies);
                array_shift($listAgencies);
                foreach ($listAgencies as $row) {

                    $stt = $row[0];
                    //khởi tạo mảng insert
                    $agency = [];
                    //khởi tạo mảng kết quả trả về
                    $agencyOuput = [];
                    $agencyOuput['stt'] = $stt;

                    //tên đại lý
                    $agency['name'] = $row[2];
                    //mã đại lý
                    $agency['code'] = $row[1];
                    //địa chỉ
                    $agency['address'] = $row[5];
                    //mã tỉnh
                    $cityCode = $row[4];
                    $checkCity = TmsCity::whereRaw("code = '$cityCode'")->first();
                    if (!isset($checkCity)) {
                        $agencyOuput['agencyname'] = $agency['name'];
                        $agencyOuput['username'] = '';
                        $agencyOuput['password'] = '';
                        $agencyOuput['status'] = 'error';
                        $agencyOuput['message'] = "Mã tỉnh thành không tồn tại trong hệ thông";
                        array_push($this->importOutput['agencyOuput'], $agencyOuput);
                        continue;
                    } else {
                        $cityId = $checkCity->id;
                    }

                    try {
                        //kiểm tra xem trưởng đại lý đã tồn tại hay chưa

                        //username
                        $username = $this->vn_to_str($row[7]);
                        //cmtnd
                        $cmtnd = str_replace([' ', 'CMND', 'CCCD', 'MST', ':', "''", "'", '.'], '', $row[8]);
                        //phone
                        $phone = str_replace(["''", "'", ' ', '.'], '', $row[6]);

                        //nếu bỏ trống => generate cmtnd
                        if(empty($cmtnd))
                            $cmtnd = $this->RandomCMTND();

                        //Tạo trưởng đại lý
                        $resultCheck = $this->CreateUser(
                            'manageagents',
                            $username,
                            $row[9],
                            0,
                            $cmtnd,
                            $row[7],
                            $phone,
                            $username,
                            '',
                            '',
                            '',
                            '',
                            0,
                            0,
                            $unknownSaleroomId,
                            $agency['code']
                        );

                        $valueCheck = $resultCheck['userId'];
                        //Tạo thất bại: 0
                        if ($valueCheck == 0) {

                            //Tạo mesage báo lỗi và tổng hợp lại
                            $agencyOuput['agencyname'] = $agency['name'];
                            $agencyOuput['username'] = '';
                            $agencyOuput['password'] = '';
                            $agencyOuput['status'] = 'error';
                            $agencyOuput['message'] = $resultCheck['message'];

                            array_push($this->importOutput['agencyOuput'], $agencyOuput);
                        }
                        else {
                            $agencyOuput['username'] = $resultCheck['username'];

                            $checkBranch = $this->CreateBranch($agency['name'], $agency['code'], $valueCheck, $cityId, $agency['address']);

                            $agencyOuput['agencyname'] = $agency['name'];
                            if ($checkBranch['code'] != 0) {
                                $agencyOuput['status'] = 'success';
                            } else {
                                $agencyOuput['status'] = 'error';
                            }
                            if ($resultCheck['type'] == 'update') {
                                $agencyOuput['password'] = '123456789';
                            } else {
                                $agencyOuput['password'] = '';
                            }
                            $agencyOuput['message'] = $checkBranch['message'];

                            array_push($this->importOutput['agencyOuput'], $agencyOuput);

                        }
                    } catch (\Exception $e) {
                        // \DB::rollBack();

                        $agencyOuput['stt'] = $stt;
                        $agencyOuput['agencyname'] = $agency['name'];
                        $agencyOuput['username'] = '';
                        $agencyOuput['password'] = '';
                        $agencyOuput['status'] = 'error';
                        $agencyOuput['message'] = 'Lỗi hệ thống, chi tiết: ' . $e->getMessage();

                        array_push($this->importOutput['agencyOuput'], $agencyOuput);
                    }
                }

                //insert điểm bán hàng không có giấy chứng nhận

                array_shift($listPointOfSalesNoCertificate);
                array_shift($listPointOfSalesNoCertificate);
                foreach ($listPointOfSalesNoCertificate as $row) {
                    $pointofsale = [];
                    $posNoOuput = [];

                    $posNoOuput['stt'] = $row[0];
                    $posNoOuput['code'] = $row[1];
                    $posNoOuput['name'] = $row[2];
                    $posNoOuput['username'] = '';

                    //tên điểm bán hàng
                    $pointofsale['name'] = $row[2];

                    try {
                        if (is_numeric($row[1])) { //check row has values
                            //mã điểm bán hàng;
                            $pointofsale['code'] = $row[1];
                            //địa chỉ
                            $pointofsale['address'] = $row[3];
                            //mã đại lý
                            $branchCode = $row[8];
                            //echo $branchCode;die; //=LEFT(B3,8) => WithCalculatedFormulas; not working

                            if (!is_numeric($branchCode)) { //Check branch code
                                $posNoOuput['status'] = 'error';
                                $posNoOuput['message'] = 'Mã đại lý không đúng định dạng/bỏ trống: [ ' . $branchCode . ' ]';
                                array_push($this->importOutput['posNoOuput'], $posNoOuput);
                            }
                            else {
                                $username = $this->vn_to_str($row[5]);
                                $cmtnd = str_replace([' ', 'CMND', 'CCCD', 'MST', ':', "''", "'", '.'], '', $row[6]);
                                $phone = str_replace(["''", "'", ' ', '.'], '', $row[4]);
                                $email = $row[7];
                                if (empty($email)) {
                                    $email = $username . '@gmail.com';
                                }
                                if(empty($cmtnd))
                                    $cmtnd = $this->RandomCMTND();

                                //tạo trưởng điểm bán
                                $resultCheck = $this->CreateUser(
                                    'managepos',
                                    $username,
                                    $email,
                                    0,
                                    $cmtnd,
                                    $row[5],
                                    $phone,
                                    $username,
                                    '',
                                    '',
                                    '',
                                    '',
                                    0,
                                    0,
                                    $unknownSaleroomId,
                                    $pointofsale['code']
                                );

                                $valueCheck = $resultCheck['userId'];
                                if ($valueCheck == 0) {
                                    $posNoOuput['status'] = 'error';
                                    $posNoOuput['message'] = 'Thông tin trưởng điểm bán của điểm bán không hợp lệ. ' . $resultCheck['message'];
                                    array_push($this->importOutput['posNoOuput'], $posNoOuput);
                                } else {
                                    $posNoOuput['username'] = $resultCheck['username'];
                                    //kiểm tra xem đại lý đã tồn tại hay chưa   //Nếu chưa thì tạo
                                    $checkBranch = $this->CreateBranch($branchCode, $branchCode, null, $unknownCityId, '', true);
                                    $branchId = $checkBranch['code'];
                                    if ($branchId !== 0) {
                                        //kiểm tra xem điểm bán hàng đã tồn tại hay chưa
                                        $checkSaleroom = $this->CreateSaleRoom($pointofsale['name'], $pointofsale['code'], $branchId, $valueCheck, $pointofsale['address']);
                                        if($checkSaleroom['code'] > 0)
                                        {
                                            $posNoOuput['status'] = 'success';
                                            $posNoOuput['message'] = $checkSaleroom['message'];
                                            array_push($this->importOutput['posNoOuput'], $posNoOuput);
                                        }
                                        else{
                                            $posNoOuput['status'] = 'error';
                                            $posNoOuput['message'] = 'Thêm thất bại';
                                            array_push($this->importOutput['posNoOuput'], $posNoOuput);
                                        }
                                        // \DB::commit();
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // \DB::rollBack();
                        //dd($e);
                        $posNoOuput['status'] = 'error';
                        $posNoOuput['message'] = $e->getMessage();
                        array_push($this->importOutput['posNoOuput'], $posNoOuput);
                    }
                }


                //insert điểm bán hàng có giấy chứng nhận

                array_shift($listPointOfSalesHaveCertificate);
                array_shift($listPointOfSalesHaveCertificate);
                foreach ($listPointOfSalesHaveCertificate as $row) {

                    $pointofsale = [];
                    $posOuput = [];

                    $posOuput['stt'] = $row[0];
                    $posOuput['code'] = $row[1];
                    $posOuput['name'] = $row[2];

                    $userId = null;
                    try {
                        if (is_numeric($row[1])) { //check row has values
                            //tên điểm bán
                            $pointofsale['name'] = $row[2];
                            //mã điểm bán hàng;
                            $pointofsale['code'] = $row[1];
                            //địa chỉ
                            $pointofsale['address'] = $row[3];
                            //trưởng điểm bán
                            $userId = $row[5];
                            //mã đại lý
                            $branchCode = $row[6];

                            $posOuput['username'] = '';

                            if (!is_numeric($branchCode)) {
                                $posOuput['status'] = 'error';
                                $posOuput['message'] = 'Mã đại lý không đúng định dạng / bỏ trống: [ ' . $branchCode . ' ]';
                                array_push($this->importOutput['posHaveOuput'], $posOuput);
                            }
                            else {
                                $checkUserTM = 0;
                                if (!empty($userId)) {
                                    $checkUser = MdlUser::where('id', '=', $userId)->first();
                                    if (empty($checkUser)) {
                                        $checkCertificate = StudentCertificate::where('code', '=', $userId)
                                            ->leftJoin('mdl_user as mu', 'mu.id', '=', 'student_certificate.userid')
                                            ->first();
                                        if (!empty($checkCertificate)) {
                                            $userId = $checkCertificate->userid;
                                            $posOuput['username'] = $checkCertificate->username;
                                            $checkUserTM = 1;
                                        }
                                    } else {
                                        $posOuput['username'] = $checkUser->username;
                                        $checkUserTM = 1;
                                    }
                                }

                                if ($checkUserTM == 0) {
                                    $posOuput['username'] = "Mã nhân viên bỏ trống / nhân viên có mã không tồn tại";
                                }

                                //kiểm tra xem đại lý đã tồn tại hay chưa, ko thì tạo
                                $checkBranch = $this->CreateBranch($branchCode, $branchCode, $userId, $unknownCityId, '', true);
                                $branchId = $checkBranch['code'];
                                //kiểm tra xem điểm bán hàng đã tồn tại hay chưa, không thì tạo
                                $CheckpointOfSale = $this->CreateSaleRoom($pointofsale['name'], $pointofsale['code'], $branchId, $userId, $pointofsale['address']);

                                $posOuput['message'] = $CheckpointOfSale['message'];
                                $posOuput['status'] = 'success';
                                array_push($this->importOutput['posHaveOuput'], $posOuput);
                            }
                        } else {
                            //row không có dữ liệu -> continue;
                            continue;
                        }
                    } catch (\Exception $e) {
                        // \DB::rollBack();
                        $posOuput['message'] = $e->getMessage();
                        $posOuput['status'] = 'error';
                        array_push($this->importOutput['posHaveOuput'], $posOuput);
                    }
                }

                //insert nhân viên

                array_shift($listEmployees);
                array_shift($listEmployees);
                foreach ($listEmployees as $row) {
                    //Order No
                    $stt = $row[0];
                    $user = [];
                    $userOuput = [];
                    try {
                        //tên đầy đủ
                        $user['fullname'] = $row[6];
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $user['fullname']);
                        //username
                        $user['username'] = empty($row[4]) ? $this->vn_to_str($user['fullname']) : $row[4];
                        //mã chứng chỉ
                        $certificateCode = empty($row[1]) ? $row[2] : $row[1];
                        //Giới tính
                        $user['sex'] = $row[13] == 'Nam' or $row[13] == 'nam' ? 1 : 0;
                        //cmtnd
                        $user['cmtnd'] = str_replace([' ', 'CMND', 'CCCD', 'MST', ':', "''", "'", '.'], '', $row[8]);
                        //email
                        $user['email'] = $row[7];
                        if (empty($user['email'])) {
                            $user['email'] = $user['username'] . '@gmail.com';
                        }
                        //số điện thoại
                        $user['phone'] = str_replace(["''", "'", ' ', '.'], '', $row[11]);
                        $user['phone'] = str_replace(' ', '', $user['phone']);
                        $user['phone'] = str_replace('.', '', $user['phone']);
                        //địa chỉ
                        $user['address'] = $row[12];
                        //mã nhan vien
                        $user['code'] = $row[1];

                        //mã đại lý cấp giấy chứng nhận
                        $agencyCode = $row[3];

                        //mã đơn vị quản lý
                        $managementCode = $row[9];

                        //ngày sinh
                        $user['dob'] = $row[10];
                        //Kiểm tra nếu cột ngày tháng năm mà là dạng ngoài general (date, custom, ...) -> convert sang datetime
                        //ngày tháng năm sinh
                        if (is_numeric($user['dob'])) {
                            $getDate = Date::excelToDateTimeObject($row[10]);
                            $user['dob'] = $getDate->format('d-m-Y');
                        }

                        $timestamp = 0;

                        if (!empty($user['dob']) && strlen($user['dob']) > 7) {
                            //kiểm tra xem dob trong excel có chứa kí tự / không? nếu có thì replace thành -
                            if (strpos($user['dob'], '/') !== false) {
                                $newDate = strtotime(str_replace('/', '-', $user['dob']));
                            }
                            else if (strpos($user['dob'], '.') !== false) {
                                $newDate = strtotime(str_replace('.', '-', $user['dob']));
                            }
                            else if (strpos($user['dob'], '-') !== false) {
                                $newDate = strtotime($user['dob']);
                            } //nếu chứa chữ cái thì không hợp lệ
                            else if (preg_match("/[a-z]/i", $user['dob'])) {
                                $newDate = 0;
                            } else if (is_numeric($user['dob'])) {
                                $newDate = (int)$user['dob'];
                            } //còn nếu type của cột dob là dạng date sẽ convert sang timestamp
                            else {
                                $newDate = Date::excelToTimestamp($user['dob']);
                            }
                            //gán timestamp = newdate
                            $timestamp = $newDate;
                        }

                        //ngày bắt đầu làm
                        $user['start_date'] = $row[14];
                        //convert về dạng tiêu chuẩn
                        if (is_numeric($user['start_date'])) {
                            $getDateW = Date::excelToDateTimeObject($row[10]);
                            $user['start_date'] = $getDateW->format('d-m-Y');
                        }

                        //validate ngày bắt đầu làm
                        $timestamp_start = 0;
                        if (!empty($user['start_date']) && strlen($user['start_date']) > 7) {
                            //kiểm tra xem dob trong excel có chứa kí tự / không? nếu có thì replace thành -
                            if (strpos($user['start_date'], '/') !== false) {
                                $newDate_start = strtotime(str_replace('/', '-', $user['start_date']));
                            } else if (strpos($user['start_date'], '-') !== false) {
                                $newDate_start = strtotime($user['start_date']);
                            } //nếu chứa chữ cái thì không hợp lệ
                            else if (preg_match("/[a-z]/i", $user['start_date'])) {
                                $newDate_start = 0;
                            } else if (is_numeric($user['start_date'])) {
                                $newDate_start = (int)$user['start_date'];
                            } //còn nếu type của cột dob là dạng date sẽ convert sang timestamp
                            else {
                                $newDate_start = Date::excelToTimestamp($user['start_date']);
                            }
                            $timestamp_start = $newDate_start;
                        }

                        //tình trạng làm việc
                        $user['working_status'] = ($row[15] == 1) ? 1 : 0;

                        //da cap giay chung nhan
                        $user['confirm'] = 0;

                        //Check name valid
                        $validUTF8 = !(false === mb_detect_encoding($row[6], 'UTF-8', true));

                        if ($validUTF8) {
                            if(empty($user['cmtnd']))
                                $user['cmtnd'] = $this->RandomCMTND();
                            $resultCheck = $this->CreateUser(
                                'student',
                                $user['username'],
                                $user['email'],
                                0,
                                $user['cmtnd'],
                                $user['fullname'],
                                $user['phone'],
                                $user['code'],
                                $user['address'],
                                $user['sex'],
                                $timestamp,
                                $timestamp_start,
                                $user['working_status'],
                                $managementCode,
                                $unknownSaleroomId
                            );

                            $user_id = $resultCheck['userId'] != 0 ? $resultCheck['userId'] : 0;

                            //tạo thành công
                            if ($user_id != 0) {
                                //tạo certificate
                                if (!empty($certificateCode)) {
                                    $student = StudentCertificate::where('userid', $user_id)->first();
                                    //nếu học viên đã có mã thì không làm gì cả
                                    if (!$student) {
                                        //update status to 1
                                        StudentCertificate::create([
                                            'userid' => $user_id,
                                            'code' => $certificateCode,
                                            'status' => 1,
                                            'timecertificate' => time()
                                        ]);
                                    }
                                }
                                $userOuput['stt'] = $stt;
                                $userOuput['username'] = $resultCheck['username'];
                                $userOuput['fullname'] = $user['fullname'];
                                if ($resultCheck['type'] == 'update') {
                                    $userOuput['password'] = '';
                                } else {
                                    $userOuput['password'] = '123456789';
                                }
                                $userOuput['status'] = 'success';
                                $userOuput['cmtnd'] = $user['cmtnd'];
                                $userOuput['message'] = $resultCheck['message'];
                                $userOuput['code'] = $user_id;

                                array_push($this->importOutput['userOuput'], $userOuput);
                            }
                            else {
                                //show error khi không tạo được user
                                $userOuput = self::composeEmployeeErrorObject($stt, $user['cmtnd'], $user['fullname'], $resultCheck['message']);
                                array_push($this->importOutput['userOuput'], $userOuput);
                            }
                        }
                        else {
                            //show error wrong format utf8
                            $userOuput = self::composeEmployeeErrorObject($stt, $user['cmtnd'], $user['fullname'], 'Tên không đúng định dạng UTF-8');
                            array_push($this->importOutput['userOuput'], $userOuput);
                        }
                    }
                    catch (\Exception $e) {
                        //show error lỗi hệ thống
                        $userOuput = self::composeEmployeeErrorObject($stt, $row[8], $user['fullname'], json_encode($e->getMessage()));
                        array_push($this->importOutput['userOuput'], $userOuput);
                    }
                }

                //Export file for result
                $dataExport = [
                    'NhanVien' => $this->importOutput['userOuput'],
                    'DaiLy' => $this->importOutput['agencyOuput'],
                    'DiemBanHang co giay chung nhan' => $this->importOutput['posHaveOuput'],
                    'DiemBanHang k giay chung nhan' => $this->importOutput['posNoOuput']
                ];

                $filename = "bg_error_" . $inputFileName . ".xlsx";

                //xóa file cũ
                if (Storage::exists($filename)) {
                    Storage::delete($filename);
                }

                //ghi file vào thư mục storage
                $exportExcel = new ListMismatchData($dataExport);
                $exportExcel->store($filename, '', Excel::XLSX);
                \DB::commit();

                //return response()->json(self::status_message('success', __('cap_nhat_thanh_cong')));
            } catch (Exception $e) {
                //return response()->json(self::status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        }
    }

    public function importEmployee()
    {
        set_time_limit(0);

        $dir = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "import";
        //return files or folders in directory above
        $files = scandir($dir);
        $files = array_diff($files, array('.', '..'));
        $files = array_slice($files, 0, 1);

        foreach ($files as $file_path) {

            //check file is xlsx, xls
            $extension = pathinfo($file_path, PATHINFO_EXTENSION);

            if ($extension != 'xls' && $extension != 'xlsx') {
                return response()->json([
                    'extension' => 'error'
                ]);
            }

            $file_path = "import" . DIRECTORY_SEPARATOR . $file_path;

            $list_uploaded = (new DataImport)->toArray($file_path, '', '');
            $response = array();

            foreach ($list_uploaded as $user) {

                $errors = array();

                //Fetch data
                //Skip 2 first row and department name row, check first column is numeric or not
                $stt = $user[0];
                if (!is_numeric($stt)) {
                    continue;
                }

                $position_name = $user[6];

                if (strpos($position_name, Role::ROLE_MANAGER)) {
                    $role = Role::ROLE_MANAGER;
                } elseif (strpos($position_name, Role::ROLE_LEADER)) {
                    $role = Role::ROLE_LEADER;
                } elseif (strpos($position_name, 'executive')) {
                    $role = Role::ROLE_EMPLOYEE;
                } else { //Skip for other roles
                    $errors[] = 'Position is not available';
                }

                $department_name = $user[5];
                if (strlen($department_name) == 0) {
                    $errors[] = 'Department is missing';
                } else {
                    $department = TmsOrganization::firstOrCreate([
                        'code' => strtoupper($department_name),
                        'name' => $department_name
                    ]);
                }

                $email = $user[25];
                //Validate required fields
                if (strlen($email) == 0) {
                    $errors[] = 'Email is missing';
                } else {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Email is wrong format";
                    }
                }

                $full_name = $user[1];
                $first_name = $user[2];
                $middle_name = $user[3];
                $last_name = $user[4];

                if (strlen($full_name) == 0) {
                    $errors[] = 'Full name is missing';
                }

                if (strlen($first_name) == 0) {
                    $errors[] = 'First name is missing';
                }

                if (strlen($last_name) == 0) {
                    $errors[] = 'Last name is missing';
                }

                $personal_id = $user[18];
                if (strlen($personal_id) == 0) {
                    $errors[] = 'Personal id is missing';
                }

                $user = self::createEmployee(
                    $role,
                    $email,
                    $email,
                    1
                );

            }

        }
    }

    function composeEmployeeErrorObject($stt, $cmtnd, $fullname, $message) {
        $userOuput = array();

        $userOuput['stt'] = $stt;
        $userOuput['username'] = '';
        $userOuput['cmtnd'] = $cmtnd;
        $userOuput['fullname'] = $fullname;
        $userOuput['status'] = 'error';
        $userOuput['password'] = '';
        $userOuput['message'] = $message;

        return $userOuput;
    }

    public function vn_to_str($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);

        $str = str_replace(' ', '', $str);
        $str = strtolower($str);

        return $str;
    }

    public function checkUsernameAfterConvert($username) {
        $checkTest = 0;
        for ($i = 0; $i < strlen($username); $i++) {
            if (ord($username[$i]) > 127) {
                $checkTest = 1;
                break;
            }
        }
        return $checkTest;
    }

    public function CreateUser(
        $role_name,
        $username,
        $email,
        $confirm,
        $cmtnd,
        $fullname,
        $phone,
        $code,
        $address,
        $sex,
        $timestamp,
        $timestamp_start,
        $working_status,
        $managementCode,
        $unknownSaleroomId,
        $manageCode = ''
    ) {

        $newUserId = 0;
        $resultOutput = [
            'userId' => $newUserId,
            'message' => '',
            'type' => '',
            'username' => ''
        ];
        $userOutputMessages = [];
        $checkTM = 1;

        try {
            //Check các thông tin bắt buộc
            if (empty($email) || empty($cmtnd) || empty($username) || empty($fullname)) {

                $message = '';

                $userOuput['username'] = $username;

                $message .= 'Dữ liệu không đủ: ';

                $missing = array();

                if (!$username) {
                    $missing[]= 'Tài khoản';
                }
                if (!$email) {
                    $missing[] = 'Email';
                }
                if (!$cmtnd) {
                    $missing[] = 'Số CMTND';
                }
                if (!$fullname) {
                    $missing[] = 'Họ và tên';
                }
                $message .= implode(", ", $missing) . ' không được để trống.';
                $userOutputMessages[] = $message;
                $checkTM = 0;
            }

            //kiểm tra nếu tồn tại các trường và các trường đó sai định dạng
            $array = [
                'phone' => $phone,
                'cmtnd' => $cmtnd,
                'confirm' => $confirm
            ];

            $check = self::validate_fields($array, [
                'phone' => 'phone',
                'cmtnd' => 'text',
                'confirm' => 'boolean'
            ]);

            if(!empty($check)) {
                $msg = [];
                foreach($check as $item=>$value) {
                    switch ($item)
                    {
                        case 'cmtnd':
                            $item = "CMTND";
                            break;
                        case 'phone':
                            $item = "SDT";
                            break;
                        case 'confirm':
                            $item = "Confirm";
                            break;
                        default:
                            break;
                    }
                    $msg[]= $item. ': '.$value;
                }
                $userOutputMessages[] = implode(", ", $msg);
                $checkTM = 0;
            }

            $usernameNew = $username;

            //Nếu thỏa mãn
            if ($checkTM == 1)
            {
                //Xử lý tên user
                $nameExpl = explode(' ', $fullname);
                $rowname = count($nameExpl);
                $firstname = $nameExpl[$rowname - 1] ? $nameExpl[$rowname - 1] : '';
                $lastname = str_replace($nameExpl[$rowname - 1], '', $fullname);
                $lastname = $lastname ? $lastname : '';

//                \DB::beginTransaction();
//                \DB::disableQueryLog();

                //Khởi tạo saleroom default
                //Nếu k tìm thấy đại lý hay điểm bán thì gán vào unknown saleroom
                $position = TmsSaleRoomUser::POS;
                $positionId = $unknownSaleroomId;

                //'manageagents'
                //'managepos'

                $isUnknown = 1;

                if (strlen($manageCode) != 0) { // tạo trưởng điểm bán, trưởng đại lý
                    if ($role_name == 'managepos') {

                        $checkSaleRoom = TmsSaleRooms::where('code', '=', $manageCode)
                            ->CreateBranchJoin('mdl_user as mu', 'mu.id', '=', 'tms_sale_rooms.user_id')
                            ->select('mu.id', 'mu.username')
                            ->first();

                        if (
                            isset($checkSaleRoom)
                            && isset($checkSaleRoom->username)
                            && strpos($checkSaleRoom->username, $username) !== false
                        ) {
                            $newUserId = $checkSaleRoom->id;
                        }
                    }
                    if ($role_name == 'manageagents') {
                        $checkBranch = TmsBranch::where('code', '=', $manageCode)
                            ->leftJoin('mdl_user as mu', 'mu.id', '=', 'tms_branch.user_id')
                            ->select('mu.id', 'mu.username')
                            ->first();
                        if (isset($checkBranch)
                            && isset($checkBranch->username)
                            && strpos($checkBranch->username, $username) !== false
                        ) {
                            $newUserId = $checkBranch->id;
                        }
                    }
                }

                if ($newUserId == 0) { //check tiếp nếu k tìm thấy user có sẵn ở trên
                    if ($managementCode > 0) { //Có truyền mã đơn vị quản lý nhân viên này

                        //kiểm tra mã đơn vị quản lý
                        //kiểm tra trong điểm bán

                        $checkSaleRoomUser = TmsSaleRooms::where('code', '=', $managementCode)->first();
                        if (isset($checkSaleRoomUser)) {
                            $position = TmsSaleRoomUser::POS;
                            $positionId = $checkSaleRoomUser->id;
                            $isUnknown = 0;
                        } else {
                            //kiểm tra trong đại lý
                            $checkBranchUser = TmsBranch::where('code', '=', $managementCode)->first();
                            if (isset($checkBranchUser)) {
                                $position = TmsSaleRoomUser::AGENTS;
                                $positionId = $checkBranchUser->id;
                                $isUnknown = 0;
                            }
                        }
                    }

                    //lấy các user có tên tương tự
                    $user_related_series = MdlUser::where('username', 'like', "{$username}%")
                        ->leftJoin('tms_user_detail as tud', 'tud.user_id', '=', 'mdl_user.id')
                        ->leftJoin('tms_sale_room_user as tsru', 'tsru.user_id', '=', 'mdl_user.id')
                        ->select(
                            'mdl_user.id',
                            'mdl_user.username',
                            'mdl_user.email',
                            'tud.cmtnd',
                            'tud.phone',
                            'tud.address',
                            'tsru.sale_room_id',
                            'tsru.type'
                        )
                        ->get();

                    $max_append = 0;
                    $checkedUsers = [];

                    //nếu có user tương tự
                    if (count($user_related_series) > 0) {

                        foreach ($user_related_series as $user) {
                            $current_username = $user->username;
                            $append = substr($current_username, strlen($username), strlen($current_username));
                            if (strlen($append) == 0 || is_numeric($append)) { //hyquoccuong hyquocuong9 hyquoccuong6 ... not hyquoccuongdeptrai
                                if ($append > $max_append) {
                                    $max_append = $append;
                                }
                                //in the series
                                //Trùng cmt => cập nhật user, break loop
                                if ($user->cmtnd == $cmtnd) {
                                    $newUserId = $user->id;
                                    break;
                                }

                                if (strlen($user->phone) != 0 && strlen($phone) != 0 && $user->phone == $phone) {
                                    $newUserId = $user->id;
                                    break;
                                }

                                if (strlen($user->address) != 0 && strlen($address) != 0 && $user->address == $address) {
                                    $newUserId = $user->id;
                                    break;
                                }

                                //Khởi tạo
                                if (!isset($checkedUsers[$user->id][TmsSaleRoomUser::POS])) {
                                    $checkedUsers[$user->id][TmsSaleRoomUser::POS] = [];
                                }
                                if (!isset($checkedUsers[$user->id][TmsSaleRoomUser::AGENTS])) {
                                    $checkedUsers[$user->id][TmsSaleRoomUser::AGENTS] = [];
                                }

                                if (isset($user->sale_room_id)) {
                                    $checkedUsers[$user->id][$user->type][] = $user->sale_room_id;
                                }

                                $checkedUsers[$user->id]['username'] = $user->username;
                                $checkedUsers[$user->id]['cmtnd'] = isset($user->cmtnd) ? $user->cmtnd : null;
                            }
                        }

                        if (count($checkedUsers) != 0) { //has series
                            foreach ($checkedUsers as $checkedUserId => $checkedUser) {
                                //cung la fake user, cung chi nhanh(k có chi nhánh)
                                if (strpos($checkedUser['cmtnd'], '0000') !== false) {
                                    if (in_array($positionId, $checkedUser[$position])) {
                                        //đã tồn tại user và same branch or same saleroom
                                        $newUserId = $checkedUserId;
                                    }
                                }
                            }
                        }

                        $new_append = $max_append + 1;
                        $usernameNew = $username . $new_append;
                    }
                }

                //nếu user đã tồn tại -> cập nhật user
                if ($newUserId > 0) {
                    //cập nhật thông tin user
                    $userGet = MdlUser::where('id', $newUserId)->first();

                    $resultOutput['username'] = $userGet->username;


                    $userGet->redirect_type = 'lms';
                    $userGet->firstname = $firstname;
                    $userGet->lastname = $lastname;
                    $userGet->email = $email;
                    $userGet->save();

                    //cập nhật thông tin chi tiết user
                    $userGetTms = TmsUserDetail::where('user_id', $newUserId)->first();
                    $userGetTms->cmtnd = $cmtnd;
                    $userGetTms->fullname = $fullname;
                    $userGetTms->email = $email;
                    $userGetTms->phone = $phone;
                    $userGetTms->address = $address;
                    $userGetTms->sex = $sex ? $sex : 1;
                    $userGetTms->confirm = $confirm ? $confirm : 0;
                    $userGetTms->user_id = $newUserId;
                    $userGetTms->dob = $timestamp;
                    $userGetTms->working_status = $working_status;
                    $userGetTms->start_time = $timestamp_start;
                    $userGetTms->save();

                    //devcpt_log_system('user', '/system/user/edit/' . $newUserId, 'update', 'Import Update User: ' . $username);

                    $userOutputMessages[] = 'Cập nhật thành công';
                    $resultOutput['type'] = 'update';
                } else {
                    //thêm mới user với name + 1 số hoặc tạo mới hoàn toàn
                    $emailNew = $usernameNew . "@gmail.com";

                    $newUserId = $this->createUserOrg(
                        $usernameNew,
                        $firstname,
                        $lastname,
                        $emailNew,
                        $role_name,
                        $confirm,
                        $cmtnd,
                        $fullname,
                        $phone,
                        $code,
                        $address,
                        $sex,
                        $timestamp,
                        $timestamp_start,
                        $working_status
                    );
                    $resultOutput['type'] = 'create';
                    $resultOutput['username'] = $usernameNew;
                    $userOutputMessages[] = 'Thêm mới thành công';
                }

                //Tạo thành công user
                if ($newUserId != 0 ) {
                    //Nếu tồn tại mã quản lý, nhân viên only
                    if($managementCode > 0)
                    {
                        $createPositionUser = $this->CreateSaleRoomUser($positionId, $newUserId, $position);
                        if ($createPositionUser['code'] == 0) {
                            if ($isUnknown == 0) {
                                $userOutputMessages[] = 'Không gán được user vào '. $position. ' ' . $managementCode;
                            } else {
                                $userOutputMessages[] = 'Không tìm thấy điểm bán và đại lý tương ứng, gán nhân viên vào điểm bán unknowsaleroom ' . $unknownSaleroomId;
                            }
                        }
                    }

                    //add user vao khung nang luc chung chi trong he thong sau khi tạo (day la khung nang luc bat buoc)
//                    $trainning = new TmsTrainningUser();
//                    $trainning->user_id = $newUserId;
//                    $trainning->trainning_id = 1; //id khung nang luc bat buoc
//                    $trainning->save();

                    $trainning = TmsTrainningUser::firstOrCreate([
                        'user_id' => $newUserId,
                        'trainning_id' => 1 //id khung nang luc bat buoc
                    ]);
                    $category = TmsTrainningCategory::select('category_id')
                        ->where('trainning_id', $trainning->trainning_id)
                        ->first()
                        ->toArray();
                    self::training_enrole($newUserId, $category['category_id']);

                    $resultOutput['userId'] = $newUserId;
                    $resultOutput['message'] = implode(". ", $userOutputMessages);

                    return $resultOutput;
                }
//                \DB::commit();
            }
        }
        catch (\Exception $e) {
//            \DB::rollBack();
            $checkUsername = self::checkUsernameAfterConvert($username);
            if ($checkUsername == 1) {
                $userOutputMessages[] = 'Tên sai định dạng, không thể chuyển về dạng không dấu: ' . $fullname;
            } else {
                //dd($e);
                $userOutputMessages[] = $e->getMessage();
            }
        }
        $resultOutput['message'] = implode(". ", $userOutputMessages);
        return $resultOutput;
    }

    public function createEmployee(
        $role,
        $username,
        $email,
        $personal_id,
        $full_name,
        $first_name,
        $middle_name,
        $last_name,


        $phone,
        $code,
        $address,
        $sex,
        $timestamp,
        $timestamp_start,
        $working_status
    ) {

        $newUserId = 0;
        $resultOutput = [
            'userId' => $newUserId,
            'message' => '',
            'type' => '',
            'username' => ''
        ];
        $userOutputMessages = [];
        $checkTM = 1;

        try {
            //Check các thông tin bắt buộc
            if (empty($email) || empty($cmtnd) || empty($username) || empty($fullname)) {

                $message = '';

                $userOuput['username'] = $username;

                $message .= 'Dữ liệu không đủ: ';

                $missing = array();

                if (!$username) {
                    $missing[]= 'Tài khoản';
                }
                if (!$email) {
                    $missing[] = 'Email';
                }
                if (!$cmtnd) {
                    $missing[] = 'Số CMTND';
                }
                if (!$fullname) {
                    $missing[] = 'Họ và tên';
                }
                $message .= implode(", ", $missing) . ' không được để trống.';
                $userOutputMessages[] = $message;
                $checkTM = 0;
            }

            //kiểm tra nếu tồn tại các trường và các trường đó sai định dạng
            $array = [
                'phone' => $phone,
                'cmtnd' => $cmtnd,
                'confirm' => $confirm
            ];

            $check = self::validate_fields($array, [
                'phone' => 'phone',
                'cmtnd' => 'text',
                'confirm' => 'boolean'
            ]);

            if(!empty($check)) {
                $msg = [];
                foreach($check as $item=>$value) {
                    switch ($item)
                    {
                        case 'cmtnd':
                            $item = "CMTND";
                            break;
                        case 'phone':
                            $item = "SDT";
                            break;
                        case 'confirm':
                            $item = "Confirm";
                            break;
                        default:
                            break;
                    }
                    $msg[]= $item. ': '.$value;
                }
                $userOutputMessages[] = implode(", ", $msg);
                $checkTM = 0;
            }

            $usernameNew = $username;

            //Nếu thỏa mãn
            if ($checkTM == 1)
            {
                //Xử lý tên user
                $nameExpl = explode(' ', $fullname);
                $rowname = count($nameExpl);
                $firstname = $nameExpl[$rowname - 1] ? $nameExpl[$rowname - 1] : '';
                $lastname = str_replace($nameExpl[$rowname - 1], '', $fullname);
                $lastname = $lastname ? $lastname : '';

//                \DB::beginTransaction();
//                \DB::disableQueryLog();

                //Khởi tạo saleroom default
                //Nếu k tìm thấy đại lý hay điểm bán thì gán vào unknown saleroom
                $position = TmsSaleRoomUser::POS;
                $positionId = $unknownSaleroomId;

                //'manageagents'
                //'managepos'

                $isUnknown = 1;

                if (strlen($manageCode) != 0) { // tạo trưởng điểm bán, trưởng đại lý
                    if ($role_name == 'managepos') {

                        $checkSaleRoom = TmsSaleRooms::where('code', '=', $manageCode)
                            ->CreateBranchJoin('mdl_user as mu', 'mu.id', '=', 'tms_sale_rooms.user_id')
                            ->select('mu.id', 'mu.username')
                            ->first();

                        if (
                            isset($checkSaleRoom)
                            && isset($checkSaleRoom->username)
                            && strpos($checkSaleRoom->username, $username) !== false
                        ) {
                            $newUserId = $checkSaleRoom->id;
                        }
                    }
                    if ($role_name == 'manageagents') {
                        $checkBranch = TmsBranch::where('code', '=', $manageCode)
                            ->leftJoin('mdl_user as mu', 'mu.id', '=', 'tms_branch.user_id')
                            ->select('mu.id', 'mu.username')
                            ->first();
                        if (isset($checkBranch)
                            && isset($checkBranch->username)
                            && strpos($checkBranch->username, $username) !== false
                        ) {
                            $newUserId = $checkBranch->id;
                        }
                    }
                }

                if ($newUserId == 0) { //check tiếp nếu k tìm thấy user có sẵn ở trên
                    if ($managementCode > 0) { //Có truyền mã đơn vị quản lý nhân viên này

                        //kiểm tra mã đơn vị quản lý
                        //kiểm tra trong điểm bán

                        $checkSaleRoomUser = TmsSaleRooms::where('code', '=', $managementCode)->first();
                        if (isset($checkSaleRoomUser)) {
                            $position = TmsSaleRoomUser::POS;
                            $positionId = $checkSaleRoomUser->id;
                            $isUnknown = 0;
                        } else {
                            //kiểm tra trong đại lý
                            $checkBranchUser = TmsBranch::where('code', '=', $managementCode)->first();
                            if (isset($checkBranchUser)) {
                                $position = TmsSaleRoomUser::AGENTS;
                                $positionId = $checkBranchUser->id;
                                $isUnknown = 0;
                            }
                        }
                    }

                    //lấy các user có tên tương tự
                    $user_related_series = MdlUser::where('username', 'like', "{$username}%")
                        ->leftJoin('tms_user_detail as tud', 'tud.user_id', '=', 'mdl_user.id')
                        ->leftJoin('tms_sale_room_user as tsru', 'tsru.user_id', '=', 'mdl_user.id')
                        ->select(
                            'mdl_user.id',
                            'mdl_user.username',
                            'mdl_user.email',
                            'tud.cmtnd',
                            'tud.phone',
                            'tud.address',
                            'tsru.sale_room_id',
                            'tsru.type'
                        )
                        ->get();

                    $max_append = 0;
                    $checkedUsers = [];

                    //nếu có user tương tự
                    if (count($user_related_series) > 0) {

                        foreach ($user_related_series as $user) {
                            $current_username = $user->username;
                            $append = substr($current_username, strlen($username), strlen($current_username));
                            if (strlen($append) == 0 || is_numeric($append)) { //hyquoccuong hyquocuong9 hyquoccuong6 ... not hyquoccuongdeptrai
                                if ($append > $max_append) {
                                    $max_append = $append;
                                }
                                //in the series
                                //Trùng cmt => cập nhật user, break loop
                                if ($user->cmtnd == $cmtnd) {
                                    $newUserId = $user->id;
                                    break;
                                }

                                if (strlen($user->phone) != 0 && strlen($phone) != 0 && $user->phone == $phone) {
                                    $newUserId = $user->id;
                                    break;
                                }

                                if (strlen($user->address) != 0 && strlen($address) != 0 && $user->address == $address) {
                                    $newUserId = $user->id;
                                    break;
                                }

                                //Khởi tạo
                                if (!isset($checkedUsers[$user->id][TmsSaleRoomUser::POS])) {
                                    $checkedUsers[$user->id][TmsSaleRoomUser::POS] = [];
                                }
                                if (!isset($checkedUsers[$user->id][TmsSaleRoomUser::AGENTS])) {
                                    $checkedUsers[$user->id][TmsSaleRoomUser::AGENTS] = [];
                                }

                                if (isset($user->sale_room_id)) {
                                    $checkedUsers[$user->id][$user->type][] = $user->sale_room_id;
                                }

                                $checkedUsers[$user->id]['username'] = $user->username;
                                $checkedUsers[$user->id]['cmtnd'] = isset($user->cmtnd) ? $user->cmtnd : null;
                            }
                        }

                        if (count($checkedUsers) != 0) { //has series
                            foreach ($checkedUsers as $checkedUserId => $checkedUser) {
                                //cung la fake user, cung chi nhanh(k có chi nhánh)
                                if (strpos($checkedUser['cmtnd'], '0000') !== false) {
                                    if (in_array($positionId, $checkedUser[$position])) {
                                        //đã tồn tại user và same branch or same saleroom
                                        $newUserId = $checkedUserId;
                                    }
                                }
                            }
                        }

                        $new_append = $max_append + 1;
                        $usernameNew = $username . $new_append;
                    }
                }

                //nếu user đã tồn tại -> cập nhật user
                if ($newUserId > 0) {
                    //cập nhật thông tin user
                    $userGet = MdlUser::where('id', $newUserId)->first();

                    $resultOutput['username'] = $userGet->username;


                    $userGet->redirect_type = 'lms';
                    $userGet->firstname = $firstname;
                    $userGet->lastname = $lastname;
                    $userGet->email = $email;
                    $userGet->save();

                    //cập nhật thông tin chi tiết user
                    $userGetTms = TmsUserDetail::where('user_id', $newUserId)->first();
                    $userGetTms->cmtnd = $cmtnd;
                    $userGetTms->fullname = $fullname;
                    $userGetTms->email = $email;
                    $userGetTms->phone = $phone;
                    $userGetTms->address = $address;
                    $userGetTms->sex = $sex ? $sex : 1;
                    $userGetTms->confirm = $confirm ? $confirm : 0;
                    $userGetTms->user_id = $newUserId;
                    $userGetTms->dob = $timestamp;
                    $userGetTms->working_status = $working_status;
                    $userGetTms->start_time = $timestamp_start;
                    $userGetTms->save();

                    //devcpt_log_system('user', '/system/user/edit/' . $newUserId, 'update', 'Import Update User: ' . $username);

                    $userOutputMessages[] = 'Cập nhật thành công';
                    $resultOutput['type'] = 'update';
                } else {
                    //thêm mới user với name + 1 số hoặc tạo mới hoàn toàn
                    $emailNew = $usernameNew . "@gmail.com";

                    $newUserId = $this->createUserOrg(
                        $usernameNew,
                        $firstname,
                        $lastname,
                        $emailNew,
                        $role_name,
                        $confirm,
                        $cmtnd,
                        $fullname,
                        $phone,
                        $code,
                        $address,
                        $sex,
                        $timestamp,
                        $timestamp_start,
                        $working_status
                    );
                    $resultOutput['type'] = 'create';
                    $resultOutput['username'] = $usernameNew;
                    $userOutputMessages[] = 'Thêm mới thành công';
                }

                //Tạo thành công user
                if ($newUserId != 0 ) {
                    //Nếu tồn tại mã quản lý, nhân viên only
                    if($managementCode > 0)
                    {
                        $createPositionUser = $this->CreateSaleRoomUser($positionId, $newUserId, $position);
                        if ($createPositionUser['code'] == 0) {
                            if ($isUnknown == 0) {
                                $userOutputMessages[] = 'Không gán được user vào '. $position. ' ' . $managementCode;
                            } else {
                                $userOutputMessages[] = 'Không tìm thấy điểm bán và đại lý tương ứng, gán nhân viên vào điểm bán unknowsaleroom ' . $unknownSaleroomId;
                            }
                        }
                    }

                    //add user vao khung nang luc chung chi trong he thong sau khi tạo (day la khung nang luc bat buoc)
//                    $trainning = new TmsTrainningUser();
//                    $trainning->user_id = $newUserId;
//                    $trainning->trainning_id = 1; //id khung nang luc bat buoc
//                    $trainning->save();

                    $trainning = TmsTrainningUser::firstOrCreate([
                        'user_id' => $newUserId,
                        'trainning_id' => 1 //id khung nang luc bat buoc
                    ]);
                    $category = TmsTrainningCategory::select('category_id')
                        ->where('trainning_id', $trainning->trainning_id)
                        ->first()
                        ->toArray();
                    self::training_enrole($newUserId, $category['category_id']);

                    $resultOutput['userId'] = $newUserId;
                    $resultOutput['message'] = implode(". ", $userOutputMessages);

                    return $resultOutput;
                }
//                \DB::commit();
            }
        }
        catch (\Exception $e) {
//            \DB::rollBack();
            $checkUsername = self::checkUsernameAfterConvert($username);
            if ($checkUsername == 1) {
                $userOutputMessages[] = 'Tên sai định dạng, không thể chuyển về dạng không dấu: ' . $fullname;
            } else {
                //dd($e);
                $userOutputMessages[] = $e->getMessage();
            }
        }
        $resultOutput['message'] = implode(". ", $userOutputMessages);
        return $resultOutput;
    }

    public function CreateBranch($name, $code, $user_id, $city, $address, $unknown = false)
    {
        try {
//            \DB::beginTransaction();

//            $check_name = DB::table('tms_branch as tb')
//                ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
//                ->where('tb.name', '=', $name)
//                ->where('tcb.city_id', '=', $city)
//                ->count();
//
//            if ($check_name > 0) {
//                $branch = DB::table('tms_branch as tb')
//                    ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
//                    ->where('tb.name', '=', $name)
//                    ->where('tcb.city_id', '=', $city)->first();
//                if ($user_id) {
//                    $branch->user_id = $user_id;
//                    $branch->save();
//                }
//                return [
//                    'code' => $branch->id,
//                    'message' => 'Tên Đại lý đã tồn tại.'
//                ];
//            }

            $check_branch = TmsBranch::where('code', $code)->first();
            if (isset($check_branch)) {
                if ($user_id) {
                    $check_branch->user_id = $user_id;
                    $check_branch->save();
                }
                $message = "Cập nhật thành công";
            } else {
                $check_branch = new TmsBranch;
                $check_branch->name = $name;
                $check_branch->code = $code;
                $check_branch->user_id = $user_id;
                $check_branch->address = $address;
                $check_branch->save();
                $message = 'Thêm đại lý thành công';
            }

            $city_branch = TmsCityBranch::where('branch_id', $check_branch->id)->first();
            if ($city_branch) {
                if ((strlen($city_branch->city_id) == 0 && $unknown) || !$unknown) {
                    $city_branch->city_id = $city;
                }
                $city_branch->save();
            } else {
                $cityBranch = new TmsCityBranch;
                $cityBranch->city_id = $city;
                $cityBranch->branch_id = $check_branch->id;
                $cityBranch->save();
            }

            if ($user_id && $user_id != 0 && !self::has_user_market($user_id)) {
                self::add_role_for_user($user_id);
            }

            if (self::has_user_market()) {
                $role_organize = new TmsRoleOrganize;
                $role_organize->user_id = Auth::id();
                $role_organize->organize_id = $check_branch->id;
                $role_organize->type = 'branch';
                $role_organize->save();
            }

//            devcpt_log_system('organize', '/system/organize/branch/edit/' . $tmsBranch->id, 'create', 'Thêm mới Đại lý: ' . $name);
//            \DB::commit();
            return [
                'code' => $check_branch->id,
                'message' => $message
            ];
        } catch (Exception $e) {
//            \DB::rollBack();
            return [
                'code' => 0,
                'message' => 'Lỗi hệ thống'
            ];
        }
    }

    public function CreateSaleRoom($name, $code, $branch, $user_id, $address)
    {
        try {
            if (!empty($name)) {
                $check_name = DB::table('tms_sale_rooms as tsr')
                    ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
                    ->where('tsr.name', '=', $name)
                    ->where('tbsr.branch_id', '=', $branch)
                    ->count();
                if ($check_name > 0) {
                    $sale_room = DB::table('tms_sale_rooms as tsr')
                        ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
                        ->where('tsr.name', '=', $name)
                        ->where('tbsr.branch_id', '=', $branch)
                        ->first();
                    $this->UpdateSaleRoom($sale_room->id, $user_id, $name, $code, $address, $branch);
                    return [
                        'code' => $sale_room->id,
                        'type' => 'update',
                        'message' => 'Cập nhật thành công.'
                    ];
                }
            }

            $sale_room = TmsSaleRooms::where('code', $code)->first();
            if ($sale_room) {
                $this->UpdateSaleRoom($sale_room->id, $user_id, $name, $code, $address, $branch);
                return [
                    'code' => $sale_room->id,
                    'type' => 'update',
                    'message' => 'Cập nhật thành công.'
                ];
            }
//            \DB::beginTransaction();
            $tmsSaleRoom = new TmsSaleRooms;
            $tmsSaleRoom->name = $name;
            $tmsSaleRoom->address = $address;
            $tmsSaleRoom->user_id = $user_id;
            $tmsSaleRoom->code = $code;
            $tmsSaleRoom->save();

            if ($branch != 0) {
                $branchRoom = new TmsBranchSaleRoom;
                $branchRoom->branch_id = $branch;
                $branchRoom->sale_room_id = $tmsSaleRoom->id;
                $branchRoom->save();
            }

            if ($user_id && $user_id != 0 && !self::has_user_market($user_id)) {
                self::add_managepos_for_user($user_id);
            }

            //devcpt_log_system('organize', '/system/organize/saleroom/edit/' . $tmsSaleRoom->id, 'create', 'Thêm mới Điểm bán: ' . $name);
//            \DB::commit();
            return [
                'code' => $tmsSaleRoom->id,
                'type' => 'add',
                'message' => 'Thêm điểm bán thành công'
            ];
        } catch (\Exception $e) {
//            \DB::rollBack();
            return [
                'code' => 0,
                'type' => "error",
                'message' => 'Lỗi dữ liệu, kiểm tra lại'
            ];
        }
    }

    public function updateBranchSaleroom($branch_id, $saleroom_id) {
        $check = TmsBranchSaleRoom::where("sale_room_id", $saleroom_id)->first();
        if (!$check) {
            $branchRoom = new TmsBranchSaleRoom;
            $branchRoom->branch_id = $branch_id;
            $branchRoom->sale_room_id = $saleroom_id;
            $branchRoom->save();
        } else {
            $check->branch_id = $branch_id;
            $check->save();
        }
    }

    public function UpdateSaleRoom($saleroom_id, $user_id, $name, $code, $address, $branch_id)
    {
        try {
//           \DB::beginTransaction();
            $saleRoom = TmsSaleRooms::findOrFail($saleroom_id);
            if ($user_id && $user_id != 0 && $saleRoom['user_id'] != $user_id && !self::has_user_market($user_id)) {
                $role = Role::where('name', Role::MANAGE_POS)->first();
                ModelHasRole::where([
                    'role_id' => $role['id'],
                    'model_id' => $saleRoom['user_id']
                ])->delete();
                self::add_managepos_for_user($user_id);
            }

            $saleRoom->name = $name;
            $saleRoom->code = $code;
            if (is_numeric($user_id) && $user_id != 0) {
                $saleRoom->user_id = $user_id;
            }
            $saleRoom->address = $address;
            $saleRoom->save();

            $this->updateBranchSaleroom($branch_id, $saleroom_id);
//           devcpt_log_system('organize', '/system/organize/saleroom/edit/' . $saleroom_id, 'edit', 'Sửa Điểm bán: ' . $name);
//           \DB::commit();
        } catch (\exception $e) {
        }
    }

    public function CreateSaleRoomUser($managementId, $user_id, $type)
    {
        try {
            $saleRoomUser = TmsSaleRoomUser::firstOrCreate([
                'sale_room_id' => $managementId,
                'user_id' => $user_id,
                'type' => $type
            ]);

            return [
                'code' => $saleRoomUser->id,
                'message' => 'Thêm thành công'
            ];
        } catch (\Exception $e) {
            return [
                'code' => 0,
                'message' => 'Lỗi dữ liệu, kiểm tra lại'
            ];
        }
    }

    public function randomNumber($length)
    {
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }

    public function createUserOrg($usernameNew, $firstname, $lastname, $email, $role_name, $confirm, $cmtnd, $fullname, $phone, $code, $address, $sex, $timestamp, $timestamp_start, $working_status)
    {
        $role = Role::select('id', 'name', 'mdl_role_id')->where('name', $role_name)->first();
        $mdlUser = new MdlUser;
        $mdlUser->username = $usernameNew;
        $mdlUser->password = bcrypt('123456789');
        $mdlUser->redirect_type = 'lms';
        $mdlUser->firstname = $firstname;
        $mdlUser->lastname = $lastname;
        $mdlUser->email = $email;
        $mdlUser->save();

        if ($role) {
            self::add_user_by_role($mdlUser->id, $role['id']);
            self::enrole_lms($mdlUser->id, $role['mdl_role_id'], $confirm ? $confirm : 0);
        }

        $tmsUser = new TmsUserDetail;
        $tmsUser->cmtnd = $cmtnd;
        $tmsUser->fullname = $fullname;
        $tmsUser->email = $email;
        $tmsUser->phone = $phone;
        $tmsUser->code = $code;
        $tmsUser->address = $address;
        $tmsUser->sex = $sex ? $sex : 1;
        $tmsUser->confirm = $confirm ? $confirm : 0;
        $tmsUser->user_id = $mdlUser->id;
        $tmsUser->dob = $timestamp;
        $tmsUser->working_status = $working_status;
        $tmsUser->start_time = $timestamp_start;
        $tmsUser->save();

        return $mdlUser->id;
    }

    public function RandomCMTND()
    {
        $checkUserCMTND = TmsUserDetail::where('cmtnd', 'like', "0000%")->get()->toArray();
        $number = 0;
        $max = 0;
        if (count($checkUserCMTND) > 0) {
            //vòng lặp qua các username trùng
            foreach ($checkUserCMTND as $checkedUser) {
                if (is_numeric($checkedUser['cmtnd']) && $checkedUser['cmtnd'] > $max)
                    $max = $checkedUser['cmtnd'];
            }
        } else {
            $max = 1;
        }
        $max += 1;
        $result = '';
        for ($i = 0; $i < (12 - strlen($max)); $i++) {
            $result .= 0;
        }
        return $result . $max;
    }

    function validate_fields($array, $keys)
    {
        $check = [];
        if (!empty($keys)) {
            foreach ($keys as $key => $value) {
                switch ($value) {
                    case 'text':
                        $validator = Validator::make($array, [
                            $key => [
                                "regex:/^[a-zA-Z0-9\-\_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\.\,\s\!\%\/\@]*$/i",
                            ],
                        ]);
                        if ($validator->fails() && $array[$key])
                            $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                        //array_push($check, $key);
                        break;

                    case 'code':
                        $validator = Validator::make($array, [
                            $key => [
                                "regex:/^[a-zA-Z0-9\-\_\.\/]*$/i",
                            ],
                        ]);
                        if ($validator->fails() && $array[$key])
                            $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                        //array_push($check, $key);
                        break;

                    case 'password': //Min 8, 1 chu hoa, 1 ki tu dac biet @$!%*?#&
                        $validator = Validator::make($array, [
                            $key => [
                                "regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?#&])[A-Za-z\d@$!%*?#&]{8,}$/i",
                            ],
                        ]);
                        if ($validator->fails() && $array[$key])
                            $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                        //array_push($check, $key);
                        break;

                    case 'token':
                        $validator = Validator::make($array, [
                            $key => [
                                "regex:/^[a-zA-Z0-9\:\-\_\.\=\@\!$\#]*$/i",
                            ],
                        ]);
                        if ($validator->fails() && $array[$key])
                            $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                        //array_push($check, $key);
                        break;
                    case 'longtext':
                        $validator = Validator::make($array, [
                            $key => [
                                "not_regex:/<script[^>]*>(.*?)<\/script>/is",
                                "not_regex:/<style[^>]*>(.*?)<\/style>/is",
                                "not_regex:/<button[^>]*>(.*?)<\/button>/is",
                                "not_regex:/<input[^>]*>/is",
                                "not_regex:/<select[^>]*>(.*?)<\/select>/is",
                                "not_regex:/<meta[^>]*>/is",

                                "not_regex:/<[^>]*value[^>]*>/is",
                                "not_regex:/<[^>]*style[^>]*>/is",
                                "not_regex:/<[^>]*onclick[^>]*>/is",
                                "not_regex:/<[^>]*onsubmit[^>]*>/is",
                                "not_regex:/<[^>]*onmouseover[^>]*>/is",
                                "not_regex:/<[^>]*onmouseout[^>]*>/is",
                                "not_regex:/<[^>]*onload[^>]*>/is",
                                "not_regex:/<[^>]*onscroll[^>]*>/is",
                                "not_regex:/<[^>]*onchange[^>]*>/is",
                                "not_regex:/<a[^>]*href[^>]*\"[^>]*\?[^>]*\"[^>]*>[^>]*<\/a>/is",
                                "not_regex:/<[^>]*src[^>]*\"[^>]*\?[^>]*\"[^>]*\/>/is",
                                "not_regex:/<a[^>]*href[^>]*'[^>]*\?[^>]*'[^>]*>[^>]*<\/a>/is",
                                "not_regex:/<[^>]*src[^>]*'[^>]*\?[^>]*'[^>]*\/>/is",
                                "not_regex:/(>=|<=|==|&&|\|\|)/is",
                                /*"not_regex:/[^>]*if([^>]*)/is",*/
                            ],
                        ]);
                        if ($validator->fails() && $array[$key])
                            $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                        //array_push($check, $key);
                        break;

                    case 'number':
                        $validator = Validator::make($array, [
                            $key => [
                                "regex:/^[0-9]*$/i"
                            ],
                        ]);
                        if ($validator->fails() && $array[$key])
                            $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                        //array_push($check, $key);
                        break;

                    case 'phone':
                        $validator = Validator::make($array, [
                            $key => [
                                "regex:/^[0-9\.\-\s]*$/i"
                            ],
                        ]);
                        if (
                        $validator->fails()
//                        && $array[$key]
//                        && strlen($array[$key]) > 12
//                        && $array[$key] != 'NULL'
                        )
                            $check[$key] = __('loi_format_gia_tri_hay_nhap_lai');
                        if (strlen($array[$key]) > 12)
                            $check[$key] = __('loi_format_gia_tri_hay_nhap_lai');
                        //array_push($check, $key);
                        break;

                    case 'date':
                        $validator = Validator::make($array, [
                            $key => [
                                "regex:/^[0-9\-\/]*$/i"
                            ],
                        ]);
                        if (
                            $validator->fails() && $array[$key] &&
                            $array[$key] != 'null' && $array[$key] != 'NULL'
                        )
                            $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                        //array_push($check, $key);
                        break;

                    case 'email':
                        $validator = Validator::make($array, [
                            $key => [
                                "regex:/^.+@.+$/i",
                                "regex:/^[a-zA-Z0-9.@\_\-]*$/i",
                            ],
                        ]);
                        if ($validator->fails() && $array[$key])
                            $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                        //array_push($check, $key);
                        break;

                    case 'boolean':
                        $validator = Validator::make($array, [
                            $key => [
                                "regex:/^(true|false|1|0)$/i"
                            ],
                        ]);
                        if ($validator->fails() && $array[$key])
                            $check[$key] = __('loi_dinh_dang_truong_nhap_vao_co_chua_ky_tu_khong_cho_phep');
                        //array_push($check, $key);
                        break;

                    case 'image':
                        $validator = Validator::make($array, [
                            $key => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        ]);
                        if ($validator->fails() && $array[$key])
                            $check[$key] = __('format_image');
                        //array_push($check, $key);
                        break;
                }
            }
        }
        return $check;
    }

    function enrole_lms($user_id, $role_id, $confirm)
    {
        $mdl_role = MdlRole::findOrFail($role_id);
        $context_id = 1;
        if ($mdl_role['shortname'] == 'student') {
            /*if ($confirm == 0) {
                $courses = DB::table('mdl_course_categories as cate')
                    ->join('mdl_course as course', 'course.category', '=', 'cate.id')
                    ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'course.id')
                    ->select('course.id as course_id')
                    ->where('cate.id', '=', 3)
                    ->get();
                if ($courses) {
                    foreach ($courses as $course) {
    //                    $enrole = DB::table('mdl_enrol')
    //                        ->where('enrol', '=', 'manual')
    //                        ->where('courseid', '=', $course->course_id)
    //                        ->first();
    //                    if (!$enrole) {

                        $enrole = MdlEnrol::firstOrCreate(

                            [
                                'enrol' => 'manual',
                                'courseid' => $course->course_id,
                                'roleid' => $role_id
                            ],
                            [
                                'sortorder' => 0,
                                'status' => 0,
                                'expirythreshold' => 86400,
                                'timecreated' => strtotime(Carbon::now()),
                                'timemodified' => strtotime(Carbon::now()),
                            ]
                        );

    //                        MdlEnrol::firstOrCreate(
    //                            [
    //                                'enrol' => 'guest',
    //                                'courseid' => $course->course_id,
    //                                'roleid' => $role_id,
    //                                'sortorder' => 1
    //                            ],
    //                            [
    //                                'expirythreshold' => 86400,
    //                                'timecreated' => strtotime(Carbon::now()),
    //                                'timemodified' => strtotime(Carbon::now())
    //                            ]
    //                        );
    //                        MdlEnrol::firstOrCreate(
    //                            [
    //                                'enrol' => 'self',
    //                                'courseid' => $course->course_id,
    //                                'roleid' => $role_id,
    //                                'sortorder' => 2
    //                            ],
    //                            [
    //                                'expirythreshold' => 86400,
    //                                'timecreated' => strtotime(Carbon::now()),
    //                                'timemodified' => strtotime(Carbon::now())
    //                            ]
    //                        );
                        // }
                        $checkEnrole = DB::table('mdl_user_enrolments')
                            ->where('enrolid', '=', $enrole->id)
                            ->where('userid', '=', $user_id)
                            ->first();
                        if (!$checkEnrole) {
                            DB::table('mdl_user_enrolments')->insert([
                                'enrolid' => $enrole->id,
                                'userid' => $user_id
                            ]);
                        }

                        $context = DB::table('mdl_context')
                            ->where('instanceid', '=', $course->course_id)
                            ->where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSE)
                            ->first();
                        $context_id = $context ? $context->id : 1;
                        $mdlRoleAssignment = MdlRoleAssignments::where([
                            'roleid' => $role_id,
                            'userid' => $user_id,
                            'contextid' => $context_id
                        ])->first();
                        if (!$mdlRoleAssignment) {
                            $roleAssign = new MdlRoleAssignments;
                            $roleAssign->roleid = $role_id;
                            $roleAssign->userid = $user_id;
                            $roleAssign->contextid = $context_id;
                            $roleAssign->save();
                        }

                        //lay gia trị trong bang mdl_grade_items
                        $mdl_grade_item = MdlGradeItem::where('courseid', $course->course_id)->first();

                        if ($mdl_grade_item) {
                            //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
                            MdlGradeGrade::firstOrCreate([
                                'userid' => $user_id,
                                'itemid' => $mdl_grade_item->id
                            ]);
                        }

                        //write log to notifications
                        self::insert_single_notification(\App\TmsNotification::MAIL, $user_id, \App\TmsNotification::ENROL, $course->course_id);
                    }
                }
            } else {
                $mdlRoleAssignment = MdlRoleAssignments::where([
                    'roleid' => $role_id,
                    'userid' => $user_id,
                    'contextid' => $context_id
                ])->first();
                if (!$mdlRoleAssignment) {
                    $roleAssign = new MdlRoleAssignments;
                    $roleAssign->roleid = $role_id;
                    $roleAssign->userid = $user_id;
                    $roleAssign->contextid = $context_id;
                    $roleAssign->save();
                }
            }*/
        } else {
            /*if ($mdl_role['shortname'] == 'manager') {
                $context_id = 1;
            }*/
            $mdlRoleAssignment = MdlRoleAssignments::where([
                'roleid' => $role_id,
                'userid' => $user_id,
                'contextid' => $context_id
            ])->first();
            if (!$mdlRoleAssignment) {
                $roleAssign = new MdlRoleAssignments;
                $roleAssign->roleid = $role_id;
                $roleAssign->userid = $user_id;
                $roleAssign->contextid = $context_id;
                $roleAssign->save();
            }
        }
    }

    function add_user_by_role($user_id, $role_id)
    {
        //Assign TMS
        /*$modelHasRole = ModelHasRole::where([
            'role_id' => $role_id,
            'model_id' => $user_id
        ])->first();
        if (!$modelHasRole) {*/
        ModelHasRole::firstOrCreate([
            'role_id' => $role_id,
            'model_id' => $user_id,
            'model_type' => 'App/MdlUser',
        ]);
        /*$userRole = new ModelHasRole;
        $userRole->role_id = $role_id;
        $userRole->model_id = $user_id;
        $userRole->model_type = 'App/MdlUser';
        $userRole->save();*/
        //}

        //Assign LMS
        // $role = \App\Role::select('name','mdl_role_id')->findOrFail($role_id);
        // if(
        //     $role['name'] != 'coursecreator' &&
        //     $role['name'] != 'editingteacher' &&
        //     $role['name'] != 'teacher' &&
        //     $role['name'] != 'student'
        // ){

        //     $mdlRoleAssignment = MdlRoleAssignments::where([
        //         'roleid' => $role['mdl_role_id'],
        //         'userid' => $user_id,
        //         'contextid' => 1
        //     ])->first();
        //     if (!$mdlRoleAssignment) {
        //         $roleAssign = new MdlRoleAssignments;
        //         $roleAssign->roleid = $role['mdl_role_id'];
        //         $roleAssign->userid = $user_id;
        //         $roleAssign->contextid = 0;
        //         $roleAssign->save();
        //     }
        // }
    }

    function training_enrole($user_id, $category_id = null)
    {
        $role = Role::select('mdl_role_id')->where('name', Role::STUDENT)->first();
        if (!$category_id) {
            $category_id = TmsTrainningUser::with('category')->where('user_id', $user_id)->first();
            $category_id = $category_id['category']['category_id'];
        }
        $courses = DB::table('mdl_course_categories as cate')
            ->join('mdl_course as course', 'course.category', '=', 'cate.id')
            ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'course.id')
            ->select('course.id as course_id')
            ->where('cate.id', '=', $category_id)
            ->get();

        /*
         * Đóng chức năng theo follow hiện tại :
         * Khi đổi khung năng lực thì enrole học viên vào các khóa học thuộc khung năng lực hiện tại
         * Và giữ lại những khóa học đã enrole của khung năng lực trước
         * Thực hiện unenrole bằng tay khóa học k mong muốn
         * */

        //delete enrole
        /*$context_del = DB::table('mdl_context as mc')
            ->select('mc.id')
            ->join('mdl_course as course', 'course.id', '=', 'mc.instanceid')
            ->join('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'course.id')
            ->where('course.category', '=', $category_id)
            ->where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSE)
            ->pluck('mc.id');

        if($context_del){
            //Xóa user được assign vào khóa học vs vai trò là học viên
            MdlRoleAssignments::where([
                'roleid'    => $role['mdl_role_id'],
                'userid'    => $user_id
            ])->whereIn('contextid', $context_del)
                ->delete();
        }

        $enrole_del = DB::table('mdl_course_categories as cate')
            ->select('me.id')
            ->join('mdl_course as course', 'course.category', '=', 'cate.id')
            ->join('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'course.id')
            ->join('mdl_enrol as me', 'me.courseid', '=', 'course.id')
            //->where('cate.id', '=', $category_id)
            ->where('me.roleid', '=', $role['mdl_role_id'])
            ->where('me.enrol', '=', 'manual')
            ->pluck('me.id');
        if (count($enrole_del) > 0) {
            //Xóa user được enrole vào các khóa học tự động vs vai trò học viên
            DB::table('mdl_user_enrolments')->where('userid' , $user_id)
                ->whereIn('enrolid', $enrole_del)
                ->delete();
        }*/
        //end delete enrole

        if ($courses) {
            foreach ($courses as $course) {
                $enrole = MdlEnrol::firstOrCreate(
                    [
                        'enrol' => 'manual',
                        'courseid' => $course->course_id,
                        'roleid' => $role['mdl_role_id']
                    ],
                    [
                        'sortorder' => 0,
                        'status' => 0,
                        'expirythreshold' => 86400,
                        'timecreated' => strtotime(Carbon::now()),
                        'timemodified' => strtotime(Carbon::now()),
                    ]
                );
                $checkEnrole = DB::table('mdl_user_enrolments')
                    ->where('enrolid', '=', $enrole->id)
                    ->where('userid', '=', $user_id)
                    ->first();
                if (!$checkEnrole) {
                    DB::table('mdl_user_enrolments')->insert([
                        'enrolid' => $enrole->id,
                        'userid' => $user_id
                    ]);
                }

                $context = DB::table('mdl_context')
                    ->where('instanceid', '=', $course->course_id)
                    ->where('contextlevel', '=', MdlUser::CONTEXT_COURSE)
                    ->first();
                $context_id = $context ? $context->id : 0;
                MdlRoleAssignments::firstOrCreate([
                    'roleid' => $role['mdl_role_id'],
                    'userid' => $user_id,
                    'contextid' => $context_id
                ]);

                //lay gia trị trong bang mdl_grade_items
                $mdl_grade_item = MdlGradeItem::where('courseid', $course->course_id)->first();

                if ($mdl_grade_item) {
                    //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
                    MdlGradeGrade::firstOrCreate([
                        'userid' => $user_id,
                        'itemid' => $mdl_grade_item->id
                    ]);
                }

                //write log to notifications
                self::insert_single_notification(TmsNotification::MAIL, $user_id, TmsNotification::ENROL, $course->course_id);
            }
        }
    }

    function insert_single_notification($type, $sendto, $target, $course_id)
    {
        $tms_notif = new TmsNotification();
        $tms_notif->type = $type;
        $tms_notif->sendto = $sendto;
        $tms_notif->target = $target;
        $tms_notif->status_send = TmsNotification::UN_SENT;
        $tms_notif->course_id = $course_id;
        if (!empty(Auth::user())) {
            $tms_notif->createdby = Auth::user()->id;
        }

        $tms_notif->save();

        self::insert_single_notification_log($tms_notif, TmsNotificationLog::CREATE_NOTIF);
    }

    function insert_single_notification_log($tmsNotif, $action)  //action bao gồm create, update, delete lấy trong bảng TmsNotificationLog
    {
        $tms_notifLog = new TmsNotificationLog();
        $tms_notifLog->type = $tmsNotif->type;
        $tms_notifLog->target = $tmsNotif->target;
        $tms_notifLog->content = json_encode($tmsNotif);
        $tms_notifLog->sendto = $tmsNotif->sendto;
        $tms_notifLog->status_send = $tmsNotif->status_send;
        $tms_notifLog->createdby = $tmsNotif->createdby;
        $tms_notifLog->course_id = $tmsNotif->course_id;
        $tms_notifLog->action = $action;
        $tms_notifLog->save();
    }

    function status_message($status, $message)
    {
        $data = [];
        $data['status'] = $status;
        $data['message'] = $message;
        return $data;
    }

    //Kiểm tra Account có phải là nhân viên giám sát thị trường
    function has_user_market($user_id = null)
    {
        if (!$user_id)
            $user_id = Auth::id();
        $check = false;
        $sru = DB::table('model_has_roles as mhr')
            ->join('roles', 'roles.id', '=', 'mhr.role_id')
            ->where('roles.name', '=', Role::MANAGE_MARKET)
            ->where('mhr.model_id', '=', $user_id)
            ->count();
        if ($sru > 0)
            $check = true;
        return $check;
    }

    //gán quyền cho chủ đại lý
    function add_role_for_user($user_id)
    {
        $role = Role::where('name', Role::MANAGE_AGENTS)->first();
        $check = ModelHasRole::where([
            'role_id' => $role['id'],
            'model_id' => $user_id
        ])->count();
        if ($check == 0) {
            $mhr = new ModelHasRole;
            $mhr->role_id = $role['id'];
            $mhr->model_id = $user_id;
            $mhr->model_type = 'App/MdlUser';
            $mhr->save();
        }
    }

    //gán quyền cho chủ điểm bán
    function add_managepos_for_user($user_id)
    {
        $role = Role::where('name', Role::MANAGE_POS)->first();
        $check = ModelHasRole::where([
            'role_id' => $role['id'],
            'model_id' => $user_id
        ])->count();
        if ($check == 0) {
            $mhr = new ModelHasRole;
            $mhr->role_id = $role['id'];
            $mhr->model_id = $user_id;
            $mhr->model_type = 'App/MdlUser';
            $mhr->save();
        }
    }
}

