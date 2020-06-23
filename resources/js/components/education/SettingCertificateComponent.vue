<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
            </li>
            <li v-if="type == 1" class="breadcrumb-item active">{{ trans.get('keys.danh_sach_chung_chi') }}</li>
            <li v-else class="breadcrumb-item active">{{ trans.get('keys.danh_sach_huy_hieu') }}</li>
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
                      <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1"
                         aria-expanded="true"><i
                        class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
                    </div>
                    <div id="collapse_1" class="collapse" data-parent="#accordion_1"
                         role="tabpanel">
                      <div class="card-body">
                        <create-certificate></create-certificate>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table_res">
                    <thead>
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>
                      <div v-if="type == 1">{{trans.get('keys.mau_chung_chi')}}</div>
                      <div v-else>{{trans.get('keys.mau_huy_hieu')}}</div>
                    </th>
                    <th>
                      <div v-if="type == 1">{{trans.get('keys.ten_chung_chi')}}</div>
                      <div v-else>{{trans.get('keys.ten_huy_hieu')}}</div>
                    </th>

                    <!--                                        <th>{{trans.get('keys.ten_to_chuc')}}</th>-->
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
                      <!--                                            <td>-->
                      <!--                                                {{ image.organization_name }}-->
                      <!--                                            </td>-->
                      <td class=" mobile_hide">
                        {{ image.description }}
                      </td>
                      <td class=" mobile_hide">
                                                <span v-if="image.is_active == 1"
                                                      class="badge badge-success">Active</span>
                        <span v-else class="badge badge-secondary">Inactive</span>
                      </td>
                      <td>
                        <router-link :title="trans.get('keys.sua')"
                                     class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                     :to="{name: 'EditCertificate', params: {id: image.id, type: image.type}}">
                          <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                        </router-link>
                        <button @click.prevent="deleteCertificate('/certificate/delete/'+image.id)"
                                class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                          <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                        </button>
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
    props: ['type'],
    components: {CreateCertificate},
    data() {
      return {
        images: [],
      }
    },
    methods: {

      onPageChange() {
      },
      getListImages() {
        // axios.get('/certificate/get_images')
        axios.post('/certificate/get_images', {
          type: this.type
        })
          .then(response => {
            if (Array.isArray(response.data))
              this.images = response.data;
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      deleteCertificate(url) {
        let current_pos = this;
        swal({
          title: this.trans.get('keys.ban_muon_xoa_muc_da_chon'),
          text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
          type: "warning",
          showCancelButton: true,
          closeOnConfirm: true,
          showLoaderOnConfirm: true
        }, function () {
          axios.post(url)
            .then(response => {
              if (response.data === 'exists') {
                toastr['error'](current_pos.trans.get('keys.chung_chi_nay_dang_duoc_chi_dinh_lam_mau_nen_khong_xoa_duoc'), current_pos.trans.get('keys.that_bai'));
              } else {
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
      },
      setFileInput() {
        $('.dropify').dropify();
      }
    },
    mounted() {
      this.getListImages();
    },
    updated() {
      this.setFileInput();
    }
  }
</script>

<style scoped>

</style>
