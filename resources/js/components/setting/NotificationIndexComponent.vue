
<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">
              {{ trans.get('keys.dashboard') }}
              </router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.gui_thong_bao') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <section class="hk-sec-wrapper">
          <h5 class="hk-sec-title">{{trans.get('keys.gui_thong_bao')}}</h5>
          <p class="mb-3">{{trans.get('keys.gui_thong_bao_den_user_qua_ung_dung_di_dong')}}</p>
          <div class="row mb-4">
            <div class="col-12">
              <form class="form-row" id="setting-form">
                <div class="col-12 form-group hide">
                  <label><strong>{{trans.get('keys.nguoi_nhan')}}</strong></label>
                  <div class="input-group">
                    <div class="wrap_search_box">
                      <div class="btn_search_box">
                        <span>{{trans.get('keys.gui_tat_ca')}}</span>
                      </div>
                      <div class="content_search_box">
                        <input @input="listDataSearchBoxUser()" type="text" v-model="search_box_user" class="form-control search_box">
                        <i class="fa fa-spinner" aria-hidden="true"></i>
                        <ul>
                          <li @click="selectSearchBoxUser(0)">{{trans.get('keys.gui_tat_ca')}}</li>
                          <li v-if="data_search_box_user.length === 0">{{trans.get('keys.khong_tim_thay_du_lieu')}}</li>
                          <li @click="selectSearchBoxUser(item.id)" v-else v-for="item in data_search_box_user" :data-value="item.id">{{item.lastname}} {{item.firstname}}</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 form-group">
                  <label for="message_content"><strong>{{trans.get('keys.noi_dung')}}</strong></label>
                  <input v-model="message.content" type="text" id="message_content" class="form-control">
                  <label v-if="!message.content" class="text-danger content_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
              </form>
            </div>
            <div class="col-12 button-list">
              <button type="button" class="btn btn-primary" @click="sendNotification()">{{trans.get('keys.gui')}}</button>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>


</template>

<script>
    export default {
        data() {
            return {
                data_search_box_user:[],
                message: {
                    'user_id' : '',
                    'content' : ''
                },
                search_box_user: '',
            }
        },
        methods: {
            selectSearchBoxUser(input_search_box_id){
                this.message.user_id = input_search_box_id;
            },
            listDataSearchBoxUser(){
                $('.content_search_box').addClass('loading');
                axios.post('/notification/list_user',{
                    keyword:this.search_box_user,
                })
                    .then(response => {
                        this.data_search_box_user = response.data;
                        $('.content_search_box').removeClass('loading');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loading');
                        console.log(error.response.data);
                    })
            },
            sendNotification(){
                let current_pos = this;
                if(!this.message.content){
                    $('.content_required').show();
                    return;
                }
                axios.post('/notification/send', this.message , {})
                    .then(response => {
                        if(response.data === 'success') {
                            swal({
                                title: current_pos.trans.get('keys.thong_bao'),
                                text: current_pos.trans.get('keys.gui_thanh_cong'),
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }else if(response.data === 'fail'){
                            swal({
                                title: current_pos.trans.get('keys.thong_bao'),
                                text: current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'),
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }else if(response.data.length !== 0){
                            $('.' + response.data).show();
                        }
                    })
                    .catch(error => {
                        swal({
                            title: current_pos.trans.get('keys.thong_bao'),
                            text: current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'),
                            type: "error",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        });
                    })
            }
        },
        mounted() {
            this.listDataSearchBoxUser();
        }
    }
</script>

<style scoped>

</style>
