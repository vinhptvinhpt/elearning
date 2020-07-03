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
                            <router-link to="/tms/self/list">{{ trans.get('keys.quan_tri_self') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.thong_ke_self') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h5 class="hk-sec-title">{{trans.get('keys.thong_ke_self')}}</h5>

                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div class="row">
                                    <div class="col-6"></div>
                                    <div class="col-6">
                                        <form v-on:submit.prevent="getSelfStatistic(1)">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword" type="text"
                                                       class="form-control"
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_or_section')+' ...'"/>
                                                <button type="button" id="btnFilter"
                                                        class="btn btn-primary btn-sm"
                                                        @click="getSelfStatistic(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="row pt-3">
                                    <div class="col-6 dataTables_wrapper">
                                        <div class="dataTables_length d-block">
                                            <label>{{trans.get('keys.hien_thi')}}
                                                <select v-model="row"
                                                        class="custom-select custom-select-sm form-control form-control-sm"
                                                        @change="getSelfStatistic(1)">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="20">20</option>
                                                    <option value="50">50</option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <br/>
                                <div class="table-responsive">
                                    <table id="datable_1" class="table_res">
                                        <thead>
                                        <tr>
                                            <th>{{trans.get('keys.stt')}}</th>
                                            <th style="width: 10%;">{{trans.get('keys.username')}}</th>
                                            <th style="width: 20%;">{{trans.get('keys.fullname')}}</th>
                                            <th>{{trans.get('keys.ques_name')}}</th>
                                            <th>{{trans.get('keys.sec_name')}}</th>
                                            <th>{{trans.get('keys.total_point')}}</th>
                                            <th>{{trans.get('keys.avg_point')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(sur,index) in surveys">
                                            <td>{{ (current-1)*row+(index+1) }}</td>
                                            <td>
                                                <router-link
                                                        :to="{ path: 'system/user/edit', name: 'EditUserById', params: { user_id: sur.user_id }, query: {type: 'system'} }">
                                                    {{ sur.username }}
                                                </router-link>
                                            </td>
                                            <td>{{ sur.fullname }}</td>
                                            <td>{{ sur.ques_name }}</td>
                                            <td>{{ sur.section_name }}</td>
                                            <td>{{ sur.total_point }}</td>
                                            <td>{{ sur.avg_point.toFixed(2) }}</td>

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
        </div>
    </div>

</template>

<script>

    export default {
        props: ['self_id'],
        components: {},
        data() {
            return {
                surveys: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 5,
                startdate: '',
                enddate: ''
            }
        },
        methods: {
            getSelfStatistic(paged) {
                axios.post('/api/self/statistic', {
                    self_id: this.self_id,
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                })
                    .then(response => {
                        this.surveys = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getSelfStatistic();
            }
        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>
