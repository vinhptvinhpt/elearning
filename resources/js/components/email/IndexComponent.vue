<template>

    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_email_template') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div>
                    <div class="row mx-0">
                        <div class="col-12 hk-sec-wrapper">
                            <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_email_template')}} : {{ source_data.length + 1 }}</h5>
                            <div class="col-sm">
                                <div class="table-wrap">
                                    <div class="row">
                                        <div class="col-6 dataTables_wrapper">
                                            <div class="dataTables_length">
<!--                                                <span style="color:#3a55b1; font-size: 20px; font-weight: 600;">{{trans.get('keys.tong_so_thu_vien_email_template_hien_tai')}}: {{ source_data.length + 1 }}</span>-->
                                            </div>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="table-responsive">
                                        <table class="table_res">
                                            <thead>
                                            <tr>
                                                <th>{{trans.get('keys.stt')}}</th>
                                                <th>{{trans.get('keys.template')}}</th>
                                                <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
<!--                                            <tr v-if="source_data.length == 0">-->
<!--                                                <td colspan="3">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>-->
<!--                                            </tr>-->
                                            <tr v-for="(template,index) in source_data">
                                                <td>{{ index+1 }}</td>
                                                <td>
                                                    <router-link
                                                            :to="{ name: 'EmailTemplateDetail', params: { name_file: template.target }}">
                                                        {{ template.label }}
                                                    </router-link>
                                                </td>
                                                <td class="text-center">
                                                    <router-link :title="trans.get('keys.sua')"
                                                                 class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                                 :to="{ name: 'EmailTemplateDetail', params: { name_file: template.target }}">
                                                        <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                                    </router-link>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ source_data.length + 1 }}</td>
                                                <td>
                                                    <router-link
                                                            :to="{ name: 'EmailTemplateDetail', params: { name_file: 'forgot_password' }}">
                                                        {{trans.get('keys.quen_mat_khau')}}
                                                    </router-link>
                                                </td>
                                                <td class="text-center">
                                                    <router-link :title="trans.get('keys.sua')"
                                                                 class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                                 :to="{ name: 'EmailTemplateDetail', params: { name_file: 'forgot_password' }}">
                                                        <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                                    </router-link>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
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
        data() {
            return {
                source_data: []
            }
        },
        methods: {
            listData() {
                axios.get('/email_template/list_data')
                    .then(response => {
                        this.source_data = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }
        },
        mounted() {
            this.listData();
        }
    }
</script>

<style scoped>

</style>
