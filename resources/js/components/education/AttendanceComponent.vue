<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
            </li>
            <li v-if="come_from === 'online'" class="breadcrumb-item">
              <router-link :to="{ name: 'CourseIndex' }">
                {{ trans.get('keys.khoa_dao_tao_online') }}
              </router-link>
            </li>
            <li v-else-if="come_from === 'offline'" class="breadcrumb-item">
              <router-link :to="{ name: 'CourseConcentrateIndex' }">
                {{ trans.get('keys.khoa_dao_tao_tap_trung') }}
              </router-link>
            </li>
            <li v-if="select_course_id" class="breadcrumb-item">
              <router-link :to="{ name: 'CourseDetail', params: {id: select_course_id} }">
                {{ course_name }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.diem_danh') }}</li>
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
                 aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.diem_danh')}}</a>
            </div>
            <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
              <div class="card-body">
                <div class="row">
                  <div class="col-xl-12">
                    <section class="hk-sec-wrapper">
                      <div class="row">
                        <div class="col-sm">
                          <div class="table-wrap">

                            <div class="row">
                              <div class="col-sm-8 dataTables_wrapper">
                                <div class="dataTables_length" style="display: inline-block;">
                                  <label>{{trans.get('keys.hien_thi')}}
                                    <select v-model="row"
                                            class="custom-select custom-select-sm form-control form-control-sm"
                                            @change="getUsers(1)">
                                      <option value="10">10</option>
                                      <option value="25">25</option>
                                      <option value="50">50</option>
                                      <option value="100">100</option>
                                    </select>
                                  </label>
                                </div>
                              </div>
                              <div class="col-sm-4">
                                <form v-on:submit.prevent="getUser(1)">
                                  <div class="d-flex flex-row form-group">
                                    <input v-model="date" type="text"
                                           class="form-control search_text"
                                           :placeholder="trans.get('keys.ngay_diem_danh') + ' ...'">
                                    <button type="button" id="btnFilter"
                                            class="btn btn-primary btn-sm btn_fillter"
                                            @click="getUsers(1)">
                                      {{trans.get('keys.xem')}}
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>

                            <div class="table-responsive">
                              <table class="table_res">
                                <thead>
                                <tr>
                                  <th>{{trans.get('keys.tai_khoan')}}</th>
                                  <th>{{trans.get('keys.ho_va_ten')}}</th>
                                  <th>{{trans.get('keys.diem_danh')}}</th>
                                  <th>{{trans.get('keys.ly_do')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(user,index) in users">
                                  <td>
                                    <router-link
                                      :to="{name: 'EditUserById', params: { user_id: user.id }, query: {type: 'attendance'} }">
                                      {{ user.username }}
                                    </router-link>
                                  </td>
                                  <td>{{ user.fullname }}</td>
                                  <td>
                                    <input v-model="user.present" type="text" :id="'inputText_'+index" class="form-control">
                                  </td>
                                  <td>
                                    <textarea v-model="user.note" :id="'textarea'+index" class="form-control" rows="3"></textarea>
                                  </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                  <th>{{trans.get('keys.tai_khoan')}}</th>
                                  <th>{{trans.get('keys.ho_va_ten')}}</th>
                                  <th>{{trans.get('keys.diem_danh')}}</th>
                                  <th>{{trans.get('keys.ly_do')}}</th>
                                </tr>
                                </tfoot>
                              </table>
                              <div :style="users.length == 0 ? 'display:none;' : 'display:block;'">
                                <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                              :classes=$pagination.classes
                                              :labels=$pagination.labels></v-pagination>
                              </div>
                              <div class="text-left">
                                <button :title="trans.get('keys.luu')" type="button"
                                        class="btn btn-sm btn-success mt-3" @click="storeAttendance()">
                                  {{trans.get('keys.luu')}}
                                </button>
                              </div>
                            </div>

                          </div>
                        </div>
                      </div>
                    </section>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xl-12">
              <section class="hk-sec-wrapper">
                <div class="row">
                  <div class="col-3">
                    <button class="btn btn-danger btn-sm" @click="goBack()">
                      {{trans.get('keys.quay_lai')}}
                    </button>
                  </div>
                </div>
              </section>
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
    props: {
      course_id: {
        type: Number,
        default: ''
      },
      come_from: String,
      course_name: {
        type: String,
        default: 'Course Name'
      },
    },
    //components: {vPagination},
    data() {
      return {
        course_name: '',
        select_course_id: this.course_id,
        users: [],

        date: '',
        current: 1,
        totalPages: 0,
        row: 5,

        //Treeselect options
        options: [
          {
            id: 0,
            label: this.trans.get('keys.khoa_hoc')
          }
        ]
      }
    },
    methods: {
      getUsers(paged) {
        axios.post('/api/course/attendance_list', {
          page: paged || this.current,
          row: this.row,
          course_id: this.course_id,
          date: this.date
        })
          .then(response => {
            this.users = response.data.data.data;
            this.current = response.data.pagination.current_page;
            this.totalPages = response.data.pagination.total;
            this.uncheckEnrolAll();
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      onPageChange() {
        this.getUsers();
      },
      storeAttendance() {
        let current_pos = this;
        axios.post('/api/course/storeAttendance', {
          users: this.userEnrols,
          user: this.user,
          course_id: this.course_id
        })
          .then(response => {
            if (response.data.status) {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
              current_pos.getCurrentUserEnrol(current_pos.current_page);
              current_pos.getUserNeedEnrol(current_pos.current_page);
            } else {
              toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
          });
      },
      goBack() {
        if (this.come_from === 'online') {
          this.$router.push({name: 'CourseIndex'});
        } else {
          this.$router.push({name: 'CourseConcentrateIndex'});
        }
      },

    },
    mounted() {
    }
  }

</script>

<style scoped>

</style>
