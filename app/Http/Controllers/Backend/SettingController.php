<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\TmsConfigs;
use App\TmsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        return view('setting.index');
    }

    public function apiListSetting()
    {
        //check and insert development in db tms_configs
        $data = [];
        TmsConfigs::initConfigs(TmsConfigs::TYPE_SYSTEM);
        $configs = TmsConfigs::initConfigs(TmsConfigs::TYPE_SYSTEM);
        if (count($configs) != 0) {
            foreach ($configs as $config) {
                $label = TmsConfigs::getAttrLabel($config->target);
                $config->label = $label;
                $data[] = $config;
            }
        }
        return response()->json($data);
    }

    public function apiUpdateSetting(Request $request)
    {
        $updates = $request->all();

        $validate_source = [];
        $stored_configs = TmsConfigs::all();
        foreach ($stored_configs as $config) {
            if ($config->target == TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY) {
                $validate_source[$config->target] = 'token';
            } else {
                if ($config->editor == TmsConfigs::EDITOR_TEXT || $config->editor == TmsConfigs::EDITOR_CHECKBOX) {
                    $validate_source[$config->target] = 'text';
                } elseif ($config->editor == TmsConfigs::EDITOR_TEXTAREA) {
                    $validate_source[$config->target] = 'longtext';
                }
            }
        }

        $validates = validate_fails($request, $validate_source);
        if (!empty($validates)) {
            $error_string_arr = [];
            foreach ($validates as $validate) {
                $error_string_arr[] = TmsConfigs::getAttrLabel($validate);
            }
            $error_string = implode(', ', $error_string_arr);
            return $error_string;
            //var_dump($validates);
        } else {
            if (count($updates) != 0) {
                $mail_dev_mode = true; //Auto turn on
                DB::beginTransaction();
                try {
                    foreach ($updates as $key => $val) {
                        //$setting =
                        DB::table('tms_configs')->where('target', $key)->update(array('content' => $val));
                        //$setting trả về số lượng bản ghi được update, nếu không thay đổi giá trị thì update sẽ trả về 0
                    }
                } catch (\Exception $e) {
                    //dd($e->getMessage());
                    DB::rollBack();
                    return 'fail';
                }
                DB::commit();
                if ($updates[TmsConfigs::DEVELOPMENT] == 'disable') {
                    $mail_dev_mode = false;
                }
                Cache::put('mail_development_mode', $mail_dev_mode, 1440);
            }
            return 'success';
        }
    }
}
