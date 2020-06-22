<?php

namespace App\Http\Controllers\Backend;

use App\Exports\AttendanceSheet;
use App\Exports\InvitationSheet;
use App\Exports\ListMismatchSaleroom;
use App\Exports\LoginSheet;
use App\Exports\ReportDetailSheet;
use App\Exports\ReportSheet;
use App\Exports\ResultSheet;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        if($selected_level == "city") {
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
                    count($saleroom['user_incomplete']) == 0 ? "0" :  count($saleroom['user_incomplete']),
                    count($saleroom['user_completed']) == 0 ? "0" :  count($saleroom['user_completed']),
                    count($saleroom['user_confirm']) == 0 ? "0" :  count($saleroom['user_confirm']),
                    count($saleroom['user']) == 0 ? "0" :  count($saleroom['user']),
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

    public function exportReportDetail(Request $request)
    {

        $data = $request->input('data');
        $type = $request->input('type');

        $export_data = array();
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
            __('diem'),
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
                $item['user_course_learn'] > 0 ? $item['user_course_completionstate'] . '/' . $item['user_course_learn'] . '(' . (($item['user_course_completionstate'] / $item['user_course_learn']) * 100 | 0.00) . "%)" : $item['user_course_completionstate'] . '/' . $item['user_course_learn'] . '(0%)',
                $final_grade,
                $item['status_user'] == 1
                && floatval($item['finalgrade']) >= floatval($item['gradepass'])
                && $item['user_course_completionstate'] == $item['user_course_learn']
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
            $currentUserEnrol->where(function($q) use ($keyword) {
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
            $lstUserAttendance->where(function($q) use ($keyword) {
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

    public function apiDownloadExport($file_name) {
        return Storage::download($file_name);
    }

    public function download($file_name) {
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
            $startdate = Carbon::yesterday();
            $startdate = strtotime($startdate);


            $enddate = Carbon::now();
            $enddate = strtotime($enddate);

            $lstData = $lstData->where('mls.timecreated', '>=', $startdate);
            $lstData = $lstData->where('mls.timecreated', '<=', $enddate);
        } else {
            if ($startdate) {
                $startdate = strtotime($startdate);
                $lstData = $lstData->where('mls.timecreated', '>=', $startdate);
            }

            if ($enddate) {
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
}
