<template>
  <div class="row" >
    <div class="col-sm">
      <div class="table-wrap">

        <div class="row">
          <div class="col-lg-5 col-md-5 col-sm-12 mb-3">
            <label>
              <h6 class="hk-sec-title">
                {{trans.get('keys.danh_sach_khoa_hoc_mau')}}</h6>
            </label>
            <div class="row">
              <div class="col-12">
                <form v-on:submit.prevent="getListSampleCourse(1)">
                  <div class="d-flex flex-row form-group">
                    <input v-model="keyword" type="text"
                           class="form-control search_text"
                           :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                    <button type="button" id="btnFilter"
                            class="btn btn-primary btn-sm"
                            @click="getListSampleCourse(1)">
                      {{trans.get('keys.tim')}}
                    </button>
                  </div>
                </form>

              </div>
            </div>

            <div class="row">
              <div class="col-6 dataTables_wrapper">
                <div class="dataTables_length"
                     style="display: inline-block;">
                  <label>{{trans.get('keys.hien_thi')}}
                    <select v-model="row"
                            class="custom-select custom-select-sm form-control form-control-sm"
                            @change="getListSampleCourse(1)">
                      <option value="10">10</option>
                      <option value="20">20</option>
                      <option value="50">50</option>
                    </select>
                  </label>
                </div>
              </div>
            </div>
            <br/>

            <div class="table-responsive table-sm">
              <table id="datable_1" class="table_res">
                <thead>
                <tr>
                  <th style="width: 7%;">{{trans.get('keys.stt')}}</th>
                  <th style="width: 40%;">{{trans.get('keys.ma_khoa_hoc')}}</th>
                  <th>
                    {{trans.get('keys.ten_khoa_hoc')}}
                  </th>
                  <th class="text-center">
                    <input type="checkbox" v-model="allSelected"
                           @click="selectAllEnrol()">
                  </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(user,index) in sampleCourses">
                  <td>{{ (current-1)*row+(index+1) }}</td>
                  <td>{{ user.shortname }}</td>
                  <td>{{ user.fullname }}</td>
                  <td class="text-center">
                    <input type="checkbox" :value="user.id"
                           v-model="userEnrols"
                           @change="onCheckboxEnrol()"/>
                  </td>
                </tr>
                </tbody>
                <tfoot>

                </tfoot>
              </table>

              <v-pagination v-model="current" @input="onPageChange"
                            :page-count="totalPages"
                            :classes=$pagination.classes></v-pagination>
            </div>

          </div>
          <div class="col-lg-1 col-md-2 col-sm-12" style="text-align: center;">
            <button :title="trans.get('keys.dua_vao_knl')"
                    data-toggle="modal"
                    style="margin-top: 30px;"
                    data-target="#delete-ph-modal"
                    @click="enrolUserToCourse()"
                    class="btn btn-icon btn-primary btn-icon-style-2 mt-sm-15">
                                                        <span class="btn-icon-wrap"><i
                                                          class="fal fa-arrow-alt-right"></i></span>
            </button>
            <br/><br/>
            <button :title="trans.get('keys.go_khoi_knl')"
                    data-toggle="modal"
                    data-target="#delete-ph-modal"
                    style="margin-bottom: 30px;"
                    @click="removeEnrolUserToCourse()"
                    class="btn btn-icon btn-danger btn-icon-style-2  mb-sm-0">
                                                        <span class="btn-icon-wrap"><i
                                                          class="fal fa-arrow-alt-left"></i></span>
            </button>
          </div>
          <div class="col-lg-6 col-md-5 col-sm-12">
            <label>
              <h6 class="hk-sec-title">
                {{trans.get('keys.danh_sach_khoa_hoc_trong_knl')}}</h6>
            </label>
            <b-card>
              <b-tabs card>
                <b-tab :title="trans.get('keys.danh_sach')" active>
                  <br/>
                  <div class="row">
                    <div class="col-12">
                      <form v-on:submit.prevent="getListCourseTrainning(1)">
                        <div class="d-flex flex-row form-group">
                          <input v-model="keyword_tn" type="text"
                                 class="form-control search_text"
                                 :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                          <button type="button" id="btnFilter1"
                                  class="btn btn-primary btn-sm"
                                  @click="getListCourseTrainning(1)">
                            {{trans.get('keys.tim')}}
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-6 dataTables_wrapper">
                      <div class="dataTables_length">
                        <label>{{trans.get('keys.hien_thi')}}
                          <select v-model="row_tn"
                                  class="custom-select custom-select-sm form-control form-control-sm"
                                  @change="getListCourseTrainning(1)">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="50">50</option>
                          </select>
                        </label>
                      </div>
                    </div>
                  </div>
                  <br/>
                  <div class="table-responsive table-sm">
                    <table id="datable_2" class="table_res">
                      <thead>
                      <tr>
                        <th style="width: 7%;">{{trans.get('keys.stt')}}</th>
                        <th style="width: 40%;"> {{trans.get('keys.ma_khoa_hoc')}}</th>
                        <th>
                          {{trans.get('keys.ten_khoa_hoc')}}
                        </th>
                        <th style="width: 12%;">{{trans.get('keys.hanh_dong')}}</th>
                        <th class="text-center"><input type="checkbox"
                                                       v-model="allSelectedRemove"
                                                       @click="selectAllRemoveEnrol()"
                        ></th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr v-for="(user,index) in trainningCourses">
                        <td>{{ (current_tn-1)*row_tn+(index+1) }}</td>
                        <td>{{ user.shortname }}</td>
                        <td>{{ user.fullname }}</td>
                        <td>
                          <a :title="trans.get('keys.clone_noi_dung')"
                             class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                             :href="lms_url + user.id+'&importid='+user.sample_id"
                          ><span class="btn-icon-wrap"><i
                            class="fal fa-book-open"></i></span></a>
                        </td>
                        <td class="text-center">
                          <input type="checkbox" :value="user.id"
                                 v-model="userRemoveEnrol"
                                 @change="onCheckboxRemoveEnrol()"/>

                        </td>
                      </tr>
                      </tbody>
                      <tfoot>

                      </tfoot>
                    </table>

                    <v-pagination v-model="current_tn" @input="onPageChangeCurr"
                                  :page-count="totalPages_tn"
                                  :classes=$pagination.classes></v-pagination>
                  </div>
                </b-tab>
                <b-tab :title="trans.get('keys.thu_tu')">

                  <template v-if="list.length !== 0">
                    <div class="row text-center action-order">
                      <a @click="sort" title="Reset Order" class="btn btn-sm btn-icon btn-danger btn-icon-style-2" style="color: rgb(255, 255, 255);"><span class="btn-icon-wrap"><i class="fal fa-refresh"></i></span></a>
                      <a @click="saveOrder" title="Save Order" class="btn btn-sm btn-icon btn-primary btn-icon-style-2" style="color: rgb(255, 255, 255);"><span class="btn-icon-wrap"><i class="fal fa-check"></i></span></a>
                    </div>
                    <div class="row">
                      <draggable
                        class="list-group"
                        tag="ul"
                        v-model="list"
                        v-bind="dragOptions"
                        @start="drag = true"
                        @end="drag = false">

                        <transition-group type="transition" :name="!drag ? 'flip-list' : null">
                          <li class="list-group-item" v-for="(element, index)  in list" :key="element.order_no">
                            #{{index + 1}} | <b>{{ element.shortname }}</b> | {{element.fullname}}
                          </li>
                        </transition-group>

                      </draggable>
                    </div>
                    <div class="row text-center action-order">
                      <a @click="sort" title="Reset Order" class="btn btn-sm btn-icon btn-danger btn-icon-style-2" style="color: rgb(255, 255, 255);"><span class="btn-icon-wrap"><i class="fal fa-refresh"></i></span></a>
                      <a @click="saveOrder" title="Save Order" class="btn btn-sm btn-icon btn-primary btn-icon-style-2" style="color: rgb(255, 255, 255);"><span class="btn-icon-wrap"><i class="fal fa-check"></i></span></a>
                    </div>
                  </template>
                  <template v-else>
                    <br/>
                    <p>No courses in competency framework</p>
                  </template>

                </b-tab>
              </b-tabs>
            </b-card>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import draggable from 'vuedraggable'

  export default {
    props: ['id', 'type'],
    components: {
      draggable
    },
    data() {
      return {
        sampleCourses: [],
        keyword: '',
        current: 1,
        totalPages: 0,
        row: 10,

        trainningCourses: [],
        keyword_tn: '',
        current_tn: 1,
        totalPages_tn: 0,
        row_tn: 10,

        userEnrols: [],
        userRemoveEnrol: [],
        allSelected: false,
        allSelectedRemove: false,

        drag: false,
        language: this.trans.get('keys.language'),
        lms_url: '/lms/backup/import.php?id=',
        list: [],
      }
    },
    methods: {
      getListSampleCourse(paged) {
        axios.post('/api/trainning/getlstsamplecourse', {
          page: paged || this.current,
          keyword: this.keyword,
          row: this.row,
          trainning_id: this.id
        })
          .then(response => {
            this.sampleCourses = response.data.data.data;
            this.current = response.data.pagination.current_page;
            this.totalPages = response.data.pagination.total;
          })
          .catch(error => {
            console.log(error);
          });
      },
      getListCourseTrainning(paged) {
        axios.post('/api/trainning/getlstcoursetrainning', {
          page: paged || this.current_tn,
          keyword: this.keyword_tn,
          row: this.row_tn,
          trainning_id: this.id,
        })
          .then(response => {
            this.trainningCourses = response.data.data.data;
            this.list = response.data.allData;
            this.current_tn = response.data.pagination.current_page;
            this.totalPages_tn = response.data.pagination.total;


            console.log()
          })
          .catch(error => {
            console.log(error);
          });
      },
      onPageChange() {
        this.getListSampleCourse();
      },
      onPageChangeCurr() {
        this.getListCourseTrainning();
      },
      enrolUserToCourse() {
        if (this.userEnrols.length === 0) {
          toastr['warning'](this.trans.get('keys.ban_chua_chon_khoa_hoc'), this.trans.get('keys.thong_bao'));
          return;
        }
        let loader = $('.preloader-it');
        loader.fadeIn();
        axios.post('/api/trainning/addcoursetotrainning', {
          lst_course: this.userEnrols,
          trainning_id: this.id
        })
          .then(response => {
            loader.fadeOut();
            if (response.data.status) {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
              $('#btnFilter1').trigger('click');
              this.getListSampleCourse();
            } else {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            loader.fadeOut();
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          });
        this.userEnrols = [];
      },
      removeEnrolUserToCourse() {
        if (this.userRemoveEnrol.length === 0) {
          toastr['warning'](this.trans.get('keys.ban_chua_chon_khoa_hoc'), this.trans.get('keys.thong_bao'));
          return;
        }
        let loader = $('.preloader-it');
        loader.fadeIn();
        axios.post('/api/trainning/removecoursetotrainning', {
          lst_course: this.userRemoveEnrol,
          trainning_id: this.id
        })
          .then(response => {
            loader.fadeOut();
            if (response.data.status) {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
              $('#btnFilter1').trigger('click');
              this.getListSampleCourse();
            } else {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            loader.fadeOut();
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          });
        this.userRemoveEnrol = [];
      },
      sort() {
        //this.list = this.list.sort((a, b) => a.order - b.order);
        this.list.sort(sortArray);
      },
      saveOrder() {
        let cc = this;
        axios.post('/api/trainning/saveorder', {
          training_id: this.id,
          list: this.list
        })
          .then(response => {
            if (response.data.status) {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
            } else {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            }
            //Reload new order, cho vao trong de chay sau khi api goi xong
            cc.getListCourseTrainning();
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
            //Reload new order
            cc.getListCourseTrainning();
          });

      },
      selectAllEnrol: function () {
        this.userEnrols = [];
        this.allSelected = !this.allSelected;
        if (this.allSelected) {
          var countEnrol = this.sampleCourses.length;
          if (countEnrol > 0) {
            for (var i = 0; i < countEnrol; i++) {
              this.userEnrols.push(this.sampleCourses[i].id.toString());
            }
          }
        }
      },
      selectAllRemoveEnrol: function () {
        this.userRemoveEnrol = [];
        this.allSelectedRemove = !this.allSelectedRemove;
        if (this.allSelectedRemove) {
          var countEnrol = this.trainningCourses.length;
          if (countEnrol > 0) {
            for (var i = 0; i < countEnrol; i++) {
              this.userRemoveEnrol.push(this.trainningCourses[i].id.toString());
            }
          }
        }
      },
      onCheckboxEnrol() {
        this.allSelected = false;
      },
      onCheckboxRemoveEnrol() {
        this.allSelectedRemove = false;
      },
    },
    mounted() {

    },
    computed: {
      dragOptions() {
        return {
          animation: 200,
          group: "description",
          disabled: false,
          ghostClass: "ghost"
        };
      }
    }
  }
  function sortArray(a, b) {
    if (a.order_no === b.order_no) {
      return 0;
    }
    else {
      return (a.order_no < b.order_no) ? -1 : 1;
    }
  }
</script>

<style scoped>

</style>
