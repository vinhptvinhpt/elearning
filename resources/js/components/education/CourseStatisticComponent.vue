<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li v-if="come_from === 'online'" class="breadcrumb-item">
              <router-link :to="{ name: 'CourseIndex' }">
                {{ trans.get('keys.khoa_dao_tao_online') }}
              </router-link>
            </li>
            <li v-else class="breadcrumb-item">
              <router-link :to="{ name: 'CourseConcentrateIndex' }">
                {{ trans.get('keys.khoa_dao_tao_tap_trung') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.thong_ke_khoa_hoc') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row mx-0">
      <div class="col-12 hk-sec-wrapper">
        <h5 class="hk-sec-title">{{trans.get('keys.thong_tin_khoa_hoc')}}</h5>
        <div class="row">
          <div class="col-12 col-lg-3">
            <div class="card">
              <div style="padding: 10px">
                <img :src="course.avatar ? course.avatar : '/assets/dist/img/img-kh.jpg'" alt="">
              </div>
              <div class="card-body">
                <h6 class="mb-5" style="text-transform: uppercase"><strong>{{course.shortname}}</strong>
                </h6>
                <p>{{trans.get('keys.ten_khoa_hoc')}}: <strong>{{course.fullname}}</strong></p>
                <!--                            <p>Mã số nhân viên bán hàng: <strong>{{users.code ? users.code : 'Chưa cập nhật'}}</strong>-->
                <!--                            </p>-->
                <!--                            <div v-if="users.confirm == 0 && type == 'student'">-->
                <!--                                <hr>-->
                <!--                                <p>Thời gian hết hạn</p>-->
                <!--                                <p><strong :class="users.diff_time_class">{{users.diff_time}}</strong></p>-->
                <!--                            </div>-->
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-9">
            <div class="table-wrap">
              <div class="">
                <table class="dev-table table-sm table-hover display mb-0">
                  <tbody>
                  <tr>
                    <div class="row">
                      <div class="col-4">
                        <th scope="row">{{trans.get('keys.ma_khoa_hoc')}}</th>
                      </div>
                      <div class="col-8">
                        <td>{{ course.shortname }}</td>
                      </div>
                    </div>


                  </tr>
                  <tr>
                    <div class="row">
                      <div class="col-4">
                        <th scope="row">{{trans.get('keys.ten_khoa_hoc')}}</th>
                      </div>
                      <div class="col-8">
                        <td>{{ course.fullname}}</td>
                      </div>
                    </div>


                  </tr>
                  <tr>
                    <div class="row">
                      <div class="col-4">
                        <th scope="row">{{trans.get('keys.danh_muc_khoa_hoc')}}</th>
                      </div>
                      <div class="col-8">
                        <td>{{ course.category_name }}</td>
                      </div>
                    </div>


                  </tr>
                  <tr>
                    <div class="row">
                      <div class="col-4">
                        <th scope="row">{{trans.get('keys.diem_qua_mon')}}</th>
                      </div>
                      <div class="col-8">
                        <td>{{ course.pass_score }}</td>
                      </div>
                    </div>
                  </tr>
                  <tr>
                    <div class="row">
                      <div class="col-4">
                        <th scope="row">{{trans.get('keys.thoi_gian_bat_dau')}}</th>
                      </div>
                      <div class="col-8">
                        <td>{{ course.startdate }}</td>
                      </div>
                    </div>
                  </tr>
                  <tr>
                    <div class="row">
                      <div class="col-4">
                        <th scope="row">{{trans.get('keys.thoi_gian_ket_thuc')}}</th>
                      </div>
                      <div class="col-8">
                        <td>{{ course.enddate}}</td>
                      </div>
                    </div>
                  </tr>
                  <tr>
                    <div class="row">
                      <div class="col-4">
                        <th scope="row">{{trans.get('keys.mo_ta')}}</th>
                      </div>
                      <div class="col-8">
                        <td>
                          <div v-html="course.summary"></div>
                        </td>
                      </div>
                    </div>
                  </tr>
                  </tbody>
                </table>
              </div>
              <div class="text-right">
                <router-link v-if="this.come_from === 'online'"
                   :to="{name: 'CourseDetail', params: {id: course.id}}"
                   class="btn btn-primary btn-sm">{{trans.get('keys.edit')}}</router-link>
                <router-link v-else
                   :to="{name: 'CourseConcentrateDetail', params: {id: course.id}}"
                   class="btn btn-primary btn-sm">{{trans.get('keys.edit')}}</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 hk-sec-wrapper">
        <div class="row">
          <div class="col-12">
            <section class="hk-sec-wrapper">
              <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_hoc_vien_trong_khoa_hoc')}}</h5>
              <div class="row">
                <div class="col-sm">
                  <div class="table-wrap">
                    <div class="row">
                      <div class="col-6 dataTables_wrapper">
                        <div class="dataTables_length">
                          <label>{{trans.get('keys.hien_thi')}}
                            <select v-model="row"
                                    class="custom-select custom-select-sm form-control form-control-sm" @change="getStatictisUserCourse(1)">
                              <option value="5">5</option>
                              <option value="10">10</option>
                              <option value="20">20</option>
                              <option value="50">50</option>
                            </select>
                          </label>
                        </div>
                      </div>
                      <div class="col-6">
                        <form v-on:submit.prevent="getStatictisUserCourse(1)">
                          <div class="d-flex flex-row form-group">
                            <input v-model="keyword" type="text"
                                   class="form-control"
                                   :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                            <button type="button" id="btnFilter"
                                    class="btn btn-primary d-none d-lg-block"
                                    @click="getStatictisUserCourse(1)">
                              {{trans.get('keys.tim')}}
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table id="datable_1" class="table_res">
                        <thead>
                        <tr>
                          <th>{{trans.get('keys.stt')}}</th>
                          <th>{{trans.get('keys.tai_khoan')}}</th>
                          <th class=" mobile_hide" style="width: 20%;">{{trans.get('keys.ho_ten')}}</th>
                          <th class="text-center">{{trans.get('keys.tien_do_hoc_%')}}</th>
                          <th class="text-center mobile_hide">{{trans.get('keys.diem_hoc_vien')}}</th>
                          <th class="text-center">{{trans.get('keys.trang_thai')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(cu,index) in courseUsers">
                          <td>{{ (current-1)*row+(index+1) }}</td>
                          <td>
                            {{ cu.username }}
                          <td class=" mobile_hide">{{ cu.lastname }} {{cu.firstname}}</td>
                          <td class="text-center">
                            <div v-if="total_course>0">
                              <div v-if="cu.user_course_learn==0">
                                0
                              </div>
                              <div v-else>
                                {{ ((cu.user_course_learn*100)/total_course).toFixed(2)}}
                              </div>

                            </div>
                            <div v-else>
                              0
                            </div>
                          </td>
                          <td class="text-center mobile_hide">
                            <div v-if="cu.finalgrade>0">{{ Math.floor(cu.finalgrade)}}</div>
                          </td>

                          <td class="text-center">
                            <!--                                                <div v-if="total_course==0">-->
                            <!--                                                    <span class="badge badge-yellow">Chưa học</span>-->
                            <!--                                                </div>-->
                            <!--                                                <div v-else>-->
                            <!--                                                    <span v-if="cu.finalgrade>=course.pass_score  && (cu.user_course_learn/total_course)>=1"-->
                            <!--                                                          class="badge badge-success">Pass</span>-->
                            <!--                                                    <span v-else class="badge badge-danger">Failed</span>-->
                            <!--                                                </div>-->

                            <div v-if="cu.finalgrade>0">
                              <div v-if="cu.user_course_learn>total_course">
                                                             <span v-if="cu.finalgrade>=course.pass_score"
                                                                   class="badge badge-success">Pass</span>
                                <span v-else class="badge badge-danger">Failed</span>
                              </div>
                              <div v-else>
                                <span class="badge badge-yellow">{{trans.get('keys.chua_hoan_thanh')}}</span>
                              </div>
                            </div>
                            <div v-else>
                              <span class="badge badge-yellow">{{trans.get('keys.chua_hoan_thanh')}}</span>
                            </div>
                          </td>

                        </tr>
                        </tbody>
                        <tfoot>

                        </tfoot>
                      </table>

                      <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
      </div>

      <!--        Danh sách học viên điểm danh-->
      <div v-if="this.come_from === 'offline'" class="col-12 hk-sec-wrapper">
        <div class="row">
          <div class="col-12">
            <section class="hk-sec-wrapper">
              <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_diem_danh_hoc_vien_trong_khoa_hoc')}}</h5>
              <div class="row">
                <div class="col-sm">
                  <div class="table-wrap">
                    <div class="row">
                      <div class="col-6 dataTables_wrapper">
                        <div class="dataTables_length">
                          <label>{{trans.get('keys.hien_thi')}}
                            <select v-model="rowAtt"
                                    class="custom-select custom-select-sm form-control form-control-sm" @click="getStatictisUserAttendance(1)">
                              <option value="5">5</option>
                              <option value="10">10</option>
                              <option value="20">20</option>
                              <option value="50">50</option>
                            </select>
                          </label>
                        </div>
                      </div>
                      <div class="col-6">
                        <form v-on:submit.prevent="getStatictisUserAttendance(1)">
                          <div class="d-flex flex-row form-group">
                            <input v-model="keywordAtt" type="text"
                                   class="form-control search_text"
                                   :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+'...'">
                            <button type="button" id="btnFilterAtt"
                                    class="btn btn-primary btn-sm"
                                    @click="getStatictisUserAttendance(1)">
                              {{trans.get('keys.tim')}}
                            </button>
                            <a style="color: #fff" class="btn btn-primary btn-sm" v-on:click="exportExcelAttendance()" :title="trans.get('keys.xuat_excel')">
                              <span class="btn-icon-wrap"><i class="fal fa-file-excel-o"></i>&nbsp;{{trans.get('keys.excel')}}</span>
                            </a>
                          </div>
                        </form>
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table id="datable_1Att" class="table_res">
                        <thead>
                        <tr>
                          <th>{{trans.get('keys.stt')}}</th>
                          <th>{{trans.get('keys.tai_khoan')}}</th>
                          <th class=" mobile_hide" style="width: 20%;">{{trans.get('keys.ho_ten')}}</th>
                          <th class="text-center">{{trans.get('keys.so_buoi_diem_danh')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(att,index) in attendanceUsers">
                          <td>{{ (currentAtt-1)*rowAtt+(index+1) }}</td>
                          <td>
                            {{ att.username }}
                          <td class=" mobile_hide">{{ att.lastname }} {{att.firstname}}</td>
                          <td class="text-center">
                            {{ att.count_attendance }} / {{ att.total_date_course }}
                          </td>

                        </tr>
                        </tbody>
                        <tfoot>

                        </tfoot>
                      </table>
                      <v-pagination v-model="currentAtt" @input="onPageChange" :page-count="totalPagesAtt" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'

    export default {
        props: ['course_id', 'come_from'],
        //components: {vPagination},
        data() {
            return {
                course: {
                    avatar: ''
                },
                keyword: '',
                row: 5,
                keywordAtt: '',
                rowAtt: 5,
                current: 1,
                currentAtt: 1,
                courseUsers: [],
                attendanceUsers: [],
                total_course: 0,
                total_attendance: 0,
                totalPages: 1,
                totalPagesAtt: 1
            }
        },
        filters: {
            convertPercent(value) {
                return (value * 100);
            }
        },
        methods: {
            getCourseDetail() {
                axios.get('/api/courses/get_course_detail/' + this.course_id)
                    .then(response => {
                        this.course = response.data;

                        var startdate = new Date(response.data.startdate * 1000);

                        var ten = function (i) {
                            return (i < 10 ? '0' : '') + i;
                        };
                        var YYYY = startdate.getFullYear();
                        var MM = ten(startdate.getMonth() + 1);
                        var DD = ten(startdate.getDate());


                        this.course.startdate = DD + '/' + MM + '/' + YYYY;

                        var endate = new Date(response.data.enddate * 1000);

                        var YYYY_end = endate.getFullYear();
                        var MM_end = ten(endate.getMonth() + 1);
                        var DD_end = ten(endate.getDate());

                        this.course.enddate = DD_end + '/' + MM_end + '/' + YYYY_end;

                        this.course.pass_score = Math.floor(response.data.pass_score);
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            onPageChange() {
                this.getStatictisUserCourse();
                this.getStatictisUserAttendance();
            },
            getStatictisUserCourse(paged) {
                axios.post('/api/course/statistic', {
                    course_id: this.course_id,
                    row: this.row,
                    keyword: this.keyword,
                    page: paged || this.current,
                })
                    .then(response => {
                        this.total_course = response.data.total_course;
                        this.courseUsers = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;

                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            getStatictisUserAttendance(paged){
                axios.post('/course/student/attendance', {
                    course_id: this.course_id,
                    row: this.rowAtt,
                    keyword: this.keywordAtt,
                    page: paged || this.currentAtt,
                })
                    .then(response => {
                        this.total_attendance = response.data.total_attendance;
                        this.attendanceUsers = response.data.data.data;
                        this.currentAtt = response.data.pagination.current_page;
                        this.totalPagesAtt = response.data.pagination.total;

                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            exportExcelAttendance() {
              axios.post('/api/exportAttendance', {
                keyword: this.keywordAtt,
                course_id: this.course_id,
                course_name: this.course.fullname
              })
                .then(response => {
                  let file_name = response.data;
                  let a = $("<a>")
                    .prop("href", "/api/downloadExport/" + file_name)
                    .appendTo("body");
                  a[0].click();
                  a.remove();
                })
                .catch(error => {
                  toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
                });
          },
        },
        mounted() {
            this.getCourseDetail();
            this.getStatictisUserCourse(1);
            this.getStatictisUserAttendance(1);
        }
    }
</script>

<style scoped>

</style>
