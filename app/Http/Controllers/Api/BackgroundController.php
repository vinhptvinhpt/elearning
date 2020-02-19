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
use App\TmsCityBranch;
use App\TmsNotification;
use App\TmsNotificationLog;
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

        foreach ($files as $file_path) {
            //$file_path = "23893_CSDL NVBH BRVT (22-11).xlsx";
            $cityId = explode("_", $file_path)[0];
            $file_path = "import" . DIRECTORY_SEPARATOR . $file_path;
            //$file_path =  storage_path('import' . DIRECTORY_SEPARATOR . $file_path);
            //use relative with parent folder is /storage/app/

            try {
                set_time_limit(0);
                $array = (new DataImport)->toArray($file_path, '', '');
                $countError = 0;

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
                    $agency = [];
                    $agencyOuput = [];
                    //tên đại lý
                    $agency['name'] = $row[2];
                    try {
                        //mã đại lý
                        $agency['code'] = $row[1];
                        //địa chỉ
                        $agency['address'] = $row[3];
                        //
                        //kiểm tra xem trưởng đại lý đã tồn tại hay chưa
                        //gán id của user mặc định bằng 0
                        $user_id = 0;
                        $username = $this->vn_to_str($row[5]);
                        $cmtnd = str_replace([' ', 'CMND', 'CCCD', 'MST', ':', "''", "'", '.'], '', $row[6]);
                        $phone = str_replace(["''", "'", ' ', '.'], '', $row[4]);
                        if (empty($cmtnd))
                            $cmtnd = $this->RandomCMTND();
                        $resultCheck = $this->CreateUser('manageagents', $username, $row[7], 0, $cmtnd, $row[5], $phone, $username, '', '', '', '', 0, 0);
                        $valueCheck = $resultCheck[0]['code'];
                        //bằng 0 là thêm thất bại
                        if ($valueCheck == 0) {
                            $agencyOuput['agencyname'] = $agency['name'];
                            $agencyOuput['username'] = '';
                            $agencyOuput['password'] = '';
                            $agencyOuput['status'] = 'error';
                            $agencyOuput['message'] = $resultCheck[0]['message'];
                            array_push($this->importOutput['agencyOuput'], $agencyOuput);
                        } else {
                            //kiểm tra xem đại lý đã tồn tại hay chưa
                            $checkAgency = TmsBranch::where('code', '=', $agency['code'])->first();
                            //nếu đã tồn tại thì update thông tin
                            if (!empty($checkAgency)) {
                                $checkAgency->name = $agency['name'];
                                $checkAgency->address = $agency['address'];
                                $checkAgency->user_id = $valueCheck;
                                $checkAgency->save();
                                $agencyOuput['agencyname'] = $agency['name'];
                                $agencyOuput['status'] = 'success';
                                $agencyOuput['username'] = $username;
                                $agencyOuput['password'] = '';
                                $agencyOuput['message'] = 'Cập nhật đại lý thành công';
                                array_push($this->importOutput['agencyOuput'], $agencyOuput);
                            } else {
                                $agencyId = $this->CreateBranch($agency['name'], $agency['code'], $valueCheck, $cityId, $agency['address']);
                                $agencyOuput['agencyname'] = $agency['name'];
                                $agencyOuput['status'] = 'success';
                                $agencyOuput['username'] = $username;
                                if ($resultCheck[0]['type'] == 'update') {
                                    $agencyOuput['password'] = '123456789';
                                } else {
                                    $agencyOuput['password'] = '';
                                }
                                $agencyOuput['message'] = 'Thêm đại lý thành công';
                                array_push($this->importOutput['agencyOuput'], $agencyOuput);
                            }
                        }
                    } catch (\Exception $e) {
                        // \DB::rollBack();
                        $agencyOuput['agencyname'] = $agency['name'];
                        $agencyOuput['username'] = '';
                        $agencyOuput['password'] = '';
                        $agencyOuput['status'] = 'error';
                        $agencyOuput['message'] = 'Lỗi dữ liệu, kiểm tra lại';
                        array_push($this->importOutput['agencyOuput'], $agencyOuput);
                    }
                }


                //insert điểm bán hàng không có giấy chứng nhận
                array_shift($listPointOfSalesNoCertificate);
                array_shift($listPointOfSalesNoCertificate);
                foreach ($listPointOfSalesNoCertificate as $row) {
                    $pointofsale = [];
                    $posNoOuput = [];
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
                            if (!is_numeric($branchCode)) {
                                $posNoOuput['username'] = $pointofsale['code'];
                                $posNoOuput['status'] = 'error';
                                $posNoOuput['message'] = 'Mã đại lý không đúng định dạng/bỏ trống: [ ' . $branchCode . ' ]';
                                array_push($this->importOutput['posNoOuput'], $posNoOuput);
                            } else {
                                $username = $this->vn_to_str($row[5]);
                                $cmtnd = str_replace([' ', 'CMND', 'CCCD', 'MST', ':', "''", "'", '.'], '', $row[6]);
                                $phone = str_replace(["''", "'", ' ', '.'], '', $row[4]);
                                $email = $row[7];
                                if (empty($email)) {
                                    $email = $username . '@gmail.com';
                                }
                                if (empty($cmtnd))
                                    $cmtnd = $this->RandomCMTND();
                                $resultCheck = $this->CreateUser('managepos', $username, $email, 0, $cmtnd, $row[5], $phone, $username, '', '', '', '', 0, 0);
                                $valueCheck = $resultCheck[0]['code'];
                                if ($valueCheck == 0) {
                                    $posNoOuput['username'] = $pointofsale['code'];
                                    $posNoOuput['status'] = 'error';
                                    $posNoOuput['message'] = 'Thông tin trưởng điểm bán của điểm bán có mã là: ' . $pointofsale['code'] . ' không hợp lệ. ' . $resultCheck[0]['message'];
                                    array_push($this->importOutput['posNoOuput'], $posNoOuput);
                                } else {
                                    //kiểm tra xem đại lý đã tồn tại hay chưa   //Nếu chưa thì tạo
                                    $checkBranch = $this->CreateBranch($branchCode, $branchCode, $valueCheck, $cityId, '');
                                    $branchId = $checkBranch['code'];
                                    if ($branchId !== 0) {
                                        //kiểm tra xem điểm bán hàng đã tồn tại hay chưa
                                        $checkSaleroom = $this->CreateSaleRoom($pointofsale['name'], $pointofsale['code'], $branchId, $valueCheck, $pointofsale['address']);
                                        if ($checkSaleroom['code'] > 0) {
                                            $posNoOuput['username'] = $pointofsale['code'];
                                            $posNoOuput['status'] = 'success';
                                            $posNoOuput['message'] = ' mã điểm bán ' . $pointofsale['code'] . ' ' . $checkSaleroom['message'];
                                            array_push($this->importOutput['posNoOuput'], $posNoOuput);
                                        } else {
                                            $posNoOuput['username'] = $pointofsale['code'];
                                            $posNoOuput['status'] = 'error';
                                            $posNoOuput['message'] = ' mã điểm bán ' . $pointofsale['code'] . ' Thêm thất bại';
                                            array_push($this->importOutput['posNoOuput'], $posNoOuput);
                                        }
                                        // \DB::commit();
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // \DB::rollBack();
                        $posNoOuput['username'] = $pointofsale['code'];
                        $posNoOuput['status'] = 'error';
                        $posNoOuput['message'] = 'Lỗi dữ liệu, kiểm tra lại';
                        array_push($this->importOutput['posNoOuput'], $posNoOuput);
                    }
                }


                //insert điểm bán hàng có giấy chứng nhận
                array_shift($listPointOfSalesHaveCertificate);
                array_shift($listPointOfSalesHaveCertificate);
                foreach ($listPointOfSalesHaveCertificate as $row) {
                    $pointofsale = [];
                    $posOuput = [];
                    $posOuput['message'] = 'Điểm bán: ';
                    $posOuput['username'] = $row[1];
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
                            if (!is_numeric($branchCode)) {
                                $posNoOuput['username'] = $pointofsale['code'];
                                $posNoOuput['status'] = 'error';
                                $posNoOuput['message'] = 'Mã đại lý không đúng định dạng / bỏ trống: [ ' . $branchCode . ' ]';
                                array_push($this->importOutput['posHaveOuput'], $posNoOuput);
                            } else {
                                //Gán sẵn là không thỏa mãn
                                $checkUserTM = 0;
                                if (!empty($userId)) {
                                    $checkUser = MdlUser::where('id', '=', $userId)->first();
                                    if (empty($checkUser)) {
                                        $checkCertificate = StudentCertificate::where('code', '=', $userId)->first();
                                        if (!empty($checkCertificate)) {
                                            $userId = $checkCertificate->userid;
                                            $checkUserTM = 1;
                                        }
                                    } else {
                                        $checkUserTM = 1;
                                    }
                                }
                                if ($checkUserTM == 0) {
                                    $userId = null;
                                }
                                $checkUserTM = 1;
                                if ($checkUserTM !== 0) {
                                    // \DB::beginTransaction();
                                    //kiểm tra xem đại lý đã tồn tại hay chưa
                                    $checkBranch = $this->CreateBranch($branchCode, $branchCode, $userId, $cityId, '');
                                    $branchId = $checkBranch['code'];
                                    //kiểm tra xem điểm bán hàng đã tồn tại hay chưa
                                    $CheckpointOfSale = $this->CreateSaleRoom($pointofsale['name'], $pointofsale['code'], $branchId, $userId, $pointofsale['address']);

                                    $posOuput['message'] .= ' mã điểm bán ' . $pointofsale['code'] . ' ' . $CheckpointOfSale['message'];
                                    $posOuput['code'] = 1;
                                    $posOuput['status'] = 'success';
                                    array_push($this->importOutput['posHaveOuput'], $posOuput);
                                    // \DB::commit();
                                } else {
                                    $posOuput['message'] .= ' mã nhân viên ' . $posOuput['username'] . ' không tồn tại';
                                    $posOuput['code'] = 0;
                                    $posOuput['status'] = 'error';
                                    array_push($this->importOutput['posHaveOuput'], $posOuput);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // \DB::rollBack();
                        //dd($e);
                        $posOuput['message'] .= ' có mã điểm bán ' . $posOuput['username'] . ' đã xảy ra lỗi khi thêm';
                        $posOuput['code'] = 0;
                        $posOuput['status'] = 'error';
                        array_push($this->importOutput['posHaveOuput'], $posOuput);
                    }
                }

                $this->importOutput['success'] = 0;
                $this->importOutput['error'] = 0;

                //insert nhân viên
                //loại bỏ 2 hàng đầu tiên
                array_shift($listEmployees);
                array_shift($listEmployees);

                //$listEmployees = array_slice($listEmployees, 0, 10);

                $stt = 0;
                foreach ($listEmployees as $row) {
                    $stt++;
                    $user = [];
                    $userOuput = [];
                    try {
                        $user['username'] = $row[1];
                        //tên đầy đủ
                        $user['fullname'] = $row[6];
                        //username
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $user['fullname']);
                        $user['username'] = empty($row[4]) ? $this->vn_to_str($user['fullname']) : $row[4];
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
                        //ma nhan vien
                        $user['code'] = $row[1];

                        //mã đại lý cấp giấy chứng nhận
                        $agencyCode = $row[3];
                        //mã đơn vị quản lý
                        $managementCode = $row[9];
                        //Sinh nhat
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
                        //Ngày bắt đầu làm
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

                        //Tình trạng làm việc
                        $user['working_status'] = ($row[15] == 1) ? 1 : 0;

                        //Da cap giay chung nhan
                        $user['confirm'] = 0;
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
                                $managementCode
                            );

                            $user_id = !empty($resultCheck) ? $resultCheck[0]['code'] : 0;

                            if ($user_id != 0) {
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


//                            $textMsg = '';
                                $this->importOutput['success']++;
                                $userOuput['username'] = $user['username'];
                                $userOuput['fullname'] = $user['fullname'];
                                if ($resultCheck[0]['type'] == 'update') {
                                    $userOuput['password'] = '';
                                } else {
                                    $userOuput['password'] = '123456789';
                                }
                                $userOuput['status'] = 'success';
                                $userOuput['cmtnd'] = $user['cmtnd'];

                                $userOuput['message'] = $resultCheck[0]['message'];
//                            if (!empty($textMsg)) {
//                                $userOuput['message'] = $textMsg;
//                            }
                                $userOuput['code'] = $user_id;
                                array_push($this->importOutput['userOuput'], $userOuput);
                            }
                            else {
                                $countError++;
                                $userOuput['username'] = '';
                                $userOuput['fullname'] = $user['fullname'];
                                $userOuput['status'] = 'error';
                                $userOuput['cmtnd'] = $user['cmtnd'];
                                $userOuput['password'] = '';
                                $userOuput['message'] = $resultCheck[0]['message'];
                                array_push($this->importOutput['userOuput'], $userOuput);
                            }
                        }
                        else {
                            $countError++;
                            $userOuput['username'] = '';
                            $userOuput['fullname'] = $user['fullname'];
                            $userOuput['status'] = 'error';
                            $userOuput['cmtnd'] = $user['cmtnd'];
                            $userOuput['password'] = '';
                            $userOuput['message'] = 'Tên không đúng định dạng UTF-8';
                            array_push($this->importOutput['userOuput'], $userOuput);
                        }
                    }
                    catch (\Exception $e) {
                        $countError++;
                        $userOuput['username'] = '';
                        $userOuput['fullname'] = $user['fullname'];
                        $userOuput['password'] = '';
                        $userOuput['status'] = 'error';
                        $userOuput['stt'] = $row[0];
                        $userOuput['cmtnd'] = str_replace([' ', 'CMND', 'CCCD', 'MST', ':', "''", "'", '.'], '', $row[8]);
                        $userOuput['message'] = $e->getMessage();
//                $userOuput['message'] = 'Lỗi dữ liệu, kiểm tra lại';
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

                $filename = "error_" . $cityId . ".xlsx";
                //xóa file cũ
                //$myFile = '/app/'.$filename;
                if (Storage::exists($filename)) {
                    // do something
                    Storage::delete($filename);
                }
                //ghi file vào thư mục storage
                $exportExcel = new ListMismatchData($dataExport);
                $exportExcel->store($filename, '', Excel::XLSX);
                \DB::commit();

                return response()->json(self::status_message('success', __('cap_nhat_thanh_cong')));
            } catch (Exception $e) {
                return response()->json(self::status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        }
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

    public function CreateBranch($name, $code, $user_id, $city, $address)
    {
        try {
            $check_name = DB::table('tms_branch as tb')
                ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
                ->where('tb.name', '=', $name)
                ->where('tcb.city_id', '=', $city)
                ->count();
            if ($check_name > 0) {
                $Branch = DB::table('tms_branch as tb')
                    ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
                    ->where('tb.name', '=', $name)
                    ->where('tcb.city_id', '=', $city)->first();
                return [
                    'code' => $Branch->id,
                    'message' => 'Tên Đại lý đã tồn tại.'
                ];
            }

            $check_branch = TmsBranch::where('code', $code)->count();
            if ($check_branch > 0) {
                $Branch = TmsBranch::where('code', $code)->first();
                return [
                    'code' => $Branch->id,
                    'message' => 'Mã Đại lý đã tồn tại.'
                ];
            }

//            \DB::beginTransaction();
            $tmsBranch = new TmsBranch;
            $tmsBranch->name = $name;
            $tmsBranch->code = $code;
            $tmsBranch->user_id = $user_id;
            //$tmsBranch->description = $description;
            $tmsBranch->address = $address;
            $tmsBranch->save();
            if ($city) {
                $cityBranch = new TmsCityBranch;
                $cityBranch->city_id = $city;
                $cityBranch->branch_id = $tmsBranch->id;
                $cityBranch->save();
            }

            if ($user_id && $user_id != 0 && !has_user_market($user_id)) {
                self::add_role_for_user($user_id);
            }

            if (has_user_market()) {
                $role_organize = new TmsRoleOrganize;
                $role_organize->user_id = Auth::id();
                $role_organize->organize_id = $tmsBranch->id;
                $role_organize->type = 'branch';
                $role_organize->save();
            }

            //devcpt_log_system('organize', '/system/organize/branch/edit/' . $tmsBranch->id, 'create', 'Thêm mới Đại lý: ' . $name);
//            \DB::commit();
            return [
                'code' => $tmsBranch->id,
                'message' => 'Thêm đại lý thành công'
            ];
        } catch (Exception $e) {
//            \DB::rollBack();
            return [
                'code' => 0,
                'message' => 'Lỗi hệ thống'
            ];
        }
    }

    public function CreateUser(
        $role_name, $username, $email, $confirm, $cmtnd,
        $fullname, $phone, $code, $address, $sex, $timestamp, $timestamp_start, $working_status, $managementCode
    )
    {
        $importOutput['userOuput'] = [];
        $userOuput = [];
        $checkTM = 1;
        $newUserId = 0;
        $userOuput['message'] = '';
        $userOuput['type'] = '';
        try {

            if (empty($email) || empty($cmtnd) || empty($username) || empty($fullname)) {
                $userOuput['username'] = $username;
                $userOuput['message'] .= ' Chi tiết : ';
                $misings = array();

                if (!$username) {
                    $misings[] = 'Tài khoản';
                }
                if (!$email) {
                    $misings[] = 'Email';
                }
                if (!$cmtnd) {
                    $misings[] = 'Số CMTND';
                }
                if (!$fullname) {
                    $misings[] = 'Họ và tên';
                }
                $userOuput['message'] .= implode(", ", $misings) . ' không được để trống.';
                $userOuput['status'] = 'error';
                $userOuput['type'] = 'error';
                $userOuput['code'] = 0;
                $checkTM = 0;
            }
            //kiểm tra nếu tồn tại các trường và các trường đó không phải là số => sai định dạng
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
            if (!empty($check)) {
                $msg = [];
                foreach ($check as $item => $value) {
                    switch ($item) {
                        case 'cmtnd':
                            $item = "Chứng minh thư nhân dân";
                            break;
                        case 'phone':
                            $item = "Số điện thoại";
                            break;
                        case 'confirm':
                            $item = "Confirm";
                            break;
                        default:
                            break;
                    }
                    $msg[] = $item . ': ' . $value;
                }
                $userOuput['message'] .= 'Thông tin: ' . implode(", ", $msg);
                $userOuput['status'] = 'error';
                $userOuput['code'] = 0;
                $userOuput['type'] = 'error';
                $checkTM = 0;
            }
            $usernameNew = $username;

            //Nếu thỏa mãn
            if ($checkTM == 1) {
                $nameExpl = explode(' ', $fullname);
                $rowname = count($nameExpl);
                $firstname = $nameExpl[$rowname - 1] ? $nameExpl[$rowname - 1] : '';
                $lastname = str_replace($nameExpl[$rowname - 1], '', $fullname);
                $lastname = $lastname ? $lastname : '';

                $checkUsername = MdlUser::where('username', 'like', "{$username}%")->get();
                $checkedUsers = [];
                //  \DB::beginTransaction();
//                \DB::disableQueryLog();
                if (count($checkUsername) > 0) {
                    foreach ($checkUsername as $user) {
                        $getTMSUser = TmsUserDetail::where('user_id', $user->id)->first();
                        if (!empty($getTMSUser)) {
                            if ($getTMSUser->cmtnd == $cmtnd || strpos($getTMSUser->cmtnd, '00000') !== false) {
                                $newUserId = $user->id;
                                $checkedUsers = [];
                                break;
                            }
                        } else {
                            $checkedUsers[] = $user->username;
                        }
                    }
                    //nếu iduserfix khác 0 -> cập nhật
                    if ($newUserId > 0) {
                        $userGet = MdlUser::where('id', $newUserId)->first();
                        //cập nhật thông tin user
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
                        $userOuput['username'] = $username;
                        $userOuput['code'] = $newUserId;
                        $userOuput['cmtnd'] = $cmtnd;
                        $userOuput['message'] = 'Cập nhật thành công';
                        $userOuput['type'] = 'update';
                        $checkedUsers = [];
                    }
                    //Nếu trùng username thì sẽ tìm ra số cuối để tăng
                    if (count($checkedUsers) > 0) {
                        $number = 0;
                        $max = 0;
                        //vòng lặp qua các username trùng
                        foreach ($checkedUsers as $checkedUser) {
                            $thisUserNumber = substr($checkedUser, strlen($username), strlen($checkedUser));
                            if (is_numeric($thisUserNumber) && $thisUserNumber > $max)
                                $max = $thisUserNumber;
                        }
                        //tạo mới
                        $usernameNew = $username . ($max + 1);

                        //thêm mới user
                        $newUserId = $this->createUserOrg(
                            $usernameNew,
                            $firstname,
                            $lastname,
                            $email,
                            $role_name,
                            $confirm,
                            $cmtnd,
                            $fullname,
                            $phone,
                            $code,
                            $address,
                            $sex,
                            $timestamp, $timestamp_start, $working_status
                        );
                        $userOuput['message'] = 'Thêm mới thành công.';
                        $userOuput['type'] = 'add';
                    }
                } else {
//                    thêm mới user
                    $newUserId = $this->createUserOrg(
                        $usernameNew,
                        $firstname,
                        $lastname,
                        $email,
                        $role_name,
                        $confirm,
                        $cmtnd,
                        $fullname,
                        $phone,
                        $code,
                        $address,
                        $sex,
                        $timestamp, $timestamp_start, $working_status
                    );
                    $userOuput['message'] = 'Thêm mới thành công.';
                    $userOuput['type'] = 'add';
                }
                if ($newUserId) {
                    //add user vao khung nang luc chung chi trong he thong (day la khung nang luc bat buoc)
//                    $trainning = new TmsTrainningUser();
//                    $trainning->user_id = $newUserId;
//                    $trainning->trainning_id = 1; //id khung nang luc bat buoc
//                    $trainning->save();

                    $trainning = TmsTrainningUser::firstOrCreate([
                        'user_id' => $newUserId,
                        'trainning_id' => 1 //id khung nang luc bat buoc
                    ]);

                    sleep(0.01);

                    $category = TmsTrainningCategory::select('category_id')->where('trainning_id', $trainning->id)->first();

                    self::training_enrole($newUserId, $category['category_id']);
                }


                //  \DB::commit();
                $textMsg = '';
                //Nếu tồn tại mã quản lý : $managementCode -> kiểm tra nếu k có thì thêm, nếu có thì cập nhật
                if ($newUserId > 0 && $managementCode > 0) {
                    //kiểm tra mã đơn vị quản lý
                    //kiểm tra trong điểm bán
                    $checkSaleRoomUser = TmsSaleRooms::where('code', '=', $managementCode)->first();
                    if (!empty($checkSaleRoomUser)) {
                        $getSRU = $this->CreateSaleRoomUser($checkSaleRoomUser->id, $newUserId, TmsSaleRoomUser::POS);
                        if ($getSRU['code'] == 0)
                            $textMsg = ' Thông tin điểm bán bị lỗi hoặc không đúng.';
                        else
                            $textMsg = ' Thông tin điểm bán: ' . $getSRU['message'];
                    } else {
                        $checkBranchUser = TmsBranch::where('code', '=', $managementCode)->first();
                        if (!empty($checkBranchUser)) {
                            $getSRU = $this->CreateSaleRoomUser($checkBranchUser->id, $newUserId, TmsSaleRoomUser::AGENTS);
                            if ($getSRU['code'] == 0)
                                $textMsg = ' Thông tin đại lý bị lỗi hoặc không đúng.';
                            else
                                $textMsg = ' Thông tin đại lý: ' . $getSRU['message'];
                        } else {
                            $createBranchUnknow = $this->CreateBranch('unknowbranch', 'unknowbranch', null, 12298, null);
                            $createSaleRoomUnknow = $this->CreateSaleRoom('unknowsaleroom', 'unknowsaleroom', $createBranchUnknow['code'], null, null);
                            $createSaleRoomUser = $this->CreateSaleRoomUser($createSaleRoomUnknow['code'], $newUserId, TmsSaleRoomUser::POS);
                            $textMsg = ' Nhân viên thuộc điểm bán unknowsaleroom.';
                        }
                    }
                }
                $userOuput['message'] .= $textMsg;
                $userOuput['code'] = $newUserId;
                $userOuput['cmtnd'] = $cmtnd;
                $userOuput['status'] = 'success';
                array_push($importOutput['userOuput'], $userOuput);
            } else {
                $userOuput['cmtnd'] = $cmtnd;
            }
        } catch (\Exception $e) {
            // \DB::rollBack();
            $userOuput['username'] = $username;
            $userOuput['status'] = 'error';
            $checkTest = 0;
            for ($i = 0; $i < strlen($username); $i++) {
                if (ord($username[$i]) > 127) {
                    $checkTest = 1;
                    break;
                }
            }
            if ($checkTest != 0)
                $userOuput['message'] = 'Lỗi format tên';
            else
                $userOuput['message'] = $e->getMessage();

            $userOuput['code'] = 0;
            $userOuput['cmtnd'] = $cmtnd;
            $userOuput['type'] = 'error';
        }
        array_push($importOutput['userOuput'], $userOuput);
        return $importOutput['userOuput'];
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

            $check_saleroom = TmsSaleRooms::where('code', $code)->count();

            if ($check_saleroom > 0) {
                $sale_room = TmsSaleRooms::where('code', $code)->first();
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
            $saleRoom->user_id = $user_id;
            $saleRoom->address = $address;
            $saleRoom->save();
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
//            \DB::rollBack();
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
                        insert_single_notification(\App\TmsNotification::MAIL, $user_id, \App\TmsNotification::ENROL, $course->course_id);
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

