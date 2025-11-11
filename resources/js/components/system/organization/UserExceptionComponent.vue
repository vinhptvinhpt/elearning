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
                            <router-link to="/tms/organization">{{ trans.get('keys.to_chuc') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.them_nguoi_dung_ngoai_le') }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">

                    <div class="row mb-4" v-if="slug_can('tms-system-administrator-grant')">
                        <div class="col-sm">
                            <div class="accordion" id="accordion_1">
                                <div class="card" style="border-bottom: 1px solid rgba(0,0,0,.125);">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1"
                                           aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_nguoi_dung_ngoai_le')}}</a>
                                    </div>
                                    <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                                        <div class="card-body">
                                            <!-- view thêm mới course-->
                                            <div class="row">
                                                <div class="col-sm">
                                                    <div class="table-wrap">
                                                        <div class="row">
                                                            <div class="col-sm-4 dataTables_wrapper">
                                                                <div class="dataTables_length"
                                                                     style="display: inline-block;">
                                                                    <label>{{trans.get('keys.hien_thi')}}
                                                                        <select v-model="row_out"
                                                                                class="custom-select custom-select-sm form-control form-control-sm"
                                                                                @change="getUserOutTrainning(1)">
                                                                            <option value="10">10</option>
                                                                            <option value="25">25</option>
                                                                            <option value="50">50</option>
                                                                            <option value="100">100</option>
                                                                        </select>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <treeselect class="form-group"
                                                                            v-model="organization_id_1"
                                                                            :multiple="false" :options="options"/>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <form v-on:submit.prevent="getUserOutTrainning(1)">
                                                                    <div class="d-flex flex-row form-group">
                                                                        <input v-model="keyword_out" type="text"
                                                                               class="form-control search_text"
                                                                               :placeholder="trans.get('keys.nhap_ten_tai_khoan_email')+ ' ...'">
                                                                        <button type="button" id="btnFilter1"
                                                                                class="btn btn-primary btn-sm btn_fillter"
                                                                                @click="getUserOutTrainning(1)">
                                                                            {{trans.get('keys.tim')}}
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <div class="mt-10 mb-20">
                                                            <strong>
                                                                {{trans.get('keys.tong_so_nguoi_dung_hien_tai')}} : {{
                                                                total_out }}
                                                            </strong>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table_res">
                                                                <thead>
                                                                <tr>
                                                                    <th class=" mobile_hide">
                                                                        <input type="checkbox"
                                                                               v-model="allSelected"
                                                                               @click="selectAll()">
                                                                    </th>
                                                                    <th>{{trans.get('keys.stt')}}</th>
                                                                    <th>{{trans.get('keys.tai_khoan')}}</th>
                                                                    <th class=" mobile_hide">
                                                                        {{trans.get('keys.ten_nguoi_dung')}}
                                                                    </th>
                                                                    <th class=" mobile_hide">
                                                                        {{trans.get('keys.email')}}
                                                                    </th>

                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <template v-if="users_out_trainning.length === 0">
                                                                    <tr>
                                                                        <td colspan="8">
                                                                            {{trans.get('keys.khong_tim_thay_du_lieu')}}
                                                                        </td>
                                                                    </tr>
                                                                </template>
                                                                <template v-else>
                                                                    <tr v-for="(user,index) in users_out_trainning">
                                                                        <td>
                                                                            <input type="checkbox" :value="user.user_id"
                                                                                   v-model="userTrainning"
                                                                                   @change="onCheckboxTrainning()"/>

                                                                        </td>
                                                                        <td>{{ (current-1)*row+(index+1) }}</td>
                                                                        <td>
                                                                            <router-link
                                                                                    :to="{ name: 'EditUserById', params: { user_id: user.user_id,type:'system' } }">
                                                                                {{ user.username }}
                                                                            </router-link>
                                                                        </td>

                                                                        <td class=" mobile_hide">{{ user.fullname }}
                                                                        </td>
                                                                        <td class=" mobile_hide">{{ user.email }}</td>


                                                                    </tr>
                                                                </template>

                                                                </tbody>
                                                            </table>

                                                            <v-pagination v-model="current_out" @input="onPageChange"
                                                                          :page-count="totalPagesOut"
                                                                          :classes=$pagination.classes
                                                                          :labels=$pagination.labels></v-pagination>

                                                            <div class="text-left">
                                                                <button type="button" id="btnExportPdf"
                                                                        style="float: right; position: relative;"
                                                                        class="btn btn-sm btn-success mt-3 btn-pdf"
                                                                        @click="addToTrainning()">
                                                                    {{trans.get('keys.them_nguoi_dung_ngoai_le')}} <i
                                                                        class="fa fa-spinner"
                                                                        aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="hk-sec-title">{{trans.get('keys.quan_tri_giang_vien')}}</h5>
                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div class="row">
                                    <div class="col-sm-8 dataTables_wrapper">
                                        <div class="dataTables_length" style="display: inline-block;">
                                            <label>{{trans.get('keys.hien_thi')}}
                                                <select v-model="row" style="height: 35px"
                                                        class="custom-select custom-select-sm form-control form-control-sm"
                                                        @change="getUser(1)">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="col-4"
                                             style="width: auto; height: 35px; display: inline-block; position: absolute;">
                                            <label>
                                                <treeselect v-model="organization_id1"
                                                            :multiple="false" :options="optionsOrganize"
                                                            @input="getUser(1)"/>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <form v-on:submit.prevent="getUser(1)">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword" type="text"
                                                       class="form-control search_text"
                                                       :placeholder="trans.get('keys.nhap_ten_tai_khoan_email')+ ' ...'">
                                                <button type="button" id="btnFilter"
                                                        class="btn btn-primary btn-sm btn_fillter"
                                                        @click="getUser(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-10 mb-20">
                                    <strong>
                                        {{trans.get('keys.tong_so_nguoi_dung_hien_tai')}} : {{ total_user }}
                                    </strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table_res">
                                        <thead>
                                        <tr>
                                            <th class="text-center" v-if="slug_can('tms-system-administrator-grant')">
                                                <input type="checkbox"
                                                       v-model="allSelected_user"
                                                       @click="selectAllUserDel()">
                                            </th>
                                            <th>{{trans.get('keys.stt')}}</th>
                                            <th>{{trans.get('keys.tai_khoan')}}</th>
                                            <th class=" mobile_hide">{{trans.get('keys.ten_nguoi_dung')}}</th>
                                            <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        <template v-if="posts.length === 0">
                                            <tr>
                                                <td colspan="8">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr v-if="user" v-for="(user,index) in posts">
                                                <td class="text-center"
                                                    v-if="slug_can('tms-system-administrator-grant')">
                                                    <input type="checkbox" :value="user.user_id"
                                                           v-model="userDels"
                                                           @change="onCheckboxUserDel()"/>
                                                </td>
                                                <td>{{ (current-1)*row+(index+1) }}</td>
                                                <td>
                                                    <router-link
                                                            :to="{ name: 'EditUserById', params: { user_id: user.user_id,type:'system' } }">
                                                        {{ user.username }}
                                                    </router-link>
                                                </td>

                                                <td class=" mobile_hide">{{ user.fullname }}</td>
                                                <td class=" mobile_hide">{{ user.email }}</td>

                                                <td class="text-center">
                                                    <button v-if="slug_can('tms-system-administrator-grant')"
                                                            @click="remove_trainning(user.user_id,org_id)"
                                                            class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 btn_open_select"
                                                            type="button">
                                                        <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                                                    </button>
                                                    <button v-else
                                                            class="btn disabled btn-sm btn-icon btn-icon-circle btn-grey btn-icon-style-2"
                                                            type="button">
                                                        <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>

                                        </tbody>
                                    </table>

                                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                                  :classes=$pagination.classes
                                                  :labels=$pagination.labels></v-pagination>

                                    <div class="text-left">
                                        <button type="button" id="btnExportExcel"
                                                style="float: right; position: relative;"
                                                class="btn btn-sm btn-danger mt-3 btn-pdf"
                                                @click="removeMulti()">
                                            {{trans.get('keys.xoa_nguoi_dung_ngoai_le')}} <i
                                                class="fa fa-spinner"
                                                aria-hidden="true"></i>
                                        </button>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>


</template>

<script>
    export default {
        props: ['org_id', 'slugs'],
        components: {},
        data() {
            return {
                posts: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                total_user: 0,
                row: 10,
                users_out_trainning: [],
                row_out: 10,
                total_out: 0,
                current_out: 1,
                keyword_out: '',
                totalPagesOut: 0,
                allSelected: false,
                userTrainning: [],
                tree_placeholder: {
                    id: 0,
                    label: this.trans.get('keys.chon_to_chuc')
                },
                tree_options: [],
                organization_parent_list: [],
                organization_id: 0,
                //Treeselect options
                options: [],
                organization_id_1: 0,
                optionsOrganize: [
                    {
                        id: 0,
                        label: this.trans.get('keys.chon_to_chuc')
                    }
                ],
                organization_id1: 0,
                trainning: {
                    code: '',
                    name: '',
                    style: 1,
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

                userDels: [],
                allSelected_user: false,
            }
        },
        methods: {
            slug_can(permissionName) {
                return this.slugs.indexOf(permissionName) !== -1;
            },
            selectAllUserDel() {
                this.userDels = [];
                this.allSelected_user = !this.allSelected_user;
                if (this.allSelected_user) {
                    var countEnrol = this.posts.length;
                    if (countEnrol > 0) {
                        for (var i = 0; i < countEnrol; i++) {
                            this.userDels.push(this.posts[i].user_id.toString());
                        }
                    }
                }
                console.log(JSON.stringify(this.userDels));
            },
            onCheckboxUserDel() {
                this.allSelected_user = false;
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
                        this.tree_options.unshift(this.tree_placeholder);
                    })
                    .catch(error => {

                    })
            },
            setOptionsTreeView(list) {
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
            removeMulti() {
                let current_pos = this;
                let count = this.userDels.length;
                if (count === 0) {
                    toastr['warning'](current_pos.trans.get('keys.chua_chon_user'), current_pos.trans.get('keys.thong_bao'));
                    return;
                }
                $('button.btn-pdf i').css("display", "inline-block");
                let loader = $('.preloader-it');
                loader.fadeIn();
                axios.post('/api/organization/remove-multi-user-org', {
                    Users: this.userDels,
                    current_org_id: this.org_id,
                })
                    .then(response => {
                        loader.fadeOut();
                        if (response.data.status) {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            current_pos.getUser(current_pos.current);
                            current_pos.getUserOutTrainning(current_pos.current_out);
                            this.allSelected_user = false;
                        } else {
                            toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                        }
                        $('button.btn-pdf i').css("display", "none");
                    })
                    .catch(error => {
                        loader.fadeOut();
                        $('button.btn-pdf i').css("display", "none");
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });
                //reset selected array
                this.userDels = [];
            },
            addToTrainning() {
                let current_pos = this;
                let count = this.userTrainning.length;
                if (count === 0) {
                    toastr['warning'](current_pos.trans.get('keys.chua_chon_user'), current_pos.trans.get('keys.thong_bao'));
                    return;
                }
                $('button.btn-pdf i').css("display", "inline-block");
                let loader = $('.preloader-it');
                loader.fadeIn();
                axios.post('/api/organization/add-user-org', {
                    Users: this.userTrainning,
                    current_org_id: this.org_id,
                })
                    .then(response => {
                        loader.fadeOut();
                        if (response.data.status) {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            current_pos.getUser(current_pos.current);
                            current_pos.getUserOutTrainning(current_pos.current_out);
                            this.allSelected = false;
                        } else {
                            toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                        }
                        $('button.btn-pdf i').css("display", "none");
                    })
                    .catch(error => {
                        loader.fadeOut();
                        $('button.btn-pdf i').css("display", "none");
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });
                //reset selected array
                this.userTrainning = [];
            },
            selectAll: function () {
                this.userTrainning = [];
                this.allSelected = !this.allSelected;
                if (this.allSelected) {
                    var countEnrol = this.users_out_trainning.length;
                    if (countEnrol > 0) {
                        for (var i = 0; i < countEnrol; i++) {
                            this.userTrainning.push(this.users_out_trainning[i].user_id.toString());
                        }
                    }
                }
            },
            onCheckboxTrainning() {
                this.allSelected = false;
            },
            remove_trainning(user_id, org_id) {

                let current_pos = this;

                swal({
                    title: current_pos.trans.get('keys.thong_bao'),
                    text: current_pos.trans.get('keys.ban_muon_thuc_hien_thao_tac_nay'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/api/organization/remove-user-org', {
                        user_id: user_id,
                        current_org_id: org_id,
                    })
                        .then(response => {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            $('.btn_open_select.actives').trigger('click');
                            if (current_pos.posts.length === 1) {
                                current_pos.current = current_pos.current > 1 ? current_pos.current - 1 : 1;
                            }
                            current_pos.onPageChange();
                        })
                        .catch(error => {
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });
            },
            getUserOutTrainning(page) {
                axios.post('/api/organization/get-user-without-org', {
                    page: page || this.current_out,
                    keyword: this.keyword_out,
                    row: this.row_out,
                    current_org_id: this.org_id,
                    organization_id: this.organization_id_1
                })
                    .then(response => {
                        this.users_out_trainning = response.data.data ? response.data.data.data : [];
                        this.current_out = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPagesOut = response.data.pagination ? response.data.pagination.total : 0;
                        this.total_out = response.data.pagination ? response.data.pagination.total_user : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getUser(paged) {
                axios.post('/api/organization/get-user-org', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    current_org_id: this.org_id,
                    organization_id: this.organization_id1
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
                        this.total_user = response.data.pagination ? response.data.pagination.total_user : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            selectOrganization(current_id) {
                $('.content_search_box').addClass('loadding');
                axios.post('/organization/list', {
                    keyword: this.organization_keyword,
                    level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
                    paginated: 0 //không phân trang
                })
                    .then(response => {
                        this.organization_list = response.data;
                        //Set options recursive
                        this.options = this.setOptions(response.data, current_id);
                        this.options.unshift(this.tree_placeholder);
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                    })
            },
            setOptions(list, current_id) {
                let outPut = [];
                for (const [key, item] of Object.entries(list)) {
                    let newOption = {
                        id: item.id,
                        label: item.name,
                    };
                    if (item.children.length > 0) {
                        for (const [key, child] of Object.entries(item.children)) {
                            if (child.id === current_id) {
                                newOption.isDefaultExpanded = true;
                                break;
                            }
                        }
                        newOption.children = this.setOptions(item.children, current_id);
                    }
                    outPut.push(newOption);
                }
                return outPut;
            },
            onPageChange() {
                this.getUser();
                this.getUserOutTrainning();
            },
            getParamsBackPage() {
                return this.$route.params.back_page;
            },
            setParamsBackPage(value) {
                this.$route.params.back_page = value;
            },
            selectOrganization1(current_id) {
                $('.content_search_box').addClass('loadding');
                axios.post('/organization/list', {
                    keyword: this.organization_keyword,
                    level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
                    paginated: 0 //không phân trang
                })
                    .then(response => {
                        this.organization_list = response.data;
                        //Set options recursive
                        this.optionsOrganize = this.setOptions(response.data, current_id);
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                    })
            }
        },
        mounted() {
            this.listOrganization();
            this.selectOrganization();
            this.selectOrganization1();
            // this.getDetailTrainning();
        },
        destroyed() {
            sessionStorage.setItem('userListPage', this.current);
            sessionStorage.setItem('userListPageSize', this.row);
            sessionStorage.setItem('userListKeyWord', this.keyword);
        }
    }
</script>

<style scoped>
</style>
