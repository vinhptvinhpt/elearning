<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.chuyen_vien_kinh_doanh') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
          <div class="row">
              <div class="col-xl-12">
                  <section class="hk-sec-wrapper">
                      <h5 class="hk-sec-title">{{trans.get('keys.chuyen_vien_kinh_doanh')}}</h5>
                      <div class="mb-20 mt-20">
                          <!--<button class="btn btn-success btn-sm show_form formCreateNew" @click="showForm('formCreateNew')">
                              <span><i class="fa fa-plus-circle"></i> Thêm nhân viên Mới</span>
                              <i class="fa fa-times hide"></i>
                          </button>
                          <button class="btn btn-primary btn-sm show_form formAddNew" @click="showForm('formAddNew')">
                              <span><i class="fa fa-plus-circle"></i> Thêm nhân viên có sẵn</span>
                              <i class="fa fa-times hide"></i>
                          </button>
                          <div class="hide border_box mt-20 bradius-4 formHide" id="formCreateNew" style="max-width:100%;">
                              <h6 class="mb-20">Thêm mới nhân viên giám sát thị trường</h6>
                              <create_user_market></create_user_market>
                          </div>
                          <div class="hide border_box mt-20 bradius-4 formHide" id="formAddNew" style="max-width:100%;">
                              <h6 class="mb-20">Thêm nhân viên giám sát thị trường</h6>
                              <div class="row">
                                  <div class="col-sm-8 dataTables_wrapper">
                                      <div class="dataTables_length" style="display: inline-block;">
                                          <label>Hiển thị
                                              <select v-model="row2" class="custom-select custom-select-sm form-control form-control-sm" @change="showUser(1)">
                                                  <option value="10">10</option>
                                                  <option value="25">25</option>
                                                  <option value="50">50</option>
                                                  <option value="100">100</option>
                                              </select>
                                          </label>
                                      </div>
                                  </div>
                                  <div class="col-sm-4">
                                      <div class="form-group">
                                          <div class="d-flex flex-row">
                                              <input  v-model="keyword2" type="text"
                                                      class="form-control search_text" placeholder="Nhập Tên, Tài khoản, Email, CMTND...">
                                              <button type="button" class="btn btn-primary btn-sm" id="btnFilter2"
                                                      @click="showUser(1)">
                                                  Tìm
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="table-responsive">
                                  <table class="">
                                      <thead>
                                      <tr>
                                          <th>
                                              <input v-model="allSelected" @click="selectAllCheckbox()" id="branch-select-all" type="checkbox" class="filled-in chk-col-light-blue" name="select_all" value=""/>
                                              <label for="branch-select-all"></label>
                                          </th>
                                          <th>Số CMTND</th>
                                          <th>Tài khoản</th>
                                          <th>Tên người dùng</th>
                                          <th>Email</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                      <tr v-if="posts2.length == 0">
                                          <td colspan="6">Không tìm thấy dữ liệu</td>
                                      </tr>
                                      <tr v-else v-for="(user,index) in posts2">
                                          <td>
                                              <input v-model="user_select" :value="user.user_id" type="checkbox" :id="'add_user'+user.user_id" class="filled-in chk-col-light-blue check_box_branch"><label :for="'add_user'+user.user_id"></label>
                                          </td>
                                          <td>{{ user.cmtnd }}</td>
                                          <td>
                                              <a :href="'/system/user/edit/'+user.user_id">
                                                  {{ user.username }}
                                              </a>
                                          </td>

                                          <td>{{ user.fullname }}</td>
                                          <td>{{ user.email }}</td>
                                      </tr>
                                      </tbody>
                                      <tfoot>
                                      <tr>
                                          <th>
                                              <input v-model="allSelected" @click="selectAllCheckbox()" id="branch-select-all2" type="checkbox" class="filled-in chk-col-light-blue" name="select_all" value=""/>
                                              <label for="branch-select-all2"></label>
                                          </th>
                                          <th>Số CMTND</th>
                                          <th>Tài khoản</th>
                                          <th>Tên người dùng</th>
                                          <th>Email</th>
                                      </tr>
                                      </tfoot>
                                  </table>
                                  <v-pagination
                                          v-model="current2"
                                          @input="onPageChange"
                                          :page-count="totalPages2"
                                          :classes="{
                                           ul: 'pagination',
                                           li: 'page-item',
                                           liActive: 'active',
                                           liDisable: 'disabled',
                                           button: 'page-link'
                                       }"
                                          :labels="{
                                           first: 'First',
                                           prev: 'Previous',
                                           next: 'Next',
                                           last: 'Last'
                                       }"
                                  ></v-pagination>
                              </div>
                              <div class="text-right">
                                  <button @click="addUserMarket()" type="button" class="btn btn-success btn-sm">Thêm nhân viên</button>
                              </div>
                          </div>-->

                          <div class="accordion" id="accordion_1">
                              <div class="card">
                                  <div class="card-header d-flex justify-content-between">
                                      <a class="collapsed" role="button" id="formCreateNew" data-toggle="collapse" href="#collapse_1" aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
                                  </div>
                                  <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                                      <div class="card-body">
                                          <h6 class="mb-20">{{trans.get('keys.them_chuyen_vien_kinh_doanh')}}</h6>
                                          <create-user-market></create-user-market>
                                      </div>
                                  </div>
                              </div>
                              <div style="display: none;" class="card">
                                  <div class="card-header d-flex justify-content-between">
                                      <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_2" aria-expanded="false"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_nhan_vien_co_san')}}</a>
                                  </div>
                                  <div id="collapse_2" class="collapse" data-parent="#accordion_1">
                                      <div class="card-body">
                                          <h6 class="mb-20">{{trans.get('keys.them_chuyen_vien_kinh_doanh')}}</h6>
                                          <div class="row">
                                              <div class="col-sm-8 dataTables_wrapper">
                                                  <div class="dataTables_length" style="display: inline-block;">
                                                      <label>{{trans.get('keys.hien_thi')}}
                                                          <select v-model="row2" class="custom-select custom-select-sm form-control form-control-sm" @change="showUser(1)">
                                                              <option value="10">10</option>
                                                              <option value="25">25</option>
                                                              <option value="50">50</option>
                                                              <option value="100">100</option>
                                                          </select>
                                                      </label>
                                                  </div>
                                              </div>
                                              <div class="col-sm-4">
                                                  <div class="form-group">
                                                      <form v-on:submit.prevent="showUser(1)">
                                                          <div class="d-flex flex-row">
                                                              <input  v-model="keyword2" type="text"
                                                                      class="form-control search_text" :placeholder="trans.get('keys.nhap_ten_tai_khoan_email_cmtnd') + ' ...'">
                                                              <button type="button" class="btn btn-primary btn-sm btn_fillter" id="btnFilter2"
                                                                      @click="showUser(1)">
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
                                                  <tr>
                                                      <th>
                                                          <input v-model="allSelected" @click="selectAllCheckbox()" id="branch-select-all" type="checkbox" class="filled-in chk-col-light-blue" name="select_all" value=""/>
                                                          <label for="branch-select-all"></label>
                                                      </th>
                                                      <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                                                      <th>{{trans.get('keys.tai_khoan')}}</th>
                                                      <th>{{trans.get('keys.ten_nguoi_dung')}}</th>
                                                      <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                                                  </tr>
                                                  </thead>
                                                  <tbody>
                                                  <tr v-if="posts2.length == 0">
                                                      <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                                                  </tr>
                                                  <tr v-else v-for="(user,index) in posts2">
                                                      <td>
                                                          <input v-model="user_select" :value="user.user_id" type="checkbox" :id="'add_user'+user.user_id" class="filled-in chk-col-light-blue check_box_branch"><label :for="'add_user'+user.user_id"></label>
                                                      </td>
                                                      <td class=" mobile_hide">{{ user.cmtnd }}</td>
                                                      <td>
                                                          <a :href="trans.get('keys.language')+'/system/user/edit/'+user.user_id">
                                                              {{ user.username }}
                                                          </a>
                                                      </td>

                                                      <td>{{ user.fullname }}</td>
                                                      <td class=" mobile_hide">{{ user.email }}</td>
                                                  </tr>
                                                  </tbody>
                                                  <tfoot>
                                                  <tr>
                                                      <th>
                                                          <input v-model="allSelected" @click="selectAllCheckbox()" id="branch-select-all2" type="checkbox" class="filled-in chk-col-light-blue" name="select_all" value=""/>
                                                          <label for="branch-select-all2"></label>
                                                      </th>
                                                      <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                                                      <th>{{trans.get('keys.tai_khoan')}}</th>
                                                      <th>{{trans.get('keys.ten_nguoi_dung')}}</th>
                                                      <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                                                  </tr>
                                                  </tfoot>
                                              </table>
                                              <div :style="posts2.length == 0 ? 'display:none;' : 'display:block;'">
                                              <v-pagination v-model="current2" @input="onPageChange" :page-count="totalPages2" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                                              </div>
                                          </div>
                                          <div class="text-right">
                                              <button @click="addUserMarket()" type="button" class="btn btn-primary btn-sm">{{trans.get('keys.them_nhan_vien')}}</button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="card">
                                  <div class="card-header d-flex justify-content-between">
                                      <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_3" aria-expanded="false"><i class="fal fa-upload mr-3"></i>{{trans.get('keys.tai_len_file_excel')}}</a>
                                  </div>
                                  <div id="collapse_3" class="collapse" data-parent="#accordion_1">
                                      <div class="card-body">
                                          <p class="mb-3">
                                              <a :href="file_url" class="btn px-0 not_shadow"><i class="fal fa-file-alt mr-3"></i>{{trans.get('keys.tai_ve_bieu_mau')}}</a>
                                          </p>
                                          <input type="file" ref="file" name="file" class="dropify fileImport" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"  @change="selectedFile"/>
                                          <p class="mt-10"><label><input type="radio" v-model="importType" value="0"> {{trans.get('keys.them_moi')}}</label></p>
                                          <p><label><input type="radio" v-model="importType" value="1"> {{trans.get('keys.them_moi_va_cap_nhat')}}</label></p>
                                          <div class="button-list">
                                              <button type="button" class="btn btn-primary btn-sm hasLoading" @click="importExcel()">{{trans.get('keys.import_nguoi_dung')}}<i class="fa fa-spinner" aria-hidden="true"></i></button>
                                          </div>
                                          <div class="logUpload mt-4" v-if="data_import">
                                              <div v-if="!data_import.extension && !data_import.fileError">
                                                  <h5 class="hk-sec-title mb-3">{{trans.get('keys.thong_tin_tai_len')}}</h5>
                                                  <ul class="list-group mb-3">
                                                      <li v-for="user in data_import.userOuput"
                                                          :class="'list-group-item '+ (user.status == 'success'? 'list-group-item-success' : 'list-group-item-danger')">
                                                          <span v-if="user.fullname">{{trans.get('keys.stt')}}: {{user.stt}}. {{trans.get('keys.tai_khoan')}}: <strong>{{user.fullname}}</strong>. </span> {{user.message}}
                                                      </li>
                                                  </ul>
                                                  <p>{{trans.get('keys.tai_khoan_them_thanh_cong')}} : <strong class="text-success">{{data_import.rowSuccess}}</strong></p>
                                                  <p>{{trans.get('keys.tai_khoan_them_that_bai')}} : <strong class="text-danger">{{data_import.rowError}}</strong></p>
                                              </div>
                                              <div v-if="data_import.extension">
                                                  <strong class="text-danger">{{trans.get('keys.dinh_dang_file_khong_dung')}}.</strong>
                                              </div>
                                              <div v-if="data_import.fileError">
                                                  <strong class="text-danger">{{trans.get('keys.ban_chua_them_file')}}</strong>
                                              </div>
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
                                      <div class="col-sm-8 dataTables_wrapper">
                                          <div class="dataTables_length" style="display: inline-block;">
                                              <label>{{trans.get('keys.hien_thi')}}
                                                  <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getUser(1)">
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
                                                         class="form-control search_text" :placeholder="trans.get('keys.nhap_ten_tai_khoan_email_cmtnd') + ' ...'">
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
                                          {{trans.get('keys.tong_so_chuyen_vien_kinh_doanh_hien_tai')}} : {{ total_user }}
                                      </strong>
                                  </div>
                                  <div class="table-responsive">
                                      <table class="table_res">
                                          <thead>
                                          <tr>
                                              <th>{{trans.get('keys.stt')}}</th>
                                              <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                                              <th>{{trans.get('keys.tai_khoan')}}</th>
                                              <th>{{trans.get('keys.ten_nguoi_dung')}}</th>
                                              <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                                              <th class=" mobile_hide">{{trans.get('keys.so_dai_ly')}}</th>
                                              <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          <tr v-if="posts.length == 0">
                                              <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                                          </tr>
                                          <tr v-else v-for="(user,index) in posts">
                                              <td>{{ (current-1)*row+(index+1) }}</td>
                                              <td class=" mobile_hide">{{ user.cmtnd }}</td>
                                              <td>
                                                  <a :href="trans.get('keys.language')+'/system/user/edit/'+user.user_id">
                                                      {{ user.username }}
                                                  </a>
                                              </td>

                                              <td>{{ user.fullname }}</td>
                                              <td class=" mobile_hide">{{ user.email }}</td>
                                              <td class=" mobile_hide">
                                                  <a :title="trans.get('keys.phan_quyen_quan_ly_dai_ly')" :href="trans.get('keys.language')+'/system/user_market/organize/'+user.user_id">
                                                      {{ user.agents_length }}
                                                  </a>
                                              </td>
                                              <td class="text-center">

                                                <router-link :title="trans.get('keys.phan_quyen_quan_ly_dai_ly')"
                                                             :to="{ name: 'UserMarketOrganize', params: { user_id: user.user_id } }"
                                                             class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2">
                                                            <span class="btn-icon-wrap"><i class="fa fa-pencil-square-o"></i></span>
                                                </router-link>


                                                <router-link
                                                            :title="trans.get('keys.sua_nguoi_dung')"
                                                            :to="{ name: 'EditDetailUserById', params: { user_id: user.user_id }, query: {type: 'user_market'} }"
                                                            class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2">
                                                    <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                                  </router-link>

                                                  <button @click="removeUserMarket(user.user_id)" :title="trans.get('keys.go_nguoi_dung')" class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                                                      <span class="btn-icon-wrap"><i class="fa fa-eraser"></i></span>
                                                  </button>
                                              </td>
                                          </tr>
                                          </tbody>
                                          <tfoot>
                                          <tr>
                                              <th>{{trans.get('keys.stt')}}</th>
                                              <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                                              <th>{{trans.get('keys.tai_khoan')}}</th>
                                              <th>{{trans.get('keys.ten_nguoi_dung')}}</th>
                                              <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                                              <th class=" mobile_hide">{{trans.get('keys.so_dai_ly')}}</th>
                                              <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                          </tr>
                                          </tfoot>
                                      </table>
                                      <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                                      <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </section>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import CreateUserMarket from '../user/CreateUserMarketComponent'

    export default {
        //props: ['file_url'],
        //components: {vPagination},
        components: {CreateUserMarket},
        data() {
            return {
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                total_user:0,
                row:10,
                posts2: {},
                keyword2: '',
                current2: 1,
                totalPages2: 0,
                row2:10,
                user_select:[],
                allSelected:false,
                importType:0,
                data_import: {},
                role_name:'managemarket',
                urlImport:'/system/user/import_user',
                file_url: '',
            }
        },
        methods: {
            selectedFile() {
                let file = this.$refs.file.files[0];
                if(!file || (file.type !== 'application/vnd.ms-excel' && file.type !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && file.type !== '.csv')){
                    const input = this.$refs.file;
                    input.type = 'file';
                    this.$refs.file.value = '';
                    roam_message("error",current_pos.trans.get('keys.dinh_dang_file_khong_hop_le'));
                }
            },
            importExcel(){
                if(!$('button.hasLoading').hasClass('loadding')){
                    $('button.hasLoading').addClass('loadding');

                    this.formData = new FormData();
                    this.formData.append('file', this.$refs.file.files[0]);
                    this.formData.append('type', this.type);
                    this.formData.append('role_name', this.role_name);
                    this.formData.append('importType', this.importType);
                    axios.post(this.urlImport, this.formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(response => {
                            this.data_import = response.data;
                            $('button.hasLoading').removeClass('loadding');
                            $('.logUpload').show();
                            $('#btnFilter').trigger('click');
                        })
                        .catch(error => {
                            $('button.hasLoading').removeClass('loadding');
                            swal({
                                title: this.trans.get('keys.thong_bao'),
                                text: this.trans.get('keys.loi_he_thong_thao_tac_that_bai'),
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        });
                }
            },
            selectAllCheckbox(){
                this.user_select = [];
                if(!this.allSelected){
                    this.posts2.forEach((select) => {
                        this.user_select.push(select.user_id);
                    });
                }
            },
            showForm(id_form){
                var parent = $('#'+id_form).parent();
                if($('.'+id_form,parent).hasClass('open')){
                    $('.show_form',parent).removeClass('open');
                    $('.formHide').hide();
                }else{
                    $('.show_form',parent).removeClass('open');
                    $('.formHide').hide();
                    $('#'+id_form).show();
                    $('.'+id_form,parent).addClass('open');
                }
            },
            addUserMarket(){
                let current_pos = this;
                if(this.user_select.length  > 0){
                    axios.post('/system/user_market/add_user_role', {
                        user_select: this.user_select,
                    })
                    .then(response => {
                        roam_message(response.data.status,response.data.message);
                        /*$('#btnFilter').trigger('click');
                        $('#btnFilter2').trigger('click');
                        $('.show_form').trigger('click');*/
                    })
                    .catch(error => {
                        roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    });
                }
            },
            removeUserMarket(user_id){
                var user = user_id;
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_muon_go_quyen_giam_sat_thi_truong_cua_nguoi_dung_nay'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user_market/remove', {
                        user_id: user,
                    })
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                        })
                        .catch(error => {
                            roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
            },
            showUser(paged2) {
                axios.post('/system/user_market/show_user', {
                    page: paged2 || this.current2,
                    keyword: this.keyword2,
                    row:this.row2,
                })
                    .then(response => {
                        this.posts2 = response.data.data ? response.data.data.data : [];
                        this.current2 = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages2 = response.data.pagination ? response.data.pagination.total : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getUser(paged) {
                axios.post('/system/user_market/list_user', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row:this.row,
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
                this.showUser();
            },
            fetch() {
              axios.post('/bridge/fetch', {
                view: 'UserMarketIndex'
              })
                .then(response => {
                  this.file_url = response.data.file_url;
                })
                .catch(error => {
                  console.log(error);
                })
            },

          setFileInput() {
            $('.dropify').dropify();
          }
        },
        mounted() {
          this.fetch();
            //this.getUser();
        },
      updated() {
          this.setFileInput();
      }
    }
</script>

<style scoped>

</style>
