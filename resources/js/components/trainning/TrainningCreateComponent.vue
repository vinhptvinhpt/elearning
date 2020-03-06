<template>
    <div class="row">
        <div class="col-12 col-lg-12">
            <form action="" class="form-row">
                <div class="col-sm-6 form-group">
                    <label for="inputText1-2">{{trans.get('keys.ma_knl')}} *</label>
                    <input v-model="code" type="text" id="inputText1-2"
                           :placeholder="trans.get('keys.ma_knl')"
                           class="form-control mb-4">
                    <label v-if="!code"
                           class="required text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-sm-6 form-group">
                    <label for="inputText1-1">{{trans.get('keys.ten_knl')}} *</label>
                    <input v-model="name" type="text" id="inputText1-1"
                           :placeholder="trans.get('keys.ten_knl')"
                           class="form-control mb-4">
                    <label v-if="!name"
                           class="required text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-12 text-right">
                    <button @click="createTrainning()" type="button"
                            class="btn btn-primary btn-sm">
                        {{trans.get('keys.tao')}}
                    </button>
                </div>
            </form>
        </div>


    </div>
</template>

<script>
    export default {
        data() {
            return {
                code: '',
                name: '',

                language: this.trans.get('keys.language')
            }
        },
        methods: {
            createTrainning() {

                if (!this.code) {
                    $('.code_required').show();
                    return;
                }

                if (!this.name) {
                    $('.name_required').show();
                    return;
                }

                axios.post('/api/trainning/create', {
                    code: this.code,
                    name: this.name
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
                                    window.location.href = language + '/trainning/detail/' + response.data.otherData;
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

        }
    }
</script>

<style scoped>

</style>
