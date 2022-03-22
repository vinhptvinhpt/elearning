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
                            <router-link v-if="trainning.style == 1"
                                         :to="{ path: '/tms/trainning/list',
                                  name: 'TrainningIndex',
                                  query: { type: trainning.style } }">
                                {{ trans.get('keys.quan_tri_khung_nang_luc') }}
                            </router-link>
                            <router-link v-else-if="trainning.style == 2"
                                         :to="{ path: '/tms/trainning/group',
                                  name: 'TrainningGroupIndex',
                                  query: { type: trainning.style } }">
                                {{ trans.get('keys.quan_tri_khung_nang_luc') }}
                            </router-link>
                            <router-link v-else-if="trainning.style == 0"
                                         :to="{ path: '/tms/trainning/certification',
                                   name: 'TrainningCertificationIndex',
                                   query: { type: trainning.style } }">
                                {{ trans.get('keys.quan_tri_khung_nang_luc') }}
                            </router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_thong_tin_knl') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-sm">
                <div class="accordion" id="accordion_1">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <a role="button" data-toggle="collapse" href="#collapse_1"
                               aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.sua_thong_tin_knl')}}</a>
                        </div>
                        <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
                            <div class="card-body">

                                <div v-if="slug_can('tms-system-administrator-grant')" class="row">
                                    <div class="col-12 col-lg-12">
                                        <form action="">
                                            <div class="row">
                                                <div class="col-12 col-lg-3 d-none">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <input type="file" ref="file" name="file" class="dropify"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-row">
                                                        <div class="col-sm-6 form-group">
                                                            <label for="inputText1-2">{{trans.get('keys.ma_knl')}}
                                                                *</label>
                                                            <input v-model="trainning.code" type="text"
                                                                   id="inputText1-2"
                                                                   :placeholder="trans.get('keys.ma_knl')"
                                                                   class="form-control">
                                                            <label v-if="!trainning.code"
                                                                   class="required text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>
                                                        <div class="col-sm-6 form-group">
                                                            <label for="inputText1-1">{{trans.get('keys.ten_knl')}}
                                                                *</label>
                                                            <input v-model="trainning.name" type="text"
                                                                   id="inputText1-1"
                                                                   :placeholder="trans.get('keys.ten_knl')"
                                                                   class="form-control">
                                                            <label v-if="!trainning.name"
                                                                   class="required text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-row" v-if="trainning.style != 2">
                                                        <div class="col-sm-6 form-group">

                                                            <label for="inputRole">{{trans.get('keys.quyen')}}</label>

                                                            <select v-model="trainning.role_id"
                                                                    class="form-control selectpicker" id="inputRole"
                                                                    autocomplete="false">
                                                                <option value="0">{{trans.get('keys.chon_vai_tro')}}
                                                                </option>
                                                                <option v-for="role in roles" :value="role.id">{{
                                                                    trans.has('keys.' + role.name) ? trans.get('keys.' +
                                                                    role.name) : role.name.charAt(0).toUpperCase() +
                                                                    role.name.slice(1) }}
                                                                </option>
                                                            </select>

                                                        </div>
                                                        <div class="col-sm-6 form-group">
                                                            <label>{{trans.get('keys.them_co_cau_to_chuc')}}</label>
                                                            <treeselect v-model="trainning.organization_id"
                                                                        :multiple="false" :options="tree_options"
                                                                        id="organization_parent_id"/>
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="col-sm-12 form-group">
                                                            <label for="inputDescription">{{trans.get('keys.mo_ta')}}</label>
                                                            <textarea class="form-control" rows="3"
                                                                      v-model="trainning.description"
                                                                      id="inputDescription"
                                                                      :placeholder="trans.get('keys.noi_dung')"></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="form-row"
                                                         v-if="trainning.style === 0 || trainning.style === 1">
                                                        <div class="col-12">
                                                            <label for="inputText1-1">{{trans.get('keys.thoi_gian_hoan_thanh')}}</label>
                                                        </div>
                                                        <div class="col-sm-6 form-group">
                                                            <date-picker v-model="trainning.time_start"
                                                                         :config="options"
                                                                         :placeholder="trans.get('keys.ngay_bat_dau')+' *'"></date-picker>
                                                            <label v-if="trainning.style == 1 && !trainning.time_start"
                                                                   class="required text-danger time_start_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>
                                                        <div class="col-sm-6 form-group">
                                                            <date-picker v-model="trainning.time_end" :config="options"
                                                                         :placeholder="trans.get('keys.ngay_ket_thuc')"></date-picker>
                                                        </div>
                                                        <!--                            <div class="col-sm-6 form-group">-->
                                                        <!--                              <p id="logic-warning" class="text-danger code_error hide">-->
                                                        <!--                                {{trans.get('keys.vui_long_nhap_ngay_bat_dau_nho_hon_hoac_bang_ngay_ket_thuc')}}</p>-->
                                                        <!--                            </div>-->
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="col-sm-4 form-group"
                                                             v-if="trainning.style === 0 || trainning.style === 1">
                                                            <label>{{trans.get('keys.tu_dong_chay_cron')}}</label>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="run_cron"
                                                                       :checked="trainning.run_cron==1?true:false"
                                                                       v-model="trainning.run_cron">
                                                                <label v-if="trainning.run_cron == 1"
                                                                       class="custom-control-label"
                                                                       for="run_cron">Yes</label>
                                                                <label v-else class="custom-control-label"
                                                                       for="run_cron">No</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 form-group">
                                                            <label>{{trans.get('keys.tu_dong_cap_chung_chi')}}</label>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="auto_certificate"
                                                                       :checked="trainning.auto_certificate==1?true:false"
                                                                       v-model="trainning.auto_certificate">
                                                                <label v-if="trainning.auto_certificate == 1"
                                                                       class="custom-control-label"
                                                                       for="auto_certificate">Yes</label>
                                                                <label v-else class="custom-control-label"
                                                                       for="auto_certificate">No</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 form-group">
                                                            <label>{{trans.get('keys.tu_dong_cap_huy_hieu')}}</label>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="auto_badge"
                                                                       :checked="trainning.auto_badge==1?true:false"
                                                                       v-model="trainning.auto_badge">
                                                                <label v-if="trainning.auto_badge == 1"
                                                                       class="custom-control-label"
                                                                       for="auto_badge">Yes</label>
                                                                <label v-else class="custom-control-label"
                                                                       for="auto_badge">No</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="col-12 form-group">
                                                            <div class="button-list text-right">
                                                                <router-link v-if="trainning.style == 1"
                                                                             :to="{ path: '/tms/trainning/list',
                                  name: 'TrainningIndex',
                                  params:{back_page: '1'},
                                  query: { type: trainning.style } }"
                                                                             class="btn btn-danger btn-sm">
                                                                    {{ trans.get('keys.huy') }}
                                                                </router-link>
                                                                <router-link v-else-if="trainning.style == 2"
                                                                             :to="{ path: '/tms/trainning/group',
                                  name: 'TrainningGroupIndex',
                                  params:{back_page: '1'},
                                  query: { type: trainning.style } }"
                                                                             class="btn btn-danger btn-sm">
                                                                    {{ trans.get('keys.huy') }}
                                                                </router-link>
                                                                <router-link v-else-if="trainning.style == 0"
                                                                             :to="{ path: '/tms/trainning/certification',
                                   name: 'TrainningCertificationIndex',
                                   params:{back_page: '1'},
                                   query: { type: trainning.style } }"
                                                                             class="btn btn-danger btn-sm">
                                                                    {{ trans.get('keys.huy') }}
                                                                </router-link>
                                                                <button @click="editTrainning()" type="button"
                                                                        class="btn btn-primary btn-sm">
                                                                    {{trans.get('keys.sua')}}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <!--                      <div class="form-row">-->
                                            <!--                        <div class="col-sm-6 form-group">-->
                                            <!--                          <label>{{trans.get('keys.khung_nang_luc_theo_tg')}}</label>-->
                                            <!--                          <div class="custom-control custom-switch">-->
                                            <!--                            <input type="checkbox" class="custom-control-input" id="style"-->
                                            <!--                                   :checked="trainning.style==1?true:false" v-model="trainning.style">-->
                                            <!--                            <label v-if="trainning.style == 1" class="custom-control-label" for="style">{{trans.get('keys.khung_nang_luc_hoan_thanh_trong_khoang_tg')}}</label>-->
                                            <!--                            <label v-else class="custom-control-label" for="style">{{trans.get('keys.khung_nang_luc_khong_gioi_han_tg')}}</label>-->
                                            <!--                          </div>-->
                                            <!--                        </div>-->
                                            <!--                      </div>-->


                                        </form>
                                    </div>
                                </div>
                                <br/>

                                <clone-course v-if="trainning.style === 0 || trainning.style === 1" :id="id"
                                              :type="trainning.style" :trainning_code="trainning.code"></clone-course>
                                <assign-course v-if="trainning.style === 2" :id="id"
                                               :type="trainning.style"></assign-course>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>

<script>
    import datePicker from 'vue-bootstrap-datetimepicker'
    import CloneCourse from "./CloneCourseComponent";
    import AssignCourse from "./AssgignCourseComponent";


    export default {
        props: ['id', 'slugs'],
        components: {
            datePicker,
            CloneCourse,
            AssignCourse
        },
        data() {
            return {
                trainning: {
                    code: '',
                    name: '',
                    style: -1,
                    role_id: 0,
                    organization_id: 0,
                    run_cron: 1,
                    auto_certificate: 1,
                    auto_badge: 1,
                    time_start: '',
                    time_end: '',
                    logo: '',
                    description: ''
                },


                date: new Date(),
                organization: {
                    name: '',
                    code: '',
                    parent_id: 0,
                    description: '',
                },
                options: {
                    format: 'DD-MM-YYYY',
                    useCurrent: false,
                    showClear: true,
                    showClose: true,
                },
                roles: [],
                //Treeselect options
                tree_options: [
                    {
                        id: 0,
                        label: this.trans.get('keys.chon_to_chuc')
                    }
                ],
                organization_parent_list: [],

                // list: message.map((name, index) => {
                //   return { name, order: index + 1 };
                // }),
            }
        },
        methods: {
            slug_can(permissionName) {
                return this.slugs.indexOf(permissionName) !== -1;
            },
            getRoles() {
                axios.post('/system/user/list_role', {
                    type: 'role'
                })
                    .then(response => {
                        this.roles = response.data;
                        this.$nextTick(function () {
                            $('.selectpicker').selectpicker('refresh');
                        });
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            listOrganization() {
                axios.post('/organization/list', {
                    keyword: this.parent_keyword,
                    level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
                    paginated: 0 //không phân trang,
                })
                    .then(response => {
                        this.organization_parent_list = response.data;
                        //Set options recursive
                        this.tree_options = this.setOptions(response.data);
                    })
                    .catch(error => {

                    })
            },
            setOptions(list) {
                let outPut = [];
                for (const [key, item] of Object.entries(list)) {
                    let newOption = {
                        id: item.id,
                        label: item.code
                    };
                    if (item.children.length > 0) {
                        newOption.children = this.setOptions(item.children);
                    }
                    outPut.push(newOption);
                }
                return outPut;
            },
            getDetailTrainning() {
                axios.get('/api/trainning/detail/' + this.id)
                    .then(response => {
                        this.trainning = response.data;
                        this.$nextTick(function () {
                            $('.selectpicker').selectpicker('refresh');
                        });
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            editTrainning() {

                if (!this.trainning.code) {
                    $('.code_required').show();
                    return;
                }

                if (!this.trainning.name) {
                    $('.name_required').show();
                    return;
                }

                if (this.trainning.style == 1 && !this.trainning.time_start) {
                    $('.time_start_required').show();
                    return;
                }
                if (this.trainning.style == 1) {
                    // $('#logic-warning').hide();
                    let has_startdate = false;
                    let has_enddate = false;
                    if (this.trainning.time_start !== null && this.trainning.time_start !== undefined) {
                        has_startdate = true;
                    }
                    if (this.trainning.time_end !== null && this.trainning.time_end !== undefined) {
                        has_enddate = true;
                    }
                    if (has_startdate && has_enddate) {
                        let startDate_stamp = Date.parse(new Date(this.trainning.time_start.split("-").reverse().join("-")));
                        let endDate_stamp = Date.parse(new Date(this.trainning.time_end.split("-").reverse().join("-")));

                        if (startDate_stamp > endDate_stamp) {
                            // $('#logic-warning').show();
                            toastr['error'](this.trans.get('keys.vui_long_nhap_ngay_bat_dau_nho_hon_hoac_bang_ngay_ket_thuc'), this.trans.get('keys.thong_bao'));
                            return;
                        }
                        // else {
                        //   $('#logic-warning').hide();
                        // }
                    }
                }

                this.formData = new FormData();
                this.formData.append('id', this.id);
                this.formData.append('code', this.trainning.code);
                this.formData.append('name', this.trainning.name);
                this.formData.append('style', this.trainning.style);
                var auto_certificate = this.trainning.auto_certificate ? 1 : 0;
                this.formData.append('auto_certificate', auto_certificate);
                var auto_badge = this.trainning.auto_badge ? 1 : 0;
                this.formData.append('auto_badge', auto_badge);
                var run_cron = this.trainning.run_cron ? 1 : 0;
                this.formData.append('run_cron', run_cron);
                this.formData.append('description', this.trainning.description ? this.trainning.description : '');
                this.formData.append('time_start', this.trainning.time_start);
                this.formData.append('time_end', this.trainning.time_end);
                this.formData.append('role_id', this.trainning.role_id ? this.trainning.role_id : 0);
                this.formData.append('organization_id', this.trainning.organization_id ? this.trainning.organization_id : 0);
                this.formData.append('file', this.$refs.file.files[0]);

                axios.post('/api/trainning/update', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                        } else {
                            toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
                    });
            },
            setFileInput() {
                $('.dropify').dropify();
            }
        },
        mounted() {
            this.getDetailTrainning();
            this.listOrganization();
            this.getRoles();
            //this.setFileInput(); //Not work
        },
        updated() {
            //this.setFileInput(); //Not work
        }
    }
</script>

<style scoped>
    .flip-list-move {
        transition: transform 0.5s;
    }

    .no-move {
        transition: transform 0s;
    }

    .ghost {
        opacity: 0.5;
        background: #c8ebfb;
    }

    .list-group {
        width: 100%;
        min-height: 20px;
    }

    .list-group-item {
        cursor: move;
        border-top-width: 1px;
    }

    .list-group-item i {
        cursor: pointer;
    }

    .action-order {
        display: block;
        margin: 20px 0;
    }

    .selectpicker {
        display: block !important;
    }
</style>
