<template>

    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/tms/self/list">{{ trans.get('keys.quan_tri_self') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.thong_ke_self') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mx-0">
            <ul class="col-12 nav nav-tabs nav-light" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                       aria-controls="home" aria-selected="true"> {{trans.get('keys.sumary')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                       aria-controls="profile" aria-selected="false">{{trans.get('keys.question')}}</a>
                </li>
            </ul>

            <div class="col-12 tab-content py-4 border-top-0 rounded-top-0" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row">
                        <div class="col-xl-12">
                            <section class="hk-sec-wrapper">
                                <h5 class="hk-sec-title">{{trans.get('keys.thong_ke_self')}}</h5>

                                <div class="row">
                                    <div class="col-sm-4 form-group">
                                        <div class="d-flex flex-row form-group">
                                            <treeselect v-model="organization.parent_id" :multiple="false"
                                                        :options="options" id="organization_parent_id"
                                                        @input="getSelfStatistic(1)"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="d-flex flex-row form-group">
                                            <input v-model="keywordc" type="text" class="form-control" id="tags"
                                                   :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 form-group">
                                        <form v-on:submit.prevent="getSelfStatistic(1)">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword" type="text"
                                                       class="form-control"
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_or_section')+' ...'"/>
                                                <button type="button" id="btnFilter"
                                                        class="btn btn-primary btn-sm"
                                                        @click="getSelfStatistic(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm">
                                        <div class="table-wrap">

                                            <div class="row pt-3">
                                                <div class="col-6 dataTables_wrapper">
                                                    <div class="dataTables_length d-block">
                                                        <label>{{trans.get('keys.hien_thi')}}
                                                            <select v-model="row"
                                                                    class="custom-select custom-select-sm form-control form-control-sm"
                                                                    @change="getSelfStatistic(1)">
                                                                <option value="5">5</option>
                                                                <option value="10">10</option>
                                                                <option value="20">20</option>
                                                                <option value="50">50</option>
                                                            </select>
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>

                                            <br/>
                                            <div class="table-responsive">
                                                <table id="datable_1" class="table_res">
                                                    <thead>
                                                    <tr>
                                                        <th>{{trans.get('keys.stt')}}</th>
                                                        <th style="width: 10%;">{{trans.get('keys.username')}}</th>
                                                        <th style="width: 20%;">{{trans.get('keys.fullname')}}</th>
                                                        <th>{{trans.get('keys.ques_name')}}</th>
                                                        <th>{{trans.get('keys.sec_name')}}</th>
                                                        <th>{{trans.get('keys.total_point')}}</th>
                                                        <th>{{trans.get('keys.avg_point')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(sur,index) in surveys">
                                                        <td>{{ (current-1)*row+(index+1) }}</td>
                                                        <td>
                                                            <router-link
                                                                    :to="{ path: 'system/user/edit', name: 'EditUserById', params: { user_id: sur.user_id }, query: {type: 'system'} }">
                                                                {{ sur.username }}
                                                            </router-link>
                                                        </td>
                                                        <td>{{ sur.fullname }}</td>
                                                        <td>{{ sur.ques_name }}</td>
                                                        <td>{{ sur.section_name }}</td>
                                                        <td>{{ sur.total_point }}</td>
                                                        <td>{{ sur.avg_point.toFixed(2) }}</td>

                                                    </tr>
                                                    </tbody>
                                                    <tfoot>

                                                    </tfoot>
                                                </table>

                                                <v-pagination v-model="current" @input="onPageChange"
                                                              :page-count="totalPages"
                                                              :classes=$pagination.classes
                                                              :labels=$pagination.labels></v-pagination>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row">
                        <div class="col-12">
                            <section class="hk-sec-wrapper">
                                <div class="col-lg-12 col-sm-12 mb-3">

                                    <div class="row">
                                        <div class="col-4">
                                            <treeselect v-model="organization_id_1"
                                                        :multiple="false" :options="options"
                                                        @input="getUserSurvey(1)"/>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword_us" type="text" class="form-control"
                                                       id="tag-users"
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <form v-on:submit.prevent="getUserSurvey(1)">
                                                <div class="d-flex flex-row form-group">
                                                    <input v-model="keyword_rs" type="text"
                                                           class="form-control search_text"
                                                           :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_dang_nhap_fullname')+' ...'">
                                                    <button type="button" id="btnFilter1"
                                                            class="btn btn-primary btn-sm"
                                                            @click="getUserSurvey(1)">
                                                        {{trans.get('keys.tim')}}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <h6 class="hk-sec-title">
                                        {{trans.get('keys.danh_sach_nguoi_dung_tham_gia_self')}}</h6>
                                    <div class="row mb-3">
                                        <div class="col-2 dataTables_wrapper">
                                            <div class="dataTables_length"
                                                 style="display:block;">
                                                <label>{{trans.get('keys.hien_thi')}}
                                                    <select v-model="row_rs"
                                                            class="custom-select custom-select-sm form-control form-control-sm"
                                                            @change="getUserSurvey(1)">
                                                        <option value="5">5</option>
                                                        <option value="10">10</option>
                                                        <option value="50">50</option>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                    <table class="table_res">
                                        <thead>
                                        <tr>
                                            <th>{{trans.get('keys.stt')}}</th>
                                            <th>{{trans.get('keys.username')}}</th>
                                            <th class=" mobile_hide">
                                                {{trans.get('keys.ho_ten')}}
                                            </th>
                                            <th>{{trans.get('keys.email')}}</th>
                                            <th>{{trans.get('keys.ten_khoa_hoc')}}</th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(user,index) in lstUsers">
                                            <td>{{ (current_rs-1)*row_rs+(index+1) }}</td>
                                            <td>
                                                <router-link
                                                        :to="{ path: 'system/user/edit', name: 'EditUserById', params: { user_id: user.user_id }, query: {type: type} }">
                                                    {{ user.username }}
                                                </router-link>
                                            </td>
                                            <td>{{ user.fullname }}
                                            </td>
                                            <td>{{ user.email }}</td>
                                            <td>{{ user.course_code }} - {{ user.course_name }}</td>
                                            <td class="text-center">
                                                <router-link :title="trans.get('keys.chi_tiet_ks')" target="_blank"
                                                             class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                             :to="{ name: 'SelfResultUser', params: { self_id: self_id,user_id: user.user_id,course_id: user.course_id } }">
                                                    <span class="btn-icon-wrap"><i
                                                            class="fal fa-arrow-alt-right"></i></span>
                                                </router-link>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>

                                        </tfoot>
                                    </table>
                                    <v-pagination v-model="current_rs" @input="onPageChangeUS"
                                                  :page-count="totalPages_rs"
                                                  :classes=$pagination.classes></v-pagination>
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
    import Ls from './../../services/ls'

    export default {
        props: ['self_id'],
        components: {Ls},
        data() {
            return {
                surveys: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 5,
                startdate: '',
                enddate: '',

                keywordc: '',
                organization: {
                    name: '',
                    code: '',
                    parent_id: 0,
                    description: '',
                },
                organization_parent_list: [],
                //Treeselect options
                options: [
                    {
                        id: 0,
                        label: this.trans.get('keys.chon_to_chuc')
                    }
                ],

                course_id: '',

                organization_id_1: 0,

                keyword_us: '',
                keyword_rs: '',
                row_rs: 10,
                lstUsers: [],
                current_rs: 1,
                totalPages_rs: 1,

                type: ''
            }
        },
        methods: {
            getCourseSelectOptions() {
                axios.post('/api/courses/list', {
                    row: 0,
                    sample: 0,
                })
                    .then(response => {
                        let additionalCities = [];
                        response.data.forEach(function (cityItem) {
                            let newCity = {
                                label: cityItem.shortname + ' - ' + cityItem.fullname,
                                data_search: cityItem.fullname,
                                id: cityItem.id
                            };
                            additionalCities.push(newCity);
                        });
                        this.courseSelectOptions = additionalCities;
                        this.loadAutoComplete();
                        this.loadAutoCompleteUser();
                    })
                    .catch(error => {
                        //console.log(error.response.data);
                    });
            },
            loadAutoComplete() {
                $("#tags").autocomplete({
                    source: this.lightWell,
                    minLength: 2,
                    select: function (e, ui) {
                        this.keywordc = ui.item.label;

                        this.course_id = ui.item.id;

                        Ls.set('course-self', this.course_id);
                    },
                    change: function (e, ui) {
                    }
                });
            },
            loadAutoCompleteUser() {
                $("#tag-users").autocomplete({
                    source: this.lightWell,
                    minLength: 2,
                    select: function (e, ui) {
                        this.keyword_us = ui.item.label;

                        this.course_id = ui.item.id;

                        Ls.set('course-self', this.course_id);
                    },
                    change: function (e, ui) {
                        // alert(ui.item.value);

                    }
                });
            },
            lightWell(request, response) {
                function hasMatch(s) {
                    return s.toLowerCase().indexOf(request.term.toLowerCase()) !== -1;
                }

                function convertUtf8(str) {
                    str = str.toLowerCase();
                    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
                    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
                    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
                    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
                    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
                    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
                    str = str.replace(/đ/g, "d");
                    str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'|\"|\&|\#|\[|\]|~|\$|_|`|-|{|}|\||\\/g, " ");
                    str = str.replace(/ + /g, " ");
                    str = str.trim();
                    return str;
                }

                var i, l, obj, matches = [];

                if (request.term === "") {
                    response([]);
                    return;
                }

                for (i = 0, l = this.courseSelectOptions.length; i < l; i++) {
                    obj = this.courseSelectOptions[i];
                    if (hasMatch(obj.label) || hasMatch(convertUtf8(obj.data_search))) {
                        matches.push(obj);
                    }
                }
                response(matches);
            },
            getSelfStatistic(paged) {

                if (this.keywordc === '' || this.keywordc === null || this.keywordc === undefined) {
                    Ls.set('course-self', '');
                }
                this.course_id = Ls.get('course-self');

                axios.post('/api/self/statistic', {
                    self_id: this.self_id,
                    course_id: this.course_id,
                    organization_id: this.organization.parent_id,
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                })
                    .then(response => {
                        this.surveys = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            selectParentItem(parent_id) {
                this.organization.parent_id = parent_id;
            },
            selectParent() {
                $('.content_search_box').addClass('loadding');
                axios.post('/organization/list', {
                    keyword: this.parent_keyword,
                    level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
                    paginated: 0 //không phân trang,
                })
                    .then(response => {
                        this.organization_parent_list = response.data;
                        //Set options recursive
                        this.options = this.setOptions(response.data);
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                    })
            },
            setOptions(list) {
                let outPut = [];
                for (const [key, item] of Object.entries(list)) {
                    let newOption = {
                        id: item.id,
                        label: item.name
                    };
                    if (item.children.length > 0) {
                        newOption.children = this.setOptions(item.children);
                    }
                    outPut.push(newOption);
                }
                return outPut;
            },
            onPageChange() {
                this.getSelfStatistic();
            },
            getUserSurvey(paged) {
                if (this.keyword_us === '' || this.keyword_us === null || this.keyword_us === undefined) {
                    Ls.set('course-self', '');
                }
                this.course_id = Ls.get('course-self');
                axios.post('/api/self/users', {
                    page: paged || this.current_rs,
                    self_id: this.self_id,
                    organization_id: this.organization_id_1,
                    course_id: this.course_id,
                    keyword: this.keyword_rs,
                    row: this.row_rs
                }).then(response => {
                    this.lstUsers = response.data.data.data;
                    this.current_rs = response.data.pagination.current_page;
                    this.totalPages_rs = response.data.pagination.total;
                })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            onPageChangeUS() {
                this.getUserSurvey();
            }
        },
        mounted() {
            this.selectParent();
            this.getCourseSelectOptions();
            this.getUserSurvey();
        }
    }
</script>

<style scoped>

</style>
