<?php

return [
    "domain" => [
        'APP_NAME' => 'ELEARNING', //Không sửa đoạn text này khi deploy lên hệ thống mới, key mã hóa bảo mật hệ thống

        'TMS' => 'https://academy.phh-group.com/',

        'TMS-LOCAL' => 'http://localhost:8000/',

        'LMS' => 'https://academy.phh-group.com/lms',

        'DIVA' => 'https://dev-op.quanlydiembanhang.com/elearning-api/v1/', //domain run dev, thời gian đầu test trên domain này, khi nào launch hệ thống chuyển sang domain live

        'DIVA-LIVE' => 'https://qldbh.bgt.com.vn/elearning-api/v1/',

        'DIVA-TOKEN-SYSTEM' => 'U7x8oakUxoL4920zJuskO9-Ikksh4292J',//token hệ thống do DIVA cung cap,

        'EDITING-TEACHER-ID' => 3,//editingteacher id

        'EMAIL-DEFAULT' => 'nohasmail@gmail.com', //editingteacher id,

        'LIMIT_SUBMIT' => 100, //giới hạn số bản ghi submit 1 lần lên db, đang dùng cho chức add user vào KNL và enroll vào tất cả các course trong KNL,

        //contants update flag run cron 1 min/time => ko sua
        'ACTION_READ_FLAG' => 'read',

        'ACTION_UPDATE_FLAG' => 'write',

        'START_CRON' => 'start',

        'STOP_CRON' => 'stop',

        // danh sach file chay cron, ko sua ten o day
        'ENROLL_TRAINNING' => 'enroll_trainning.json', //add hoc vien vao KNL

        'ENROLL_USER' => 'enroll_user.json', //enroll hoc vien vao KNL

        //azure blob storage account info
        'ACCOUNT_NAME' => 'elearningdata',

        'CONTAINER_NAME' => 'asset-f8418a8e-bf70-44d8-bba0-b4c3144d7dd6',

        'ACCOUNT_KEY' => 'GRC03bagorlSpRO94e40uAuM/4o+xpw5pC/g3FMYy1u9fPDtmyybjPd4m74x0Pabc8wPmCte90f/rwYV+7nJqw==',


        'TMS_NAME' => 'PHH Academy',

        'APP-TOKEN-SYSTEM' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJUVkUtVFZDIn0.8d61LFSiugnJp72Vdd3BqFCuOsMS8up7kif5bMQvYjM',

        'HISTAFF-API' => 'http://adminhrm.phh-group.com/SynData.asmx/',

        'HISTAFF-KEY' => 'G6rikv0U0eQUIRak7owtmw==',

        'HISTAFF-COOKIE' => 'hrm_token',

        'DOMAIN-COOKIE' => 'academy.phh-group.com',

        'EXCEPTION-DOMAIN' => ['localhost', 'dev', 'el','phh-group'],
		
		'HISTAFF-INTEGRATION-ENABLED' => false, // Flag to enable/disable histaff integration

    ],
];
