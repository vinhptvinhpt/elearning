<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item">
              <router-link :to="{ name: 'CityIndex'}">
                {{ trans.get('keys.tinh_thanh') }}
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
                <h6 class="mb-20">{{trans.get('keys.tinh')}} : {{data.city.name}}</h6>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.ma')}} *</strong></label>
                      <div class="input-group">
                        <input id="city_code" type="text" class="form-control form-control-line" v-model="data.city.code">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.ten')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" id="city_name" class="form-control form-control-line" :placeholder="trans.get('keys.ten')+' *'" required v-model="data.city.name">
                      </div>
                      <label v-if="!data.city.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.chi_nhanh')}}</strong></label>
                      <div class="input-group">
                        <select class="form-control form-control-line" v-model="data.city.department">
                          <option value="0">{{trans.get('keys.chi_nhanh')}}</option>
                          <option v-for="item in department_list" :value="item.id">{{item.name}}</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label ><strong>{{trans.get('keys.khu_vuc')}} *</strong></label>
                      <div class="input-group">
                        <select class="form-control form-control-line" v-model="data.city.district" required>
                          <option value="">-- {{trans.get('keys.khu_vuc')}} * --</option>
                          <option value="MB">{{trans.get('keys.mien_bac')}}</option>
                          <option value="MT">{{trans.get('keys.mien_trung')}}</option>
                          <option value="MN">{{trans.get('keys.mien_nam')}}</option>
                        </select>
                      </div>
                      <label v-if="!data.city.district" class="text-danger district_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>
                </div>
                <!-- <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label ><strong>Mã *</strong></label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-line" placeholder="Mã *" required v-model.code="data.city.code">
                            </div>
                            <label v-if="!data.city.code" class="text-danger code_required hide">Trường bắt buộc phải nhập.</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label ><strong>Người quản lý</strong></label>
                            <div class="input-group">
                                <select class="form-control form-control-line" v-model.user_id="data.city.user_id">
                                    <option value="">-- Người quản lý --</option>
                                    <option v-for="user in data.user" :value="user.user_id">{{user.fullname}}</option>
                                </select>
                            </div>
                            <label v-if="!data.city.user_id" class="text-danger user_required hide">Trường bắt buộc phải nhập.</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label ><strong>Mô tả *</strong></label>
                            <div class="input-group">
                                <textarea placeholder="Mô tả" class="form-control form-control-line" v-model.description="data.city.description"></textarea>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="row">
                  <div class="col-12">
                    <div class="form-group text-right">
                      <button type="button" class="btn btn-primary btn-sm" @click="updateCity()">{{trans.get('keys.cap_nhat')}}</button>
                      <router-link to="/tms/system/organize/city" class="btn btn-secondary btn-sm">
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
        props: ['id'],
        //components: {vPagination},
        data() {
            return {
                data:{
                    city:{
                        name:'',
                        code:'',
                        user_id:0,
                        district:'',
                        description:'',
                        department: 0
                    }
                },
                department_list:[]
            }
        },
        methods: {
            getDepartment() {
                axios.post('/system/organize/city/get_department_list')
                    .then(response => {
                        this.department_list = response.data ? response.data : [];
                    })
                    .catch(error => {
                        this.department_list = [];
                    });
            },
            getDataCity(){
                axios.post('/system/organize/city/detail_data/'+this.id)
                    .then(response => {
                        this.data = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
            },
            updateCity(){
                if(!this.data.city.name){
                    $('.name_required').show();
                    return;
                }
                if(!this.data.city.code){
                    $('.code_required').show();
                    return;
                }
                if(!this.data.city.district){
                    $('.district_required').show();
                    return;
                }
                axios.post('/system/organize/city/update/'+this.id,{
                    name:this.data.city.name,
                    code:this.data.city.code,
                    district:this.data.city.district,
                    department:this.data.city.department
                })
                    .then(response => {
                        if(response.data.key) {
                            roam_message('error',response.data.message);
                            $('.form-control').removeClass('error');
                            $('#city_'+response.data.key).addClass('error');
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
            this.getDataCity();
            this.getDepartment();
        }
    }
</script>

<style scoped>

</style>
