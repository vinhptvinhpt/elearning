<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\TmsDevice;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        return TmsDevice::all(['user_id', 'imei', 'token', 'type', 'is_active']);
    }

    public function show($id)
    {
        return TmsDevice::where("id", $id)->first();
    }

    public function store(Request $request)
    {
        $article = TmsDevice::create($request->all());
        return response()->json($article, 201);
    }

    public function update(Request $request, $id)
    {
        $article = TmsDevice::findOrFail($id, ['user_id', 'imei', 'token', 'type', 'is_active']);
        $article->update($request->all());
        return response()->json($article, 200);
    }

    public function delete(Request $request, $id)
    {
        $article = TmsDevice::findOrFail($id);
        $article->delete();
        return response()->json(null, 204);
    }
}
