<?php

namespace App\Repositories;

use App\TmsTdCompetency;
use App\TmsTdCompetencyCourse;
use App\TmsTdCompetencyMark;
use App\ViewModel\ResponseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TNDReportRepository implements ITNDReportInteface, ICommonInterface
{
    /**
     * List all
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getall(Request $request)
    {
        // TODO: Implement getall() method.
        $keyword = $request->input('keyword');
        $row = $request->input('row');

        $lstData = TmsTdCompetency::select('id', 'code', 'name');

        if ($keyword) {
            $lstData = $lstData->where(function ($query) use ($keyword) {
                $query->where('code', 'like', '%' . $keyword . '%')
                    ->orWhere('name', 'like', "%{$keyword}%");
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

    /**
     * Create
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // TODO: Implement store() method.
        $response = new ResponseModel();
        try {

            $code = $request->input('code');
            $name = $request->input('name');
            $description = $request->input('description');

            $param = [
                'code' => 'code',
                'name' => 'text'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $compeCheck = TmsTdCompetency::select('id')->where('code', $request->input('code'))->first();

            if ($compeCheck) {
                $response->status = false;
                $response->message = __('ma_trainning_da_ton_tai');
                return response()->json($response);
            }

            $competency = TmsTdCompetency::firstOrCreate(
                [
                    'code' => $code
                ],
                [
                    'name' => $name,
                    'description' => $description
                ]);

            $response->status = true;
            $response->message = __('thao_tac_thanh_cong');
            $response->otherData = $competency->id;
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = __('thao_tac_khong_thanh_cong');
        }
        return response()->json($response);
    }

    /**
     * Update
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // TODO: Implement update() method.
        $response = new ResponseModel();

        try {
            $id = $request->input('id');
            $code = $request->input('code');
            $name = $request->input('name');
            $description = $request->input('description');

            $param = [
                'id' => 'number',
                'code' => 'code',
                'name' => 'text'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $compeCheck = TmsTdCompetency::select('id')->whereNotIn('id', [$id])->where('code', $code)->first();
            if ($compeCheck) {
                $response->status = false;
                $response->message = __('ma_trainning_da_ton_tai');
                return response()->json($response);
            }

            $competency = TmsTdCompetency::findOrFail($id);
            $competency->code = $code;
            $competency->name = $name;
            $competency->description = $description;
            $competency->save();

            $response->status = true;
            $response->message = __('thao_tac_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = __('thao_tac_khong_thanh_cong');
        }
        return response()->json($response);
    }

    /**
     * Delete
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
        $response = new ResponseModel();
        try {

            TmsTdCompetencyCourse::where('competency_id', $id)->delete();
            TmsTdCompetencyMark::where('competency_id', $id)->delete();
            TmsTdCompetency::where('id', $id)->delete();

            $response->status = true;
            $response->message = __('thao_tac_thanh_cong');
        } catch (\Exception $e) {
            \Log::info($e);
            $response->status = false;
            $response->message = __('thao_tac_khong_thanh_cong');
        }
        return response()->json($response);
    }

    /**
     * View detail
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        // TODO: Implement detail() method.
        $competency = TmsTdCompetency::findOrFail($id);
        return response()->json($competency);
    }

    /**
     * List children course
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompetencyCourse(Request $request)
    {
        // TODO: Implement getCompetencyCourse() method.
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
            ->leftJoin('tms_td_competency_courses as ttc', function ($join) use ($training_id) {
                $join->on('c.id', '=', 'ttc.course_id');
                $join->where('ttc.competency_id', $training_id);
            })
            ->select(
                'c.id',
                'c.fullname',
                'c.shortname'
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
                $listCourses = $listCourses->orderBy('ttc.id', 'desc');
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

    /**
     * Assign children courses
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignCourseCompetency(Request $request)
    {
        // TODO: Implement assignCourseCompetency() method.
        $response = new ResponseModel();
        try {
            $trainning_id = $request->input('trainning_id');
            $lstCourseId = $request->input('lst_course');

            \DB::beginTransaction();
            $count_course = count($lstCourseId);
            if ($count_course > 0) {
                foreach ($lstCourseId as $item) {
                    //insert du lieu vao bang chua thong tin course trong khung nang luc
                    TmsTdCompetencyCourse::firstOrCreate([
                        'competency_id' => $trainning_id,
                        'course_id' => $item['id'],
                    ]);

                    devcpt_log_system('course', 'lms/course/view.php?id=' . $item['id'], 'create', 'Create course: ' . $item['shortname']);
                    updateLastModification('create', $item['id']);
                    #endregion

                    usleep(50);
                }
            }
            \DB::commit();
            $response->status = true;
            $response->message = __('them_khoa_hoc_vao_knl_thanh_cong');
        } catch (\Exception $e) {
            Log::info($e);
            \DB::rollBack();
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    /**
     * Remove children courses
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeAssignCourseCompetency(Request $request)
    {
        // TODO: Implement removeAssignCourseCompetency() method.
        $response = new ResponseModel();
        try {
            $trainning_id = $request->input('trainning_id');
            $lstCourseId = $request->input('lst_course');

            TmsTdCompetencyCourse::whereIn('course_id', $lstCourseId)->where('competency_id', $trainning_id)->delete();

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
}
