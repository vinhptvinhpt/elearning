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
          <li class="breadcrumb-item">
                <router-link v-if="type === 'view_user_market'" to="/tms/system/view_user_market">
                  {{ trans.get('keys.quan_ly_nhan_vien') }}
                </router-link>
                <router-link v-else-if="type === 'sale_room_user'" :to="{ name: 'SaleRoomUserIndex' }">
                  {{ trans.get('keys.nhan_vien_diem_ban') }}
                </router-link>
                <router-link v-else-if="type === 'user_market'" :to="{ name: 'UserMarketIndex' }">
                  {{ trans.get('keys.chuyen_vien_kinh_doanh') }}
                </router-link>
                <router-link v-else-if="type === 'teacher'" :to="{ name: 'TeacherIndex' }">
                  {{ trans.get('keys.quan_tri_giang_vien') }}
                </router-link>
                <router-link v-else-if="type === 'student'" :to="{ name: 'StudentIndex' }">
                  {{ trans.get('keys.quan_tri_hoc_vien') }}
                </router-link>
                <router-link v-else :to="{ path: 'system/user', name: 'SystemUserList', query: { type: 'system' } }">
                  {{ trans.get('keys.quan_tri_nguoi_dung') }}
                </router-link>
          </li>
          <li class="breadcrumb-item" >
            <router-link v-if="type === 'teacher'" :to="{ name: 'EditUserById', params: { user_id: user_id }, query: { type: type } }">
              {{ trans.get('keys.thong_tin_giang_vien') }}
            </router-link>
            <router-link v-else-if="type === 'student'" :to="{ name: 'EditUserById', params: { user_id: user_id }, query: { type: type } }">
              {{ trans.get('keys.thong_tin_hoc_vien') }}
            </router-link>
            <router-link v-else :to="{ name: 'EditUserById', params: { user_id: user_id }, query: { type: type } }">
              {{ trans.get('keys.thong_tin_nguoi_dung') }}
            </router-link>
          </li>
          <li class="breadcrumb-item active">{{ trans.get('keys.sua_nguoi_dung') }}</li>
        </ol>
      </nav>
    </div>
  </div>
    <div>
        <div class="row mx-0">
            <div class="col-12 hk-sec-wrapper">
                <h5 class="hk-sec-title" v-if="type === 'system'">{{trans.get('keys.chinh_sua_thong_tin_nguoi_dung')}}</h5>
                <h5 class="hk-sec-title" v-else-if="type === 'teacher'">{{trans.get('keys.chinh_sua_thong_tin_giang_vien')}}</h5>
                <h5 class="hk-sec-title" v-else>{{trans.get('keys.chinh_sua_thong_tin_hoc_vien')}}</h5>
                <div class="row">
                    <div class="col-12 col-lg-3">
                        <div class="card">
                            <div style="padding: 10px;position: relative;" v-if="users.avatar">
                                <a class="remove_avatar" @click="removeAvatar()" :title="trans.get('keys.go_avatar')">
                                    <i class="fa fa-times"></i>
                                </a>
                                <img class="user_avatar" :src="users.avatar" alt="">
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
                                            <input autocomplete="false" v-model="password" type="password" id="inputPassword" :placeholder="trans.get('keys.mat_khau') + ' *'" class="form-control mb-4 " @input="validate_password">
                                            <i class="fa fa-check-circle-o label_validate" aria-hidden="true"></i>
                                        </span>
                                        <label class="text-danger message error passError" style="display: none;">{{trans.get('keys.vui_long_nhap_mat_khau_moi')}}</label>
                                        <label class="text-danger message error passStyleError" style="display: none;">{{trans.get('keys.mat_khau_sai_dinh_dang')}}</label>
                                        <span class="wrap_password pass_conf">
                                            <input autocomplete="false" v-model="passwordConf" type="password" id="inputPasswordConfirm" :placeholder="trans.get('keys.nhap_lai_mat_khau') + ' *'" class="form-control mb-4" @input="validate_password">
                                            <i class="fa fa-check-circle-o label_validate" aria-hidden="true"></i>
                                        </span>
                                        <label class="text-danger message error passConfError" style="display: none;">{{trans.get('keys.vui_long_nhap_mat_khau_moi')}}</label>
                                        <label class="text-danger message error passConfStyleError" style="display: none;">{{trans.get('keys.mat_khau_sai_dinh_dang')}}</label>
                                        <label style="display: block;">
                                            <input type="checkbox" @click="viewPassword()">{{trans.get('keys.hien_thi_mat_khau')}}
                                        </label>
                                        <button type="button" class="btn btn-primary btn-sm" @click="updatePassword()">{{trans.get('keys.cap_nhat_mat_khau')}}</button>
                                        <label class="text-danger message error passwordError" style="display: none;">{{trans.get('keys.mat_khau_khong_khop_nhau')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-9">
                        <form action="" class="form-row">
<!--                            <div class="col-sm-6 form-group" v-if="users.student_role > 0">-->
<!--                                <label for="inputConfirm">{{trans.get('keys.cap_giay_chung_nhan')}}</label>-->
<!--                                <select id="inputConfirm" class="form-control custom-select" v-model="users.confirm" @change="changeConfirm()">-->
<!--                                    <option value="0">{{trans.get('keys.chua_cap')}}</option>-->
<!--                                    <option value="1">{{trans.get('keys.da_cap')}}</option>-->
<!--                                </select>-->
<!--                            </div>-->

<!--                            <div class="col-sm-6 form-group" v-if="users.student_role > 0">-->
<!--                                <label for="inputConfirmAddress">{{trans.get('keys.noi_cap_giay_chung_nhan')}}</label>-->
<!--                                <select id="inputConfirmAddress" class="form-control custom-select" v-model="users.confirm_address" :disabled="(users.confirm == 0) ? true : false">-->
<!--                                    <option value="0">{{trans.get('keys.chua_cap_nhat')}}</option>-->
<!--                                    <option v-for="city in citys" :value="city.id">{{ city.name }}</option>-->
<!--                                </select>-->
<!--                            </div>-->

<!--                            <div class="col-sm-6 form-group" v-if="users.student_role > 0">-->
<!--                                <label for="certificateCode">{{trans.get('keys.ma_chung_nhan')}}</label>-->
<!--                                <input type="text" id="certificateCode" v-model="users.certificate.code" class="form-control mb-4" :disabled="(users.confirm == 0) ? true : false">-->
<!--                            </div>-->

<!--                            <div class="col-sm-6 form-group" v-if="users.student_role > 0">-->
<!--                                <label for="certificateDate">{{trans.get('keys.ngay_cap')}}</label>-->
<!--                                <input type="date" id="certificateDate" v-model="users.certificate.timecertificate" class="form-control mb-4" :disabled="(users.confirm == 0) ? true : false">-->
<!--                            </div>-->

                            <div class="col-md-4 col-sm-6 form-group">
                                <label for="inputUsername">{{trans.get('keys.ten_dang_nhap')}} *</label>
                                <input type="text" id="inputUsername" v-model="users.username" class="form-control mb-4" autocomplete="false" disabled @input="changeRequired('inputUsername')" readonly>
                                <label v-if="!users.username" class="required text-danger username_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}.</label>
                            </div>
                            <div class="col-md-4 col-sm-6 form-group">
                                <label for="inputCmtnd">{{trans.get('keys.cmnd')}} *</label>
                                <input type="text" id="inputCmtnd" v-model="users.cmtnd" class="form-control mb-4" @input="changeRequired('inputCmtnd')">
                                <label v-if="!users.cmtnd" class="required text-danger cmtnd_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                            </div>
                            <div class="col-md-4 col-sm-6 form-group">
                                <label for="inputFullname">{{trans.get('keys.ho_va_ten')}} *</label>
                                <input type="text" id="inputFullname" v-model="users.fullname" class="form-control mb-4" @input="changeRequired('inputFullname')">
                                <label v-if="!users.fullname" class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                            </div>
                            <div class="col-md-4 col-sm-6 form-group">
                                <label for="inputDob">{{trans.get('keys.ngay_sinh')}} </label>
                                <input type="date" id="inputDob" v-model="users.dob" class="form-control mb-4">
                            </div>
                            <div class="col-md-4 col-sm-6 form-group">
                                <label for="inputAddress">{{trans.get('keys.dia_chi')}}</label>
                                <input type="text" id="inputAddress" v-model="users.address" class="form-control mb-4">
                            </div>
                            <div class="col-md-4 col-sm-6 form-group">
                              <label for="inputCity">{{trans.get('keys.van_phong')}}</label>
                              <input v-model="users.city" type="text" id="inputCity" :placeholder="trans.get('keys.nhap_van_phong')" class="form-control mb-4">
<!--                              <label v-if="!users.city" class="required text-danger city_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                            </div>
                            <div class="col-md-4 col-sm-6 form-group">
                              <label for="inputCountry">{{trans.get('keys.quoc_gia')}} *</label>
                              <select id="inputCountry" class="form-control custom-select mb-4" v-model="users.country">
                                <option value="">{{trans.get('keys.chon_quoc_gia')}}</option>
                                <option v-for="(country_name, country_code, index) in countries" :value="country_code">{{ country_name }}</option>
                              </select>
                              <label v-if="!users.country" class="required text-danger country_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                            </div>


<!--                            <div class="col-md-4 col-sm-6 form-group">-->
<!--                                <label for="inputEmail">{{trans.get('keys.email')}} *</label>-->
<!--                                <input type="text" id="inputEmail" v-model="users.email" class="form-control mb-4" @input="changeRequired('inputEmail')">-->
<!--                                <label v-if="!users.email" class="required text-danger email_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
<!--                            </div>-->

                            <div class="col-md-4 col-sm-6 form-group">
                                <label for="inputPhone">{{trans.get('keys.so_dt')}}</label>
                                <input type="text" id="inputPhone" v-model="users.phone" class="form-control mb-4" @input="acceptNumber">
                            </div>

                            <div class="col-md-4 col-sm-6 form-group">
                                <label for="inputSex">{{trans.get('keys.gioi_tinh')}}</label>
                                <select id="inputSex" class="form-control custom-select" v-model="users.sex">
                                    <option value="1">{{trans.get('keys.nam')}}</option>
                                    <option value="0">{{trans.get('keys.nu')}}</option>
                                </select>
                            </div>

<!--                            <div class="col-md-4 col-sm-6 form-group" v-if="role_type !== 'market'">-->
<!--                                <label for="inputCode">{{trans.get('keys.ma_nhan_vien')}}</label>-->
<!--                                <input v-model="users.code" type="text" id="inputCode" placeholder="" class="form-control mb-4">-->
<!--                            </div>-->

                            <div v-if="roles && type === 'system'" class="col-12 form-group">
                                <label for="inputRole">{{trans.get('keys.quyen')}} *</label>
                                <select v-model="users.role" class="form-control selectpicker" id="inputRole" multiple >
                                    <option v-for="role in roles" :value="role.id">{{ trans.has('keys.' + role.name) ? trans.get('keys.' + role.name) : role.name.charAt(0).toUpperCase() + role.name.slice(1) }}</option>
                                </select>
                                <label v-if="!users.role" class="text-danger user_role_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                            </div>

                            <div class="col-md-4 col-sm-6 form-group">
                                <label for="inputTimeStart">{{trans.get('keys.ngay_bat_dau_lam')}}</label>
                                <input v-model="users.start_time" type="date" id="inputTimeStart" class="form-control mb-4">
                            </div>

                            <div class="col-md-4 col-sm-6 form-group" v-if="role_type !== 'market'">
                                <label for="inputWorkingStatus">{{trans.get('keys.tinh_trang_lam_viec')}}</label>
                                <select id="inputWorkingStatus" class="form-control custom-select" v-model="users.working_status">
                                    <option value="0">{{trans.get('keys.dang_cong_tac')}}</option>
                                    <option value="1">{{trans.get('keys.nghi_cong_tac')}}</option>
                                </select>
                            </div>

                            <div v-if="roles && type === 'system'" class="col-md-4 col-sm-6 form-group">
                              <label for="employee_organization_id">{{trans.get('keys.noi_lam_viec')}} *</label>
                              <treeselect v-model="users.employee.organization_id" :multiple="false" :options="options" id="employee_organization_id"/>
                              <label v-if="!users.employee.organization_id" class="text-danger organization_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>

                            </div>

<!--                            <div class="col-md-4 col-sm-6 form-group">-->
<!--                                <label for="inputTraining">{{trans.get('keys.vi_tri')}} *</label>-->
<!--                                <select id="inputTraining" class="form-control custom-select" v-model="users.training.trainning_id">-->
<!--                                    <option value="0">{{trans.get('keys.chon_vi_tri')}}</option>-->
<!--                                    <option v-for="(item,index) in training_list" :value="item.id">{{item.name}}</option>-->
<!--                                </select>-->
<!--                                <label v-if="users.training.trainning_id === 0" class="required text-danger training_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
<!--                            </div>-->

<!--                            <div class="col-12" v-if="role_type !== 'market'">-->
<!--                                <div class="working_address">-->
<!--                                    <div class="form-group">-->
<!--                                        <label>{{trans.get('keys.noi_lam_viec')}}</label>-->
<!--                                        <select class="form-control custom-select" v-model="option_work" @change="changeOption()">-->
<!--                                            <option value="">{{trans.get('keys.chon_noi_lam_viec')}}</option>-->
<!--                                            <option value="age">{{trans.get('keys.them_dai_ly')}}</option>-->
<!--                                            <option value="pos">{{trans.get('keys.them_diem_ban')}}</option>-->
<!--                                        </select>-->
<!--                                        <hr>-->
<!--                                        <div class="col-12" v-if="option_work === 'age'">-->
<!--                                            <div class="row">-->
<!--                                                <div class="form-group" style="width: 100%;">-->
<!--                                                    <div class="input-group">-->
<!--                                                        <div class="wrap_search_box multi">-->
<!--                                                            <div class="btn_search_box search_branch" @click="get_branch()">-->
<!--                                                                <span>{{trans.get('keys.chon_dai_ly')}}</span>-->
<!--                                                            </div>-->
<!--                                                            <div class="content_search_box">-->
<!--                                                                <input @input="get_branch()" type="text" v-model="branch_input"-->
<!--                                                                       class="form-control search_box">-->
<!--                                                                <i class="fa fa-spinner" aria-hidden="true"></i>-->
<!--                                                                <ul>-->
<!--                                                                    <li @click="selectBranch(0)">{{trans.get('keys.chon_dai_ly')}}</li>-->
<!--                                                                    <li @click="selectBranch(item.id)"-->
<!--                                                                        v-for="item in branchs" :data-value="item.id">-->
<!--                                                                        {{item.name}} ( {{ item.code }} )-->
<!--                                                                    </li>-->
<!--                                                                </ul>-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->

<!--                                        <div class="col-12" v-if="option_work === 'pos'">-->
<!--                                            <div class="row">-->
<!--                                                <div class="form-group" style="width: 100%;">-->
<!--                                                    <div class="input-group">-->
<!--                                                        <div class="wrap_search_box multi">-->
<!--                                                            <div class="btn_search_box search_sale_room" @click="get_saleroom()">-->
<!--                                                                <span>{{trans.get('keys.chon_diem_ban')}}</span>-->
<!--                                                            </div>-->
<!--                                                            <div class="content_search_box">-->
<!--                                                                <input @input="get_saleroom()" type="text" v-model="saleroom_input"-->
<!--                                                                       class="form-control search_box">-->
<!--                                                                <i class="fa fa-spinner" aria-hidden="true"></i>-->
<!--                                                                <ul>-->
<!--                                                                    <li @click="selectSaleRoom(0)">{{trans.get('keys.chon_diem_ban')}}</li>-->
<!--                                                                    <li @click="selectSaleRoom(item.id)"-->
<!--                                                                        v-for="item in salerooms" :data-value="item.id">-->
<!--                                                                        {{item.name}} ( {{ item.code }} )-->
<!--                                                                    </li>-->
<!--                                                                </ul>-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->

<!--                                        <div class="col-12">-->
<!--                                            <div class="row">-->
<!--                                                <div v-if="branch_list.length > 0" style="width: 100%;">-->
<!--                                                    <label style="font-weight:500;">{{trans.get('keys.dai_ly_da_chon')}}</label>-->
<!--                                                    <ul class="select_val">-->
<!--                                                        <li v-for="item in branch_list">-->
<!--                                                            {{item.name}}-->
<!--                                                            <a @click="removeBranch(item.id)" :title="trans.get('keys.bo_chon_dai_ly')"><i class="fal fa-remove"></i></a>-->
<!--                                                        </li>-->
<!--                                                    </ul>-->
<!--                                                </div>-->
<!--                                                <div v-if="saleroom_list.length > 0" style="width: 100%;">-->
<!--                                                    <label style="font-weight:500;">{{trans.get('keys.diem_ban_da_chon')}}</label>-->
<!--                                                    <ul class="select_val">-->
<!--                                                        <li v-for="item in saleroom_list">-->
<!--                                                            {{item.name}}-->
<!--                                                            <a @click="removeSaleRoom(item.id)" :title="trans.get('keys.bo_chon_diem_ban')"><i class="fal fa-remove"></i></a>-->
<!--                                                        </li>-->
<!--                                                    </ul>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->

                        </form>
                        <div class="button-list" v-if="users.deleted === 0">
                            <button type="button" class="btn btn-primary btn-sm" @click="updateUser()">{{trans.get('keys.cap_nhat_thong_tin')}}</button>

                            <router-link v-if="type === 'view_user_market'"
                                         to="/tms/system/view_user_market"
                                         class="btn btn-secondary btn-sm">
                              {{trans.get('keys.quay_lai')}}
                            </router-link>

                            <router-link v-else-if="type === 'sale_room_user'"
                                         to="/tms/sale_room_user"
                                         class="btn btn-secondary btn-sm">
                              {{trans.get('keys.quay_lai')}}
                            </router-link>

                            <router-link v-else-if="type === 'user_market'"
                                         to="/tms/system/user_market"
                                         class="btn btn-secondary btn-sm">
                              {{trans.get('keys.quay_lai')}}
                            </router-link>

                            <router-link v-else-if="type === 'teacher'"
                                         to="/tms/education/user_teacher"
                                         class="btn btn-secondary btn-sm">
                              {{trans.get('keys.quay_lai')}}
                            </router-link>

                            <router-link v-else-if="type === 'student'"
                                         to="/tms/education/user_student"
                                         class="btn btn-secondary btn-sm">
                              {{trans.get('keys.quay_lai')}}
                            </router-link>

                            <router-link v-else
                                         to="/tms/system/user"
                                         class="btn btn-secondary btn-sm">
                              {{trans.get('keys.quay_lai')}}
                            </router-link>

                            <button style="float:right;" @click.prevent="deletePost('/system/user/delete/'+users.user_id)" class="btn btn-sm btn-danger">
                                {{trans.get('keys.xoa')}}
                            </button>
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
        props: ['user_id','type'],
        data() {
            return {
                users: {
                    role:[],
                    confirm_address:0,
                    start_time:'',
                    certificate: {
                        code: '',
                        timecertificate: '',
                    },
                    password:'',
                    saleroom:0,
                    branch:0,
                    training: {
                        trainning_id: 0,
                    },
                    employee: {
                      organization_id: 0,
                    },
                    city: '',
                    country: ''
                },
                roles:[],
                citys:[],
                countries: [],
                passwordConf:'',
                password:'',
                branch_input:'',
                saleroom_input:'',
                branch_active:'',
                option_work:'',
                branchs:[],
                branch_list:[],
                salerooms:[],
                saleroom_list:[],
                branch_select:[],
                saleroom_select:[],
                training_list:[],
                training:0,
                role_type: '',
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
                ]
            }
        },
        methods:{
            acceptNumber() {
                let has_plus = false;
                if (this.users.phone.indexOf('+') === 0) {
                  has_plus = true;
                }
                let x = this.users.phone.replace(/\D/g,'');
                if (has_plus === true) {
                  this.users.phone = '+' + x;
                }
            },
            changeOption(){
                if(this.option_work === 'pos'){
                    $('.btn_search_box span').html(this.trans.get('keys.chon_diem_ban'));
                }
                if(this.option_work === 'age'){
                    $('.btn_search_box span').html(this.trans.get('keys.chon_dai_ly'));
                }
            },
            selectBranch(id) {
                this.branch_select.push(id);
                $('.search_branch span').html(this.trans.get('keys.chon_dai_ly'));
                axios.post('/system/user/get_list_branch_select', {
                    branch_select:this.branch_select
                })
                    .then(response => {
                        this.branch_list = response.data;
                    })
                    .catch(error => {
                        this.branch_list = [];
                    })
            },
            removeBranch(id){
                for( var i = 0; i < this.branch_select.length; i++){
                    if ( this.branch_select[i] === id) {
                        this.branch_select.splice(i, 1);
                    }
                }
                axios.post('/system/user/get_list_branch_select', {
                    branch_select:this.branch_select
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
                    branch_select:this.branch_select
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
                    saleroom_select:this.saleroom_select
                })
                    .then(response => {
                        this.saleroom_list = response.data;
                    })
                    .catch(error => {
                        this.saleroom_list = [];
                    })
            },
            removeSaleRoom(id){
                for( var i = 0; i < this.saleroom_select.length; i++){
                    if ( this.saleroom_select[i] === id) {
                        this.saleroom_select.splice(i, 1);
                    }
                }
                axios.post('/system/user/get_list_saleroom_select', {
                    saleroom_select:this.saleroom_select
                })
                    .then(response => {
                        this.saleroom_list = response.data;
                    })
                    .catch(error => {
                        this.saleroom_list = [];
                    })
            },
            removeAvatar(){
                var user_id = this.user_id;
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_muon_go_avatar'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user/remove_avatar',{
                        user_id:user_id
                    })
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                            if(response.data.status === 'success'){
                                $('.user_avatar').attr('src','');
                            }
                        })
                        .catch(error => {
                            roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
            },
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
            deletePost(url) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_xoa_muc_da_chon'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post(url)
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                            location.reload();
                        })
                        .catch(error => {
                            roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });

                return false;
            },
            updatePassword(){

                $('.message.error').hide();
                if(this.password.length === 0){
                    $('.passError').show();
                    return;
                }
                if( this.passwordConf.length === 0 ){
                    $('.passConfError').show();
                    return;
                }
                if( this.password !== this.passwordConf){
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
                        if(response.data === 'success'){
                            roam_message('success',current_pos.trans.get('keys.cap_nhat_mat_khau_thanh_cong'));
                            $('.showChangePass').slideUp();
                            $('.btnShowChangePass').removeClass('active');
                        }else if(response.data === 'passwordFail'){
                            $('.passwordError').show();
                            roam_message('error',current_pos.trans.get('keys.mat_khau_khong_khop_nhau'));
                        }else if(response.data === 'passFail'){
                            $('.passStyleError').show();
                            roam_message('error',current_pos.trans.get('keys.mat_khau_chua_dap_ung_mat_khau_can_bao_gom_chu_in_hoa_so_va_ky_tu_dac_biet'));
                        }else if(response.data === 'passConfFail'){
                            $('.passConfStyleError').show();
                            roam_message('error',current_pos.trans.get('keys.mat_khau_chua_dap_ung_mat_khau_can_bao_gom_chu_in_hoa_so_va_ky_tu_dac_biet'));
                        }else if(response.data === 'passwordExist'){
                            $('.wrap_password').removeClass('success');
                            $('.wrap_password').addClass('warning');
                            roam_message('error',current_pos.trans.get('keys.mat_khau_moi_trung_mat_khau_dang_su_dung'));
                        }else if(response.data === 'validateFail'){
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
                        if(response.data.password === 'password_success'){
                            $('span.wrap_password.pass').removeClass('warning');
                            $('span.wrap_password.pass').addClass('success');
                        }else if(response.data.password === 'password_warning'){
                            $('span.wrap_password.pass').removeClass('success');
                            $('span.wrap_password.pass').addClass('warning');
                        }

                        if(response.data.passwordConf === 'passwordConf_success'){
                            $('span.wrap_password.pass_conf').removeClass('warning');
                            $('span.wrap_password.pass_conf').addClass('success');
                        }else if(response.data.passwordConf === 'passwordConf_warning'){
                            $('span.wrap_password.pass_conf').removeClass('success');
                            $('span.wrap_password.pass_conf').addClass('warning');
                        }
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            changeConfirm(){
                if(this.confirm === 1){
                    $('#inputConfirmAddress').attr('disabled',false);
                    $('#inputUsername').attr('placeholder',this.trans.get('keys.nhap_ma_chung_nhan'));
                }else{
                    $('#inputConfirmAddress').attr('disabled',true);
                    $('#inputUsername').attr('placeholder',this.trans.get('keys.nhap_tai_khoan_dang_nhap'));
                }

            },
            getRoles() {
                if(this.type === 'system') {
                    axios.post('/system/user/list_role')
                        .then(response => {
                            this.roles = response.data;
                            this.$nextTick(function(){
                                $('.selectpicker').selectpicker('refresh');
                            });
                        })
                        .catch(error => {
                            console.log(error.response.data);
                        });
                }
            },
            userData(){
                axios.post('/system/user/detail',{
                    user_id:this.user_id
                })
                    .then(response => {
                        this.users = response.data;

                        if (!response.data.training) {
                            this.users.training = {
                                trainning_id: 0,
                            }
                        }
                        if (!response.data.certificate) {
                            this.users.certificate = {
                                code: '',
                                timecertificate: '',
                            }
                        }

                        if (!this.users.employee) {
                          this.users.employee = {
                            organization_id: 0
                          };
                        }

                        this.selectOrganization(this.users.employee.organization_id);

                        if (response.data.salerooms.length > 0) {
                            var workplaces = response.data.salerooms;
                            var branch_a = [];
                            var saleroom_a = [];
                            for (var i=0;i < workplaces.length;i++) {
                                if(workplaces[i].type === 'agents'){
                                    branch_a = {
                                        'id' : workplaces[i].branch_id,
                                        'name' : workplaces[i].branch_name
                                    };
                                    this.branch_list.push(branch_a);
                                    this.branch_select.push(workplaces[i].branch_id);
                                }
                                if(workplaces[i].type === 'pos'){
                                    saleroom_a = {
                                        'id' : workplaces[i].id,
                                        'name' : workplaces[i].name
                                    };
                                    this.saleroom_list.push(saleroom_a);
                                    this.saleroom_select.push(workplaces[i].id);
                                }
                            }
                        }
                        this.$nextTick(function(){
                            $('.selectpicker').selectpicker('refresh');
                        });
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

                // if(!this.users.city){
                //     $('.city_required').show();
                //     return;
                // }
                // if(this.users.training.trainning_id === 0){
                //     $('.training_required').show();
                //     return;
                // }

                if (!this.users.role) {
                  $('.user_role_required').show();
                  return;
                }

                if(!this.users.country) {
                  $('.country_required').show();
                  return;
                }

                if (!this.users.employee.organization_id) {
                  $('.organization_required').show();
                  return;
                }

                let organization_roles_selected = [];
                for (const [key, item] of Object.entries(this.roles)) {
                  if (this.users.role.indexOf(item.id) !== -1) {
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
                //   if (!this.users.employee.organization_id) {
                //     toastr['error'](this.trans.get('keys.ban_phai_chon_noi_lam_viec_neu_da_chon_quyen_trong_nhom'), this.trans.get('keys.that_bai'));
                //     $('.organization_required').show();
                //     return;
                //   }
                // }
                if (this.users.employee.organization_id) {
                  if (organization_roles_selected.length === 0) {
                    toastr['error'](this.trans.get('keys.ban_phai_chon_quyen_trong_nhom_neu_muon_chon_noi_lam_viec'), this.trans.get('keys.that_bai'));
                    return;
                  }
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
                this.formData.append('city', this.users.city);
                this.formData.append('country', this.users.country);
                this.formData.append('role', this.users.role);
                //this.formData.append('type', this.type);
                this.formData.append('user_id', this.user_id);
                this.formData.append('sex', this.users.sex);
                this.formData.append('code', this.users.code);
                this.formData.append('start_time', this.users.start_time);
                this.formData.append('working_status', this.users.working_status);
                this.formData.append('confirm_address', this.users.confirm_address);
                this.formData.append('confirm', this.users.confirm);
                this.formData.append('certificate_code', this.users.certificate.code);
                this.formData.append('certificate_date', this.users.certificate.timecertificate);
                this.formData.append('branch', this.branch);
                this.formData.append('saleroom', this.saleroom);
                this.formData.append('branch_select', this.branch_select);
                this.formData.append('saleroom_select', this.saleroom_select);
                this.formData.append('trainning_id', this.users.training.trainning_id);
                this.formData.append('organization_id', this.users.employee.organization_id);

                let current_pos = this;

                axios.post('/system/user/update', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if(response.data.status){
                            roam_message(response.data.status,response.data.message);
                            this.goBack();
                        }else{
                            roam_message('error',response.data.message);
                            $('.form-control').removeClass('notValidate');
                            $('#'+response.data.id).addClass('notValidate');
                        }
                    })
                    .catch(error => {
                        roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    });
            },
            goBack(){
                switch (this.type) {
                    case "teacher":
                        location.href = "/tms/education/user_teacher";
                        break;
                    case "student":
                        location.href = "/tms/education/user_student";
                        break;
                    default:
                        location.href = "/tms/system/user";
                        break;
                }
            },
            restoreUser(user_id){
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_khoi_phuc_lai_tai_khoan_nay'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user/restore',{user_id:user_id})
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                            location.reload();
                        })
                        .catch(error => {
                            roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });

                return false;
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
            fetch() {
              axios.post('/bridge/fetch', {
                user_id: this.user_id,
                view: 'EditDetailUserById'
              })
              .then(response => {
                this.role_type = response.data.role_type;
              })
              .catch(error => {
                console.log(error);
              })
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
              axios.post('/organization/list',{
                keyword: this.organization_keyword,
                level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
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
            getCountries() {
              if(this.type === 'system') {
                axios.post('/system/user/list_country')
                  .then(response => {
                    this.countries = response.data;
                  })
                  .catch(error => {
                    console.log(error.response.data);
                  });
              }
            },
        },
        mounted() {
            this.fetch();
            //this.getTrainingProgram();
            this.getRoles();
            //this.getCitys();
            this.userData();
            this.getCountries();
        }
    }
</script>

<style scoped>

</style>
