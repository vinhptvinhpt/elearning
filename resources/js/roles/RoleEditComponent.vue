<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/role">{{ trans.get('keys.quyen_he_thong') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.chi_tiet') }}</li>
          </ol>
        </nav>
      </div>
    </div>


    <div class="row">
      <div class="col-12">
        <div>
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-sm-8">
                  <h4 class="mb-3">{{trans.get('keys.sua_quyen')}}</h4>
                </div>
                <div v-if="!roles.role_organization" class="col-sm-4 text-right">
                  <router-link :to="{name: 'RoleUserIndex', params: {role_id: role_id}}" class="btn btn-default btn-sm mb-3">{{trans.get('keys.gan_nguoi_dung')}}</router-link>
                  <!--<a :href="'/role/edit/organize/'+role_id" class="btn btn-default btn-sm mb-3">Quản lý nhân lực</a>-->
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <div class="input-group">
                      <input v-if="roles.status === 0 && !roles.role_organization" type="text" class="form-control form-control-line" :placeholder="trans.get('keys.ten_quyen')+' *'" v-model="roles.name">
                      <input v-else type="text" class="form-control form-control-line" :placeholder="trans.get('keys.ten_quyen') + ' *'" v-model="roles.name" disabled>
                    </div>
                    <label v-if="!roles.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <div class="input-group">
                      <textarea :placeholder="trans.get('keys.mo_ta') +' *'" class="form-control form-control-line" v-model="roles.description"></textarea>
                    </div>
                    <label v-if="!roles.description" class="text-danger description_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                  </div>
                </div>
              </div>
              <hr v-if="!roles.role_organization">
              <h4 v-if="!roles.role_organization" class="mb-10">{{trans.get('keys.chuc_nang')}}</h4>
              <div v-if="!roles.role_organization" class="row">
                <div class="col-12">
                  <div class="permission_panel pb-3 ">
                    <div class="permission_panel_item" v-for="(permissions,index1) in permission_slug">
                      <span class="permission_title" v-html="permission_name[index1]"></span>
                      <div class="panel-body">
                        <table class=" table-bordered table-striped table-hover mb-0">
                          <tr  v-for="(value,index) in permissions">
                            <td :colspan="value.length" style="width:40%;">
                              {{ permission_name[index] }}
                            </td>
                            <td style="width:60%;">
                              <p v-for="(per_name,per_index) in value">
                                <label style="margin-bottom: 0" :for="per_index">
                                  <input :id="per_index" class="filled-in " v-model="permission_select" :value="per_index" type="checkbox">
                                  {{per_name}}
                                </label>
                              </p>
                            </td>
                          </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-6">
                  <div class="form-group">

                    <router-link :to="{ name: 'RoleIndex', params: { back_page: '1' } }" class="btn btn-secondary btn-sm">
                      {{trans.get('keys.quay_lai')}}
                    </router-link>

                    <button type="button" class="btn btn-primary btn-sm" @click="updateRole()">{{trans.get('keys.cap_nhat_quyen')}}</button>
                  </div>
                </div>
                <div  v-if="!roles.role_organization && roles.status === 0" class="col-6">
                  <div class="form-group text-right">
                    <button type="button" class="btn waves-effect waves-light btn-danger btn-sm" @click="deleteRole()">{{trans.get('keys.xoa')}}</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <mapping-course v-if="roles.role_organization" :role_id="role_id"></mapping-course>

  </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import MappingCourse from './MappingCourseComponent'


    export default {
        props: ['role_id'],
        components: {
          //vPagination,
          MappingCourse
        },
        data(){
            return{
                posts: {},
                current: 1,
                totalPages: 0,
                roles:{},
                permission_slug:{},
                permission_name:{},
                permission_select:{},
                //cap_select:{},
                role_organize:{},
                type:'off',
                organize_type:'',
                listcity:{},
                listbranch:{},
                listsaleroom:{},
                district:'',
                citys:[],
                branchs:[],
                salerooms:[],
            }
        },
        methods:{
            resetSelectpicker(){
                this.$nextTick(function () {
                    $('.selectpicker').selectpicker('refresh')
                });
            },
            getCity(){//Lấy danh sách tỉnh thành theo khu vực ( Quản lý nhân lực )
                axios.post('/role/get_data_city', {
                    district: this.district
                })
                    .then(response => {
                        this.listcity  = response.data;
                        this.$nextTick(function () {
                            $('.selectpicker').selectpicker('refresh')
                        });
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getBranch(){//Lấy danh sách Đại lý theo Tỉnh thành ( Quản lý nhân lực )
                axios.post('/role/get_data_branch', {
                    citys: this.citys
                })
                    .then(response => {
                        this.listbranch  = response.data;
                        this.$nextTick(function () {
                            $('.selectpicker').selectpicker('refresh')
                        });
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getSaleRoom(){//Lấy danh sách Điểm bán theo Đại lý ( Quản lý nhân lực )
                axios.post('/role/get_data_saleroom', {
                    branchs: this.branchs
                })
                    .then(response => {
                        this.listsaleroom  = response.data;
                        this.$nextTick(function () {
                            $('.selectpicker').selectpicker('refresh')
                        });
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            listData(paged){
                axios.post('/role/list_data_role', {
                    page: paged || this.current,
                    role_id: this.role_id
                })
                    .then(response => {
                        if(response.data){
                            this.roles              = response.data.roles;
                            if (response.data.roles.name === 'teacher') {
                              this.roles.name = 'creator';
                            }
                            this.permission_slug    = response.data.permission_slug;
                            this.permission_name    = response.data.permission_name;
                            this.permission_select  = response.data.permission_select;
                            this.role_organize      = response.data.role_organize;
                            if(this.role_organize){
                                if( this.role_organize.type == 'MB' || this.role_organize.type == "MT" && this.role_organize.type == "MN"){
                                    this.type = 'district';
                                }
                            }else{
                                this.type = 'off';
                            }
                        }

                        //this.cap_select         = response.data.cap_select;
                        this.posts              = response.data.pagination ? response.data.pagination.data : [];
                        //this.current            = response.data.pagination.current_page;
                        //this.totalPages         = response.data.pagination.total;

                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.listData();
            },
            updateRole(){
                if(!this.roles.name ) {
                    $('html,body').animate({scrollTop: 0}, 100);
                    $('.name_required').show();
                    return;
                }
                if(!this.roles.description ) {
                    $('html,body').animate({scrollTop: 0}, 100);
                    $('.description_required').show();
                    return;
                }

                /*if(this.type == 'district' && !this.organize_type){
                    $('.district_required').show();
                    return;
                }

                if(this.type == 'city' && this.citys.length == 0){
                    $('.city_required').show();
                    return;
                }

                if(this.type == 'branch' && this.branchs.length == 0){
                    $('.branch_required').show();
                    return;
                }

                if(this.type == 'saleroom' && this.salerooms.length == 0){
                    $('.saleroom_required').show();
                    return;
                }*/
                let current_pos = this;
                axios.post('/role/update', {
                    role_id: this.role_id,
                    per_slug_input: this.permission_select,
                    //cap_input: this.cap_select,
                    name:this.roles.name === 'creator' ? 'teacher' : this.roles.name,
                    description:this.roles.description,
                    type:this.type,
                    /*organize_type:this.organize_type,
                    citys:this.citys,
                    branchs:this.branchs,
                    salerooms:this.salerooms,*/
                })
                    .then(response => {
                        roam_message(response.data.status,response.data.message);
                    })
                    .catch(error => {
                        roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    });
            },
            deleteRole(){
                var role_id = this.role_id;
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_muon_xoa_quyen_nay'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/role/delete', {
                        role_id: role_id
                    })
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                            this.$router.push({ name: 'RoleIndex' });
                        })
                        .catch(error => {
                            roam_message('error', current_pos.trans.get('keys.ban_muon_xoa_quyen_nay'));
                        });
                });
            }
        },
        mounted() {
            this.listData();
        }
    }
</script>

<style scoped>

</style>
