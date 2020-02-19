<template>
    <div>
        <div class="row">
            <div class="col-sm-8 dataTables_wrapper">
                <div class="dataTables_length" style="display: inline-block;">
                    <label>{{trans.get('keys.hien_thi')}}
                        <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getCityAddFromDepartment(1)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </label>
                </div>
            </div>
            <div class="col-sm-4">
                <form v-on:submit.prevent="getCityAddFromDepartment(1)">
                    <div class="d-flex flex-row form-group">
                        <input  v-model="keyword" type="text"
                                class="form-control search_text" :placeholder="trans.get('keys.ten_ma_tinh_thanh')+' ...'">
                        <button type="button" class="btn btn-primary btn-sm btn_fillter"
                                @click="getCityAddFromDepartment(1)">
                            {{trans.get('keys.tim')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="mt-10 mb-20">
            <strong>
                {{trans.get('keys.tong_so_tinh_thanh')}} : {{totalRow}}
            </strong>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table_res">
                        <thead>
                        <th>
                            <input v-model="allSelected" @click="selectAllCheckbox()" id="city-select-all" type="checkbox" class="filled-in chk-col-light-blue" name="select_all" value=""/>
                            <label for="city-select-all"></label>
                        </th>
                        <th>{{trans.get('keys.ma_tinh_thanh')}}</th>
                        <th>{{trans.get('keys.ten_tinh_thanh')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.khu_vuc')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.dai_ly')}}</th>
                        <th>{{trans.get('keys.hanh_dong')}}</th>
                        </thead>
                        <tbody>
                        <tr v-if="posts.length == 0">
                            <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                        </tr>
                        <tr v-else v-for="(item,index) in posts">
                            <td>
                                <input v-model="city_add" :value="item.city_id" type="checkbox" :id="'city_add'+item.city_id" class="filled-in chk-col-light-blue">
                                <label :for="'city_add'+item.city_id"></label>
                            </td>
                            <td>{{ item.code }}</td>
                            <td>{{ item.name }}</td>
                            <td class=" mobile_hide">
                                <span v-if="item.district == 'MB'">{{trans.get('keys.mien_bac')}}</span>
                                <span v-else-if="item.district == 'MT'">{{trans.get('keys.mien_trung')}}</span>
                                <span v-else-if="item.district == 'MN'">{{trans.get('keys.mien_nam')}}</span>
                                <span v-else></span>
                            </td>
                            <td class=" mobile_hide">
                                <a :href="trans.get('keys.language')+'/system/organize/city/branch/'+item.city_id" :title="trans.get('keys.xem_danh_sach_dai_ly')">
                                    {{item.branch_count}} ( <span class="text-underline">{{trans.get('keys.dai_ly')}}</span> )
                                </a>
                            </td>
                            <td>
                                <a :href="trans.get('keys.language')+'/system/organize/city/edit/'+item.city_id" :title="trans.get('keys.sua_tinh_thanh')"
                                   class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2">
                                    <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <th>
                            <input v-model="allSelected" @click="selectAllCheckbox()" id="city-select-all1" type="checkbox" class="filled-in chk-col-light-blue" name="select_all" value=""/>
                            <label for="city-select-all1"></label>
                        </th>
                        <th>{{trans.get('keys.ma_tinh_thanh')}}</th>
                        <th>{{trans.get('keys.ten_tinh_thanh')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.khu_vuc')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.dai_ly')}}</th>
                        <th>{{trans.get('keys.hanh_dong')}}</th>
                        </tfoot>
                    </table>
                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                </div>
                <div class="button-list text-right">
                    <button @click="addCityFromDepartment()" type="button" class="btn btn-primary btn-sm">{{trans.get('keys.them')}}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    export default {
        props: ['id'],
        //components: {vPagination},
        data() {
            return {
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                totalRow:0,
                row: 10,
                allSelected: false,
                city_add:[]
            }
        },
        methods: {
            selectAllCheckbox(){
                this.city_add = [];
                if(!this.allSelected){
                    this.posts.forEach((select) => {
                        this.city_add.push(select.city_id);
                    });
                }
            },
            addCityFromDepartment(){
                if(this.city_add.length  == 0){
                    swal({
                        title: this.trans.get('keys.thong_bao'),
                        text: this.trans.get('keys.ban_chua_con_tinh_thanh'),
                        type: "warning",
                        showCancelButton: false,
                        closeOnConfirm: true,
                        showLoaderOnConfirm: false
                    });
                    return false;
                }
                axios.post('/system/organize/department/add_city_from_department',{
                    city_add:this.city_add,
                    id:this.id
                })
                    .then(response => {
                        roam_message(response.data.status,response.data.message);
                        this.city_add = [];
                        this.allSelected = false;
                    })
                    .catch(error => {
                        roam_message('error','Lỗi hệ thống. Thao tác thất bại');
                    });
            },
            getCityAddFromDepartment(paged) {
                axios.post('/system/organize/department/list_city_addfrom_department', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
                        this.totalRow = response.data.pagination ? response.data.pagination.totalRow : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getCityAddFromDepartment();
            },
        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>
