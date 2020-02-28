<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>

            <template v-if="owner_type === 'master'" >
              <li class="breadcrumb-item"><router-link to="/tms/branch/list">{{ trans.get('keys.dai_ly') }}</router-link></li>
              <li class="breadcrumb-item">{{ branch_name }}</li>
            </template>

            <li class="breadcrumb-item"><router-link to="/tms/saleroom/list">{{ trans.get('keys.danh_sach_diem_ban') }}</router-link></li>

            <template v-if="role === 'employee'">
              <li class="breadcrumb-item">
                <router-link :to="{
                name: 'SaleroomEditByRole',
                params: {saleroom_id: saleroom_id}
              }">{{ saleroom_name }}</router-link>
              </li>
              <li class="breadcrumb-item">
                <router-link :to="{
                name: 'SaleroomUserIndexByRole',
                params: {saleroom_id: saleroom_id}
              }">{{ trans.get('keys.danh_sach_nhan_vien') }}</router-link>
              </li>
              <li class="breadcrumb-item active">{{ trans.get('keys.sua_thong_tin_nhan_vien') }}</li>
            </template>
            <li v-if="role === 'manager'" class="breadcrumb-item active">{{ trans.get('keys.sua_thong_tin_chu_diem_ban') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="row mx-0">
        <div class="col-12 hk-sec-wrapper">
          <h5 class="hk-sec-title">{{trans.get('keys.chinh_sua_thong_tin_nguoi_dung')}}</h5>
          <div class="row">
            <div class="col-12 col-lg-3">
              <div class="card">
                <div style="padding: 10px">
                  <img :src="users.avatar" alt="">
                </div>
                <div class="card-body">
                  <p>
                    <input  type="file" ref="file" name="file" class="dropify" />
                  </p>
                </div>
                <div class="form-group">
                  <div class="card-body">
                    <a class="mb-10 btnShowChangePass" style="cursor: pointer;display:block;text-decoration: underline;" @click="showChangePass()">{{trans.get('keys.thay_doi_mat_khau')}} > </a>

                    <div class="showChangePass" style="display:none;">
                                        <span class="wrap_password pass">
                                            <input autocomplete="false" v-model="password" type="password" id="inputPassword" :placeholder="trans.get('keys.mat_khau')+' *'" class="form-control mb-4 " @input="validate_password">
                                            <i class="fa fa-check-circle-o label_validate" aria-hidden="true"></i>
                                        </span>
                      <label class="text-danger message error passError" style="display: none;">{{trans.get('keys.vui_long_nhap_mat_khau_moi')}}!</label>
                      <label class="text-danger message error passStyleError" style="display: none;">{{trans.get('keys.mat_khau_sai_dinh_dang')}}!</label>
                      <span class="wrap_password pass_conf">
                                            <input autocomplete="false" v-model="passwordConf" type="password" id="inputPasswordConfirm" :placeholder="trans.get('keys.nhap_lai_mat_khau')+' *'" class="form-control mb-4" @input="validate_password">
                                            <i class="fa fa-check-circle-o label_validate" aria-hidden="true"></i>
                                        </span>
                      <label class="text-danger message error passConfError" style="display: none;">{{trans.get('keys.vui_long_nhap_mat_khau_moi')}}!</label>
                      <label class="text-danger message error passConfStyleError" style="display: none;">{{trans.get('keys.mat_khau_sai_dinh_dang')}}!</label>
                      <label>
                        <input type="checkbox" @click="viewPassword()">{{trans.get('keys.hien_thi_mat_khau')}}
                      </label>
                      <button type="button" class="btn btn-primary btn-sm" @click="updatePassword()">{{trans.get('keys.cap_nhat_mat_khau')}}</button>
                      <label class="text-danger message error passwordError" style="display: none;">{{trans.get('keys.mat_khau_khong_khop_nhau')}}!</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-9">
              <form action="" class="form-row">
                <div class="col-6 form-group" v-if="users.student_role > 0">
                  <label for="inputConfirm">{{trans.get('keys.cap_giay_chung_nhan')}}</label>
                  <select id="inputConfirm" class="form-control custom-select" v-model="users.confirm" @change="changeConfirm()">
                    <option value="0">{{trans.get('keys.chua_cap')}}</option>
                    <option value="1">{{trans.get('keys.hanh_dong')}}</option>
                  </select>
                </div>

                <div class="col-6 form-group" v-if="users.student_role > 0">
                  <label for="inputConfirmAddress">{{trans.get('keys.noi_cap_giay_chung_nhan')}}</label>
                  <select id="inputConfirmAddress" class="form-control custom-select" v-model="users.confirm_address" :disabled="(users.confirm == 0) ? true : false">
                    <option value="0">{{trans.get('keys.chua_cap_nhat')}}</option>
                    <option v-for="city in citys" :value="city.id">{{ city.name }}</option>
                  </select>
                </div>

                <div class="col-4 form-group">
                  <label for="inputUsername">{{trans.get('keys.ten_dang_nhap')}} *</label>
                  <input type="text" id="inputUsername" v-model="users.username" class="form-control mb-4" autocomplete="false" :disabled="(users.confirm == 0) ? true : false" @input="changeRequired('inputUsername')" readonly>
                  <label v-if="!users.username" class="required text-danger username_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-4 form-group">
                  <label for="inputCmtnd">{{trans.get('keys.cmnd')}} *</label>
                  <input type="text" id="inputCmtnd" v-model="users.cmtnd" class="form-control mb-4" @input="changeRequired('inputCmtnd')">
                  <label v-if="!users.cmtnd" class="required text-danger cmtnd_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-4 form-group">
                  <label for="inputFullname">{{trans.get('keys.ho_va_ten')}} *</label>
                  <input type="text" id="inputFullname" v-model="users.fullname" class="form-control mb-4" @input="changeRequired('inputFullname')">
                  <label v-if="!users.fullname" class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-4 form-group">
                  <label for="inputDob">{{trans.get('keys.ngay_sinh')}}</label>
                  <input type="date" id="inputDob" v-model="users.dob" class="form-control mb-4">
                </div>
                <div class="col-4 form-group">
                  <label for="inputAddress">{{trans.get('keys.dia_chi')}}</label>
                  <input type="text" id="inputAddress" v-model="users.address" class="form-control mb-4">
                </div>
                <div class="col-4 form-group">
                  <label for="inputEmail">{{trans.get('keys.email')}} *</label>
                  <input type="text" id="inputEmail" v-model="users.email" class="form-control mb-4" @input="changeRequired('inputEmail')">
                  <label v-if="!users.email" class="required text-danger email_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>

                <div class="col-4 form-group">
                  <label for="inputPhone">{{trans.get('keys.so_dt')}}</label>
                  <input type="text" id="inputPhone" v-model="users.phone" class="form-control mb-4">
                </div>

                <div class="col-4 form-group">
                  <label for="inputSex">{{trans.get('keys.gioi_tinh')}}</label>
                  <select id="inputSex" class="form-control custom-select" v-model="users.sex">
                    <option value="1">{{trans.get('keys.nam')}}</option>
                    <option value="0">{{trans.get('keys.nu')}}</option>
                  </select>
                </div>

                <div class="col-4 form-group">
                  <label for="inputCode">{{trans.get('keys.ma_nhan_vien')}}</label>
                  <input v-model="users.code" type="text" id="inputCode" placeholder="" class="form-control mb-4">
                </div>

                <div class="col-4 form-group">
                  <label for="inputTimeStart">{{trans.get('keys.ngay_bat_dau_lam')}}</label>
                  <input v-model="users.start_time" type="date" id="inputTimeStart" class="form-control mb-4">
                </div>

                <div class="col-4 form-group">
                  <label for="inputWorkingStatus">{{trans.get('keys.tinh_trang_viec_lam')}}</label>
                  <select id="inputWorkingStatus" class="form-control custom-select" v-model="users.working_status">
                    <option value="0">{{trans.get('keys.dang_cong_tac')}}</option>
                    <option value="1">{{trans.get('keys.nghi_cong_tac')}}</option>
                  </select>
                </div>

              </form>
              <div class="button-list" v-if="users.deleted === 0">
                <button type="button" class="btn btn-primary btn-sm" @click="updateUser()">{{trans.get('keys.cap_nhat_thong_tin')}}</button>

                <router-link v-if="type === 'saleroom'"
                             :to="{
                                name: 'SaleroomUserViewByRole',
                                params: {saleroom_id: saleroom_id, user_id: user_id},
                                query: {type: owner_type}
                             }"
                             class="btn btn-secondary btn-sm">
                  {{trans.get('keys.quay_lai')}}
                </router-link>
                <router-link v-else-if="type === 'branch'"
                             :to="{
                                name: 'BranchUserViewByRole',
                                params: {branch_id: branch_id, user_id: user_id},
                                query: {type: owner_type}
                             }"
                             class="btn btn-secondary btn-sm">{{trans.get('keys.quay_lai')}}
                </router-link>

              </div>
              <div class="text-right" v-else>
                <a :title="trans.get('keys.khoi_phuc_tai_khoan')" href="#" class="btn btn-sm btn-success" @click="restoreUser(users.user_id)">
                  {{trans.get('keys.khoi_phuc')}}
                </a>
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
        props: [
          'user_id',
          'type',
          'owner_type',
          'saleroom_id'
        ],
        data() {
            return {
                saleroom_name: '',
                branch_id: 0,
                branch_name: '',
                role: '',
                users: {
                    role:[],
                    confirm_address:0,
                    start_time:'',
                    password:''
                },
                roles:{},
                citys:[],
                passwordConf:'',
                password:'',
                placeholder_user_name: '',
            }
        },
        methods:{
            changeRequired(element){
                $('#'+element).removeClass('notValidate');
            },
            viewPassword(){
                var inputPassword = document.getElementById("inputPassword");
                var inputPasswordConfirm = document.getElementById("inputPasswordConfirm");
                if (inputPassword.type === "password") {
                    inputPassword.type = "text";
                    inputPasswordConfirm.type = "text";
                } else {
                    inputPassword.type = "password";
                    inputPasswordConfirm.type = "password";
                }
            },
            showChangePass(){
                if($('.btnShowChangePass').hasClass('active')){
                    $('.showChangePass').slideUp();
                    $('.btnShowChangePass').removeClass('active');
                }else{
                    $('.showChangePass').slideDown();
                    $('.btnShowChangePass').addClass('active');
                }
                return;
            },
            updatePassword(){
                $('.message.error').hide();
                if(this.password.length == 0){
                    $('.passError').show();
                    return;
                }
                if( this.passwordConf.length == 0 ){
                    $('.passConfError').show();
                    return;
                }
                if( this.password != this.passwordConf){
                    $('.passwordError').show();
                    return;
                }
                axios.post('/system/user/updatePassword', {
                    user_id:this.user_id,
                    password:this.password,
                    passwordConf:this.passwordConf,
                })
                    .then(response => {
                        if(response.data == 'success'){
                            roam_message('success','Cập nhật mật khẩu thành công!');
                            $('.showChangePass').slideUp();
                            $('.btnShowChangePass').removeClass('active');
                        }else if(response.data == 'passwordFail'){
                            $('.passwordError').show();
                            roam_message('error','Mật khẩu không khớp nhau!');
                        }else if(response.data == 'passFail'){
                            $('.passStyleError').show();
                            roam_message('error','Mật khẩu chưa đáp ứng. Mật khẩu cần bao gồm chữ in hoa, số và ký tự đặc biệt.');
                        }else if(response.data == 'passConfFail'){
                            $('.passConfStyleError').show();
                            roam_message('error','Mật khẩu chưa đáp ứng. Mật khẩu cần bao gồm chữ in hoa, số và ký tự đặc biệt.');
                        }else if(response.data == 'passwordExist'){
                            $('.wrap_password').removeClass('success');
                            $('.wrap_password').addClass('warning');
                            roam_message('error','Mật khẩu mới trùng mật khẩu đang sử dụng.');
                        }else if(response.data == 'validateFail'){
                            roam_message('error','Lỗi định dạng. Một số trường nhập vào chứa ký tự đặc biệt!');
                        }else{
                            roam_message('error','Lỗi hệ thống. Thao tác thất bại!');
                        }
                    })
                    .catch(error => {
                        roam_message('error','Lỗi hệ thống. Thao tác thất bại!');
                    });
            },
            validate_password(){
                axios.post('/validate_password',{
                    password:this.password,
                    passwordConf:this.passwordConf,
                })
                    .then(response => {
                        if(response.data.password == 'password_success'){
                            $('span.wrap_password.pass').removeClass('warning');
                            $('span.wrap_password.pass').addClass('success');
                        }else if(response.data.password == 'password_warning'){
                            $('span.wrap_password.pass').removeClass('success');
                            $('span.wrap_password.pass').addClass('warning');
                        }

                        if(response.data.passwordConf == 'passwordConf_success'){
                            $('span.wrap_password.pass_conf').removeClass('warning');
                            $('span.wrap_password.pass_conf').addClass('success');
                        }else if(response.data.passwordConf == 'passwordConf_warning'){
                            $('span.wrap_password.pass_conf').removeClass('success');
                            $('span.wrap_password.pass_conf').addClass('warning');
                        }
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            changeConfirm(){
                if(this.confirm == 1){
                    $('#inputConfirmAddress').attr('disabled',false);
                    $('#inputUsername').attr('placeholder','Nhập mã chứng nhận');
                }else{
                    $('#inputConfirmAddress').attr('disabled',true);
                    $('#inputUsername').attr('placeholder','Nhập tài khoản đăng nhập');
                }

            },
            userData(){
                axios.post('/system/user/detail',{
                    user_id:this.user_id
                })
                    .then(response => {
                        this.users = response.data;
                    })
                    .catch(error => {

                    })
            },
            getCitys() {
                axios.post('/system/list/list_city')
                    .then(response => {
                        this.citys = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            updateUser(){
                if(!this.users.username) {
                    $('.username_required').show();
                    return;
                }
                if(!this.users.email){
                    $('.email_required').show();
                    return;
                }
                if(!this.users.cmtnd){
                    $('.cmtnd_required').show();
                    return;
                }
                if(!this.users.fullname){
                    $('.fullname_required').show();
                    return;
                }
                this.formData = new FormData();
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('fullname', this.users.fullname);
                this.formData.append('dob', this.users.dob);
                this.formData.append('email', this.users.email);
                this.formData.append('username', this.users.username);
                //this.formData.append('password', this.users.password);
                this.formData.append('phone', this.users.phone);
                this.formData.append('cmtnd', this.users.cmtnd);
                this.formData.append('address', this.users.address);
                this.formData.append('role', this.users.role);
                this.formData.append('type', this.type);
                this.formData.append('user_id', this.user_id);
                this.formData.append('sex', this.users.sex);
                this.formData.append('code', this.users.code);
                this.formData.append('start_time', this.users.start_time);
                this.formData.append('working_status', this.users.working_status);
                this.formData.append('confirm_address', this.users.confirm_address);
                this.formData.append('confirm', this.users.confirm);
                axios.post('/system/user/update', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if(response.data.status){
                            roam_message(response.data.status,response.data.message);
                        }else{
                            roam_message('error',response.data.message);
                            $('.form-control').removeClass('notValidate');
                            $('#'+response.data.id).addClass('notValidate');
                        }
                    })
                    .catch(error => {
                        roam_message('error','Lỗi hệ thống. Thao tác thất bại');
                    });
            },
            restoreUser(user_id){
                swal({
                    title: "Bạn muốn khôi phục lại tài khoản này",
                    text: "Chọn 'ok' để thực hiện thao tác.",
                    type: "error",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user/restore',{user_id:user_id})
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                            location.reload();
                        })
                        .catch(error => {
                            roam_message('error','Lỗi hệ thống. Thao tác thất bại');
                        });
                });

                return false;
            },
            fetch() {
            axios.post('/bridge/fetch', {
              user_id: this.user_id,
              saleroom_id: this.saleroom_id,
              view: 'SaleroomUserEditByRole'
            })
              .then(response => {
                this.saleroom_name = response.data.saleroom_name;
                this.role = response.data.role;
                this.branch_id = response.data.branch_id;
                this.branch_name = response.data.branch_name;
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
            this.userData();
            this.getCitys();
            this.fetch();
            this.setFileInput();
        }
    }
</script>

<style scoped>

</style>
