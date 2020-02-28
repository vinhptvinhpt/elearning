<template>
    <div class="row form_create_user">
        <div class="col-12 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <p>
                        <input  type="file" ref="file" name="file" class="dropify" />
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-9">
            <form action="" class="form-row hk-sec-wrapper">
                <div class="col-sm-6 form-group">
                    <label for="inputConfirm">{{trans.get('keys.cap_giay_chung_nhan')}}</label>
                    <select id="inputConfirm" class="form-control custom-select" v-model="confirm">
                        <option value="0">{{trans.get('keys.chua_cap')}}</option>
                        <option value="1">{{trans.get('keys.da_cap')}}</option>
                    </select>
                </div>

                <div class="col-sm-6 form-group">
                    <label for="inputConfirmAddress">{{trans.get('keys.noi_cap_giay_chung_nhan')}}</label>
                    <select id="inputConfirmAddress" class="form-control custom-select" v-model="confirm_address" :disabled="confirm == 1 ? false : true">
                        <option value="0">{{trans.get('keys.chua_cap_nhat')}}</option>
                        <option v-for="city in citys" :value="city.id">{{ city.name }}</option>
                    </select>
                </div>

                <div class="col-sm-6 form-group">
                    <label for="inputCertificateCode">{{trans.get('keys.ma_chung_nhan')}}</label>
                    <input v-model="certificate_code" id="inputCertificateCode" class="form-control mb-4" :disabled="confirm == 1 ? false : true">
                </div>

                <div class="col-sm-6 form-group">
                    <label for="inputCertificateDate">{{trans.get('keys.ngay_cap_ma_chung_nhan')}}</label>
                    <input v-model="certificate_date" type="date" id="inputCertificateDate" class="form-control mb-4" :disabled="confirm == 1 ? false : true">
                </div>

                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputUsername">{{trans.get('keys.ten_dang_nhap')}} *</label>
                    <input autocomplete="false" v-model="username" type="text" id="inputUsername" :placeholder="trans.get('keys.nhap_id_dung_de_dang_nhap')" class="form-control mb-4" @input="changeRequired('inputUsername')" readonly>
                    <label v-if="!username" class="required text-danger username_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputPassword">{{trans.get('keys.mat_khau')}} *</label>
                    <span class="wrap_password pass">
                        <input autocomplete="false" v-model="password" type="password" id="inputPassword" placeholder="" class="form-control mb-4" @input="validate_password" readonly>
                        <i class="fa fa-check-circle-o label_validate" aria-hidden="true"></i>
                    </span>
                    <label v-if="!password" class="required text-danger password_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    <label>
                        <input type="checkbox" @click="viewPassword()">{{trans.get('keys.hien_thi_mat_khau')}}
                    </label>
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputPasswordConfirm">{{trans.get('keys.nhap_lai_mat_khau')}} *</label>
                    <span class="wrap_password pass_conf">
                        <input autocomplete="false" v-model="passwordConf" type="password" id="inputPasswordConfirm" placeholder="" class="form-control mb-4" @input="validate_password">
                        <i class="fa fa-check-circle-o label_validate" aria-hidden="true"></i>
                    </span>
                    <label v-if="!passwordConf" class="required text-danger passwordConf_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>

                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputFullname">{{trans.get('keys.ho_va_ten')}} *</label>
                    <input v-model="fullname" type="text" id="inputFullname" :placeholder="trans.get('keys.nhap_ho_ten_day_du')" class="form-control mb-4" @input="changeRequired('inputFullname')">
                    <label v-if="!fullname" class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputDob">{{trans.get('keys.ngay_thang_nam_sinh')}}</label>
                    <input v-model="dob" type="date" id="inputDob" class="form-control mb-4">
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputEmail">{{trans.get('keys.email')}}</label>
                    <input v-model="email" type="text" id="inputEmail" :placeholder="trans.get('keys.dia_chi_email')" class="form-control mb-4" @input="changeRequired('inputEmail')">
                    <label v-if="!email" class="required text-danger email_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputCmtnd">{{trans.get('keys.so_cmtnd')}} *</label>
                    <input v-model="cmtnd" type="text" id="inputCmtnd" :placeholder="trans.get('keys.nhap_so_cmtnd')" class="form-control mb-4" @input="changeRequired('inputCmtnd')">
                    <label v-if="!cmtnd" class="required text-danger cmtnd_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputPhone">{{trans.get('keys.so_dien_thoai_lien_lac')}}</label>
                    <input v-model="phone" type="text" id="inputPhone" :placeholder="trans.get('keys.nhap_so_dt')" class="form-control mb-4">
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputAddress">{{trans.get('keys.dia_chi_thuong_tru')}}</label>
                    <input v-model="address" type="text" id="inputAddress" :placeholder="trans.get('keys.dia_chi')" class="form-control mb-4">
                </div>

                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputSex">{{trans.get('keys.gioi_tinh')}}</label>
                    <select id="inputSex" class="form-control custom-select" v-model="sex">
                        <option value="1">{{trans.get('keys.nam')}}</option>
                        <option value="0">{{trans.get('keys.nu')}}</option>
                    </select>
                </div>

                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputCode">{{trans.get('keys.ma_nhan_vien')}}</label>
                    <input v-model="code" type="text" id="inputCode" placeholder="" class="form-control mb-4">
                </div>

                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputTraining">{{trans.get('keys.vi_tri')}} *</label>
                    <select id="inputTraining" class="form-control custom-select" v-model="training">
                        <option value="0">{{trans.get('keys.chon_vi_tri')}}</option>
                        <option v-for="(item,index) in training_list" :value="item.id">{{item.name}}</option>
                    </select>
                    <label v-if="training == 0" class="required text-danger training_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>

                <div class="col-sm-6 form-group">
                    <label for="inputTimeStart">{{trans.get('keys.ngay_bat_dau_lam')}}</label>
                    <input v-model="start_time" type="date" id="inputTimeStart"  class="form-control mb-4">
                </div>

                <div class="col-sm-6 form-group">
                    <label for="inputWorkingStatus">{{trans.get('keys.tinh_trang_lam_viec')}}</label>
                    <select id="inputWorkingStatus" class="form-control custom-select" v-model="working_status">
                        <option value="0">{{trans.get('keys.dang_cong_tac')}}</option>
                        <option value="1">{{trans.get('keys.nghi_cong_tac')}}</option>
                    </select>
                </div>

                <div class="col-12">
                    <div class="button-list text-right">
                        <button type="button" class="btn btn-secondary btn-sm collapsed closeForm" @click="removeForm('formCreateNew')">{{trans.get('keys.huy')}}</button>
                        <button @click="createUser()" type="button" class="btn btn-primary btn-sm">{{trans.get('keys.tao')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['sale_room_id','type'],
        data() {
            return {
                fullname: '',
                dob: '',
                email: '',
                username: '',
                password:'',
                passwordConf:'',
                phone:'',
                cmtnd:'',
                address:'',
                certificate_code: '',
                certificate_date: '',
                sex: 1,
                code: '',
                start_time: '',
                working_status: 0,
                confirm:0,
                confirm_address:0,
                citys:[],
                training:0,
                training_list:[],
            }
        },
        methods: {
            removeForm(id_wrap){
                this.fullname = '';
                this.dob = '';
                this.email = '';
                this.username = '';
                this.password = '';
                this.passwordConf = '';
                this.phone = '';
                this.cmtnd = '';
                this.address = '';
                this.sex = 1;
                this.code = '';
                this.start_time = '';
                this.working_status = 0;
                this.confirm = 0;
                this.confirm_address = 0;
                this.certificate_code = '';
                this.certificate_date = '';
                $('span.wrap_password').removeClass('success');
                $('#'+id_wrap).trigger('click');
            },
            changeRequired(element){
                console.log(element);
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
            getCitys() {
                axios.post('/system/list/list_city')
                    .then(response => {
                        this.citys = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            createUser(){
                if(!this.username) {
                    $('.username_required').show();
                    return;
                }
                if(!this.password){
                    $('.password_required').show();
                    return;
                }
                if(!this.passwordConf){
                    $('.passwordConf_required').show();
                    return;
                }
                if(!this.fullname) {
                    $('.fullname_required').show();
                    return;
                }
                /*if(!this.email){
                    $('.email_required').show();
                    return;
                }*/
                if(!this.cmtnd){
                    $('.cmtnd_required').show();
                    return;
                }
                if(this.training == 0) {
                    $('.training_required').show();
                    return;
                }
                this.formData = new FormData();
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('fullname', this.fullname);
                this.formData.append('dob', this.dob);
                this.formData.append('email', this.email);
                this.formData.append('username', this.username);
                this.formData.append('password', this.password);
                this.formData.append('passwordConf', this.passwordConf);
                this.formData.append('phone', this.phone);
                this.formData.append('cmtnd', this.cmtnd);
                this.formData.append('address', this.address);
                this.formData.append('sale_room_id', this.sale_room_id);
                this.formData.append('sex', this.sex);
                this.formData.append('code', this.code);
                this.formData.append('start_time', this.start_time);
                this.formData.append('working_status', this.working_status);
                this.formData.append('confirm', this.confirm);
                this.formData.append('confirm_address', this.confirm_address);
                this.formData.append('certificate_code', this.certificate_code);
                this.formData.append('certificate_date', this.certificate_date);
                this.formData.append('type', this.type);
                this.formData.append('training_id', this.training);
                axios.post('/system/user/create_in_saleroom', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if(response.data.status && response.data.status == 'success'){
                            roam_message(response.data.status,response.data.message);
                            $('#btnFilter').trigger('click');
                            $('.closeForm').trigger('click');
                            this.$nextTick(function(){
                                $('.selectpicker').selectpicker('refresh');
                            });
                        }else{
                            if(response.data.status){
                                roam_message(response.data.status,response.data.message);
                            }else{
                                $('.form-control').removeClass('notValidate');
                                $('#'+response.data.id).addClass('notValidate');
                                roam_message('error',response.data.message);
                            }
                        }
                    })
                    .catch(error => {
                        roam_message('error','Lỗi hệ thống. Thao tác thất bại');
                    });
            },
            getTrainingProgram(){
                axios.post('/system/user/get_training_list')
                    .then(response => {
                        this.training_list = response.data;
                    })
                    .catch(error => {
                        this.training_list = [];
                    })
            },
            setFileInput() {
              $('.dropify').dropify();
            }
        },
        mounted() {
            this.getTrainingProgram();
            this.getCitys();
            this.setFileInput();
        }
    }
</script>

<style scoped>

</style>
