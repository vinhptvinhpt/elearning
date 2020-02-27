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
            <li class="breadcrumb-item active">{{ trans.get('keys.ghi_danh_khoa_hoc') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row mb-4">
      <div class="col-sm">
        <div class="accordion" id="accordion_1">
          <div class="card">
            <div class="card-header d-flex justify-content-between">
              <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_2"
                 aria-expanded="false"><i
                class="fal fa-upload mr-3"></i>{{trans.get('keys.tai_len_excel')}}</a>
            </div>
            <div id="collapse_2" class="collapse" data-parent="#accordion_1">
              <div class="card-body">
                <p class="mb-3">
                  <a :href="'/files/enrol_sample.xlsx'" class="px-0"><i
                    class="fal fa-file-alt mr-3"></i>{{trans.get('keys.tai_ve_bieu_mau')}}</a>
                </p>
                <input type="file" ref="file" name="file" class="dropify fileImport"
                       accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                       @change="selectedFile"/>

                <div class="button-list">
                  <button type="button" class="btn btn-primary hasLoading btn-sm" @click="importExcel()">
                    {{trans.get('keys.ghi_danh')}}<i class="fa fa-spinner" aria-hidden="true"></i>
                  </button>
                </div>
                <div class="logUpload mt-4" v-if="data_import">

                  <h5 class="hk-sec-title mb-3">{{trans.get('keys.thong_tin_ghi_danh')}}</h5>
                  <ul class="list-group mb-3">
                    <li v-for="user in data_import"
                        :class="'list-group-item '+ (user.status == 'success'? 'list-group-item-success' : 'list-group-item-danger')">
                      <span v-if="user.username">{{trans.get('keys.tai_khoan')}}: <strong>{{user.username}}</strong>. </span>
                      {{user.message}}
                    </li>
                  </ul>
                  <li class="list-group-item list-group-item-danger">
                    <span>{{trans.get('keys.refresh_trang_de_xem_du_lieu_vua_import')}}</span>
                  </li>
                </div>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header d-flex justify-content-between">
              <a role="button" data-toggle="collapse" href="#collapse_1"
                 aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.ghi_danh_khoa_dao_tao')}}</a>
            </div>
            <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
              <div class="card-body">
                <div class="row">
                  <div class="col-xl-12">
                    <section class="hk-sec-wrapper">
                      <div class="row">
                        <div class="col-12 mb-3">
                          <h5 class="mb-2">{{trans.get('keys.chon_loai_nguoi_dung')}}</h5>
                          <select v-model="role_id" v-on:change="onSelectChange($event)"
                                  class="form-control">
                            <option value="5">{{trans.get('keys.hoc_vien')}}</option>
                            <option value="4">{{trans.get('keys.giao_vien')}}</option>
                          </select>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm">
                          <div class="table-wrap">

                            <div class="row">
                              <div class="col-lg-5 col-sm-10 mb-3">
                                <h6 class="hk-sec-title">
                                  {{trans.get('keys.danh_sach_nguoi_dung_can_ghi_danh')}}</h6>
                                <div class="row">
                                  <div class="col-12">
                                    <form v-on:submit.prevent="getUserNeedEnrol(1)">
                                      <div class="d-flex flex-row form-group">
                                        <input v-model="keyword" type="text"
                                               class="form-control search_text"
                                               :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_username')+' ...'">
                                        <button type="button" id="btnFilter"
                                                class="btn btn-primary btn-sm"
                                                @click="getUserNeedEnrol(1)">
                                          {{trans.get('keys.tim')}}
                                        </button>
                                      </div>
                                    </form>

                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-6 dataTables_wrapper">
                                    <div class="dataTables_length" style="display:block;">
                                      <label>{{trans.get('keys.hien_thi')}}
                                        <select v-model="row"
                                                class="custom-select custom-select-sm form-control form-control-sm"
                                                @change="getUserNeedEnrol(1)">
                                          <option value="5">5</option>
                                          <option value="10">10</option>
                                          <option value="50">50</option>
                                        </select>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <br/>

                                <table id="datable_1" class="table_res">
                                  <thead>
                                  <tr>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.username')}}</th>
                                    <th class=" mobile_hide" style="width: 30%;">
                                      {{trans.get('keys.ho_ten')}}
                                    </th>
                                    <th class="text-center"><input type="checkbox"
                                                                   v-model="allSelected"
                                                                   @click="selectAllEnrol()"
                                    ></th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <tr v-for="(user,index) in userNeedEnrols">
                                    <td>{{ (current-1)*row+(index+1) }}</td>
                                    <td>{{ user.username }}</td>
                                    <td class=" mobile_hide">{{ user.lastname }} {{ user.firstname }}</td>
                                    <td class="text-center">
                                      <input type="checkbox" :value="user.id"
                                             v-model="userEnrols"
                                             @change="onCheckboxEnrol()"/>

                                    </td>
                                  </tr>
                                  </tbody>
                                  <tfoot>

                                  </tfoot>
                                </table>

                                <v-pagination v-model="current" @input="onPageChange"
                                              :page-count="totalPages"
                                              :classes=$pagination.classes></v-pagination>

                              </div>
                              <div class="col-sm-2" style="text-align: center;">
                                <button :title="trans.get('keys.ghi_danh')"
                                        data-toggle="modal"
                                        style="margin-top: 30px;"
                                        data-target="#delete-ph-modal"
                                        @click="enrolUserToCourse()"
                                        class="btn btn-icon btn-primary btn-icon-style-2 mt-sm-15">
                                                                <span class="btn-icon-wrap"><i
                                                                  class="fal fa-arrow-alt-right"></i></span>
                                </button>
                                <br/><br/>
                                <button :title="trans.get('keys.huy_ghi_danh')"
                                        data-toggle="modal"
                                        style="margin-bottom: 30px;"
                                        data-target="#delete-ph-modal"
                                        @click="removeEnrolUserToCourse()"
                                        class="btn btn-icon btn-danger btn-icon-style-2 mb-sm-0">
                                                                <span class="btn-icon-wrap"><i
                                                                  class="fal fa-arrow-alt-left"></i></span>
                                </button>

                              </div>
                              <div class="col-lg-5">
                                <h6 class="hk-sec-title">
                                  {{trans.get('keys.danh_sach_nguoi_dung_da_ghi_danh_khoa_dao_tao')}}</h6>
                                <div class="row">
                                  <div class="col-12">
                                    <form v-on:submit.prevent="getCurrentUserEnrol(1)">
                                      <div class="d-flex flex-row form-group">
                                        <input v-model="keyword_curr" type="text"
                                               class="form-control search_text"
                                               :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_username')+' ...'">
                                        <button type="button" id="btnFilter1"
                                                class="btn btn-primary btn-sm"
                                                @click="getCurrentUserEnrol(1)">
                                          {{trans.get('keys.tim')}}
                                        </button>
                                      </div>
                                    </form>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-6 dataTables_wrapper">
                                    <div class="dataTables_length" style="display:block;">
                                      <label>{{trans.get('keys.hien_thi')}}
                                        <select v-model="row_crr"
                                                class="custom-select custom-select-sm form-control form-control-sm" @change="getCurrentUserEnrol(1)">
                                          <option value="5">5</option>
                                          <option value="10">10</option>
                                          <option value="50">50</option>
                                        </select>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <br/>

                                <table id="datable_2" class="table_res">
                                  <thead>
                                  <tr>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.username')}}</th>
                                    <th class=" mobile_hide" style="width: 30%;">
                                      {{trans.get('keys.ho_ten')}}
                                    </th>
                                    <th class="text-center"><input type="checkbox"
                                                                   v-model="allSelectedRemove"
                                                                   @click="selectAllRemoveEnrol()"
                                    ></th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <tr v-for="(user,index) in currentUserEnrols">
                                    <td>{{ (current_page-1)*row_crr+(index+1) }}</td>
                                    <td>{{ user.username }}</td>
                                    <td class=" mobile_hide">{{ user.lastname }} {{ user.firstname }}</td>
                                    <td class="text-center">
                                      <input type="checkbox" :value="user.id"
                                             v-model="userRemoveEnrol"
                                             @change="onCheckboxRemoveEnrol()"/>

                                    </td>
                                  </tr>
                                  </tbody>
                                  <tfoot>

                                  </tfoot>
                                </table>

                                <v-pagination v-model="current_page" @input="onPageChangeCurr"
                                              :page-count="totalPages_crr"
                                              :classes=$pagination.classes></v-pagination>

                              </div>
                            </div>
                            <br/>

                          </div>
                        </div>
                      </div>
                    </section>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xl-12">
              <section class="hk-sec-wrapper">
                <div class="row">
                  <div class="col-3">
                    <button class="btn btn-danger btn-sm" @click="goBack()">
                      {{trans.get('keys.quay_lai')}}
                    </button>
                  </div>
                </div>
              </section>
            </div>
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
                userNeedEnrols: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 5,

                currentUserEnrols: [],
                keyword_curr: '',
                current_page: 1,
                totalPages_crr: 0,
                row_crr: 5,

                role_id: 5,
                userEnrols: [],
                userRemoveEnrol: [],
                allSelected: false,
                allSelectedRemove: false,

                data_import: [{
                    "message": "Ghi danh thành công",
                    "status": "success",
                    "username": "khuend2"
                }, {
                    "message": "Ghi danh thành công",
                    "status": "success",
                    "username": "hoangkhang"
                }, {"message": "Ghi danh thành công", "status": "success", "username": "hocvien4"}]
            }
        },
        methods: {
            selectedFile() {
                let file = this.$refs.file.files[0];
                if (!file || (file.type !== 'application/vnd.ms-excel' && file.type !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && file.type !== '.csv')) {
                    const input = this.$refs.file;
                    input.type = 'file';
                    this.$refs.file.value = '';
                    roam_message("error","Định dạng file không hợp lệ");
                }
            },
            importExcel() {
                if (!$('button.hasLoading').hasClass('loadding')) {
                    $('button.hasLoading').addClass('loadding');
                    this.formData = new FormData();
                    this.formData.append('file', this.$refs.file.files[0]);
                    this.formData.append('course_id', this.course_id);
                    axios.post('/api/course/import_enrol', this.formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(response => {
                            if (response.data.status) {
                                this.data_import = response.data.error;
                                $('button.hasLoading').removeClass('loadding');
                                $('.logUpload').show();
                            } else {
                                $('button.hasLoading').removeClass('loadding');
                                swal({
                                    title: response.data.message,
                                    // text: response.data.message,
                                    type: "error",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                });
                            }

                        })
                        .catch(error => {
                            $('button.hasLoading').removeClass('loadding');
                            console.log(error.response.data);
                        });
                }
            },
            getUserNeedEnrol(paged) {
                axios.post('/api/course/user_need_enrol', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    role_id: this.role_id,
                    course_id: this.course_id
                })
                    .then(response => {
                        this.userNeedEnrols = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getCurrentUserEnrol(paged) {
                axios.post('/api/course/current_user_enrol', {
                    page: paged || this.current_page,
                    keyword: this.keyword_curr,
                    row: this.row_crr,
                    role_id: this.role_id,
                    course_id: this.course_id
                })
                    .then(response => {
                        this.currentUserEnrols = response.data.data.data;
                        this.current_page = response.data.pagination.current_page;
                        this.totalPages_crr = response.data.pagination.total;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getUserNeedEnrol();
            },
            onPageChangeCurr() {
                this.getCurrentUserEnrol();
            },
            onSelectChange(event) {
                this.userEnrols = [];
                this.userRemoveEnrol = [];
                this.role_id = event.target.value;
                this.getUserNeedEnrol();
                this.getCurrentUserEnrol();
            },
            selectAllEnrol: function () {
                this.userEnrols = [];
                this.allSelected = !this.allSelected;
                if (this.allSelected) {
                    var countEnrol = this.userNeedEnrols.length;
                    if (countEnrol > 0) {
                        for (var i = 0; i < countEnrol; i++) {
                            this.userEnrols.push(this.userNeedEnrols[i].id.toString());
                        }
                    }
                }
            },
            selectAllRemoveEnrol: function () {
                this.userRemoveEnrol = [];
                this.allSelectedRemove = !this.allSelectedRemove;
                if (this.allSelectedRemove) {
                    var countEnrol = this.currentUserEnrols.length;
                    if (countEnrol > 0) {
                        for (var i = 0; i < countEnrol; i++) {
                            this.userRemoveEnrol.push(this.currentUserEnrols[i].id.toString());
                        }
                    }
                }
            },
            onCheckboxEnrol() {
                this.allSelected = false;
            },
            onCheckboxRemoveEnrol() {
                this.allSelectedRemove = false;
            },
            enrolUserToCourse() {
                if (this.userEnrols.length === 0) {
                    swal({
                        title: 'Bạn chưa chọn học viên',
                        type: "error",
                        showCancelButton: false,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    });
                    return;
                }
                axios.post('/api/course/enrol_user_to_course', {
                    Users: this.userEnrols,
                    role_id: this.role_id,
                    course_id: this.course_id
                })
                    .then(response => {
                        if (response.data.status) {
                            swal({
                                    title: response.data.message,
                                    // text: response.data.message,
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }
                                , function () {
                                    location.reload();
                                }
                            );
                        } else {
                            swal({
                                title: response.data.message,
                                // text: response.data.message,
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }

                    })
                    .catch(error => {
                        swal({
                            title: "Thông báo",
                            text: " Lỗi hệ thống.",
                            type: "error",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        });
                    });
            },
            removeEnrolUserToCourse() {
                if (this.userRemoveEnrol.length === 0) {
                    swal({
                        title: 'Bạn chưa chọn học viên',
                        type: "error",
                        showCancelButton: false,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    });
                    return;
                }
                axios.post('/api/course/remove_enrol_user_to_course', {
                    Users: this.userRemoveEnrol,
                    role_id: this.role_id,
                    course_id: this.course_id
                })
                    .then(response => {
                        if (response.data.status) {
                            swal({
                                    title: response.data.message,
                                    // text: response.data.message,
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }
                                , function () {
                                    location.reload();
                                }
                            );
                        } else {
                            swal({
                                title: response.data.message,
                                // text: response.data.message,
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }

                    })
                    .catch(error => {
                        swal({
                            title: "Thông báo",
                            text: " Lỗi hệ thống.",
                            type: "error",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        });
                    });
            },
            goBack() {
                if (this.come_from === 'online') {
                    this.$router.push({ name: 'CourseIndex' });
                } else {
                    this.$router.push({ name: 'CourseConcentrateIndex' });
                }
            },
            setFileInput() {
              $('.dropify').dropify();
            }
        },
        mounted() {
            this.getUserNeedEnrol();
            this.getCurrentUserEnrol();
            this.setFileInput();
        }
    }
</script>

<style scoped>

</style>
