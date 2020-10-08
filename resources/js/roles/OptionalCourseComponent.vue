<template>
    <div class="row">
        <div class="col-12">
            <div class="accordion" id="accordion_3">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <a role="button" data-toggle="collapse" href="#collapse_3" aria-expanded="true">
                            <i class="fal fa-plus mr-3"></i>{{trans.get('keys.optional_course')}}
                        </a>
                    </div>
                    <div id="collapse_3" class="collapse show" data-parent="#accordion_3" role="tabpanel">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12">
                                    <section class="hk-sec-wrapper">

                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="table-wrap">
                                                    <div class="row">
                                                        <div class="col-lg-5 col-sm-10 mb-3">
                                                            <h6 class="hk-sec-title">
                                                                {{trans.get('keys.danh_sach_khoa_hoc')}}</h6>

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <form v-on:submit.prevent="getUserNeedEnrol(1)">
                                                                        <div class="d-flex flex-row form-group">
                                                                            <input v-model="keyword" type="text"
                                                                                   class="form-control search_text"
                                                                                   :placeholder="trans.get('keys.nhap_tu_khoa')+' ...'">
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
                                                                    <div class="dataTables_length"
                                                                         style="display:block;">
                                                                        <label>{{trans.get('keys.hien_thi')}}
                                                                            <select v-model="row"
                                                                                    class="custom-select custom-select-sm form-control form-control-sm"
                                                                                    @change="getUserNeedEnrol(1)">
                                                                                <option value="10">10</option>
                                                                                <option value="50">50</option>
                                                                                <option value="100">100</option>
                                                                            </select>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <table id="datable_1" class="table_res">
                                                                <thead>
                                                                <tr>
                                                                    <th>{{trans.get('keys.stt')}}</th>
                                                                    <th>{{trans.get('keys.ma_khoa_hoc')}}</th>
                                                                    <th class=" mobile_hide">
                                                                        {{trans.get('keys.ten_khoa_hoc')}}
                                                                    </th>
                                                                    <th class="text-center">
                                                                        <input class="selection-all" type="checkbox"
                                                                               v-model="allSelected"
                                                                               @click="selectAllEnrol()">
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr v-for="(course,index) in userNeedEnrols">
                                                                    <td>{{ (current-1)*row+(index+1) }}</td>
                                                                    <td>{{ course.shortname }}</td>
                                                                    <td class="mobile_hide">{{ course.fullname }}</td>
                                                                    <td class="text-center">
                                                                        <input class="selection-child" type="checkbox"
                                                                               :value="course.id" v-model="userEnrols"
                                                                               @change="onCheckboxEnrol()"/>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>

                                                            <v-pagination v-model="current" @input="onPageChangeOP"
                                                                          :page-count="totalPages"
                                                                          :classes=$pagination.classes></v-pagination>

                                                        </div>

                                                        <div class="col-sm-2"
                                                             style="text-align: center; margin-top: 11rem;">
                                                            <button :title="trans.get('keys.cap_quyen')"
                                                                    data-toggle="modal"
                                                                    data-target="#delete-ph-modal"
                                                                    @click="enrolUserToCourse()"
                                                                    class="btn btn-icon btn-primary btn-icon-style-2">
                                                                <span class="btn-icon-wrap"><i
                                                                        class="fal fa-arrow-alt-right"></i></span>
                                                            </button>
                                                            <button :title="trans.get('keys.huy_quyen')"
                                                                    data-toggle="modal"
                                                                    data-target="#delete-ph-modal"
                                                                    @click="removeEnrolUserToCourse()"
                                                                    class="btn btn-icon btn-danger btn-icon-style-2">
                                                                <span class="btn-icon-wrap"><i
                                                                        class="fal fa-arrow-alt-left"></i></span>
                                                            </button>
                                                        </div>

                                                        <div class="col-lg-5">
                                                            <h6 class="hk-sec-title">
                                                                {{trans.get('keys.danh_sach_khoa_hoc_optional')}}</h6>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <form v-on:submit.prevent="getCurrentUserEnrol(1)">
                                                                        <div class="d-flex flex-row form-group">
                                                                            <input v-model="keyword_curr" type="text"
                                                                                   class="form-control search_text"
                                                                                   :placeholder="trans.get('keys.nhap_tu_khoa')+'...'">
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
                                                                    <div class="dataTables_length"
                                                                         style="display:block;">
                                                                        <label>{{trans.get('keys.hien_thi')}}
                                                                            <select v-model="row_crr"
                                                                                    class="custom-select custom-select-sm form-control form-control-sm"
                                                                                    @change="getCurrentUserEnrol(1)">
                                                                                <option value="10">10</option>
                                                                                <option value="50">50</option>
                                                                                <option value="100">100</option>
                                                                            </select>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <table id="datable_2" class="table_res">
                                                                <thead>
                                                                <tr>
                                                                    <th>{{trans.get('keys.stt')}}</th>
                                                                    <th>{{trans.get('keys.ma_khoa_hoc')}}</th>
                                                                    <th class=" mobile_hide">
                                                                        {{trans.get('keys.ten_khoa_hoc')}}
                                                                    </th>
                                                                    <th class="text-center">
                                                                        <input class="selection-all" type="checkbox"
                                                                               v-model="allSelectedRemove"
                                                                               @click="selectAllRemoveEnrol()">
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr v-for="(course_inside,index) in currentUserEnrols">
                                                                    <td>{{ (current_page-1)*row_crr+(index+1) }}</td>
                                                                    <td>{{ course_inside.shortname }}</td>
                                                                    <td class=" mobile_hide">{{ course_inside.fullname
                                                                        }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <input class="selection-child" type="checkbox"
                                                                               :value="course_inside.id"
                                                                               v-model="userRemoveEnrol"
                                                                               @change="onCheckboxRemoveEnrol()"/>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>

                                                            <v-pagination v-model="current_page"
                                                                          @input="onPageChangeCurr"
                                                                          :page-count="totalPages_crr"
                                                                          :classes=$pagination.classes></v-pagination>

                                                        </div>
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
            </div>
        </div>
    </div>


</template>

<script>
    export default {
        props: ['role_id', 'org_id'],
        data() {
            return {
                userNeedEnrols: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 10,
                category_id: 0,
                sample: 0,

                currentUserEnrols: [],
                keyword_curr: '',
                current_page: 1,
                totalPages_crr: 0,
                row_crr: 10,
                category_id_crr: 0,

                userEnrols: [],
                userRemoveEnrol: [],
                allSelected: false,
                allSelectedRemove: false,
            }
        },
        methods: {
            getUserNeedEnrol(paged) {
                axios.post('/api/courses/optional_courses', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    is_excluded: 1,
                    org_id: this.org_id
                })
                    .then(response => {
                        this.userNeedEnrols = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                        this.uncheckEnrolAll();
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getCurrentUserEnrol(paged) {
                axios.post('/api/courses/optional_courses', {
                    page: paged || this.current_page,
                    keyword: this.keyword_curr,
                    row: this.row_crr,
                    is_excluded: 0,
                    org_id: this.org_id
                })
                    .then(response => {
                        this.currentUserEnrols = response.data.data.data;
                        this.current_page = response.data.pagination.current_page;
                        this.totalPages_crr = response.data.pagination.total;
                        this.uncheckRemoveEnrolAll();
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChangeOP() {
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
                let current_pos = this;
                if (this.userEnrols.length === 0) {
                    toastr['error'](current_pos.trans.get('keys.ban_chua_chon_khoa_hoc'), current_pos.trans.get('keys.loi'));
                    return;
                }
                axios.post('/api/courses/assign_optional_course', {
                    lstCourse: this.userEnrols,
                    org_id: this.org_id,
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                            current_pos.getCurrentUserEnrol(current_pos.current_page);
                            current_pos.getUserNeedEnrol(current_pos.current_page);
                        } else {
                            toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });
            },
            removeEnrolUserToCourse() {
                let current_pos = this;
                if (this.userRemoveEnrol.length === 0) {
                    toastr['error'](current_pos.trans.get('keys.ban_chua_chon_khoa_hoc'), current_pos.trans.get('keys.loi'));
                    return;
                }
                axios.post('/api/courses/remove_assign_optional_course', {
                    lstCourse: this.userRemoveEnrol,
                    org_id: this.org_id,
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                            current_pos.getCurrentUserEnrol(current_pos.current_page);
                            current_pos.getUserNeedEnrol(current_pos.current_page);
                        } else {
                            toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });
            },
            uncheckEnrolAll() {
                this.allSelected = true;
                this.selectAllEnrol();
            },
            uncheckRemoveEnrolAll() {
                this.allSelectedRemove = true;
                this.selectAllRemoveEnrol();
            }
        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>
