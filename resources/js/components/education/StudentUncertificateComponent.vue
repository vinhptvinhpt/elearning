<template>

    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_hoc_vien_du_dieu_kien_cap_chung_chi') }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div>
            <div class="card">
                <!--            Danh sách học viên chưa được cấp mã-->
                <div class="card-body">
                    <div class="edit_city_form form-material">
                        <h5 class="mb-20 text-uppercase">
                            {{trans.get('keys.danh_sach_hoc_vien_chua_cap_ma_chung_chi')}}</h5>
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

                                        <select id="training_select" v-model="training_id" autocomplete="false"
                                                class="form-control" @change="getListStudentsUncertificate(1)">
                                            <option value="0">-- {{trans.get('keys.chon_khung_nang_luc')}} --</option>
                                            <option v-for="training_option in training_options"
                                                    :value="training_option.id">
                                                {{training_option.name}}
                                            </option>
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
                                            <input v-model="allSelected" @click="selectAllCheckbox()"
                                                   id="branch-select-all"
                                                   type="checkbox" class="filled-in chk-col-light-blue"
                                                   name="select_all"
                                                   value=""/>
                                            <label for="branch-select-all"></label>
                                        </th>
                                        <th>{{trans.get('keys.stt')}}</th>
                                        <th>{{trans.get('keys.ten_hoc_vien')}}</th>
                                        <th>{{trans.get('keys.ten_knl')}}</th>
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
                                                <!--                                                <input v-model="certificate_id" :value="user.user_id" type="checkbox"-->
                                                <!--                                                       :id="'certificate_id'+user.user_id"-->
                                                <!--                                                       class="filled-in chk-col-light-blue check_box_branch"><label-->
                                                <!--                                                    :for="'restore_user'+certificate_id.user_id"></label>-->

                                                <input type="checkbox" :value="user" v-model="certificate_id"
                                                       @change="onCheckboxCer()"/>
                                            </td>
                                            <td>{{ (current-1)*row+(index+1) }}</td>
                                            <td>{{ user.fullname }}</td>
                                            <td>{{ user.training_name }}</td>
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
                                                        @click="CreateCertificate(user.user_id,user.training_id)">
                                                    {{trans.get('keys.cap_ma')}}
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                                  :classes=$pagination.classes
                                                  :labels=$pagination.labels></v-pagination>

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

                                        <select id="training_select_certificate" v-model="training_id_certificate"
                                                autocomplete="false" class="form-control"
                                                @change="getListStudentsCertificate(1)">
                                            <option value="0">-- {{trans.get('keys.chon_khung_nang_luc')}} --</option>
                                            <option v-for="training_option in training_options_certificate"
                                                    :value="training_option.id">
                                                {{training_option.name}}
                                            </option>
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
                                        <th>{{trans.get('keys.ten_knl')}}</th>
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
                                            <td>{{ user.training_name }}</td>
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
                                                    <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                                </router-link>


                                                <button v-if="user.status == 0" type="button"
                                                        class="btn btn-primary btn-sm"
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
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2">
                                                    <span class="btn-icon-wrap"><i class="fal fa-spinner"></i></span>
                                                </button>

                                                <router-link v-else-if="user.status == 2" :title="trans.get('keys.xem_chung_chi')"
                                                             class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                             :to="{ name: 'ImageCertificate', query: { code: user.code, badge: user.badge }}">
                                                    <span class="btn-icon-wrap"><i
                                                            class="fal fa-arrow-alt-right"></i></span>
                                                </router-link>
                                                <button v-else-if="user.status == 3" type="button"
                                                        :title="trans.get('keys.cap_chung_chi_loi')"
                                                        data-toggle="modal" data-target="#delete-ph-modal"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                                                    <span class="btn-icon-wrap"><i class="fal fa-exclamation-triangle"></i></span>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <v-pagination v-model="currentCt" @input="onPageChange" :page-count="totalPagesCt"
                                                  :classes=$pagination.classes
                                                  :labels=$pagination.labels></v-pagination>
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
                training_options: [],
                training_id: 0,
                training_options_certificate: [],
                training_id_certificate: 0
            }
        },
        filters: {
            convertDateTime(value) {
                var time = new Date(value * 1000);
                return time.toLocaleDateString();
            }
        },
        methods: {
            fetchTraining() {
                axios.get('/api/training/list_for_filter')
                    .then(response => {
                        this.training_options = response.data;
                        this.training_options_certificate = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            onCheckboxCer() {
                this.allSelected = false;
            },
            selectAllCheckbox() {
                this.certificate_id = [];
                this.allSelected = !this.allSelected;
                if (this.allSelected) {

                    var countEnrol = this.uncertificate.length;
                    if (countEnrol > 0) {
                        for (var i = 0; i < countEnrol; i++) {
                            this.certificate_id.push(this.uncertificate[i]);
                        }
                    }
                }

            },
            generateSelectUser() {
                var user_selected = this.certificate_id;
                let current_pos = this;
                if (this.certificate_id.length === 0) {
                    swal({
                        title: this.trans.get('keys.thong_bao'),
                        text: this.trans.get('keys.ban_chua_chon_hoc_vien_nao'),
                        type: "warning",
                        showCancelButton: false,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    });
                    return;
                }

                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_muon_cap_ma_hang_loat_tai_khoan_da_chon'),
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
                                toastr['success'](current_pos.trans.get('keys.cap_ma_cho_cac_tai_khoan_thanh_cong'), current_pos.trans.get('keys.thong_bao'));
                                current_pos.getListStudentsUncertificate();
                                current_pos.getListStudentsCertificate();
                            } else {
                                toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.that_bai'));
                            }
                            swal.close();
                        })
                        .catch(error => {
                            swal.close();
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });
            },
            getListStudentsUncertificate(paged) {
                axios.post('/student/get/uncertificate', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    training_id: this.training_id
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
                    training_id: this.training_id_certificate
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
            CreateCertificate(user_id, trainning_id) {
                let current_pos = this;
                axios.post('/student/check/certificate', {
                    user_id: user_id,
                    trainning_id: trainning_id
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            current_pos.getListStudentsUncertificate();
                            current_pos.getListStudentsCertificate();

                        } else {
                            toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });
            },
        },
        mounted() {
            this.fetchTraining();
        }
    }
</script>

<style scoped>
    #training_select {
        width: auto !important;
        display: inherit;
    }

    #training_select_certificate {
        width: auto !important;
        display: inherit;
    }
</style>
