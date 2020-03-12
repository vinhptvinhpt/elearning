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
          <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_nguoi_dung')}}</h5>
          <div class="row">
            <div class="col-sm">
              <div class="table-wrap">
                <div class="row">
                  <div class="col-sm-8 dataTables_wrapper">
                    <div class="dataTables_length" style="display: inline-block;">
                      <label>{{trans.get('keys.hien_thi')}}
                        <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm"
                                @change="getUser(1)">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select>
                      </label>
                    </div>
                    <div class="fillterConfirm" style="display: inline-block;">
                      <label>
                        <select v-model="trainning" class="custom-select custom-select-sm form-control form-control-sm" @change="getUser(1)">
                          <option value="0">{{trans.get('keys.khung_nang_luc')}}</option>
                          <option v-for="value in trainning_list" :value="value.id">{{value.name}}</option>
                        </select>
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <form v-on:submit.prevent="getUser(1)">
                      <div class="d-flex flex-row form-group">
                        <input v-model="keyword" type="text"
                               class="form-control search_text"
                               :placeholder="trans.get('keys.nhap_ten_tai_khoan_email_cmtnd')+ ' ...'">
                        <button type="button" id="btnFilter" class="btn btn-primary btn-sm btn_fillter"
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
                      <th>{{trans.get('keys.khung_nang_luc')}}</th>
                      <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="posts.length == 0">
                      <td colspan="8">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                    </tr>
                    <tr v-else v-for="(user,index) in posts">
                      <td>{{ (current-1)*row+(index+1) }}</td>
                      <td>
                        <router-link
                          :to="{ name: 'EditUserById', params: { user_id: user.user_id,type:'system' } }">
                          {{ user.user_detail.user.username }}
                        </router-link>
                      </td>

                      <td class=" mobile_hide">{{ user.user_detail.fullname }}</td>
                      <td class=" mobile_hide">{{ user.user_detail.email }}</td>
                      <td class="wrap_select">
                        <table v-if="user.user_detail.trainning_user.length > 0" style="margin-bottom: 0;">
                          <tr v-for="trainning in user.user_detail.trainning_user">
                            <td>
                              {{ trainning.training_detail.name }}
                              <span style="display: none;" class="remove_trainning" :title="trans.get('keys.go_khung_nang_luc')"
                                    @click="remove_trainning(trainning.id)"><i class="fas fa-times"></i></span>
                            </td>
                          </tr>
                        </table>
<!--                        <span>{{ user.trainning_name }}</span>-->
<!--                        <div style="display: none;">-->
<!--                          <div style="display: flex;min-width: 100px;">-->
<!--                            <select v-model="user.trainning_id"-->
<!--                                    class="custom-select custom-select-sm form-control form-control-sm">-->
<!--                              <option v-for="value in trainning_list" :value="value.id">{{value.name}}</option>-->
<!--                            </select>-->
<!--                            <button style="height: 33px;min-width: 45px;" class="btn btn-primary btn-sm" type="button"-->
<!--                                    @click="changeTrainning(user.trainning_id,user.id)">{{trans.get('keys.luu')}}-->
<!--                            </button>-->
<!--                          </div>-->
<!--                        </div>-->
                      </td>
                      <td class="text-center">
                        <button class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2 btn_open_select" type="button">
                          <span class="btn-icon-wrap"><i class="fal fa-wrench"></i></span>
                        </button>
                      </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th>{{trans.get('keys.tai_khoan')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.ten_nguoi_dung')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                      <th>{{trans.get('keys.khung_nang_luc')}}</th>
                      <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                    </tr>
                    </tfoot>
                  </table>

                  <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
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
    components: {EditIndexComponent},
    data() {
      return {
        posts: {},
        keyword: '',
        current: 1,
        totalPages: 0,
        total_user: 0,
        row: 10,
        trainning: 0,
        trainning_list: []
      }
    },
    methods: {
      remove_trainning(id){
        swal({
          title: this.trans.get('keys.thong_bao'),
          text: this.trans.get('keys.ban_muon_loai_khung_nang_luc_gan_cho_nguoi_dung_nay'),
          type: "warning",
          showCancelButton: true,
          closeOnConfirm: true,
          showLoaderOnConfirm: true
        }, function () {
          axios.post('/trainning/api_remove_trainning', {
            id: id,
          })
            .then(response => {
              roam_message(response.data.status, response.data.message);
              $('.btn_open_select.actives').trigger('click');
            })
            .catch(error => {
              roam_message('error', this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
            });
        });
      },
      changeTrainning(trainning_id, user_id) {
        swal({
          title: this.trans.get('keys.thong_bao'),
          text: this.trans.get('keys.ban_muon_cap_nhat_lai_khung_nang_luc_cho_nguoi_dung_nay'),
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
              roam_message(response.data.status, response.data.message);
              $('.btn_open_select.actives').trigger('click');
            })
            .catch(error => {
              roam_message('error', this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
            });
        });

      },
      getTrainning() {
        axios.post('/trainning/api_trainning_list')
          .then(response => {
            this.trainning_list = response.data;
          })
          .catch(error => {
            this.trainning_list = [];
          });
      },
      getUser(paged) {
        axios.post('/trainning/api_list_user', {
          page: paged || this.current,
          keyword: this.keyword,
          row: this.row,
          trainning: this.trainning
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
      onPageChange() {
        this.getUser();
      },
    },
    mounted() {
      this.getTrainning();
    }
  }
</script>

<style scoped>

</style>
