<template>
  <div class="container-fluid mt-15">
  <div class="row">
    <div class="col">
      <nav class="breadcrumb" aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0">
          <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
          <li class="breadcrumb-item active">{{ trans.get('keys.test_import_excel') }}</li>
        </ol>
      </nav>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-12">
      <section class="hk-sec-wrapper">
        <h5 class="hk-sec-title" v-if="type === 'system'">{{trans.get('keys.them_moi_du_lieu_excel')}}</h5>
        <h5 class="hk-sec-title" v-else-if="type === 'teacher'">{{trans.get('keys.them_moi_du_lieu_excel')}}</h5>
        <h5 class="hk-sec-title" v-else>{{trans.get('keys.them_moi_du_lieu_excel')}}</h5>
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
                            <label for="input-mode">{{trans.get('keys.chon_che_do')}}</label>
                            <select id="input-mode" class="form-control" v-model="mode">
                              <option value="full">{{trans.get('keys.full')}}</option>
                              <option value="lite">{{trans.get('keys.lite')}}</option>
                            </select>
                        </div>
                      </div>
                      <div class="col-sm-4 offset-4 text-right">
                        <p class="mb-3">
                          <a v-if="mode === 'lite'" :href="file_url_lite" class="btn px-0 not_shadow"><i class="fal fa-file-alt mr-3"></i>{{trans.get('keys.tai_ve_bieu_mau_rut_gon')}}</a>
                          <a v-else :href="file_url" class="btn px-0 not_shadow"><i class="fal fa-file-alt mr-3"></i>{{trans.get('keys.tai_ve_bieu_mau')}}</a>
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
        props: ['type'],
        data() {
            return {
                city_id: 0,
                department_id: 0,
                mode: 'full',
                data: [],
                urlImport:'/api/excel/import/user',
                data_import: {},
                importType:0,
                confirm:'',
                departmentSelectOptions: [],
                file_url: '',
                file_url_lite: ''
            }
        },
        methods: {
            myFilterBy: (option, label, search) => {
                if (!label) {
                    label = '';
                }
                let new_search = convertUtf8(search);
                let new_label = convertUtf8(label);
                //return this.filterBy(option, new_label, new_search); //can not call components function here
                return (new_label || '').toLowerCase().indexOf(new_search) > -1; // "" not working
            },
            selectedFile() {
                let file = this.$refs.file.files[0];
                if(!file || (file.type !== 'application/vnd.ms-excel' && file.type !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && file.type !== '.csv')){
                    const input = this.$refs.file;
                    input.type = 'file';
                    this.$refs.file.value = '';
                    roam_message("error",this.trans.get('keys.dinh_dang_file_khong_hop_le'));
                }
            },
            listDepartment(){
                axios.post('/system/organize/city/get_department_list')
                    .then(response => {
                        //this.data = response.data;
                        let additionalDepartments = [];
                        response.data.forEach(function(departmentItem) {
                            let newDepartment = {
                                label: departmentItem.name,
                                id: departmentItem.id
                            };
                            additionalDepartments.push(newDepartment);
                        });
                        this.departmentSelectOptions = additionalDepartments;
                        this.$nextTick(function () {
                            $('.selectpicker').selectpicker('refresh');
                        });
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            importExcel(){
                // if(this.city_id == 0){
                //     $('.city_required').show();
                //     return;
                // }

                // if(!this.department_id || this.department_id === 0){
                //     $('.department_required').show();
                //     return;
                // }

                if(this.$refs.file.files[0] === undefined){
                    $('.file_required').show();
                    return;
                }
                let loader = $('.preloader-it');

                if (this.mode === 'lite') {
                  this.urlImport = '/api/excel/import/user/lite';
                }

                loader.fadeIn();

                if(!$('button.hasLoading').hasClass('loadding')){
                    $('button.hasLoading').addClass('loadding');
                    this.formData = new FormData();
                    this.formData.append('file', this.$refs.file.files[0]);
                    this.formData.append('importType', this.importType);
                    this.formData.append('city_id', this.city_id); //removed
                    this.formData.append('department_id', this.department_id); //removed
                    this.formData.append('role_name', 'student');
                    this.formData.append('from', 'cms');
                    axios.post(this.urlImport, this.formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        loader.fadeOut();
                          if (response.data) {
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
                            $('#btnFilter').trigger('click');
                            //roam_message('success','Lỗi hệ thống. Thao tác thất bại');
                          }
                        })
                    .catch(error => {
                        loader.fadeOut();
                        $('button.hasLoading').removeClass('loadding');
                        $('.logUpload').show();
                        $('#btnFilter').trigger('click');
                        console.log(error);
                        roam_message('error', this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    });
                }
            },
            onPageChange() {
            },
            fetch() {
              axios.post('/bridge/fetch', {
                view: 'ImportIndex'
              })
                .then(response => {
                  this.file_url = response.data.file_url;
                  this.file_url_lite = response.data.file_url_lite
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
            //this.listData();
            //this.listDepartment();
            //this.getUser();
            this.fetch();
        },
      updated() {
          this.setFileInput();
      }
    }
    function convertUtf8(str) {
        str = str.toLowerCase();
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
        str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
        str = str.replace(/đ/g,"d");
        str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'|\"|\&|\#|\[|\]|~|\$|_|`|-|{|}|\||\\/g," ");
        str = str.replace(/ + /g," ");
        str = str.trim();
        return str;
    }
</script>

<style scoped>

</style>
