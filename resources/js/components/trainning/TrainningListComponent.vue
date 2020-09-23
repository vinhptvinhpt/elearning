<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.quan_tri_khung_nang_luc') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h5 class="hk-sec-title" v-if="type == 1">{{trans.get('keys.khung_nang_luc_theo_thoi_gian')}}</h5>
                    <h5 class="hk-sec-title" v-else-if="type == 2">{{trans.get('keys.khung_nang_luc_nhom')}}</h5>
                    <h5 class="hk-sec-title" v-else>{{trans.get('keys.khung_nang_luc')}}</h5>
                    <div class="row mb-4" v-if="slug_can('tms-system-administrator-grant')">
                        <div class="col-sm">
                            <div class="accordion" id="accordion_1">
                                <div class="card" style="border-bottom:1px solid rgba(0,0,0,.125) !important;">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1"
                                           aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.tao_moi_knl')}}</a>
                                    </div>
                                    <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                                        <div class="card-body">
                                            <trainning-create :type="type"></trainning-create>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div class="row">
                                    <div class="col-sm-6 col-md-8">
                                        <div class="dataTables_length">
                                            <span class="d-inline-block">{{trans.get('keys.hien_thi')}}</span>
                                            <select v-model="row"
                                                    class="custom-select custom-select-sm form-control form-control-sm d-inline-block"
                                                    @change="getTrainnings(1)">
                                                <option value="10">10</option>
                                                <option value="20">20</option>
                                                <option value="50">50</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4">
                                        <form v-on:submit.prevent="getTrainnings(1)">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword" type="text"
                                                       class="form-control search_text"
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_knl')+' ...'"/>
                                                <button type="button" id="btnFilter"
                                                        class="btn btn-primary btn-sm btn_fillter"
                                                        @click="getTrainnings(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <br/>
                                <table id="datable_1" class="table_res">
                                    <thead>
                                    <tr>
                                        <th>{{trans.get('keys.stt')}}</th>
                                        <th>{{trans.get('keys.ma_knl')}}</th>
                                        <th>{{trans.get('keys.ten_knl')}}</th>
                                        <!--                    <th>{{trans.get('keys.quyen')}}</th>-->
                                        <!--                    <th>{{trans.get('keys.co_cau_to_chuc')}}</th>-->
                                        <th>{{trans.get('keys.hoc_vien')}}</th>
                                        <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(sur,index) in trainnings">
                                        <td>{{ (current-1)*row+(index+1) }}</td>
                                        <td>
                                            {{ sur.code }}
                                        </td>
                                        <td>{{ sur.name }}</td>
                                        <!--                    <td>-->
                                        <!--                      {{ sur.group_role && sur.group_role.role ? sur.group_role.role.name : '' }}-->
                                        <!--                    </td>-->
                                        <!--                    <td>-->
                                        <!--                      {{ sur.group_organize && sur.group_organize.organize ? sur.group_organize.organize.name : '' }}-->
                                        <!--                    </td>-->
                                        <td>
                                            {{ sur.total_user ? sur.total_user : 0 }}
                                        </td>

                                        <td class="text-center">
                                            <router-link :title="trans.get('keys.xem_nhan_vien')"
                                                         class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                         :to="{ name: 'ListUserTrainning', params: {trainning_id: sur.id}}">
                                                <span class="btn-icon-wrap"><i class="fal fa-users"></i></span>
                                            </router-link>

                                            <router-link
                                                    class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                    :title="trans.get('keys.sua_thong_tin_knl')"
                                                    :to="{ name: 'TrainningEdit', params: { id: sur.id }}">
                        <span class="btn-icon-wrap"><i
                                class="fal fa-pencil"></i></span>
                                            </router-link>

                                            <button v-if="slug_can('tms-system-administrator-grant')" :title="trans.get('keys.xoa')" data-toggle="modal"
                                                    data-target="#delete-ph-modal"
                                                    @click="deletePost(sur.id)"
                                                    class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                                                <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>

                                <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                              :classes=$pagination.classes :labels=$pagination.labels></v-pagination>

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
    import TrainningCreate from './TrainningCreateComponent'

    export default {
        props: ['type', 'slugs'],
        //components: {vPagination},
        components: {TrainningCreate},
        data() {
            return {
                trainnings: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 10
            }
        },
        methods: {
            slug_can(permissionName) {
              return this.slugs.indexOf(permissionName) !== -1;
            },
            getTrainnings(paged) {
                axios.post('/api/trainning/list', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    style: this.type
                })
                    .then(response => {
                        this.trainnings = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                let back = this.getParamsBackPage();
                if(back == '1') {
                  this.current = Number(sessionStorage.getItem('trainingPage'));
                  this.row = Number(sessionStorage.getItem('trainingPageSize'));
                  this.keyword = sessionStorage.getItem('trainingKeyWord');
                  sessionStorage.clear();
                  this.$route.params.back_page= null;
                }
                this.getTrainnings();
            },
            getParamsBackPage() {
              return this.$route.params.back_page;
            },
            setParamsBackPage(value) {
              this.$route.params.back_page = value;
            },
            deletePost(id) {
                // sessionStorage.setItem('trainingPage', this.current);
                // sessionStorage.setItem('trainingPageSize', this.row);
                // sessionStorage.setItem('trainingKeyWord', this.keyword);
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_co_chac_muon_xoa_khung_nang_luc_nay'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: false
                }, function () {
                    axios.post('/api/trainning/delete/' + id)
                        .then(response => {
                            if (response.data.status) {
                                toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                                if(current_pos.trainnings.length == 1){
                                  current_pos.current = current_pos.current > 1 ? current_pos.current -1 : 1 ;
                                }
                                // current_pos.onPageChange();
                                current_pos.getTrainnings(current_pos.current);
                            } else {
                                toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                            }
                        })
                        .catch(error => {
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });
                return false;
            }
        },
        mounted() {
        },
        destroyed() {
          sessionStorage.setItem('trainingPage', this.current);
          sessionStorage.setItem('trainingPageSize', this.row);
          sessionStorage.setItem('trainingKeyWord', this.keyword);
        }
    }
</script>

<style scoped>

</style>
