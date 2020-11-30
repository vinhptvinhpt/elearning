<template>
  <div class="container-fluid mt-15">
  <div class="row">
    <div class="col">
      <nav class="breadcrumb" aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0">
          <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
          <li class="breadcrumb-item active">{{ trans.get('keys.nhap_du_lieu_diem_co_san') }}</li>
        </ol>
      </nav>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-12">
      <section class="hk-sec-wrapper">
        <h5 class="hk-sec-title">{{trans.get('keys.nhap_du_lieu_diem_co_san')}}</h5>
        <div class="row mb-4">
          <div class="col-sm">
            <div class="accordion" id="accordion_1">
              <div class="card">
              </div>
              <div class="card">
                <div class="card-header d-flex justify-content-between">
                  <a class="" role="button" data-toggle="collapse" href="#collapse_2" aria-expanded="true"><i class="fal fa-upload mr-3"></i>{{trans.get('keys.tai_len_file_excel')}}</a>
                </div>
                <div id="collapse_2" class="collapse show" data-parent="#accordion_1">
                  <div class="card-body">

                    <div class="row">
                      <div class="col-sm-4">
                        <div class="form-group">
                          <div class="form-group">
                            <label for="input-year">{{trans.get('keys.chon_nam')}}</label>
                            <select id="input-year" class="form-control" v-model="year">
                              <option v-for="year in filter_year" :value="year">{{ year }}</option>-->
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-4 offset-4 text-right">
                        <p class="mb-3">
                          <a :href="file_url" class="btn px-0 not_shadow"><i class="fal fa-file-alt mr-3"></i>{{trans.get('keys.tai_ve_bieu_mau')}}</a>
                        </p>
                      </div>
                    </div>
                    <input type="file" ref="file" name="file" class="dropify fileImport" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"  @change="selectedFile"/>
                    <label class="text-danger file_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    <div class="button-list">
                      <button type="button" class="btn btn-primary btn-sm hasLoading" @click="importExcel()">{{trans.get('keys.import_excel')}}<i class="fa fa-spinner" aria-hidden="true"></i></button>
                    </div>
                  </div>
                </div>
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
        data() {
            return {
                year: '',
                urlImport:'/api/excel/import/user_mark',
                file_url: '',
                filter_year: []
            }
        },
        methods: {
            selectedFile() {
                let file = this.$refs.file.files[0];
                if(!file || (file.type !== 'application/vnd.ms-excel' && file.type !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && file.type !== '.csv')){
                    const input = this.$refs.file;
                    input.type = 'file';
                    this.$refs.file.value = '';
                    roam_message("error",this.trans.get('keys.dinh_dang_file_khong_hop_le'));
                }
                let file_required = $('.file_required');
                file_required.hide();
            },
            setYearInput() {
              let currYear = new Date().getFullYear();
              let startYear = currYear - 5;
              for (let i = startYear; i <= currYear; i++) {
                this.filter_year.push(i);
              }
              this.year = currYear;
            },
            importExcel() {
                let file_required = $('.file_required');
                let hasLoading = $('button.hasLoading');
                let loader = $('.preloader-it');

                file_required.hide();

                if(this.$refs.file.files[0] === undefined){
                  file_required.show();
                  return;
                }

                loader.fadeIn();
                if(!hasLoading.hasClass('loadding')){
                    hasLoading.addClass('loadding');
                    this.formData = new FormData();
                    this.formData.append('file', this.$refs.file.files[0]);
                    this.formData.append('year', parseInt(this.year));
                    axios.post(this.urlImport, this.formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        loader.fadeOut();
                          if (response.data) {
                            //Show message
                            toastr[response.data.status](response.data.message, this.trans.get('keys.' + response.data.status));
                            //Download file
                            if (response.data.data.result_file) {
                              let file_name = response.data.data.result_file;
                              let a = $("<a>")
                                .prop("href", "/api/excel/download/" + file_name)
                                .appendTo("body");
                              a[0].click();
                              a.remove();
                            }
                            $('button.hasLoading').removeClass('loadding');
                            $('.logUpload').show();
                          }
                        })
                    .catch(error => {
                        loader.fadeOut();
                        $('button.hasLoading').removeClass('loadding');
                        $('.logUpload').show();
                        console.log(error);
                        roam_message('error', this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    });
                }
            },
            fetch() {
              axios.post('/bridge/fetch', {
                view: 'ImportMark'
              })
                .then(response => {
                  this.file_url = response.data.file_url;
                })
                .catch(error => {
                  console.log(error);
                })
            },
            setFileInput() {
              $('.dropify').dropify();
            }
        },
        mounted() {
            this.fetch();
            this.setYearInput();
        },
      updated() {
          this.setFileInput();
      }
    }
</script>

<style scoped>

</style>
