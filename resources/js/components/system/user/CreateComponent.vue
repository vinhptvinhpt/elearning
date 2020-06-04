<template>
    <div class="row form_create_user">
        <div class="col-12 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <p>
                        <input type="file" ref="file" name="file" class="dropify"/>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-9">
            <form action="" class="form-row hk-sec-wrapper">

                <!--                <div class="col-sm-6 form-group" v-if="type === 'student'">-->
                <!--                    <label for="inputConfirm">{{trans.get('keys.cap_giay_chung_nhan')}}</label>-->
                <!--                    <select id="inputConfirm" class="form-control custom-select" v-model="confirm" >-->
                <!--                        <option value="0">{{trans.get('keys.chua_cap')}}</option>-->
                <!--                        <option value="1">{{trans.get('keys.da_cap')}}</option>-->
                <!--                    </select>-->
                <!--                </div>-->

                <!--                <div class="col-sm-6 form-group" v-if="type === 'student'">-->
                <!--                    <label for="inputConfirmAddress">{{trans.get('keys.noi_cap_giay_chung_nhan')}}</label>-->
                <!--                    <select id="inputConfirmAddress" class="form-control custom-select" v-model="confirm_address" :disabled="confirm == 1 ? false : true">-->
                <!--                        <option value="0">{{trans.get('keys.chua_cap_nhat')}}</option>-->
                <!--                        <option v-for="city in citys" :value="city.id">{{ city.name }}</option>-->
                <!--                    </select>-->
                <!--                </div>-->

                <!--                <div class="col-sm-6 form-group" v-if="type === 'student'">-->
                <!--                    <label for="inputCertificateCode">{{trans.get('keys.ma_chung_nhan')}}</label>-->
                <!--                    <input v-model="certificate_code" id="inputCertificateCode" class="form-control mb-4" :disabled="confirm == 1 ? false : true">-->
                <!--                </div>-->

                <!--                <div class="col-sm-6 form-group" v-if="type === 'student'">-->
                <!--                    <label for="inputCertificateDate">{{trans.get('keys.ngay_cap_ma_chung_nhan')}}</label>-->
                <!--                    <input v-model="certificate_date" type="date" id="inputCertificateDate" class="form-control mb-4" :disabled="confirm == 1 ? false : true">-->
                <!--                </div>-->

                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputUsername">{{trans.get('keys.ten_dang_nhap')}}(Email) *</label>
                    <input autocomplete="false" v-model="username" type="text" id="inputUsername"
                           :placeholder="trans.get('keys.nhap_id_dung_de_dang_nhap')" class="form-control mb-4"
                           @input="changeRequired('inputUsername')">
                    <label v-if="!username" class="required text-danger username_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputPassword">{{trans.get('keys.mat_khau')}} *</label>
                    <span class="wrap_password pass">
                        <input autocomplete="false" v-model="password" type="password" id="inputPassword" placeholder=""
                               class="form-control mb-4" @input="validate_password">
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
                        <input autocomplete="false" v-model="passwordConf" type="password" id="inputPasswordConfirm"
                               placeholder="" class="form-control mb-4" @input="validate_password">
                        <i class="fa fa-check-circle-o label_validate" aria-hidden="true"></i>
                    </span>
                    <label v-if="!passwordConf" class="required text-danger passwordConf_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}.</label>
                </div>

                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputFullname">{{trans.get('keys.ho_va_ten')}} *</label>
                    <input v-model="fullname" type="text" id="inputFullname"
                           :placeholder="trans.get('keys.nhap_ho_ten_day_du')" class="form-control mb-4"
                           @input="changeRequired('inputFullname')">
                    <label v-if="!fullname" class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputDob">{{trans.get('keys.ngay_thang_nam_sinh')}}</label>
                    <input v-model="dob" type="date" id="inputDob" class="form-control mb-4">
                </div>
                <div class="col-md-4 col-sm-6 form-group hide">
                    <label for="inputEmail">{{trans.get('keys.email')}} *</label>
                    <input v-model="email" type="text" id="inputEmail" :placeholder="trans.get('keys.dia_chi_email')"
                           class="form-control mb-4" @input="changeRequired('inputEmail')">
                    <label v-if="!email" class="required text-danger email_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputCmtnd">{{trans.get('keys.so_cmtnd')}} *</label>
                    <input v-model="cmtnd" type="text" id="inputCmtnd" :placeholder="trans.get('keys.nhap_so_cmtnd')"
                           class="form-control mb-4" @input="changeRequired('inputCmtnd')">
                    <label v-if="!cmtnd" class="required text-danger cmtnd_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputPhone">{{trans.get('keys.so_dien_thoai_lien_lac')}}</label>
                    <input v-model="phone" type="text" id="inputPhone" :placeholder="trans.get('keys.nhap_so_dt')"
                           class="form-control mb-4" @input="acceptNumber">
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputAddress">{{trans.get('keys.dia_chi')}}</label>
                    <input v-model="address" type="text" id="inputAddress" :placeholder="trans.get('keys.nhap_dia_chi')"
                           class="form-control mb-4">
                </div>

                <div class="col-md-4 col-sm-6  form-group">
                    <label for="inputSex">{{trans.get('keys.gioi_tinh')}}</label>
                    <select id="inputSex" class="form-control custom-select" v-model="sex">
                        <option value="1">{{trans.get('keys.nam')}}</option>
                        <option value="0">{{trans.get('keys.nu')}}</option>
                    </select>
                </div>

                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputCity">{{trans.get('keys.van_phong')}} *</label>
                    <input v-model="city" type="text" id="inputCity" :placeholder="trans.get('keys.nhap_van_phong')"
                           class="form-control mb-4">
                    <label v-if="!city" class="required text-danger city_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputCountry">{{trans.get('keys.quoc_gia')}} *</label>
                    <select id="inputCountry" class="form-control custom-select mb-4" v-model="country">
                        <option value="">{{trans.get('keys.chon_quoc_gia')}}</option>
                        <option v-for="(country_name, country_code, index) in countries" :value="country_code">{{
                            country_name }}
                        </option>
                    </select>
                    <label v-if="!country" class="required text-danger country_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div v-if="roles && type === 'system'" class="col-md-4 col-sm-6 form-group">
                    <label>{{trans.get('keys.quyen')}} *</label>
                    <v-select
                            multiple
                            :options="roleSelectOptions"
                            :reduce="roleSelectOption => roleSelectOption.id"
                            :placeholder="this.trans.get('keys.chon_vai_tro')"
                            :filter-by="myFilterBy"
                            :required="inputRole"
                            v-model="inputRole">
                    </v-select>
                    <label v-if="inputRole.length==0" class="text-danger inputRole_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    <!--                  <label for="inputRole">{{trans.get('keys.quyen')}}</label>-->
                    <!--                    <select v-model="inputRole" class="form-control selectpicker" id="inputRole" autocomplete="false" multiple>-->
                    <!--                        <option value="">{{trans.get('keys.chon_vai_tro')}}</option>-->
                    <!--                        <option v-for="role in roles" :value="role.id">{{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}</option>-->
                    <!--                    </select>-->
                    <!--                    <label v-if="!inputRole" class="text-danger user_role_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->

                </div>


                <!--                <div class="col-md-4 col-sm-6 form-group">-->
                <!--                    <label for="inputCode">{{trans.get('keys.ma_nhan_vien')}}</label>-->
                <!--                    <input v-model="code" type="text" id="inputCode" placeholder="" class="form-control mb-4">-->
                <!--                </div>-->

                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputTimeStart">{{trans.get('keys.ngay_bat_dau_lam')}}</label>
                    <input v-model="start_time" type="date" id="inputTimeStart" class="form-control mb-4">
                </div>

                <div class="col-md-4 col-sm-6 form-group">
                    <label for="inputWorkingStatus">{{trans.get('keys.tinh_trang_lam_viec')}}</label>
                    <select id="inputWorkingStatus" class="form-control custom-select" v-model="working_status">
                        <option value="0">{{trans.get('keys.dang_cong_tac')}}</option>
                        <option value="1">{{trans.get('keys.nghi_cong_tac')}}</option>
                    </select>
                </div>

                <div class="col-md-4 col-sm-6 form-group">
                    <label for="employee_organization_id">{{trans.get('keys.noi_lam_viec')}} *</label>
                    <treeselect v-model="input_organization_id" :multiple="false" :options="options"
                                id="employee_organization_id" :disabled="input_organization_id !== 0"/>
                    <label v-if="!input_organization_id" class="text-danger organization_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>

                <!--Training-->
                <!--                <div class="col-sm-6 form-group">-->
                <!--                    <label for="inputTraining">{{trans.get('keys.vi_tri')}} *</label>-->
                <!--                    <select id="inputTraining" class="form-control custom-select" v-model="training">-->
                <!--                        <option value="0">{{trans.get('keys.chon_vi_tri')}}</option>-->
                <!--                        <option v-for="(item,index) in training_list" :value="item.id">{{item.name}}</option>-->
                <!--                    </select>-->
                <!--                    <label v-if="training == 0" class="required text-danger training_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                <!--                </div>-->

                <!--Working at-->
                <!--                <div class="col-12">-->
                <!--                    <div class="working_address">-->
                <!--                        <div class="form-group">-->
                <!--                            <label>{{trans.get('keys.noi_lam_viec')}}</label>-->
                <!--                            <select class="form-control custom-select" v-model="option_work" @change="changeOption()">-->
                <!--                                <option value="">{{trans.get('keys.chon_noi_lam_viec')}}</option>-->
                <!--                                <option value="age">{{trans.get('keys.them_dai_ly')}}</option>-->
                <!--                                <option value="pos">{{trans.get('keys.them_diem_ban')}}</option>-->
                <!--                            </select>-->
                <!--                            <label v-if="branch_select.length == 0 && saleroom_select.length == 0" class="required text-danger work_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                <!--                            <hr>-->
                <!--                            <div class="col-12" v-if="option_work == 'age'">-->
                <!--                                <div class="row">-->
                <!--                                    <div class="form-group" style="width: 100%;">-->
                <!--                                        <div class="input-group">-->
                <!--                                            <div class="wrap_search_box multi">-->
                <!--                                                <div class="btn_search_box search_branch" @click="get_branch()">-->
                <!--                                                    <span>{{trans.get('keys.chon_dai_ly')}}</span>-->
                <!--                                                </div>-->
                <!--                                                <div class="content_search_box">-->
                <!--                                                    <input @input="get_branch()" type="text" v-model="branch_input"-->
                <!--                                                           class="form-control search_box">-->
                <!--                                                    <i class="fa fa-spinner" aria-hidden="true"></i>-->
                <!--                                                    <ul>-->
                <!--                                                        <li @click="selectBranch(0)">{{trans.get('keys.chon_dai_ly')}}</li>-->
                <!--                                                        <li @click="selectBranch(item.id)"-->
                <!--                                                            v-for="item in branchs" :data-value="item.id">-->
                <!--                                                            {{item.name}} ( {{ item.code }} )-->
                <!--                                                        </li>-->
                <!--                                                    </ul>-->
                <!--                                                </div>-->
                <!--                                            </div>-->
                <!--                                        </div>-->
                <!--                                    </div>-->
                <!--                                </div>-->
                <!--                            </div>-->

                <!--                            <div class="col-12" v-if="option_work == 'pos'">-->
                <!--                                <div class="row">-->
                <!--                                    <div class="form-group" style="width: 100%;">-->
                <!--                                        <div class="input-group">-->
                <!--                                            <div class="wrap_search_box multi">-->
                <!--                                                <div class="btn_search_box search_sale_room" @click="get_saleroom()">-->
                <!--                                                    <span>{{trans.get('keys.chon_diem_ban')}}</span>-->
                <!--                                                </div>-->
                <!--                                                <div class="content_search_box">-->
                <!--                                                    <input @input="get_saleroom()" type="text" v-model="saleroom_input"-->
                <!--                                                           class="form-control search_box">-->
                <!--                                                    <i class="fa fa-spinner" aria-hidden="true"></i>-->
                <!--                                                    <ul>-->
                <!--                                                        <li @click="selectSaleRoom(0)">{{trans.get('keys.chon_diem_ban')}}</li>-->
                <!--                                                        <li @click="selectSaleRoom(item.id)"-->
                <!--                                                            v-for="item in salerooms" :data-value="item.id">-->
                <!--                                                            {{item.name}} ( {{ item.code }} )-->
                <!--                                                        </li>-->
                <!--                                                    </ul>-->
                <!--                                                </div>-->
                <!--                                            </div>-->
                <!--                                        </div>-->
                <!--                                    </div>-->
                <!--                                </div>-->
                <!--                            </div>-->

                <!--                            <div class="col-12">-->
                <!--                                <div class="row">-->
                <!--                                    <div v-if="branch_list.length > 0" style="width: 100%;">-->
                <!--                                        <label style="font-weight:500;">{{trans.get('keys.dai_ly_da_chon')}}</label>-->
                <!--                                        <ul class="select_val">-->
                <!--                                            <li v-for="item in branch_list">-->
                <!--                                                {{item.name}}-->
                <!--                                                <a @click="removeBranch(item.id)" :title="trans.get('keys.bo_chon_dai_ly')"><i class="fal fa-remove"></i></a>-->
                <!--                                            </li>-->
                <!--                                        </ul>-->
                <!--                                    </div>-->
                <!--                                    <div v-if="saleroom_list.length > 0" style="width: 100%;">-->
                <!--                                        <label style="font-weight:500;">{{trans.get('keys.diem_ban_da_chon')}}</label>-->
                <!--                                        <ul class="select_val">-->
                <!--                                            <li v-for="item in saleroom_list">-->
                <!--                                                {{item.name}}-->
                <!--                                                <a @click="removeSaleRoom(item.id)" :title="trans.get('keys.bo_chon_diem_ban')"><i class="fal fa-remove"></i></a>-->
                <!--                                            </li>-->
                <!--                                        </ul>-->
                <!--                                    </div>-->
                <!--                                </div>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->

                <!--Submit-->
                <div class="col-12">
                    <div class="button-list text-right mt-2">
                        <button type="button" class="btn btn-secondary btn-sm collapsed closeForm"
                                data-toggle="collapse" href="#collapse_1" aria-expanded="true">{{trans.get('keys.huy')}}
                        </button>
                        <button @click="createUser()" type="button" class="btn btn-primary btn-sm">
                            {{trans.get('keys.tao_moi')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>

    export default {
        props: {
            type: {
                type: String,
                default: 'system'
            },
            role_login: String,
            current_roles: Object,
            organization_id: {
                type: Number,
                default: 0
            }
        },
        data() {
            return {
                fullname: '',
                dob: '',
                email: '',
                username: '',
                password: '',
                passwordConf: '',
                phone: '',
                cmtnd: '',
                address: '',
                city: '',
                country: '',
                inputRole: [],
                roles: [],
                sex: 1,
                code: '',
                start_time: '',
                working_status: 0,
                confirm: 0,
                confirm_address: 0,
                certificate_code: '',
                certificate_date: '',
                //citys:[],
                countries: [],
                branch: 0,
                branch_input: '',
                saleroom: 0,
                saleroom_input: '',
                branch_active: '',
                training: 0,
                training_list: [],
                option_work: '',
                branchs: [],
                branch_list: [],
                salerooms: [],
                saleroom_list: [],
                branch_select: [],
                saleroom_select: [],
                //Treeselect options
                options: [
                    {
                        id: 0,
                        label: this.trans.get('keys.chon_to_chuc')
                    }
                ],
                organization_roles: [
                    'manager',
                    'employee',
                    'leader'
                ],
                role_selected: 'user',
                roleSelectOptions: [],
                last_organization_id: 0
            }
        },
        methods: {
            acceptNumber() {
                let has_plus = false;
                if (this.phone.indexOf('+') === 0) {
                    has_plus = true;
                }
                let x = this.phone.replace(/\D/g, '');
                if (has_plus === true) {
                    this.phone = '+' + x;
                }
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
            changeOption() {
                if (this.option_work == 'pos') {
                    $('.btn_search_box span').html(this.trans.get('keys.chon_diem_ban'));
                }
                if (this.option_work == 'age') {
                    $('.btn_search_box span').html(this.trans.get('keys.chon_dai_ly'));
                }
            },
            getTrainingProgram() {
                axios.post('/system/user/get_training_list')
                    .then(response => {
                        this.training_list = response.data;
                    })
                    .catch(error => {
                        this.training_list = [];
                    })
            },
            selectBranch(id) {
                this.branch_select.push(id);
                $('.search_branch span').html(this.trans.get('keys.chon_dai_ly'));
                axios.post('/system/user/get_list_branch_select', {
                    branch_select: this.branch_select
                })
                    .then(response => {
                        this.branch_list = response.data;
                    })
                    .catch(error => {
                        this.branch_list = [];
                    })
            },
            removeBranch(id) {
                for (var i = 0; i < this.branch_select.length; i++) {
                    if (this.branch_select[i] === id) {
                        this.branch_select.splice(i, 1);
                    }
                }
                axios.post('/system/user/get_list_branch_select', {
                    branch_select: this.branch_select
                })
                    .then(response => {
                        this.branch_list = response.data;
                    })
                    .catch(error => {
                        this.branch_list = [];
                    })
            },
            get_branch() {
                $('.content_search_box').addClass('loadding');
                axios.post('/system/user/get_list_branch', {
                    keyword: this.branch_input,
                    branch_select: this.branch_select
                })
                    .then(response => {
                        this.branchs = response.data;
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                        this.branchs = [];
                    })
            },
            get_saleroom() {
                $('.content_search_box').addClass('loadding');
                axios.post('/system/user/get_list_saleroom', {
                    keyword: this.saleroom_input,
                    saleroom_select: this.saleroom_select,
                })
                    .then(response => {
                        this.salerooms = response.data;
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                        this.salerooms = [];
                    })
            },
            selectSaleRoom(id) {
                this.saleroom_select.push(id);
                $('.search_sale_room span').html(this.trans.get('keys.chon_diem_ban'));
                axios.post('/system/user/get_list_saleroom_select', {
                    saleroom_select: this.saleroom_select
                })
                    .then(response => {
                        this.saleroom_list = response.data;
                    })
                    .catch(error => {
                        this.saleroom_list = [];
                    })
            },
            removeSaleRoom(id) {
                for (var i = 0; i < this.saleroom_select.length; i++) {
                    if (this.saleroom_select[i] === id) {
                        this.saleroom_select.splice(i, 1);
                    }
                }
                axios.post('/system/user/get_list_saleroom_select', {
                    saleroom_select: this.saleroom_select
                })
                    .then(response => {
                        this.saleroom_list = response.data;
                    })
                    .catch(error => {
                        this.saleroom_list = [];
                    })
            },
            changeRequired(element) {
                $('#' + element).removeClass('notValidate');
            },
            viewPassword() {
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
            validate_password() {
                axios.post('/validate_password', {
                    password: this.password,
                    passwordConf: this.passwordConf,
                })
                    .then(response => {
                        if (response.data.password == 'password_success') {
                            $('span.wrap_password.pass').removeClass('warning');
                            $('span.wrap_password.pass').addClass('success');
                        } else if (response.data.password == 'password_warning') {
                            $('span.wrap_password.pass').removeClass('success');
                            $('span.wrap_password.pass').addClass('warning');
                        }

                        if (response.data.passwordConf == 'passwordConf_success') {
                            $('span.wrap_password.pass_conf').removeClass('warning');
                            $('span.wrap_password.pass_conf').addClass('success');
                        } else if (response.data.passwordConf == 'passwordConf_warning') {
                            $('span.wrap_password.pass_conf').removeClass('success');
                            $('span.wrap_password.pass_conf').addClass('warning');
                        }
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            changeConfirm() {
                if (this.confirm == 1) {
                    $('#inputConfirmAddress').attr('disabled', false);
                    $('#inputUsername').attr('placeholder', this.trans.get('keys.nhap_ma_chung_nhan'));
                } else {
                    $('#inputConfirmAddress').attr('disabled', true);
                    $('#inputUsername').attr('placeholder', this.trans.get('keys.nhap_tai_khoan_dang_nhap'));
                }

            },
            getRoles() {
                if (this.type === 'system') {
                    axios.post('/system/user/list_role')
                        .then(response => {
                            this.roles = response.data;
                            this.getRoleFromCurrentRoles(this.current_roles);
                            this.setRoles(this.roles);
                            this.$nextTick(function () {
                                $('.selectpicker').selectpicker('refresh');
                            });
                        })
                        .catch(error => {
                            console.log(error.response.data);
                        });
                }
            },
            getCountries() {
                if (this.type === 'system') {
                    axios.post('/system/user/list_country')
                        .then(response => {
                            this.countries = response.data;
                        })
                        .catch(error => {
                            console.log(error.response.data);
                        });
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
            createUser() {
                if (!this.username) {
                    $('.username_required').show();
                    return;
                }
                if (!this.password) {
                    $('.password_required').show();
                    return;
                }
                if (!this.passwordConf) {
                    $('.passwordConf_required').show();
                    return;
                }
                if (!this.fullname) {
                    $('.fullname_required').show();
                    return;
                }

                if (!this.city) {
                    $('.city_required').show();
                    return;
                }

                if (!this.country) {
                    $('.country_required').show();
                    return;
                }

                if (this.inputRole.length === 0) {
                    $('.inputRole_required').show();
                    return;
                }

                if (!this.last_organization_id) {
                    $('.organization_required').show();
                    return;
                }

                // if(!this.email){
                //     $('.email_required').show();
                //     return;
                // }
                if (!this.cmtnd) {
                    $('.cmtnd_required').show();
                    return;
                }
                // if(this.training == 0) {
                //     $('.training_required').show();
                //     return;
                // }
                if (this.role_login == 'user_market') {
                    if (this.branch_select.length == 0 && this.saleroom_select.length == 0) {
                        $('.work_required').show();
                        return;
                    }
                }


                let organization_roles_selected = [];
                for (const [key, item] of Object.entries(this.roles)) {
                    if (this.inputRole.indexOf(item.id) !== -1) {
                        if (this.organization_roles.indexOf(item.name) !== -1) {
                            organization_roles_selected.push(item.name);
                        }
                    }
                }
                if (organization_roles_selected.length > 1) {
                    toastr['error'](this.trans.get('keys.ban_chi_duoc_chon_1_quyen_trong_nhom'), this.trans.get('keys.that_bai'));
                    return;
                }
                // if (organization_roles_selected.length > 0) {
                //     if (!this.organization_id) {
                //         toastr['error'](this.trans.get('keys.ban_phai_chon_noi_lam_viec_neu_da_chon_quyen_trong_nhom'), this.trans.get('keys.that_bai'));
                //         $('.organization_required').show();
                //         return;
                //     }
                // }
                // if (this.last_organization_id) {
                //     if (organization_roles_selected.length === 0) {
                //         toastr['error'](this.trans.get('keys.ban_phai_chon_quyen_trong_nhom_neu_muon_chon_noi_lam_viec'), this.trans.get('keys.that_bai'));
                //         return;
                //     }
                // }

                this.formData = new FormData();
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('fullname', this.fullname);
                this.formData.append('dob', this.dob);
                // this.formData.append('email', this.email);
                this.formData.append('email', this.username);
                this.formData.append('username', this.username);
                this.formData.append('password', this.password);
                this.formData.append('passwordConf', this.passwordConf);
                this.formData.append('phone', this.phone);
                this.formData.append('cmtnd', this.cmtnd);
                this.formData.append('address', this.address);
                this.formData.append('city', this.city);
                this.formData.append('country', this.country);
                this.formData.append('inputRole', this.inputRole);
                this.formData.append('type', this.type);
                this.formData.append('sex', this.sex);
                this.formData.append('code', this.code);
                this.formData.append('start_time', this.start_time);
                this.formData.append('working_status', this.working_status);
                this.formData.append('confirm', this.confirm);
                this.formData.append('confirm_address', this.confirm_address);
                this.formData.append('certificate_code', this.certificate_code);
                this.formData.append('certificate_date', this.certificate_date);
                this.formData.append('branch', this.branch);
                this.formData.append('saleroom', this.saleroom);
                this.formData.append('branch_select', this.branch_select);
                this.formData.append('saleroom_select', this.saleroom_select);
                this.formData.append('training_id', this.training);
                this.formData.append('organization_id', this.last_organization_id);

                let current_pos = this;

                axios.post('/system/user/create', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if (response.data.status && response.data.status == 'success') {
                            roam_message(response.data.status, response.data.message);

                            this.clearFileInput();
                            this.fullname = '';
                            this.dob = '';
                            this.email = '';
                            this.username = '';
                            this.password = '';
                            this.passwordConf = '';
                            this.phone = '';
                            this.cmtnd = '';
                            this.address = '';
                            this.city = '';
                            this.country = '';
                            this.inputRole = [5];
                            this.sex = 1;
                            this.code = '';
                            this.start_time = '';
                            this.working_status = 0;
                            this.confirm = 0;
                            this.confirm_address = 0;
                            this.certificate_code = '';
                            this.certificate_date = '';
                            this.branch = 0;
                            this.saleroom = 0;
                            $('span.wrap_password').removeClass('success');
                            $('.closeForm').trigger('click');
                            this.$nextTick(function () {
                                $('.selectpicker').selectpicker('refresh');
                            });
                            $('.search_sale_room span').html(this.trans.get('keys.chon_diem_ban'));
                            $('.search_branch span').html(this.trans.get('keys.chon_dai_ly'));

                            this.$parent.assignBatch += 1;
                        } else {
                            if (response.data.status) {
                                roam_message(response.data.status, response.data.message);
                            } else {
                                $('.form-control').removeClass('notValidate');
                                $('#' + response.data.id).addClass('notValidate');
                                roam_message('error', response.data.message);
                            }
                        }
                    })
                    .catch(error => {
                        roam_message('error', current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    });
            },
            clearFileInput() {
                $('.dropify-clear').click();
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
            selectOrganization(current_id) {
                $('.content_search_box').addClass('loadding');
                axios.post('/organization/list', {
                    keyword: this.organization_keyword,
                    level: 1, // lấy cấp lớn nhất only, vì đã đệ quy
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
            setRoles(list) {
                let outPut = [];
                for (const [key, item] of Object.entries(list)) {
                    let newOption = {
                        id: item.id,
                        label: this.trans.has('keys.' + item.name) ? this.trans.get('keys.' + item.name) : item.name.charAt(0).toUpperCase() + item.name.slice(1),
                        //label: item.name.charAt(0).toUpperCase() + item.name.slice(1),
                        //name: item.name
                    };
                    if (this.role_selected === 'root' || this.role_selected === 'admin') {
                        outPut.push(newOption);
                    }
                    if (this.role_selected === 'leader' || this.role_selected === 'manager') {
                        if (item.name === 'employee') {
                            outPut.push(newOption);
                        }
                    }
                    if (this.role_selected === 'manager') {
                        if (item.name === 'leader') {
                            outPut.push(newOption);
                        }
                    }
                }
                this.roleSelectOptions = outPut;
                //this.roles = outPut;
            },
            getRoleFromCurrentRoles(current_roles) {
                if (current_roles.root_user === true) {
                    this.role_selected = 'root';
                } else if (current_roles.has_role_admin === true) {
                    this.role_selected = 'admin';
                } else if (current_roles.has_role_manager === true) {
                    this.role_selected = 'manager';
                } else if (current_roles.has_role_leader === true) {
                    this.role_selected = 'leader';
                } else if (current_roles.has_user_market === true) {
                    this.role_selected = 'user_market';
                } else {
                    this.role_selected = 'user';
                }
            }
        },
        mounted() {
            //this.getCitys();
            //this.get_branch();
            //this.getTrainingProgram();
            this.selectOrganization(this.organization_id);
            this.getRoles();
            this.getCountries();
        },
        computed: {
            input_organization_id: {
                get() {
                    this.last_organization_id = this.organization_id;
                    return this.organization_id;
                },
                set(value) {
                    this.last_organization_id = value;
                }
            }
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
