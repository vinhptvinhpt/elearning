<template>
    <div class="row">

        <div class="col-12">
            <form action="" class="form-row hk-sec-wrapper" id="user_form_create">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label ><strong>{{trans.get('keys.chu_dai_ly')}}</strong></label>
                        <div class="input-group">
                            <div class="wrap_search_box">
                                <div class="btn_search_box search_user_default">
                                    <span>{{trans.get('keys.chon_chu_dai_ly')}}</span>
                                </div>
                                <div class="content_search_box" id="search_box_1">
                                    <input @input="listDataSearchBoxUser()" type="text" v-model="search_box_user" class="form-control search_box">
                                    <i class="fa fa-spinner" aria-hidden="true"></i>
                                    <ul>
                                        <li @click="selectSearchBoxUser(item.user_id)" v-for="item in data_search_box_user" :data-value="item.user_id">{{item.fullname}}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <label v-if="!user_id" class="text-danger user_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label ><strong>{{trans.get('keys.dai_ly')}}</strong></label>
                        <div class="input-group">
                            <div class="wrap_search_box">
                                <div class="btn_search_box search_branch_default">
                                    <span>{{trans.get('keys.chon_dai_ly')}}</span>
                                </div>
                                <div class="content_search_box" id="search_box_2" >
                                    <input @input="listDataSearchBoxBranch()" type="text" v-model="search_box_branch" class="form-control search_box">
                                    <i class="fa fa-spinner" aria-hidden="true"></i>
                                    <ul>
                                        <li @click="selectSearchBoxBranch(item.id)" v-for="item in data_search_box_branch" :data-value="item.id">{{item.name}}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <label v-if="!branch_id" class="text-danger user_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="button-list text-right">
                        <button @click="assign()" type="button" class="btn btn-primary btn-sm">{{trans.get('keys.phan_quyen')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                user_id: '',
                branch_id: '',
                data_search_box_user:[],
                data_search_box_branch:[],
                search_box_user:'',
                search_box_branch:'',
            }
        },
        methods: {
            selectSearchBoxUser(input_search_box_id){
                this.user_id = input_search_box_id;
            },
            selectSearchBoxBranch(input_search_box_id){
                this.branch_id = input_search_box_id;
            },
            listDataSearchBoxUser(){
                $('#search_box_1').addClass('loadding');
                axios.post('/system/organize/branch/data_search_box_user_branch_master',{
                    keyword:this.search_box_user,
                })
                    .then(response => {
                        this.data_search_box_user = response.data;
                        $('#search_box_1').removeClass('loadding');
                    })
                    .catch(error => {
                        $('#search_box_1').removeClass('loadding');
                        this.data_search_box_user = [];
                    })
            },
            listDataSearchBoxBranch(){
                $('#search_box_2').addClass('loadding');
                axios.post('/system/organize/branch/data_search_box_branch_for_master',{
                    keyword:this.search_box_branch,
                })
                    .then(response => {
                        this.data_search_box_branch = response.data;
                        $('#search_box_2').removeClass('loadding');
                    })
                    .catch(error => {
                        $('#search_box_2').removeClass('loadding');
                        this.data_search_box_user = [];
                    })
            },
            assign(){
                if(!this.user_id){
                    $('.user_required').show();
                    return;
                }
                if(!this.branch_id){
                    $('.branch_required').show();
                    return;
                }
                axios.post('/system/organize/branch/assign_master',{
                    user_id:this.user_id,
                    branch_id:this.branch_id
                })
                    .then(response => {
                        if(response.data.key) {
                            roam_message('error',response.data.message);
                            $('.form-control').removeClass('error');
                            $('#branch_'+response.data.key).addClass('error');
                        }else{
                            roam_message(response.data.status,response.data.message);
                            if(response.data.status === 'success'){
                                $('.form-control').removeClass('error');
                                this.listDataSearchBoxBranch();
                                //$(".search_user_default").html("<span>" + this.trans.get('keys.chon_chu_dai_ly') + "</span>");
                                $(".search_branch_default").html("<span>" + this.trans.get('keys.chon_dai_ly') + "</span>");

                            }
                        }
                    })
                    .catch(error => {
                        roam_message('error','Lỗi hệ thống. Thao tác thất bại');
                    })
            }
        },
        mounted() {
            this.listDataSearchBoxUser();
            this.listDataSearchBoxBranch();
        }
    }
</script>

<style scoped>

</style>
