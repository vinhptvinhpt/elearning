<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">
                {{ trans.get('keys.dashboard') }}
              </router-link>
            </li>
            <li v-if="type === 'teacher'" class="breadcrumb-item active">{{ trans.get('keys.quan_tri_giang_vien') }}
            </li>
            <li v-else-if="type === 'student'" class="breadcrumb-item active">{{ trans.get('keys.quan_tri_hoc_vien')
              }}
            </li>
            <li v-else class="breadcrumb-item active">{{ trans.get('keys.quan_tri_nguoi_dung') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <section class="hk-sec-wrapper">
          <h5 class="hk-sec-title" v-if="type == 'system'">{{trans.get('keys.danh_sach_nguoi_dung')}}</h5>
          <h5 class="hk-sec-title" v-else-if="type == 'teacher'">{{trans.get('keys.danh_sach_giang_vien')}}</h5>
          <h5 class="hk-sec-title" v-else>{{trans.get('keys.danh_sach_hoc_vien')}}</h5>
          <div class="row mb-4">
            <div class="col-sm">
              <div class="accordion" id="accordion_1">
                <div class="card" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125);">
                  <div class="card-header d-flex justify-content-between">
                    <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1" aria-expanded="true"><i
                      class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
                  </div>
                  <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                    <div class="card-body">
                      <system-user-create v-if="roles_ready === true"
                        :type="type"
                        :current_roles="current_roles"
                        :roles_ready="roles_ready"></system-user-create>
                    </div>
                  </div>
                </div>
                <div class="card" style="display: none">
                  <div class="card-header d-flex justify-content-between">
                    <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_2"
                       aria-expanded="false"><i class="fal fa-upload mr-3"></i>{{trans.get('keys.tai_len_file_excel')}}</a>
                  </div>
                  <div id="collapse_2" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                      <p class="mb-3">
                        <a :href="file_url" class="btn px-0 not_shadow"><i class="fal fa-file-alt mr-3"></i>{{trans.get('keys.tai_ve_bieu_mau')}}</a>
                      </p>
                      <input type="file" ref="file" name="file" class="dropify fileImport"
                             accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                             @change="selectedFile"/>
                      <p class="mt-10"><label><input type="radio" v-model="importType" value="0">
                        {{trans.get('keys.them_moi')}}</label></p>
                      <p><label><input type="radio" v-model="importType" value="1">
                        {{trans.get('keys.them_moi_va_cap_nhat')}}</label></p>
                      <div class="button-list">
                        <button type="button" class="btn btn-primary btn-sm hasLoading" @click="importExcel()">
                          {{trans.get('keys.import_nguoi_dung')}}<i class="fa fa-spinner" aria-hidden="true"></i>
                        </button>
                      </div>
                      <div class="logUpload mt-4" v-if="data_import">
                        <div v-if="!data_import.extension && !data_import.fileError">
                          <h5 class="hk-sec-title mb-3">{{trans.get('keys.thong_tin_tai_len')}}</h5>
                          <ul class="list-group mb-3">
                            <li v-for="user in data_import.userOuput"
                                :class="'list-group-item '+ (user.status == 'success'? 'list-group-item-success' : 'list-group-item-danger')">
                              <!--                                                        <span v-if="user.username">{{trans.get('keys.tai_khoan')}}: <strong>{{user.username}}</strong>. </span> {{user.message}}-->
                              <span v-if="user.fullname">{{trans.get('keys.stt')}}: {{user.stt}}. {{trans.get('keys.tai_khoan')}}: <strong>{{user.fullname}}</strong>. </span>
                              {{user.message}}
                            </li>
                          </ul>
                          <p>{{trans.get('keys.tai_khoan_them_thanh_cong')}} : <strong class="text-success">{{data_import.rowSuccess}}</strong>
                          </p>
                          <p>{{trans.get('keys.tai_khoan_them_that_bai')}} : <strong class="text-danger">{{data_import.rowError}}</strong>
                          </p>
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
                <!--<div class="card" v-if="type == 'system'">
                    <div class="card-header d-flex justify-content-between">
                        <a class="collapsed" target="_blank" href="/system/user/trash"><i class="fa fa-trash-o mr-3"></i></i>Tài khoản đã xóa</a>
                    </div>
                </div>
                <div class="card" v-else-if="type == 'teacher'">
                    <div class="card-header d-flex justify-content-between">
                        <a class="collapsed" target="_blank" href="/education/user_teacher/trash"><i class="fa fa-trash-o mr-3"></i></i>Giáo viên đã xóa</a>
                    </div>
                </div>
                <div class="card" v-else>
                    <div class="card-header d-flex justify-content-between">
                        <a class="collapsed" target="_blank" href="/education/user_student/trash"><i class="fa fa-trash-o mr-3"></i></i>Học viên đã xóa</a>
                    </div>
                </div>-->
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm">
              <div class="table-wrap">

                <div class="row">
                  <div class="fillterConfirm col-sm-4 offset-sm-4" style="display: inline-block;">
                    <!--                    <v-select-->
                    <!--                      :options="userSelectOptions"-->
                    <!--                      :reduce="userSelectOption => userSelectOption.id"-->
                    <!--                      :placeholder="this.trans.get('keys.chon_nguoi_dung')"-->
                    <!--                      :filter-by="myFilterBy"-->
                    <!--                      v-model="user_filter">-->
                    <!--                    </v-select>-->
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <form v-on:submit.prevent="getUser(1)">
                        <div class="d-flex flex-row">
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
                </div>

                <div class="row">
                  <div class="col-md-12 col-sm-12 dataTables_wrapper">
                    <!--Items per page -->
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
                    <div class="fillterConfirm" style="display: inline-block;" v-if="type == 'system'">
                      <label>
                        <select v-model="roles" class="custom-select custom-select-sm form-control form-control-sm"
                                @change="getUser(1)">
                          <option value="0">{{trans.get('keys.theo_quyen')}}</option>
                          <option v-for="role in listrole" :value="role.id">{{ trans.has('keys.' + role.name) ? trans.get('keys.' + role.name) : role.name.charAt(0).toUpperCase() + role.name.slice(1) }}</option>
                        </select>
                      </label>
                    </div>
                    <div class="fillterConfirm" style="display: inline-block;" v-if="type == 'student'">
                      <label>
                        <select v-model="confirm" class="custom-select custom-select-sm form-control form-control-sm"
                                @change="getUser(1)">
                          <option value="">{{trans.get('keys.giay_chung_nhan')}}</option>
                          <option value="1">{{trans.get('keys.da_co')}}</option>
                          <option value="0">{{trans.get('keys.chua_co')}}</option>
                        </select>
                      </label>
                    </div>
                  </div>
                </div>

                <div class="mt-10 mb-20">
                  <strong v-if="type == 'student'">
                    {{trans.get('keys.tong_so_hoc_vien_hien_tai')}} : {{ total_user }}
                  </strong>
                  <strong v-else-if="type == 'teacher'">
                    {{trans.get('keys.tong_so_giao_vien_hien_tai')}} : {{ total_user }}
                  </strong>
                  <strong v-else>
                    {{trans.get('keys.tong_so_nguoi_dung_hien_tai')}} : {{ total_user }}
                  </strong>
                </div>
                <div class="table-responsive">
                  <table class="table_res">
                    <thead>
                    <tr>
                      <th>
                        <input v-model="allSelected" @click="selectAllCheckbox()" id="branch-select-all" type="checkbox"
                               class="filled-in chk-col-light-blue" name="select_all" value=""/>
                        <label for="branch-select-all"></label>
                      </th>
                      <th>{{trans.get('keys.so_cmtnd')}}</th>
                      <th>{{trans.get('keys.tai_khoan')}}</th>
                      <th class="mobile_hide">{{trans.get('keys.ten_nguoi_dung')}}</th>
                      <th class="mobile_hide">{{trans.get('keys.email')}}</th>
                      <th v-if="type == 'student'" class="mobile_hide">{{trans.get('keys.giay_chung_nhan')}}</th>
                      <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="posts.length == 0">
                      <td colspan="8">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                    </tr>
                    <tr v-else v-for="(user,index) in posts">
                      <td>
                        <input v-model="user_delete" :value="user.user_id" type="checkbox"
                               :id="'delete_user'+user.user_id" class="filled-in chk-col-light-blue check_box_branch">
                        <label :for="'delete_user'+user.user_id"></label>
                      </td>
                      <td>{{ user.cmtnd }}</td>

                      <td>
                        <router-link
                          :to="{ path: 'system/user/edit', name: 'EditUserById', params: { user_id: user.user_id }, query: {type: type} }">
                          {{ user.username }}
                        </router-link>
                      </td>

                      <td class="mobile_hide">{{ user.fullname }}</td>
                      <td class="mobile_hide">{{ user.email }}</td>
                      <td class="mobile_hide" v-if="type == 'student'">{{ (user.confirm && user.confirm == 1) ? trans.get('keys.da_co') : trans.get('keys.chua_co') }}
                      </td>
                      <td class="text-center">
                        <router-link
                          :title="trans.get('keys.sua')"
                          :to="{ name: 'EditDetailUserById', params: { user_id: user.user_id }, query: {type: type} }"
                          class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2">
                          <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                        </router-link>
                        <button @click.prevent="deletePost('/system/user/delete/'+user.user_id)"
                                class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2"><span
                          class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
                      </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                      <th>
                        <input v-model="allSelected" @click="selectAllCheckbox()" id="branch-select-all2"
                               type="checkbox" class="filled-in chk-col-light-blue" name="select_all" value=""/>
                        <label for="branch-select-all2"></label>
                      </th>
                      <th>{{trans.get('keys.so_cmtnd')}}</th>
                      <th>{{trans.get('keys.tai_khoan')}}</th>
                      <th class="mobile_hide">{{trans.get('keys.ten_nguoi_dung')}}</th>
                      <th class="mobile_hide">{{trans.get('keys.email')}}</th>
                      <th v-if="type == 'student'" class="mobile_hide">{{trans.get('keys.giay_chung_nhan')}}</th>
                      <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                    </tr>
                    </tfoot>
                  </table>
                  <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                  :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                  </div>
                  <div class="text-right">
                    <button :title="trans.get('keys.xoa_tai_khoan_da_chon')" type="button" style="float: right;"
                            class="btn btn-sm btn-danger mt-3" @click="deleteSelectUser()">
                      {{trans.get('keys.xoa')}}
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
  //import vPagination from 'vue-plain-pagination'
  import SystemUserCreate from './CreateComponent'

  export default {
    props: {
      type: {
        type: String,
        default: 'system'
      },
      current_roles: Object,
      roles_ready: Boolean
    },
    //components: {vPagination},
    components: {SystemUserCreate},
    data() {
      return {
        posts: {},
        keyword: '',
        current: 1,
        totalPages: 0,
        total_user: 0,
        row: 10,
        urlListUser: '/system/user/list',
        urlImport: '/system/user/import_user',
        data_import: {},
        role_name: 'student',
        importType: 0,
        confirm: '',
        roles: 0,
        listrole: {},
        user_delete: [],
        allSelected: false,
        file_url: '',
        user_filter: '',
        userSelectOptions: []
      }
    },
    methods: {
      selectedFile() {
        let file = this.$refs.file.files[0];
        if (!file || (file.type !== 'application/vnd.ms-excel' && file.type !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && file.type !== '.csv')) {
          const input = this.$refs.file;
          input.type = 'file';
          this.$refs.file.value = '';
          toastr['error'](this.trans.get('keys.dinh_dang_file_khong_hop_le'), this.trans.get('keys.that_bai'));
        }
      },
      selectAllCheckbox() {
        this.user_delete = [];
        if (!this.allSelected) {
          this.posts.forEach((select) => {
            this.user_delete.push(select.user_id);
          });
        }
      },
      getListRole() {
        axios.post('/system/user/get_list_role')
          .then(response => {
            this.listrole = response.data;
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      importExcel() {
        if (!$('button.hasLoading').hasClass('loadding')) {
          $('button.hasLoading').addClass('loadding');
          if (this.type === 'teacher') {
            //this.urlImport = '/system/user/import_teacher';
            this.role_name = 'teacher';
          }
          if (this.type === 'student') {
            //this.urlImport = '/system/user/import_student';
          }
          this.formData = new FormData();
          this.formData.append('file', this.$refs.file.files[0]);
          this.formData.append('type', this.type);
          this.formData.append('role_name', this.role_name);
          this.formData.append('importType', this.importType);

          let current_pos = this;

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
                title: current_pos.trans.get('keys.thong_bao'),
                text: current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'),
                type: "error",
                showCancelButton: false,
                closeOnConfirm: false,
                showLoaderOnConfirm: true
              });
            });
        }
      },
      getUser(paged) {
        if (this.type === 'teacher') {
          this.urlListUser = '/education/user/list_teacher';
        }
        if (this.type === 'student') {
          this.urlListUser = '/education/user/list_student';
        }

        axios.post(this.urlListUser, {
          page: paged || this.current,
          keyword: this.keyword,
          row: this.row,
          confirm: this.confirm,
          roles: this.roles,
          user: this.user_filter
        })
          .then(response => {
            this.posts = response.data.data ? response.data.data.data : [];
            this.current = response.data.pagination ? response.data.pagination.current_page : 1;
            this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
            this.total_user = response.data.pagination ? response.data.pagination.total_user : 0;
          })
          .catch(error => {
            //console.log(error.response.data);
          });
      },
      onPageChange() {
        this.getUser();

      },
      deleteSelectUser() {
        let user_delete = this.user_delete;
        let current_pos = this;
        if (this.user_delete.length === 0) {
          toastr['warning'](this.trans.get('keys.ban_chua_chon_tai_khoan'), this.trans.get('keys.thong_bao'));
          return;
        }
        swal({
          title: this.trans.get('keys.thong_bao'),
          text: this.trans.get('keys.ban_muon_xoa_nhung_tai_khoan_da_chon'),
          type: "warning",
          showCancelButton: true,
          closeOnConfirm: false,
          showLoaderOnConfirm: true
        }, function () {
          axios.post('/system/user/delete_list_user', {
            user_delete: user_delete
          })
            .then(response => {
                swal.close();
              if (response.data === 'success') {
                toastr['success'](current_pos.trans.get('keys.xoa_tai_khoan_thanh_cong'), current_pos.trans.get('keys.thanh_cong'));
                $('#btnFilter').trigger('click');
                //   const index = current_pos.posts.findIndex(post => post.user_id == user_delete);
                //   if (~index) // if the post exists in array
                //       current_pos.posts.splice(index, 1); //delete the post
                this.user_delete = [];
              } else {
                toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
              }
            })
            .catch(error => {
              toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
            });
        });
      },
      deletePost(url) {
        let current_pos = this;
        swal({
            title: this.trans.get('keys.ban_muon_xoa_muc_da_chon'),
          text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
          type: "error",
          showCancelButton: true,
          closeOnConfirm: true,
          showLoaderOnConfirm: true
        }, function () {
          axios.post(url)
            .then(response => {
              toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                $('#btnFilter').trigger('click');
              // var url_split = url.split('/');
              // var user_id = url_split[url_split.length - 1];
              //   const index = current_pos.posts.findIndex(post => post.user_id == user_id);
              //   if (~index) // if the post exists in array
              //       current_pos.posts.splice(index, 1); //delete the post
            })
            .catch(error => {
              toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
              //console.log(error);
            });
        });

        return false;
      },
      fetch() {
        axios.post('/bridge/fetch', {
          type: this.type,
          view: 'SystemUserList'
        })
          .then(response => {
            this.file_url = response.data.file_url;
          })
          .catch(error => {
            //console.log(error);
          })
      },
      myFilterBy: (option, label, search) => {
        if (!label) {
          label = '';
        }
        let new_search = convertUtf8(search);
        let new_label = convertUtf8(label);
        //return this.filterBy(option, new_label, new_search); //can not call components function here
        return (new_label || '').toLowerCase().indexOf(new_search) > -1; // "" not working
      },
      getUserForFilter() {
        this.user = '';
        this.userSelectOptions = []; //reset after search again

        axios.post('/system/filter/fetch', {
          type: 'user'
        })
          .then(response => {

            let additionalSelections = [];
            response.data.forEach(function (selectItem) {
              let newItem = {
                label: selectItem.label,
                id: selectItem.id
              };
              additionalSelections.push(newItem);
            });
            this.userSelectOptions = additionalSelections;

          })
          .catch(error => {
            console.log(error);
          });
      }
    },
    mounted() {
      //this.getUser();
      this.getListRole();
      this.fetch();
      //this.getUserForFilter();
      this.getUser();
    }
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
</script>

<style scoped>

</style>
