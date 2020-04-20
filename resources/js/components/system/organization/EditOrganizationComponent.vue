<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item" v-if="selected_role === 'root' || getSelectedRole==true">
              <router-link :to="{name: 'IndexOrganization', params: {page: source_page}}">
                {{ trans.get('keys.to_chuc') }}
              </router-link>
            </li>
            <li v-else class="breadcrumb-item">
              {{ trans.get('keys.to_chuc') }}
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
                <h5 class="mb-20">{{trans.get('keys.sua_to_chuc')}}: {{organization.name}}</h5>

                <div class="row">

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="organization_name"><strong>{{trans.get('keys.ten_to_chuc')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" id="organization_name" class="form-control form-control-line" :placeholder="trans.get('keys.ten_to_chuc')+' *'" required v-model="organization.name">
                      </div>
                      <label v-if="!organization.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="organization_code"><strong>{{trans.get('keys.ma_to_chuc')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" id="organization_code" class="form-control form-control-line" v-model="organization.code">
                      </div>
                      <em>{{trans.get('keys.gom_chu_cai_khong_dau_chu_so_ky_tu_dac_biet_-_/_.')}}</em>
                      <label v-if="!organization.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label><strong>{{trans.get('keys.mo_ta')}}</strong></label>
                      <div class="input-group">
                        <textarea id="organization_description" class="form-control form-control-line" :placeholder="trans.get('keys.mo_ta')" required v-model="organization.description"></textarea>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="organization_parent_id" ><strong>{{trans.get('keys.truc_thuoc')}}</strong></label>

<!--                      <div class="input-group">-->
<!--                        <div class="wrap_search_box">-->
<!--                          <div class="btn_search_box" @click="selectParent()">-->
<!--                            <span v-if="!organization.parent_name">{{trans.get('keys.chon_to_chuc')}}</span>-->
<!--                            <span v-else>{{organization.parent_name}}</span>-->
<!--                          </div>-->
<!--                          <div class="content_search_box">-->
<!--                            <input @input="selectParent()" type="text" v-model="parent_keyword" class="form-control search_box" id="organization_parent_id">-->
<!--                            <i class="fa fa-spinner" aria-hidden="true"></i>-->
<!--                            <ul>-->
<!--                              <li @click="selectParentItem(0)" >{{trans.get('keys.chon_to_chuc')}}</li>-->
<!--                              <li @click="selectParentItem(item.id)" v-for="item in organization_parent_list">{{item.name}}</li>-->
<!--                            </ul>-->
<!--                          </div>-->
<!--                        </div>-->
<!--                      </div>-->

                      <treeselect v-model="organization.parent_id" :multiple="false" :options="options" id="organization_parent_id"/>

                    </div>
                  </div>

                  <div class="col-sm-6">
                    <input v-model="organization.enabled" type="checkbox" id="organization_enabled" style="width:20px; height:20px;">
                    <label for="organization_enabled">{{trans.get('keys.kich_hoat')}}</label>
                  </div>
                  <div class="col-sm-6">
                    <input v-model="organization.is_role" type="checkbox" id="organization_is_role" style="width:20px; height:20px;">
                    <label for="organization_is_role">{{trans.get('keys.su_dung_phan_quyen')}}</label>
                    <br>
                    <em>{{ trans.get('keys.cho_phep_phan_quyen_quan_ly_du_lieu_khoa_hoc_trong_giao_dien_chinh_sua_quyen') }}</em>
                  </div>

                </div>
                <div class="row mt-3">
                  <div class="col-12">
                    <div class="form-group text-right">
                      <router-link v-if="selected_role === 'root'" :to="{name: 'IndexOrganization', params: {page: source_page}}" class="btn btn-secondary btn-sm" style="color: rgb(255, 255, 255);">
                        {{trans.get('keys.quay_lai')}}
                      </router-link>
                      <router-link v-else :to="{name: 'IndexEmployee', params: {page: source_page}}" class="btn btn-secondary btn-sm" style="color: rgb(255, 255, 255);">
                        {{trans.get('keys.quay_lai')}}
                      </router-link>


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
    props: {
      source_page: Number,
      current_roles: Object,
      roles_ready: Boolean,
      id: [ String, Number ]
    },
    //components: {vPagination},
    data() {
      return {
        organization: {
          id: 0,
          name: '',
          code: '',
          manage: 0,
          description: '',
          parent_id: 0,
          parent_name: '',
          enabled: true,
          is_role: false
        },
        organization_parent_list:[],
        parent_keyword:'',
        //Treeselect options
        options: [
          {
            id: 0,
            label: this.trans.get('keys.chon_to_chuc')
          }
        ],
        selected_role: 'user'
      }
    },
    methods: {
      selectParentItem(parent_id) {
        this.organization.parent_id = parent_id;
      },
      selectParent(current_id) {
        $('.content_search_box').addClass('loadding');
        axios.post('/organization/list',{
          keyword: this.parent_keyword,
          exclude: this.organization.id,
          level: 1,
          paginated: 0
        })
          .then(response => {
            this.organization_parent_list = response.data;
            //Set options recursive
            //this.options = this.options.concat(this.setOptions(response.data));
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
          if (item.id === parseInt(this.id)) {
            newOption.isDisabled = true;
          }
          outPut.push(newOption);
        }
        return outPut;
      },
      getData() {
        axios.post('/organization/detail/'+this.id)
          .then(response => {
            this.organization = response.data;
            if (response.data.role_organization) {
              this.organization.is_role = true;
            }
            if (response.data.parent) {
              this.organization.parent_id = response.data.parent.id;
              this.organization.parent_name = response.data.parent.name;
            }
            this.selectParent(this.organization.parent_id);
          })
          .catch(error => {
            //console.log(error);
          })
      },
      update() {
          let current_pos = this;
        if(!this.organization.name){
          $('.name_required').show();
          return;
        }
        if(!this.organization.code){
          $('.code_required').show();
          return;
        }
        axios.post('/organization/update',{
          name: this.organization.name,
          code: this.organization.code,
          parent_id: this.organization.parent_id,
          description: this.organization.description,
          id: this.organization.id,
          enabled: this.organization.enabled,
          is_role: this.organization.is_role
        })
          .then(response => {
            if(response.data.key) {
              toastr['error'](response.data.message, current_pos.trans.get('keys.loi'));
              $('.form-control').removeClass('error');
              $('#organization_'+response.data.key).addClass('error');
            }else{
              toastr[response.data.status](response.data.message, current_pos.trans.get('keys.thanh_cong'));
              if(response.data.status === 'success'){
                $('.form-control').removeClass('error');
              }
            }
          })
          .catch(error => {
            //console.log(error);
            roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
          })
      },
      getRoleFromCurrentRoles (current_roles) {
        if (current_roles.root_user === true) {
          this.selected_role = 'root';
        } else if (current_roles.has_role_manager === true) {
          this.selected_role = 'manager';
        } else if (current_roles.has_role_leader === true) {
          this.selected_role = 'leader';
        } else if (current_roles.has_user_market === true) {
          this.selected_role = 'user_market';
        } else {
          this.selected_role = 'user';
        }
      },
    },
    mounted() {
      this.getData();
    },
      computed: { //Phải gọi trên html nó mới trigger computed value
          getSelectedRole: function() {
              if (this.roles_ready) {
                  this.getRoleFromCurrentRoles(this.current_roles);
                  return true;
              }
              return false;
          }
      },
    // watch: {
    //   roles_ready: function(newVal, oldVal) {
    //     if (newVal === true && oldVal === false) {
    //       this.getRoleFromCurrentRoles(this.current_roles);
    //     }
    //   }
    // }
  }
</script>

<style scoped>

</style>
