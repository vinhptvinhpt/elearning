<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>

            <li v-if="owner_type === 'master'" class="breadcrumb-item">
              <router-link to="/tms/branch/list">
                {{ trans.get('keys.dai_ly') }}
              </router-link>
            </li>
            <li class="breadcrumb-item">{{ data.saleroom.branch_name }}</li>
            <li class="breadcrumb-item">
              <router-link to="/tms/saleroom/list">
                {{ trans.get('keys.danh_sach_diem_ban') }}
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
                <h5 class="mb-20">{{trans.get('keys.sua_diem_ban')}}</h5>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.ten')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" class="form-control form-control-line" :placeholder="trans.get('keys.ten')+' *'" required v-model.name="data.saleroom.name">
                      </div>
                      <label v-if="!data.saleroom.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.ma')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" class="form-control form-control-line"  disabled :value="data.saleroom.code" @input="inputClearMessage('code_error')">
                      </div>
                      <label v-if="!data.saleroom.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      <label v-if="data.saleroom.code" class="text-danger code_error hide">{{trans.get('keys.ma_diem_ban_da_ton_tai')}}</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.quan_ly')}}</strong></label>
                      <div class="input-group">
                        <div class="wrap_search_box">
                          <div class="btn_search_box">
                            <span v-if="data.saleroom.fullname">{{data.saleroom.fullname}}</span>
                            <span v-else>{{trans.get('keys.khong_chon_quan_ly')}}</span>
                          </div>
                          <div class="content_search_box">
                            <input @input="listDataSearchBoxUser()" type="text" v-model="search_box_user" class="form-control search_box">
                            <i class="fa fa-spinner" aria-hidden="true"></i>
                            <ul>
                              <li @click="selectSearchBoxUser(0)" >{{trans.get('keys.khong_chon_quan_ly')}}</li>
                              <li v-if="data.saleroom.user_id" @click="selectSearchBoxUser(data.saleroom.user_id)" class="active">{{ data.saleroom.fullname }}</li>
                              <li v-if="data_search_box_user.length == 0">{{trans.get('keys.khong_tim_thay_du_lieu')}}</li>
                              <li @click="selectSearchBoxUser(item.user_id)" v-else v-for="item in data_search_box_user" :data-value="item.user_id">{{item.fullname}}</li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.tinh_thanh')}}</strong></label>
                      <div class="input-group">
                        <select class="form-control" disabled v-model="data.saleroom.city_id" data-live-search="true">
                          <option value="">{{trans.get('keys.chon_tinh_thanh')}}</option>
                          <option v-for="city in data_search_box_city" :value="city.id" >{{city.name}}</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.dai_ly')}}</strong></label>
                      <div class="input-group">
                        <!-- <select class="form-control form-control-line" v-model="data.saleroom.branch_id">
                            <option value="">-- Đại lý --</option>
                            <option v-if="data.saleroom.branch_id" :value="data.saleroom.branch_id" >{{data.saleroom.branch_name}}</option>
                            <option v-for="branch in branchs" :value="branch.branch.id" >{{branch.branch.name}}</option>
                        </select> -->
                        <div class="wrap_search_box">
                          <div class="btn_search_box disabled" @click="listDataSearchBox()">
                            <span>{{ data.saleroom.branch_name ? data.saleroom.branch_name : 'Chọn Đại lý' }}</span>
                          </div>
                          <div class="content_search_box" style="display: none;"><!-- hiển thị lại khi k tích hợp diva -->
                            <input @input="listDataSearchBox()" type="text" v-model="search_box" class="form-control search_box">
                            <i class="fa fa-spinner" aria-hidden="true"></i>
                            <ul>
                              <li @click="selectSearchBox(0)" >{{trans.get('keys.khong_chon_dai_ly')}}</li>
                              <li v-if="data_search_box.length == 0" value=""> {{trans.get('keys.khong_tim_thay_du_lieu')}} </li>
                              <li @click="selectSearchBox(item.id)" v-else v-for="item in data_search_box" :data-value="item.id">{{item.name}} ( {{ item.code }} )</li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <!-- <div class="col-sm-6">
                      <div class="form-group">
                          <label ><strong>Địa điểm </strong></label>
                          <div class="input-group">
                              <input type="text" class="form-control form-control-line" placeholder="Địa điểm" v-model.address="data.saleroom.address">
                          </div>
                      </div>
                  </div> -->
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group text-right">
                      <button type="button" class="btn waves-effect waves-light btn-primary btn-sm" @click="updateSaleRoom()">{{trans.get('keys.cap_nhat')}}</button>

                      <router-link v-if="owner_type === 'master'" :to="{ name: 'SaleroomIndexByRole', query: {branch_id: branchId} }" class="btn waves-effect waves-light btn-sm btn-secondary">
                        {{trans.get('keys.quay_lai')}}
                      </router-link>
                      <router-link v-else :to="{ name: 'SaleroomIndexByRole'}" class="btn waves-effect waves-light btn-sm btn-secondary">
                        {{trans.get('keys.quay_lai')}}
                      </router-link>

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
        props: ['id', 'owner_type'],
        //components: {vPagination},
        data() {
            return {
                branchId: 0,
                branch_name: '',
                data:{
                    saleroom:{
                        name:'',
                        code:'',
                        user_id:0,
                        branch_id:'',
                        city_id:'',
                        address:'',
                        branch_name:'',
                        fullname:''
                    },
                },
                inputDistrict:'*',
                inputCity:'',
                data_search_box_city:[],
                data_search_box:[],
                search_box:'',
                data_search_box_user:[],
                search_box_user:'',
            }
        },
        methods: {
            inputClearMessage(messClass){
                $('.'+messClass).hide();
            },
            selectSearchBoxUser(input_search_box_id){
                this.data.saleroom.user_id = input_search_box_id;
            },
            listDataSearchBoxUser(){
                $('.content_search_box').addClass('loadding');
                axios.post('/system/organize/saleroom/data_search_box_user',{
                    keyword:this.search_box_user,
                })
                    .then(response => {
                        this.data_search_box_user = response.data;
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                        console.log(error.response.data);
                    })
            },
            getDataSaleRoom(){
                axios.post('/system/organize/saleroom/detail_data/'+this.id)
                    .then(response => {
                        this.data = response.data;
                        this.setBranchId();
                        /*this.$nextTick(function () {
                            $('.selectpicker').selectpicker('refresh')
                        });*/
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
            },
            selectSearchBox(input_search_box_id){
                this.data.saleroom.branch_id = input_search_box_id;
            },
            listDataSearchBox(){
                $('.content_search_box').addClass('loadding');
                axios.post('/system/organize/saleroom/data_search_box',{
                    keyword:this.search_box,
                    city:this.data.saleroom.city_id,
                })
                    .then(response => {
                        this.data_search_box = response.data;
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                        console.log(error.response.data);
                    })
            },
            listDataSearchBoxCity(){
                axios.post('/system/organize/saleroom/data_search_box_city')
                    .then(response => {
                        this.data_search_box_city = response.data;
                        /*this.$nextTick(function () {
                            $('.selectpicker').selectpicker('refresh')
                        });*/
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
            },
            getCity(){
                axios.post('/system/organize/saleroom/get_city_by_district', {
                    district: this.inputDistrict
                })
                    .then(response => {
                        this.citys = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getBranch(){
                axios.post('/system/organize/saleroom/get_branch_by_city', {
                    city: this.inputCity,
                    saleroom_id: this.id,
                })
                    .then(response => {
                        this.branchs = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            updateSaleRoom(){
                if(!this.data.saleroom.name){
                    $('.name_required').show();
                    return;
                }
                if(!this.data.saleroom.code){
                    $('.code_required').show();
                    return;
                }
                axios.post('/system/organize/saleroom/update/'+this.id,{
                    name: this.data.saleroom.name,
                    code: this.data.saleroom.code,
                    user_id: this.data.saleroom.user_id,
                    branch_id: this.data.saleroom.branch_id,
                    address: this.data.saleroom.address,
                })
                    .then(response => {
                        if(response.data === 'success') {
                            swal({
                                title: "Thông báo!",
                                text: "Sửa Điểm bán thành công!",
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }else if(response.data === 'errorCode'){
                            $('.code_error').show();
                        }else{
                            swal({
                                title: "Thông báo!",
                                text: "Lỗi hệ thống. Thao tác thất bại!",
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }
                    })
                    .catch(error => {
                        swal({
                            title: "Thông báo!",
                            text: "Lỗi hệ thống. Thao tác thất bại!",
                            type: "error",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        });
                    })
            },
            setBranchId() {
                if (this.owner_type === 'master') {
                    this.branchId = this.data.saleroom.branch_id;
                }
            }
        },
        mounted() {
            this.getDataSaleRoom();
            this.listDataSearchBoxCity();
            this.listDataSearchBox();
            this.listDataSearchBoxUser();
            //this.setBranchId();
        }
    }
</script>

<style scoped>

</style>
