<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/system/user_market">{{ trans.get('keys.chuyen_vien_kinh_doanh') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.quan_ly_dai_ly') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <h6 class="mb-3">{{ trans.get('keys.chuyen_vien_kinh_doanh') }}: {{ fullname }}</h6>
        <div>
          <div class="role_organize">
            <div class="role_organize_content">
              <div class="accordion" id="accordion_1">
                <div class="card" style="border: 1px solid rgba(0,0,0,.125);">
                  <div class="card-header d-flex justify-content-between">
                    <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1" aria-expanded="true">
                      <i class="fal fa-plus mr-3"></i>{{trans.get('keys.cap_quyen_dai_ly')}}</a>
                  </div>
                  <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-sm-8 dataTables_wrapper">
                          <div class="dataTables_length" style="display: inline-block;">
                            <label>{{trans.get('keys.hien_thi')}}
                              <select v-model="row2" class="custom-select custom-select-sm form-control form-control-sm" @change="showOrganize(1)">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                              </select>
                            </label>
                          </div>
                          <div class="fillterConfirm" style="display: inline-block;">
                            <label>
                              <select v-model="city" @change="showOrganize(1)" class="custom-select custom-select-sm form-control form-control-sm selectpicker" data-live-search="true">
                                <option value="0">{{trans.get('keys.theo_tinh_thanh')}}</option>
                                <option v-for="itemcity in listcity" :value="itemcity.id">{{itemcity.name}}</option>
                              </select>
                            </label>
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="form-group">
                            <form v-on:submit.prevent="showOrganize(1)">
                              <div class="d-flex flex-row">
                                <input  v-model="keyword2" type="text"
                                        class="form-control search_text" :placeholder="trans.get('keys.nhap_tu_khoa') + ' ...'">
                                <button type="button" class="btn btn-primary btn-sm btn_fillter" id="btnFilter2"
                                        @click="showOrganize(1)">
                                  {{trans.get('keys.tim')}}
                                </button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="table-responsive">
                        <table class="table_res">
                          <thead>
                          <th>{{trans.get('keys.stt')}}</th>
                          <th>{{trans.get('keys.ma_dai_ly')}}</th>
                          <th>{{trans.get('keys.ten_dai_ly')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.tinh_thanh')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.truong_dai_ly')}}</th>
                          <th>{{trans.get('keys.hanh_dong')}}</th>
                          </thead>
                          <tbody>
                          <tr v-if="posts2.length == 0">
                            <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                          </tr>
                          <tr v-else v-for="(post,index) in posts2">
                            <td>{{ (current2-1)*row2+(index+1) }}</td>
                            <td>{{ post.code }}</td>
                            <td>{{ post.name }}</td>
                            <td class=" mobile_hide">{{ post.city_name }}</td>
                            <td class=" mobile_hide">{{ post.user_name }}</td>
                            <td>
                              <button type="button" class="btn btn-sm btn-primary" @click="addOrganize(post.id)">{{trans.get('keys.cap_quyen')}}</button>
                            </td>
                          </tr>
                          </tbody>
                          <tfoot>
                          <th>{{trans.get('keys.stt')}}</th>
                          <th>{{trans.get('keys.ma_dai_ly')}}</th>
                          <th>{{trans.get('keys.ten_dai_ly')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.tinh_thanh')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.truong_dai_ly')}}</th>
                          <th>{{trans.get('keys.hanh_dong')}}</th>
                          </tfoot>
                        </table>
                        <div :style="posts2.length == 0 ? 'display:none;' : 'display:block;'">
                          <v-pagination v-model="current2" @input="onPageChange" :page-count="totalPages2" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <h6 class="mb-20 mt-20">{{trans.get('keys.danh_sach_dai_ly')}}</h6>
              <div class="row">
                <div class="col-sm-8 dataTables_wrapper">
                  <div class="dataTables_length" style="display: inline-block;">
                    <label>{{trans.get('keys.hien_thi')}}
                      <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="listData(1)">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                      </select>
                    </label>
                  </div>
                </div>
                <div class="col-sm-4 hide">
                  <div class="form-group">
                    <form v-on:submit.prevent="listData(1)">
                      <div class="d-flex flex-row">
                        <input  v-model="keyword" type="text"
                                class="form-control search_text" :placeholder="trans.get('keys.nhap_tu_khoa') + ' ...'">
                        <button type="button" class="btn btn-primary btn-sm btn_fillter" id="btnFilter"
                                @click="listData(1)">
                          {{trans.get('keys.tim')}}
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table_res">
                  <thead>
                  <th>{{trans.get('keys.stt')}}</th>
                  <th>{{trans.get('keys.ma_dai_ly')}}</th>
                  <th>{{trans.get('keys.ten_dai_ly')}}</th>
                  <th class=" mobile_hide">{{trans.get('keys.tinh_thanh')}}</th>
                  <th class=" mobile_hide">{{trans.get('keys.truong_dai_ly')}}</th>
                  <th>{{trans.get('keys.hanh_dong')}}</th>
                  </thead>
                  <tbody>
                  <tr v-if="posts.length == 0">
                    <td colspan="5">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                  </tr>
                  <tr v-else v-for="(post,index) in posts">
                    <td>{{ (current-1)*row+(index+1) }}</td>
                    <td>{{ post.code }}</td>
                    <td>{{ post.name }}</td>
                    <td class=" mobile_hide">{{ post.city_name }}</td>
                    <td class=" mobile_hide">{{ post.user_name }}</td>
                    <td>
                      <button type="button" class="btn btn-sm btn-danger" @click="removeOrganize(post.id)">{{trans.get('keys.go_quyen')}}</button>
                    </td>
                  </tr>
                  </tbody>
                  <tfoot>
                  <th>{{trans.get('keys.stt')}}</th>
                  <th>{{trans.get('keys.ma_dai_ly')}}</th>
                  <th>{{trans.get('keys.ten_dai_ly')}}</th>
                  <th class=" mobile_hide">{{trans.get('keys.tinh_thanh')}}</th>
                  <th class=" mobile_hide">{{trans.get('keys.truong_dai_ly')}}</th>
                  <th>{{trans.get('keys.hanh_dong')}}</th>
                  </tfoot>
                </table>

                <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>

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
        props: ['user_id'],
        //components: {vPagination},
        data(){
            return{
                posts2: {},
                current2: 1,
                totalPages2: 0,
                row2:10,
                keyword2:'',
                posts: {},
                current: 1,
                totalPages: 0,
                row:10,
                keyword:'',
                city:0,
                listcity: {},
                fullname: ''
            }
        },
        methods:{
            showForm(id_form){
                var parent = $('#'+id_form).parent();
                if($('#'+id_form).hasClass('active')){
                    $('#'+id_form).slideUp();
                    $('#'+id_form).removeClass('active');
                    $('.show_form',parent).removeClass('open');
                }else{
                    $('#'+id_form).slideDown();
                    $('#'+id_form).addClass('active');
                    $('.show_form',parent).addClass('open');
                }
            },
            getCity(){
                axios.post('/system/user_market/get_city')
                    .then(response => {
                        this.listcity  = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            showOrganize(paged){
                axios.post('/system/user_market/list_organize', {
                    page: paged || this.current2,
                    row: this.row2,
                    keyword: this.keyword2,
                    city: this.city,
                })
                    .then(response => {
                        this.posts2              = response.data.data ? response.data.data.data : [];
                        this.current2            = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages2         = response.data.pagination ? response.data.pagination.total : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            addOrganize(organize_id){
                axios.post('/system/user_market/add_role_organize', {
                    organize_id: organize_id,
                    user_id: this.user_id,
                })
                    .then(response => {
                        roam_message(response.data.status,response.data.message);
                        $('.show_form').trigger('click');
                    })
                    .catch(error => {
                        roam_message('error','Lỗi hệ thống. Thao tác thất bại');
                    });
            },
            removeOrganize(organize_id){
                var user = this.user_id
                swal({
                    title: "Thông báo",
                    text: "Bạn muốn Gỡ đại lý này.",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user_market/remove_role_organize', {
                        organize_id: organize_id,
                        user_id: user,
                    })
                    .then(response => {
                        roam_message(response.data.status,response.data.message);
                    })
                    .catch(error => {
                        roam_message('error','Lỗi hệ thống. Thao tác thất bại');
                    });
                });
            },
            listData(paged){
                axios.post('/system/user_market/list_role_organize', {
                    page: paged || this.current,
                    user_id: this.user_id,
                    row: this.row,
                    //keyword: this.keyword
                })
                    .then(response => {
                        this.posts              = response.data.data ? response.data.data.data : [];
                        this.current            = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages         = response.data.pagination ? response.data.pagination.total : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.showOrganize();
                this.listData();
            },
            fetch() {
              axios.post('/bridge/fetch', {
                user_id: this.user_id,
                view: 'UserMarketOrganize'
              })
                .then(response => {
                  this.fullname = response.data.fullname;
                })
                .catch(error => {
                  console.log(error);
                })
            },
        },
        mounted() {
            this.getCity();
        }
    }
</script>

<style scoped>

</style>
