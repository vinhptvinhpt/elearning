<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.quyen_he_thong') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="row mb-4">
        <div class="col-12">
          <div class="accordion" id="accordion_1">
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1" aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
              </div>
              <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                <div class="card-body">
                  <label class="hide output error"></label>
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <div class="input-group">
                          <input type="text" class="form-control form-control-line" :placeholder="trans.get('keys.ten_quyen')+' *'" v-model="role.name">
                        </div>
                        <label class="text-danger hide name error"></label>
                        <label v-if="!role.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group">
                        <div class="input-group">
                          <textarea :placeholder="trans.get('keys.mo_ta') + ' *'" class="form-control form-control-line" v-model="role.description"></textarea>
                        </div>
                        <label class="text-danger hide description error"></label>
                        <label v-if="!role.description" class="text-danger description_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group text-right">
                        <button type="button" class="btn btn-primary btn-sm" @click="createRole()">{{trans.get('keys.them_quyen')}}</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h4 class="mb-20">{{trans.get('keys.danh_sach_quyen')}}</h4>
              <p class="mb-10">{{trans.get('keys.tong_so_quyen_hien_tai')}} : {{roles.length}} {{trans.get('keys.quyen')}}.</p>
              <div class="table-responsive">
                <button class="hide btn_fillter" id="btn-filter" @click="listRoles()"></button>
                <table class="table_res">
                  <thead>
                  <th>{{trans.get('keys.stt')}}</th>
                  <th>{{trans.get('keys.ten_quyen')}}</th>
                  <th class=" mobile_hide">{{trans.get('keys.mo_ta')}}</th>
                  <th>{{trans.get('keys.hanh_dong')}}</th>
                  </thead>
                  <tbody>
                  <tr v-for="(ro,index) in roles">
                    <td>{{ index+1 }}</td>
                    <td>{{ ro.name }}</td>
                    <td class=" mobile_hide">{{ ro.description }}</td>
                    <td>

                      <router-link :title="trans.get('keys.sua')"
                                   class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                   :to="{ name: 'RoleEdit', params: { role_id: ro.id }}">
                        <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                      </router-link>

<!--                      <button v-if="ro.status == 0" @click="deleteRole(ro.id)" class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2" :title="trans.get('keys.xoa')">-->
<!--                        <span  class="btn-icon-wrap"><i class="fal fa-trash"></i></span>-->
<!--                      </button>-->

                    </td>
                  </tr>
                  </tbody>
                </table>
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
        data() {
            return {
                role: {
                    name: '',
                    description:'',
                },
                roles:[]
            }
        },
        methods: {
            createRole(){
                $('.error').hide();
                $('.output.error').hide();
                $('.output.error').html();
                if(!this.role.name ) {
                    $('.name.error').show();
                    $('.name.error').html('Trường bắt buộc phải nhập.');
                    return;
                }
                if(!this.role.description ) {
                    $('.description.error').show();
                    $('.description.error').html('Trường bắt buộc phải nhập.');
                    return;
                }
                axios.post('/role/create', {
                    name: this.role.name,
                    description: this.role.description,
                })
                    .then(response => {
                        roam_message(response.data.status,response.data.message);
                        this.role.name = '';
                        this.role.description = '';
                    })
                    .catch(error => {
                        roam_message('error','Lỗi hệ thống. Thao tác thất bại');
                    });
            },
            listRoles(){
                axios.post('/role/list_role')
                    .then(response => {
                        this.roles = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            deleteRole(role_id){
                var role_id = role_id;
                swal({
                    title: "Thông báo",
                    text: "Bạn muốn xóa quyền này.",
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
                        })
                        .catch(error => {
                            roam_message('error','Lỗi hệ thống. Thao tác thất bại');
                        });
                });
            }
        },
        mounted() {
            this.listRoles();
        }
    }
</script>

<style scoped>

</style>
