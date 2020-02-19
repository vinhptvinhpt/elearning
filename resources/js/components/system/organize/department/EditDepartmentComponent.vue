<template>
  <div class="container-fluid mt-15">
  <div class="row">
    <div class="col">
      <nav class="breadcrumb" aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0">
          <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
          <li class="breadcrumb-item">
            <router-link :to="{ name: 'DepartmentIndex' }">
              {{ trans.get('keys.chi_nhanh') }}
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
              <h5 class="mb-20">{{trans.get('keys.sua_chi_nhanh')}}: {{department.name}}</h5>

              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label ><strong>{{trans.get('keys.ten_chi_nhanh')}} *</strong></label>
                    <div class="input-group">
                      <input type="text" id="department_name" class="form-control form-control-line" :placeholder="trans.get('keys.ten_chi_nhanh')+' *'" required v-model="department.name">
                    </div>
                    <label v-if="!department.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label ><strong>{{trans.get('keys.ma_chi_nhanh')}} *</strong></label>
                    <div class="input-group">
                      <input type="text" id="department_code" class="form-control form-control-line" v-model="department.code">
                    </div>
                    <em>{{trans.get('keys.gom_chu_cai_khong_dau_chu_so_ky_tu_dac_biet_-_/_.')}}</em>
                    <label v-if="!department.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label ><strong>{{trans.get('keys.mo_ta')}}</strong></label>
                    <div class="input-group">
                      <input type="text" id="department_des" class="form-control form-control-line" :placeholder="trans.get('keys.mo_ta')" required v-model="department.des">
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label ><strong>{{trans.get('keys.giam_doc_chi_nhanh')}} *</strong></label>
                    <div class="input-group">
                      <div class="wrap_search_box">
                        <div class="btn_search_box" @click="listDataSearchBoxUser()">
                          <span v-if="!department.fullname">{{trans.get('keys.giam_doc_chi_nhanh')}} *</span>
                          <span v-else>{{department.fullname}}</span>
                        </div>
                        <div class="content_search_box">
                          <input @input="listDataSearchBoxUser()" type="text" v-model="search_box_user" class="form-control search_box">
                          <i class="fa fa-spinner" aria-hidden="true"></i>
                          <ul>
                            <li @click="selectSearchBoxUser(0)" >{{trans.get('keys.giam_doc_chi_nhanh')}}</li>
                            <li @click="selectSearchBoxUser(department.manage)" >{{department.fullname}}</li>
                            <li @click="selectSearchBoxUser(item.user_id)" v-for="item in data_search_box_user" :data-value="item.user_id">{{item.fullname}}</li>
                          </ul>
                        </div>
                      </div>
                      <label v-if="!department.manage" class="text-danger manage_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="form-group text-right">
                    <router-link to="/tms/system/organize/department" class="btn btn-secondary btn-sm" style="color: rgb(255, 255, 255);">
                      {{trans.get('keys.quay_lai')}}
                    </router-link>


                    <button type="button" class="btn btn-primary btn-sm" @click="updateDepartment()">{{trans.get('keys.cap_nhat')}}</button>
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
                department: {
                    name: '',
                    code:'',
                    manage:0,
                    des:'',
                    fullname:'',
                },
                data_search_box_user:[],
                search_box_user:'',
            }
        },
        methods: {
            selectSearchBoxUser(input_search_box_id){
                this.department.manage = input_search_box_id;
            },
            listDataSearchBoxUser(){
                $('.content_search_box').addClass('loadding');
                axios.post('/system/organize/department/data_search_box_user',{
                    keyword:this.search_box_user,
                })
                    .then(response => {
                        this.data_search_box_user = response.data;
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                    })
            },
            getDataDepartment(){
                axios.post('/system/organize/department/detail_data/'+this.id)
                    .then(response => {
                        this.department = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
            },
            updateDepartment(){
                if(!this.department.name){
                    $('.name_required').show();
                    return;
                }
                if(!this.department.code){
                    $('.code_required').show();
                    return;
                }
                if(this.department.manage == 0){
                    $('.manage_required').show();
                    return;
                }
                axios.post('/system/organize/department/update',{
                    name:this.department.name,
                    code:this.department.code,
                    manage:this.department.manage,
                    des:this.department.des,
                    id:this.id
                })
                    .then(response => {
                        if(response.data.key) {
                            roam_message('error',response.data.message);
                            $('.form-control').removeClass('error');
                            $('#department_'+response.data.key).addClass('error');
                        }else{
                            roam_message(response.data.status,response.data.message);
                            if(response.data.status == 'success'){
                                $('.form-control').removeClass('error');
                            }
                        }
                    })
                    .catch(error => {
                        roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    })
            }
        },
        mounted() {
            this.getDataDepartment();
        }
    }
</script>

<style scoped>

</style>
