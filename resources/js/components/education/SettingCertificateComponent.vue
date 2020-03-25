<template>

  <div class="container-fluid mt-15">
    <div class="row">
    <div class="col">
      <nav class="breadcrumb" aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0">
          <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
          <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_chung_chi') }}</li>
        </ol>
      </nav>
    </div>
  </div>
    <div>
      <div class="card">
        <div class="card-body">
          <div class="edit_city_form form-material">
            <h5 class="mb-20 text-uppercase">{{trans.get('keys.danh_sach_mau_chung_chi')}}</h5>
            <div class="row mb-4">
              <div class="col-sm">
                <div class="accordion" id="accordion_1">
                  <div class="card">
                    <div class="card-header d-flex justify-content-between">
                      <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1" aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
                    </div>
                    <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                      <div class="card-body">
                        <create-certificate></create-certificate>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <!--                                    <div class="card-header d-flex justify-content-between">-->
                    <!--                                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_2" aria-expanded="true"><i class="fa fa-cog mr-3" aria-hidden="true"></i> Cài đặt hiển thị trên chứng chỉ</a>-->
                    <!--                                    </div>-->
                    <!--                                    <div id="collapse_2" class="collapse" data-parent="#accordion_1" role="tabpanel">-->
                    <!--                                        <div class="card-body">-->
                    <!--                                            <setting_show_certificate></setting_show_certificate>-->
                    <!--                                        </div>-->
                    <!--                                    </div>-->
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-8 dataTables_wrapper">
                <div class="dataTables_length" style="display: inline-block;">
                  <!--                                <label>Hiển thị-->
                  <!--                                    <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getListUsers(1)">-->
                  <!--                                        <option value="10">10</option>-->
                  <!--                                        <option value="25">25</option>-->
                  <!--                                        <option value="50">50</option>-->
                  <!--                                        <option value="100">100</option>-->
                  <!--                                    </select>-->
                  <!--                                </label>-->
                </div>
              </div>
              <div class="col-sm-4">
                <!--                            <div class="d-flex flex-row form-group">-->
                <!--                                <input  type="text"-->
                <!--                                        class="form-control search_text" placeholder="Nhập từ khóa...">-->
                <!--                                <button type="button" class="btn btn-primary btn-sm"-->
                <!--                                        @click="getListStudents(1)">-->
                <!--                                    Tìm-->
                <!--                                </button>-->
                <!--                            </div>-->
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table_res">
                    <thead>
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>{{trans.get('keys.mau_chung_chi')}}</th>
                    <th>{{trans.get('keys.ten_chung_chi')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.mo_ta')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.mau')}}</th>
                    <th>{{trans.get('keys.hanh_dong')}}</th>
                    </thead>
                    <tbody>
                    <tr v-if="images.length == 0">
                      <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                    </tr>
                    <tr v-else v-for="(image,index) in images">
                      <td>{{ index+1 }}</td>
                      <td>
                        <img :src="image.path" alt="" width="100px">
                      </td>
                      <td>
                        {{ image.name }}
                      </td>
                      <td class=" mobile_hide">
                        {{ image.description }}
                      </td>
                      <td class=" mobile_hide">
                        <span v-if="image.is_active == 1" class="badge badge-success">Active</span>
                        <span v-else class="badge badge-secondary">Inactive</span>
                      </td>
                      <td>
                        <router-link :title="trans.get('keys.sua')"
                           class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                           :to="{name: 'EditCertificate', params: {id: image.id}}">
                          <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                        </router-link>
                        <button @click.prevent="deleteCertificate('/certificate/delete/'+image.id)" class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2"><span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
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

</template>

<script>
  import CreateCertificate from "./CreateCertificateComponent";

    export default {
        props: [],
        components: {CreateCertificate},
        data() {
            return {
                images: {},
            }
        },
        methods: {
            onPageChange() {
            },
            getListImages(){
                axios.get('/certificate/get_images')
                    .then(response => {
                        this.images = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            deleteCertificate(url) {
                let current_pos = this;
                swal({
                    title: "Bạn muốn xóa mục đã chọn",
                    text: "Chọn 'ok' để thực hiện thao tác.",
                    type: "error",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post(url)
                        .then(response => {
                            console.log(response);
                            if(response.data === 'exists'){
                                toastr['error']("Chứng chỉ này đang được chỉ định làm mẫu nên không xóa được!", current_pos.trans.get('keys.that_bai'));
                            }else{
                                toastr['success'](current_pos.trans.get('keys.xoa_thanh_cong'), current_pos.trans.get('keys.thanh_cong'));
                                current_pos.getListImages();
                            }
                        })
                        .catch(error => {
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                            console.log(error);
                        });
                });

                return false;
            }
        },
        mounted() {
            this.getListImages();
        }
    }
</script>

<style scoped>

</style>
