<?php


namespace App\Repositories;


use App\MdlCourse;
use App\TmsTdCompetency;
use App\TmsTdCompetencyCourse;
use App\TmsTdCompetencyMark;
use App\TmsTrainningProgram;
use App\ViewModel\ResponseModel;
use Illuminate\Http\Request;

class TNDReportRepository implements ITNDReportInteface, ICommonInterface
{

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

    public function detail($id)
    {
        // TODO: Implement detail() method.
        $competency = TmsTdCompetency::findOrFail($id);
        return response()->json($competency);
    }
}
