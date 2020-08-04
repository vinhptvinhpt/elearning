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
                            <router-link to="/tms/survey/list">{{ trans.get('keys.quan_tri_self') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_thong_tin_self') }}</li>
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
                               aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.chinh_sua_thong_tin_self')}}</a>
                        </div>
                        <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <form action="" class="form-row">
                                            <div class="col-4 form-group">
                                                <label for="inputText1-2">{{trans.get('keys.ma_self')}} *</label>
                                                <input v-model="survey.code" type="text" id="inputText1-2"
                                                       :placeholder="trans.get('keys.nhap_ma')"
                                                       class="form-control mb-4">
                                                <label v-if="!survey.code"
                                                       class="required text-danger sur_code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <div class="col-4 form-group">
                                                <label for="inputText1-1">{{trans.get('keys.ten_self')}} *</label>
                                                <input v-model="survey.name" type="text" id="inputText1-1"
                                                       :placeholder="trans.get('keys.nhap_ten')"
                                                       class="form-control mb-4">
                                                <label v-if="!survey.name"
                                                       class="required text-danger sur_name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-12 form-group">
                                                <label for="inputText1-1">{{trans.get('keys.mo_ta')}}</label>
                                                <ckeditor v-model="survey.description"
                                                          :config="editorConfig"></ckeditor>
                                            </div>
                                        </form>
                                        <div class="button-list">
                                            <button @click="editSurvey()" type="button" class="btn btn-primary btn-sm">
                                                {{trans.get('keys.sua')}}
                                            </button>

                                            <router-link :to="{name:'SelfIndex',params:{back_page: '1'}}" class="btn btn-secondary btn-sm">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>

<script>
    export default {
        props: ['self_id'],
        data() {
            return {
                survey: {
                    code: '',
                    name: '',
                    description: ''
                },
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
            getSurveyDetail() {
                axios.get('/api/self/getbyid/' + this.self_id)
                    .then(response => {
                        this.survey = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            editSurvey() {
                if (!this.survey.code) {
                    $('.sur_code_required').show();
                    return;
                }

                if (!this.survey.name) {
                    $('.sur_name_required').show();
                    return;
                }
                let current_pos = this;

                axios.post('/api/self/update/' + +this.self_id, {
                    sur_code: this.survey.code,
                    sur_name: this.survey.name,
                    description: this.survey.description
                }).then(response => {
                    if (response.data.status) {
                        toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                        this.$router.push({name: 'SelfIndex'});
                    } else {
                        toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                    }
                })
                    .catch(error => {
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });


            }
        },
        mounted() {
            this.getSurveyDetail();
        }
    }
</script>

<style scoped>

</style>
