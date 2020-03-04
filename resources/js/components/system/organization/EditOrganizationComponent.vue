<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item">
              <router-link :to="{name: 'IndexOrganization', params: {page: source_page}}" >
                {{ trans.get('keys.to_chuc') }}
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
                <h5 class="mb-20">{{trans.get('keys.sua_chi_nhanh')}}: {{organization.name}}</h5>

                <div class="row">

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.ten_to_chuc')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" id="organization_name" class="form-control form-control-line" :placeholder="trans.get('keys.ten_to_chuc')+' *'" required v-model="organization.name">
                      </div>
                      <label v-if="!organization.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.ma_to_chuc')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" id="organization_code" class="form-control form-control-line" v-model="organization.code">
                      </div>
                      <em>{{trans.get('keys.gom_chu_cai_khong_dau_chu_so_ky_tu_dac_biet_-_/_.')}}</em>
                      <label v-if="!organization.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.mo_ta')}}</strong></label>
                      <div class="input-group">
                        <textarea id="organization_description" class="form-control form-control-line" :placeholder="trans.get('keys.mo_ta')" required v-model="organization.description"></textarea>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.truc_thuoc')}}</strong></label>
                      <div class="input-group">
                        <div class="wrap_search_box">
                          <div class="btn_search_box" @click="selectParent()">
                            <span v-if="!organization.parent_name">{{trans.get('keys.chon_to_chuc')}}</span>
                            <span v-else>{{organization.parent_name}}</span>
                          </div>
                          <div class="content_search_box">
                            <input @input="selectParent()" type="text" v-model="parent_keyword" class="form-control search_box">
                            <i class="fa fa-spinner" aria-hidden="true"></i>
                            <ul>
                              <li @click="selectParentItem(0)" >{{trans.get('keys.chon_to_chuc')}}</li>
                              <li @click="selectParentItem(item.id)" v-for="item in organization_parent_list">{{item.name}}</li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <input v-model="organization.enabled" type="checkbox" id="enabled" style="width:20px; height:20px;">
                    <label for="enabled">{{trans.get('keys.kich_hoat')}}</label>
                  </div>

                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group text-right">
                      <router-link :to="{name: 'IndexOrganization', params: {page: source_page}}" class="btn btn-secondary btn-sm" style="color: rgb(255, 255, 255);">
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
    props: ['id', 'source_page'],
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
          enabled: true
        },
        organization_parent_list:[],
        parent_keyword:'',
      }
    },
    methods: {
      selectParentItem(parent_id){
        this.organization.parent_id = parent_id;
      },
      selectParent(){
        $('.content_search_box').addClass('loadding');
        axios.post('/organization/list',{
          keyword: this.parent_keyword,
          exclude: this.organization.id
        })
          .then(response => {
            this.organization_parent_list = response.data;
            $('.content_search_box').removeClass('loadding');
          })
          .catch(error => {
            $('.content_search_box').removeClass('loadding');
          })
      },
      getData(){
        axios.post('/organization/detail/'+this.id)
          .then(response => {
            this.organization = response.data;
          })
          .catch(error => {
            console.log(error.response.data);
          })
      },
      update(){
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
          enabled: this.organization.enabled
        })
          .then(response => {
            if(response.data.key) {
              roam_message('error',response.data.message);
              $('.form-control').removeClass('error');
              $('#organization_'+response.data.key).addClass('error');
            }else{
              roam_message(response.data.status,response.data.message);
              if(response.data.status === 'success'){
                $('.form-control').removeClass('error');
              }
            }
          })
          .catch(error => {
            console.log(error);
            roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
          })
      }
    },
    mounted() {
      this.getData();
    }
  }
</script>

<style scoped>

</style>
