<template>
  <div class="row">
    <div class="col-sm">
      <div class="table-wrap">
        <div class="row mb-3">
          <div class="col-12">
            <div class="col-md-4 col-sm-6 form-group">
              <label for="inputCountry">{{trans.get('keys.quoc_gia')}} *</label>
              <select id="inputCountry" class="form-control custom-select mb-4"
                      v-model="country">
                <option value="">{{trans.get('keys.chon_quoc_gia')}}</option>
                <option v-for="(country_name, country_code, index) in countries"
                        :value="country_code">{{ country_name }}
                </option>
              </select>
              <label v-if="!country" class="required text-danger country_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
            </div>
            <div class="col-md-4 col-sm-6 form-group">
              <label>{{trans.get('keys.country_manager')}} </label>
              <v-select
                :options="userOptions"
                :reduce="userOption => userOption.id"
                :placeholder="this.trans.get('keys.chon_country_manager')"
                :filter-by="myFilterBy"
                v-model="user_id">
              </v-select>
              <label v-if="!user_id" class="required text-danger user_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
            </div>
            <div class="col-md-4 col-sm-6 form-group">
              <button type="button tex-right" class="btn btn-primary btn-sm" @click="createCountryManager()">
                {{trans.get('keys.gan_country_manager')}}
              </button>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4 dataTables_wrapper">
            <div class="dataTables_length"
                 style="display: inline-block;">
              <label>{{trans.get('keys.hien_thi')}}
                <select v-model="row"
                        class="custom-select custom-select-sm form-control form-control-sm"
                        @change="listCountryManager(1)">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </label>
            </div>
          </div>
        </div>
        <div class="mt-10 mb-20">
          <strong>
            {{trans.get('keys.tong_so_nguoi_dung_hien_tai')}} : {{ total }}
          </strong>
        </div>
        <div class="table-responsive">
          <table class="table_res">
            <thead>
            <tr>
              <th>{{trans.get('keys.stt')}}</th>
              <th>{{trans.get('keys.quoc_gia')}}</th>
              <th>{{trans.get('keys.ten_nguoi_dung')}}</th>
              <th>{{trans.get('keys.email')}}</th>
              <th class="text-center" style="min-width: 130px;">
                {{trans.get('keys.hanh_dong')}}
              </th>
            </tr>
            </thead>
            <tbody>
            <template v-if="posts.length === 0">
              <tr>
                <td colspan="4">
                  {{trans.get('keys.khong_tim_thay_du_lieu')}}
                </td>
              </tr>
            </template>
            <template v-else>
              <tr v-for="(user,index) in posts">
                <td>{{ (current-1)*row+(index+1) }}</td>
                <td>{{ user.country }}</td>
                <td>
                  <router-link
                    :to="{ name: 'EditUserById', params: { user_id: user.user_id, type:'system' } }">
                    {{ user.user.fullname }}
                  </router-link>
                </td>
                <td>{{ user.user.email }}</td>
                <td class="text-center">
                  <a href="javascript(0)"
                     @click.prevent="deletePost(user.id)"
                     :title="trans.get('keys.xoa_country_manager')"
                     class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 delete-user">
                    <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                  </a>
                </td>
              </tr>
            </template>
            </tbody>
          </table>
          <v-pagination v-model="current" @input="onPageChange"
                        :page-count="totalPages"
                        :classes=$pagination.classes
                        :labels=$pagination.labels></v-pagination>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: [],
    components: {},
    data() {
      return {
        posts: [],
        keyword: '',
        current: 1,
        totalPages: 0,
        total: 0,
        row: 10,
        country: '',
        user_id: 0,
        userOptions: [],
        countries: [],
      }
    },
    methods: {
      createCountryManager() {
        if (!this.country) {
          $('.username_required').show();
          return;
        }
        if (!this.user_id) {
          $('.email_required').show();
          return;
        }

        this.formData = new FormData();
        this.formData.append('country', this.country);
        this.formData.append('user_id', this.user_id);

        let current_pos = this;

        axios.post('/api/organization/create_country_manager', this.formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
        })
          .then(response => {
            if (response.data.status) {
              if (response.data.status === 'success') {
                toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                this.listCountryManager(1);
              }
            } else {
              toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
            }
          })
          .catch(error => {
            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
          });
      },
      getCountries() {
        axios.post('/system/user/list_country')
          .then(response => {
            this.countries = response.data;
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      myFilterBy: (option, label, search) => {
        if (!label) {
          label = '';
        }
        let new_search = convertUtf8(search);
        let new_label = convertUtf8(label);
        //return this.filterBy(option, new_label, new_search); //can not call components function here
        return (new_label || '').toLowerCase().indexOf(new_search) > -1; // "" not working
      },
      listCountryManager(page) {
        axios.post('/api/organization/list_country_manager', {
          page: page || this.current,
          keyword: this.keyword,
          row: this.row
        })
          .then(response => {
            this.posts = response.data.data ? response.data.data.data : [];
            this.current = response.data.pagination ? response.data.pagination.current_page : 1;
            this.totalPages= response.data.pagination ? response.data.pagination.total_page : 0;
            this.total = response.data ? response.data.total : 0;
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      onPageChange() {
        this.listCountryManager();
      },
      deletePost(id) {
        let current_pos = this;
        swal({
          title: this.trans.get('keys.thong_bao'),
          text: this.trans.get('keys.ban_co_muon_xoa'),
          type: "warning",
          showCancelButton: true,
          closeOnConfirm: true,
          showLoaderOnConfirm: true
        }, function () {
          let loader = $('.preloader-it');
          loader.fadeIn();
          axios.post('/api/organization/delete_country_manager/' + id)
            .then(response => {
              loader.fadeOut();
              toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
              if (current_pos.posts.length === 1) {
                current_pos.current = current_pos.current > 1 ? current_pos.current - 1 : 1;
              }
              //Reset form
              current_pos.user_id = 0;
              current_pos.country = '';
              //Reload list
              current_pos.listCountryManager(current_pos.current);
            })
            .catch(error => {
              loader.fadeOut();
              toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
            });
        });
        return false;
      },
      getUsers() {
        this.userOptions = []; //reset after search again
        axios.post('/system/filter/fetch', {
          type: 'user',
          exclude: this.user_id
        })
          .then(response => {
            let additionalSelections = [];
            response.data.forEach(function (selectItem) {
              let newItem = {
                label: selectItem.label,
                id: selectItem.id
              };
              additionalSelections.push(newItem);
            });
            this.userOptions = additionalSelections;
          })
          .catch(error => {
            console.log(error);
          });
      }
    },
    mounted() {
      this.listCountryManager();
      this.getCountries();
      this.getUsers();
    }
  }
</script>

<style scoped>
</style>
