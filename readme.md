<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

## Laravel: 5.8
## Moodle: 3.7

## Hướng dẫn cài đặt hệ thống Elearning

Cài đặt các phần mềm liên quan
- **Docker** (google để biết cách cài đặt, ko có lưu ý cài đặt)
- **Git** (google để biết cách cài đặt, ko có lưu ý cài đặt)
- **PIP** (cài đặt các thư viện chạy cron gen ảnh chứng chỉ bằng python)


# Deploy hệ thống
### 1. Deploy qua Docker
- Pull source code từ git về folder chỉ định trên server
- Cấp quyền cho các folder: public, storage, vendor, node_modules
- cd /path/to/folder/source
- Run: **sudo docker-compose up -d**
- Import database, connect src -> db tại 2 file: **.env** và **/public/lms/config.php**, nếu chưa có file .env có thể copy từ file **.env.example**
- Chỉnh sửa config trong file: **/config/constanst.php**
- Tạo ra các file json bao gồm: **enroll_trainning.json**, **enroll_user.json** với nội dung: **{"flag":"stop"}** trong folder: **{path}/{to}/{folder}/{source}/storage/app/public/cron**
    Nếu ko có folder cron trong src, cần tạo mới folder này
- Remote vào docker app: **sudo docker-compose exec app bash**
- Chạy config cho Laravel: 

         1. composer install
         2. php artisan key:generate 
         3. php artisan config:cache
         4. php artisan storage:link

### 2. Deploy thường (ko qua docker)
- Import database, connect src -> db tại 2 file: **.env** và **/public/lms/config.php**, nếu chưa có file .env có thể copy từ file **.env.example**
- Chỉnh sửa config trong file: **/config/constanst.php**
- Tạo ra các file json bao gồm: **enroll_trainning.json**, **enroll_user.json** với nội dung: **{"flag":"stop"}** trong folder: **{path}/{to}/{folder}/{source}/storage/app/public/cron**
    Nếu ko có folder cron trong src, cần tạo mới folder này

- Chạy config cho Laravel: 

            1. composer install
            2. php artisan key:generate 
            3. php artisan config:cache
            4. php artisan storage:link
         
### 3.Cài đặt NodeJS và NPM
- Sau khi cài xong, đi đến folder chứa source và chạy các lệnh: 

    1. npm install
    2. npm run dev
    
### 4.Cấu hình NGINX trỏ đến thư mục source                              
    
### 5.Cài đặt PIP
Chạy tuần tự các lệnh
- **sudo yum install epel-release**
- **sudo yum install python-pip**

Cài đặt thư viện cho script python gen chứng chỉ
- **sudo pip install mysql-connector-python**
- **sudo pip install Pillow**

**Lưu ý**: khi cấu hình connection trong file generate.py, nếu cài database container trên cùng 1 máy thì đặt host='localhost', tương tự cho trường hợp container db nằm trên server khác

# Danh sách các cron của hệ thống
Lưu ý, cần chỉnh sửa path cho các cron trên

# cron moodle - xu ly cac action lien quan den recyclebin khoa hoc
*/1 * * * * root cd /home/easia/elearning-easia && docker-compose exec -T app php /var/www/public/lms/admin/cli/cron.php

# SEND MAIL
#1 cron gui mail thong bao nguoi dung da duoc cap chung chi (tam thoi khong su dung)
#40 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendRemindCertificate?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#2 cron gui mail enrol, quiz_start, quiz_end, quiz_completed 
45 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendESEC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#3 cron de xuat khoa hoc optional
50 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendSuggestOC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#4 cron nhac nho khoa hoc bat buoc sap het han
55 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendRemindERC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#5 cron gui mail invite tham gia khoa hoc
*/2 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendInvitation?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#6 cron gui thong bao nhac nho tham gia bai thi toeic
48 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendRemindExam?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#7 cron gui thong bao hoc vien duoc tham gia KNL
44 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendEnrolCompetency?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#8 cron gui thong bao hoan thanh KNL
46 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendCompetencyCompleted?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#9 cron gui thong bao them luot thi bai thi toeic cho leaders, admins
#10 thong bao fail bai thi cho hoc vien
#Gui 2 loai mail gui cung luc
58 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendRequestMoreAttempt?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#11 cron gui thong bao ket qua bai thi toeic
53 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/sendToeicResult?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#INSERT MAIL
# cron them du lieu vao bang notification cho mail het han khoa hoc bat buoc
15 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/insertRemindERC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

# cron them du lieu vao bang notification cho mail suggest optional course
45 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/insertSuggestOC?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

#REMOVE ALL REMINDS
0 0 1 * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/mail/removeAllRemind?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ


#OTHER TASKS
# cron add hoc vien vao KNL
*/1 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/autoAddTrainningUser?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

# cron add hoc vien vao KNL trong TH hoc vien moi duoc them vao he thong
7 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/autoAddTrainningUserCron?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

# cron enroll hoc vien vao khoa hoc trong TH hoc vien moi duoc them vao he thong
10 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/autoEnrolCron?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

# cron enroll hoc vien vao khoa hoc
*/1 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/autoEnrol?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

# cron add hoc vien trong cac khoa hoc don le vao bang trainning_user
31 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/addSingleUserToTrainning?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

# cron add hoc vien da hoan thanh khoa hoc vao bang course_completion doi voi TH hoc vien nam trong KNL
33 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/completeCourse?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

# cron add hoc vien da hoan thanh khoa hoc vao bang course_completion doi voi TH hoc vien ko nam trong KNL duoc add tu khoa don le
33 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/completeCourseSingle?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

# cron enrol hoc vien vao cac khoa hoc thuoc cctc
34 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/enrolUserOrganization?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

# cron add hoc vien da hoan thanh KNL vao bang tms_trainning_completion
43 * * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/completeTrainning?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ

# cron tu dong gen anh chung chi
*/1 * * * * root python /home/easia/elearning-easia/python/generate.py 0 0

# cron generate sas url link azure 
10 0 * * * root cd /home/easia/elearning-easia && /usr/local/bin/docker-compose exec -T app php artisan route:call /api/cron/task/autogenerateSASAzure?token=AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ
