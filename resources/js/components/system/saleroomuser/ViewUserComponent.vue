<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item">
              <router-link v-if="name_section === 'uncertificate'" :to="{ name: 'StudentUncertificate'}">
                {{ trans.get('keys.danh_sach_hoc_vien_chua_cap_chung_chi') }}
              </router-link>
              <router-link v-else :to="{ name: 'SaleRoomUserIndex'}">
                {{ trans.get('keys.diem_ban_hang') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.thong_tin_nguoi_dung') }}</li>
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
                <p>{{trans.get('keys.ma_so_nhan_vien_ban_hang')}}: <strong>{{users.code ? users.code : trans.get('keys.chua_cap_nhat')}}</strong></p>
                <div v-if="users.confirm == 0 && type == 'student'">
                  <hr>
                  <p>{{trans.get('keys.thoi_gian_het_han')}}</p>
                  <p><strong :class="users.diff_time_class">{{users.diff_time}}</strong></p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-9">
            <div class="table-wrap">
              <div class="">
                <table class="dev-table table-sm table-hover display mb-0">
                  <tbody>
                  <tr>
                    <th scope="row">{{trans.get('keys.ngay_thang_nam_sinh')}}</th>
                    <td>{{ (users.dob && users.dob != 0) ? users.dob : trans.get('keys.chua_cap_nhat') }}</td>
                  </tr>
                  <tr>
                    <th scope="row">{{trans.get('keys.gioi_tinh')}}</th>
                    <td>{{ (users.sex == 1) ? trans.get('keys.nam')  : trans.get('keys.nu')  }}</td>
                  </tr>
                  <tr>
                    <th scope="row">{{trans.get('keys.dia_chi_thuong_tru')}}</th>
                    <td>{{ users.address ? users.address : trans.get('keys.chua_cap_nhat')  }}</td>
                  </tr>
                  <tr>
                    <th scope="row">{{trans.get('keys.dia_chi_email')}}</th>
                    <td>{{ users.email }}</td>
                  </tr>
                  <tr>
                    <th scope="row">{{trans.get('keys.so_dien_thoai_lien_lac')}}</th>
                    <td>{{ users.phone ? users.phone : trans.get('keys.chua_cap_nhat')  }}</td>
                  </tr>
                  <tr>
                    <th scope="row">{{trans.get('keys.so_chung_minh_nhan_dan_so_the_can_cuoc')}}</th>
                    <td>{{ users.cmtnd }}</td>
                  </tr>
                  <tr v-if="users.student_role > 0">
                    <th scope="row">{{trans.get('keys.tinh_trang_cap_giay_chung_nhan')}}</th>
                    <td>{{ (users.confirm) ? trans.get('keys.da_cap')  : trans.get('keys.chua_cap')  }}</td>
                  </tr>
                  <tr v-if="users.student_role > 0">
                    <th scope="row">{{trans.get('keys.noi_cap_giay_chung_nhan')}}</th>
                    <td>{{ (users.confirm_address) ? users.city ? users.city.name : trans.get('keys.da_cap_nhat') : trans.get('keys.chua_cap_nhat') }}</td>
                  </tr>
                  <tr>
                    <th scope="row">{{trans.get('keys.tinh_trang_cong_tac')}}</th>
                    <td>{{ (users.working_status == 0) ? trans.get('keys.dang_cong_tac')  : trans.get('keys.nghi_cong_tac') }}</td>
                  </tr>
                  <tr>
                    <th scope="row">{{trans.get('keys.ngay_bat_dau_lam_viec')}}</th>
                    <td>{{ users.start_time ? users.start_time : trans.get('keys.chua_cap_nhat') }}</td>
                  </tr>
<!--                  <tr>-->
<!--                    <th scope="row">{{trans.get('keys.noi_lam_viec')}}</th>-->
<!--                    <td>-->
<!--                      &lt;!&ndash;<p v-if="users.salerooms" v-for="saleroom in users.salerooms">-->
<!--                          {{ saleroom.saleroom.name }}-->
<!--                      </p>-->
<!--                      <span v-else>{{trans.get('keys.chua_cap_nhat')}}</span>&ndash;&gt;-->
<!--                      <div v-if="users.salerooms" v-for="saleroom in users.salerooms" class="clearfix">-->
<!--                        <router-link v-if="saleroom.type == 'pos'" style="float:left;"-->
<!--                                     :to="{name: 'SaleroomIndex', query: {code: saleroom.code}}">-->
<!--                          {{ saleroom.name }} ( {{ trans.get('keys.diem_ban') }} )-->
<!--                        </router-link>-->
<!--                        <router-link v-else style="float:left;"-->
<!--                                     :to="{ name: 'BranchIndex', query: {code: saleroom.branch_code}}">-->
<!--                          {{ saleroom.branch_name }} ( {{ trans.get('keys.dai_ly') }} )-->
<!--                        </router-link>-->
<!--                      </div>-->
<!--                      <div v-else>-->
<!--                        <p>{{trans.get('keys.chua_cap_nhat')}}</p>-->
<!--                      </div>-->
<!--                    </td>-->
<!--                  </tr>-->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <user-schedule :user_id="user_id"></user-schedule>

      <div class="col-12 hk-sec-wrapper" >
        <user-course-grade :user_id="user_id"></user-course-grade>
      </div>

    </div>
  </div>
</template>

<script>
  import UserSchedule from '../user/ScheduleComponent'
  import UserCourseGrade from '../user/UserCourseGradeComponent'

    export default {
        props: ['user_id', 'type', 'name_section'],
        components: {UserSchedule, UserCourseGrade},
        data() {
            return {
                users: {
                    saleroom: []
                },
            }
        },
        methods: {
            userData() {
                axios.post('/system/user/detail', {
                    user_id: this.user_id
                })
                    .then(response => {
                        this.users = response.data;
                    })
                    .catch(error => {

                    })
            },
            restoreUser(user_id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_khoi_phuc_lai_tai_khoan_nay'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "error",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user/restore', {user_id: user_id})
                        .then(response => {
                            if (response.data == 'success') {
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
                            } else {
                                swal(current_pos.trans.get('keys.thong_bao'), current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), "error");
                            }

                        })
                        .catch(error => {
                            swal(current_pos.trans.get('keys.thong_bao'), current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), "error");
                        });
                });

                return false;
            },
        },
        mounted() {
            this.userData();
        }
    }
</script>

<style scoped>

</style>
