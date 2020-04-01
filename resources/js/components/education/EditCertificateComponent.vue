<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/certificate/setting">{{ trans.get('keys.danh_sach_chung_chi') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_thong_tin_chung_chi') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="row mx-0">
        <div class="col-12 hk-sec-wrapper">
          <h5 class="hk-sec-title">{{trans.get('keys.chinh_sua_thong_tin_chung_chi')}}</h5>
          <div class="row">
            <div class="col-12 col-lg-6">
              <div class="card">
                <div style="padding: 10px; overflow: hidden; position: inherit">
                  <img v-bind:src="certificate.path" alt="" @click.stop="onClickImage" id="img_certificate" ref="busstop">
                  <span id="sp_inputFullName" class="spText"></span>
                  <span id="sp_inputProgram" class="spText"></span>
                  <span id="sp_inputDate" class="spText"></span>
                  <img :src="logo_path" alt="" class="logo_img" id="sp_inputLogo">
<!--                  <span id="sp_inputLogo" class="spText"></span>-->
                </div>
                <div class="card-body">
                  <p>
                    <input  type="file" ref="file" name="file" class="dropify"  accept="image/*"  @change="selectedFile"/>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <form action="" class="form-row hk-sec-wrapper">
                <div class="col-12 form-group">
                  <h6 for="inputName">{{trans.get('keys.ten_chung_chi')}} </h6>
                  <input autocomplete="false" v-model="certificate.name" type="text" id="inputName" :placeholder="trans.get('keys.nhap_id_dung_de_dang_nhap')" class="form-control mb-4" @input="changeRequired('inputName')">
                  <label v-if="!certificate.name" class="required text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-12 form-group">
                  <h6 for="inputDescription">{{trans.get('keys.mo_ta')}} </h6>
                  <input autocomplete="false" v-model="certificate.description" type="text" id="inputDescription" :placeholder="trans.get('keys.nhap_id_dung_de_dang_nhap')" class="form-control mb-4" @input="changeRequired('inputDescription')">
                  <label v-if="!certificate.description" class="required text-danger description_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-12 form-group">
                  <h6 class="d-inline-flex">
                    {{trans.get('keys.chung_chi_mau')}}
                    <span class="inline-checkbox ml-3">
                                    <span class="custom-control custom-checkbox custom-control-inline">
                                        <input v-if="certificate.is_active == 1" class="custom-control-input" :id="'inputCheck'" type="checkbox" v-model="certificate.confirm" checked>
                                        <input v-else type="checkbox" v-model="certificate.confirm"  class="custom-control-input" :id="'inputCheck'">
                                        <label class="custom-control-label" :for="'inputCheck'"></label>
                                    </span>
                                </span>
                  </h6>
                </div>

                <div class="col-12 form-group">
                  <h6>{{trans.get('keys.toa_do_chung_chi')}} </h6>
                  <p> {{trans.get('keys.chon_toa_do_hien_thi_thong_tin_khi_hien_thi_len_anh_chung_chi')}}</p>
                </div>

                  <div class="col-12 d-inline-flex">
                       <span class="inline-checkbox ml-3">
                                    <span class="custom-control custom-checkbox custom-control-inline">
                    <input  class="custom-control-input" :id="'inputLogo'" type="checkbox" v-model="certificate_img.pos_logo" @click="onClickTypeShow">
                    <label class="custom-control-label" :for="'inputLogo'"></label>
                                    </span></span>
                       {{trans.get('keys.toa_do_logo')}}
                       <span id="span_inputLogo"></span>
                  </div>

                <div class="row col-12">
                       <div class="col-5 d-inline-flex">
                         <span class="inline-checkbox ml-3">
                                    <span class="custom-control custom-checkbox custom-control-inline">
                    <input  class="custom-control-input" :id="'inputFullName'" type="checkbox" v-model="certificate_img.pos_name" @click="onClickTypeShow">
                    <label class="custom-control-label" :for="'inputFullName'"></label>
                                    </span></span>
                         {{trans.get('keys.toa_do_ten')}}
                       </div>
                  <div class="col-5">
                    <input id="ip_inputFullName" v-model="fullName" class="form-control txtChange" @change="OnchangeTextBox">
                  </div>
                  <div class="col-2">
                    <input id="ip_inputSizeFullName" type="number" min="1" class="form-control txtNumber" @change="OnchangeTextBox" :placeholder="trans.get('keys.co_chu')">
                  </div>
                </div>

                  <div class="row col-12">
                      <div class="col-5 d-inline-flex">
                         <span class="inline-checkbox ml-3">
                                    <span class="custom-control custom-checkbox custom-control-inline">
                    <input  class="custom-control-input" :id="'inputProgram'" type="checkbox" v-model="certificate_img.pos_program" @click="onClickTypeShow">
                    <label class="custom-control-label" :for="'inputProgram'"></label>
                                    </span></span>
                        {{trans.get('keys.toa_do_ten_khung_nang_luc')}}
                      </div>
<!--                      <span id="span_inputProgram"></span>-->
                    <div class="col-5">
                      <input id="ip_inputProgram" v-model="programName" class="form-control txtChange" @change="OnchangeTextBox">
                    </div>
                    <div class="col-2">
                      <input id="ip_inputSizeProgram" type="number" min="1" class="form-control txtNumber" @change="OnchangeTextBox" :placeholder="trans.get('keys.co_chu')">
                    </div>
                  </div>

                <div class="col-12 d-inline-flex">
                       <span class="inline-checkbox ml-3">
                                    <span class="custom-control custom-checkbox custom-control-inline">
                    <input  class="custom-control-input" :id="'inputDate'" type="checkbox" v-model="certificate_img.pos_date" @click="onClickTypeShow">
                    <label class="custom-control-label" :for="'inputDate'"></label>
                                    </span></span>
                  {{trans.get('keys.toa_do_ngay_cap')}}
                  <span id="span_inputDate"></span>
                </div>

                <div class="col-12">
                  <div class="button-list">
                    <button type="button" class="btn btn-primary btn-sm" @click="updateCertificate()">{{trans.get('keys.cap_nhat')}}</button>

                    <router-link :to="{name: 'SettingCertificate'}"
                                 class="btn btn-secondary btn-sm">
                      {{trans.get('keys.quay_lai')}}
                    </router-link>

                    <button style="float:right;" @click.prevent="deleteCertificate('/certificate/delete/'+certificate.id)" class="btn btn-sm btn-danger">
                      {{trans.get('keys.xoa')}}
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
    export default {
        props: ['id'],
        data() {
            return {
                certificate: {
                    id: '',
                    name: '',
                    description: '',
                    confirm: false,
                    is_active: 0,
                    path: ''
                },
                language : this.trans.get('keys.language'),
                imgLeft: null,
                imgTop: null,
                certificate_img: {
                    pos_logo: false,
                    pos_name: false,
                    pos_program: false,
                    pos_date: false
                },
                currentChoose: "",
                coordinates:{},
                logo_path: "",
                img_width: 0,
                img_height: 0,
                fullName: "Nguyễn Văn A",
                programName: "Chứng chỉ khóa học"
            }
        },
        methods:{
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
                    // this.certificate.path = e.target.value;
                    // $('#img_certificate').attr('src', e.target.value);
                }
            },
            changeRequired(element){
                $('#'+element).removeClass('notValidate');
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
                            toastr['success'](current_pos.trans.get('keys.xoa_thanh_cong'), current_pos.trans.get('keys.thanh_cong'));
                            current_pos.$router.push({name: 'SettingCertificate'});
                        })
                        .catch(error => {
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                            console.log(error);
                        });
                });

                return false;
            },
            certificateData(){
                axios.post('/certificate/detail',{
                    id: this.id
                })
                    .then(response => {

                        this.certificate = response.data;
                        this.certificate.confirm = this.certificate.is_active == 1 ? true : false;
                        // try {
                        //     var splitPath = this.certificate.path.split('/');
                        //     let fileName = require("../../../../public/storage/upload/certificate/"+splitPath[splitPath.length - 1]);
                        //     // do something
                        // } catch (e) {
                        //     this.certificate.path = "/storage/upload/certificate/default_certificate.jpg";
                        //     toastr['warning'](this.trans.get('keys.khong_tim_thay_anh_chung_toi_se_chon_anh_mac_dinh'), this.trans.get('keys.thong_bao'));
                        // }


                        // this.coordinates = JSON.parse(response.data.position);
                        if(response.data.position !== '')
                            this.coordinates = JSON.parse(response.data.position);
                        this.showCoordinates();
                        this.img_width = this.coordinates.image_width;
                        this.img_height = this.coordinates.image_height;
                    })
                    .catch(error => {
                    })
            },
            updateCertificate(){
                if(!this.certificate.name){
                    $('.name_required').show();
                    return;
                }

                if(!this.certificate.description) {
                    $('.description_required').show();
                    return;
                }

                if(!this.certificate_img.pos_logo){
                    toastr['error'](this.trans.get('keys.hay_chon_toa_do_logo'), this.trans.get('keys.thong_bao'));
                    return;
                }

                if(!this.certificate_img.pos_name){
                    toastr['error'](this.trans.get('keys.hay_chon_toa_do_ten'), this.trans.get('keys.thong_bao'));
                    return;
                }

                if(!this.certificate_img.pos_program){
                    toastr['error'](this.trans.get('keys.hay_chon_toa_do_khung_nang_luc'), this.trans.get('keys.thong_bao'));
                    return;
                }

                if(!this.certificate_img.pos_date)
                {
                    toastr['error'](this.trans.get('keys.hay_chon_toa_do_ngay_cap'), this.trans.get('keys.thong_bao'));
                    return;
                }

                if($('#ip_inputSizeFullName').val() == '')
                {
                    toastr['error'](this.trans.get('keys.hay_nhap_co_chu_ten_nguoi_dung'), this.trans.get('keys.thong_bao'));
                    return;
                }

                if($('#ip_inputSizeProgram').val() == '')
                {
                    toastr['error'](this.trans.get('keys.hay_nhap_co_chu_ten_khung_nang_luc'), this.trans.get('keys.thong_bao'));
                    return;
                }


                this.coordinates.image_width = $('#img_certificate')[0].clientWidth;
                this.coordinates.image_height = $('#img_certificate')[0].clientHeight;

                this.coordinates.fullnameWidth = $('#sp_inputFullName')[0].clientWidth;
                this.coordinates.fullnameHeight = $('#sp_inputFullName')[0].clientHeight;

                this.coordinates.fullnameSize = $('#ip_inputSizeFullName').val();
                this.coordinates.programSize = $('#ip_inputSizeProgram').val();


                this.formData = new FormData();
                this.certificate.is_active = this.certificate.confirm == true ? 1 : 0;
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('name', this.certificate.name);
                this.formData.append('is_active', this.certificate.is_active);
                this.formData.append('description', this.certificate.description);
                this.formData.append('id', this.id);
                this.formData.append('position', JSON.stringify(this.coordinates));

                axios.post('/certificate/update', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        var language =  this.language;
                        if (response.data.status) {
                          toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                          this.$router.push({ name: 'SettingCertificate' });
                        }else{
                            $('.form-control').removeClass('notValidate');
                            $('#'+response.data.id).addClass('notValidate');
                            toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
                    });
            },
            setFileInput() {
              $('.dropify').dropify();
            },
            onClickImage(e){
                var posX_click = `${e.clientX}`;
                var posY_click = `${e.clientY}`;
                var posX = this.$refs.busstop.getBoundingClientRect().x;
                var posY = this.$refs.busstop.getBoundingClientRect().y;

                this.img_width = this.$refs.busstop.getBoundingClientRect().width;
                this.img_height = this.$refs.busstop.getBoundingClientRect().height;

                var coordinate_x = posX_click-posX;
                var coordinate_y = posY_click-posY;
                // $('#span_'+this.currentChoose).text(": x="+coordinate_x + "; y="+coordinate_y);

                this.setTextToShowIntoImage(this.currentChoose, coordinate_x, coordinate_y);
            },
            onClickTypeShow(e){
                this.currentChoose = e.currentTarget.id;
                // $('#span_'+this.currentChoose).text("");
                $('#sp_'+this.currentChoose).text("");
                if(this.currentChoose == 'inputFullName')
                {
                    this.coordinates.fullnameX = 0;
                    this.coordinates.fullnameY = 0;
                    $('#ip_inputFullName').css('display', 'none');
                    $('#ip_inputSizeFullName').css('display', 'none');
                    if(!this.certificate_img.pos_name)
                    {
                       this.SetShowTextFullName();
                        this.certificate_img.pos_name = true;
                        this.setTextToShowIntoImage(this.currentChoose, this.img_width/2, this.img_height/2);
                    }
                }
                else if(this.currentChoose == 'inputDate')
                {
                    this.coordinates.dateX = 0;
                    this.coordinates.dateY = 0;
                    if(!this.certificate_img.pos_date){
                        this.certificate_img.pos_date = true;
                        this.setTextToShowIntoImage(this.currentChoose, this.img_width/2, this.img_height/2);
                    }
                }
                else if(this.currentChoose == 'inputProgram')
                {
                    this.coordinates.programX = 0;
                    this.coordinates.programY = 0;
                    $('#ip_inputProgram').css('display', 'none');
                    $('#ip_inputSizeProgram').css('display', 'none');
                    if(!this.certificate_img.pos_program){
                        this.SetShowTextProgram();
                        this.certificate_img.pos_program = true;
                        this.setTextToShowIntoImage(this.currentChoose, this.img_width/2, this.img_height/2);
                    }
                }
                else{
                    this.coordinates.logoX = 0;
                    this.coordinates.logoY = 0;
                    this.logo_path = "";
                    if(!this.certificate_img.pos_logo){
                        this.certificate_img.pos_logo = true;
                        this.setTextToShowIntoImage(this.currentChoose, this.img_width/2, this.img_height/2);
                    }
                }
            },
            setTextToShowIntoImage(name, coordinate_x, coordinate_y){
                const today = new Date();
                if(name == 'inputFullName' && this.certificate_img.pos_name)
                {
                    $('#sp_'+name).text(this.fullName);
                    $('#sp_'+name).css('font-size', this.coordinates.fullnameSize+'px');
                    this.coordinates.fullnameX = coordinate_x;
                    this.coordinates.fullnameY = coordinate_y;
                }
                else if(name == 'inputDate' && this.certificate_img.pos_date)
                {
                    $('#sp_'+name).text(today.getDate()+"/"+today.getMonth()+"/"+today.getFullYear());
                    this.coordinates.dateX = coordinate_x;
                    this.coordinates.dateY = coordinate_y;
                }
                else if(name == 'inputProgram' && this.certificate_img.pos_program)
                {
                    $('#sp_'+name).text(this.programName);
                    $('#sp_'+name).css('font-size', this.coordinates.programSize+'px');
                    this.coordinates.programX = coordinate_x;
                    this.coordinates.programY = coordinate_y;
                }
                else if(name == 'inputLogo' && this.certificate_img.pos_logo){
                    this.coordinates.logoX = coordinate_x;
                    this.coordinates.logoY = coordinate_y;
                    this.logo_path = "/storage/upload/certificate/default_logo.jpg";
                }
                $('#sp_'+name).css('left', coordinate_x);
                $('#sp_'+name).css('top', coordinate_y);

            },
            showCoordinates(){
                if(this.coordinates.fullnameX > 0)
                {
                    this.certificate_img.pos_name = true;
                    this.SetShowTextFullName();
                    this.setTextToShowIntoImage("inputFullName", this.coordinates.fullnameX, this.coordinates.fullnameY);
                }
                if(this.coordinates.logoX > 0)
                {
                    this.certificate_img.pos_logo = true;
                    this.setTextToShowIntoImage("inputLogo", this.coordinates.logoX, this.coordinates.logoY);
                }
                if(this.coordinates.programX > 0)
                {
                    this.certificate_img.pos_program = true;
                   this.SetShowTextProgram();
                    this.setTextToShowIntoImage("inputProgram", this.coordinates.programX, this.coordinates.programY);
                }
                if(this.coordinates.dateX > 0)
                {
                    this.certificate_img.pos_date = true;
                    this.setTextToShowIntoImage("inputDate", this.coordinates.dateX, this.coordinates.dateY);
                }
            },
            OnchangeTextBox(e){
                if(e.currentTarget.id.indexOf('Size') > -1){
                    if(e.currentTarget.id.indexOf('FullName') > -1 && this.certificate_img.pos_name)
                    {
                        if($('#ip_inputSizeFullName').val() < 0)
                            $('#ip_inputSizeFullName').val(15);
                        else {
                            $('#sp_inputFullName').css('font-size', e.currentTarget.value+'px');
                            $('#sp_inputFullName').css('line-height', e.currentTarget.value+'px');
                            this.currentChoose = "inputFullName";
                            this.coordinates.fullnameSize = e.currentTarget.value;
                        }
                    }
                    else if(e.currentTarget.id.indexOf('Program') > -1 && this.certificate_img.pos_program)
                    {
                        if($('#ip_inputSizeProgram').val() < 0)
                            $('#ip_inputSizeProgram').val(15);
                        else {
                            $('#sp_inputProgram').css('font-size', e.currentTarget.value + 'px');
                            $('#sp_inputProgram').css('line-height', e.currentTarget.value+'px');
                            this.currentChoose = "inputProgram";
                            this.coordinates.programSize = e.currentTarget.value;
                        }
                    }
                }
                else{
                    if(e.currentTarget.id == 'ip_inputFullName' && this.certificate_img.pos_name)
                    {
                        $('#sp_inputFullName').text(this.fullName);
                        this.currentChoose = "inputFullName";
                    }
                    else if(e.currentTarget.id == 'ip_inputProgram' && this.certificate_img.pos_program)
                    {
                        $('#sp_inputProgram').text(this.programName);
                        this.currentChoose = "inputProgram";
                    }
                }
            },
            SetShowTextFullName(){
                $('#ip_inputFullName').css('display', 'block');
                $('#ip_inputSizeFullName').css('display', 'block');
                if($('#ip_inputSizeFullName').val() !== ''){
                    this.coordinates.fullnameSize = $('#ip_inputSizeFullName').val();
                }
                else if(typeof(this.coordinates.fullnameSize) == 'undefined' || this.coordinates.fullnameSize === undefined){
                    this.coordinates.fullnameSize = 15;
                }

                $('#sp_inputFullName').css('font-size', this.coordinates.fullnameSize+'px');
                $('#sp_inputFullName').css('line-height', this.coordinates.fullnameSize+'px');
                $('#ip_inputSizeFullName').val(this.coordinates.fullnameSize);
            },
            SetShowTextProgram(){
                $('#ip_inputProgram').css('display', 'block');
                $('#ip_inputSizeProgram').css('display', 'block');
                if($('#ip_inputSizeProgram').val() !== ''){
                    this.coordinates.programSize = $('#ip_inputSizeProgram').val();
                }
                else if(typeof(this.coordinates.programSize) == 'undefined' || this.coordinates.programSize === undefined){
                    this.coordinates.programSize = 15;
                }

                $('#sp_inputProgram').css('font-size', this.coordinates.programSize+'px');
                $('#sp_inputProgram').css('line-height', this.coordinates.programSize+'px');
                $('#ip_inputSizeProgram').val(this.coordinates.programSize);
            },
        },
        mounted() {
            this.certificateData();
            this.setFileInput();

        }
    }
</script>

<style scoped>

  .spText{
    color: black;
    position: absolute;
    word-break: break-word;
  }
  #sp_inputDate{
    font-size: 12px;
  }
  #sp_inputFullName{
    font-size: 32px;
  }
  #sp_inputProgram{
    font-size: 28px;
  }
  .logo_img{
    width: 100px;
    height: 100px;
    position: absolute;
  }
  .txtChange, .txtNumber{
    display: none;
  }
  .txtNumber{
    padding: 3px;
  }
</style>
