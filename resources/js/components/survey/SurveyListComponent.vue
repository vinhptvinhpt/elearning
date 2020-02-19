<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.quan_tri_survey') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <section class="hk-sec-wrapper">
          <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_survey')}}</h5>

          <div class="row">
            <div class="col-sm">
              <div class="table-wrap">
                <div class="row">
                  <div class="col-6">
                    <form v-on:submit.prevent="getSurveys(1)">
                      <div class="d-flex flex-row form-group">
                        <input v-model="keyword" type="text"
                               class="form-control"
                               :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_survey')+' ...'"/>
                        <button type="button" id="btnFilter"
                                class="btn btn-primary"
                                @click="getSurveys(1)">
                          {{trans.get('keys.tim')}}
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="row">
                  <!--                                <div class="col-6">-->
                  <!--                                    <div class="dataTables_length">-->

                  <!--                                    </div>-->
                  <!--                                </div>-->
                  <div class="col-sm-6">
                    <div class="dataTables_length">
                      <label>{{trans.get('keys.ngay_bat_dau')}}</label>
                      <date-picker v-model="startdate" :config="{format: 'YYYY-MM-DD'}"></date-picker>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="dataTables_length">
                      <label>{{trans.get('keys.ngay_ket_thuc')}}</label>
                      <date-picker v-model="enddate" :config="{format: 'YYYY-MM-DD'}"></date-picker>
                    </div>
                  </div>
                </div>
                <div class="row pt-3">
                  <div class="col-6 dataTables_wrapper">
                    <div class="dataTables_length d-block">
                      <label>{{trans.get('keys.hien_thi')}}
                        <select v-model="row"
                                class="custom-select custom-select-sm form-control form-control-sm"
                                @change="getSurveys(1)">
                          <option value="5">5</option>
                          <option value="10">10</option>
                          <option value="20">20</option>
                          <option value="50">50</option>
                        </select>
                      </label>
                    </div>
                  </div>

                  <div class="col-6">
                    <div id="datable_1_filter" class="dataTables_filter" style="float: right;">
                      <label>
                        <router-link to="/tms/survey/create">
                          <button type="button"
                                  class="btn btn-success btn-sm"
                                  :placeholder="trans.get('keys.tao_moi')"
                                  value="Tạo mới"
                                  aria-controls="datable_1">{{trans.get('keys.tao_moi')}}
                          </button>
                        </router-link>
                      </label>
                    </div>
                  </div>
                </div>

                <br/>
                <div class="table-responsive">
                  <table id="datable_1" class="table_res">
                    <thead>
                    <tr>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th style="width: 10%;">{{trans.get('keys.ma_survey')}}</th>
                      <th style="width: 20%;">{{trans.get('keys.ten_survey')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.bat_dau')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.ket_thuc')}}</th>
                      <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(sur,index) in surveys">
                      <td>{{ (current-1)*row+(index+1) }}</td>
                      <td>
                        <router-link :to="{name: 'SurveyStatistic', params: {survey_id: sur.id}}">{{ sur.code }}</router-link>
                      </td>
                      <td>{{ sur.name }}</td>
                      <td class=" mobile_hide">{{ sur.startdate |convertDateTime}}</td>
                      <td class=" mobile_hide">{{ sur.enddate |convertDateTime}}</td>
                      <td class="text-center">

                        <router-link
                           :title="trans.get('keys.giao_dien_trinh_bay_survey')"
                           class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                           :to="{name: 'SurveyPresent', params: {survey_id: sur.id}}">
                          <span class="btn-icon-wrap"><i class="fal fa-arrow-alt-right"></i></span>
                        </router-link>

                        <router-link
                           :title="trans.get('keys.them_cau_hoi_vao_survey')"
                           class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                           :to="{name: 'QuestionCreate', params: {survey_id: sur.id}}">
                        <span class="btn-icon-wrap"><i class="fal fa-question"></i></span></router-link>

                        <router-link
                           :title="trans.get('keys.sua_thong_tin_khao_sat')"
                           class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                           :to="{name: 'SurveyDetail', params: {survey_id: sur.id}}">
                          <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span></router-link>

                        <button :title="trans.get('keys.xoa')" data-toggle="modal"
                                data-target="#delete-ph-modal"
                                @click="deletePost(sur.id)"
                                class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                          <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
                      </td>
                    </tr>
                    </tbody>
                    <tfoot>

                    </tfoot>
                  </table>

                  <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>

</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import datePicker from 'vue-bootstrap-datetimepicker'

    export default {
        components: {
          //vPagination,
          datePicker
        },
        data() {
            return {
                surveys: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 5,
                startdate: '',
                enddate: '',
                date: new Date(),
                options: {
                    format: 'DD/MM/YYYY',
                    useCurrent: false,
                }
            }
        },
        filters: {
            convertDateTime(value) {
                var time = new Date(value * 1000);
                return time.toLocaleDateString();
            }
        },
        methods: {
            getSurveys(paged) {
                axios.post('/api/survey/list', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    startdate: this.startdate,
                    enddate: this.enddate
                })
                    .then(response => {
                        this.surveys = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getSurveys();
            },
            deletePost(id) {
                swal({
                    title: "Bạn muốn xóa mục đã chọn",
                    text: "Chọn 'ok' để thực hiện thao tác.",
                    type: "success",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/api/survey/delete', {survey_id: id})
                        .then(response => {
                            if (response.data.status) {
                                swal({
                                    title: response.data.message,
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }, function () {
                                    location.reload();
                                });
                            } else {
                                swal({
                                    title: response.data.message,
                                    type: "error",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                });
                            }

                        })
                        .catch(error => {
                            swal("Thông báo!", "Lỗi hệ thống. Thao tác thất bại!", "error")
                            console.log(error);
                        });
                });

                return false;
            }
        },
        mounted() {
            this.getSurveys();
        }
    }
</script>

<style scoped>

</style>
