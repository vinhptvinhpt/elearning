<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.training_effect_report') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h5 class="hk-sec-title">{{trans.get('keys.export_report_excel')}}</h5>
                    <div class="row mb-4">
                        <div class="col-sm">
                            <div class="accordion" id="accordion_1">
                                <div class="card">
                                </div>
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class="" role="button" data-toggle="collapse" href="#collapse_2"
                                           aria-expanded="true"><i class="fal fa-file-excel-o mr-3"></i>{{trans.get('keys.export_report_excel')}}</a>
                                    </div>
                                    <div id="collapse_2" class="collapse show" data-parent="#accordion_1">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label>{{trans.get('keys.chon_nam')}}</label>
                                                            <multiselect
                                                                    v-model="years"
                                                                    :options="filter_year"
                                                                    :searchable="false"
                                                                    :close-on-select="false"
                                                                    placeholder="Select year(s)"
                                                                    :allow-empty="false"
                                                                    :multiple="true"
                                                                    @input="selectYears"
                                                            >
                                                            </multiselect>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <label for="organization_id">{{trans.get('keys.chon_phong_ban')}} </label>
                                                    <treeselect v-model="organization_id" :multiple="false" :options="options" id="organization_id"/>
                                                </div>
                                            </div>
                                            <div class="button-list">
                                                <button type="button" class="btn btn-primary btn-sm hasLoading btn-view" @click="viewTrainingEffect()">
                                                    {{trans.get('keys.xem')}}<i class="fa fa-spinner" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" class="btn btn-primary btn-sm hasLoading btn-export" @click="exportExcel()">
                                                    {{trans.get('keys.export_excel')}}<i class="fa fa-spinner" aria-hidden="true"></i>
                                                </button>
                                            </div>

                                            <div class="table-responsive mt-20">
                                                <table>
                                                    <thead>
                                                    <tr>
                                                        <th>{{trans.get('keys.email')}}</th>
                                                        <th v-for="(year, index) in display_years">{{ year }}</th>
                                                        <th>{{trans.get('keys.hoan_thanh_khoa_hoc')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        <template v-for="(item, competency_code) in list_data">
                                                            <tr>
                                                                <td style="background-color: #5BBFDE;" :colspan="years.length + 2"><strong>{{ item.name }}</strong></td>
                                                            </tr>
                                                            <tr v-for="(data_item, email) in item.users">
                                                                <td>{{ email }}</td>
                                                                <td style="word-wrap:break-word" v-for="mark in data_item.marks">{{ mark }}</td>
                                                                <td>
                                                                    <template v-if="data_item.courses.length > 0" v-for="(course, index) in data_item.courses">
                                                                      {{ course }}<br v-if="index < data_item.courses.length">
                                                                    </template>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect'

    export default {
        data() {
            return {
                years: [],
                organization_id: 0,
                urlImport: '/api/excel/export/user_mark',
                options: [],
                filter_year: [],
                organization_list: [],
                default_options: [
                    {
                        id: 0,
                        label: this.trans.get('keys.chon_to_chuc')
                    }
                ],
                list_data: [],
                display_years: []
            }
        },
        components: {
            Multiselect
        },
        methods: {
            setLayout() {
                this.display_years = this.years;
            },
            setYearInput() {
                let currYear = new Date().getFullYear();
                let startYear = currYear - 5;
                for (let i = startYear; i <= currYear; i++) {
                    this.filter_year.push(i);
                }
                this.years.push(currYear);
            },
            selectYears() {
              this.years.sort();
            },
            exportExcel() {
                let hasLoading = $('button.btn-export');
                let loader = $('.preloader-it');
                loader.fadeIn();
                if (!hasLoading.hasClass('loadding')) {
                    hasLoading.addClass('loadding');
                    this.formData = new FormData();
                    this.formData.append('organization_id', this.organization_id);
                    this.formData.append('years', this.years);
                    this.formData.append('flow', 'export');
                    axios.post(this.urlImport, this.formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(response => {
                            loader.fadeOut();
                            if (response.data) {
                                //Show message
                                toastr[response.data.status](response.data.message, this.trans.get('keys.' + response.data.status));
                                //Download file
                                if (response.data.data.result_file) {
                                    let file_name = response.data.data.result_file;
                                    let a = $("<a>")
                                        .prop("href", "/api/excel/download/" + file_name)
                                        .appendTo("body");
                                    a[0].click();
                                    a.remove();
                                }
                                hasLoading.removeClass('loadding');
                            }
                        })
                        .catch(error => {
                            loader.fadeOut();
                            hasLoading.removeClass('loadding');
                            console.log(error);
                            roam_message('error', this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                }
            },
            viewTrainingEffect() {
                this.setLayout();
                let hasLoading = $('button.btn-view');
                let loader = $('.preloader-it');
                loader.fadeIn();
                if (!hasLoading.hasClass('loadding')) {
                    hasLoading.addClass('loadding');
                    this.formData = new FormData();
                    this.formData.append('organization_id', this.organization_id);
                    this.formData.append('years', this.years);
                    this.formData.append('flow', 'show');
                    axios.post(this.urlImport, this.formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(response => {
                            loader.fadeOut();
                            if (response.data) {
                                this.list_data = response.data.data;
                                hasLoading.removeClass('loadding');
                            }
                        })
                        .catch(error => {
                            loader.fadeOut();
                            hasLoading.removeClass('loadding');
                            console.log(error);
                            roam_message('error', this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                }
            },
            selectOrganization(current_id) {
                axios.post('/organization/list', {
                    keyword: this.organization_keyword,
                    level: 1, // lấy cấp lớn nhất only, vì đã đệ quy
                    paginated: 0 //không phân trang
                })
                    .then(response => {
                        this.organization_list = response.data;
                        //Set options recursive
                        this.options = this.default_options;
                        let new_options = this.setOptions(response.data, current_id);
                        if (new_options.length > 0) {
                            Array.prototype.push.apply(this.default_options, new_options);
                            this.options = this.default_options;
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            setOptions(list, current_id) {
                let outPut = [];
                for (const [key, item] of Object.entries(list)) {
                    let newOption = {
                        id: item.id,
                        label: item.code,
                    };
                    if (item.children.length > 0) {
                        for (const [key, child] of Object.entries(item.children)) {
                            if (child.id === current_id) {
                                newOption.isDefaultExpanded = true;
                                break;
                            }
                        }
                        newOption.children = this.setOptions(item.children, current_id);
                    }
                    outPut.push(newOption);
                }
                return outPut;
            }
        },
        mounted() {
            this.setYearInput();
            this.setLayout();
            this.selectOrganization(this.organization_id);
            this.viewTrainingEffect();
        }
    }
</script>

<style scoped>

</style>
