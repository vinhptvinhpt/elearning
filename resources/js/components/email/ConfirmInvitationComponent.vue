<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col-xl-12">
        <section class="hk-sec-wrapper">
          <h5 class="hk-sec-title">{{trans.get('keys.xac_nhan_loi_moi_tham_gia_khoa_hoc')}}</h5>
          <div class="row">
            <div class="col-12">
              {{ trans.get('keys.ten_khoa_hoc') }}: <b>{{ course_name }}</b><br>
              {{ trans.get('keys.ma_khoa_hoc') }}: <b>{{ course_code }}</b><br>
              {{ trans.get('keys.dia_diem') }}: <b>{{ course_place }}</b><br>
              {{ trans.get('keys.ngay_bat_dau') }}: <b>{{ convertUnixTimeToDate(start_date) }}</b><br>
              {{ trans.get('keys.ngay_ket_thuc') }}: <b>{{ convertUnixTimeToDate(end_date) }}</b>
            </div>
          </div>
          <div class="row mt-20" v-if="replied === 0">
            <div class="col-12">
              <form class="form-row" id="confirm-form">
                <div class="col-12 form-group">
                  <h6 class="d-inline-flex">
                      <span class="inline-checkbox ml-3">
                          <span class="custom-control custom-checkbox custom-control-inline">
                              <input v-model="accepted" type="checkbox" class="custom-control-input chb"
                                     id="checkbox_accepted">
                              <label class="custom-control-label" for="checkbox_accepted"></label>
                          </span>
                      </span>
                    {{ trans.get('keys.tham_gia') }}
                  </h6>
                </div>
                <div class="col-12 form-group">
                  <h6 class="d-inline-flex">
                      <span class="inline-checkbox ml-3">
                          <span class="custom-control custom-checkbox custom-control-inline">
                              <input v-model="rejected" type="checkbox" class="custom-control-input chb"
                                     id="checkbox_rejected">
                              <label class="custom-control-label" for="checkbox_rejected"></label>
                          </span>
                      </span>
                    {{ trans.get('keys.tu_choi') }}
                  </h6>
                </div>
                <div class="col-12 form-group" v-if="rejected !== false">
                  <label><strong>{{ trans.get('keys.ly_do') }}</strong></label>
                  <textarea v-model="reason" class="form-control" rows="3"></textarea>
                  <label v-if="!reason" class="required text-danger reason_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
              </form>
            </div>
            <div class="col-12 button-list">
              <button type="button" class="btn btn-primary" @click="confirmInvitation()">
                {{trans.get('keys.xac_nhan')}}
              </button>
            </div>
          </div>
          <div v-else class="row mt-20">
            <div class="col-12" v-if="!deny">
              {{ trans.get('keys.ban_da_xac_nhan_loi_moi_nay') }}
            </div>
            <div class="col-12" v-else>
              {{ trans.get('keys.ban_da_tu_choi_khoa_hoc_nay') }}
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>


<script>

  import Ls from "../../services/ls";

  export default {
    props: ["invitation_id"],
    data() {
      return {
        course_name: "",
        course_code: "",
        course_place: "",
        start_date: 0,
        end_date: 0,
        accepted: false,
        rejected: false,
        deny: false,
        reason: "",
        replied: 0,
        exist: 0,
        redirect_timeout: 5000,
      };
    },
    methods: {
      getInvitation() {
        axios.get('/api/invitation/detail/' + this.invitation_id)
          .then(response => {
            console.log(response.data.accepted);
            if (response.data.accepted == 0)
              this.deny = true;
            if (response.data.course) {
              this.course_name = response.data.course.fullname;
              this.course_code = response.data.course.shortname;
              this.course_place = response.data.course.course_place;
              this.start_date = response.data.course.startdate;
              this.end_date = response.data.course.enddate;
            }

            if (!jQuery.isEmptyObject(response.data)) {
              this.replied = response.data.replied ? response.data.replied : 0;
              this.exist = 1;
            } else {
              this.redirect_timeout = 2500;
              toastr['error'](this.trans.get('keys.loi_moi_khong_ton_tai'), this.trans.get('keys.thong_bao'));
            }
            let callback_url = Ls.get('callback_url');
            if (this.replied === 1 || this.exist === 0) {
              setTimeout(function () {
                window.location.href = '/sso/authenticate?apiKey=bd629ce2de47436e3a9cdd2673e97b17&callback=' + callback_url;
              }, this.redirect_timeout);
            }
          })
          .catch(error => {
            console.log(error);
          });
      },
      confirmInvitation() {
        if (!this.reason && this.rejected) {
          $('.reason_required').show();
          toastr['error'](this.trans.get('keys.neu_lua_chon_tu_choi_vui_long_nhap_ly_do'), this.trans.get('keys.that_bai'));
          return;
        }

        if (!this.accepted && !this.rejected) {
          toastr['error'](this.trans.get('keys.ban_phai_lua_chon_tham_gia_hoac_tu_choi'), this.trans.get('keys.that_bai'));
          return;
        }

        this.formData = new FormData();
        this.formData.append('id', this.invitation_id);
        this.formData.append('accepted', this.accepted);
        this.formData.append('reason', this.reason);
        axios.post('/api/invitation/confirm', this.formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
        })
          .then(response => {
            var language = this.language;
            if (response.data.status === 'success') {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
              this.redirect_timeout = 2500;
              this.getInvitation();
              // setTimeout(function () {
              //     window.location.href = '/sso/authenticate?apiKey=bd629ce2de47436e3a9cdd2673e97b17';
              // }, 5000);
            } else {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          });

      },
      convertUnixTimeToDate(timestamp) {
        if (timestamp !== 0) {
          let date = new Date(timestamp * 1000);
          return date.toLocaleString();
        } else {
          return 'N/A';
        }
      }
    },
    mounted() {
      this.getInvitation();
      let check_rejected = this;
      $(".chb").change(function () {
        $(".chb").not(this).prop('checked', false);
        if (this.id === "checkbox_accepted") {
          check_rejected.rejected = false;
        } else {
          check_rejected.accepted = false;
        }
      });
    }
  };

</script>

<style scoped>
</style>
