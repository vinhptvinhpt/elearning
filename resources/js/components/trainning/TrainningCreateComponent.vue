<template>
  <div class="row">
    <div class="col-12 col-lg-12">
      <form action="" class="">
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
              <option v-for="item in roles" :value="item.id">{{item.name}}</option>
            </select>
          </div>
          <div class="col-sm-6 form-group">
            <label>{{trans.get('keys.them_co_cau_to_chuc')}}</label>
            <treeselect v-model="organization.parent_id" :multiple="false" :options="tree_options" id="organization_parent_id"/>
          </div>
        </div>

        <div class="form-row">
          <div class="col-sm-6 form-group">
            <label>{{trans.get('keys.khung_nang_luc_theo_tg')}}</label>
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="style" :checked="style==1?true:false" v-model="style">
              <label v-if="style == 1" class="custom-control-label" for="style">{{trans.get('keys.khung_nang_luc_hoan_thanh_trong_khoang_tg')}}</label>
              <label v-else class="custom-control-label" for="style">{{trans.get('keys.khung_nang_luc_khong_gioi_han_tg')}}</label>
            </div>
          </div>
        </div>

        <div class="form-row" v-if="style == 1">
          <div class="col-sm-6 form-group">
            <date-picker v-model="time_start" :config="options"
                         :placeholder="trans.get('keys.ngay_bat_dau')"></date-picker>
            <label v-if="style == 1 && !time_start"
                   class="required text-danger time_start_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
          </div>
          <div class="col-sm-6 form-group">
            <date-picker v-model="time_end" :config="options"
                         :placeholder="trans.get('keys.ngay_ket_thuc')"></date-picker>
          </div>
        </div>

        <div class="form-row">
          <div class="col-sm-6 form-group">
            <label>{{trans.get('keys.tu_dong_chay_cron')}}</label>
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="run_cron" :checked="run_cron==1?true:false" v-model="run_cron">
              <label v-if="run_cron == 1" class="custom-control-label" for="run_cron">Yes</label>
              <label v-else class="custom-control-label" for="run_cron">No</label>
            </div>
          </div>
          <div class="col-sm-6 form-group">
            <label>{{trans.get('keys.tu_dong_cap_chung_chi')}}</label>
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="auto_certificate" :checked="auto_certificate==1?true:false" v-model="auto_certificate">
              <label v-if="auto_certificate == 1" class="custom-control-label" for="auto_certificate">Yes</label>
              <label v-else class="custom-control-label" for="auto_certificate">No</label>
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
      </form>
    </div>


  </div>
</template>

<script>
  import datePicker from 'vue-bootstrap-datetimepicker'
  export default {
    components: {
      datePicker
    },
    data() {
      return {
        code: '',
        name: '',
        style: 0,
        run_cron: 1,
        auto_certificate: 1,
        time_start: '',
        time_end: '',
        date: new Date(),
        organization: {
          name: '',
          code: '',
          parent_id: 0,
          description: '',
        },
        role:0,
        roles:{},
        organization_parent_list:[],

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
        axios.post('/system/user/list_role')
          .then(response => {
            this.roles = response.data;
            this.$nextTick(function(){
              $('.selectpicker').selectpicker('refresh');
            });
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      listOrganization(){
        axios.post('/organization/list', {
          keyword: this.parent_keyword,
          level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
          paginated: 0 //không phân trang,
        })
          .then(response => {
            this.organization_parent_list = response.data;
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

        if (this.style == 1 && this.time_start == 0) {
          $('.time_start_required').show();
          return;
        }

        axios.post('/api/trainning/create', {
          code: this.code,
          name: this.name,
          style: this.style,
          run_cron: this.run_cron,
          auto_certificate: this.auto_certificate,
          time_start: this.time_start,
          time_end: this.time_end,
          role_id: this.role,
          organization_id: this.organization.parent_id
        })
          .then(response => {
            var language = this.language;
            if (response.data.status) {
              swal({
                  title: response.data.message,
                  type: "success",
                  showCancelButton: false,
                  closeOnConfirm: false,
                  showLoaderOnConfirm: true
                }
                , function () {
                  window.location.href = language + '/tms/trainning/detail/' + response.data.otherData;
                }
              );
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
      this.listOrganization();
      this.getRoles();
    }
  }
</script>

<style scoped>

</style>
