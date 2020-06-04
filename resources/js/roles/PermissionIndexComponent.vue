<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
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
              <h4>{{trans.get('keys.danh_sach_chuc_nang')}}</h4>
              <div class="permission_panel">

                <div class="permission_panel_item" v-for="(permissions,index1) in permission_slug">
                  <span class="permission_title" v-html="permission_name[index1]"></span>
                  <div v-for="(value,index) in permissions" class="panel-group permission_content_tab" :id="'accordion_'+index" role="tablist" aria-multiselectable="true" style="margin-bottom: 5px">
                    <div class="panel panel-default">
                      <div class="panel-heading" role="tab" :id="'headingOne_'+index">
                        <h4 class="panel-title">
                          <a role="button" data-toggle="collapse" :href="'#per_'+index" aria-expanded="false" :aria-controls="'collapseOne_'+index" class="collapsed">
                            <i class="fab fa-get-pocket"></i> {{ permission_name[index] }}
                          </a>
                        </h4>
                      </div>
                      <div :id="'per_'+index" class="panel-collapse collapse" role="tabpanel" :aria-labelledby="'headingOne_'+index" aria-expanded="false" style="height: 0px;">
                        <div class="panel-body">
                          <div class="">
                            <table class="table table-bordered table-striped table-hover mb-0">
                              <tr v-for="(per_name,per_index) in value">
                                <td>
                                  <label style="margin-bottom: 0" :for="per_index">
                                    {{per_name}}
                                  </label>
                                </td>
                                <td>
                                  <router-link
                                    :to="{name: 'AddPermission', params: {slug: per_index}}"
                                    class="btn btn-primary btn-sm"
                                    style="margin-right: 3px;">Gán chức năng</router-link>
                                </td>
                              </tr>
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
        </div>
      </div>
    </div>
  </div>

</template>

<script>
    export default {
        data(){
            return{
                permission_slug:{},
                permission_name:{},
            }
        },
        methods:{
            listData(){
                axios.post('/permission/list_data')
                    .then(response => {
                        this.permission_slug    = response.data.permission_slug;
                        this.permission_name    = response.data.permission_name;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
        },
        mounted() {
            this.listData();
        }
    }
</script>

<style scoped>

</style>
