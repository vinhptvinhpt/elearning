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
use App\TmsTrainningCategory;
use App\TmsTrainningCourse;
use App\TmsTrainningGroup;
use App\TmsTrainningProgram;
use App\TmsTrainningUser;
use App\TmsUserDetail;
use App\ViewModel\ResponseModel;
use Carbon\Carbon;
use Horde\Socket\Client\Exception;
use Illuminate\Http\Request;
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
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
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
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            \DB::beginTransaction();
            $trainning = TmsTrainningProgram::findOrFail($id);

            $trainningInfo = TmsTrainningProgram::select('id')->whereNotIn('id', [$trainning->id])->where('code', $code)->first();
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
            ->select('ttp.id', 'ttp.code', 'ttp.name', DB::raw('count(ttu.id) as total_user'))
            ->where('ttp.deleted', '=', 0)
            ->where('ttp.deleted', '!=', 2);//cac KNL tu dong sinh ra khi tao moi khoa hoc online, tap trung;

        if ($this->keyword) {
            $lstData = $lstData->where(function ($query) {
                $query->orWhere('ttp.code', 'like', "%{$this->keyword}%")
                    ->orWhere('ttp.name', 'like', "%{$this->keyword}%");
            });
        }

        if ($style) {
            $lstData = $lstData->where('.ttp.style', 1);
        } else {
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
            ->where('deleted', '!=', 2)//cac KNL tu dong sinh ra khi tao moi khoa hoc online, tap trung
            ->orderBy('id', 'desc')->get();
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
            ->select('mc.id', 'mc.fullname', 'mc.shortname', 'ttc.sample_id');

        if ($this->keyword) {
            $lstData = $lstData->where(function ($query) {
                $query->orWhere('mc.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('mc.shortname', 'like', "%{$this->keyword}%");
            });
        }

        $lstData = $lstData->orderBy('mc.id', 'desc');

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

        $lstData = MdlCourse::select('id', 'shortname', 'fullname')->where('category', '=', MdlCourseCategory::COURSE_LIBRALY[0])
            ->where('deleted', '=', 0)
            ->whereNotIn('id', $lstCourseTrainning);


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
            $lstCourseId = $request->input('lst_course');

            \DB::beginTransaction();
            $count_course = count($lstCourseId);
            if ($count_course > 0) {
                foreach ($lstCourseId as $course_id) {
                    $course_id = is_numeric($course_id) ? $course_id : 0;

                    $course_sample = DB::table('mdl_course')
                        ->leftJoin('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
                        ->where('mdl_course.id', '=', $course_id)
                        ->select(
                            'mdl_course.id',
                            'mdl_course.fullname',
                            'mdl_course.shortname',
                            'mdl_course.category',
                            'mdl_course.summary',
                            'mdl_course.course_avatar',
                            'mdl_course.startdate',
                            'mdl_course.enddate',
                            'mdl_course.visible',
                            'mdl_course_completion_criteria.gradepass as pass_score'
                        )->first();

                    $data_trainning = DB::table('tms_trainning_courses as ttc')
                        ->where('ttc.trainning_id', '=', $trainning_id)
                        ->where('ttc.sample_id', '=', $course_id)
                        ->where('ttc.deleted', '=', 1)->select('ttc.id', 'ttc.deleted')->first();

                    if ($data_trainning) {
                        DB::table('tms_trainning_courses as ttc')
                            ->where('ttc.trainning_id', '=', $trainning_id)
                            ->where('ttc.sample_id', '=', $course_id)
                            ->where('ttc.deleted', '=', 1)->update(['deleted' => 0]);
                    } else if ($course_sample) {
                        #region clone course tu thu vien khoa hoc
                        $course = new MdlCourse(); //khởi tạo theo cách này để tránh trường hợp insert startdate và endate bị set về 0
                        $course->category = MdlCourseCategory::COURSE_ONLINE[0];
                        $course->shortname = $course_sample->shortname . $course_sample->id . $trainning_id;
                        $course->fullname = $course_sample->fullname . ' ' . $course_sample->id . $trainning_id;
                        $course->summary = $course_sample->summary;
                        $course->course_avatar = $course_sample->course_avatar;

                        $course->startdate = $course_sample->startdate;
                        $course->enddate = $course_sample->enddate;

                        $course->visible = 1; //tao ra 1 khoa hoc moi, mac dinh set khoa hoc do dc active

                        $course->save();

                        //insert dữ liệu điểm qua môn
                        MdlCourseCompletionCriteria::firstOrCreate(array(
                            'course' => $course->id,
                            'criteriatype' => 6, //default là 6 trong trường hợp này
                            'gradepass' => $course_sample->pass_score
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
                            'gradepass' => $course_sample->pass_score ? $course_sample->pass_score : 0
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
                            'course_id' => $course->id
                        ]);

                        //write log to mdl_logstore_standard_log

                        $app_name = Config::get('constants.domain.APP_NAME');

                        $key_app = encrypt_key($app_name);


                        $dataLog = array(
                            'app_key' => $key_app,
                            'courseid' => $course->id,
                            'action' => 'create',
                            'description' => json_encode($course),
                        );

                        $dataLog = createJWT($dataLog, 'data');

                        $data_write = array(
                            'data' => $dataLog,
                        );

                        $url = Config::get('constants.domain.LMS') . '/course/write_log.php';

                        //call api write log
                        callAPI('POST', $url, $data_write, false, '');
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
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
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
                        ->where('ttc.deleted', '=', 0)->update(['deleted' => 1]);

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
                //->where('mu.active', '=', 0)
                ->select('ttu.id as id', 'mu.id as user_id', 'mu.username', 'tud.fullname', 'mu.email');

            if ($trainning != 0) {
                $data = $data->where('ttu.trainning_id', '=', $trainning);
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

            $param = [
                'keyword' => 'text',
                'row' => 'number',
                'trainning' => 'number'
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
                ->where('mu.active', '=', 0)
                ->whereNull('ttpu.trainning_id')
                ->select('mu.id as user_id', 'mu.username', 'tud.fullname', 'mu.email');

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
            $num = 0;
            $limit = 300;

            foreach ($lstUserIDs as $user_id) {
                $queryItem = [];
                $queryItem['trainning_id'] = $trainning_id;
                $queryItem['user_id'] = $user_id;
                array_push($queryArray, $queryItem);
                $num++;
                if ($num >= $limit) {
                    TmsTrainningUser::insert($queryArray);
                    $num = 0;
                    $queryArray = [];
                }
            }
            TmsTrainningUser::insert($queryArray);

            //lay danh sach course_id trong KNL
            $courses = TmsTrainningCourse::where('trainning_id', $trainning_id)->where('deleted', 0)->pluck('course_id');

            // enroll user to course in competency framework
            // do moodle chi hieu user duoc hoc khi duoc enroll voi quyen student or teacher
            // he thong dang set mac dinh user tao ra deu co quyen student

            foreach ($courses as $course) {
                enrole_user_to_course_multiple($lstUserIDs, Role::ROLE_STUDENT, $course, true);
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
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            //lay danh sach user nam trong co cau to chuc va ko nam trong KNL
            $tblQuery = '(select  ttoe.organization_id, ttoe.user_id
                                    from (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                     join tms_organization tor on tor.id = toe.organization_id
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $org_id . ') initialisation
                                    where   find_in_set(ttoe.parent_id, @pv)
                                    and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                                    UNION
                                    select   toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $org_id . ') as org_us';

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

                foreach ($lstUserIDs as $user_id) {
                    $queryItem = [];
                    $queryItem['trainning_id'] = $trainning_id;
                    $queryItem['user_id'] = $user_id;
                    array_push($queryArray, $queryItem);
                    $num++;
                    if ($num >= $limit) {
                        TmsTrainningUser::insert($queryArray);
                        $num = 0;
                        $queryArray = [];
                    }
                }
                TmsTrainningUser::insert($queryArray);

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
}
