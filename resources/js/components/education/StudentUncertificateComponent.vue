<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_hoc_vien_du_dieu_kien_cap_chung_chi') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="card">
        <!--            Danh sách học viên chưa được cấp mã-->
        <div class="card-body">
          <div class="edit_city_form form-material">
            <h5 class="mb-20 text-uppercase">{{trans.get('keys.danh_sach_hoc_vien_chua_cap_ma_chung_chi')}}</h5>
            <div class="row">
              <div class="col-sm-8 dataTables_wrapper">
                <div class="dataTables_length" style="display: inline-block;">
                  <label>{{trans.get('keys.hien_thi')}}
                    <select v-model="row"
                            class="custom-select custom-select-sm form-control form-control-sm"
                            @change="getListStudentsUncertificate(1)">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                    </select>
                  </label>
                </div>
              </div>
              <div class="col-sm-4">
                <form v-on:submit.prevent="getListStudentsUncertificate(1)">
                  <div class="d-flex flex-row form-group">
                    <input v-model="keyword" type="text"
                           class="form-control search_text"
                           :placeholder="trans.get('keys.nhap_tu_khoa')+'...'">
                    <button type="button" class="btn btn-primary btn-sm"
                            @click="getListStudentsUncertificate(1)">
                      {{trans.get('keys.tim')}}
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table_res">
                    <thead>
                    <th>
                      <input v-model="allSelected" @click="selectAllCheckbox()" id="branch-select-all"
                             type="checkbox" class="filled-in chk-col-light-blue" name="select_all"
                             value=""/>
                      <label for="branch-select-all"></label>
                    </th>
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>{{trans.get('keys.ten_hoc_vien')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.dien_thoai')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                    <th>{{trans.get('keys.hanh_dong')}}</th>
                    </thead>
                    <tbody>
                    <tr v-if="uncertificate.length == 0">
                      <td colspan="7">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                    </tr>
                    <tr v-else v-for="(user,index) in uncertificate">
                      <td>
                        <input v-model="certificate_id" :value="user.user_id" type="checkbox"
                               :id="'certificate_id'+user.user_id"
                               class="filled-in chk-col-light-blue check_box_branch"><label
                        :for="'restore_user'+certificate_id.user_id"></label>
                      </td>
                      <td>{{ (current-1)*row+(index+1) }}</td>
                      <td>{{ user.fullname }}</td>
                      <td class=" mobile_hide">{{ user.cmtnd }}</td>
                      <td class=" mobile_hide">{{ user.phone }}</td>
                      <td class=" mobile_hide">{{ user.email }}</td>
                      <td>
                        <router-link
                           :to="{name: 'SaleRoomUserView', params: {name_section: name_section, user_id: user.user_id}}"
                           class="btn waves-effect waves-light btn-sm btn-primary">
                          {{trans.get('keys.xem')}}
                        </router-link>
                        <button v-if="user.status == null" type="button"
                                class="btn btn-primary btn-sm"
                                @click="CreateCertificate(user.user_id)">
                          {{trans.get('keys.cap_ma')}}
                        </button>
                      </td>
                    </tr>
                    </tbody>
                  </table>

                  <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                :classes=$pagination.classes :labels=$pagination.labels></v-pagination>

                  <div class="text-left">
                    <button :title="trans.get('keys.cap_ma_cac_hoc_vien_da_chon')" type="button"
                            class="btn btn-primary btn-sm mt-3" @click="generateSelectUser()">
                      {{trans.get('keys.cap_ma_hang_loat')}}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <!--            Danh sách học viên đã được cấp mã-->
        <div class="card-body">
          <div class="edit_city_form form-material">
            <h5 class="mb-20 text-uppercase">
              {{trans.get('keys.danh_sach_hoc_vien_da_duoc_cap_ma_chung_chi')}}</h5>
            <div class="row">
              <div class="col-sm-8 dataTables_wrapper">
                <div class="dataTables_length" style="display: inline-block;">
                  <label>{{trans.get('keys.hien_thi')}}
                    <select v-model="rowCt"
                            class="custom-select custom-select-sm form-control form-control-sm"
                            @change="getListStudentsCertificate(1)">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                    </select>
                  </label>
                </div>
              </div>
              <div class="col-sm-4">
                <form v-on:submit.prevent="getListStudentsCertificate(1)">
                  <div class="d-flex flex-row form-group">
                    <input v-model="keywordCt" type="text"
                           class="form-control search_text"
                           :placeholder="trans.get('keys.nhap_tu_khoa')+'...'">
                    <button type="button" class="btn btn-primary btn-sm"
                            @click="getListStudentsCertificate(1)">
                      {{trans.get('keys.tim')}}
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="">
                    <thead>
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>{{trans.get('keys.ten_hoc_vien')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.dien_thoai')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                    <th>{{trans.get('keys.ma_chung_chi')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.ngay_cap')}}</th>
                    <th>{{trans.get('keys.hanh_dong')}}</th>
                    </thead>
                    <tbody>
                    <tr v-if="posts.length == 0">
                      <td colspan="7">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                    </tr>
                    <tr v-else v-for="(user,index) in posts">
                      <td>{{ (currentCt-1)*rowCt+(index+1) }}</td>
                      <td>{{ user.fullname }}</td>
                      <td class=" mobile_hide">{{ user.cmtnd }}</td>
                      <td class=" mobile_hide">{{ user.phone }}</td>
                      <td class=" mobile_hide">{{ user.email }}</td>
                      <td>
                        <span v-if="user.code != null">{{ user.code }}</span>
                      </td>
                      <td class=" mobile_hide">
                        <span v-if="user.code != null">{{ user.timecertificate |convertDateTime}}</span>
                      </td>
                      <td>
                        <!--                                            <a :href="trans.get('keys.language')+'/user/view/'+ name_section + '/' +user.user_id"-->
                        <!--                                               class="btn waves-effect waves-light btn-sm btn-primary">{{trans.get('keys.xem')}}</a>-->
                        <router-link :title="trans.get('keys.xem')"
                           class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                           :to="{name: 'SaleRoomUserView', params: {name_section: name_section, user_id: user.user_id}}">
                          <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span></router-link>


                        <button v-if="user.status == 0" type="button" class="btn btn-primary btn-sm"
                                @click="CreateCertificate(user.user_id)">
                          {{trans.get('keys.cap_chung_chi')}}
                        </button>
                        <!--                                            <button v-else-if="user.status == 1" type="button"-->
                        <!--                                                    class="btn btn-primary btn-sm">-->
                        <!--                                                {{trans.get('keys.dang_doi_cap_chung_chi')}}-->
                        <!--                                            </button>-->

                        <button v-else-if="user.status == 1" type="button"
                                :title="trans.get('keys.dang_doi_cap_chung_chi')"
                                data-toggle="modal" data-target="#delete-ph-modal"
                                class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                          <span class="btn-icon-wrap"><i class="fal fa-spinner"></i></span>
                        </button>

                        <a :title="trans.get('keys.xem_chung_chi')"
                           v-else :href="'/storage/upload/certificate/'+ user.code+'.png'" target="_blank"
                           class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                        ><span class="btn-icon-wrap"><i
                          class="fal fa-arrow-alt-right"></i></span></a>

                      </td>
                    </tr>
                    </tbody>
                  </table>
                  <v-pagination v-model="currentCt" @input="onPageChange" :page-count="totalPagesCt"
                                :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                </div>
              </div>
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
        props: [],
        //components: {vPagination},
        data() {
            return {
                data: {
                    saleroom: {
                        name: '',
                        code: '',
                        user_id: 0,
                        branch_id: 0,
                        address: '',
                        description: '',
                    },
                },
                name_section: 'uncertificate',
                uncertificate: {},
                posts: {},
                certificate_id: [],
                keyword: '',
                keywordCt: '',
                current: 1,
                currentCt: 1,
                totalPages: 0,
                totalPagesCt: 0,
                rowCt: 10,
                row: 10,
                allSelected: false,
            }
        },
        filters: {
            convertDateTime(value) {
                var time = new Date(value * 1000);
                return time.toLocaleDateString();
            }
        },
        methods: {
            selectAllCheckbox() {
                this.certificate_id = [];
                if (!this.allSelected) {
                    this.uncertificate.forEach((select) => {
                        this.certificate_id.push(select.user_id);
                    });
                }
            },
            generateSelectUser() {
                var user_selected = this.certificate_id;
                if (this.certificate_id.length === 0) {
                    swal({
                        title: "Thông báo",
                        text: "Bạn chưa chọn học viên nào.",
                        type: "warning",
                        showCancelButton: false,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    });
                    return;
                }
                swal({
                    title: "Thông báo",
                    text: "Bạn muốn cấp mã hàng loạt tài khoản đã chọn.",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/certificate/generate/multiple', {
                        user_selected: user_selected
                    })
                        .then(response => {
                            if (response.data === 'success') {
                                swal({
                                        title: "Thông báo",
                                        text: "Cấp mã cho các tài khoản thành công!",
                                        type: "success",
                                        showCancelButton: false,
                                        closeOnConfirm: false,
                                        showLoaderOnConfirm: true
                                    },
                                    function () {
                                        window.location.reload();
                                    });
                            } else {
                                swal({
                                    title: "Lỗi hệ thống",
                                    text: "Thao tác thất bại.",
                                    type: "error",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                });
                            }
                        })
                        .catch(error => {
                            swal({
                                title: "Lỗi hệ thống",
                                text: "Thao tác thất bại.",
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        });
                });
            },
            getListStudentsUncertificate(paged) {
                axios.post('/student/get/uncertificate', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                })
                    .then(response => {
                        if (response.data) {
                            this.uncertificate = response.data.data.data;
                            this.current = response.data.pagination.current_page;
                            this.totalPages = response.data.pagination.total;
                        }
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getListStudentsCertificate(paged) {
                axios.post('/student/get/certificate', {
                    page: paged || this.currentCt,
                    keyword: this.keywordCt,
                    row: this.rowCt,
                })
                    .then(response => {
                        if (response.data) {
                            this.posts = response.data.data.data;
                            this.currentCt = response.data.pagination.current_page;
                            this.totalPagesCt = response.data.pagination.total;
                        }
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            generateMutipleCertificate() {

            },
            onPageChange() {
                this.getListStudentsUncertificate();
                this.getListStudentsCertificate();
            },
            CreateCertificate(user_id) {
                axios.post('/student/check/certificate', {
                    user_id: user_id,
                })
                    .then(response => {
                        if (response.data.status) {
                            swal(
                                {
                                    title: response.data.message,
                                    // text: response.data.message,
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                },
                                function () {
                                    window.location.reload();
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
                        console.log(error.response.data);
                    });
            },
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
