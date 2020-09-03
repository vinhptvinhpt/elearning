<template>
  <div class="row">
    <div class="col-12 col-lg-12">
      <form action="" class="">
        <div class="row">
          <div class="col-12 col-lg-3 d-none">
            <div class="card">
              <div class="card-body">
                <p>
                  <input type="file" ref="file" name="file" class="dropify"/>
                </p>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="form-row">
              <div class="col-sm-6 form-group">
                <label for="inputText1-2">{{trans.get('keys.ma_knl')}} *</label>
                <input v-model="code" type="text" id="inputText1-2"
                       :placeholder="trans.get('keys.ma_knl')"
                       class="form-control">
                <label v-if="!code"
                       class="required text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
              </div>
              <div class="col-sm-6 form-group">
                <label for="inputText1-1">{{trans.get('keys.ten_knl')}} *</label>
                <input v-model="name" type="text" id="inputText1-1"
                       :placeholder="trans.get('keys.ten_knl')"
                       class="form-control">
                <label v-if="!name"
                       class="required text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
              </div>
            </div>

            <div class="form-row">
              <div class="col-sm-6 form-group">
                <label for="inputRole">{{trans.get('keys.quyen')}}</label>
                <select v-model="role" class="form-control selectpicker" id="inputRole" autocomplete="false">
                  <option value="0">{{trans.get('keys.chon_vai_tro')}}</option>
                  <option v-for="role in roles" :value="role.id">{{ trans.has('keys.' + role.name) ? trans.get('keys.' +
                    role.name) : role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                  </option>
                </select>
              </div>
              <div class="col-sm-6 form-group">
                <label>{{trans.get('keys.them_co_cau_to_chuc')}}</label>
                <treeselect v-model="organization.parent_id" :multiple="false" :options="tree_options"
                            id="organization_parent_id"/>
              </div>
            </div>
            <div class="form-row">
              <div class="col-sm-12 form-group">
                <label for="inputDescription">{{trans.get('keys.mo_ta')}}</label>
                <textarea class="form-control" rows="3" v-model="description"
                          id="inputDescription"
                          :placeholder="trans.get('keys.noi_dung')"></textarea>
              </div>
            </div>

            <div class="form-row" :style="type == 1 ? '' : 'display: none;'">
              <div class="col-12">
                <label for="inputText1-1">{{trans.get('keys.thoi_gian_hoan_thanh')}}</label>
              </div>
              <div class="col-sm-6 form-group">
                <date-picker v-model="time_start" :config="options"
                             :placeholder="trans.get('keys.ngay_bat_dau')+ ' *'"></date-picker>
                <label v-if="type == 1 && !time_start"
                       class="required text-danger time_start_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
              </div>
              <div class="col-sm-6 form-group">
                <date-picker v-model="time_end" :config="options"
                             :placeholder="trans.get('keys.ngay_ket_thuc')"></date-picker>
              </div>
<!--              <div class="col-sm-6 form-group">-->
<!--                <p id="logic-warning" class="text-danger code_error hide">-->
<!--                  {{trans.get('keys.vui_long_nhap_ngay_bat_dau_nho_hon_hoac_bang_ngay_ket_thuc')}}</p>-->
<!--              </div>-->
            </div>

            <div class="form-row">
              <div class="col-sm-4 form-group">
                <label>{{trans.get('keys.tu_dong_chay_cron')}}</label>
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="run_cron" :checked="run_cron==1?true:false"
                         v-model="run_cron">
                  <label v-if="run_cron == 1" class="custom-control-label" for="run_cron">Yes</label>
                  <label v-else class="custom-control-label" for="run_cron">No</label>
                </div>
              </div>
              <div class="col-sm-4 form-group">
                <label>{{trans.get('keys.tu_dong_cap_chung_chi')}}</label>
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="auto_certificate"
                         :checked="auto_certificate==1?true:false" v-model="auto_certificate">
                  <label v-if="auto_certificate == 1" class="custom-control-label" for="auto_certificate">Yes</label>
                  <label v-else class="custom-control-label" for="auto_certificate">No</label>
                </div>
              </div>
              <div class="col-sm-4 form-group">
                <label>{{trans.get('keys.tu_dong_cap_huy_hieu')}}</label>
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="auto_badge"
                         :checked="auto_badge==1?true:false" v-model="auto_badge">
                  <label v-if="auto_badge == 1" class="custom-control-label" for="auto_badge">Yes</label>
                  <label v-else class="custom-control-label" for="auto_badge">No</label>
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="col-12 text-right">
                <button @click="createTrainning()" type="button"
                        class="btn btn-primary btn-sm">
                  {{trans.get('keys.tao')}}
                </button>
              </div>
            </div>
          </div>
        </div>

      </form>
    </div>
  </div>
</template>

<script>
  import datePicker from 'vue-bootstrap-datetimepicker'

  export default {
    props: ['type'],
    components: {
      datePicker
    },
    data() {
      return {
        code: '',
        name: '',
        logo: '',
        run_cron: 1,
        auto_certificate: 1,
        auto_badge: 1,
        time_start: '',
        time_end: '',
        description: '',
        date: new Date(),
        organization: {
          name: '',
          code: '',
          parent_id: 0,
          description: '',
        },
        role: 0,
        roles: {},

        language: this.trans.get('keys.language'),
        options: {
          format: 'DD-MM-YYYY',
          useCurrent: false,
          showClear: true,
          showClose: true,
        },
        //Treeselect options
        tree_options: [
          {
            id: 0,
            label: this.trans.get('keys.chon_to_chuc')
          }
        ]
      }
    },
    methods: {
      getRoles() {
        axios.post('/system/user/list_role', {
          type: 'role'
        })
          .then(response => {
            this.roles = response.data;
            this.$nextTick(function () {
              $('.selectpicker').selectpicker('refresh');
            });
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      listOrganization() {
        axios.post('/organization/list', {
          keyword: this.parent_keyword,
          level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
          paginated: 0 //không phân trang,
        })
          .then(response => {
            //Set options recursive
            this.tree_options = this.setOptions(response.data);
          })
          .catch(error => {

          })
      },
      setOptions(list) {
        let outPut = [];
        for (const [key, item] of Object.entries(list)) {
          let newOption = {
            id: item.id,
            label: item.name
          };
          if (item.children.length > 0) {
            newOption.children = this.setOptions(item.children);
          }
          outPut.push(newOption);
        }
        return outPut;
      },
      createTrainning() {

        if (!this.code) {
          $('.code_required').show();
          return;
        }

        if (!this.name) {
          $('.name_required').show();
          return;
        }

        if (this.type == 1 && this.time_start == '') {
          $('.time_start_required').show();
          return;
        }
        if(this.type == 1){
          // $('#logic-warning').hide();
          let has_startdate = false;
          let has_enddate = false;
          if(this.time_start !== null && this.time_start !== undefined){
            has_startdate = true;
          }
          if(this.time_end !== null && this.time_end !== undefined){
            has_enddate = true;
          }
          if (has_startdate && has_enddate) {
            let startDate_stamp = Date.parse(new Date(this.time_start.split("-").reverse().join("-")));
            let endDate_stamp = Date.parse(new Date(this.time_end.split("-").reverse().join("-")));

            if (startDate_stamp > endDate_stamp) {
              // $('#logic-warning').show();
              toastr['error'](this.trans.get('keys.vui_long_nhap_ngay_bat_dau_nho_hon_hoac_bang_ngay_ket_thuc'), this.trans.get('keys.thong_bao'));
              return;
            }
            // else {
            //   $('#logic-warning').hide();
            // }
          }
        }

        this.formData = new FormData();
        this.formData.append('code', this.code);
        this.formData.append('name', this.name);
        this.formData.append('style', this.type);
        var auto_certificate = this.auto_certificate ? 1 : 0;
        this.formData.append('auto_certificate', auto_certificate);
        var auto_badge = this.auto_badge ? 1 : 0;
        this.formData.append('auto_badge', auto_badge);
        var run_cron = this.run_cron ? 1 : 0;
        this.formData.append('run_cron', run_cron);
        this.formData.append('time_start', this.time_start);
        this.formData.append('time_end', this.time_end);
        this.formData.append('description', this.description ? this.description : '');
        this.formData.append('role_id', this.role ? this.role : 0);
        this.formData.append('organization_id', this.organization.parent_id ? this.organization.parent_id : 0);
        this.formData.append('file', this.$refs.file.files[0]);

        axios.post('/api/trainning/create', this.formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
        })
          .then(response => {
            var language = this.language;
            if (response.data.status) {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
              this.$router.push({name: 'TrainningEdit', params: {id: response.data.otherData}});
            } else {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          });

      },

      setFileInput() {
        $('.dropify').dropify();
      }
    },
    mounted() {
      this.listOrganization();
      this.getRoles();
    },
    updated() {
      this.setFileInput();
    }
  }
</script>

<style scoped>

</style>
