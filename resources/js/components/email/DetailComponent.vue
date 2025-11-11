<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
            </li>
            <li class="breadcrumb-item">
              <router-link :to="{ name: 'EmailTemplateIndex' }">
                {{ trans.get('keys.danh_sach_email_template') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_email_template') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="row mx-0">
        <div class="col-12 hk-sec-wrapper">
          <h5 class="hk-sec-title">{{trans.get('keys.chinh_sua_email_template')}}: {{name_show}}</h5>
          <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
            <div class="card-body">
              <div class="col-12 col-lg-12">
                <form action>
                  <div class="col-12 form-group">
                    <label>{{trans.get('keys.mo_ta')}} <span style="color: red">{{trans.get('keys.chu_y_chi_sua_chu_nhung_doan_van_co_dang_vi_du_fullname_username_khong_duoc_chinh_sua_hoac_xoa')}}</span></label>
                    <ckeditor v-model="content_html" :config="editorConfig"></ckeditor>
                  </div>

                  <div class="button-list">
                    <button @click="editEmailTemplate()" type="button" class="btn btn-primary">
                      {{trans.get('keys.sua')}}
                    </button>
                    <button type="button" @click="goBack()" class="btn btn-secondary">
                      {{trans.get('keys.huy')}}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


</template>


<script>

  import CKEditor from 'ckeditor4-vue';


  export default {
    props: ["name_file"],
    components: {
      CKEditor
    },
    data() {
      return {
        content_html: '',
        json_content: {},
        name_show: '',
        editorConfig: {
          filebrowserUploadMethod: 'form', //fix for response when uppload file is cause filetools-response-error
          // The configuration of the editor.
          //add responseType=json for original version of ckeditor 4, else cause filetools-response-error
          filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
          filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content'),
          filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
          filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content'),
          versionCheck: false
        }
      };
    },
    methods: {
      getTemplate() {
        //get content html of file
        axios
          .get("/email_template/getContentFile/" + this.name_file)
          .then(response => {
            this.json_content = JSON.parse(response.data.content);
            this.name_show = response.data.name_show;
            switch (this.name_file) {

              case 'enrol':
                this.content_html = this.json_content.enrol;
                break;
              case 'suggest':
                this.content_html = this.json_content.suggest;
                break;
              case 'remind_certificate':
                this.content_html = this.json_content.remind_certificate;
                break;
              case 'assigned_competency':
                this.content_html = this.json_content.assigned_competency;
                break;
              case 'remind_exam':
                this.content_html = this.json_content.remind_exam;
                break;
              case 'invitation_offline_course':
                this.content_html = this.json_content.invitation_offline_course;
                break;
              case 'remind_expire_required_course':
                this.content_html = this.json_content.remind_expire_required_course;
                break;
              case 'forgot_password':
                this.content_html = this.json_content.forgot_password;
                break;
              case 'invite_student':
                this.content_html = this.json_content.invite_student;
                break;
              case 'completed_competency_framework':
                this.content_html = this.json_content.completed_competency_framework;
                break;
              case 'calculate_toeic_grade':
                this.content_html = this.json_content.calculate_toeic_grade;
                break;
              case 'request_more_attempt':
                this.content_html = this.json_content.request_more_attempt;
                break;
              case 'fail_exam':
                this.content_html = this.json_content.fail_exam;
                break;
              case 'retake_exam':
                this.content_html = this.json_content.retake_exam;
                break;
              default:
                break;
            }
          })
          .catch(error => {
            console.log(error.response);
          });
      },
      editEmailTemplate() {

        this.formData = new FormData();

        this.formData.append("name_file", this.name_file);

        this.formData.append("editor_data", this.content_html);

        this.formData.append("type", "ckeditor");

        axios.post(
          "/email_template/detail/update",
          this.formData,
          {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          }
        )
          .then(response => {
            if (response.data.status) {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
              this.$router.push({name: 'EmailTemplateIndex'});
            } else {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          });
      },
      goBack() {
        this.$router.push({name: 'EmailTemplateIndex'});
      }
    },
    mounted() {
      this.getTemplate();
    }
  };
</script>

<style scoped>
</style>
