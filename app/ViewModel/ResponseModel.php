<?php

namespace App\ViewModel;

//viewmodel - trả dữ liệu từ api -> view
//ThoLD (21/08/2019)
class ResponseModel
{
    public $status;
    public $message;
    public $error;
    public $otherData;
    public $existCertificate;
    public $existBadge;
    public $survey;
}
