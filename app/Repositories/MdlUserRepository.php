<?php


namespace App\Repositories;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MdlUserRepository implements IMdlUserInterface, ICommonInterface
{

    public function getall(Request $request)
    {
        // TODO: Implement getall() method.
    }

    public function store(Request $request)
    {
        // TODO: Implement store() method.
    }

    public function update(Request $request)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function detail($id)
    {
        // TODO: Implement detail() method.
    }

    public function getTrainningUser(Request $request)
    {
        // TODO: Implement getTrainningUser() method.

        $user_id = $request->input('user_id');

        $param = [
            'user_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstData = DB::table('tms_traninning_users as ttu')
            ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttu.trainning_id')
            ->where('ttp.deleted', '=', 0)
            ->where('ttu.user_id', '=', $user_id)
            ->select('ttp.id', 'ttp.name')->groupBy('ttp.id')->get();

        return response()->json($lstData);
    }
}
