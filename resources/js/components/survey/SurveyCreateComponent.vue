<template>

    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/tms/survey/list">{{ trans.get('keys.quan_tri_survey') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('tao_moi_survey') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm">
                <div class="accordion" id="accordion_1">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <a role="button" data-toggle="collapse" href="#collapse_1"
                               aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.tao_moi_survey')}}</a>
                        </div>
                        <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <form action="" class="form-row">
                                            <div class="col-4 form-group">
                                                <label for="inputText1-2">{{trans.get('keys.ma_survey')}} *</label>
                                                <input v-model="sur_code" type="text" id="inputText1-2"
                                                       :placeholder="trans.get('keys.nhap_ma')"
                                                       class="form-control mb-4">
                                                <label v-if="!sur_code"
                                                       class="required text-danger sur_code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <div class="col-4 form-group">
                                                <label for="inputText1-1">{{trans.get('keys.ten_survey')}} *</label>
                                                <input v-model="sur_name" type="text" id="inputText1-1"
                                                       :placeholder="trans.get('keys.nhap_ma')"
                                                       class="form-control mb-4">
                                                <label v-if="!sur_name"
                                                       class="required text-danger sur_name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-4 form-group">
                                                <label for="inputText6">{{trans.get('keys.thoi_gian_bat_dau')}}
                                                    *</label>
                                                <input v-model="startdate" type="date"
                                                       id="inputText7"
                                                       class="form-control mb-4">
                                                <label v-if="!startdate"
                                                       class="required text-danger startdate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <div class="col-4 form-group">
                                                <label for="inputText6">{{trans.get('keys.thoi_gian_ket_thuc')}}
                                                    *</label>
                                                <input v-model="enddate" type="date"
                                                       id="inputText6"
                                                       class="form-control mb-4">
                                                <label v-if="!enddate"
                                                       class="required text-danger enddate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-12 form-group">
                                                <label for="inputText6">{{trans.get('keys.mo_ta')}}</label>
                                                <!--                                                <textarea v-model="description" class="form-control" rows="10"-->
                                                <!--                                                          id="article_ckeditor"-->
                                                <!--                                                          :placeholder="trans.get('keys.noi_dung')"></textarea>-->
                                                <ckeditor v-model="description"
                                                          :config="editorConfig"></ckeditor>
                                            </div>
                                        </form>
                                        <div class="button-list">
                                            <button @click="createSurvey()" type="button" class="btn btn-primary">
                                                {{trans.get('keys.tao')}}
                                            </button>
                                            <router-link to="/tms/survey/list" class="btn btn-secondary">
                                                {{trans.get('keys.huy')}}
                                            </router-link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <!--                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_2"-->
                            <!--                           aria-expanded="false"><i class="fal fa-upload mr-3"></i>Tải lên file Excel</a>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        data() {
            return {
                sur_code: '',
                sur_name: '',
                startdate: '',
                enddate: '',
                description: '',

                editorConfig: {
                    filebrowserUploadMethod: 'form', //fix for response when uppload file is cause filetools-response-error
                    // The configuration of the editor.
                    //add responseType=json for original version of ckeditor 4, else cause filetools-response-error
                    filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                    filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
                    filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content')
                }
            }
        },
        methods: {
            createSurvey() {

                if (!this.sur_code) {
                    $('.sur_code_required').show();
                    return;
                }

                if (!this.sur_name) {
                    $('.sur_name_required').show();
                    return;
                }

                if (!this.startdate) {
                    $('.startdate_required').show();
                    return;
                }
                if (!this.enddate) {
                    $('.enddate_required').show();
                    return;
                }


              //  var editor_data = CKEDITOR.instances.article_ckeditor.getData();

                axios.post('/api/survey/create', {
                    sur_code: this.sur_code,
                    sur_name: this.sur_name,
                    startdate: this.startdate,
                    enddate: this.enddate,
                    description: this.description
                })
                    .then(response => {
                            if (response.data.status) {
                                toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                                this.$router.push({
                                    name: 'QuestionCreate',
                                    params: {survey_id: response.data.otherData}
                                });
                            } else {
                                toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                            }
                        }
                    )
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


            }

        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>
