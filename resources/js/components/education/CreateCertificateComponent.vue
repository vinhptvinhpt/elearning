<template>
    <div class="row">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <p>
                      <input  type="file" ref="file" name="file" class="dropify"  accept="image/*"  @change="selectedFile"/>
                      <label class="required text-danger image_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <form action="" class="form-row hk-sec-wrapper">

              <div class="col-12 form-group">
                <label>{{trans.get('keys.chon_to_chuc')}} </label>
                <select id="organization_select" v-model="organization_id" autocomplete="false" class="form-control">
                  <option value="0">-- {{trans.get('keys.chon_to_chuc')}} --</option>
                  <option v-for="organization_option in organization_options" :value="organization_option.id">
                    {{organization_option.name}}
                  </option>
                </select>
                <label v-if="organization_id == '0'" class="required text-danger organization_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
              </div>

                <div class="col-12 form-group">
                    <label for="inputName">{{trans.get('keys.ten_chung_chi')}}</label>
                    <input autocomplete="false" v-model="name" type="text" id="inputName" :placeholder="trans.get('keys.nhap_ten_chung_chi')" class="form-control" @input="changeRequired('inputName')">
                    <label v-if="!name" class="required text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>

                <div class="col-12 form-group">
                    <label for="inputDescription">{{trans.get('keys.mo_ta')}} </label>
                    <input autocomplete="false" v-model="description" type="text" id="inputDescription" :placeholder="trans.get('keys.mo_ta_chung_chi_nay')" class="form-control" @input="changeRequired('inputDescription')">
                    <label v-if="!description" class="required text-danger description_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>

                <div class="col-12">
                    <div class="button-list">
                        <button @click="createCertificate()" type="button" class="btn btn-primary btn-sm">{{trans.get('keys.tao')}}</button>
                        <button type="button" class="btn btn-secondary btn-sm collapsed closeForm" data-toggle="collapse" href="#collapse_1" aria-expanded="true">{{trans.get('keys.huy')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['type'],
        data() {
            return {
                name: '',
                description: '',
                confirm:false,
                is_active: 0,
                coordinates:{},
                organization_options: [],
                organization_id: 0,
            }
        },
        methods: {
            selectedFile(e) {
                let file = this.$refs.file.files[0];
                const validFileTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/jpg'];
                if(!file || (validFileTypes.indexOf(file.type) == -1)){
                    const input = this.$refs.file;
                    input.type = 'file';
                    this.$refs.file.value = '';
                    toastr['error'](this.trans.get('keys.dinh_dang_file_khong_hop_le'), this.trans.get('keys.thong_bao'));
                }
                else{
                    $('.image_required').hide();
                    // this.certificate.path = e.target.value;
                    // $('#img_certificate').attr('src', e.target.value);
                }
            },
            changeRequired(element){
                $('#'+element).removeClass('notValidate');
            },
            createCertificate(){

                if(typeof(this.$refs.file.files[0]) == 'undefined'){
                    $('.image_required').show();
                    return;
                }

                if(this.organization_id == '0') {
                    $('.organization_required').show();
                    return;
                }

                if(!this.name) {
                    $('.name_required').show();
                    return;
                }

                if(!this.description) {
                    $('.description_required').show();
                    return;
                }

                var img = $('.dropify-render img')[0];

                if(typeof(img) !== 'undefined'){
                    this.coordinates.image_width = img.width;
                    this.coordinates.image_height = img.height;
                }

                this.formData = new FormData();
                this.is_active = this.confirm == true ? 1 : 0;
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('name', this.name);
                this.formData.append('is_active', this.is_active);
                this.formData.append('organization_id', this.organization_id);
                this.formData.append('description', this.description);
                this.formData.append('position', JSON.stringify(this.coordinates));

                axios.post('/certificate/create', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if(response.data.status){
                            toastr['success'](response.data.message, this.trans.get('keys.thong_bao'));
                            this.clearFileInput();
                            this.name = '';
                            this.description = '';
                            this.organization_id = 0;
                            $('.closeForm').trigger('click');
                            this.$parent.getListImages();
                        }
                        else{
                              $('.form-control').removeClass('notValidate');
                              $('#'+response.data.id).addClass('notValidate');
                            toastr['error'](response.data.message, this.trans.get('keys.thong_bao'));
                        }
                    })
                    .catch(error => {
                        console.log(error);
                        toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
                    });
            },
            setFileInput() {
              $('.dropify').dropify();
            },
            clearFileInput() {
                $('.dropify-clear').click();
            },
            fetchOrganization() {
                axios.get('/api/organization/getorganizations')
                    .then(response => {
                        this.organization_options = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
        },
        mounted() {
          this.setFileInput();
            this.fetchOrganization();
        }
    }
</script>

<style scoped>

</style>
