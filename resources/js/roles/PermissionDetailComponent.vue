<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/permission">{{ trans.get('keys.danh_muc_chuc_nang') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.chuc_nang') }}</li>
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
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name">{{trans.get('keys.key')}} (*)</label>
                    <div class="form-line">
                      <input v-model="data.permission.name" id="name" class="form-control" placeholder="VD: admin_permission" type="text">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="url">{{trans.get('keys.url')}} (*)</label>
                    <div class="form-line">
                      <input v-model="data.permission.url" id="url" class="form-control" type="text">
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="method">{{trans.get('keys.method')}} (*)</label>
                    <div class="form-line">
                      <select v-model="data.permission.method" class="form-control" id="method">
                        <option value="">Select a method</option>
                        <option value="POST">POST</option>
                        <option value="GET">GET</option>
                        <option value="DELETE">DELETE</option>
                        <option value="PATCH">PATCH</option>
                        <option value="ANY">ANY</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="method">{{trans.get('keys.gan_chuc_nang')}} (*)</label>
                    <div class="form-line">
                      <select v-model="data.permission.permission_slug" class="form-control">
                        <option value="">--{{trans.get('keys.chon_chuc_nang')}}--</option>
                        <option v-for="(per_slug) in data.permission_list" :value="per_slug.id" :disabled="per_slug.disabled" v-html="per_slug.space+per_slug.name"></option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12 text-right">
                  <button type="button" class="btn btn-primary btn-sm" @click="updatePermission()">{{trans.get('keys.cap_nhat')}}</button>
                </div>
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
        props: ['permission_id'],
        data(){
            return{
                data:{
                    permission:{
                        name:'',
                        description:'',
                        url:'',
                        method:'',
                        permission_slug:''
                    },
                    permission_list:{}
                },
            }
        },
        methods:{
            permissionDetail(){
                axios.post('/permission/detail',{
                    permission_id:this.permission_id,
                })
                    .then(response => {
                        this.data = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            updatePermission(){
                let current_pos = this;
                if(this.data.permission.name && this.data.permission.method && this.data.permission.url && this.data.permission.permission_slug){
                    axios.post('/permission/detail/update',{
                        permission_id:this.permission_id,
                        name:this.data.permission.name,
                        description:this.data.permission.description,
                        url:this.data.permission.url,
                        method:this.data.permission.method,
                        permission_slug:this.data.permission.permission_slug
                    })
                        .then(response => {
                            if(response.data == 'success'){
                              toastr['success'](current_pos.trans.get('keys.cap_nhat_chuc_nang_chi_tiet_thanh_cong'), current_pos.trans.get('keys.thanh_cong'));
                              current_pos.$router.push({ name: 'DetailPermission', params: {id: current_pos.permission_id} });
                            }else if(response.data == 'warning'){
                              toastr['warning'](current_pos.trans.get('keys.key_da_ton_tai'), current_pos.trans.get('keys.thong_bao'));
                            }else{
                              toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                            }
                        })
                        .catch(error => {
                            console.log(error.response.data);
                        });
                }else{
                      toastr['error'](current_pos.trans.get('keys.ban_can_nhap_day_du_thong_tin'), current_pos.trans.get('keys.thong_bao'));
                }
            },
        },
        mounted() {
            this.permissionDetail();
        }
    }
</script>

<style scoped>

</style>
