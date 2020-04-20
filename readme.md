<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

## Larvel: 5.8.1
## Moodle: 3.7

## Hướng dẫn cài đặt hệ thống Elearning

Cài đặt các phần mềm liên quan
- Docker (google để biết cách cài đặt, ko có lưu ý cài đặt)
- Git (google để biết cách cài đặt, ko có lưu ý cài đặt)
- PIP (cài đặt các thư viện chạy cron gen ảnh chứng chỉ bằng python)


## Deploy hệ thống

- Pull source code từ git về folder chỉ định trên server
- Cấp quyền cho các folder
- cd /path/to/folder/source
- Run: sudo docker-compose up -d
- Import database, connect src -> db tại 2 file: .env và /public/lms/config.php, nếu chưa có file .env có thể copy từ file .env.example
- Chỉnh sửa config trong file: /config/constanst.php
- Remote vào docker app: sudo docker-compose exec app bash
- Chạy config cho Laravel: composer install,
                           php artisan key:generate,
                           php artisan config:cache
                              
    
## Cài đặt PIP
Chạy tuần tự các lệnh
- sudo yum install epel-release
- sudo yum install python-pip

Cài đặt thư viện cho script python gen chứng chỉ
- sudo pip install mysql-connector-python
- sudo pip install Pillow

## Danh sách các cron của hệ thống
Lưu ý, cần chỉnh sửa path cho các cron trên

# cron moodle - xu ly cac action lien quan den recyclebin khoa hoc
* * * * * root cd /home/easia/elearning-easia && docker-compose exec -T app php /var/www/public/lms/admin/cli/cron.php

# cron gui mail xac nhan tham gia khoa hoc
*/2 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendInvitation?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron gui mail thong bao tham gia khoa hoc cap chung chi
40 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendRemindCertificate?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron gui mail nhac nho lam bai quiz
45 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendESEC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron de xuat khoa hoc ky nang mem
50 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendSuggestSSC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron nhac nho khoa hoc sap het han
55 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendRemindERC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron nhac nho lich hoc
28 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendRemindES?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ


#cron nhac nho lau khong dang nhap he thong
38 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendRemindLogin?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron goi y khoa hoc sap dien ra
48 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendRemindUC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ


#cron nhac nho lau ko tham gia khoa hoc
57 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendRemindAccess?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ


#cron them du lieu vao bang notification cho mail enrol hoc vien vao khoa hoc ky nang mem
5 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/insertSuggestSSC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron remove hoc vien khoi khoa hoc ky nang mem
10 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/removeSuggestSSC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron them du lieu vao bang notification cho mail het han khoa hoc bat buoc
15 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/insertRemindERC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron them du lieu vao bang notification cho mail nhac nho lich dao tao
20 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/insertRemindES?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron insert data to db notification for remind login
25 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/insertRemindLogin?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron ithem du lieu vao bang notification cho mail khoa hoc sap dien ra
30 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/insertRemindUC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron ithem du lieu vao bang notification cho mail nhac nho lau ko tham gia khoa hoc
35 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/insertRemindAccess?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron add hoc vien vao KNL
17 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/autoAddTrainningUser?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron enroll hoc vien vao khoa hoc
23 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/autoEnrol?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#cron tu dong gen anh chung chi
0 1 * * * root python /home/easia/elearning-easia/python/generate.py 0 0
