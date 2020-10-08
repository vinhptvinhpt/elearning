<template>

    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link
                                    :to="{ path: '/tms/competency/list',
                                  name: 'CompetencyListComponent'}">
                                {{ trans.get('keys.khung_nang_luc_effect') }}
                            </router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_thong_tin_knl') }}</li>
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
                               aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.sua_thong_tin_knl')}}</a>
                        </div>
                        <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
                            <div class="card-body">

                                <div v-if="slug_can('tms-system-administrator-grant')" class="row">
                                    <div class="col-12 col-lg-12">
                                        <form action="">
                                            <div class="row">
                                                <div class="col-12 col-lg-3 d-none">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <input type="file" ref="file" name="file" class="dropify"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-row">
                                                        <div class="col-sm-6 form-group">
                                                            <label for="inputText1-2">{{trans.get('keys.ma_knl')}}
                                                                *</label>
                                                            <input v-model="trainning.code" type="text"
                                                                   id="inputText1-2"
                                                                   :placeholder="trans.get('keys.ma_knl')"
                                                                   class="form-control">
                                                            <label v-if="!trainning.code"
                                                                   class="required text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>
                                                        <div class="col-sm-6 form-group">
                                                            <label for="inputText1-1">{{trans.get('keys.ten_knl')}}
                                                                *</label>
                                                            <input v-model="trainning.name" type="text"
                                                                   id="inputText1-1"
                                                                   :placeholder="trans.get('keys.ten_knl')"
                                                                   class="form-control">
                                                            <label v-if="!trainning.name"
                                                                   class="required text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>
                                                    </div>


                                                    <div class="form-row">
                                                        <div class="col-sm-12 form-group">
                                                            <label for="inputDescription">{{trans.get('keys.mo_ta')}}</label>
                                                            <textarea class="form-control" rows="3"
                                                                      v-model="trainning.description"
                                                                      id="inputDescription"
                                                                      :placeholder="trans.get('keys.noi_dung')"></textarea>
                                                        </div>
                                                    </div>


                                                    <div class="form-row">
                                                        <div class="col-12 form-group">
                                                            <div class="button-list text-right">
                                                                <button @click="editTrainning()" type="button"
                                                                        class="btn btn-primary btn-sm">
                                                                    {{trans.get('keys.sua')}}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </form>
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


    export default {
        props: ['id', 'slugs'],
        components: {},
        data() {
            return {
                trainning: {
                    code: '',
                    name: '',
                    description: ''
                }
            }
        },
        methods: {
            slug_can(permissionName) {
                return this.slugs.indexOf(permissionName) !== -1;
            },

            getDetailTrainning() {
                axios.get('/api/tnd-report/detail/' + this.id)
                    .then(response => {
                        this.trainning = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            editTrainning() {

                if (!this.trainning.code) {
                    $('.code_required').show();
                    return;
                }

                if (!this.trainning.name) {
                    $('.name_required').show();
                    return;
                }


                this.formData = new FormData();
                this.formData.append('id', this.id);
                this.formData.append('code', this.trainning.code);
                this.formData.append('name', this.trainning.name);
                this.formData.append('description', this.trainning.description ? this.trainning.description : '');

                let current_pos = this;

                axios.post('/api/tnd-report/update', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
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
            this.getDetailTrainning();
        },
        updated() {
        }
    }
</script>

<style scoped>
    .flip-list-move {
        transition: transform 0.5s;
    }

    .no-move {
        transition: transform 0s;
    }

    .ghost {
        opacity: 0.5;
        background: #c8ebfb;
    }

    .list-group {
        width: 100%;
        min-height: 20px;
    }

    .list-group-item {
        cursor: move;
        border-top-width: 1px;
    }

    .list-group-item i {
        cursor: pointer;
    }

    .action-order {
        display: block;
        margin: 20px 0;
    }

    .selectpicker {
        display: block !important;
    }
</style>
