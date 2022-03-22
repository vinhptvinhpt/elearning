<?php

namespace App\Http\Controllers\Api;

use App\TmsDevice;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        $msg = "Test notify from Elearning System: " . time();

        $android_device_tokens = array();
        $ios_device_tokens = array();

        $devices = TmsDevice::all();
        foreach ($devices as $item) {
            if (strlen($item->token) != 0) {
                if ($item->type == TmsDevice::TYPE_ANDROID) {
                    $android_device_tokens[] = $item->token;
                }
                if ($item->type == TmsDevice::TYPE_IOS) {
                    $ios_device_tokens[] = $item->token;
                }
            }
        }

        $params = [
            'title' => 'Title sample ' . time(),
            'link' => 'http://bgt.tinhvan.com'
        ];

        $send = sendPushNotification($msg, 'android', $android_device_tokens, $params, true);
        $send2 = sendPushNotification($msg, 'ios', $ios_device_tokens, $params, true);
        //var_dump($send);
    }
}
