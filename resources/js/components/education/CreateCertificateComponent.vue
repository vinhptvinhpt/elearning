<template>
    <div class="row">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <p>
                        <input  type="file" ref="file" name="file" class="dropify" />
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <form action="" class="form-row hk-sec-wrapper">
                <div class="col-12 form-group">
                    <label for="inputName">{{trans.get('keys.ten_chung_chi')}}</label>
                    <input autocomplete="false" v-model="name" type="text" id="inputName" :placeholder="trans.get('keys.nhap_ten_chung_chi')" class="form-control mb-4" @input="changeRequired('inputName')">
                    <label v-if="!name" class="required text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>

                <div class="col-12 form-group">
                    <label for="inputDescription">{{trans.get('keys.mo_ta')}} </label>
                    <input autocomplete="false" v-model="description" type="text" id="inputDescription" :placeholder="trans.get('keys.mo_ta_chung_chi_nay')" class="form-control mb-4" @input="changeRequired('inputDescription')">
                    <label v-if="!description" class="required text-danger description_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>

<!--                <div class="col-6 form-group">-->
<!--                    <h6 class="mb-5 d-inline-flex">-->
<!--                        {{trans.get('keys.chon_lam_chung_chi_mau')}}-->
<!--                        <span class="inline-checkbox ml-3">-->
<!--                            <span class="custom-control custom-checkbox custom-control-inline">-->
<!--                                <input v-model="confirm" type="checkbox" class="custom-control-input" :id="'inputCheck'">-->
<!--                                <label class="custom-control-label" :for="'inputCheck'"></label>-->
<!--                            </span>-->
<!--                        </span>-->
<!--                    </h6>-->
<!--                </div>-->

                <div class="col-12">
                    <div class="button-list">
                        <button @click="createCertificate()" type="button" class="btn btn-primary btn-sm">{{trans.get('keys.tao')}}</button>
                        <button type="button" class="btn btn-secondary btn-sm collapsed closeForm" data-toggle="collapse" href="#collapse_1" aria-expanded="true">{{trans.get('keys.huy')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['type'],
        data() {
            return {
                name: '',
                description: '',
                confirm:false,
                is_active: 0,
            }
        },
        methods: {
            changeRequired(element){
                console.log(element);
                $('#'+element).removeClass('notValidate');
            },
            createCertificate(){
                if(!this.name) {
                    $('.name_required').show();
                    return;
                }

                if(!this.description) {
                    $('.description_required').show();
                    return;
                }

                this.formData = new FormData();
                this.is_active = this.confirm == true ? 1 : 0;
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('name', this.name);
                this.formData.append('is_active', this.is_active);
                this.formData.append('description', this.description);
                axios.post('/certificate/create', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
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
                                    window.location.reload();
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
            setFileInput() {
              $('.dropify').dropify();
            }
        },
        mounted() {
          this.setFileInput();
        }
    }
</script>

<style scoped>

</style>
