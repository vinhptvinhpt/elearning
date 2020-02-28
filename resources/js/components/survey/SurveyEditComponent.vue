<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/survey/list">{{ trans.get('keys.quan_tri_survey') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_thong_tin_survey') }}</li>
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
                 aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.sua_thong_tin_survey')}}</a>
            </div>
            <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 col-lg-12">
                    <form action="" class="form-row">
                      <div class="col-4 form-group">
                        <label for="inputText1-2">{{trans.get('keys.ma_survey')}} *</label>
                        <input v-model="survey.code" type="text" id="inputText1-2"
                               :placeholder="trans.get('keys.nhap_ma')"
                               class="form-control mb-4">
                        <label v-if="!survey.code"
                               class="required text-danger sur_code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-4 form-group">
                        <label for="inputText1-1">{{trans.get('keys.ten_survey')}} *</label>
                        <input v-model="survey.name" type="text" id="inputText1-1"
                               :placeholder="trans.get('keys.nhap_ma')"
                               class="form-control mb-4">
                        <label v-if="!survey.name"
                               class="required text-danger sur_name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>

                      <div class="col-4 form-group">
                        <label for="inputText6">{{trans.get('keys.thoi_gian_bat_dau')}} *</label>
                        <input v-model="survey.startdate"" type="date"
                               id="inputText7"
                               class="form-control mb-4">
                        <label v-if="!survey.startdate"
                               class="required text-danger startdate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-4 form-group">
                        <label for="inputText6">{{trans.get('keys.thoi_gian_ket_thuc')}} *</label>
                        <input v-model="survey.enddate" type="date"
                               id="inputText6"
                               class="form-control mb-4">
                        <label v-if="!survey.enddate"
                               class="required text-danger enddate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>

                      <div class="col-12 form-group">
                        <label for="inputText6">Mô tả</label>
                        <textarea v-model="survey.description" class="form-control" rows="10"
                                  id="article_ckeditor"
                                  :placeholder="trans.get('keys.noi_dung')"></textarea>
                      </div>
                    </form>
                    <div class="button-list">
                      <button @click="editSurvey()" type="button" class="btn btn-primary">{{trans.get('keys.sua')}}
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
        props: ['survey_id'],
        data() {
            return {
                survey: {
                    code: '',
                    name: '',
                    description: '',
                }
            }
        },
        methods: {
            getSurveyDetail() {
                axios.get('/api/survey/detail/' + this.survey_id)
                    .then(response => {
                        this.survey = response.data;

                        var startdate = new Date(response.data.startdate * 1000);

                        var ten = function (i) {
                            return (i < 10 ? '0' : '') + i;
                        };
                        var YYYY = startdate.getFullYear();
                        var MM = ten(startdate.getMonth() + 1);
                        var DD = ten(startdate.getDate());

                        this.survey.startdate = YYYY + '-' + MM + '-' + DD;

                        var endate = new Date(response.data.enddate * 1000);

                        var YYYY_end = endate.getFullYear();
                        var MM_end = ten(endate.getMonth() + 1);
                        var DD_end = ten(endate.getDate());
                        // var HH_end = ten(endate.getHours());
                        // var II_end = ten(endate.getMinutes());

                        this.survey.enddate = YYYY_end + '-' + MM_end + '-' + DD_end;


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

                if (!this.survey.startdate) {
                    $('.startdate_required').show();
                    return;
                }
                if (!this.survey.enddate) {
                    $('.enddate_required').show();
                    return;
                }

                var editor_data = CKEDITOR.instances.article_ckeditor.getData();
                axios.post('/api/survey/edit/' + +this.survey_id, {
                    sur_code: this.survey.code,
                    sur_name: this.survey.name,
                    startdate: this.survey.startdate,
                    enddate: this.survey.enddate,
                    description: editor_data
                }).then(response => {
                    if (response.data.status) {
                        swal({
                                title: response.data.message,
                                // text: response.data.message,
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            }
                            , function () {
                                this.$router.push({ name: 'SurveyIndex' });
                            }
                        );
                    } else {
                        swal({
                            title: response.data.message,
                            // text: response.data.message,
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


            }
        },
        mounted() {
            this.getSurveyDetail();
        }
    }
</script>

<style scoped>

</style>
