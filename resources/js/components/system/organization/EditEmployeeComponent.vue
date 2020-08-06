<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li v-if="selected_role === 'root' || selected_role === 'admin'" class="breadcrumb-item">
              <router-link :to="{name: 'IndexOrganization', params: {page: source_page}}" >
                {{ trans.get('keys.to_chuc') }}
              </router-link>
            </li>
            <li v-if="selected_role === 'root'|| selected_role === 'admin'" class="breadcrumb-item"><router-link :to="{ name: 'EditOrganization', params: {id: employee.organization_id}}">{{ employee.organization.name }}</router-link></li>
            <li class="breadcrumb-item">
              <router-link v-if="selected_role === 'leader' || selected_role === 'manager'" :to="{name: 'IndexEmployee', params: {page: source_page}, query: view_mode === 'recursive' ? {view_mode: view_mode} : {}}" >
                {{ trans.get('keys.nhan_vien') }}
              </router-link>
              <router-link v-else :to="{name: 'IndexEmployee', params: {page: source_page}, query: view_mode === 'recursive' ? {organization_id: organization_id, view_mode: view_mode} : {organization_id: organization_id}}" >
                {{ trans.get('keys.nhan_vien') }}
              </router-link>
            </li>
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
              <div class="edit_city_form form-material">
                <h5 class="mb-20">{{trans.get('keys.sua_nhan_vien')}}: {{employee.user.fullname}}</h5>

                <div class="row">


                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="employee_organization_id"><strong>{{trans.get('keys.to_chuc')}}</strong></label>

<!--                      <div class="input-group">-->
<!--                        <div class="wrap_search_box">-->
<!--                          <div class="btn_search_box" @click="selectOrganization()">-->
<!--                            <span>{{employee.organization.name}}</span>-->
<!--                          </div>-->
<!--                          <div class="content_search_box">-->
<!--                            <input @input="selectOrganization()" type="text" v-model="organization_keyword" class="form-control search_box" id="employee_organization_id">-->
<!--                            <i class="fa fa-spinner" aria-hidden="true"></i>-->
<!--                            <ul>-->
<!--                              <li @click="selectOrganizationItem(0)" >{{trans.get('keys.chon_to_chuc')}}</li>-->
<!--                              <li @click="selectOrganizationItem(item.id)" v-for="item in organization_list">{{item.name}}</li>-->
<!--                            </ul>-->
<!--                          </div>-->
<!--                        </div>-->
<!--                        <label v-if="!employee.organization_id" class="text-danger organization_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
<!--                      </div>-->

                      <treeselect :disabled="isOrgUpper" v-model="employee.organization_id" :multiple="false" :options="options" id="employee_organization_id"/>
                      <label v-if="!employee.organization_id" class="text-danger organization_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>

                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="employee_position" ><strong>{{trans.get('keys.vi_tri')}} *</strong></label>
                      <div class="input-group">
                        <select class="form-control" v-model="employee.position" id="employee_position">
                          <option value="">{{trans.get('keys.vi_tri') + ' *'}}</option>
                          <option v-for="position in filterPosition" :value="position.key">
                            {{ position.value }}
                          </option>
                        </select>
                      </div>
                      <label v-if="!employee.position" class="text-danger position_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>

                  <div class="col-sm-6 hide">
                    <input v-model="employee.enabled" type="checkbox" id="employee_enabled" style="width:20px; height:20px;">
                    <label for="employee_enabled">{{trans.get('keys.kich_hoat')}}</label>
                  </div>

                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group text-right">
                      <router-link :to="backButton()" class="btn btn-secondary btn-sm" style="color: rgb(255, 255, 255);">{{trans.get('keys.quay_lai')}}</router-link>
                      <button type="button" class="btn btn-primary btn-sm" @click="update()">{{trans.get('keys.cap_nhat')}}</button>
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
  //import vPagination from 'vue-plain-pagination'
  export default {
    props: [
      'id',
      'source_page',
      'organization_id',
      'roles_ready',
      'selected_role',
      'view_mode'
    ],
    components: {
      //vPagination
    },
    data() {
      return {
        employee: {
          organization_id: 0,
          user: {
            fullname: '',
          },
          organization: {
            id: 0,
            name: ''
          }
        },
        organization_list: [],
        organization_keyword: '',
        //Treeselect options
        options: []
      }
    },
    methods: {
      backButton() {
        let query = {};
        if (this.organization_id && this.organization_id !== 0) {
          query.organization_id = this.organization_id;
        }
        if (this.view_mode === 'recursive') {
          query.view_mode = this.view_mode;
        }
        return {
          name: 'IndexEmployee',
          params: {page: this.source_page,back_page:'1'},
          query: query
        };
      },
      selectOrganizationItem(id) {
        this.employee.organization_id = id;
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
      setOptions(list, current_id) {
        let outPut = [];
        for (const [key, item] of Object.entries(list)) {
          let newOption = {
            id: item.id,
            label: item.code,
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
      getData() {
        axios.post('/organization-employee/detail/'+this.id)
          .then(response => {
            this.employee = response.data;
            this.selectOrganization(this.employee.organization_id);
          })
          .catch(error => {
            console.log(error.response.data);
          })
      },
      update() {
          let current_pos = this;
        if(!this.employee.organization_id){
          $('.organization_required').show();
          return;
        }
        if(!this.employee.position){
          $('.position_required').show();
          return;
        }
        axios.post('/organization-employee/update',{
          id: this.id,
          organization_id: this.employee.organization_id,
          user_id: this.employee.user_id,
          position: this.employee.position,
          enabled: this.employee.enabled
        })
          .then(response => {
            if(response.data.key) {
              toastr['error'](response.data.message, this.trans.get('keys.loi'));
              $('.form-control').removeClass('error');
              $('#employee_'+response.data.key).addClass('error');
            }
            else{
              if (response.data.status === 'success') {
                toastr[response.data.status](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                $('.form-control').removeClass('error');
                let query = { organization_id: current_pos.employee.organization_id };
                if (this.view_mode === 'recursive') {
                  query.view_mode = current_pos.view_mode;
                }
                this.$router.push({ name: 'IndexEmployee', query: query});
              }
              else {
                toastr[response.data.status](response.data.message, current_pos.trans.get('keys.that_bai'));
              }
            }
          })
          .catch(error => {
            //console.log(error);
            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
          })
      },
    },
    computed: { //Phải gọi trên html nó mới trigger computed value
      filterPosition: function() {
        let default_response = [
          {
            key: 'manager',
            value: this.trans.get('keys.manager')
          },
          {
            key: 'leader',
            value: this.trans.get('keys.leader')
          },
          {
            key: 'employee',
            value: this.trans.get('keys.employee')
          }
        ];
        if (this.roles_ready) {
          //overwrite filter for manager / leader
          if (this.selected_role === 'root' || this.selected_role === 'admin') {
            return default_response;
          }
          let response = [];
          if (this.selected_role === 'manager') {
            response.push({
              key: 'leader',
              value: this.trans.get('keys.leader')
            });
          }
          if (this.selected_role === 'manager' || this.selected_role === 'leader') {
            response.push({
              key: 'employee',
              value: this.trans.get('keys.employee')
            });
          }
          return response;
        } else {
          return default_response;
        }
      },
      isOrgUpper: function() {
        if (this.roles_ready) {
          //overwrite filter for manager / leader
          return this.selected_role === 'manager' || this.selected_role === 'leader';
        } else {
          return false;
        }
      }
    },
    mounted() {
      this.getData();
    }
  }
</script>

<style scoped>

</style>
