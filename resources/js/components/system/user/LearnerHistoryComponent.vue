<template>
    <div>
        <div class="row">
            <div class="col-6 form-group">
                <h5 class="hk-sec-title">{{trans.get('keys.lich_su_hoc_tap')}}</h5>
                <select v-model="trainning_id" @change="getLearnerHistory(1)"
                        class="form-control" id="trainning_id">
                    <option value="">{{trans.get('keys.khung_nang_luc')}}</option>
                    <option v-for="trr in trainningUser" :value="trr.id">
                        {{trr.trainning_name}}
                    </option>
                </select>
            </div>
        </div>
        <div class="col-12 hk-sec-wrapper">
            <h5 class="hk-sec-title">{{trans.get('keys.lich_su_khoa_hoc')}}</h5>
            <div class="row">
                <div class="col-sm">
                    <div class="table-wrap">
                        <div class="row">
                            <div class="col-sm-6 dataTables_wrapper">
                                <div class="dataTables_length">
                                    <label>{{trans.get('keys.hien_thi')}}
                                        <select @change="getLearnerHistory(1)" v-model="row"
                                                class="custom-select custom-select-sm form-control form-control-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <form v-on:submit.prevent="getLearnerHistory(1)">
                                    <div class="d-flex flex-row form-group">
                                        <input v-model="keyword" type="text"
                                               class="form-control search_text"
                                               :placeholder="trans.get('keys.nhap_khoa_hoc')+ ' ...'">
                                        <button type="button" id="btnFilterGrade" class="btn btn-primary btn-sm"
                                                @click="getLearnerHistory(1)">
                                            {{trans.get('keys.tim')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table_res table-hover w-100 pb-30">
                                <thead>
                                <tr>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.ma_khoa_hoc')}}</th>
                                    <th class="mobile_hide">{{trans.get('keys.ten_khoa_hoc')}}</th>
                                    <th class="mobile_hide">{{trans.get('keys.khung_nang_luc')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="posts.length == 0">
                                    <td colspan="6">{{trans.get('keys.khong_co_du_lieu')}}</td>
                                </tr>
                                <tr v-else v-for="(post,index) in posts">
                                    <td>{{ (current-1)*row+(index+1) }}</td>
                                    <td>{{ post.course_code }}</td>
                                    <td class="mobile_hide">
                                        {{post.course_name}}
                                    </td>

                                    <td class="mobile_hide">
                                        {{post.trainning_name}}
                                    </td>

                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.ma_khoa_hoc')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.ten_khoa_hoc')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.khung_nang_luc')}}</th>
                                </tr>
                                </tfoot>
                            </table>
                            <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                                <v-pagination v-model="current" @input="onPageChange"
                                              :page-count="totalPages"
                                              :classes=$pagination.classes
                                              :labels=$pagination.labels>
                                </v-pagination>
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
        props: ['user_id'],
        data() {
            return {
                posts: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 10,
                trainning_id: '',
                trainningUser: []
            }
        }, 
        methods: {
            getTrainningUser() {
                axios.get('/api/system/get_trainning_history/' + this.user_id)
                    .then(response => {
                        this.trainningUser = response.data;
                    })
                    .catch(error => {
                    });
            },
            getLearnerHistory(paged) {
                axios.post('/api/system/get_learner_history', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    user_id: this.user_id,
                    trainning_id: this.trainning_id
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getLearnerHistory();
            }
        },
        mounted() {
            this.getTrainningUser();
        }
    }
</script>

<style scoped>

</style>
