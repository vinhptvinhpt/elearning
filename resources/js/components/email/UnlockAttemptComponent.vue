<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h5 class="hk-sec-title">{{trans.get('keys.xac_nhan_cap_luot_lam_bai_kiem_tra')}}</h5>
                    <div class="row">
                        <div class="col-12">
                            {{ trans.get('keys.ten_khoa_hoc') }}: <b>{{ course_name }}</b><br>
                            {{ trans.get('keys.ma_khoa_hoc') }}: <b>{{ course_code }}</b><br>
                            {{ trans.get('keys.dia_diem') }}: <b>{{ course_place ? course_place : 'N/A' }}</b><br>
                            {{ trans.get('keys.ngay_bat_dau') }}: <b>{{ convertUnixTimeToDate(start_date) }}</b><br>
                            {{ trans.get('keys.ngay_ket_thuc') }}: <b>{{ convertUnixTimeToDate(end_date) }}</b><br>
                            {{ trans.get('keys.bai_kiem_tra') }}: <b>{{ quiz_name }}</b><br>

                            <template v-if="unlocked === 0">
                              <p>The following learner has not passed a final test with a 100% pass rate and will need to make another attempt.</p>
                              <p>Learner: <b>{{ this.student_name}}</b></p>
                              <p>Do you want to unlock the test to allow them another attempt?</p>
                            </template>

                        </div>
                        <div v-if="unlocked === 0" class="col-12 button-list">
                            <button type="button" class="btn btn-primary" @click="confirmUnlock()">
                                {{trans.get('keys.xac_nhan')}}
                            </button>
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
        props: ["notification_id"],
        data() {
            return {
                course_name: "",
                course_code: "",
                course_place: "",
                start_date: 0,
                end_date: 0,
                student_name: "",
                quiz_name: "",
                quiz_id: 0,
                unlocked: 1,


                accepted: false,
                rejected: false,
                reason: "",
                replied: 0,
                exist: 0,
                redirect_timeout: 5000,
            };
        },
        methods: {
            getNotificationDetail() {
                axios.get('/api/notification/attempt/detail/' + this.notification_id)
                    .then(response => {
                        if (response.data) {
                            this.course_name = response.data.fullname;
                            this.course_code = response.data.shortname;
                            this.course_place = response.data.course_place;
                            this.start_date = response.data.startdate;
                            this.end_date = response.data.enddate;
                            this.student_name = response.data.student_name;
                            this.quiz_name = response.data.quiz_name;
                            this.quiz_id = response.data.quiz_id;
                            this.unlocked = response.data.unlocked;
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            confirmUnlock() {
                let loader = $('.preloader-it');
                loader.fadeIn();
                this.formData = new FormData();
                this.formData.append('notification_id', this.notification_id);
                axios.post('/api/unlock/confirm', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        loader.fadeOut();
                        if (response.data.status === 'success') {
                            toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                            this.getNotificationDetail();
                        } else {
                            toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                        }
                    })
                    .catch(error => {
                        loader.fadeOut();
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
            this.getNotificationDetail();
        }
    };

</script>

<style scoped>
</style>
