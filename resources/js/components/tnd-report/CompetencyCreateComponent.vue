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
                            <div class="col-sm-12 form-group">
                                <label for="inputDescription">{{trans.get('keys.mo_ta')}}</label>
                                <textarea class="form-control" rows="3" v-model="description"
                                          id="inputDescription"
                                          :placeholder="trans.get('keys.noi_dung')"></textarea>
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

    export default {
        props: [],
        components: {},
        data() {
            return {
                code: '',
                name: '',
                description: ''
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

                this.formData = new FormData();
                this.formData.append('code', this.code);
                this.formData.append('name', this.name);
                this.formData.append('description', this.description ? this.description : '');
                let current_pos = this;
                axios.post('/api/tnd-report/store', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            this.$router.push({name: 'CompetencyEditComponent', params: {id: response.data.otherData}});
                        } else {
                            toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
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

        },
        updated() {
            this.setFileInput();
        }
    }
</script>

<style scoped>

</style>
