<template>

    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_faq') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div>
                    <div class="row mx-0">
                        <div class="col-12 hk-sec-wrapper">
                            <h5 class="hk-sec-title">{{ trans.get('keys.danh_sach_faq') }}</h5>
                            <div class="col-sm">
                                <div class="table-wrap">
                                    <div class="row mb-3">
                                        <div class="dataTables_wrapper">
                                            <div class="dataTables_length">
                                              <button class="btn btn-info" id="all-tabs" v-on:click="listData(0)">All({{ total }})</button>
                                              <button v-for="(tab,index) in tab_list" class="btn btn-info" style="margin-right: 5px" v-on:click="listData(tab.id)">{{ tab.name }}({{ tab.faqs.length }}) <i class="fa fa-remove" v-on:click="removeTab(tab.id)"></i></button>
                                            </div>
                                        </div>
                                        <div class="form-inline">
                                          <input class="form-control" v-model="tab_name" :placeholder="trans.get('keys.ten_tab')">
                                          <button @click="addTab()" type="button" class="btn btn-primary">
                                            <i class="fa fa-plus"></i>
                                            {{trans.get('keys.them_tab')}}
                                          </button>
                                        </div>
                                    </div>
                                    <div class="row form-inline pull-right mb-3">
                                      <div class="form-inline">
                                        <input class="form-control" v-model="faq_name" :placeholder="trans.get('keys.ten_faq')">
                                        <select v-model="tab_id" class="form-control custom-select">
                                          <option value="0">{{ trans.get('keys.chon_tab') }}</option>
                                          <option v-for="(tab,index) in tab_list" :value="tab.id">{{ tab.name }}</option>
                                        </select>
                                        <button @click="addFaq()" type="button" class="btn btn-primary">
                                          <i class="fa fa-plus"></i>
                                          {{trans.get('keys.them_faq')}}
                                        </button>
                                      </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table_res">
                                            <thead>
                                            <tr>
                                                <th>{{trans.get('keys.stt')}}</th>
                                                <th>{{trans.get('keys.ten')}}</th>
                                                <th>{{trans.get('keys.tab')}}</th>
                                                <th>{{trans.get('keys.trang_thai')}}</th>
                                                <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(template,index) in data_list">
                                                <td>{{ index+1 }}</td>
                                                <td>{{ template.name }}</td>
                                                <td>{{ template.tab ? template.tab.name : '' }}</td>
                                                <td>
                                                  <span v-if="template.content && template.content.length !== 0" class="badge badge-success">{{trans.get('keys.co_noi_dung')}}</span>
                                                  <span v-else class="badge badge-grey">{{trans.get('keys.bo_trong')}}</span>
                                                </td>
                                                <td class="text-center">
                                                    <router-link
                                                      :title="trans.get('keys.sua')"
                                                      class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                      :to="{ name: 'FaqDetail', params: { id: template.id }}">
                                                        <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                                    </router-link>
                                                    <button
                                                            :title="trans.get('keys.xoa')" data-toggle="modal"
                                                            data-target="#delete-ph-modal"
                                                            @click="deletePost(template.id)"
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
    </div>


</template>

<script>
    export default {
        data() {
            return {
                data_list: [],
                tab_list: [],
                tab_name: '',
                faq_name: '',
                tab_id: 0,
                total: 0,
                selected_tab: 0
            }
        },
        methods: {
            listData(tabId) {
                this.selected_tab = tabId;
                this.formData = new FormData();
                this.formData.append("id", tabId);
                axios.post('/api/faq/list',
                  this.formData,
                  {
                    headers: {
                      "Content-Type": "multipart/form-data"
                    }
                  })
                    .then(response => {
                        this.data_list = response.data.data;
                        this.total = response.data.total;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            removeTab(tabId) {
                this.formData = new FormData();
                this.formData.append("id", tabId);
                axios.post(
                  "/api/faq_tab/remove",
                  this.formData,
                  {
                    headers: {
                      "Content-Type": "multipart/form-data"
                    }
                  }
                )
                .then(response => {
                  if (response.data.status === 'success') {
                    toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                    //reset
                    this.tab_name = '';
                    this.listTab();
                    let all_tab = $('#all-tabs');
                    all_tab.click();
                    all_tab.hover();
                  } else if (response.data.status === 'warning') {
                    toastr['warning'](response.data.message, this.trans.get('keys.canh_bao'));
                  } else {
                    toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                  }
                })
                .catch(error => {
                  toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
                });
            },
            listTab() {
              axios.get('/api/faq_tab/list')
                .then(response => {
                  this.tab_list = response.data;
                })
                .catch(error => {
                  console.log(error);
                });
            },
            addTab() {
              if (this.tab_name.length === 0) {
                toastr['error'](this.trans.get('keys.ten_khong_duoc_bo_trong'), this.trans.get('keys.that_bai'));
                return;
              }
              this.formData = new FormData();
              this.formData.append("name", this.tab_name);
              axios.post(
                "/api/faq_tab/create",
                this.formData,
                {
                  headers: {
                    "Content-Type": "multipart/form-data"
                  }
                }
              )
              .then(response => {
                if (response.data.status === 'success') {
                  toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                  //reset
                  this.tab_name = '';
                  this.listTab();
                  let all_tab = $('#all-tabs');
                  all_tab.click();
                  all_tab.hover();
                } else if (response.data.status === 'warning') {
                  toastr['warning'](response.data.message, this.trans.get('keys.canh_bao'));
                } else {
                  toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                }
              })
              .catch(error => {
                toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
              });
            },
            addFaq() {
              if (this.faq_name.length === 0) {
                toastr['error'](this.trans.get('keys.ten_khong_duoc_bo_trong'), this.trans.get('keys.that_bai'));
                return;
              }
              this.formData = new FormData();
              this.formData.append("name", this.faq_name);
              this.formData.append("tab_id", this.tab_id);
              axios.post(
                "/api/faq/create",
                this.formData,
                {
                  headers: {
                    "Content-Type": "multipart/form-data"
                  }
                }
              )
              .then(response => {
                if (response.data.status === 'success') {
                  toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                  //reset
                  this.tab_name = '';
                  this.listData(this.selected_tab);
                  let all_tab = $('#all-tabs');
                  all_tab.click();
                  all_tab.hover();
                } else if (response.data.status === 'warning') {
                  toastr['warning'](response.data.message, this.trans.get('keys.canh_bao'));
                } else {
                  toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                }
              })
              .catch(error => {
                toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
              });
            },
            deletePost(id) {
            let current_pos = this;
            swal({
              title: this.trans.get('keys.ban_muon_xoa_muc_da_chon'),
              text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
              type: "warning",
              showCancelButton: true,
              closeOnConfirm: true,
              showLoaderOnConfirm: true
            }, function () {
              let loader = $('.preloader-it');
              loader.fadeIn();
              axios.post('/api/faq/delete', {id: id})
                .then(response => {
                  loader.fadeOut();
                  if (response.data.status) {
                    toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                    current_pos.listData(current_pos.selected_tab);
                  } else {
                    toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                  }
                  swal.close();
                })
                .catch(error => {
                  loader.fadeOut();
                  swal.close();
                  toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                });
            });
            return false;
          },
        },
        mounted() {
            this.listData(0);
            this.listTab();
        }
    }
</script>

<style scoped>

</style>
