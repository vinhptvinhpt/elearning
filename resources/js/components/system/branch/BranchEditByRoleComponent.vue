<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/branch/list">{{ trans.get('keys.dai_ly') }}</router-link></li>
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
                <h5 class="mb-20"><span class="text-uppercase">{{trans.get('keys.sua_dai_ly')}}: </span>{{data.branch.name}}</h5>
                <!-- <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label ><strong>Tên *</strong></label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-line" placeholder="Tên *" required v-model="data.branch.name">
                            </div>
                            <label v-if="!data.branch.name" class="text-danger name_required hide">Trường bắt buộc phải nhập.</label>
                        </div>
                    </div>
                </div> -->
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.ten_dai_ly')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" id="branch_name" class="form-control form-control-line" :placeholder="trans.get('keys.ten')+' *'" required v-model="data.branch.name">
                      </div>
                      <label v-if="!data.branch.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.ma')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" id="branch_code" class="form-control form-control-line" v-model="data.branch.code" @input="inputClearMessage('code_error')">
                      </div>
                      <em>{{trans.get('keys.gom_chu_cai_khong_dau_chu_so_ky_tu_dac_biet_-_/_.')}}</em>
                      <label v-if="!data.branch.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      <label v-if="data.branch.code" class="text-danger code_error hide">{{trans.get('keys.ma_dai_ly_da_ton_tai')}}</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.tinh_thanh')}} *</strong></label>
                      <div class="input-group">
                        <select class="form-control selectpicker" v-model="data.branch.city_id" data-live-search="true">
                          <option value="0">-- {{trans.get('keys.chon_tinh_thanh')}} * --</option>
                          <option v-for="city in data.city" :value="city.id">{{city.name}}</option>
                        </select>
                      </div>
                      <label v-if="!data.branch.city_id || data.branch.city_id === 0" class="text-danger city_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.nguoi_quan_ly')}}</strong></label>
                      <div class="input-group">
                        <div class="wrap_search_box">
                          <div class="btn_search_box">
                            <span v-if="data.branch.user_id">{{data.branch.fullname}}</span>
                            <span v-else>{{trans.get('keys.chon_quan_ly')}}</span>
                          </div>
                          <div class="content_search_box">
                            <input @input="listDataSearchBoxUser()" type="text" v-model="search_box_user" class="form-control search_box">
                            <i class="fa fa-spinner" aria-hidden="true"></i>
                            <ul>
                              <li @click="selectSearchBoxUser(0)" >{{trans.get('keys.chon_quan_ly')}}</li>
                              <li v-if="data.branch.user_id" @click="selectSearchBoxUser(data.branch.user_id)" class="active">{{ data.branch.fullname }}</li>
                              <li @click="selectSearchBoxUser(item.user_id)" v-for="item in data_search_box_user" :data-value="item.user_id">{{item.fullname}}</li>
                            </ul>
                          </div>
                        </div>
                      </div>
                      <label v-if="!data.branch.user_id" class="text-danger user_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.dia_diem')}}</strong></label>
                      <div class="input-group">
                        <input type="text" class="form-control form-control-line" :placeholder="trans.get('keys.dia_diem')" v-model="data.branch.address">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group text-right">

                      <router-link to="/tms/branch/list" class="btn btn-secondary btn-sm" style="color: rgb(255, 255, 255);">{{trans.get('keys.quay_lai')}}</router-link>

                      <button type="button" class="btn btn-primary btn-sm" @click="updateBranch()">{{trans.get('keys.cap_nhat')}}</button>
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
        props: ['id'],
        //components: {vPagination},
        data() {
            return {
                data:{
                    branch:{
                        name:'',
                        city_id:0,
                        user_id:0,
                        address:'',
                        fullname:'',
                        code:'',
                    }
                },
                data_search_box_user:[],
                search_box_user:'',
            }
        },
        methods: {
            inputClearMessage(messClass){
                $('.'+messClass).hide();
            },
            selectSearchBoxUser(input_search_box_id){
                this.data.branch.user_id = input_search_box_id;
            },
            listDataSearchBoxUser(){
                $('.content_search_box').addClass('loadding');
                axios.post('/system/organize/branch/data_search_box_user',{
                    keyword:this.search_box_user,
                })
                    .then(response => {
                        this.data_search_box_user = response.data;
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                        this.data_search_box_user = [];
                    })
            },
            getDataBranch(){
                axios.post('/system/organize/branch/detail_data/'+this.id)
                    .then(response => {
                        this.data = response.data;
                        this.$nextTick(function () {
                            $('.selectpicker').selectpicker('refresh')
                        });
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
            },
            updateBranch(){
                if(!this.data.branch.name){
                    $('.name_required').show();
                    return;
                }
                if(!this.data.branch.code){
                    $('.code_required').show();
                    return;
                }
                if(!this.data.branch.city_id || this.data.branch.city_id === 0){
                    $('.city_required').show();
                    return;
                }
                axios.post('/system/organize/branch/update/'+this.id,{
                    name:this.data.branch.name,
                    code:this.data.branch.code,
                    user_id:this.data.branch.user_id,
                    city_id:this.data.branch.city_id,
                    address:this.data.branch.address,
                })
                    .then(response => {
                        if(response.data.key) {
                            roam_message('error',response.data.message);
                            $('.form-control').removeClass('error');
                            $('#branch_'+response.data.key).addClass('error');
                        }else{
                            roam_message(response.data.status,response.data.message);
                            if(response.data.status === 'success'){
                                $('.form-control').removeClass('error');
                            }
                        }
                    })
                    .catch(error => {
                        roam_message('error','Lỗi hệ thống. Thao tác thất bại');
                    })
            }
        },
        mounted() {
            this.getDataBranch();
            this.listDataSearchBoxUser();
        }
    }
</script>

<style scoped>

</style>
