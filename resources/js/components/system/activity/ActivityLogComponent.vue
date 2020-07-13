<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">
                {{ trans.get('keys.dashboard') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.thong_tin_log_he_thong') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div>
          <div class="accordion" id="accordion_1">
            <div class="card">
              <div class="card-body">
                <h5 class="mb-20">{{trans.get('keys.danh_sach_log_he_thong')}}</h5>
                <div class="row">
                  <div class="col-md-8 dataTables_wrapper">
                    <div class="dataTables_length" style="display: inline-block;">
                      <label>{{trans.get('keys.hien_thi')}}
                        <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getActivity(1)">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select>
                      </label>
                    </div>
                    <div class="fillterConfirm" style="display: inline-block;">
                      <label>
                        <select v-model="type" class="custom-select custom-select-sm form-control form-control-sm" @change="getActivity(1)">
                          <option value="user">{{trans.get('keys.nguoi_dung')}}</option>
                          <option value="organize">{{trans.get('keys.co_cau_to_chuc')}}</option>
                          <option value="education">{{trans.get('keys.khoa_dao_tao')}}</option>
                          <option value="role">{{trans.get('keys.quyen')}}</option>
                          <option value="survey">{{trans.get('keys.khao_sat')}}</option>
                          <option value="system">{{trans.get('keys.he_thong')}}</option>
                          <option value="notification">{{trans.get('keys.thong_bao')}}</option>
                        </select>
                      </label>
                    </div>
                    <div class="fillterConfirm" style="display: inline-block;">
                      <label>
                        <select v-model="action" class="custom-select custom-select-sm form-control form-control-sm" @change="getActivity(1)">
                          <option value="">Action</option>
                          <option value="create">Create</option>
                          <option value="update">Update</option>
                          <option value="delete">Delete</option>
                          <option value="restore">Restore</option>
                          <option value="add">Add</option>
                          <option value="remove">Remove</option>
                          <option value="clear">Delete Recycle Bin</option>
                        </select>
                      </label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="d-flex flex-row form-group">
                      <input v-model="keyword" type="text"
                             class="form-control search_text" :placeholder="trans.get('keys.nhap_tu_khoa') + ' ...'">
                      <button type="button" id="btnFilter" class="btn btn-primary btn-sm"
                              @click="getActivity(1)">
                        {{trans.get('keys.tim')}}
                      </button>
                    </div>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table_res">
                    <thead>
                    <tr>
                      <th>STT</th>
                      <th>Activity Log</th>
                      <th>Action</th>
                      <th>User</th>
                      <th class=" mobile_hide">Url</th>
                      <th class=" mobile_hide">Info</th>
                      <th class=" mobile_hide">Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="posts.length == 0">
                      <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                    </tr>
                    <tr v-else v-for="(post,index) in posts">
                      <td>{{ (current-1)*row+(index+1) }}</td>
                      <td v-switch="post.type">
                        <span v-case="'education'">{{trans.get('keys.khoa_dao_tao')}}</span>
                        <span v-case="'organize'">{{trans.get('keys.co_cau_to_chuc')}}</span>
                        <span v-case="'user'">{{trans.get('keys.nguoi_dung')}}</span>
                        <span v-case="'role'">{{trans.get('keys.quyen')}}</span>
                        <span v-case="'survey'">{{trans.get('keys.khao_sat')}}</span>
                        <span v-case="'system'">{{trans.get('keys.he_thong')}}</span>
                        <span v-case="'notification'">{{trans.get('keys.thong_bao')}}</span>
                      </td>
                      <td>{{ post.action }}</td>

                      <td>
                        <router-link v-if="post.user"
                          :to="{ name: 'EditUserById', params: { user_id: post.user.id } }">
                          {{ post.user.username }}
                        </router-link>
                      </td>

                      <td  class=" mobile_hide">
                        <a v-if="type === 'education'" :href="lms_url + post.course_id">{{ 'lms/course/view.php?id=' + post.course_id }}</a>
                        <a v-else :href="post.url">{{ post.url }}</a>
                      </td>
                      <template class=" mobile_hide">
                        <td v-if="type === 'education'">{{translateAction(post.action, post.target) + ' ' + post.course_name}}</td>
                        <td v-else v-html="post.info"></td>
                      </template>
                      <td class=" mobile_hide">{{ post.created_at }}</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                      <th>STT</th>
                      <th>Activity Log</th>
                      <th>Action</th>
                      <th>User</th>
                      <th class=" mobile_hide">Url</th>
                      <th class=" mobile_hide">Info</th>
                      <th class=" mobile_hide">Time</th>
                    </tr>
                    </tfoot>
                  </table>
                  <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
    //import VSwitch from 'v-switch-case'
    //Vue.use(VSwitch);
    //import vPagination from 'vue-plain-pagination'
    export default {
        //components: {vPagination},
        data() {
            return{
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                row:10,
                type:'education',
                action: '',
                lms_url: ''
            }
        },
        methods:{
            translateAction(action, target) {
                let translated_action = '';
                let translated_target = '';
                switch (action) {
                    case 'created':
                        translated_action = this.trans.get('keys.them_moi');
                        break;
                    case 'updated':
                        translated_action = this.trans.get('keys.cap_nhat');
                        break;
                    case 'deleted':
                        translated_action = this.trans.get('keys.xoa');
                        break;
                    case 'viewed':
                        translated_action = this.trans.get('keys.xem');
                        break;
                    case 'restored':
                        translated_action = this.trans.get('keys.khoi_phuc');
                        break;
                    default:
                        translated_action = action;
                }
                switch (target) {
                    case 'course':
                        translated_target = this.trans.get('keys.khoa_hoc')+ ':';
                        break;
                    case 'quiz':
                        translated_target = this.trans.get('keys.bai_kiem_tra') + ':';
                        break;
                    default:
                        translated_target = target;
                }
                return translated_action + ' ' + translated_target;
            },
            getActivity(paged) {
                axios.post('/activity_log', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row:this.row,
                    type:this.type,
                    action:this.action
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total : 1;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            onPageChange() {
                this.getActivity();
            },
            fetch() {
              axios.post('/bridge/fetch', {
                type: this.type,
                view: 'ActivityLog'
              })
                .then(response => {
                  this.lms_url = response.data.lms_url;
                })
                .catch(error => {
                  console.log(error);
                })
            },
        },
        mounted() {
            // this.translateAction();
          this.fetch();
        }
    }
</script>

<style scoped>

</style>
