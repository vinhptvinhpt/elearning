<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.quyen_he_thong') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="accordion" id="accordion_1">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1"
                                   aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
                            </div>
                            <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                                <div class="card-body">
                                    <label class="hide output error"></label>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-line"
                                                           :placeholder="trans.get('keys.ten_quyen')+' *'"
                                                           v-model="role.name">
                                                </div>
                                                <label class="text-danger hide name error"></label>
                                                <label v-if="!role.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <textarea :placeholder="trans.get('keys.mo_ta') + ' *'"
                                                              class="form-control form-control-line"
                                                              v-model="role.description"></textarea>
                                                </div>
                                                <label class="text-danger hide description error"></label>
                                                <label v-if="!role.description"
                                                       class="text-danger description_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                          <input type="checkbox" id="is_organization_role" v-model="role.is_organization_role" style="width:20px; height:20px;">
                                          <label for="is_organization_role">{{trans.get('keys.su_dung_cho_to_chuc')}}</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group text-right">
                                                <button type="button" class="btn btn-primary btn-sm"
                                                        @click="createRole()">{{trans.get('keys.them_quyen')}}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <!--Role list-->
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-20">{{trans.get('keys.danh_sach_quyen')}}</h4>
                            <p class="mb-10">{{trans.get('keys.tong_so_quyen_hien_tai')}} : {{roles.length}}
                                {{trans.get('keys.quyen')}}.</p>
                            <div class="table-responsive">
                                <button class="hide btn_fillter" id="btn-filter" @click="listRoles()"></button>
                                <table class="table_res">
                                    <thead>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.ten_quyen')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.mo_ta')}}</th>
                                    <th>{{trans.get('keys.hanh_dong')}}</th>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(ro,index) in roles">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ ro.name === 'teacher' ? 'creator' : ro.name }}</td>
                                        <td class=" mobile_hide">{{ ro.description }}</td>
                                        <td>

                                            <router-link :title="trans.get('keys.sua')"
                                                         class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                         :to="{ name: 'RoleEdit', params: { role_id: ro.id }}">
                                                <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                            </router-link>

                                            <button v-if="ro.status === 0" @click="deleteRole(ro.id)"
                                                    class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2"
                                                    :title="trans.get('keys.xoa')">
                                                <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                                            </button>

                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--Content access permission-->
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-20">{{trans.get('keys.phan_quyen_du_lieu')}}</h4>
                            <div class="row">
                                <div class="col-6">
                                    <form v-on:submit.prevent="listContentRoles(1)">
                                        <div class="d-flex flex-row form-group">
                                            <input v-model="keyword" type="text"
                                                   class="form-control search_text"
                                                   :placeholder="trans.get('keys.nhap_tu_khoa')+'...'">
                                            <button type="button" id="btnFilter1"
                                                    class="btn btn-primary btn-sm"
                                                    @click="listContentRoles(1)">
                                                {{trans.get('keys.tim')}}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <p class="mb-10">{{trans.get('keys.tong_so')}} : {{total_item}}
                                {{trans.get('keys.to_chuc')}}.</p>
                            <div class="table-responsive">
                                <table class="table_res">
                                    <thead>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.ten_to_chuc')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.mo_ta')}}</th>
                                    <th>{{trans.get('keys.hanh_dong')}}</th>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(roc,index) in content_roles">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ roc.name === 'teacher' ? 'creator' : roc.name }}</td>
                                        <td class=" mobile_hide">{{ roc.description }}</td>
                                        <td>
                                            <router-link :title="trans.get('keys.sua')"
                                                         class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                         :to="{ name: 'RoleEdit', params: { role_id: roc.id }}">
                                                <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                            </router-link>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div :style="content_roles.length === 0 ? 'display:none;' : 'display:block;'">
                                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
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
    export default {
        data() {
            return {
                role: {
                    name: '',
                    description: '',
                    is_organization_role: 0,
                },
                roles: [],
                content_roles: [],
                type: 'all',
                current: 1,
                totalPages: 0,
                row: 10,
                total_item: 0,
                keyword: ''
            }
        },
        methods: {
            createRole() {
                $('.error').hide();
                $('.output.error').hide();
                $('.output.error').html();
                if (!this.role.name) {
                    $('.name.error').show();
                    $('.name.error').html(this.trans.get('keys.truong_bat_buoc_phai_nhap'));
                    return;
                }
                if (!this.role.description) {
                    $('.description.error').show();
                    $('.description.error').html(this.trans.get('keys.truong_bat_buoc_phai_nhap'));
                    return;
                }

                let current_pos = this;

                axios.post('/role/create', {
                    name: this.role.name,
                    description: this.role.description,
                    is_organization_role: this.role.is_organization_role
                })
                    .then(response => {
                        roam_message(response.data.status, response.data.message);
                        this.role.name = '';
                        this.role.description = '';
                        this.role.is_organization_role = 0;
                    })
                    .catch(error => {
                        roam_message('error', current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    });
            },
            listRoles() {
                axios.post('/role/list_role', {
                    type: 'role'
                })
                    .then(response => {
                        this.roles = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            listContentRoles(paged) {
                axios.post('/role/list_role', {
                    page: paged || this.current,
                    row: this.row,
                    keyword: this.keyword,
                    type: 'content_permission'
                })
                    .then(response => {
                        this.content_roles = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total_page : 0;
                        this.total_item = response.data.pagination ? response.data.pagination.total_item : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                // let back = this.getParamsBackPage();
                if (sessionStorage.getItem('roleBack') === '1') {
                    this.current = Number(sessionStorage.getItem('rolePage'));
                    this.row = Number(sessionStorage.getItem('rolePageSize'));
                    // this.$route.params.back_page= null;
                }
                this.listContentRoles();
            },
            deleteRole(role_id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_muon_xoa_quyen_nay'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/role/delete', {
                        role_id: role_id
                    })
                        .then(response => {
                            roam_message(response.data.status, response.data.message);
                        })
                        .catch(error => {
                            roam_message('error', current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
            },
            getParamsBackPage() {
                return this.$route.params.back_page;
            },
            setParamsBackPage(value) {
                this.$route.params.back_page = value;
            },
        },
        mounted() {
            this.listRoles();
            this.listContentRoles();
            sessionStorage.clear();
        },

        destroyed() {
            sessionStorage.setItem('roleBack', '1');
            sessionStorage.setItem('rolePage', this.current);
            sessionStorage.setItem('rolePageSize', this.row);
        }
    }
</script>

<style scoped>

</style>
