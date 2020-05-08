<template>
    <div class="col-12 hk-sec-wrapper" >
        <div>
            <h5 class="hk-sec-title">{{trans.get('keys.thoi_khoa_bieu')}}</h5>
            <div class="row">
                <div class="col-sm">
                    <div class="table-wrap">
                        <div class="row">
                            <div class="col-sm-8 dataTables_wrapper">
                                <div class="dataTables_length" style="display: inline-block;">
                                    <label>{{trans.get('keys.hien_thi')}}
                                        <select @change="getSchedule(1)" v-model="row" class="custom-select custom-select-sm form-control form-control-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="fillterConfirm" style="display: inline-block;">
                                    <label>
                                        <select v-model="status" class="custom-select custom-select-sm form-control form-control-sm" @change="getSchedule(1)">
                                            <option value="0">{{trans.get('keys.trang_thai')}}</option>
                                            <option value="1">{{trans.get('keys.dang_dien_ra')}}</option>
                                            <option value="2">{{trans.get('keys.sap_dien_ra')}}</option>
                                            <option value="3">{{trans.get('keys.da_ket_thuc')}}</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <form v-on:submit.prevent="getSchedule(1)">
                                    <div class="d-flex flex-row form-group">
                                        <input v-model="keyword" type="text"
                                               class="form-control search_text" :placeholder="trans.get('keys.ten_ma_khoa_hoc_hinh_thuc_dao_tao') + ' ...'">
                                        <button type="button" id="btnFilterSchedule" class="btn btn-primary btn-sm"
                                                @click="getSchedule(1)">
                                            {{trans.get('keys.tim')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="container">
                                <p style="color:#333;" class="text-center"><strong>{{trans.get('keys.loc_theo_thoi_gian')}}</strong></p>
                                <div class="box_time_search">
                                    <div>
                                        <input v-model="start_date" type="date" :placeholder="trans.get('keys.ngay_bat_dau')" class="form-control  form-control-sm">
                                    </div>
                                    <div>
                                        <input v-model="end_date" type="date" :placeholder="trans.get('keys.ngay_ket_thuc')" class="form-control  form-control-sm">
                                    </div>
                                    <div>
                                        <button style="height:33px;" type="button" class="btn btn-primary btn-sm"
                                                @click="getSchedule(1)">
                                            {{trans.get('keys.loc')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="datable_1" class="table_res table-hover w-100 pb-30">
                                <thead>
                                <tr>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.ma_khoa_hoc')}}</th>
                                    <th>{{trans.get('keys.ten_khoa_hoc')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.thoi_gian_bat_dau')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.thoi_gian_ket_thuc')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.hinh_thuc_dt')}}</th>
<!--                                    <th class=" mobile_hide">{{trans.get('keys.vai_tro')}}</th>-->
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="posts.length == 0">
                                    <td colspan="7">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                                </tr>
                                <tr v-else v-for="(post,index) in posts">
                                    <td>{{ (current-1)*row+(index+1) }}</td>
                                    <td>{{post.shortname}}</td>
                                    <td>
                                        <a :href="'/lms/course/view.php?id='+post.id">
                                            {{post.course_name}}
                                        </a>
                                    </td>
                                    <td class=" mobile_hide">{{convertDateTime(post.startdate)}}</td>
                                    <td class=" mobile_hide">{{convertDateTime(post.enddate)}}</td>
                                    <td class=" mobile_hide">
                                        <span v-if="post.category == 2 || post.category == 5" class="badge bg-light-10">Offline</span>
                                        <span v-else class="badge badge-primary">Online</span>
                                    </td>
<!--                                    <td class=" mobile_hide">{{post.role_name}}</td>-->
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.ma_khoa_hoc')}}</th>
                                    <th>{{trans.get('keys.ten_khoa_hoc')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.thoi_gian_bat_dau')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.thoi_gian_ket_thuc')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.hinh_thuc_dt')}}</th>
<!--                                    <th class=" mobile_hide">{{trans.get('keys.vai_tro')}}</th>-->
                                </tr>
                                </tfoot>
                            </table>

                           <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                               <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                           </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'

    export default {
        props: ['user_id'],
        //components: {vPagination},
        data() {
            return {
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                row:10,
                status:0,
                start_date:'',
                end_date:'',
            }
        },
        methods: {
            convertDateTime(value) {
                var options = {
                   timeZone:"Asia/Bangkok",
                   hour12 : false,
                   hour:  "2-digit",
                   minute: "2-digit"
                };
                var time = new Date(value * 1000);
                return time.toLocaleDateString('hi',options);
            },
            getSchedule(paged) {
                axios.post('/system/user/user_schedule', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row:this.row,
                    user_id:this.user_id,
                    status:this.status,
                    start_date:this.start_date,
                    end_date:this.end_date
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
                this.getSchedule();
            },
        },
        mounted() {
            //this.getSchedule();
        }
    }
</script>

<style scoped>

</style>
