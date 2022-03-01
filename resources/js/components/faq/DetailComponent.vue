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
              <router-link :to="{ name: 'FaqIndex' }">
                {{ trans.get('keys.danh_sach_faq') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_faq') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="row mx-0">
        <div class="col-12 hk-sec-wrapper">
          <h5 class="hk-sec-title">{{trans.get('keys.chinh_sua_faq')}}</h5>
          <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
            <div class="card-body">
              <div class="col-12 col-lg-12">
                <form>

                  <div class="col-sm-12 form-group">
                      <label><strong>{{trans.get('keys.ten_faq')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" id="faq_name" class="form-control form-control-line" :placeholder="trans.get('keys.ten')+' *'" required v-model="faq.name">
                      </div>
                      <label v-if="!faq.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                  </div>

                  <div class="col-12 form-group">
                    <label>{{trans.get('keys.mo_ta')}}</label>
                    <ckeditor v-model="faq.content" :config="editorConfig"></ckeditor>
                  </div>

                  <div class="col-12 form-group">
                    <label>{{trans.get('keys.tab')}}</label>
                    <select v-model="faq.tab_id" class="form-control" id="tab_id">
                      <option value="0">{{ trans.get('keys.chon_tab') }}</option>
                      <option v-for="(tab,index) in tab_list" :value="tab.id">
                        {{ tab.name }}
                      </option>
                    </select>
                  </div>

                  <div class="col-md-12 form-group">
                    <label for="is_active">{{trans.get('keys.trang_thai')}}</label>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="is_active" :checked="parseInt(faq.is_active) === 1" v-model="faq.is_active">
                      <label v-if="parseInt(faq.is_active) === 1" class="custom-control-label" for="is_active">Yes</label>
                      <label v-else class="custom-control-label" for="is_active">No</label>
                    </div>
                  </div>

                  <div class="button-list">
                    <button @click="editFaq()" type="button" class="btn btn-primary">{{trans.get('keys.sua')}}</button>
                    <button type="button" @click="goBack()" class="btn btn-secondary">{{trans.get('keys.huy')}}</button>
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
    props: ["id"],
    components: {
      CKEditor
    },
    data() {
      return {
        faq:{
          name:'',
          tab_id: 0,
          content: '',
          is_active: 1
        },
        tab_list: [],
        editorConfig: {
          filebrowserUploadMethod: 'form', //fix for response when uppload file is cause filetools-response-error
          // The configuration of the editor.
          //add responseType=json for original version of ckeditor 4, else cause filetools-response-error
          filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
          filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content'),
          filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
          filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content')
        }
      };
    },
    methods: {
      listTab() {
        axios.get('/api/faq_tab/list')
          .then(response => {
            this.tab_list = response.data;
          })
          .catch(error => {
            console.log(error);
          });
      },
      getFaq() {
        //get content html of file
        axios
          .get("/api/faq/detail/" + this.id)
          .then(response => {
            this.faq = response.data;
          })
          .catch(error => {
            console.log(error);
          });
      },
      editFaq() {

        let current_pos = this;

        if(!this.faq.name){
          $('.name_required').show();
          return;
        }

        //var is_toeic = this.course.is_toeic ? 1 : 0;

        this.formData = new FormData();
        this.formData.append("id", this.id);
        this.formData.append("name", this.faq.name);
        this.formData.append("content", this.faq.content);
        this.formData.append("tab_id", this.faq.tab_id);
        this.formData.append("is_active", this.faq.is_active ? 1 : 0);
        this.formData.append("type", "ckeditor");
        axios.post(
          "/api/faq/update",
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
              current_pos.goBack();
            } else {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          });
      },
      goBack() {
        this.$router.push({name: 'FaqIndex'});
      }
    },
    mounted() {
      this.listTab();
      this.getFaq();
    }
  };
</script>

<style scoped>
</style>
