<?php


namespace App\Repositories;


use App\MdlContext;
use App\MdlCourse;
use App\MdlCourseCategory;
use App\MdlCourseCompletionCriteria;
use App\MdlEnrol;
use App\MdlGradeCategory;
use App\MdlGradeItem;
use App\TmsTrainningCategory;
use App\TmsTrainningCourse;
use App\TmsTrainningProgram;
use App\ViewModel\ResponseModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TrainningRepository implements ITranningInterface,ICommonInterface
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

            $param = [
                'code' => 'code',
                'name' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            \DB::beginTransaction();
            //check course info exist
            $trainningInfo = TmsTrainningProgram::select('id')->where('code', $code)->first();

            if ($trainningInfo) {
                $response->status = false;
                $response->message = __('ma_trainning_da_ton_tai');
                return response()->json($response);
            }

            $tms_trainning = TmsTrainningProgram::firstOrCreate([
                'code' => $code,
                'name' => $name
            ]);

            \DB::commit();
            $response->status = true;
            $response->otherData = $tms_trainning->id;
            $response->message = __('them_moi_khung_nang_luc_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    public function update($id)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function apiGetListTrainning(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');

        $param = [
            'keyword' => 'text',
            'row' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = TmsTrainningProgram::select('id', 'code', 'name')->where('deleted', '=', 0);

        if ($this->keyword) {
            $lstData = $lstData->where(function ($query) {
                $query->orWhere('code', 'like', "%{$this->keyword}%")
                    ->orWhere('name', 'like', "%{$this->keyword}%");
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

    public function apiGetDetailTrainning($id)
    {
        $id = is_numeric($id) ? $id : 0;
        $trainning = DB::table('tms_traninning_programs as ttp')
            //->join('tms_trainning_categories as ttc', 'ttc.trainning_id', '=', 'ttp.id')
            ->where('ttp.id', '=', $id)
            ->select('ttp.id', 'ttp.code', 'ttp.name')
            ->first();

        return response()->json($trainning);
    }

    //lay danh sach khoa hoc mau da co trong khung nang luc
    public function apiGetCourseSampleTrainning(Request $request)
    {
        $this->keyword  = $request->input('keyword');
        $row            = $request->input('row');
        $trainning_id   = $request->input('trainning_id');

        $param = [
            'keyword'       => 'text',
            'row'           => 'number',
            'trainning_id'  => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = DB::table('tms_trainning_courses as ttc')
            ->join('mdl_course as mc','mc.id','=','ttc.course_id')
            ->where('ttc.trainning_id', '=', $trainning_id)
            ->where('ttc.deleted', '=', 0)
            ->select('mc.id', 'mc.fullname', 'mc.shortname');

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
            ->select('c.id')->groupBy('ttc.sample_id')->pluck('c.id');

        $lstData = MdlCourse::select('id', 'shortname', 'fullname')->where('category', '=', MdlCourseCategory::COURSE_LIBRALY[0])
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
            $trainning_id   = $request->input('trainning_id');
            $lstCourseId    = $request->input('lst_course');

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

                        $course->visible = 0;

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
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    //xoa khoa hoc khoi khung nang luc
    public function apiRemoveCourseTrainning(Request $request)
    {
        $response = new ResponseModel();
        try {
            $trainning_id   = $request->input('trainning_id');
            $lstCourseId    = $request->input('lst_course');

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
            $response->message = $e->getMessage();
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

            $data = DB::table('tms_user_detail as tud')
                ->select(
                    'tud.fullname',
                    'tud.email',
                    'tud.cmtnd',
                    'mu.username',
                    'mu.id',
                    'ttp.name as trainning_name',
                    'ttp.id as trainning_id'
                )
                ->join('mdl_user as mu', 'mu.id', '=', 'tud.user_id')
                ->join('tms_traninning_users as ttu', 'ttu.user_id', '=', 'mu.id')
                ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttu.trainning_id')
                ->where('tud.deleted', '=', 0);

            if ($trainning != 0) {
                $data = $data->where('ttp.id', '=', $trainning);
            }

            if ($keyword) {
                $data = $data->where(function ($query) use ($keyword) {
                    $query->orWhere('tud.fullname', 'like', "%{$keyword}%")
                        ->orWhere('tud.email', 'like', "%{$keyword}%")
                        ->orWhere('tud.cmtnd', 'like', "%{$keyword}%")
                        ->orWhere('mu.username', 'like', "%{$keyword}%");
                });
            }
            $data = $data->orderBy('tud.created_at', 'desc');
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

}
