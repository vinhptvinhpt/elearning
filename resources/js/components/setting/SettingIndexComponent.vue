<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">
              {{ trans.get('keys.dashboard') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.tuy_chinh') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <section class="hk-sec-wrapper">
          <h5 class="hk-sec-title">{{trans.get('keys.thay_doi_tuy_chinh')}}</h5>
          <p class="mb-3">{{trans.get('keys.thay_doi_mot_so_tuy_chinh_cho_he_thong')}}</p>
          <div class="row mb-4">
            <div class="col-12">
              <form class="form-row" id="setting-form">
                <div class="col-12 form-group" v-for="(setting_object, index) in setting">
                  <template v-if="setting_object.editor === 'text'">
                    <label :for="'inputText_'+index"><strong>{{ setting_object.label }}</strong></label>
                    <input v-model="setting_object.content" type="text" :id="'inputText_'+index" class="form-control">
                  </template>
                  <template v-else-if="setting_object.editor === 'textarea'">
                    <label :for="'textarea'+index"><strong>{{ setting_object.label }}</strong></label>
                    <textarea v-model="setting_object.content" :id="'textarea'+index" class="form-control" rows="3"></textarea>
                  </template>
                  <template v-else-if="setting_object.editor === 'checkbox'">
                    <h6 class="mb-5 d-inline-flex">
                                        <span class="inline-checkbox ml-3">
                                            <span class="custom-control custom-checkbox custom-control-inline">
                                                <input v-model="setting_object.content" type="checkbox" class="custom-control-input" :id="'checkbox'+index">
                                                <label class="custom-control-label" :for="'checkbox'+index"></label>
                                            </span>
                                        </span>
                      {{ setting_object.label }}
                    </h6>
                  </template>
                </div>
              </form>
            </div>
            <div class="col-12 button-list">
              <button type="button" class="btn btn-primary" @click="updateSetting()">{{trans.get('keys.luu')}}</button>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
        data() {
            return {
                setting: [],
            }
        },
        methods: {
            listData() {
                axios.get('/configuration/list_data')
                    .then(response => {
                        let source_data = response.data;
                        $.each(source_data, function() {
                            if (this.content === "disable") {
                                this.content = false;
                            } else if (this.content === 'enable') {
                                this.content = true;
                            }
                        });
                        this.setting = source_data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            updateSetting() {
                this.formData = new FormData();
                this.setting.forEach(val => {
                    let pretty_value = val.content;
                    if (val.content === false) {
                        pretty_value = 'disable';
                    } else if (val.content === true) {
                        pretty_value = 'enable';
                    }
                    this.formData.append(val.target, pretty_value);
                });
                axios.post('/configuration/update', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if(response.data === 'success') {
                            swal({
                                title: "Thông báo",
                                text: "Update tùy chọn thành công.",
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            }, function () {
                                location.reload();
                            });
                        }
                        else if(response.data.length !== 0) {
                            swal({
                                title: "Dữ liệu sai định dạng",
                                text: "Dữ liệu nhập vào không đúng: " + response.data,
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }
                        else{
                            swal({
                                title: "Lỗi hệ thống",
                                text: "Thao tác thất bại.",
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
        },
        mounted() {
            this.listData();
        }
    }
</script>

<style scoped>

</style>
