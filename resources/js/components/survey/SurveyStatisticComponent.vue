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
                            <router-link to="/tms/survey/list">{{ trans.get('keys.quan_tri_survey') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.thong_ke_thong_tin_khao_sat') }}</li>
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
                        <div class="col-12">
                            <section class="hk-sec-wrapper">
                                <h4>{{trans.get('keys.tong_so_nguoi_tham_gia_danh_gia_khao_sat')}}: {{total_view}}</h4>
                                <br/>
                                <h5 class="hk-sec-title">
                                    {{trans.get('keys.thong_ke_ket_qua_khao_sat_so_nguoi_chon_dap_an_tong_so')}}</h5>

                                <div class="row">
                                    <div class="col-sm-4 form-group">
                                        <div class="d-flex flex-row form-group">
                                            <treeselect v-model="organization.parent_id" :multiple="false"
                                                        :options="options" id="organization_parent_id"
                                                        @input="search()"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="d-flex flex-row form-group">
                                            <input v-model="keyword" type="text" class="form-control" id="tags"
                                                   :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 form-group">

                                        <div class="d-flex flex-row form-group">
                                            <button type="button" id="btnFilter"
                                                    class="btn btn-primary d-none d-lg-block"
                                                    @click="search()">
                                                {{trans.get('keys.tim')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-3 form-group">
                                        <form v-on:submit.prevent="search()">
                                            <div class="d-flex flex-row form-group">
                                                <date-picker v-model="startdate" :config="date_options"
                                                             :placeholder="trans.get('keys.ngay_bat_dau')"></date-picker>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-3 form-group">
                                        <form v-on:submit.prevent="search()">
                                            <div class="d-flex flex-row form-group">
                                                <date-picker v-model="enddate" :config="date_options"
                                                             :placeholder="trans.get('keys.ngay_ket_thuc')"></date-picker>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-2 form-group">
                                        <button type="button" id="btnExportPdf"
                                                class="btn-sm btn-pdf btn-primary d-none d-lg-block"
                                                @click="exportFileData('pdf')">
                                            {{trans.get('keys.xuat_file_pdf')}} <i class="fa fa-spinner"
                                                                                   aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div class="col-2 form-group">
                                        <button type="button" id="btnExportExcel"
                                                class="btn-sm btn-excel btn-primary d-none d-lg-block"
                                                @click="exportFileData('excel')">
                                            {{trans.get('keys.xuat_file_excel')}} <i class="fa fa-spinner"
                                                                                     aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div class="col-2 form-group">
                                        <button type="button" v-if="chart_type==='bar'"
                                                class="btn-sm btn-primary d-none d-lg-block"
                                                @click="changeChartFormat('pie')">
                                            {{trans.get('keys.pie_chart')}}
                                        </button>
                                        <button type="button" v-else
                                                class="btn-sm btn-primary d-none d-lg-block"
                                                @click="changeChartFormat('bar')">
                                            {{trans.get('keys.bar_chart')}}
                                        </button>
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="table-wrap">
                                            <div v-for="(question,index) in survey_exam">
                                                <div v-if="question.type_question==='multiplechoice'">
                                                    <question-statistic :question="question.lstQuesChild[0]"
                                                                        :index_question="index" :chart_type="chart_type"
                                                    ></question-statistic>
                                                </div>
                                                <div v-else-if="question.type_question==='checkbox'">
                                                    <checkbox-statistic :question="question.lstQuesChild[0]"
                                                                        :index_question="index" :chart_type="chart_type"
                                                    ></checkbox-statistic>
                                                </div>
                                                <div v-else-if="question.type_question==='minmax'">
                                                    <min-max-question-statistic :question="question"
                                                                                :index_question="index"
                                                                                :chart_type="chart_type"
                                                    ></min-max-question-statistic>
                                                </div>
                                                <div v-else>
                                                    <group-question-statistic :question="question"
                                                                              :index_question="index"
                                                                              :chart_type="chart_type"
                                                    ></group-question-statistic>
                                                </div>
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
                                                        @input="searchUser()"/>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword_us" type="text" class="form-control"
                                                       id="tag-users"
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <form v-on:submit.prevent="searchUser()">
                                                <div class="d-flex flex-row form-group">
                                                    <input v-model="keyword_rs" type="text"
                                                           class="form-control search_text"
                                                           :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_dang_nhap_fullname')+' ...'">
                                                    <button type="button" id="btnFilter1"
                                                            class="btn btn-primary btn-sm"
                                                            @click="searchUser()">
                                                        {{trans.get('keys.tim')}}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-3 form-group">
                                            <form v-on:submit.prevent="searchUser()">
                                                <div class="d-flex flex-row form-group">
                                                    <date-picker v-model="startdate_us" :config="date_options"
                                                                 :placeholder="trans.get('keys.ngay_bat_dau')"></date-picker>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-3 form-group">
                                            <form v-on:submit.prevent="searchUser()">
                                                <div class="d-flex flex-row form-group">
                                                    <date-picker v-model="enddate_us" :config="date_options"
                                                                 :placeholder="trans.get('keys.ngay_ket_thuc')"></date-picker>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <h6 class="hk-sec-title">
                                        {{trans.get('keys.danh_sach_nguoi_dung_tham_gia_ks')}}</h6>
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
                                    <table id="datable_1" class="table_res">
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
                                                        :to="{ path: 'system/user/edit', name: 'EditUserById', params: { user_id: user.id }, query: {type: type} }">
                                                    {{ user.username }}
                                                </router-link>
                                            </td>
                                            <td>{{ user.fullname }}
                                            </td>
                                            <td>{{ user.email }}</td>
                                            <td>{{ user.course_code }} - {{ user.course_name }}</td>
                                            <td class="text-center">
                                                <router-link :title="trans.get('keys.chi_tiet_ks')"
                                                             class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                             :to="{ name: 'SurveyResultUser', params: { survey_id: survey_id,user_id: user.id,course_id: user.course_id } }">
                                                    <span class="btn-icon-wrap"><i
                                                            class="fal fa-arrow-alt-right"></i></span>
                                                </router-link>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>

                                        </tfoot>
                                    </table>
                                    <v-pagination v-model="current_rs" @input="onPageChange"
                                                  :page-count="totalPages_rs"
                                                  :classes=$pagination.classes></v-pagination>


                                    <br/>
                                    <h6 class="hk-sec-title">
                                        {{trans.get('keys.danh_sach_nguoi_dung_xem_ks')}}</h6>
                                    <div class="row mb-3">
                                        <div class="col-2 dataTables_wrapper">
                                            <div class="dataTables_length"
                                                 style="display:block;">
                                                <label>{{trans.get('keys.hien_thi')}}
                                                    <select v-model="row_vs"
                                                            class="custom-select custom-select-sm form-control form-control-sm"
                                                            @change="getUserViewSurvey(1)">
                                                        <option value="5">5</option>
                                                        <option value="10">10</option>
                                                        <option value="50">50</option>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="datable_2" class="table_res">
                                        <thead>
                                        <tr>
                                            <th>{{trans.get('keys.stt')}}</th>
                                            <th>{{trans.get('keys.username')}}</th>
                                            <th class=" mobile_hide">
                                                {{trans.get('keys.ho_ten')}}
                                            </th>
                                            <th>{{trans.get('keys.email')}}</th>
                                            <th>{{trans.get('keys.total_view')}}</th>
                                            <th>{{trans.get('keys.ten_khoa_hoc')}}</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(user,index) in lstUserViews">
                                            <td>{{ (current_rs-1)*row_rs+(index+1) }}</td>
                                            <td>
                                                <router-link
                                                        :to="{ path: 'system/user/edit', name: 'EditUserById', params: { user_id: user.id }, query: {type: type} }">
                                                    {{ user.username }}
                                                </router-link>
                                            </td>
                                            <td>{{ user.fullname }}
                                            </td>
                                            <td>{{ user.email }}</td>
                                            <td>{{ user.total_view }}</td>
                                            <td>{{ user.course_code }} - {{ user.course_name }}</td>

                                        </tr>
                                        </tbody>
                                        <tfoot>

                                        </tfoot>
                                    </table>
                                    <v-pagination v-model="current_vs" @input="onPageChangeVS"
                                                  :page-count="totalPages_vs"
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
    import QuestionStatistic from "./template/StatisticQuestionComponent";
    import GroupQuestionStatistic from "./template/StatisticsGroupQuesComponent";
    import MinMaxQuestionStatistic from "./template/StatisticMinMaxQuesComponent"
    import CheckboxStatistic from "./template/StatisticCheckboxComponent"
    import Ls from './../../services/ls'
    import datePicker from 'vue-bootstrap-datetimepicker'

    export default {
        props: ['survey_id'],
        components: {
            QuestionStatistic,
            GroupQuestionStatistic,
            MinMaxQuestionStatistic,
            CheckboxStatistic,
            Ls,
            datePicker
        },
        data() {
            return {
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

                organization_id_1: 0,

                keyword: '',
                keyword_us: '',
                row: 5,
                current: 1,
                total_view: 0,
                totalPages: 1,
                survey_exam: [],

                course_id: '',

                startdate: '',
                enddate: '',
                base_url: '',
                chart_type: 'pie',

                keyword_rs: '',
                row_rs: 10,
                lstUsers: [],
                current_rs: 1,
                totalPages_rs: 1,
                type: '',

                lstUserViews: [],
                current_vs: 1,
                totalPages_vs: 1,
                row_vs: 10,

                startdate_us: '',
                enddate_us: '',

                date: new Date(),
                date_options: {
                    format: 'DD-MM-YYYY',
                    useCurrent: false,
                    showClear: true,
                    showClose: true,
                },
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
                        this.keyword = ui.item.label;

                        this.course_id = ui.item.id;

                        Ls.set('course', this.course_id);
                        Ls.set('course_data', ui.item.label);
                    },
                    change: function (e, ui) {
                        // alert(ui.item.value);

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

                        Ls.set('course', this.course_id);
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


            changeChartFormat(type) {
                this.chart_type = type;
                this.survey_exam = [];
                this.getStatictisSurveyExam();
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

            fetch() {
                axios.post('/bridge/fetch', {
                    view: 'SurveyStatistic'
                })
                    .then(response => {
                        this.base_url = response.data.base_url;
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },

            getPropertyName(arrData) {
                return Object.getOwnPropertyNames(arrData);
            },
            search() {
                this.getStatictisSurveyView();
                this.getStatictisSurveyExam();
            },
            getStatictisSurveyExam() {
                if (this.keyword === '' || this.keyword === null || this.keyword === undefined) {
                    Ls.set('course', '');
                    Ls.set('course_data', '');
                }
                this.course_id = Ls.get('course');

                axios.post('/api/survey/statistic_exam', {
                    survey_id: this.survey_id,
                    organization_id: this.organization.parent_id,
                    course_id: this.course_id,
                    startdate: this.startdate,
                    enddate: this.enddate
                })
                    .then(response => {
                        this.survey_exam = [];
                        var data_result = response.data;
                        var keys = this.getPropertyName(data_result);
                        var count_key = keys.length;
                        if (count_key > 0) {
                            for (var i = 0; i < count_key; i++) {
                                // var objData = data_result[keys[i]];
                                this.survey_exam.push(data_result[keys[i]]);
                            }
                        }

                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            getStatictisSurveyView() {
                if (this.keyword === '' || this.keyword === null || this.keyword === undefined) {
                    Ls.set('course', '');
                    Ls.set('course_data', '');
                }
                this.course_id = Ls.get('course');

                axios.post('/api/survey/statistic_view', {
                    survey_id: this.survey_id,
                    organization_id: this.organization.parent_id,
                    course_id: this.course_id,
                    startdate: this.startdate,
                    enddate: this.enddate
                }).then(response => {
                    this.total_view = response.data.length;
                })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            searchUser() {
                this.getUserSurvey();
                this.getUserViewSurvey();
            },
            onPageChange() {
                this.getUserSurvey();
            },
            getUserSurvey(paged) {
                if (this.keyword_us === '' || this.keyword_us === null || this.keyword_us === undefined) {
                    Ls.set('course', '');
                }
                this.course_id = Ls.get('course');
                axios.post('/api/survey/list_user_result', {
                    page: paged || this.current_rs,
                    survey_id: this.survey_id,
                    org_id: this.organization_id_1,
                    course_id: this.course_id,
                    keyword: this.keyword_rs,
                    row: this.row_rs,
                    startdate: this.startdate_us,
                    enddate: this.enddate_us
                }).then(response => {
                    this.lstUsers = response.data.data.data;
                    this.current_rs = response.data.pagination.current_page;
                    this.totalPages_rs = response.data.pagination.total;
                })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            onPageChangeVS() {
                this.getUserViewSurvey();
            },
            getUserViewSurvey(paged) {
                if (this.keyword_us === '' || this.keyword_us === null || this.keyword_us === undefined) {
                    Ls.set('course', '');
                }
                this.course_id = Ls.get('course');
                axios.post('/api/survey/user-view', {
                    page: paged || this.current_vs,
                    survey_id: this.survey_id,
                    org_id: this.organization_id_1,
                    course_id: this.course_id,
                    keyword: this.keyword_rs,
                    row: this.row_vs,
                    startdate: this.startdate_us,
                    enddate: this.enddate_us
                }).then(response => {
                    this.lstUserViews = response.data.data.data;
                    this.current_vs = response.data.pagination.current_page;
                    this.totalPages_vs = response.data.pagination.total;
                })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            exportFileData(type_file) {
                this.course_id = Ls.get('course');
                if (this.course_id === '' || this.course_id === null || this.course_id === undefined) {
                    toastr['warning'](this.trans.get('keys.chon_khoa_hoc'), this.trans.get('keys.that_bai'));
                    return;
                }

                var course_info = Ls.get('course_data');

                if (type_file === 'pdf') {
                    $('button.btn-pdf i').css("display", "inline-block");
                } else {
                    $('button.btn-excel i').css("display", "inline-block");
                }
                axios.post('/api/survey/export_file', {
                    survey_id: this.survey_id,
                    organization_id: this.organization.parent_id,
                    course_id: this.course_id,
                    course_info: course_info,
                    type_file: type_file,
                    startdate: this.startdate,
                    enddate: this.enddate
                })
                    .then(response => {
                        if (response.data.status) {
                            var a = $("<a>")
                                .prop("href", "/downloadexcelsurvey/" + type_file)
                                .appendTo("body");
                            a[0].click();
                            a.remove();

                            if (type_file === 'pdf') {
                                $('button.btn-pdf i').css("display", "none");
                            } else {
                                $('button.btn-excel i').css("display", "none");
                            }
                        }

                    })
                    .catch(error => {
                        if (type_file === 'pdf') {
                            $('button.btn-pdf i').css("display", "none");
                        } else {
                            $('button.btn-excel i').css("display", "none");
                        }
                    });
            }
        },
        mounted() {
            this.selectParent();
            this.getStatictisSurveyExam();
            this.getStatictisSurveyView();
            this.fetch();
            this.getCourseSelectOptions();
            this.getUserSurvey();
            this.getUserViewSurvey();
        }
    }
</script>

<style scoped>

</style>
