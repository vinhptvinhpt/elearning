<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.thong_tin_ca_nhan') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row mx-0">
      <div class="col-12 hk-sec-wrapper">
        <h5 class="hk-sec-title">{{trans.get('keys.thong_tin_nguoi_dung')}}</h5>
        <div class="row">
          <div class="col-12 col-lg-3">
            <div class="card">
              <div style="padding: 10px">
                <img :src="users.avatar ? users.avatar : '/images/user.png'" alt="">
              </div>
              <div class="card-body">
                <h6 class="mb-5" style="text-transform: uppercase"><strong>{{users.fullname}}</strong>
                </h6>
                <p>ID: <strong>{{users.username}}</strong></p>
<!--                <p v-if="users.student_role > 0">{{trans.get('keys.ma_so_nhan_vien_ban_hang')}}: <strong>{{users.code ? users.code : trans.get('keys.chua_cap_nhat') }}</strong></p>-->
<!--                <div v-if="users.confirm == 0 && users.student_role > 0">-->
<!--                  <hr>-->
<!--                  <p>{{trans.get('keys.thoi_gian_het_han')}}</p>-->
<!--                  <p><strong :class="users.diff_time_class">{{users.diff_time}}</strong></p>-->
<!--                </div>-->
                <template v-if="users.employee">
                  <p style="text-transform: capitalize;">Title/Position: <strong>{{users.employee.description ? users.employee.description : 'N/A'}} / {{users.employee.position}}</strong></p>
                  <p>Department: <strong>{{users.employee.organization.code}}({{users.employee.organization.name}})</strong></p>
                </template>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-9">
            <div class="table-wrap">
              <ul class="user_detail_wrap">
                <li>
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.ho_ten')}}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15">{{ users.fullname }}</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.ngay_thang_nam_sinh')}}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15">{{ (users.dob && users.dob != 0) ? users.dob :  trans.get('keys.chua_cap_nhat') }}</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.gioi_tinh')}}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15" v-if="users.sex === 1">{{ trans.get('keys.nam') }}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15" v-else-if="users.sex === 0">{{ trans.get('keys.nu') }}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15" v-else>{{ trans.get('keys.chua_cap_nhat') }}</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.dia_chi_thuong_tru')}}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15">{{ users.address ? users.address : trans.get('keys.chua_cap_nhat') }}</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.van_phong')}}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15">{{ users.city ? users.city : trans.get('keys.chua_cap_nhat') }}</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.quoc_gia')}}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15">{{ users.country ? countries[users.country] : trans.get('keys.chua_cap_nhat') }}</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.dia_chi_email')}}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15">{{ users.email }}</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.so_dien_thoai_lien_lac')}}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15">{{ users.phone ? users.phone : trans.get('keys.chua_cap_nhat') }}</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.so_chung_minh_nhan_dan_so_the_can_cuoc')}}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15">{{ users.cmtnd }}</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.tinh_trang_cong_tac')}}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15">{{ (users.working_status == 0) ? trans.get('keys.dang_cong_tac') : trans.get('keys.nghi_cong_tac') }}</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.ngay_bat_dau_lam_viec')}}</div>
                    <div class="col-sm-6 pl-40 pl-sm-15">{{ users.start_time ? users.start_time : trans.get('keys.chua_cap_nhat') }}</div>
                  </div>
<!--                  <div class="row" v-if="users.student_role > 0">-->
<!--                    <div class="col-sm-6">{{trans.get('keys.tinh_trang_cap_giay_chung_nhan')}}</div>-->
<!--                    <div class="col-sm-6 pl-40 pl-sm-15">{{ (users.confirm) ?  trans.get('keys.da_cap') :  trans.get('keys.chua_cap') }}</div>-->
<!--                  </div>-->
<!--                  <div class="row" v-if="users.student_role > 0">-->
<!--                    <div class="col-sm-6">{{trans.get('keys.noi_cap_giay_chung_nhan')}}</div>-->
<!--                    <div class="col-sm-6 pl-40 pl-sm-15">{{ (users.confirm_address) ? users.confirm_address_detail.name : trans.get('keys.chua_cap_nhat') }}</div>-->
<!--                  </div>-->
                  <div class="row">
                    <div class="col-sm-6">{{trans.get('keys.quyen_tai_khoan')}}</div>
                    <div class="col-sm-6 pl-30 pl-sm-15">
                      <p v-if="users.roles.length > 0" v-for="roleitem in users.roles" style="text-transform: capitalize;">
                        {{ roleitem.role.description }}
                      </p>
                      <p v-else>{{trans.get('keys.chua_cap_nhat')}}</p>
                    </div>
                  </div>
                </li>
              </ul>
              <div class="text-right">
                <router-link :to="{ name: 'ProfileEdit', params: { user_id: users.id } }" class="btn btn-primary btn-sm">{{trans.get('keys.sua')}}</router-link>
              </div>
              <!-- <div class="text-right" v-else>
                  <a title="Khôi phục tài khoản" href="#" class="btn btn-sm btn-success" @click="restoreUser(users.user_id)">
                      Khôi phục
                  </a>
              </div> -->
            </div>
          </div>
        </div>
      </div>

      <user-schedule :user_id="user_id"></user-schedule>

      <div class="col-12 hk-sec-wrapper">
        <user-course-grade :user_id="user_id" :training_name="training_name" :fullname="users.fullname" :username="users.username"></user-course-grade>
      </div>

    </div>
  </div>
</template>

<script>
    import UserSchedule from './ScheduleComponent'
    import UserCourseGrade from './UserCourseGradeComponent'
    import Const from '../../../services/const'

    export default {
        props: ['type'],
        components: {UserSchedule, UserCourseGrade},
        data() {
            return {
                users: {},
                training_name:'',
                user_id: 0,
                countries: {},
            }
        },
        methods:{
            userData(){
                axios.post('/system/user/profile')
                    .then(response => {
                        this.users = response.data;
                        if(response.data.training){
                            this.training_name = response.data.training.training_detail.name;
                        }
                    })
                    .catch(error => {
                        //console.log(error);
                    })
            },
            restoreUser(user_id){
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_khoi_phuc_lai_tai_khoan_nay'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user/restore',{user_id:user_id})
                        .then(response => {
                            if(response.data == 'success'){
                                swal({
                                    title: current_pos.trans.get('keys.thong_bao'),
                                    text: current_pos.trans.get('keys.khoi_phuc_thanh_cong'),
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }, function () {
                                    location.reload();
                                });
                            }else{
                                swal(current_pos.trans.get('keys.thong_bao'), current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), "error");
                            }

                        })
                        .catch(error => {
                            swal(current_pos.trans.get('keys.thong_bao'), current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), "error");
                        });
                });
                return false;
            },
            fetchCountries() {
              this.countries = Const.data().countries;
            },
        },
        mounted() {
          this.userData();
          this.fetchCountries();
        }
    }
</script>

<style scoped>

</style>
