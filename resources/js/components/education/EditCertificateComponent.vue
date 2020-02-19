<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/certificate/setting">{{ trans.get('keys.danh_sach_chung_chi') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_thong_tin_chung_chi') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="row mx-0">
        <div class="col-12 hk-sec-wrapper">
          <h5 class="hk-sec-title">{{trans.get('keys.chinh_sua_thong_tin_chung_chi')}}</h5>
          <div class="row">
            <div class="col-12 col-lg-3">
              <div class="card">
                <div style="padding: 10px">
                  <img :src="certificate.path" alt="">
                </div>
                <div class="card-body">
                  <p>
                    <input  type="file" ref="file" name="file" class="dropify" />
                  </p>
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-9">
              <form action="" class="form-row hk-sec-wrapper">
                <div class="col-12 form-group">
                  <label for="inputName">{{trans.get('keys.ten_chung_chi')}} </label>
                  <input autocomplete="false" v-model="certificate.name" type="text" id="inputName" :placeholder="trans.get('keys.nhap_id_dung_de_dang_nhap')" class="form-control mb-4" @input="changeRequired('inputName')">
                  <label v-if="!certificate.name" class="required text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>

                <div class="col-12 form-group">
                  <label for="inputDescription">{{trans.get('keys.mo_ta')}} </label>
                  <input autocomplete="false" v-model="certificate.description" type="text" id="inputDescription" :placeholder="trans.get('keys.nhap_id_dung_de_dang_nhap')" class="form-control mb-4" @input="changeRequired('inputDescription')">
                  <label v-if="!certificate.description" class="required text-danger description_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>

                <div class="col-6 form-group">
                  <h6 class="mb-5 d-inline-flex">
                    {{trans.get('keys.chon_lam_chung_chi_mau')}}
                    <span class="inline-checkbox ml-3">
                                    <span class="custom-control custom-checkbox custom-control-inline">
                                        <input v-if="certificate.is_active == 1" class="custom-control-input" :id="'inputCheck'" type="checkbox" v-model="certificate.confirm" checked>
                                        <input v-else type="checkbox" v-model="certificate.confirm"  class="custom-control-input" :id="'inputCheck'">
                                        <label class="custom-control-label" :for="'inputCheck'"></label>
                                    </span>
                                </span>
                  </h6>
                </div>

                <div class="col-12">
                  <div class="button-list">
                    <button type="button" class="btn btn-primary btn-sm" @click="updateCertificate()">{{trans.get('keys.cap_nhat_thong_tin')}}</button>

                    <router-link :to="{name: 'SettingCertificate'}"
                                 class="btn btn-secondary btn-sm">
                      {{trans.get('keys.quay_lai')}}
                    </router-link>

                    <button style="float:right;" @click.prevent="deleteCertificate('/certificate/delete/'+certificate.id)" class="btn btn-sm btn-danger">
                      {{trans.get('keys.xoa')}}
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
    export default {
        props: ['id'],
        data() {
            return {
                certificate: {
                    id: '',
                    name: '',
                    description: '',
                    confirm: false,
                    is_active: 0,
                    path: ''
                },
                language : this.trans.get('keys.language')
            }
        },
        methods:{
            changeRequired(element){
                $('#'+element).removeClass('notValidate');
            },
            deleteCertificate(url) {
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
                            swal({
                                title: "Thông báo!",
                                text: "Xóa thành công!",
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            }, function () {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            swal("Thông báo!", "Lỗi hệ thống. Thao tác thất bại!", "error")
                            console.log(error);
                        });
                });

                return false;
            },
            userData(){
                axios.post('/certificate/detail',{
                    id: this.id
                })
                    .then(response => {
                        this.certificate = response.data;
                        this.certificate.confirm = this.certificate.is_active == 1 ? true : false;
                    })
                    .catch(error => {

                    })
            },
            updateCertificate(){
                console.log("vào r");

                if(!this.certificate.name){
                    $('.name_required').show();
                    return;
                }

                if(!this.certificate.description) {
                    $('.description_required').show();
                    return;
                }
                this.formData = new FormData();
                this.certificate.is_active = this.certificate.confirm == true ? 1 : 0;
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('name', this.certificate.name);
                this.formData.append('is_active', this.certificate.is_active);
                this.formData.append('description', this.certificate.description);
                this.formData.append('id', this.id);

                console.log(this.certificate.is_active);

                axios.post('/certificate/update', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        var language =  this.language;
                        if (response.data.status) {
                            swal(
                                {
                                    title: response.data.message,
                                    // text: response.data.message,
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                },
                                function() {
                                    this.$router.push({ name: 'SettingCertificate' });
                                }
                            );
                        }else{
                            $('.form-control').removeClass('notValidate');
                            $('#'+response.data.id).addClass('notValidate');
                            swal({
                                title: "Thông báo",
                                text: response.data.message,
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }
                    })
                    .catch(error => {
                        swal({
                            title: "Thông báo",
                            text: " Lỗi hệ thống.",
                            type: "error",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        });
                    });
            },
        },
        mounted() {
            this.userData();
        }
    }
</script>

<style scoped>

</style>
