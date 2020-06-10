<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/profile">{{ trans.get('keys.thong_tin_ca_nhan') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.sua_thong_tin_ca_nhan') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="row mx-0">
        <div class="col-12 hk-sec-wrapper">
          <h5 class="hk-sec-title" v-if="type == 'system'">{{trans.get('keys.chinh_sua_thong_tin_nguoi_dung')}}</h5>
          <h5 class="hk-sec-title" v-else-if="type == 'teacher'">{{trans.get('keys.chinh_sua_thong_tin_giang_vien')}}</h5>
          <h5 class="hk-sec-title" v-else>{{trans.get('keys.chinh_sua_thong_tin_hoc_vien')}}</h5>
          <div class="row">
            <div class="col-12 col-lg-3">
              <div class="card">
                <div style="padding: 10px">
                  <img :src="users.avatar" alt="">
                </div>
                <div class="card-body">
                    <input type="file" ref="file" name="file" class="dropify" />
                </div>
                <div class="form-group">
                  <div class="card-body">
                    <a class="mb-10 btnShowChangePass" style="cursor: pointer;display:block;text-decoration: underline;" @click="showChangePass()">{{trans.get('keys.thay_doi_mat_khau')}} > </a>

                    <div class="showChangePass" style="display:none;">
                                        <span class="wrap_password pass">
                                            <input autocomplete="false" v-model="password" type="password" id="inputPassword" :placeholder="trans.get('keys.mat_khau') + ' *'" class="form-control mb-4 " @input="validate_password">
                                            <i class="fa fa-check-circle-o label_validate" aria-hidden="true"></i>
                                        </span>
                      <label class="text-danger message error passError" style="display: none;">{{trans.get('keys.vui_long_nhap_mat_khau_moi')}}!</label>
                      <label class="text-danger message error passStyleError" style="display: none;">{{trans.get('keys.mat_khau_sai_dinh_dang')}}!</label>
                      <span class="wrap_password pass_conf">
                                            <input autocomplete="false" v-model="passwordConf" type="password" id="inputPasswordConfirm" :placeholder="trans.get('keys.nhap_lai_mat_khau') + ' *'" class="form-control mb-4" @input="validate_password">
                                            <i class="fa fa-check-circle-o label_validate" aria-hidden="true"></i>
                                        </span>
                      <label class="text-danger message error passConfError" style="display: none;">{{trans.get('keys.vui_long_nhap_mat_khau_moi')}}!</label>
                      <label class="text-danger message error passConfStyleError" style="display: none;">{{trans.get('keys.mat_khau_sai_dinh_dang')}}!</label>
                      <label  style="display: block;">
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
                <div class="col-4 form-group">
                  <label for="inputUsername">{{trans.get('keys.ten_dang_nhap')}} *</label>
                  <input type="text" id="inputUsername" :value="users.username" class="form-control mb-4" autocomplete="false" disabled="true">
                </div>
                <div class="col-4 form-group">
                  <label for="inputCmtnd">{{trans.get('keys.cmnd')}} *</label>
                  <input type="text" id="inputCmtnd" :value="users.cmtnd" class="form-control mb-4" disabled="true">
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
                  <label for="inputEmail">{{trans.get('keys.email')}}</label>
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

              </form>
              <div class="button-list">
                <button type="button" class="btn btn-primary btn-sm" @click="updateUser()">{{trans.get('keys.cap_nhat_thong_tin')}}</button>
                <router-link to="/tms/profile" class="btn btn-secondary btn-sm">{{trans.get('keys.quay_lai')}}</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    import Ls from '../../../services/ls'

    export default {
        props: ['type', 'userid'],
        data() {
            return {
                users: {},
                roles:{},
                citys:[],
                passwordConf:'',
                password:'',
                user_id: 0
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

                let current_pos = this;

                axios.post('/system/user/updatePassword', {
                    user_id:this.user_id,
                    password:this.password,
                    passwordConf:this.passwordConf,
                })
                    .then(response => {
                        if(response.data == 'success'){
                            roam_message('success',current_pos.trans.get('keys.cap_nhat_mat_khau_thanh_cong'));
                            $('.showChangePass').slideUp();
                            $('.btnShowChangePass').removeClass('active');
                        }else if(response.data == 'passwordFail'){
                            $('.passwordError').show();
                            roam_message('error',current_pos.trans.get('keys.mat_khau_khong_khop_nhau'));
                        }else if(response.data == 'passFail'){
                            $('.passStyleError').show();
                            roam_message('error',current_pos.trans.get('keys.mat_khau_chua_dap_ung_mat_khau_can_bao_gom_chu_in_hoa_so_va_ky_tu_dac_biet'));
                        }else if(response.data == 'passConfFail'){
                            $('.passConfStyleError').show();
                            roam_message('error',current_pos.trans.get('keys.mat_khau_chua_dap_ung_mat_khau_can_bao_gom_chu_in_hoa_so_va_ky_tu_dac_biet'));
                        }else if(response.data == 'passwordExist'){
                            $('.wrap_password').removeClass('success');
                            $('.wrap_password').addClass('warning');
                            roam_message('error',current_pos.trans.get('keys.mat_khau_moi_trung_mat_khau_dang_su_dung'));
                        }else if(response.data == 'validateFail'){
                            roam_message('error',current_pos.trans.get('keys.loi_dinh_dang_mot_so_truong_nhap_vao_chua_ky_tu_dac_biet'));
                        }else{
                            roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        }
                    })
                    .catch(error => {
                        roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
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
                    $('#inputUsername').attr('placeholder',this.trans.get('keys.nhap_ma_chung_nhan'));
                }else{
                    $('#inputConfirmAddress').attr('disabled',true);
                    $('#inputUsername').attr('placeholder',this.trans.get('keys.nhap_tai_khoan_dang_nhap'));
                }

            },
            getRoles(){
                if(this.type == 'system') {
                    axios.post('/system/user/list_role')
                        .then(response => {
                            this.roles = response.data;
                        })
                        .catch(error => {
                            console.log(error.response.data);
                        });
                }
            },
            userData(){
                axios.post('/system/user/profile',)
                    .then(response => {
                        this.users = response.data;
                    })
                    .catch(error => {
                        //console.log(error);
                    })
                this.user_id = this.userid;
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
                let userJson = Ls.get('auth.user');
                let auth = {
                  fullname: this.users.fullname,
                  username: this.users.username,
                  avatar: this.users.avatar
                };

                /*if(!this.users.email){
                    $('.email_required').show();
                    return;
                }*/
                if(!this.users.fullname){
                    $('.fullname_required').show();
                    return;
                }
                this.formData = new FormData();
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('fullname', this.users.fullname);
                this.formData.append('dob', this.users.dob);
                this.formData.append('email', this.users.email);
                this.formData.append('phone', this.users.phone);
                this.formData.append('address', this.users.address);
                this.formData.append('user_id', this.user_id);
                this.formData.append('sex', this.users.sex);
                axios.post('/profile/update', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if(response.data.status){
                          toastr[response.data.status](response.data.message, this.trans.get('keys.thanh_cong'));
                          let fileUploaded = this.$refs.file.files[0];
                          this.clearFileInput();
                          if (fileUploaded && fileUploaded !== 'undefined') {
                            if (response.data.avatar) {
                              auth.avatar = response.data.avatar; //Cap nhat user avatar vao session
                            }
                          }
                          auth.fullname = this.users.fullname; //Cap nhat user full name vao session
                          Ls.set('auth.user', JSON.stringify(auth));
                          this.userData();
                          this.$parent.renderTopBarAgain();
                        }else{
                          toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                          $('.form-control').removeClass('notValidate');
                            $('#'+response.data.id).addClass('notValidate');
                        }
                    })
                    .catch(error => {
                      //console.log(error);
                      toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
                    });
            },
            clearFileInput() {
              $('.dropify-clear').click();
            }
        },
        mounted() {
            this.userData();
            this.getRoles();
            this.getCitys();
        }
    }
</script>

<style scoped>

</style>
