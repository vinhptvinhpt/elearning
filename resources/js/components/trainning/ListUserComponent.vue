<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_nguoi_dung') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">

                    <div class="row mb-4">
                        <div class="col-sm">
                            <div class="accordion" id="accordion_1">
                                <div class="card" style="border-bottom: 1px solid rgba(0,0,0,.125);">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1"
                                           aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_nguoi_dung_vao_knl')}}</a>
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
                                                            <treeselect class="form-group" v-model="organization_id_1" :multiple="false" :options="options"/>
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
                                                                {{trans.get('keys.tong_so_nguoi_dung_hien_tai')}} : {{ total_out }}
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
                                                                <template v-if="users_out_trainning.length == 0">
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
                                                                <tfoot>
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
                                                                </tfoot>
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
                                                                    {{trans.get('keys.them_vao_knl')}} <i
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
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_2"
                                           aria-expanded="false"><i class="fal fa-upload mr-3"></i>{{trans.get('keys.them_cctc_vao_knl')}}</a>
                                    </div>
                                    <div id="collapse_2" class="collapse" data-parent="#accordion_1">
                                        <div class="card-body">
                                            <div class="col-12 col-lg-12">
                                                <div class="form-row">
                                                    <div class="col-sm-8 form-group">
                                                        <treeselect v-model="organization_id"
                                                                    :multiple="false" :options="tree_options"
                                                                    id="organization_parent_id"/>
                                                    </div>
                                                    <div class="col-4 form-group">
                                                        <button type="button"
                                                                style="float: right; position: relative;"
                                                                class="btn btn-sm btn-success mt-3 btn-pdf"
                                                                @click="addOrganizationToTrainning()">
                                                            {{trans.get('keys.them_vao_knl')}} <i
                                                                class="fa fa-spinner"
                                                                aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-sm-12 form-group">
                                                        <label style="font-style: italic;">{{trans.get('keys.them_user_cctc_vao_knl')}}</label>
<!--                                                        <br/>-->
<!--                                                        <label style="font-style: italic; color: red;">{{trans.get('keys.luu_y_cctc_vao_knl')}}</label>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_nguoi_dung')}}</h5>
                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div class="row">
                                    <div class="col-sm-8 dataTables_wrapper">
                                        <div class="dataTables_length" style="display: inline-block;">
                                            <label>{{trans.get('keys.hien_thi')}}
                                                <select v-model="row"
                                                        class="custom-select custom-select-sm form-control form-control-sm"
                                                        @change="getUser(1)">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
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
                                            <th>{{trans.get('keys.stt')}}</th>
                                            <th>{{trans.get('keys.tai_khoan')}}</th>
                                            <th class=" mobile_hide">{{trans.get('keys.ten_nguoi_dung')}}</th>
                                            <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <template v-if="posts.length == 0">
                                            <!--                      <tr v-if="posts.length == 0">-->
                                            <!--                        <td colspan="8">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>-->
                                            <!--                      </tr>-->
                                            <tr>
                                                <td colspan="8">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr v-if="user" v-for="(user,index) in posts">
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
                                                    <button @click="remove_trainning(user.id,user.user_id,trainning_id)"
                                                            class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 btn_open_select"
                                                            type="button">
                                                        <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>

                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th>{{trans.get('keys.stt')}}</th>
                                            <th>{{trans.get('keys.tai_khoan')}}</th>
                                            <th class=" mobile_hide">{{trans.get('keys.ten_nguoi_dung')}}</th>
                                            <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                        </tr>
                                        </tfoot>
                                    </table>

                                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
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


</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import EditIndexComponent from '../system/user/EditIndexComponent'

    export default {
        props: ['trainning_id'],
        components: {EditIndexComponent},
        data() {
            return {
                posts: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                total_user: 0,
                row: 10,
                trainning: 0,
                users_out_trainning: [],
                row_out: 10,
                total_out: 0,
                current_out: 1,
                keyword_out: '',
                totalPagesOut: 0,
                allSelected: false,
                userTrainning: [],
                tree_options: [
                    {
                        id: 0,
                        label: this.trans.get('keys.chon_to_chuc')
                    }
                ],
                organization_parent_list: [],
                organization_id: 0,
              //Treeselect options
              options: [
                {
                  id: 0,
                  label: this.trans.get('keys.chon_to_chuc')
                }
              ],
              organization_id_1: 0
            }
        },
        methods: {
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
            addOrganizationToTrainning() {
                let current_pos = this;
                if (this.organization_id === 0) {
                    toastr['warning'](current_pos.trans.get('keys.chua_chon_cctc'), current_pos.trans.get('keys.thong_bao'));
                    return;
                }
                $('button.btn-pdf i').css("display", "inline-block");
                let loader = $('.preloader-it');
                loader.fadeIn();
                axios.post('/api/trainning/adduserorganizationtotrainning', {
                    org_id: this.organization_id,
                    trainning_id: this.trainning_id
                })
                .then(response => {
                    loader.fadeOut();
                    if (response.data.status) {
                        toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                        current_pos.getUser(current_pos.current);
                        current_pos.getUserOutTrainning(current_pos.current_out);
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
                this.organization_id = 0;
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
                axios.post('/api/trainning/addusertotrainning', {
                    Users: this.userTrainning,
                    trainning_id: this.trainning_id
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
            remove_trainning(id, user_id, tr_id) {
                let current_pos = this;
                //console.log('trainning_id: ' + tr_id);
                swal({
                    title: current_pos.trans.get('keys.thong_bao'),
                    text: current_pos.trans.get('keys.ban_muon_loai_khung_nang_luc_gan_cho_nguoi_dung_nay'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/trainning/api_remove_trainning', {
                        id: id,
                        user_id: user_id,
                        trainning_id: tr_id
                    })
                        .then(response => {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            $('.btn_open_select.actives').trigger('click');
                            current_pos.getUser(current_pos.current);
                            current_pos.getUserOutTrainning(current_pos.current_out);
                        })
                        .catch(error => {
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });
            },
            changeTrainning(trainning_id, user_id) {
                let current_pos = this;
                swal({
                    title: current_pos.trans.get('keys.thong_bao'),
                    text: current_pos.trans.get('keys.ban_muon_cap_nhat_lai_khung_nang_luc_cho_nguoi_dung_nay'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/trainning/api_trainning_change', {
                        trainning_id: trainning_id,
                        user_id: user_id,
                    })
                        .then(response => {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            $('.btn_open_select.actives').trigger('click');
                            current_pos.getUser(current_pos.current);
                        })
                        .catch(error => {
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });

            },
            getUserOutTrainning(page) {
                axios.post('/api/trainning/getlistuserouttrainning', {
                    page: page || this.current_out,
                    keyword: this.keyword_out,
                    row: this.row_out,
                    trainning: this.trainning_id,
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
                axios.post('/trainning/api_list_user', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    trainning: this.trainning_id
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
              axios.post('/organization/list',{
                keyword: this.organization_keyword,
                level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
                paginated: 0 //không phân trang
              })
                .then(response => {
                  this.organization_list = response.data;
                  //Set options recursive
                  this.options = this.setOptions(response.data, current_id);
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
        },
        mounted() {
            this.listOrganization();
          this.selectOrganization();
        }
    }
</script>

<style scoped>
</style>
