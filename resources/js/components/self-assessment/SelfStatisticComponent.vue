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

                            <div class="col-10 form-group">
                                <button type="button" id="btnExportExcel" style="float: right;"
                                        class="btn-sm btn-excel btn-primary d-none d-lg-block"
                                        @click="exportFileData('excel')">
                                    {{trans.get('keys.xuat_file_excel')}} <i class="fa fa-spinner"
                                                                             aria-hidden="true"></i>
                                </button>
                            </div>

                        </div>
                        <p style="color: red;font-style: italic;">
                            {{trans.get('keys.chu_y_click_moi_hang_de_xem_chi_tiet')}}</p>
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
                            <tbody v-for="(user,index) in lstUsers">
                            <tr @click="getPointOfSection(user.user_id,user.course_id,'self_user_' + user.tsu_id)">
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
                                    <a v-if="selected_role === 'root' || selected_role === 'admin'" href="javascript(0)"
                                       @click.prevent="deletePost('/api/self-assessment-result/delete/'+user.tsu_id)"
                                       :title="trans.get('keys.xoa_ket_qua')"
                                       class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 delete-user">
                                        <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                                    </a>
                                </td>
                            </tr>
                            <tr class="hidden self-user-content" :id="'self_user_' + user.tsu_id"
                                style="display: none;">
                                <td></td>
                                <td colspan="5">
                                    <table class="table_res">
                                        <thead>
                                        <tr>
                                            <th style="width: 15%; color: #007bff;">
                                                {{trans.get('keys.ques_name')}}
                                            </th>
                                            <th style="width: 30%; color: #007bff;">
                                                {{trans.get('keys.ques_title')}}
                                            </th>
                                            <th style="width: 15%; color: #007bff;">
                                                {{trans.get('keys.sec_name')}}
                                            </th>
                                            <th style="width: 15%; color: #007bff;">
                                                {{trans.get('keys.total_point')}}
                                            </th>
                                            <th style="width: 15%; color: #007bff;">
                                                {{trans.get('keys.avg_point')}}
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(sur,index) in points">
                                            <td>{{ sur.ques_name }}</td>
                                            <td>
                                                <div v-html="sur.ques_content"
                                                     style="font-weight: normal;"></div>
                                            </td>
                                            <td>{{ sur.section_name }}</td>
                                            <td>{{ sur.total_point }}</td>
                                            <td>{{ sur.avg_point.toFixed(2) }}</td>

                                        </tr>
                                        </tbody>
                                        <tfoot>

                                        </tfoot>
                                    </table>
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

</template>

<script>
    import Ls from './../../services/ls'

    export default {
        props: {
            current_roles: Object,
            roles_ready: Boolean,
            self_id: {
                type: [String, Number],
                default: ''
            },
        },
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

                type: '',

                points: [],
                selected_role: 'user',
            }
        },
        methods: {
            exportFileData(type_file) {
                this.course_id = Ls.get('course-self');
                if (this.course_id === '' || this.course_id === null || this.course_id === undefined) {
                    toastr['warning'](this.trans.get('keys.chon_khoa_hoc'), this.trans.get('keys.that_bai'));
                    return;
                }

                var course_info = Ls.get('course-self-data');

                if (type_file === 'pdf') {
                    $('button.btn-pdf i').css("display", "inline-block");
                } else {
                    $('button.btn-excel i').css("display", "inline-block");
                }

                axios.post('/api/self/export-file', {
                    self_id: this.self_id,
                    organization_id: this.organization_id_1,
                    course_id: this.course_id,
                    course_info: course_info,
                    type_file: type_file
                })
                    .then(response => {
                        if (response.data.status) {
                            var a = $("<a>")
                                .prop("href", "/api/self/download-file/" + type_file)
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
            },
            getPointOfSection(user_id, course_id, tag_id) {

                if ($('#' + tag_id + ':visible').length)
                    $('#' + tag_id).hide(500);
                else {
                    axios.post('/api/self/point-of-section', {
                        self_id: this.self_id,
                        user_id: user_id,
                        course_id: course_id
                    })
                        .then(response => {
                            this.points = response.data;
                            $('.self-user-content').hide(100);
                            $('#' + tag_id).show(500);
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }
            },
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
                        // this.loadAutoComplete();
                        this.loadAutoCompleteUser();
                    })
                    .catch(error => {
                        //console.log(error.response.data);
                    });
            },
            // loadAutoComplete() {
            //     $("#tags").autocomplete({
            //         source: this.lightWell,
            //         minLength: 2,
            //         select: function (e, ui) {
            //             this.keywordc = ui.item.label;
            //
            //             this.course_id = ui.item.id;
            //
            //             Ls.set('course-self', this.course_id);
            //         },
            //         change: function (e, ui) {
            //         }
            //     });
            // },
            loadAutoCompleteUser() {
                $("#tag-users").autocomplete({
                    source: this.lightWell,
                    minLength: 2,
                    select: function (e, ui) {
                        this.keyword_us = ui.item.label;

                        this.course_id = ui.item.id;

                        Ls.set('course-self', this.course_id);
                        Ls.set('course-self-data', ui.item.label);
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
            // getSelfStatistic(paged) {
            //
            //     if (this.keywordc === '' || this.keywordc === null || this.keywordc === undefined) {
            //         Ls.set('course-self', '');
            //     }
            //     this.course_id = Ls.get('course-self');
            //
            //     axios.post('/api/self/statistic', {
            //         self_id: this.self_id,
            //         course_id: this.course_id,
            //         organization_id: this.organization.parent_id,
            //         page: paged || this.current,
            //         keyword: this.keyword,
            //         row: this.row,
            //     })
            //         .then(response => {
            //             this.surveys = response.data.data.data;
            //             this.current = response.data.pagination.current_page;
            //             this.totalPages = response.data.pagination.total;
            //         })
            //         .catch(error => {
            //             console.log(error.response.data);
            //         });
            // },
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
            // onPageChange() {
            //     this.getSelfStatistic();
            // },
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
            },
            getRoleFromCurrentRoles(current_roles) {
                if (current_roles.root_user === true) {
                    this.selected_role = 'root';
                } else if (current_roles.has_role_admin === true) {
                    this.selected_role = 'admin';
                } else if (current_roles.has_role_manager === true) {
                    this.selected_role = 'manager';
                } else if (current_roles.has_role_leader === true) {
                    this.selected_role = 'leader';
                } else if (current_roles.has_user_market === true) {
                    this.selected_role = 'user_market';
                } else {
                    this.selected_role = 'user';
                }
            },
            deletePost(url) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_co_muon_xoa_ket_qua_nay'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post(url)
                        .then(response => {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            if(current_pos.lstUsers.length === 1){
                                current_pos.current_rs = current_pos.current_rs > 1 ? current_pos.current_rs -1 : 1 ;
                            }
                            current_pos.onPageChangeUS();
                            //reload assign batch
                            current_pos.assignBatch += 1;
                        })
                        .catch(error => {
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });

                return false;
            },
        },
        mounted() {
            this.selectParent();
            this.getCourseSelectOptions();
            this.getUserSurvey();
        },
        watch: {
          roles_ready: function(newVal, oldVal) {
            if (newVal === true && oldVal === false) {
                this.getRoleFromCurrentRoles(this.current_roles);
                console.log(this.selected_role);
            }
          }
        }
    }
</script>

<style scoped>

</style>
