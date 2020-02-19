<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item">
              <router-link :to="{ name: 'CityIndex'}">
                {{ trans.get('keys.tinh_thanh') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.dai_ly') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div>
          <div class="card">
            <div class="card-body">
              <div class="edit_city_form form-material">
                <h5 class="mb-20">{{trans.get('keys.danh_sach_dai_ly_cua')}} : {{data.city.name}}</h5>
                <div class="mb-20 mt-20">

                  <div class="accordion" id="accordion_1" style="border-bottom: 1px solid rgba(0,0,0,.125);">
                    <div class="card">
                      <div class="card-header d-flex justify-content-between">
                        <router-link class="collapsed"
                          :to="{ name: 'BranchIndex', params: { city: id } }">
                          <i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}
                        </router-link>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-8 dataTables_wrapper">
                    <div class="dataTables_length" style="display: inline-block;">
                      <label>{{trans.get('keys.hien_thi')}}
                        <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getBranchByCity(1)">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select>
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <form v-on:submit.prevent="getBranchByCity(1)">
                      <div class="d-flex flex-row form-group">
                        <input  v-model="keyword" type="text"
                                class="form-control search_text" :placeholder="trans.get('keys.ten_ma_dai_ly')+' ...'">
                        <button type="button" class="btn btn-primary btn-sm btn_fillter"
                                @click="getBranchByCity(1)">
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
                        <th>{{trans.get('keys.stt')}}</th>
                        <th>{{trans.get('keys.ma')}}</th>
                        <th>{{trans.get('keys.ten_dai_ly')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.quan_ly')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.dia_diem')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.diem_ban')}}</th>
                        <th>{{trans.get('keys.hanh_dong')}}</th>
                        </thead>
                        <tbody>
                        <tr v-if="posts.length == 0">
                          <td colspan="7">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                        </tr>
                        <tr v-else v-for="(branch,index) in posts">
                          <td>{{ (current-1)*row+(index+1) }}</td>
                          <td>{{ branch.code }}</td>
                          <td>{{ branch.name }}</td>
                          <td  class=" mobile_hide">{{ branch.user_name }}</td>
                          <td class=" mobile_hide">{{ branch.address }}</td>
                          <td class=" mobile_hide">
                            <router-link :title="trans.get('keys.xem_danh_sach_diem_ban')"
                                         :to="{
                                           name: 'SaleroomIndexByBranch',
                                           params: { branch_id: branch.id },
                                         }">
                              {{ branch.saleroom_count }} <span class="text-underline">({{trans.get('keys.diem_ban')}})</span>
                            </router-link>
                          </td>
                          <td>
                            <router-link :title="trans.get('keys.sua_dai_ly')"
                                         :to="{
                              						 path: 'system/organize/branch/edit',
                                           name: 'EditBranch',
                                           params: { branch_id: branch.id },
                                           query: { city: id}
                                         }"
                                         class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2">
                              <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                            </router-link>

                            <a href="javascript(0)" @click.prevent="removeBranch(branch.id)" :title="trans.get('keys.go_dai_ly')"
                               class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                              <span class="btn-icon-wrap"><i class="fa fa-eraser"></i></span>
                            </a>
                          </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <th>{{trans.get('keys.stt')}}</th>
                        <th>{{trans.get('keys.ma')}}</th>
                        <th>{{trans.get('keys.ten_dai_ly')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.quan_ly')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.dia_diem')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.diem_ban')}}</th>
                        <th>{{trans.get('keys.hanh_dong')}}</th>
                        </tfoot>
                      </table>
                      <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                        <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                      </div>
                    </div>
                    <div class="button-list">
                      <!--<a class="btn btn-primary btn-sm" :href="trans.get('keys.language')+'/system/organize/city/add_branch/'+id">{{trans.get('keys.them_dai_ly')}}</a>-->
                      <router-link to="/tms/system/organize/city" class="btn btn-secondary btn-sm">
                        {{trans.get('keys.quay_lai')}}
                      </router-link>

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
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    export default {
        props: ['id'],
        //components: {vPagination},
        data() {
            return {
                data:{
                    city:{
                        name:'',
                    }
                },
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 10,
            }
        },
        methods: {
            getBranchByCity(paged) {
                axios.post('/system/organize/city/list_branch_by_city', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    id:this.id
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getBranchByCity();
            },
            getDataCity(){
                axios.post('/system/organize/city/detail_data/'+this.id)
                    .then(response => {
                        this.data = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
            },
            removeBranch(branch_id){
                var city_id = this.id;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_muon_go_dai_ly_da_chon'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/organize/city/remove_branch',{
                        city_id:city_id,
                        branch_id:branch_id
                    })
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                        })
                        .catch(error => {
                            roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
            },
        },
        mounted() {
            this.getDataCity();
        }
    }
</script>

<style scoped>

</style>
