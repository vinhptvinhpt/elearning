<template>
  <div>
    <div v-if="course_radio == 0">
      <div class="col-12 hk-sec-wrapper">
        <h5 class="hk-sec-title">{{trans.get('keys.diem_khoa_hoc')}}</h5>
        <div class="row">
          <div class="col-sm">
            <div class="table-wrap">
              <div class="row">
                <div class="col-sm-2 dataTables_wrapper">
                  <div class="dataTables_length">
                    <label>{{trans.get('keys.hien_thi')}}
                      <select @change="getGradeByCourse(1)" v-model="row"
                              class="custom-select custom-select-sm form-control form-control-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                      </select>
                    </label>
                  </div>
                </div>
                <div class="col-sm-4">
                  <select v-model="status" @change="getGradeByCourse(1)"
                          class="form-control" id="status">
                    <option value="all">{{trans.get('keys.lms_tat_ca_khoa_hoc')}}</option>
                    <option value="required">{{trans.get('keys.lms_cac_khoa_hoc_bat_buoc')}}</option>
                    <option value="current">{{trans.get('keys.lms_dang_hoc_khoa_hoc')}}</option>
                    <option value="completed">{{trans.get('keys.lms_hoan_thanh_khoa_hoc')}}</option>
                  </select>
                </div>
                <div class="col-sm-6">
                  <form v-on:submit.prevent="getGradeByCourse(1)">
                    <div class="d-flex flex-row form-group">
                      <input v-model="keyword" type="text"
                             class="form-control search_text"
                             :placeholder="trans.get('keys.nhap_khoa_hoc')+ ' ...'">
                      <button type="button" id="btnFilterGrade" class="btn btn-primary btn-sm"
                              @click="getGradeByCourse(1)">
                        {{trans.get('keys.tim')}}
                      </button>
                      <a style="color: #fff" class="btn btn-sm btn-primary"
                         v-on:click="exportExcel(posts)" :title="trans.get('keys.xuat_excel')">
                        <span class="btn-icon-wrap"><i class="fal fa-file-excel-o"></i>&nbsp;{{trans.get('keys.excel')}}</span>
                      </a>
                    </div>
                  </form>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table_res table-hover w-100 pb-30">
                  <thead>
                  <tr>
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>{{trans.get('keys.ma_khoa_hoc')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.ten_khoa_hoc')}}</th>
                    <th class="text-center">{{trans.get('keys.tien_do')}}</th>
                    <!--                    <th class=" mobile_hide">{{trans.get('keys.diem')}}</th>-->
                    <th>{{trans.get('keys.trang_thai')}}</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-if="posts.length == 0">
                    <td colspan="6">{{trans.get('keys.khong_co_du_lieu')}}</td>
                  </tr>
                  <tr v-else v-for="(post,index) in posts">
                    <td>{{ (current-1)*row+(index+1) }}</td>
                    <td>{{ post.shortname }}</td>
                    <td class=" mobile_hide">
                      <label style="cursor: pointer;color: #3a55b1;"
                             @click="getGradeCourseDetail(post.course_id)">
                        {{post.fullname}}
                      </label>
                    </td>

                    <td class="text-center">
                      <label style="display: block;" v-if="parseInt(post.user_course_learn) > 0">
                        {{post.user_course_completionstate}} / {{post.user_course_learn}} (
                        {{Math.round(post.user_course_completionstate *
                        100/post.user_course_learn)+"%"}} )
                      </label>
                      <label style="display: block;" v-else>
                        0 / 0 ( 0% )
                      </label>
                      <!-- <ul class="devcpt_progress_bar" v-if="post.user_course_learn > 0">
                           <li v-for="index in post.user_course_learn"
                               :class='(index <= post.user_course_completionstate) ? ((post.status_user == 1 && post.finalgrade > 0 && post.user_course_completionstate / post.user_course_learn == 1) ? "success" : "warning"):""'
                           >

                           </li>
                       </ul>-->
                    </td>
                    <!--                    <td class=" mobile_hide">-->
                    <!--                      <div v-if="post.finalgrade!=undefined">-->
                    <!--                        {{parseFloat(post.finalgrade).toFixed(2)}}-->
                    <!--                      </div>-->
                    <!--                      <div v-else>-->
                    <!--                        0-->
                    <!--                      </div>-->
                    <!--                    </td>-->
                    <td>
                      <div v-if="
                                            //parseInt(post.status_user) === 1 &&
                                            //checkGradepass(post.finalgrade, post.gradepass)
                                            //&&
                                            parseInt(post.user_course_completionstate) === parseInt(post.user_course_learn)
                                            && parseInt(post.user_course_completionstate) > 0">
                        <span class="badge badge-success">{{trans.get('keys.hoan_thanh')}}</span>
                      </div>
                      <div v-else><span class="badge badge-warning">{{trans.get('keys.chua_hoan_thanh')}}</span>
                      </div>
                      <!--                                            <span v-if="post.status_user == 1 && post.finalgrade >= post.gradepass && post.user_course_completionstate == post.user_course_learn && post.user_course_completionstate > 0"-->
                      <!--                                                  class="badge badge-success">{{trans.get('keys.hoan_thanh')}}</span>-->
                      <!--                                            <span v-else class="badge badge-warning">{{trans.get('keys.chua_hoan_thanh')}}</span>-->
                    </td>
                  </tr>
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>{{trans.get('keys.ma_khoa_hoc')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.ten_khoa_hoc')}}</th>
                    <th class="text-center">{{trans.get('keys.tien_do')}}</th>
                    <!--                    <th class=" mobile_hide">{{trans.get('keys.diem')}}</th>-->
                    <th>{{trans.get('keys.trang_thai')}}</th>
                  </tr>
                  </tfoot>
                </table>
                <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                  <v-pagination v-model="current" @input="onPageChange"
                                :page-count="totalPages"
                                :classes=$pagination.classes
                                :labels=$pagination.labels>
                  </v-pagination>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
    <div v-else>
      <div class="col-12 hk-sec-wrapper">
        <h5 class="hk-sec-title">{{trans.get('keys.diem_chi_tiet_khoa_hoc')}}: {{course_detail.fullname}}</h5>
        <div class="row">
          <div class="col-sm">
            <div class="table-wrap">
              <table id="datable_1" class="table_res table-hover w-100 pb-30">
                <thead>
                <tr>
                  <th>{{trans.get('keys.stt')}}</th>
                  <th>{{trans.get('keys.ten_diem_thanh_phan')}}</th>
                  <th>{{trans.get('keys.diem')}}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="course_grade.length == 0">
                  <td colspan="3">{{trans.get('keys.khong_co_du_lieu')}}</td>
                </tr>
                <tr v-else v-for="(course,index) in course_grade">
                  <td>{{index+1}}</td>
                  <td>{{course.itemname}}</td>
                  <td>{{parseFloat(course.finalgrade).toFixed(2)}}</td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                  <th>{{trans.get('keys.stt')}}</th>
                  <th>{{trans.get('keys.ten_diem_thanh_phan')}}</th>
                  <th>{{trans.get('keys.diem')}}</th>
                </tr>
                </tfoot>
              </table>
              <a style="color:#fff;cursor: pointer;" class="btn btn-secondary btn-sm" @click="backPage()">{{trans.get('keys.quay_lai')}}</a>
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
    props: ['user_id', 'training_name', 'username', 'fullname'],
    //components: {vPagination},
    data() {
      return {
        categorys: {},
        posts: {},
        keyword: '',
        current: 1,
        totalPages: 0,
        category: 3,
        row: 10,
        course_radio: 0,
        course_detail: {},
        course_grade: {},
        trainningUser: [],
        status: "all"
      }
    },
    methods: {
      backPage() {
        this.course_radio = 0;
      },
      getTrainningUser() {
        axios.post('/api/system/get_trainning_user', {
          user_id: this.user_id
        })
          .then(response => {
            this.trainningUser = response.data;
          })
          .catch(error => {
          });
      },
      getGradeCourseDetail(courseid) {
        // this.course_radio = courseid;
        // axios.post('/system/user/course_grade_detail', {
        //   course_id: this.course_radio,
        //   user_id: this.user_id,
        // })
        //   .then(response => {
        //     this.course_detail = response.data.detail;
        //     this.course_grade = response.data.course_grade;
        //   })
        //   .catch(error => {
        //     console.log(error.response.data);
        //   });
      },
      getGradeByCourse(paged) {
        axios.post('/system/user/course_total', {
          page: paged || this.current,
          keyword: this.keyword,
          row: this.row,
          user_id: this.user_id,
          category: this.category,
          status: this.status
        })
          .then(response => {
            this.posts = response.data.data ? response.data.data.data : [];
            this.current = response.data.pagination ? response.data.pagination.current_page : 1;
            this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      getCourseCategory() {
        axios.post('/system/user/course_list')
          .then(response => {
            this.categorys = response.data;
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      onPageChange() {
        this.getGradeByCourse();
      },
      exportExcel(data) {
        axios.post('/api/exportResult', {
          data: data,
          username: this.username,
          fullname: this.fullname
        })
          .then(response => {
            let file_name = response.data;
            let a = $("<a>")
              .prop("href", "/api/downloadExport/" + file_name)
              .appendTo("body");
            a[0].click();
            a.remove();
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          });
      },
      checkGradepass(finalgrade, gradepass) {
        let check = false;
        if (finalgrade == null) { //check both undefined and null
          finalgrade = 0
        } else {
          check = true;
        }
        if (gradepass == null) {
          gradepass = 0
        } else {
          check = true;
        }
        if (gradepass === 0) {
          return true;
        }
        if (check === true) {
          let new_final_grade = parseFloat(finalgrade);
          let new_grade_pass = parseFloat(gradepass);
          if (new_final_grade >= new_grade_pass) {
            return true;
          }
        }
        return false;
      },
    },
    mounted() {
      this.getCourseCategory();
      this.getTrainningUser();
    }
  }
</script>

<style scoped>

</style>
