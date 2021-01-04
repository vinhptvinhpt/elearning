<?php


namespace App\Repositories;


use App\TmsCourseInfrastructure;
use App\ViewModel\ResponseModel;
use Illuminate\Http\Request;

class TmsCourseInfrastructureRepository implements ICommonInterface
{

    /**
     * List infrastructures with pagination
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getall(Request $request)
    {
        // TODO: Implement getall() method.
        $keyword = $request->input('keyword');
        $course_id = $request->input('course_id');
        $row = $request->input('row');

        $param = [
            'keyword' => 'text',
            'course_id' => 'number',
            'row' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = TmsCourseInfrastructure::where('course_id', $course_id);


        if ($keyword) {
            $lstData = $lstData->where(function($query) use ($keyword){
                $query->where('infra_name', 'like', '%' . $keyword . '%');
                $query->orWhere('infra_number', $keyword);
            });
        }

        $lstData = $lstData->orderBy('id', 'desc');
        $totalData = count($lstData->get());

        $lstData = $lstData->paginate($row);
        $total = ceil($lstData->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstData->currentPage(),
            ],
            'data' => $lstData,
            'total_course' => $totalData
        ];


        return response()->json($response);
    }

    /**
     * Create infrastructure
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // TODO: Implement store() method.
        $response = new ResponseModel();
        try {
            $course_id = $request->input('course_id');
            $infra_name = $request->input('infra_name');
            $infra_number = $request->input('infra_number');

            $param = [
                'infra_name' => 'text',
                'course_id' => 'number',
                'infra_number' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = 'Định dạng dữ liệu không hợp lệ';
                return response()->json($response);
            }

            TmsCourseInfrastructure::firstOrCreate([
                'course_id' => $course_id,
                'infra_name' => $infra_name,
                'infra_number' => $infra_number
            ]);

            $response->status = true;
            $response->message = __('tao_moi_csvc_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    /**
     * Update infrastructure
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // TODO: Implement update() method.
        $response = new ResponseModel();
        try {
            $id = $request->input('id');
            $course_id = $request->input('course_id');
            $infra_name = $request->input('infra_name');
            $infra_number = $request->input('infra_number');

            $param = [
                'infra_name' => 'text',
                'course_id' => 'number',
                'infra_number' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = 'Định dạng dữ liệu không hợp lệ';
                return response()->json($response);
            }

            $tms_infra = TmsCourseInfrastructure::findOrFail($id);

            if (!$tms_infra) {
                $response->status = false;
                $response->message = 'Định dạng dữ liệu không hợp lệ';
                return response()->json($response);
            }

            $tms_infra->course_id = $course_id;
            $tms_infra->infra_name = $infra_name;
            $tms_infra->infra_number = $infra_number;
            $tms_infra->save();

            $response->status = true;
            $response->message = __('capnhat_csvc_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    /**
     * Delete infrastructure
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
        $response = new ResponseModel();
        try {

            $tms_infra = TmsCourseInfrastructure::findOrFail($id);

            if (!$tms_infra) {
                $response->status = false;
                $response->message = 'Định dạng dữ liệu không hợp lệ';
                return response()->json($response);
            }

            $tms_infra->delete();

            $response->status = true;
            $response->message = __('xoa_csvc_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    /**
     * Detail infrastructure
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        // TODO: Implement detail() method.
        $tms_infra = TmsCourseInfrastructure::findOrFail($id);
        return response()->json($tms_infra);
    }
}
