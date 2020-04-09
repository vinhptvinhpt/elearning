<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/permission">{{ trans.get('keys.chuc_nang') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.them_chuc_nang') }}</li>
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
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="name">{{trans.get('keys.key')}} (*)</label>
                    <div class="form-line">
                      <input v-model="permission.name" id="name" class="form-control" placeholder="VD: admin_permission" type="text">
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="url">{{trans.get('keys.url')}} (*)</label>
                    <div class="form-line">
                      <input v-model="permission.url" id="url" class="form-control" type="text" placeholder="VD: abc/xyz" >
                    </div>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="method">{{trans.get('keys.method')}} (*)</label>
                    <div class="form-line">
                      <select v-model="permission.method" class="form-control" id="method">
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
              </div>
              <div class="row">
                <div class="col-12 mb-3 text-right">
                  <button type="button" class="btn btn-primary btn-sm" @click="addPermission()">{{trans.get('keys.them_chuc_nang')}}</button>
                </div>
              </div>
              <p></p>
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                      <thead>
                      <th>{{trans.get('keys.key')}}</th>
                      <th>{{trans.get('keys.url')}}</th>
                      <th>{{trans.get('keys.method')}}</th>
                      <th>{{trans.get('keys.hanh_dong')}}</th>
                      </thead>
                      <tbody>
                      <tr v-for="per in data">
                        <td>{{ per.name }}</td>
                        <td>{{ per.url }}</td>
                        <td>{{ per.method }}</td>
                        <td>

                          <router-link :title="trans.get('keys.sua')"
                                       class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                       :to="{ name: 'DetailPermission', params: { id: per.id }}">
                            <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                          </router-link>

                          <button @click.prevent="deletePost('/permission/detail/delete/'+per.id)"
                                  class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                            <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
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
    </div>
  </div>

</template>

<script>
    export default {
        props: ['slug'],
        data(){
            return{
                permission:{
                    name:'',
                    url:'',
                    description:'',
                    method:''
                },
                data:{},
            }
        },
        methods:{
            listPermissionDetail(){
                axios.post('/permission/slug/list_detail',{
                    slug:this.slug,
                })
                    .then(response => {
                        this.data = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            addPermission(){
                let current_pos = this;
                if(this.permission.name && this.permission.method && this.permission.url){
                    axios.post('/permission/slug/add',{
                        slug:this.slug,
                        name:this.permission.name,
                        url:this.permission.url,
                        description:this.permission.description,
                        method:this.permission.method
                    })
                        .then(response => {
                            if(response.data == 'success'){
                                toastr['success']("Gán chức năng chi tiết thành công.", current_pos.trans.get('keys.thanh_cong'));
                                current_pos.$router.push({ name: 'AddPermission', params: {slug: current_pos.slug} });
                                current_pos.listPermissionDetail();
                            }else if(response.data == 'warning'){
                              toastr['warning']("Key đã tồn tại.", current_pos.trans.get('keys.thong_bao'));
                            }else{
                              toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                            }
                        })
                        .catch(error => {
                            console.log(error.response.data);
                        });
                }
            },
            deletePost(url) {
                let current_pos = this;
                swal({
                    title: "Bạn muốn xóa mục đã chọn",
                    text: "Chọn 'ok' để thực hiện thao tác.",
                    type: "error",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post(url)
                        .then(response => {
                          toastr['success']("Xóa thành công!", current_pos.trans.get('keys.thanh_cong'));
                            current_pos.$router.push({ name: 'AddPermission', params: {slug: current_pos.slug} });
                            swal.close();
                            current_pos.listPermissionDetail();
                        })
                        .catch(error => {
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                            console.log(error);
                        });
                });

                return false;
            }
        },
        mounted() {
            this.listPermissionDetail();
        }
    }
</script>

<style scoped>

</style>
