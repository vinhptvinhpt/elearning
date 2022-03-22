<template>
    <div class="row">
        <div class="col-xl-12">
            <section class="hk-sec-wrapper">
                <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_co_so_vat_chat')}}</h5>
                <div class="row mb-4">
                    <div class="col-sm">
                        <div class="accordion" id="accordion_10">
                            <div class="card" style="border-bottom: 1px solid rgba(0,0,0,.125);">
                                <div class="card-header d-flex justify-content-between">
                                    <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1"
                                       aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.tao_moi_co_so_vat_chat_khoa_hoc')}}</a>
                                </div>
                                <div id="collapse_1" class="collapse" data-parent="#accordion_10" role="tabpanel">
                                    <div class="card-body">
                                        <!-- view thêm mới co so vat chat-->
                                        <div class="row">
                                            <div class="col-12 col-lg-12">
                                                <form action="" class="">
                                                    <div class="form-row">
                                                        <div class="col-sm-6 form-group">
                                                            <label for="inputText1-2">{{trans.get('keys.ten_vat_chat')}}
                                                                *</label>
                                                            <input v-model="infra_name" type="text" id="inputText1-2"
                                                                   :placeholder="trans.get('keys.ten_vat_chat')"
                                                                   class="form-control">
                                                            <label v-if="!infra_name"
                                                                   class="required text-danger infra_name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>
                                                        <div class="col-sm-6 form-group">
                                                            <label for="inputText1-1">{{trans.get('keys.so_luongvc')}}
                                                                *</label>
                                                            <input v-model="infra_number" type="number"
                                                                   id="inputText1-1" min="0"
                                                                   :placeholder="trans.get('keys.so_luongvc')"
                                                                   class="form-control">
                                                            <label v-if="!infra_number"
                                                                   class="required text-danger infra_number_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="col-12 text-right">
                                                            <button @click="createInfras()" type="button"
                                                                    class="btn btn-primary btn-sm">
                                                                {{trans.get('keys.tao')}}
                                                            </button>
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
                <div class="row">
                    <div class="col-sm">
                        <div class="table-wrap">
                            <div class="row">
                                <div class="col-md-6 col-sm-5 dataTables_wrapper">
                                    <div class="dataTables_length" style="display: block;">
                                        <label>{{trans.get('keys.hien_thi')}}
                                            <select v-model="row"
                                                    class="custom-select custom-select-sm form-control form-control-sm"
                                                    @click="getInfras(1)">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-7">
                                    <form v-on:submit.prevent="getInfras(1)">
                                        <div class="d-flex flex-row form-group">
                                            <input v-model="keyword" type="text"
                                                   class="form-control search_text"
                                                   :placeholder="trans.get('keys.tim_kiem_theo_ten_csvc')+' ...'">
                                            <button type="button" id="btnFilter" class="btn btn-primary btn-sm"
                                                    @click="getInfras(1)">
                                                {{trans.get('keys.tim')}}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <br/>
                            <div class="table-responsive">
                                <table id="datable_1" class="table_res">
                                    <thead>
                                    <tr>
                                        <th>{{trans.get('keys.stt')}}</th>
                                        <th>{{trans.get('keys.ten_vat_chat')}}</th>
                                        <th style="width: 30%;">{{trans.get('keys.so_luongvc')}}</th>
                                        <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(course,index) in infras">
                                        <td>{{ (current-1)*row+(index+1) }}</td>
                                        <td>{{ course.infra_name }}</td>
                                        <td>{{ course.infra_number }}</td>
                                        <td class="text-center">
                                            <button type="button" data-toggle="modal"
                                                    class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                    :title="trans.get('keys.sua_thong_tin_khoa_hoc')"
                                                    @click="getDetailInfra(course.id)"
                                                    data-target="#exampleModalCenter">
                                                <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                            </button>

                                            <button :title="trans.get('keys.xoa')" data-toggle="modal"
                                                    data-target="#delete-ph-modal"
                                                    @click="deleteInfra(course.id)"
                                                    class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                                                <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>

                                    </tfoot>
                                </table>

                                <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                              :classes=$pagination.classes
                                              :labels=$pagination.labels></v-pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">

            <!-- Add .modal-dialog-centered to .modal-dialog to vertically center the modal -->
            <div class="modal-dialog modal-dialog-centered" role="document">


                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">{{trans.get('keys.sua_csvc')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <form action="" class="">
                                    <div class="form-row">
                                        <div class="col-sm-6 form-group">
                                            <label for="inputText1-2">{{trans.get('keys.ten_vat_chat')}}
                                                *</label>
                                            <input v-model="infra_data.infra_name" type="text"
                                                   :placeholder="trans.get('keys.ten_vat_chat')"
                                                   class="form-control">
                                            <label v-if="!infra_data.infra_name"
                                                   class="required text-danger dtinfra_name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label for="inputText1-1">{{trans.get('keys.so_luongvc')}}
                                                *</label>
                                            <input v-model="infra_data.infra_number" type="number"
                                                   min="0"
                                                   :placeholder="trans.get('keys.so_luongvc')"
                                                   class="form-control">
                                            <label v-if="!infra_data.infra_number"
                                                   class="required text-danger dtinfra_number_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-12 text-right">
                                            <button @click="updateInfras()" type="button"
                                                    class="btn btn-primary btn-sm">
                                                {{trans.get('keys.sua')}}
                                            </button>
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
</template>

<script>
    export default {
        props: ['course_id'],
        components: {},
        data() {
            return {
                infra_name: '',
                infra_number: '',

                infra_data: {},

                infras: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                total_course: 0,
                row: 10,
                lms_url: ''
            }
        },
        methods: {
            createInfras() {
                if (!this.infra_name) {
                    $('.infra_name_required').show();
                    return;
                }
                if (!this.infra_number) {
                    $('.infra_number_required').show();
                    return;
                }

                // var editor_data = CKEDITOR.instances.article_ckeditor.getData();
                this.formData = new FormData();
                this.formData.append('infra_name', this.infra_name);
                this.formData.append('infra_number', this.infra_number);
                this.formData.append('course_id', this.course_id);

                let current_pos = this;
                axios.post('/api/infrastructer/create', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, this.trans.get('keys.thong_bao'));
                            this.getInfras(this.current);
                            this.infra_name = '';
                            this.infra_number = '';
                        } else {
                            toastr['error'](response.data.message, this.trans.get('keys.thong_bao'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });


            },
            getDetailInfra(id) {
                axios.get('/api/infrastructer/detail/' + id)
                    .then(response => {
                        this.infra_data = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            updateInfras() {
                // if (!this.infra_data.infra_name) {
                //     $('.dtinfra_name_required').show();
                //     return;
                // }
                // if (!this.infra_data.infra_number) {
                //     $('.dtinfra_number_required').show();
                //     return;
                // }

                // var editor_data = CKEDITOR.instances.article_ckeditor.getData();
                this.formData = new FormData();
                this.formData.append('id', this.infra_data.id);
                this.formData.append('infra_name', this.infra_data.infra_name);
                this.formData.append('infra_number', this.infra_data.infra_number);
                this.formData.append('course_id', this.course_id);

                let current_pos = this;
                axios.post('/api/infrastructer/update', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, this.trans.get('keys.thong_bao'));
                            this.getInfras(this.current);
                            this.infra_data = {};
                            $('#exampleModalCenter').modal('hide');
                        } else {
                            toastr['error'](response.data.message, this.trans.get('keys.thong_bao'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });


            },

            getInfras(paged) {
                axios.post('/api/infrastructer/getall', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    course_id: this.course_id
                })
                    .then(response => {
                        this.infras = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                        this.total_course = response.data.total_course;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getInfras();
            },
            deleteInfra(id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.press_ok'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/api/infrastructer/delete', {id: id})
                        .then(response => {
                            if (response.data.status) {
                                toastr['success'](response.data.message, current_pos.trans.get('keys.thong_bao'));
                                current_pos.getInfras(this.current);
                            } else {
                                toastr['error'](response.data.message, current_pos.trans.get('keys.thong_bao'));
                            }
                            swal.close();
                        })
                        .catch(error => {
                            swal.close();
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });

                return false;
            }
        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>
