<?php


namespace App\Repositories;


use App\MdlContext;
use App\MdlCourse;
use App\MdlCourseCategory;
use App\MdlCourseCompletionCriteria;
use App\MdlEnrol;
use App\MdlGradeCategory;
use App\MdlGradeItem;
use App\Role;
use App\TmsNotification;
use App\TmsRoleCourse;
use App\TmsRoleOrganization;
use App\TmsTrainningCategory;
use App\TmsTrainningCourse;
use App\TmsTrainningGroup;
use App\TmsTrainningProgram;
use App\TmsTrainningUser;
use App\TmsUserDetail;
use App\ViewModel\ResponseModel;
use Carbon\Carbon;
use Horde\Socket\Client\Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TrainningRepository implements ITranningInterface, ICommonInterface
{

    public function getall(Request $request)
    {
        // TODO: Implement getall() method.
    }

    public function store(Request $request)
    {
        // TODO: Implement store() method.
        $response = new ResponseModel();
        try {
            $code = $request->input('code');
            $name = $request->input('name');
            $style = $request->input('style');
            $run_cron = $request->input('run_cron');
            $auto_certificate = $request->input('auto_certificate');
            $auto_badge = $request->input('auto_badge');
            $time_start = $request->input('time_start');
            $time_end = $request->input('time_end');
            $role_id = $request->input('role_id');
            $organization_id = $request->input('organization_id');
            $description = $request->input('description');
            $logo = $request->file('file');

            $param = [
                'code' => 'code',
                'name' => 'text',
                'time_start' => 'text',
                'time_end' => 'text',
                'role_id' => 'number',
                'organization_id' => 'number',
                'description' => 'text',
            ];
            $validator = validate_fails($request, $param);

            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }
            if ($style == 1) {
                if (!$time_start || $time_start == 0) {
                    $response->status = false;
                    $response->message = __('ban_chua_nhap_ngay_bat_dau');
                    return response()->json($response);
                }
            }

            \DB::beginTransaction();
            //check course info exist
            $trainningInfo = TmsTrainningProgram::select('id')->where('code', $code)->first();

            if ($trainningInfo) {
                $response->status = false;
                $response->message = __('ma_trainning_da_ton_tai');
                return response()->json($response);
            }

            if ($style == 1) {
                $time_start = strtotime($time_start);
                if (!$time_end) {
                    $time_end = $time_start + 365 * 24 * 60 * 60;
                } else {
                    $time_end = strtotime($time_end);
                }
            } else {
                $time_start = 0;
                $time_end = 0;
            }

            $path_logo = '';
            if ($logo) {
                $name_logo = time() . '.' . $logo->getClientOriginalExtension();

                // cơ chế lưu ảnh vào folder storage, tăng cường bảo mật
                Storage::putFileAs(
                    'public/upload/certificate',
                    $logo,
                    $name_logo
                );
                $path_logo = '/storage/upload/certificate/' . $name_logo;
            }

            $tms_trainning = TmsTrainningProgram::firstOrCreate([
                'code' => $code,
                'name' => $name,
                'style' => $style,
                'run_cron' => $run_cron ? 1 : 0,
                'time_start' => $time_start,
                'time_end' => $time_end,
                'logo' => $path_logo,
                'description' => $description,
                'auto_certificate' => $auto_certificate ? 1 : 0,
                'auto_badge' => $auto_badge ? 1 : 0
            ]);

            if ($role_id && $role_id != 0 && $tms_trainning) {
                TmsTrainningGroup::firstOrCreate([
                    'trainning_id' => $tms_trainning->id,
                    'group_id' => $role_id,
                    'type' => 0
                ]);
            }

            if ($organization_id && $organization_id != 0 && $tms_trainning) {
                TmsTrainningGroup::firstOrCreate([
                    'trainning_id' => $tms_trainning->id,
                    'group_id' => $organization_id,
                    'type' => 1
                ]);
            }

            \DB::commit();
            $response->status = true;
            $response->otherData = $tms_trainning->id;
            $response->message = __('them_moi_khung_nang_luc_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function update(Request $request)
    {
        // TODO: Implement update() method.
        $response = new ResponseModel();
        try {
            $id = $request->input('id');
            $code = $request->input('code');
            $name = $request->input('name');
            $style = $request->input('style');
            $auto_certificate = $request->input('auto_certificate');
            $auto_badge = $request->input('auto_badge');
            $run_cron = $request->input('run_cron');
            $time_start = $request->input('time_start');
            $time_end = $request->input('time_end');
            $role_id = $request->input('role_id');
            $organization_id = $request->input('organization_id');
            $description = $request->input('description');
            $logo = $request->file('file');

            $param = [
                'code' => 'code',
                'name' => 'text',
                'time_start' => 'text',
                'time_end' => 'text',
                'role_id' => 'number',
                'organization_id' => 'number',
                'description' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }

            \DB::beginTransaction();


            $trainningInfo = TmsTrainningProgram::select('id')->whereNotIn('id', [$id])->where('code', $code)->first();
            if ($trainningInfo) {
                $response->status = false;
                $response->message = __('ma_trainning_da_ton_tai');
                return response()->json($response);
            }

            $trainning = TmsTrainningProgram::findOrFail($id);


            if ($style == 1) {
                $time_start = strtotime($time_start);
                if (!$time_end) {
                    $time_end = $time_start + 365 * 24 * 60 * 60;
                } else {
                    $time_end = strtotime($time_end);
                }
            } else {
                $time_start = 0;
                $time_end = 0;
            }

            $trainning->code = $code;
            $trainning->name = $name;
            $trainning->style = $style;
            $trainning->description = $description;
            $trainning->auto_certificate = $auto_certificate ? 1 : 0;
            $trainning->auto_badge = $auto_badge ? 1 : 0;
            $trainning->run_cron = $run_cron;
            $trainning->time_start = $time_start;
            $trainning->time_end = $time_end;

            if ($logo) {
                $name_logo = time() . '.' . $logo->getClientOriginalExtension();

                // cơ chế lưu ảnh vào folder storage, tăng cường bảo mật
                Storage::putFileAs(
                    'public/upload/certificate',
                    $logo,
                    $name_logo
                );
                $path_logo = '/storage/upload/certificate/' . $name_logo;
                $trainning->logo = $path_logo;
            }

            $trainning->save();

            TmsTrainningGroup::where([
                'trainning_id' => $id,
                'type' => 0
            ])->delete();
            TmsTrainningGroup::where([
                'trainning_id' => $id,
                'type' => 1
            ])->delete();
            if ($role_id && $role_id != 0) {
                TmsTrainningGroup::firstOrCreate([
                    'trainning_id' => $id,
                    'group_id' => $role_id,
                    'type' => 0
                ]);
            }

            if ($organization_id && $organization_id != 0) {
                TmsTrainningGroup::firstOrCreate([
                    'trainning_id' => $id,
                    'group_id' => $organization_id,
                    'type' => 1
                ]);
            }

            $totalCourse = TmsTrainningCourse::where('trainning_id', '=', $trainning->id)->where('deleted', '=', 0)->count();
            if ($totalCourse > 0) {
                //cap nhat flag, chay cron
                updateFlagCron(Config::get('constants.domain.ENROLL_TRAINNING'), Config::get('constants.domain.ACTION_UPDATE_FLAG'),
                    Config::get('constants.domain.START_CRON'));
            }

            \DB::commit();
            $response->status = true;
            $response->message = __('sua_khung_nang_luc_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
        $response = new ResponseModel();
        try {
            if (!is_numeric($id)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $check = TmsTrainningUser::where('trainning_id', $id)->count();
            if ($check > 0) {
                $response->status = false;
                $response->message = __('ton_tai_hoc_vien_trong_khung_nang_luc_khong_the_xoa');
                return response()->json($response);
            }

            TmsTrainningGroup::where('trainning_id', $id)->delete();

            TmsTrainningCourse::where('trainning_id', $id)->update(['deleted' => 1]);

            $trainning = TmsTrainningProgram::findOrFail($id);
            $trainning->deleted = 1;
            $trainning->save();

            $response->status = true;
            $response->message = __('xoa_khung_nang_luc_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function apiGetListTrainning(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $style = $request->input('style');

        $param = [
            'keyword' => 'text',
            'row' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }
        $lstData = DB::table('tms_traninning_programs as ttp')
            ->leftJoin('tms_traninning_users as ttu', 'ttu.trainning_id', '=', 'ttp.id')
            ->where('ttp.deleted', '=', 0);

        if (tvHasRoles(\Auth::user()->id, ["admin", "root"]) or slug_can('tms-system-administrator-grant')) {
            //Admin thì lấy hết
            $lstData = $lstData
//                ->leftJoin('tms_traninning_users as ttu', 'ttu.trainning_id', '=', 'ttp.id')
//                ->leftJoin('mdl_user as mu', 'mu.id', '=', 'ttu.user_id')
                ->leftJoin('tms_user_detail as tud', function ($join) {
                    $join->on('ttu.user_id', '=', 'tud.user_id');
                    $join->where('tud.deleted', '=', 0);
                })
//                ->select('ttp.id', 'ttp.code', 'ttp.name', DB::raw('count(ttu.id) as total_user'))
                ->select('ttp.id', 'ttp.code', 'ttp.name', DB::raw('count(DISTINCT ttu.user_id) as total_user'));
//                ->where('ttp.deleted', '!=', 2);//cac KNL tu dong sinh ra khi tao moi khoa hoc online, tap trung;
        } else {

            $organization_id = 0;
            $checkRoleOrg = tvHasOrganization(\Auth::user()->id);
            if ($checkRoleOrg != 0) {
                $organization_id = $checkRoleOrg;
            }
            //lấy các khung năng lực được gán cho tổ chức
            $lstData = $lstData
                ->join('tms_trainning_groups', function ($q) use ($organization_id) {
                    $q->on('tms_trainning_groups.trainning_id', '=', 'ttp.id');
                    $q->where('tms_trainning_groups.group_id', $organization_id);
                    $q->where('tms_trainning_groups.type', 1); //Training to organization relation
                })
//                ->leftJoin('tms_traninning_users as ttu', 'ttu.trainning_id', '=', 'ttp.id')
                ->select('ttp.id', 'ttp.code', 'ttp.name', DB::raw('count(ttu.id) as total_user'));
//                ->where('ttp.deleted', '=', 0);
            //->where('ttp.deleted', '!=', 2);//cac KNL tu dong sinh ra khi tao moi khoa hoc online, tap trung;

        }

        if ($this->keyword) {
            $lstData = $lstData->where(function ($query) {
                $query->orWhere('ttp.code', 'like', "%{$this->keyword}%")
                    ->orWhere('ttp.name', 'like', "%{$this->keyword}%");
            });
        }

        if ($style) {
            $lstData = $lstData->where('ttp.style', $style);
        } else { //Khung năng lực thông thường
            $lstData = $lstData->where('ttp.style', 0);
        }

        $lstData = $lstData->orderBy('ttp.id', 'desc');

        if (is_null($row) || $row == 0)
            $row = 5;

        $lstData->groupBy('ttp.id');

        $lstData = $lstData->paginate($row);
        $total = ceil($lstData->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstData->currentPage(),
            ],
            'data' => $lstData
        ];

        return response()->json($response);
    }

    public function apiGetListTrainingForFilter()
    {
        $response = TmsTrainningProgram::select('id', 'code', 'name')
            ->where('deleted', '=', 0)
            ->orderBy('name', 'asc')
            ->get();
        return response()->json($response);
    }

    public function apiGetDetailTrainning($id)
    {
        $id = is_numeric($id) ? $id : 0;
        $trainning = DB::table('tms_traninning_programs as ttp')
            ->where('ttp.id', '=', $id)
            ->select(
                'ttp.id',
                'ttp.code',
                'ttp.name',
                'ttp.style',
                'ttp.run_cron',
                'ttp.auto_certificate',
                'ttp.auto_badge',
                'ttp.time_start',
                'ttp.time_end',
                'ttp.description',
                'ttp.logo',
                DB::raw('(select ttr.group_id as role_id from tms_trainning_groups ttr where ttr.type = 0 and ttr.trainning_id = ttp.id) as role_id'),
                DB::raw('(select tto.group_id as organization_id from tms_trainning_groups tto where tto.type = 1 and tto.trainning_id = ttp.id) as organization_id')
            )
            ->first();

        if ($trainning->time_start && $trainning->time_start != 0) {
            $trainning->time_start = date('d-m-Y', $trainning->time_start);
        } else {
            $trainning->time_start = '';
        }
        if ($trainning->time_end && $trainning->time_end != 0) {
            $trainning->time_end = date('d-m-Y', $trainning->time_end);
        } else {
            $trainning->time_end = '';
        }

        return response()->json($trainning);
    }

    //lay danh sach khoa hoc mau da co trong khung nang luc
    public function apiGetCourseSampleTrainning(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $trainning_id = $request->input('trainning_id');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'trainning_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = DB::table('tms_trainning_courses as ttc')
            ->join('mdl_course as mc', 'mc.id', '=', 'ttc.course_id')
            ->where('ttc.trainning_id', '=', $trainning_id)
            ->where('ttc.deleted', '=', 0)
            ->where('mc.deleted', '=', 0)
            ->where('mc.visible', '=', 1)
            ->select('mc.id', 'mc.fullname', 'mc.shortname', 'ttc.sample_id', 'ttc.order_no');

        if ($this->keyword) {
            $lstData = $lstData->where(function ($query) {
                $query->orWhere('mc.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('mc.shortname', 'like', "%{$this->keyword}%");
            });
        }
        $lstData = $lstData->orderBy('ttc.order_no', 'asc');

        $lstAllData = $lstData->get();
        $lstData = $lstData->paginate($row);
        $total = ceil($lstData->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstData->currentPage(),
            ],
            'data' => $lstData,
            'allData' => $lstAllData
        ];


        return response()->json($response);
    }

    public function apiGetListTrainingCourse(Request $request)
    {
        // TODO: Implement getall() method.
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $is_excluded = $request->input('is_excluded');
        $training_id = $request->input('training_id'); //đã gán vào quyền hay chưa

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'is_excluded' => 'number',
            'training_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $listCourses = DB::table('mdl_course as c')
            ->leftJoin('tms_trainning_courses as ttc', function ($join) use ($training_id) {
                $join->on('c.id', '=', 'ttc.course_id');
                $join->where('ttc.trainning_id', $training_id);
                $join->where('ttc.deleted', 0);
            })
            ->select(
                'c.id',
                'c.fullname',
                'c.shortname',
                'c.startdate',
                'c.enddate',
                'c.visible',
                'c.deleted',
                'ttc.order_no'
            );

        if (strlen($is_excluded) != 0) {
            if ($is_excluded == 0) { //List khóa học chưa gán
                $listCourses->whereNull('ttc.id');
            } else { //List khóa học đã gán
                $listCourses->whereNotNull('ttc.id');
            }
        }

        $listCourses = $listCourses->where('c.deleted', '=', 0);
        $listCourses = $listCourses->where('c.category', '<>', 2);
        $listCourses = $listCourses->where('c.visible', '=', 1);

        if ($keyword) {
            //lỗi query của mysql, không search được kết quả khi keyword bắt đầu với kỳ tự d or D
            // code xử lý remove ký tự đầu tiên của keyword đi
            if (substr($keyword, 0, 1) === 'd' || substr($keyword, 0, 1) === 'D') {
                $total_len = strlen($keyword);
                if ($total_len > 2) {
                    $keyword = substr($keyword, 1, $total_len - 1);
                }
            }

            $listCourses = $listCourses->whereRaw('( c.fullname like "%' . $keyword . '%" OR c.shortname like "%' . $keyword . '%" )');
        }

        if (strlen($is_excluded) != 0) {
            if ($is_excluded == 0) { //List khóa học chưa gán
                $listCourses = $listCourses->orderBy('c.id', 'desc');
            } else { //List khóa học đã gán
                $listCourses = $listCourses->orderBy('ttc.order_no', 'asc');
            }
        }

        $lstAllData = $listCourses->get();
        $totalCourse = count($lstAllData); //lấy tổng số khóa học hiện tại
        $listCourses = $listCourses->paginate($row);
        $total = ceil($listCourses->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listCourses->currentPage(),
            ],
            'data' => $listCourses,
            'total_course' => $totalCourse,
            'allData' => $lstAllData
        ];


        return response()->json($response);
    }

    public function apiSaveOrder(Request $request)
    {
        $list = $request->input('list');
        $training_id = $request->input('training_id');
        $param = [
            'trainning_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator) || !is_array($list)) {
            return response()->json([]);
        }

        //Check đã có học viên học
        $checkLearned = DB::table('mdl_course_modules as cm')
            ->join('mdl_course_modules_completion as cmc', 'cm.id', '=', 'cmc.coursemoduleid')
            ->join('mdl_course_sections as cs', function ($join) {
                $join->on('cm.course', '=', 'cs.course');
                $join->on('cm.section', '=', 'cs.id'); //Lấy học viên only
            })
            ->join('mdl_course as c', 'cm.course', '=', 'c.id')
            ->join('tms_trainning_courses as ttc', 'ttc.course_id', '=', 'c.id')
            ->where('cs.section', '<>', 0)
            ->where('cm.completion', '<>', 0)
            ->whereIn('cmc.completionstate', [1, 2])
            ->where('ttc.trainning_id', '=', $training_id)
            ->select(DB::raw('count(cmc.coursemoduleid) as num'))->first();

        if ($checkLearned && $checkLearned->num > 0) {
            return array(
                'status' => false,
                'message' => __('da_co_hoc_vien_hoc_khung_nang_luc_nay_nen_khong_thay_doi_thu_tu_duoc')
            );
        }

        DB::beginTransaction();
        try {
            foreach ($list as $key => $item) {
                $order_no = $key + 1;
                TmsTrainningCourse::query()
                    ->where('trainning_id', $training_id)
                    ->where('course_id', $item['id'])
                    ->update([
                        'order_no' => $order_no,
                    ]);
            }
            DB::commit();
            $response = array(
                'status' => true,
                'message' => __('thanh_cong')
            );
        } catch (\Exception $e) {
            DB::rollBack();
            $response = array(
                'status' => false,
                'message' => __('loi_he_thong_thao_tac_that_bai')
            );
        }
        return response()->json($response);
    }

    //lay danh sach khoa hoc mau chua co trong khung nang luc
    public function apiGetListSampleCourse(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $trainning_id = $request->input('trainning_id');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'trainning_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstCourseTrainning = DB::table('tms_trainning_courses as ttc')
            ->join('mdl_course as c', 'c.id', '=', 'ttc.sample_id')
            ->where('ttc.trainning_id', '=', $trainning_id)
            ->where('ttc.deleted', '=', 0)
            ->where('c.deleted', '=', 0)
            ->select('c.id')->groupBy('ttc.sample_id')->pluck('c.id');

        $lstData = DB::table('mdl_course as mc')
            ->leftJoin('mdl_course_completion_criteria as mcc', 'mcc.course', '=', 'mc.id')
            ->where('mc.category', '=', MdlCourseCategory::COURSE_LIBRALY[0])
            ->where('mc.deleted', '=', 0)
            ->where('mc.visible', '=', 1)
            ->whereNotIn('mc.id', $lstCourseTrainning)
            ->select('mc.id',
                'mc.fullname',
                'mc.shortname',
                'mc.category',
                'mc.summary',
                'mc.course_avatar',
                'mc.startdate',
                'mc.enddate',
                'mc.visible',
                'mcc.gradepass as pass_score');

        if (!(tvHasRoles(\Auth::user()->id, ["admin", "root"]) or slug_can('tms-system-administrator-grant'))) {
            $lstData = $lstData
                ->whereIn('mc.id', function ($q) { //organization
                    /* @var $q Builder */
                    $q->select('mdl_course.id')
                        ->from('tms_organization_employee')
                        ->join('tms_role_organization', 'tms_organization_employee.organization_id', '=', 'tms_role_organization.organization_id')
                        ->join('tms_role_course', 'tms_role_organization.role_id', '=', 'tms_role_course.role_id')
                        ->join('mdl_course', 'tms_role_course.course_id', '=', 'mdl_course.id')
                        ->where('tms_organization_employee.user_id', '=', \Auth::user()->id);
                });
        }


        if ($this->keyword) {
            $lstData = $lstData->where(function ($query) {
                $query->orWhere('shortname', 'like', "%{$this->keyword}%")
                    ->orWhere('fullname', 'like', "%{$this->keyword}%");
            });
        }

        $lstData = $lstData->orderBy('id', 'desc');

        $lstData = $lstData->paginate($row);
        $total = ceil($lstData->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstData->currentPage(),
            ],
            'data' => $lstData
        ];


        return response()->json($response);
    }

    //them khoa hoc vao khung nang luc
    public function apiAddCourseTrainning(Request $request)
    {
        $response = new ResponseModel();
        try {
            $trainning_id = $request->input('trainning_id');
            $trainning_code = $request->input('trainning_code');
            $lstCourseId = $request->input('lst_course');

            \DB::beginTransaction();
            $count_course = count($lstCourseId);
            if ($count_course > 0) {

                $existed_codes = MdlCourse::query()
                    ->select('id', 'shortname')
                    ->get();

                $index_existed = array();
                foreach ($existed_codes as $code) {
                    $extract = self::extractCode($code->shortname);
                    if (!empty($extract)) {
                        if (isset($index_existed[$extract['lib_code']])) {
                            if (intval($extract['number']) > intval($index_existed[$extract['lib_code']])) {
                                $index_existed[$extract['lib_code']] = $extract['number'];
                            }
                        } else {
                            $index_existed[$extract['lib_code']] = $extract['number'];
                        }
                    }
                }

                foreach ($lstCourseId as $item) {
                    $course_id = is_numeric($item['id']) ? $item['id'] : 0;
//                    $course_sample = DB::table('mdl_course')
//                        ->leftJoin('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
//                        ->where('mdl_course.id', '=', $course_id)
//                        ->select(
//                            'mdl_course.id',
//                            'mdl_course.fullname',
//                            'mdl_course.shortname',
//                            'mdl_course.category',
//                            'mdl_course.summary',
//                            'mdl_course.course_avatar',
//                            'mdl_course.startdate',
//                            'mdl_course.enddate',
//                            'mdl_course.visible',
//                            'mdl_course_completion_criteria.gradepass as pass_score'
//                        )->first();

                    $data_trainning = DB::table('tms_trainning_courses as ttc')
                        ->where('ttc.trainning_id', '=', $trainning_id)
                        ->where('ttc.sample_id', '=', $course_id)
                        ->where('ttc.deleted', '=', 1)
                        ->select('ttc.id', 'ttc.deleted', 'ttc.course_id')
                        ->first();

                    $current_max = TmsTrainningCourse::query()->where('trainning_id', $trainning_id)->max('order_no');
                    $next_max = is_integer($current_max) ? $current_max + 1 : 1;

                    if ($data_trainning) {
                        DB::table('tms_trainning_courses')
                            ->where('id', '=', $data_trainning->id)
                            ->update(['deleted' => 0, 'order_no' => $next_max]);
                        //Enable khóa học lại
                        $old_course_id = $data_trainning->course_id;
                        DB::table('mdl_course')
                            ->where('id', '=', $old_course_id)
                            ->update(['deleted' => 0]);
                    } else {
                        #region clone course tu thu vien khoa hoc
                        $course = new MdlCourse(); //khởi tạo theo cách này để tránh trường hợp insert startdate và endate bị set về 0
                        $course->category = MdlCourseCategory::COURSE_ONLINE[0];


                        $number = 0;
                        if (isset($index_existed[$item['shortname']])) {
                            $number = intval($index_existed[$item['shortname']]);
                        }
                        $next_number = $number + 1;
                        $append = self::composeAppend($next_number);
                        $prefix = '_ONL';
                        $course->shortname = $trainning_code . '_' . $item['shortname'] . $prefix . $append;


                        //$course->shortname = $course_sample->shortname . $course_sample->id . $trainning_id;


                        $course->fullname = $item['fullname']; // . ' ' . $course_sample->id . $trainning_id;  //bỏ id sau tên course
                        $course->summary = $item['summary'];
                        $course->course_avatar = $item['course_avatar'];

//                        $course->startdate = $course_sample->startdate;   //trungta bỏ ngày clone từ course sample 04/08/2020
//                        $course->enddate = $course_sample->enddate;

                        $course->visible = 1; //tao ra 1 khoa hoc moi, mac dinh set khoa hoc do dc active
                        $course->save();

                        $index_existed[$item['shortname']] = $append;

                        //insert dữ liệu điểm qua môn
                        MdlCourseCompletionCriteria::firstOrCreate(array(
                            'course' => $course->id,
                            'criteriatype' => 6, //default là 6 trong trường hợp này
                            'gradepass' => $item['pass_score']
                        ));


                        $context_cate = MdlContext::where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSECAT)
                            ->where('instanceid', '=', MdlCourseCategory::COURSE_ONLINE[0])->first();

                        if ($context_cate) {
                            //insert dữ liệu vào bảng mdl_context
                            $mdl_context = MdlContext::firstOrCreate([
                                'contextlevel' => \App\MdlUser::CONTEXT_COURSE,
                                'instanceid' => $course->id,
                                'depth' => 3,
                                'locked' => 0
                            ]);

                            //cập nhật path
                            $mdl_context->path = '/1/' . $context_cate->id . '/' . $mdl_context->id;
                            $mdl_context->save();
                        }

                        //write data to table mdl_grade_categories -> muc dich phuc vu cham diem, Vinh PT yeu cau
                        $mdl_grade_cate = MdlGradeCategory::firstOrCreate([
                            'courseid' => $course->id,
                            'depth' => 1,
                            'aggregation' => 13,
                            'aggregateonlygraded' => 1,
                            'timecreated' => strtotime(Carbon::now()),
                            'timemodified' => strtotime(Carbon::now())
                        ]);

                        $mdl_grade_cate->path = '/' . $mdl_grade_cate->id . '/';
                        $mdl_grade_cate->save();

                        //write data to table mdl_grade_items
                        MdlGradeItem::firstOrCreate([
                            'courseid' => $course->id,
                            'itemname' => $course->fullname,
                            'itemtype' => 'course',
                            'iteminstance' => $mdl_grade_cate->id,
                            'gradepass' => $item['pass_score'] ? $item['pass_score'] : 0
                        ]);

                        //insert dữ liệu vào bảng mdl_enrol, yêu cầu của VinhPT phục vụ mục đích đăng ký học của học viên bên LMS
                        $enrol = DB::table('mdl_enrol')
                            ->where('enrol', '=', 'manual')
                            ->where('courseid', '=', $course->id)
                            ->where('enrol', '=', 'self')
                            ->where('roleid', '=', 5)//quyền học viên
                            ->first();

                        if ($enrol) {
                            $enrol->status = 0;
                            $enrol->save();
                        } else {
                            MdlEnrol::create([
                                'enrol' => 'self',
                                'courseid' => $course->id,
                                'roleid' => 5,
                                'sortorder' => 0,
                                'status' => 0
                            ]);
                        }


                        //insert du lieu vao bang chua thong tin course trong khung nang luc
                        TmsTrainningCourse::firstOrCreate([
                            'trainning_id' => $trainning_id,
                            'sample_id' => $course_id, // khoa hoc vua tao duoc clone tu khoa hoc mau nao
                            'course_id' => $course->id,
                            'order_no' => $next_max
                        ]);


                        $user_id = Auth::id();
                        //Check role teacher and enrol for creator of course
                        $current_user_roles_and_slugs = checkRole();
                        //If user ís not a teacher, assign as teacher
                        $role_teacher = Role::select('id', 'name', 'mdl_role_id', 'status')->where('name', Role::TEACHER)->first();
                        if (!$current_user_roles_and_slugs['roles']->has_role_teacher) {
                            add_user_by_role($user_id, $role_teacher->id);
                            enrole_lms($user_id, $role_teacher->mdl_role_id, 1);
                        }
                        //Enrol user to newly created course as teacher
                        enrole_user_to_course_multiple(array($user_id), $role_teacher->mdl_role_id, $course->id, true);

                        //Add newly course to phân quyền dữ liệu
                        if (tvHasRoles(\Auth::user()->id, ["admin", "root"]) or slug_can('tms-system-administrator-grant')) {
                            //admin do nothing
                        } else {
                            $checkRoleOrg = tvHasOrganization(\Auth::user()->id);
                            if ($checkRoleOrg != 0) {
                                $org_role = TmsRoleOrganization::query()->where('organization_id', $checkRoleOrg)->first();
                                if (isset($org_role)) {
                                    $new_relation = new TmsRoleCourse();
                                    $new_relation->role_id = $org_role->role_id;
                                    $new_relation->course_id = $course->id;
                                    $new_relation->save();
                                }
                            }
                        }


                        devcpt_log_system('course', 'lms/course/view.php?id=' . $course->id, 'create', 'Create course: ' . $course->shortname);
                        updateLastModification('create', $course->id);

                        #endregion
                    }

                    sleep(0.01);
                }
            }
            \DB::commit();

            $response->status = true;
            $response->message = __('them_khoa_hoc_vao_knl_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    //them khoa hoc vao khung nang luc
    public function apiAddCourseToTraining(Request $request)
    {
        $response = new ResponseModel();
        try {
            $trainning_id = $request->input('trainning_id');
            $lstCourseId = $request->input('lst_course');

            \DB::beginTransaction();
            $count_course = count($lstCourseId);
            if ($count_course > 0) {
                foreach ($lstCourseId as $item) {


                    $data_trainning = DB::table('tms_trainning_courses as ttc')
                        ->where('ttc.trainning_id', '=', $trainning_id)
                        ->where('ttc.sample_id', '=', $item['id'])
                        ->where('ttc.deleted', '=', 1)
                        ->select('ttc.id', 'ttc.deleted', 'ttc.course_id')
                        ->first();

                    $current_max = TmsTrainningCourse::query()->where('trainning_id', $trainning_id)->max('order_no');
                    $next_max = is_integer($current_max) ? $current_max + 1 : 1;

                    if ($data_trainning) { //Nếu từng bị remove khỏi khung năng lực => mở lại
                        DB::table('tms_trainning_courses')
                            ->where('id', '=', $data_trainning->id)
                            ->update(['deleted' => 0, 'order_no' => $next_max]);
                    } else { //Nếu chưa từng thì add mới vào bảng quan hệ

                        //insert du lieu vao bang chua thong tin course trong khung nang luc
                        TmsTrainningCourse::firstOrCreate([
                            'trainning_id' => $trainning_id,
                            'sample_id' => $item['id'], // khoa hoc vua tao duoc clone tu khoa hoc mau nao
                            'course_id' => $item['id'],
                            'order_no' => $next_max
                        ]);

                        devcpt_log_system('course', 'lms/course/view.php?id=' . $item['id'], 'create', 'Create course: ' . $item['shortname']);
                        updateLastModification('create', $item['id']);
                        #endregion
                    }

                    usleep(50);
                }
            }
            \DB::commit();
            $response->status = true;
            $response->message = __('them_khoa_hoc_vao_knl_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }


    /**
     * @param $num
     * @return string
     */
    public function composeAppend($num)
    {
        $length = 3;
        if (strlen($num) >= $length) {
            return $num;
        } else {
            return str_repeat('0', $length - strlen($num)) . $num;
        }
    }

    /**
     * @param $code
     * @return array
     */
    public function extractCode($code)
    {
        $response = [];
        $prefix = '_ONL';
//        let arr = str.split('_ONL');
//              let reverse = arr.reverse();
//              if (isNaN(reverse[0])) {
//                  return '0';
//              } else {
//                  return reverse[0];
//              }
        if (strpos($code, $prefix) !== false) {
            $explode = explode($prefix, $code);
            $response = array(
                'lib_code' => $explode[0],
                'number' => $explode[1]
            );
        }
        return $response;
    }


    //xoa khoa hoc khoi khung nang luc
    public function apiRemoveCourseTrainning(Request $request)
    {
        $response = new ResponseModel();
        try {
            $trainning_id = $request->input('trainning_id');
            $lstCourseId = $request->input('lst_course');

            //\DB::beginTransaction();
            $count_course = count($lstCourseId);
            if ($count_course > 0) {
                foreach ($lstCourseId as $course_id) {
                    $course_id = is_numeric($course_id) ? $course_id : 0;

                    DB::table('tms_trainning_courses as ttc')
                        ->where('ttc.trainning_id', '=', $trainning_id)
                        ->where('ttc.course_id', '=', $course_id)
                        ->where('ttc.deleted', '=', 0)
                        ->update(['deleted' => 1]);

                    //Disable khoa hoc luon?
                    DB::table('mdl_course')
                        ->where('id', '=', $course_id)
                        ->update(['deleted' => 1]);

                    sleep(0.01);
                }
            }
            //\DB::commit();
            $response->status = true;
            $response->message = __('xoa_khoa_hoc_vao_knl_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function apiRemoveCourseFromTraining(Request $request)
    {
        $response = new ResponseModel();
        try {
            $trainning_id = $request->input('trainning_id');
            $lstCourseId = $request->input('lst_course');

            //\DB::beginTransaction();
            $count_course = count($lstCourseId);
            if ($count_course > 0) {
                foreach ($lstCourseId as $course_id) {
                    $course_id = is_numeric($course_id) ? $course_id : 0;

                    DB::table('tms_trainning_courses as ttc')
                        ->where('ttc.trainning_id', '=', $trainning_id)
                        ->where('ttc.course_id', '=', $course_id)
                        ->where('ttc.deleted', '=', 0)
                        ->update(['deleted' => 1]);

                    //Không disable khoa hoc vì không phải là khóa học dành riêng cho KNL
                    sleep(0.01);
                }
            }
            //\DB::commit();
            $response->status = true;
            $response->message = __('xoa_khoa_hoc_vao_knl_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }


    public function apiTrainningListUser(Request $request)
    {
        try {
            $keyword = $request->input('keyword');
            $row = $request->input('row');
            $trainning = $request->input('trainning');
            $organization_id = $request->input('organization_id');

            $param = [
                'keyword' => 'text',
                'row' => 'number',
                'trainning' => 'number'
            ];

            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }

            $data = DB::table('tms_traninning_users as ttu')
                ->join('mdl_user as mu', 'mu.id', '=', 'ttu.user_id')
                ->join('tms_user_detail as tud', 'mu.id', '=', 'tud.user_id')
                ->where('tud.deleted', '=', 0)
                ->select('ttu.id as id', 'mu.id as user_id', 'mu.username', 'tud.fullname', 'mu.email');

            if ($trainning != 0) {
                $data = $data->where('ttu.trainning_id', '=', $trainning);
            }

            if ($keyword) {
                $data = $data->whereRaw('(tud.fullname like "%' . $keyword . '%" OR mu.email like "%' . $keyword . '%" OR mu.username like "%' . $keyword . '%")');
            }
            if (strlen($organization_id) != 0 && $organization_id != 0) {
                //$query = $query->where('tms_organization.id', '=', $organization_id); commented 2020 06 25
                //đệ quy tổ chức con nếu có
                $data = $data->join('tms_organization_employee', 'mu.id', '=', 'tms_organization_employee.user_id');
                $data = $data->join('tms_organization', 'tms_organization_employee.organization_id', '=', 'tms_organization.id');
                $data = $data->whereIn('tms_organization.id', function ($q) use ($organization_id) {
                    $q->select('id')->from(DB::raw("
                            (select id from (select * from tms_organization) torg,
                            (select @pv := $organization_id) initialisation
                            where find_in_set(parent_id, @pv) and length(@pv := concat(@pv, ',', id))
                            UNION
                            select id from tms_organization where id = $organization_id) as merged"));
                });
            }

            $data = $data->orderBy('mu.id', 'desc');
            $data = $data->groupBy('mu.id');
            $data = $data->paginate($row);

            $total = ceil($data->total() / $row);
            $total_user = $data->total();
            $response = [
                'pagination' => [
                    'total' => $total,
                    'total_user' => $total_user,
                    'current_page' => $data->currentPage(),
                ],
                'data' => $data,
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function apiTrainningRemove(Request $request)
    {
        try {
            $id = $request->input('id');
            $user_id = $request->input('user_id');
            $trainning_id = $request->input('trainning_id');
            if (!$user_id || !is_numeric($user_id)) {
                return response()->json(status_message('error', __('dinh_dang_du_lieu_khong_hop_le')));
            }

            //lay danh sach course_id trong KNL
            $courses = TmsTrainningCourse::where('trainning_id', $trainning_id)->where('deleted', 0)->pluck('course_id');

            DB::beginTransaction();
            //remove enrol hoc vien khoi KNL
            //remove quyen student cua hoc vien
            foreach ($courses as $course) {
                remove_enrole_user_to_course($user_id, Role::ROLE_STUDENT, $course);
            }

            TmsTrainningUser::find($id)->delete();
            DB::commit();
            return response()->json(status_message('success', __('cap_nhat_khung_nang_luc_thanh_cong')));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function detail($id)
    {
        // TODO: Implement detail() method.
    }

    //lay danh sach user ko nam trong KNL
    public function apiGetUsersOutTranning(Request $request)
    {
        try {
            $keyword = $request->input('keyword');
            $row = $request->input('row');
            $trainning = $request->input('trainning');
            $organization_id = $request->input('organization_id');

            $param = [
                'keyword' => 'text',
                'row' => 'number',
                'trainning' => 'number',
                'organization_id' => 'number'
            ];

            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }

            $leftJoin = '(SELECT ttu.trainning_id,ttu.user_id
                        FROM tms_traninning_users ttu where ttu.trainning_id = ' . $trainning . ' group by ttu.user_id) as ttpu';

            $leftJoin = DB::raw($leftJoin);

            $data = DB::table('mdl_user as mu')
                ->leftJoin($leftJoin, 'ttpu.user_id', '=', 'mu.id')
                ->join('tms_user_detail as tud', 'mu.id', '=', 'tud.user_id')
                //->where('mu.active', '=', 0)
                ->where('tud.deleted', '=', 0)
                ->whereNull('ttpu.trainning_id')
                ->select('mu.id as user_id', 'mu.username', 'tud.fullname', 'mu.email');

            if (!is_null($organization_id) && $organization_id > 0) {
                $data = $data->join('tms_organization_employee as toe', 'toe.user_id', '=', 'mu.id');
                $data = $data->whereIn('toe.user_id', function ($q2) use ($organization_id) {
                    $q2->select('org_uid')->from(DB::raw("(select ttoe.organization_id, ttoe.user_id as org_uid
                            from (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe join tms_organization tor on tor.id = toe.organization_id order by tor.parent_id, toe.id) ttoe,
                            (select @pv := $organization_id) initialisation
                            where find_in_set(ttoe.parent_id, @pv) and length(@pv := concat(@pv, ',', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = $organization_id) as org_tp"));
                });
//                $data = $data->join('tms_organization_employee as toe', 'toe.user_id', '=', 'mu.id')->where('toe.organization_id', '=', $organization_id);
            }

            if ($keyword) {
                $data = $data->whereRaw('(tud.fullname like "%' . $keyword . '%" OR mu.email like "%' . $keyword . '%" OR mu.username like "%' . $keyword . '%")');
            }

            $data = $data->orderBy('mu.id', 'desc');
            $data = $data->groupBy('mu.id');
            $data = $data->paginate($row);

            $total = ceil($data->total() / $row);
            $total_user = $data->total();
            $response = [
                'pagination' => [
                    'total' => $total,
                    'total_user' => $total_user,
                    'current_page' => $data->currentPage(),
                ],
                'data' => $data,
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    /**
     *
     * @param $target
     * @param $receiver
     * @param $content
     * @param bool $encoded
     */
    public function insert_mail_notifications($target, $receiver, $content, $encoded = false)
    {
        $send_to = [];
        if (!empty($receiver)) {
            if (is_array($receiver)) {
                $send_to = $receiver;
            } elseif (is_int($receiver)) {
                $send_to[] = $receiver;
            }

            if (!empty($send_to)) {
                foreach ($send_to as $user_id) {
                    $element = array(
                        'type' => TmsNotification::MAIL,
                        'target' => $target,
                        'status_send' => 0,
                        'sendto' => $user_id,
                        'createdby' => 0,
                        'course_id' => 0,
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time()),
                        'content' => $encoded ? json_encode($content, JSON_UNESCAPED_UNICODE) : $content
                    );
                    $data[] = $element;
                }
                if (!empty($data)) {
                    TmsNotification::insert($data);
                }
            }
        }
    }

    //them nguoi dung vao KNL
    public function apiAddUserToTrainning(Request $request)
    {
        $response = new ResponseModel();
        try {
            $trainning_id = $request->input('trainning_id');
            $lstUserIDs = $request->input('Users');

            $param = [
                'trainning_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            //add user to competency framework
            DB::beginTransaction();
            $queryArray = [];
            $userArrayByTraining = [];

            $num = 0;
            $limit = 300;

            foreach ($lstUserIDs as $user_id) {
                $queryItem = [];
                $queryItem['trainning_id'] = $trainning_id;
                $queryItem['user_id'] = $user_id;
                $queryItem['created_at'] = Carbon::now();
                $queryItem['updated_at'] = Carbon::now();

                array_push($queryArray, $queryItem);
                $userArrayByTraining[] = $user_id;

                $num++;
                if ($num >= $limit) {
                    TmsTrainningUser::insert($queryArray);
                    $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $userArrayByTraining, $trainning_id);
                    $num = 0;
                    $queryArray = [];
                    $userArrayByTraining = [];
                }
            }

            TmsTrainningUser::insert($queryArray);
            $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $userArrayByTraining, $trainning_id);

            //lay danh sach course_id trong KNL
            $courses = TmsTrainningCourse::where('trainning_id', $trainning_id)->where('deleted', 0)->pluck('course_id');

            // enroll user to course in competency framework
            // do moodle chi hieu user duoc hoc khi duoc enroll voi quyen student or teacher
            // he thong dang set mac dinh user tao ra deu co quyen student

            foreach ($courses as $course) {
                enrole_user_to_course_multiple($lstUserIDs, Role::ROLE_STUDENT, $course, false);
                usleep(10);
            }

            DB::commit();
            $response->status = true;
            $response->message = __('thao_tac_thanh_cong');
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    //them nguoi dung vao KNL
    public function apiAddUserOrganiToTrainning(Request $request)
    {
        $response = new ResponseModel();
        try {
            $trainning_id = $request->input('trainning_id');
            $org_id = $request->input('org_id');

            $param = [
                'trainning_id' => 'number',
                'org_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return json_encode($response);
            }

            //lay danh sach user nam trong co cau to chuc va ko nam trong KNL
            $tblQuery = '(select  ttoe.organization_id, ttoe.user_id
                                    from (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                     join tms_user_detail tud on tud.user_id = toe.user_id
                                     join tms_organization tor on tor.id = toe.organization_id
                                     where tud.deleted = 0
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $org_id . ') initialisation
                                    where   find_in_set(ttoe.parent_id, @pv)
                                    and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                                    UNION
                                    select   toe.organization_id,toe.user_id from tms_organization_employee toe
                                    join tms_user_detail tud on tud.user_id = toe.user_id
                                    where tud.deleted = 0 and toe.organization_id = ' . $org_id . ') as org_us';

            $tblQuery = DB::raw($tblQuery);

            $leftJoin = '(select user_id, trainning_id from tms_traninning_users where trainning_id = ' . $trainning_id . ') ttu';
            $leftJoin = DB::raw($leftJoin);

            $lstUserIDs = DB::table($tblQuery)->leftJoin($leftJoin, 'ttu.user_id', '=', 'org_us.user_id')
                ->whereNull('ttu.trainning_id')
                ->pluck('org_us.user_id')->toArray();

//            if (count($lstUserIDs) >= Config::get('constants.domain.LIMIT_SUBMIT')) {
//                $response->status = false;
//                $response->message = __('qua_so_luong_submit');
//                return response()->json($response);
//            }


            //add user to competency framework
            if (count($lstUserIDs) > 0) {
                DB::beginTransaction();
                $queryArray = [];
                $num = 0;
                $limit = 300;
                $userArrayByTraining = [];

                foreach ($lstUserIDs as $user_id) {
                    $queryItem = [];
                    $queryItem['trainning_id'] = $trainning_id;
                    $queryItem['user_id'] = $user_id;
                    $queryItem['created_at'] = Carbon::now();
                    $queryItem['updated_at'] = Carbon::now();

                    array_push($queryArray, $queryItem);
                    $userArrayByTraining[] = $user_id;

                    $num++;
                    if ($num >= $limit) {
                        TmsTrainningUser::insert($queryArray);
                        $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $userArrayByTraining, $trainning_id);

                        $num = 0;
                        $queryArray = [];
                        $userArrayByTraining = [];

                    }
                }
                TmsTrainningUser::insert($queryArray);
                $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $userArrayByTraining, $trainning_id);

                //cap nhat flag, chay cron
                updateFlagCron(Config::get('constants.domain.ENROLL_USER'), Config::get('constants.domain.ACTION_UPDATE_FLAG'),
                    Config::get('constants.domain.START_CRON'));

                //lay danh sach course_id trong KNL
//                $courses = TmsTrainningCourse::where('trainning_id', $trainning_id)->where('deleted', 0)->pluck('course_id');

                // enroll user to course in competency framework
                // do moodle chi hieu user duoc hoc khi duoc enroll voi quyen student or teacher
                // he thong dang set mac dinh user tao ra deu co quyen student

//                foreach ($courses as $course) {
//                    enrole_user_to_course_multiple($lstUserIDs, Role::ROLE_STUDENT, $course, true);
//                    usleep(10);
//                }

                DB::commit();
            }

            $response->status = true;
            $response->message = __('thao_tac_thanh_cong');
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function removeMultiUser(Request $request)
    {
        // TODO: Implement removeMultiUser() method.
        try {
            $lstUser = $request->input('Users');
            $trainning_id = $request->input('trainning_id');

            //lay danh sach course_id trong KNL
            $courses = TmsTrainningCourse::where('trainning_id', $trainning_id)->where('deleted', 0)->pluck('course_id');

            DB::beginTransaction();
            //remove enrol hoc vien khoi KNL
            //remove quyen student cua hoc vien
            foreach ($courses as $course) {
                foreach ($lstUser as $user_id) {
                    remove_enrole_user_to_course($user_id, Role::ROLE_STUDENT, $course);
                }
            }

            TmsTrainningUser::where('trainning_id', $trainning_id)->whereIn('user_id', $lstUser)->delete();

            DB::commit();
            return response()->json(status_message('success', __('cap_nhat_khung_nang_luc_thanh_cong')));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }
}
