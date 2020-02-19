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
              <li class="breadcrumb-item active">{{ trans.get('keys.thong_tin_nhan_vien') }}</li>
            </template>
            <li v-if="role === 'manager'" class="breadcrumb-item active">{{ trans.get('keys.thong_tin_chu_diem_ban') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row mx-0">
        <div class="col-12 hk-sec-wrapper">
            <h5 class="hk-sec-title">{{trans.get('keys.thong_tin_nhan_vien')}}</h5>
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
                                    <td>{{ (users.dob && users.dob !== 0) ? users.dob : trans.get('keys.chua_cap_nhat') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{trans.get('keys.gioi_tinh')}}</th>
                                    <td>{{ (users.sex === 1) ? trans.get('keys.nam') : trans.get('keys.nu') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{trans.get('keys.dia_chi_thuong_tru')}}</th>
                                    <td>{{ users.address ? users.address : trans.get('keys.chua_cap_nhat') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{trans.get('keys.dia_chi_email')}}</th>
                                    <td>{{ users.email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{trans.get('keys.so_dien_thoai_lien_lac')}}</th>
                                    <td>{{ users.phone ? users.phone : trans.get('keys.chua_cap_nhat') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{trans.get('keys.so_chung_minh_nhan_dan_so_the_can_cuoc')}}</th>
                                    <td>{{ users.cmtnd }}</td>
                                </tr>
                                <tr v-if="users.student_role > 0">
                                    <th scope="row">{{trans.get('keys.tinh_trang_cap_giay_chung_nhan')}}</th>
                                    <td>{{ (users.confirm) ? trans.get('keys.da_cap') : trans.get('keys.chua_cap') }}</td>
                                </tr>
                                <tr v-if="users.student_role > 0">
                                    <th scope="row">{{trans.get('keys.noi_cap_giay_chung_nhan')}}</th>
                                    <td>{{ (users.confirm_address) ? users.city.name : trans.get('keys.chua_cap_nhat')}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{trans.get('keys.tinh_trang_cong_tac')}}</th>
                                    <td>{{ (users.working_status === 0) ? trans.get('keys.dang_cong_tac') : trans.get('keys.nghi_cong_tac')}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{trans.get('keys.ngay_bat_dau_lam_viec')}}</th>
                                    <td>{{ users.start_time ? users.start_time : trans.get('keys.chua_cap_nhat')}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{trans.get('keys.noi_lam_viec')}}</th>
                                    <td>
                                        {{ branch_name.length > 0 ? branch_name : saleroom_name}}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right">
                            <template v-if="type === 'saleroom'">
                                <router-link v-if="role === 'employee'"
                                             :to="{ name: 'SaleroomUserIndexByRole', params: {saleroom_id: saleroom_id}, query: { type: owner_type } }"
                                             class="btn btn-secondary btn-sm"
                                             style="color: rgb(255, 255, 255);">{{trans.get('keys.quay_lai')}}</router-link>
                                <router-link v-if="role === 'manager'"
                                             :to="{ name: 'SaleroomIndexByRole', params: {branch_id: branchId}}"
                                             class="btn btn-secondary btn-sm"
                                             style="color: rgb(255, 255, 255);">{{trans.get('keys.quay_lai')}}</router-link>
                                <router-link
                                  :to="{ name: 'SaleroomUserEditByRole', params: {saleroom_id: saleroom_id, user_id: user_id}, query: {type: owner_type}}"
                                  class="btn btn-primary btn-sm">{{trans.get('keys.sua')}}</router-link>
                            </template>
                            <template v-if="type === 'branch'">
                                <router-link
                                  :to="{ name: 'BranchUserIndexByRole', query: {branch_id: branchId}}"
                                  class="btn btn-secondary btn-sm"
                                  style="color: rgb(255, 255, 255);">{{trans.get('keys.quay_lai')}}</router-link>
                                <router-link
                                  :to="{ name: 'BranchUserEditByRole', params: {branch_id: branchId, user_id: user_id}, query: {type: owner_type} }"
                                  class="btn btn-primary btn-sm">{{trans.get('keys.sua')}}
                                </router-link>
                            </template>
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
        components: {UserSchedule, UserCourseGrade},
        props: [
          'user_id',
          'saleroom_id',
          'type',
          'owner_type',
        ],
        data() {
            return {
                saleroom_name: '',
                branch_id: 0,
                branch_name: '',
                role: '',
                branchId: 0,
                users: {
                    saleroom:[]
                },
            }
        },
        methods:{
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
                            if(response.data === 'success'){
                                swal({
                                    title: "Thông báo!",
                                    text: "Khôi phục thành công!",
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }, function () {
                                    location.reload();
                                });
                            }else{
                                swal("Thông báo!", "Lỗi hệ thống. Thao tác thất bại!", "error");
                            }

                        })
                        .catch(error => {
                            swal("Thông báo!", "Lỗi hệ thống. Thao tác thất bại!", "error");
                        });
                });

                return false;
            },
            setBranchId() {
                if(this.owner_type === "master") {
                    this.branchId = this.branch_id
                }
            },
            fetch() {
              axios.post('/bridge/fetch', {
                user_id: this.user_id,
                saleroom_id: this.saleroom_id,
                view: 'SaleroomUserViewByRole'
              })
                .then(response => {
                  this.saleroom_name = response.data.saleroom_name;
                  this.role = response.data.role;
                  this.branch_id = response.data.branch_id;
                  this.branch_name = response.data.branch_name;
                  this.setBranchId();
                })
                .catch(error => {
                  console.log(error);
                })
            },
        },
        mounted() {
            this.userData();
            this.fetch();
        }
    }
</script>

<style scoped>

</style>
