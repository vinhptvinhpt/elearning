<?php

return [
    "domain" => [
        'APP_NAME' => 'ELEARNING', //Không sửa đoạn text này khi deploy lên hệ thống mới, key mã hóa bảo mật hệ thống

        'TMS' => 'http://localhost:8000/',

        'TMS-LOCAL' => 'http://localhost:8000/',

        'LMS' => 'https://localhost:8000/lms',

        'DIVA' => 'https://dev-op.quanlydiembanhang.com/elearning-api/v1/', //domain run dev, thời gian đầu test trên domain này, khi nào launch hệ thống chuyển sang domain live

        'DIVA-LIVE' => 'https://qldbh.bgt.com.vn/elearning-api/v1/',

        'DIVA-TOKEN-SYSTEM' => 'U7x8oakUxoL4920zJuskO9-Ikksh4292J' ,//token hệ thống do DIVA cung cap,

        'EDITING-TEACHER-ID' => 3 ,//editingteacher id

        'EMAIL-DEFAULT' => 'nohasmail@gmail.com' //editingteacher id
    ],
];
