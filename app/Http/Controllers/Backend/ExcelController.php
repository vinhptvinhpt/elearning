<?php

namespace App\Http\Controllers\Backend;

use App\Exports\AttendanceSheet;
use App\Exports\ImportResultSheet;
use App\Exports\InvitationSheet;
use App\Exports\ListMismatchSaleroom;
use App\Exports\LoginSheet;
use App\Exports\ReportDetailRawSheet;
use App\Exports\ReportDetailSheet;
use App\Exports\ReportMarkSheet;
use App\Exports\ReportSheet;
use App\Exports\ResultSheet;
use App\Http\Controllers\Controller;
use App\Imports\DataImport;
use App\MdlUser;
use App\TmsTdCompetency;
use App\TmsTdCompetencyMark;
use App\TmsTdUserMark;
use App\TmsUserDetail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\Request;

class ExcelController extends Controller
{
    public function exportMismatchSaleroom()
    {

        $sheet = array(
            array(
                '1',
                'DB001',
                'Điểm bán 1',
                'Địa chỉ 1, XYZ',
                '0123456789',
                'NV00001',
                'DL001',
                'Chủ điểm bán, đại lý không chính xác'
            ),
            array(
                '2',
                'DB002',
                'Điểm bán 2',
                'Địa chỉ 2, ZYX',
                '0123456789',
                'NV00001',
                'DL001',
                'Chủ điểm bán, đại lý k đẹp'
            )
        );

        $sheet2 = array(
            array(
                '1',
                'DB001',
                'Điểm bán 1',
                'Địa chỉ 1, XYZ',
                '0123456789',
                'NV00001',
                'DL001',
                'Chủ điểm bán, đại lý không chính xác'
            ),
            array(
                '2',
                'DB002',
                'Điểm bán 2',
                'Địa chỉ 2, ZYX',
                '0123456789',
                'NV00001',
                'DL001',
                'Chủ điểm bán, đại lý k đẹp'
            ),
            array(
                '3',
                'DB003',
                'Điểm bán 3',
                'Địa chỉ 3, ZYX',
                '0123456789',
                'NV00001',
                'DL001',
                'Chủ điểm bán, đại lý k đẹp'
            )
        );


        $dataExport = array(
            'Sheet 01' => $sheet,
            'Sheet 0cccccc' => $sheet2
        );


        $filename = "dat09.xlsx";

        $exportExcel = new ListMismatchSaleroom($dataExport);

        //$exportExcel->store($filename, '', \Maatwebsite\Excel\Excel::XLSX); //store at D:\xampp\htdocs\elearning-bgt\storage\app\dat09.xlsx
        //Excel::store($exportExcel, 'invoices.xlsx', '', \Maatwebsite\Excel\Excel::XLSX);

        echo "Success";

        //return ($exportExcel)->download($filename, \Maatwebsite\Excel\Excel::XLSX);
        //return Excel::download($exportExcel, $filename);
    }

    public function exportReport(Request $request)
    {

        $data = $request->input('data');

        $selected_level = $data['selected_level'];

        $export_data = array();

        if ($selected_level == 'district') {
            $first_row_label = __('chi_nhanh');
            $data = $data["region"];
            foreach ($data as $department) {

                $parent_total = $department['user_count'];
                $parent_incomplete = $department['user_incomplete_count'];
                $parent_completed = $department['user_completed_count'];
                $parent_confirmed = $department['user_confirm_count'];

                $parent_incomplete_percent = $parent_total != 0 ? intval($parent_incomplete / $parent_total * 100) : 0;
                $parent_completed_percent = $parent_total != 0 ? intval($parent_completed / $parent_total * 100) : 0;
                $parent_confirmed_percent = $parent_total != 0 ? intval($parent_confirmed / $parent_total * 100) : 0;

                $export_data[] = array(
                    $first_row_label,
                    $department['name'],
                    $parent_incomplete . "(" . $parent_incomplete_percent . "%)",
                    $parent_completed . "(" . $parent_completed_percent . "%)",
                    $parent_confirmed . "(" . $parent_confirmed_percent . "%)",
                    $parent_total,
                );
                $stt = 1;
                foreach ($department['cities'] as $item) {
                    $total = count($item['user']);
                    $incomplete = count($item['user_incomplete']);
                    $completed = count($item['user_completed']);
                    $confirmed = count($item['user_confirm']);

                    $incomplete_percent = $total != 0 ? intval($incomplete / $total * 100) : 0;
                    $completed_percent = $total != 0 ? intval($completed / $total * 100) : 0;
                    $confirmed_percent = $total != 0 ? intval($confirmed / $total * 100) : 0;

                    $export_data[] = array(
                        $stt,
                        $item['name'],
                        $incomplete . "(" . $incomplete_percent . "%)",
                        $completed . "(" . $completed_percent . "%)",
                        $confirmed . "(" . $confirmed_percent . "%)",
                        $total,
                    );
                    $stt++;
                }
            }
        }

        if ($selected_level == "city") {
            $first_row_label = __('tinh_thanh');

            $parent_total = $data['user_count'];
            $parent_incomplete = $data['user_incomplete_count'];
            $parent_completed = $data['user_completed_count'];
            $parent_confirmed = $data['user_confirm_count'];

            $parent_incomplete_percent = $parent_total != 0 ? intval($parent_incomplete / $parent_total * 100) : 0;
            $parent_completed_percent = $parent_total != 0 ? intval($parent_completed / $parent_total * 100) : 0;
            $parent_confirmed_percent = $parent_total != 0 ? intval($parent_confirmed / $parent_total * 100) : 0;

            $export_data[] = array(
                $first_row_label,
                $data['name'],
                $parent_incomplete . "(" . $parent_incomplete_percent . "%)",
                $parent_completed . "(" . $parent_completed_percent . "%)",
                $parent_confirmed . "(" . $parent_confirmed_percent . "%)",
                $parent_total,
            );
            $stt = 1;
            foreach ($data['items'] as $item) {

                $total = count($item['user']);
                $incomplete = count($item['user_incomplete']);
                $completed = count($item['user_completed']);
                $confirmed = count($item['user_confirm']);

                $incomplete_percent = $total != 0 ? intval($incomplete / $total * 100) : 0;
                $completed_percent = $total != 0 ? intval($completed / $total * 100) : 0;
                $confirmed_percent = $total != 0 ? intval($confirmed / $total * 100) : 0;

                $export_data[] = array(
                    $stt,
                    $item['name'],
                    $incomplete . "(" . $incomplete_percent . "%)",
                    $completed . "(" . $completed_percent . "%)",
                    $confirmed . "(" . $confirmed_percent . "%)",
                    $total,
                );
                $stt++;
            }
        }

        if ($selected_level == "branch") {
            $export_data[] = array(
                __('dai_ly'),
                $data['name'],
                $data['user_incomplete_count'],
                $data['user_completed_count'],
                $data['user_confirm_count'],
                $data['user_count'],
            );

            $incomplete_array = [];
            $completed_array = [];
            $confirm_array = [];
            $user_array = [];


            foreach ($data['branch_users'] as $item) {
                foreach ($item as $key => $users) {
                    $name = array_column($users, 'user_name');
                    if ($key == 'user') {
                        $user_array = array_merge($user_array, $name);
                    } elseif ($key == 'user_completed') {
                        $completed_array = array_merge($completed_array, $name);
                    } elseif ($key == 'user_confirm') {
                        $confirm_array = array_merge($confirm_array, $name);
                    } elseif ($key == 'user_incomplete') {
                        $incomplete_array = array_merge($incomplete_array, $name);
                    }
                }
            }

            $max_count = max(count($user_array), count($incomplete_array), count($completed_array), count($confirm_array));

            for ($i = 0; $i < $max_count; $i++) {
                $branch_data = array(
                    '',
                    '',
                    isset($incomplete_array[$i]) ? $incomplete_array[$i] : '',
                    isset($completed_array[$i]) ? $completed_array[$i] : '',
                    isset($confirm_array[$i]) ? $confirm_array[$i] : '',
                    isset($user_array[$i]) ? $user_array[$i] : '',
                );
                $export_data[] = $branch_data;
            }

        }

        if ($selected_level == "branch" || $selected_level == "saleroom") {
            $items = $data['items'];
            $stt = 1;
            foreach ($items as $saleroom) {

                $saleroom_data = array(
                    $stt,
                    $saleroom['name'],
                    count($saleroom['user_incomplete']) == 0 ? "0" : count($saleroom['user_incomplete']),
                    count($saleroom['user_completed']) == 0 ? "0" : count($saleroom['user_completed']),
                    count($saleroom['user_confirm']) == 0 ? "0" : count($saleroom['user_confirm']),
                    count($saleroom['user']) == 0 ? "0" : count($saleroom['user']),
                );
                $export_data[] = $saleroom_data;

                $item_incomplete_array = array_column($saleroom['user_incomplete'], 'user_name');
                $item_completed_array = array_column($saleroom['user_completed'], 'user_name');
                $item_confirm_array = array_column($saleroom['user_confirm'], 'user_name');
                $item_array = array_column($saleroom['user'], 'user_name');

                $max_saleroom_count = max(
                    count($item_incomplete_array),
                    count($item_completed_array),
                    count($item_confirm_array),
                    count($item_array)
                );
                for ($i = 0; $i < $max_saleroom_count; $i++) {
                    $saleroom_item = array(
                        '',
                        '',
                        isset($item_incomplete_array[$i]) ? $item_incomplete_array[$i] : '',
                        isset($item_completed_array[$i]) ? $item_completed_array[$i] : '',
                        isset($item_confirm_array[$i]) ? $item_confirm_array[$i] : '',
                        isset($item_array[$i]) ? $item_array[$i] : '',
                    );
                    $export_data[] = $saleroom_item;
                }
                $stt++;
            }
        }

        $exportExcel = new ReportSheet('Report Detail', $selected_level, $export_data);

        $filename = "report_detail.xlsx";

        $exportExcel->store($filename, '', \Maatwebsite\Excel\Excel::XLSX);

        return response()->json(storage_path($filename));
    }

    public function exportReportDetailRaw(Request $request)
    {

        $data = $request->input('data');
        $mode = $request->input('mode');
        $type = $request->input('type');

        $export_data = array();

        //Export export_data
        $stt = 0;

        if ($mode == 'certificated' || $mode == 'completed_training') {

            foreach ($data as $organization_id => $organization) { //organization
                $organization_name = $organization['col0'];
                $training_list = isset($organization['training']) ? $organization['training'] : [];
                if (!empty($training_list)) { //Certificated
                    foreach ($training_list as $training_id => $training) {

                        $training_name = $training['col0'];
                        $certificated_array = $training['col1'];
                        if (is_array($certificated_array) && !empty($certificated_array)) {
                            foreach ($certificated_array as $certificated) {
                                $stt++;
                                $export_data[] = array(
                                    $stt,
                                    $certificated['fullname'],
                                    $certificated['email'],
                                    $organization_name,
                                    self::getCountryName($certificated['country']),
                                    $certificated['city'],
                                    $training_name,
                                    'Yes'
                                );
                            }
                        }

                        $missing_array = $training['col2'];
                        if (is_array($missing_array) && !empty($missing_array)) {
                            foreach ($missing_array as $missing) {
                                $stt++;
                                $export_data[] = array(
                                    $stt,
                                    $missing['fullname'],
                                    $missing['email'],
                                    $organization_name,
                                    self::getCountryName($missing['country']),
                                    $missing['city'],
                                    $training_name,
                                    'No'
                                );
                            }
                        }

                    }
                }
            }
        }

        if ($mode == 'completed_course' || $mode == 'learning_time') {
            if ($type == 'single_course') {
                foreach ($data as $course_id => $course) {
                    $course_name = $course['col0'];
                    $training_name = null;
                    //Col1 for both
                    $certificated_array = $course['col1'];
                    if (is_array($certificated_array) && !empty($certificated_array)) {
                        foreach ($certificated_array as $certificated) {
                            $stt++;
                            if ($mode == 'completed_course') {
                                $export_data[] = array(
                                    $stt,
                                    $certificated['fullname'],
                                    $certificated['email'],
                                    $certificated['organization_name'],
                                    self::getCountryName($certificated['country']),
                                    $certificated['city'],
                                    $training_name,
                                    $course_name,
                                    'Yes'
                                );
                            } else {
                                $export_data[] = array(
                                    $stt,
                                    $certificated['fullname'],
                                    $certificated['email'],
                                    $certificated['organization_name'],
                                    self::getCountryName($certificated['country']),
                                    $certificated['city'],
                                    $training_name,
                                    $course_name,
                                    $certificated['duration'],
                                    $certificated['estimate_duration'],
                                );
                            }
                        }
                    }

                    //Col2 for complete course only
                    if ($mode == 'completed_course') {
                        $missing_array = $course['col2'];
                        if (is_array($missing_array) && !empty($missing_array)) {
                            foreach ($missing_array as $missing) {
                                $stt++;
                                $export_data[] = array(
                                    $stt,
                                    $missing['fullname'],
                                    $missing['email'],
                                    $missing['organization_name'],
                                    self::getCountryName($missing['country']),
                                    $missing['city'],
                                    $training_name,
                                    $course_name,
                                    'No'
                                );
                            }
                        }
                    }
                }
            } else {
                foreach ($data as $organization_id => $organization) { //organization
                    $organization_name = $organization['col0'];
                    $training_list = isset($organization['training']) ? $organization['training'] : [];
                    if (!empty($training_list)) { //Certificated
                        foreach ($training_list as $training_id => $training) {
                            $training_name = $training['col0'];
                            $course_list = isset($training['courses']) ? $training['courses'] : [];
                            if (!empty($course_list)) {
                                foreach ($course_list as $course) {
                                    $course_name = $course['col0'];

                                    //Col1 for both
                                    $certificated_array = $course['col1'];
                                    if (is_array($certificated_array) && !empty($certificated_array)) {
                                        foreach ($certificated_array as $certificated) {
                                            $stt++;
                                            if ($mode == 'completed_course') {
                                                $export_data[] = array(
                                                    $stt,
                                                    $certificated['fullname'],
                                                    $certificated['email'],
                                                    $organization_name,
                                                    self::getCountryName($certificated['country']),
                                                    $certificated['city'],
                                                    $training_name,
                                                    $course_name,
                                                    'Yes'
                                                );
                                            } else {
                                                $export_data[] = array(
                                                    $stt,
                                                    $certificated['fullname'],
                                                    $certificated['email'],
                                                    $organization_name,
                                                    self::getCountryName($certificated['country']),
                                                    $certificated['city'],
                                                    $training_name,
                                                    $course_name,
                                                    $certificated['duration'],
                                                    $certificated['estimate_duration'],
                                                );
                                            }
                                        }
                                    }

                                    //Col2 for complete course only
                                    if ($mode == 'completed_course') {
                                        $missing_array = $course['col2'];
                                        if (is_array($missing_array) && !empty($missing_array)) {
                                            foreach ($missing_array as $missing) {
                                                $stt++;
                                                $export_data[] = array(
                                                    $stt,
                                                    $missing['fullname'],
                                                    $missing['email'],
                                                    $organization_name,
                                                    self::getCountryName($missing['country']),
                                                    $missing['city'],
                                                    $training_name,
                                                    $course_name,
                                                    'No'
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $exportExcel = new ReportDetailRawSheet('Report Detail', $export_data, $mode, $type);

        $filename = "report_detail.xlsx";

        $exportExcel->store($filename, '', \Maatwebsite\Excel\Excel::XLSX);

        return response()->json(storage_path($filename));
    }

    public function getCountryName($code)
    {
        $countries = TmsUserDetail::country;
        return array_key_exists($code, $countries) ? $countries[$code] : 'N/A';
    }

    public function exportReportDetail(Request $request)
    {
        $data = $request->input('data');
        $type = $request->input('type');

        $export_data = array();

        //Export report_data
        foreach ($data as $item) {
            if ($item['type'] == 'users') {
                //In PHP, '\n' (in single quotes) is a literal \ character followed by a literal n character. "\n" (in double quotes) is a newline character
                $column2 = str_replace('<br/>', "\n", $item['column2']);
                $column3 = str_replace('<br/>', "\n", $item['column3']);
                $column4 = str_replace('<br/>', "\n", $item['column4']);
            } else {
                $column2 = $item['column2'];
                $column3 = $item['column3'];
                $column4 = $item['column4'];
            }
            $export_data[] = array(
                $item['column1'],
                $column2,
                $column3,
                $column4,
                $item['type']
            );
        }

        $exportExcel = new ReportDetailSheet('Report Detail', $export_data, $type);

        $filename = "report_detail.xlsx";

        $exportExcel->store($filename, '', \Maatwebsite\Excel\Excel::XLSX);

        return response()->json(storage_path($filename));
    }

    public function apiExportResult(Request $request)
    {

        $username = $request->input('username');
        $fullname = $request->input('fullname');

        $data = $request->input('data');

        $export_data = array();

        $export_data[] = array(
            __('stt'),
            __('ma_khoa_hoc'),
            __('ten_khoa_hoc'),
            __('tien_do'),
            __('thoi_gian_hoc'),
            __('trang_thai'),
        );

        foreach ($data as $key => $item) {
            $final_grade = 'N/A';
            if (isset($item['finalgrade'])) {
                if (is_numeric($item['finalgrade'])) {
                    $final_grade = strval(number_format((float)$item['finalgrade'], 2, '.', ''));
                }
            }
            $export_data[] = array(
                $key + 1,
                isset($item['shortname']) ? $item['shortname'] : '',
                isset($item['fullname']) ? $item['fullname'] : '',
                $item['user_course_learn'] > 0 ? $item['user_course_completionstate'] . '/' . $item['user_course_learn'] . '(' . round($item['user_course_completionstate'] * 100 / $item['user_course_learn']) . "%)" : $item['user_course_completionstate'] . '/' . $item['user_course_learn'] . '(0%)',
                //$final_grade,
                //$item['status_user'] == 1 &&
                //floatval($item['finalgrade']) >= floatval($item['gradepass'])
                //&&
                $item['duration'] ? (float)round($item['duration']/3600, 2) : '',
                $item['user_course_completionstate'] >= $item['user_course_learn']
                && $item['user_course_completionstate'] > 0
                    ? __('hoan_thanh') : __('chua_hoan_thanh')
            );
        }

        $exportExcel = new ResultSheet($fullname, $export_data);

        $filename = $username . "_result.xlsx";

        $exportExcel->store($filename, '', \Maatwebsite\Excel\Excel::XLSX);

        return response()->json($filename);
    }

    public function apiExportInvite(Request $request)
    {
        $keyword = $request->input('keyword');
        $course_id = $request->input('course_id');
        $course_name = $request->input('course_name');
        $organization_id = $request->input('organization_id');

        $param = [
            'course_id' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        //lấy danh sách học viên đang được enrol vào khóa học hiện tại
        $currentUserEnrol = DB::table('tms_invitation')
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_invitation.user_id')
            ->leftJoin('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->where('tms_invitation.course_id', '=', $course_id)
            ->select(
                'mdl_user.id',
                'mdl_user.username',
                'mdl_user.firstname',
                'mdl_user.lastname',
                'tms_user_detail.fullname',
                'tms_invitation.replied',
                'tms_invitation.accepted',
                'tms_invitation.reason'
            );
        if ($keyword) {
            $currentUserEnrol->where(function ($q) use ($keyword) {
                $q->where('mdl_user.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tms_user_detail.fullname', 'like', '%' . $keyword . '%');
            });
        }

        if (strlen($organization_id) != 0 && $organization_id != 0) {
            $currentUserEnrol = $currentUserEnrol->join('tms_organization_employee', 'mdl_user.id', '=', 'tms_organization_employee.user_id');
            $currentUserEnrol = $currentUserEnrol->where('tms_organization_employee.organization_id', '=', $organization_id);
        }

        $data = $currentUserEnrol->get();

        $export_data = array();

        $export_data[] = array(
            __('stt'),
            __('tai_khoan'),
            __('ho_va_ten'),
            __('trang_thai'),
            __('ly_do'),
        );

        foreach ($data as $key => $item) {
            if ($item->replied == 0) {
                $status = __('dang_cho');
            } else {
                if ($item->accepted == 1) {
                    $status = __('dong_y');
                } else {
                    $status = __('tu_choi');
                }
            }
            $export_data[] = array(
                $key + 1,
                $item->username,
                isset($item->fullname) ? $item->fullname : $item->lastname . " " . $item->firstname,
                $status,
                $item->reason
            );
        }


        $exportExcel = new InvitationSheet($course_name, $export_data);

        $filename = $course_name . " Invitation List.xlsx";

        $exportExcel->store($filename, '', \Maatwebsite\Excel\Excel::XLSX);

        return response()->json($filename);
    }

    public function apiExportUserException(Request $request)
    {
        $course_id = $request->input('course_id');
        $keyword = $request->input('keyword');
        $organization_id = $request->input('organization_id');
        $course_name = $request->input('course_name');
//        $invite_status = $request->input('invite_status');

        $param = [
            'course_id' => 'number',
            'row' => 'number',
            'role_id' => 'number',
            'keyword' => 'text',
//            'invite_status' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        //lấy danh sách học viên đang được enrol vào khóa học hiện tại
        $currentUserCourseException = DB::table('tms_user_course_exception')
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_user_course_exception.user_id')
            ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->where('tms_user_course_exception.course_id', '=', $course_id)
            ->select(
                'mdl_user.id',
                'mdl_user.username',
                'mdl_user.firstname',
                'mdl_user.lastname',
                'tms_user_detail.fullname'
            );
        if ($keyword) {
            $currentUserCourseException = $currentUserCourseException->where(function ($query) use ($keyword) {
                $query->where('mdl_user.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tms_user_detail.fullname', 'like', "%{$keyword}%");
            });
        }

        if (strlen($organization_id) != 0 && $organization_id != 0) {
            $currentUserCourseException = $currentUserCourseException->join('tms_organization_employee', 'mdl_user.id', '=', 'tms_organization_employee.user_id');
            $currentUserCourseException = $currentUserCourseException->where('tms_organization_employee.organization_id', '=', $organization_id);
        }

        $data = $currentUserCourseException->get();

        $export_data = array();

        $export_data[] = array(
            __('stt'),
            __('tai_khoan'),
            __('ho_va_ten'),
        );

        foreach ($data as $key => $item) {
            $export_data[] = array(
                $key + 1,
                $item->username,
                isset($item->fullname) ? $item->fullname : $item->lastname . " " . $item->firstname
            );
        }


        $exportExcel = new InvitationSheet($course_name, $export_data);

        $filename = $course_name . " User Exception List.xlsx";

        $exportExcel->store($filename, '', \Maatwebsite\Excel\Excel::XLSX);

        return response()->json($filename);
    }

    public function apiExportAttendance(Request $request)
    {
        $keyword = $request->input('keyword');
        $course_id = $request->input('course_id');
        $course_name = $request->input('course_name');
        $course_code = $request->input('course_code');

        $param = [
            'course_id' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }


        $param = [
            'course_id' => 'number',
            'row' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        //Lấy danh sách học viên trong khóa học và leftjoin vào table điểm danh
        $lstUserAttendance = DB::table('mdl_user_enrolments as mu')
            ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
            ->leftJoin('mdl_attendance as mat', 'mat.userid', '=', 'mu.userid')
            ->leftJoin('tms_user_detail as tud', 'u.id', '=', 'tud.user_id')
            ->where('c.id', '=', $course_id)
            ->where('e.roleid', 5)//hoc vien only
            ->select(
                'u.id as user_id',
                'u.username',
                'u.firstname',
                'u.lastname',
                'tud.fullname',
                'c.total_date_course',
                DB::raw('(SELECT count(att.id) FROM `mdl_attendance` att where att.courseid = c.id and att.userid = u.id) as count_attendance')
            );

        //search
        if ($keyword) {
            $lstUserAttendance->where(function ($q) use ($keyword) {
                $q->where('u.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tud.fullname', 'like', '%' . $keyword . '%');
            });
        }
        //order by
        $lstUserAttendance = $lstUserAttendance->orderBy('u.id', 'desc')->groupBy('u.id');

        $data = $lstUserAttendance->get();

        $export_data = array();

        $export_data[] = array(
            __('stt'),
            __('tai_khoan'),
            __('ho_va_ten'),
            __('so_buoi_diem_danh')
        );

        foreach ($data as $key => $item) {
            $attendance = $item->count_attendance . "/" . $item->total_date_course;
            $export_data[] = array(
                $key + 1,
                $item->username,
                isset($item->fullname) ? $item->fullname : $item->lastname . " " . $item->firstname,
                $attendance
            );
        }

        $exportExcel = new AttendanceSheet($course_code, $export_data);

        $filename = $course_code . " Attendance List.xlsx";

        $exportExcel->store($filename, '', \Maatwebsite\Excel\Excel::XLSX);

        return response()->json($filename);
    }

    public function apiDownloadExport($file_name)
    {
        return Storage::download($file_name);
    }

    public function download($file_name)
    {
        return Storage::download($file_name);
    }

    public function exportLogLogin(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        $param = [
            'keyword' => 'text',
            'row' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = DB::table('mdl_logstore_standard_log as mls')
            ->join('mdl_user as u', 'u.id', '=', 'mls.objectid')
            ->join('tms_user_detail as tud', 'tud.user_id', '=', 'u.id')
            ->where('mls.target', '=', 'user')
            ->where('mls.action', '=', 'loggedin')
            ->where('u.username', '!=', 'admin')
            ->select('u.id', 'u.username', 'tud.fullname', 'mls.timecreated');

        if ($keyword) {
            $lstData = $lstData->whereRaw('( tud.fullname like "%' . $keyword . '%" OR u.username like "%' . $keyword . '%" )');
        }

        if (empty($startdate) && empty($enddate)) {
            $now = \date('d-m-Y');

            $startdate = $now . " 00:00:00";
            $startdate = strtotime($startdate);

            $enddate = $now . " 23:59:59";
            $enddate = strtotime($enddate);

            $lstData = $lstData->where('mls.timecreated', '>=', $startdate);
            $lstData = $lstData->where('mls.timecreated', '<=', $enddate);
        } else {
            if ($startdate) {
                $startdate = $startdate . " 00:00:00";
                $startdate = strtotime($startdate);
                $lstData = $lstData->where('mls.timecreated', '>=', $startdate);
            }

            if ($enddate) {
                $enddate = $enddate . " 23:59:59";
                $enddate = strtotime($enddate);
                $lstData = $lstData->where('mls.timecreated', '<=', $enddate);
            }

        }

        $datas = $lstData->orderBy('mls.id', 'desc')->get();

        $arr_data = [];
        $stt = 1;
        foreach ($datas as $item) {
            $arr_data[] = array(
                $stt,
                $item->username,
                $item->fullname,
                date('d M Y H:i:s', $item->timecreated)
            );
            $stt++;
        }
        $exportExcel = new LoginSheet('Statistic Login Report', $arr_data);

        $filename = "statistic_login_report.xlsx";
        $exportExcel->store($filename, '', \Maatwebsite\Excel\Excel::XLSX);

        return response()->json(storage_path($filename));
    }

    public function downloadExportLoginReport()
    {
        $filename = "statistic_login_report.xlsx";
        return Storage::download($filename);
    }

    public function importMark(Request $request)
    {

        set_time_limit(0);


        $year = $request->input('year');


        if (!$request->file('file')) {
            return response()->json(self::status_message('error', "File missing"));
        } else {
            //check file is xlsx, xls
            $extension = $request->file('file')->getClientOriginalExtension();
            if ($extension != 'xls' && $extension != 'xlsx') {
                return response()->json(self::status_message('error', "File type mismatch. Allow XLS, XLSX file only"));
            }
            $files = array($request->file('file'));
        }

        foreach ($files as $file_path) {

            /* @var $file_path UploadedFile */
            $full_file_name = $file_path->getClientOriginalName();
            $file_name = pathinfo($full_file_name, PATHINFO_FILENAME);

            $list_uploaded = (new DataImport())->toArray($file_path, '', '');

            $response = array();
            //Lấy dữ liệu từ tab Mark <= Required
            $list_data = $list_uploaded['Mark'];
            $title = '';
            $competencies = array();
            $competencies_ids = array();
            $competencies_marks = array();

            foreach ($list_data as $row_no => $item) {

                $content = array();
                $status = true;

                //Row 0 Title
                if ($row_no == 0) {
                    for ($i = 0; $i <= 30; $i++) {
                        if (isset($item[$i])) {
                            if (strlen($item[$i]) > 0) {
                                $title = $item[$i];
                            }
                        } else {
                            break;
                        }

                    }
                }
                //Row 1 competency code
                if ($row_no == 1) {
                    for ($i = 1; $i <= 30; $i++) {
                        if (isset($item[$i])) {
                            if (strlen($item[$i]) > 0) {
                                $competencies[$i] = $item[$i];
                            }
                        } else { //duyệt đến hết cột thì thôi
                            continue;
                        }
                    }
                    if (count($competencies) > 0) {
                        $checkCompetency = TmsTdCompetency::query()->whereIn('code', $competencies)->get();
                        if (count($checkCompetency) != count($competencies)) { //Lệch data, có code không thuộc bảng competencies
                            return response()->json(self::status_message('error', "Competency code is not valid, please check and try again"));
                        } else {
                            $competencies_ids = $competencies;
                            $competencies_marks = $competencies;
                            foreach ($checkCompetency as $competency) {
                                $key = array_search($competency->code, $competencies);
                                $competencies_ids[$key] = $competency->id;
                            }
                        }
                    } else {
                        return response()->json(self::status_message('error', "Competency code is not valid, please check and try again"));
                    }

                }
                //Row 2 competency average mark
                if ($row_no == 2) {
                    $email = 'MAXIMUM MARK';
                    $max = max(array_keys($competencies));
                    for ($i = 1; $i <= $max; $i++) {
                        $mark = $item[$i];
                        if (!isset($mark) || strlen($mark) == 0) {
                            $content[] = "Missing maximum mark for competency";
                        } else {
                            if (self::validateRawData($mark, 'number')) {
                                $competencies_marks[$i] = $mark;
                                TmsTdCompetencyMark::updateOrCreate(
                                    [
                                        'competency_id' => $competencies_ids[$i],
                                        'year' => $year
                                    ],
                                    ['mark' => $mark] //Update
                                );
                            } else { //check by validation function
                                $content[] = 'Maximum mark is not valid';
                            }
                        }
                    }
                }

                //From row 3 => data row
                if ($row_no > 2) {
                    $email = $item[0];
                    if (strlen($email) != 0) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                            $content[] = "Email is wrong format";
                            $status = false;
                        } else {//Continue
                            $checkUser = MdlUser::query()->where('username', $email)->first();
                            if (!isset($checkUser)) {
                                $status = false;
                                $content[] = "User does not exist";
                            } else {//Continue
                                $max = max(array_keys($competencies));
                                for ($i = 1; $i <= $max; $i++) {
                                    $mark = $item[$i];
                                    if (!isset($mark) || strlen($mark) == 0) {
                                        $content[] = "Missing user mark";
                                    } else {
                                        if (self::validateRawData($mark, 'number')) {
                                            if ($mark > $competencies_marks[$i]) {
                                                $content[] = "User's mark is greater than competency maximum mark";
                                            } else {
                                                TmsTdUserMark::updateOrCreate(
                                                    [
                                                        'user_id' => $checkUser->id,
                                                        'competency_id' => $competencies_ids[$i],
                                                        'year' => $year
                                                    ],
                                                    ['mark' => $mark] //Update
                                                );
                                            }
                                        } else {
                                            $content[] = "User's mark is not valid";
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        //dừng code k duyệt tiếp
                        break;
                    }
                }

                if ($row_no > 1) {
                    $response_item = array(
                        '',
                        $email,
                        '',
                        ''
                    );

                    if ($status == false) {
                        $response_item[2] = 'error';
                    } else {
                        if (!empty($content)) {
                            $response_item[2] = 'warning';
                        } else {
                            $response_item[2] = 'success';
                        }
                    }
                    $response_item[3] = implode("\n", $content);

                    $response[] = $response_item;
                }
            }

            $result_file_name = "import_result_" . $file_name . ".xlsx";

            //xóa file cũ
            if (Storage::exists($result_file_name)) {
                Storage::delete($result_file_name);
            }

            //ghi file vào thư mục storage, không được mở file khi đang lưu nếu k sẽ lỗi k lưu được
            $exportExcel = new ImportResultSheet('Import Result', $response);
            $exportExcel->store($result_file_name, '', Excel::XLSX);

            return response()->json(self::status_message('success', __('nhap_du_lieu_thanh_cong'), ['result_file' => $result_file_name]));

        }
    }

    public function apiExportMark(Request $request) {

        set_time_limit(0);
        $years = $request->input('years');
        $organization_id = $request->input('organization_id');
        $countries = TmsUserDetail::country;
        $flow = $request->input('flow');

        $years = explode(',', $years);
        asort($years);
        $max_year = max($years);

        $competencies = DB::table('tms_td_competencies')->get();


        $marks = DB::table('tms_td_user_marks')

            ->join('tms_user_detail', 'tms_td_user_marks.user_id', '=', 'tms_user_detail.user_id')
            ->join('tms_td_competencies', 'tms_td_user_marks.competency_id', '=', 'tms_td_competencies.id')
            ->join('tms_td_competency_marks', function ($join) {
                $join->on('tms_td_competency_marks.competency_id', '=', 'tms_td_user_marks.competency_id');
                $join->on('tms_td_competency_marks.year', '=', 'tms_td_user_marks.year');
            })

            ->join('tms_td_competency_courses', 'tms_td_user_marks.competency_id', '=', 'tms_td_competency_courses.competency_id')
            ->join('mdl_course as org_course', 'tms_td_competency_courses.course_id', '=', 'org_course.id')

            ->leftJoin('course_completion', function ($join) use ($max_year, $years) {
                $join->on('tms_td_competency_courses.course_id', '=', 'course_completion.courseid');
                $join->on('tms_td_user_marks.user_id', '=', 'course_completion.userid');
                //$join->whereIn(DB::raw('year(course_completion.updated_at)'), $years);
                $join->whereRaw('year(course_completion.updated_at) <= ' . $max_year);
            }) //Will be duplicates => removes when execute data later
            ->leftJoin('mdl_course as learned_course', 'course_completion.courseid', '=', 'learned_course.id')

            ->join('tms_organization_employee', 'tms_user_detail.user_id', '=', 'tms_organization_employee.user_id')
            ->join('tms_organization', 'tms_organization_employee.organization_id', '=', 'tms_organization.id')

            ->select(
                'tms_td_user_marks.user_id',
                'tms_td_user_marks.year',
                'tms_td_user_marks.user_id',
                'tms_user_detail.fullname',
                'tms_user_detail.email',
                'tms_user_detail.city',
                'tms_user_detail.country',
                'tms_organization.code as organization_code',
                'tms_organization_employee.description as employee_title',
                'tms_td_competencies.name as competency_name',
                'tms_td_competencies.code as competency_code',
                DB::raw("CONCAT(tms_td_user_marks.mark, '/', tms_td_competency_marks.mark) AS result"),
                DB::raw("GROUP_CONCAT(org_course.shortname) AS org_courses"),
                DB::raw("GROUP_CONCAT(learned_course.shortname) AS learned_courses")
            )

            ->whereIn('tms_td_user_marks.year', $years)

            ->groupBy([
                'tms_td_user_marks.user_id',
                'tms_td_user_marks.mark',
                'tms_td_user_marks.year',
                'tms_user_detail.fullname',
                'tms_user_detail.email',
                'tms_user_detail.city',
                'tms_user_detail.country',
                'organization_code',
                'employee_title',
                'tms_td_user_marks.competency_id',
                'competency_name',
                'competency_code',
                'tms_td_competency_marks.mark'
            ]);

        $view_mode = 'recursive';
        if (is_numeric($organization_id) && $organization_id != 0) {
            //$marks->where('tms_organization_employee.organization_id', $organization_id);
            if ($view_mode == 'recursive') {
                $marks->whereIn('tms_organization_employee.user_id', function ($q2) use ($organization_id) {
                    $q2->select('org_uid')->from(DB::raw("(select ttoe.organization_id, ttoe.user_id as org_uid
                            from (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe join tms_organization tor on tor.id = toe.organization_id order by tor.parent_id, toe.id) ttoe,
                            (select @pv := $organization_id) initialisation
                            where find_in_set(ttoe.parent_id, @pv) and length(@pv := concat(@pv, ',', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = $organization_id) as org_tp"));
                });
            } else {
                $marks->where('organization_id', $organization_id);
            }

        }

        $marks = $marks->get();

        $response = []; //Export vertical
        $flip_response = []; //Show to page horizontal

        $headings = [
            'Email',
            'Title',
            'Department',
            'City',
            'Country',
        ];

        $dynamic_heading = [];

        $code_grey = [];

        foreach ($competencies as $key => $competency) {
            $code = $competency->code;
            if ($key%2 == 0) {
                $code = $competency->code . '_GREY_';
                $code_grey[] = $competency->code;
            }

            //FLow export
            foreach ($years as $year) {
                $dynamic_heading[$code. $year] = $year;
            }
            $dynamic_heading[$code] = $competency->name;
            //$dynamic_heading[$competency->code . "_all"] = $competency->name;

            //Flow show to page
            if (!array_key_exists($competency->code, $flip_response)) { //Khởi tạo object cho từng competency
                $flip_response[$competency->code]['name'] = $competency->name;
                $flip_response[$competency->code]['users'] = [];
            }
        }

        $headings = array_merge($headings, $dynamic_heading);

        foreach ($marks as $mark) {

            //Flow export
            //khởi tạo
            if (!array_key_exists($mark->user_id, $response)) {
                $country_name = array_key_exists($mark->country, $countries) ? $countries[$mark->country] : '';
                $mark_item = array(
                    $mark->email,
                    $mark->employee_title,
                    $mark->organization_code,
                    $mark->city,
                    $country_name,
                );
                foreach ($dynamic_heading as $key => $heading) {
                    $mark_item[$key] = '';
                }
                $response[$mark->user_id] = $mark_item;
            }

            $new_code = $mark->competency_code;
            if (in_array($mark->competency_code, $code_grey)) {
                $new_code = $mark->competency_code . '_GREY_';
            }

            //check row, nếu có điểm thì add vào
            $response[$mark->user_id][ $new_code . $mark->year] = $mark->result;
            //$response[$mark->user_id][$mark->competency_code . "_all"] = $mark->org_courses;
            //$response[$mark->user_id][$mark->competency_code] = str_replace(',', PHP_EOL, $mark->learned_courses);
            $learned_courses = array_unique(explode(',', $mark->learned_courses));
            $export_learned_courses = implode(',',$learned_courses);
            $response[$mark->user_id][$new_code] = str_replace(',', PHP_EOL, $export_learned_courses);


            //Flow show to page
            if (!array_key_exists($mark->email, $flip_response[$mark->competency_code]['users'])) {
                $flip_response[$mark->competency_code]['users'][$mark->email] = [
                    'marks' => [intval($mark->year) => $mark->result],
                    'courses' => array_values($learned_courses)
                ];
                foreach ($years as $year) { //Thêm unit year empty
                    if (!array_key_exists(intval($year), $flip_response[$mark->competency_code]['users'][$mark->email]['marks'])) {
                        $flip_response[$mark->competency_code]['users'][$mark->email]['marks'][intval($year)] = '';
                    }
                }

            } else {
                $flip_response[$mark->competency_code]['users'][$mark->email]['marks'][intval($mark->year)] = $mark->result;
            }
        }

        if ($flow == 'show') { //Flow show to page
            return response()->json(self::status_message('success', __('thanh_cong'), $flip_response));
        }

        //continue flow export
        $result_file_name = "training_effect.xlsx";
        //xóa file cũ
        if (Storage::exists($result_file_name)) {
            Storage::delete($result_file_name);
        }

        $exportExcel = new ReportMarkSheet('Training Effect', $response, $headings);
        $exportExcel->store($result_file_name, '', Excel::XLSX);

        return response()->json(self::status_message('success', __('xuat_du_lieu_thanh_cong'), ['result_file' => $result_file_name]));
    }

    function status_message($status, $message, $additional_data = [])
    {
        $data = [];
        $data['status'] = $status;
        $data['message'] = $message;
        $data['data'] = $additional_data;
        return $data;
    }

    /**
     * @param $val
     * @param $type
     * @return bool
     */
    public function validateRawData($val, $type) {
        $need_to_validate = new \Illuminate\Http\Request();
        $need_to_validate['input'] = $val;
        $param = [
            'input' => $type
        ];
        $validator = validate_fails($need_to_validate, $param);
        if (!empty($validator)) {
            return false;
        } else {
            return true;
        }
    }
}
