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
                            <router-link to="/tms/selfquestion/list">{{ trans.get('keys.danh_sach_cau_hoi_self') }}
                            </router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.tao_moi_cau_hoi') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm">
                <div class="accordion" id="accordion_1">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <a role="button" data-toggle="collapse" href="#collapse_1"
                               aria-expanded="true"><i
                                    class="fal fa-plus mr-3"></i>{{trans.get('keys.tao_moi_cau_hoi')}}</a>
                        </div>
                        <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <form action="" class="form-row">
                                            <div class="col-4 form-group">
                                                <label for="survey_id">{{trans.get('keys.danh_sach_self')}} *</label>
                                                <select v-model="survey_id" class="form-control" id="survey_id">
                                                    <option value="">{{trans.get('keys.chon_self')}}</option>
                                                    <option v-for="sur in surveys" :value="sur.id">
                                                        {{sur.code}} - {{sur.name}}
                                                    </option>
                                                </select>
                                                <label v-if="!survey_id"
                                                       class="required text-danger survey_id_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <!--                                            <div class="col-4 form-group">-->
                                            <!--                                                <label for="type_question">{{trans.get('keys.chon_loai_cau_hoi_self')}}-->
                                            <!--                                                    *</label>-->
                                            <!--                                                <select v-model="type_question" class="form-control"-->
                                            <!--                                                        @change="onChange()"-->
                                            <!--                                                        id="type_question">-->
                                            <!--                                                    <option value="">{{trans.get('keys.chon_loai_cau_hoi_self')}}-->
                                            <!--                                                    </option>-->
                                            <!--                                                    <option value="group">{{trans.get('keys.cau_hoi_nhom')}}</option>-->
                                            <!--                                                    <option value="minmax">{{trans.get('keys.cau_hoi_min_max')}}-->
                                            <!--                                                    </option>-->

                                            <!--                                                </select>-->
                                            <!--                                                <label v-if="!type_question"-->
                                            <!--                                                       class="required text-danger type_question_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                                            <!--                                            </div>-->

                                            <div class="col-4 form-group">
                                                <label for="inputText1-2">{{trans.get('keys.ten_cau_hoi')}} *</label>
                                                <input v-model="question_name" type="text" id="inputText1-2"
                                                       :placeholder="trans.get('keys.nhap_ques_name')"
                                                       class="form-control mb-4">
                                                <label v-if="!question_name"
                                                       class="required text-danger question_name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-12 form-group">
                                                <label for="inputText1-2">{{trans.get('keys.noi_dung_cau_hoi')}}
                                                    (*)</label>
                                                <ckeditor v-model="question_content"
                                                          :config="editorConfig"></ckeditor>
                                                <label v-if="!question_content"
                                                       class="required text-danger question_content_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                        </form>
                                        <div v-if="type_question==='group'"
                                             id="multiple-answer">

                                            <div class="button-list" style="text-align: center;">
                                                <h5>{{trans.get('keys.phan_tra_loi')}}</h5>
                                                <div class="col-md-12 margin_buttom_0">
                                                    <hr style="border-top: 2px solid #3a55b1; padding-left: 0px !important; padding-right: 0px !important;
                                            padding-top: 20px;">
                                                </div>
                                            </div>

                                            <div v-for="anw in anwsers"
                                                 :key="anw.index">
                                                <answer-add :answer="anw" :lstAnswer="anwsers"></answer-add>
                                            </div>

                                            <div class="button-list" style="text-align: center;">
                                                <button @click="addAnswer()" type="button" class="btn-sm btn-success">
                                                    {{trans.get('keys.them_dap_an')}}
                                                </button>
                                            </div>
                                        </div>
                                        <br/>
                                        <div v-if="type_question==='minmax'" id="minmax-question">
                                            <div class="row">
                                                <div class="col-12 col-lg-12">
                                                    <form action="" class="form-row">
                                                        <div class="col-4 form-group">
                                                            <label for="inputText1-2">{{trans.get('keys.gia_tri_min')}}
                                                                *</label>
                                                            <input v-model="min_value" type="number" min="1" max="5"
                                                                   :placeholder="trans.get('keys.nhap_ma')"
                                                                   class="form-control mb-4">
                                                            <label v-if="!min_value"
                                                                   class="required text-danger question_name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>

                                                        <div class="col-4 form-group">
                                                            <label for="inputText1-2">{{trans.get('keys.gia_tri_max')}}
                                                                *</label>
                                                            <input v-model="max_value" type="number" min="1" max="5"
                                                                   :placeholder="trans.get('keys.nhap_ma')"
                                                                   class="form-control mb-4">
                                                            <label v-if="!max_value"
                                                                   class="required text-danger question_name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>

                                                    </form>
                                                </div>

                                            </div>

                                        </div>
                                        <br/>
                                        <div v-if="type_question==='minmax'">
                                            <div class="button-list" style="text-align: center;">
                                                <h5>{{trans.get('keys.cau_hoi_con')}}</h5>
                                                <div class="col-md-12 margin_buttom_0">
                                                    <hr style="border-top: 2px solid #3a55b1; padding-left: 0px !important; padding-right: 0px !important;
                                            padding-top: 20px;">
                                                </div>
                                            </div>

                                            <div v-for="ques in question_childs"
                                                 :key="ques.index">
                                                <question-child-min-max :question="ques"
                                                                        :lstQuestion="question_childs"
                                                                        :editorConfig="editorConfig"></question-child-min-max>
                                            </div>

                                            <div class="button-list" style="text-align: center;">
                                                <button @click="addQuestionChildMinMax()" type="button"
                                                        class="btn-sm btn-success">
                                                    {{trans.get('keys.them_cau_hoi_con')}}
                                                </button>
                                            </div>
                                        </div>
                                        <br/>
                                        <div v-if="type_question==='group'"
                                             id="group-question">
                                            <div class="button-list" style="text-align: center;">
                                                <h5>{{trans.get('keys.cau_hoi_con')}}</h5>
                                                <div class="col-md-12 margin_buttom_0">
                                                    <hr style="border-top: 2px solid #3a55b1; padding-left: 0px !important; padding-right: 0px !important;
                                            padding-top: 20px;">
                                                </div>
                                            </div>

                                            <div v-for="section in group_sections"
                                                 :key="section.index">
                                                <!--                                                <question-child :grSec="grSec"-->
                                                <!--                                                                :group_sections="group_sections"></question-child>-->
                                                <question-section :section="section"
                                                                  :lstSection="group_sections"></question-section>
                                            </div>

                                            <div class="button-list" style="text-align: center;">
                                                <button @click="addQuestionChild()" type="button"
                                                        class="btn-sm btn-success">
                                                    {{trans.get('keys.them_section')}}
                                                </button>
                                            </div>
                                        </div>

                                        <div class="button-list">
                                            <button @click="createQuestion()" type="button"
                                                    class="btn btn-primary btn-sm">
                                                {{trans.get('keys.tao')}}
                                            </button>
                                            <router-link to="/tms/selfquestion/list" class="btn btn-secondary btn-sm">
                                                {{trans.get('keys.quay_lai')}}
                                            </router-link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>

<script>
    import AnswerAdd from "./template/AnswerSelfComponent";
    import QuestionChildMinMax from "./template/QuestionChildMinMaxComponent"
    import QuestionSection from "./template/QuestionSectionComponent"

    export default {
        props: ['self_id'],
        components: {AnswerAdd, QuestionChildMinMax, QuestionSection},
        data() {
            return {
                survey_id: '',
                surveys: [],
                type_question: 'group',
                display: '1',
                question_name: '',
                question_content: '',
                anwsers: [],
                index_anwser: 0,
                group_sections: [],
                question_childs: [],
                index_ques_child_mimax: 0,
                index_ques_child: 0,
                min_value: 0,
                max_value: 0,
                editorConfig: {
                    filebrowserUploadMethod: 'form', //fix for response when uppload file is cause filetools-response-error
                    // The configuration of the editor.
                    //add responseType=json for original version of ckeditor 4, else cause filetools-response-error
                    filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                    filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
                    filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content')
                }
            }
        },
        methods: {
            getSurveys() {
                axios.get('/api/selfquestion/listself')
                    .then(response => {
                        this.surveys = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            addQuestionChild() {
                this.index_ques_child++;
                var section = {index: this.index_ques_child, sec_name: '', lst_child_question: []};
                this.group_sections.push(section);
            },
            addQuestionChildMinMax() {
                this.index_ques_child_mimax++;
                var ques = {index: this.index_ques_child_mimax, content: ''};
                this.question_childs.push(ques);
            },
            addAnswer() {
                this.index_anwser++;
                var anwser = {index: this.index_anwser, content: '', point: ''};
                this.anwsers.push(anwser);

            },
            onChange() {

            },
            createQuestion() {

                if (!this.survey_id) {
                    $('.survey_id_required').show();
                    return;
                }

                if (!this.type_question) {
                    $('.type_question_required').show();
                    return;
                }

                if (!this.question_name) {
                    $('.question_name_required').show();
                    return;
                }

                if (!this.question_content) {
                    $('.question_content_required').show();
                    return;
                }

                if (this.type_question === 'group' && (this.group_sections.length === 0 || this.anwsers.length === 0)) {
                    toastr['warning'](this.trans.get('keys.ban_chua_nhap_cau_tra_loi_hoac_cau_hoi_con'), this.trans.get('keys.thong_bao'));
                    return;
                }

                if (this.type_question === 'minmax' && this.min_value > this.max_value) {
                    toastr['warning'](this.trans.get('keys.gia_tri_ban_nhap_khong_hop_le'), this.trans.get('keys.thong_bao'));
                    return;
                }

                if (this.type_question === 'minmax' && this.question_childs.length === 0) {
                    toastr['warning'](this.trans.get('keys.ban_chua_nhap_cau_tra_loi_hoac_cau_hoi_con'), this.trans.get('keys.thong_bao'));
                    return;
                }

                let current_pos = this;
                let loader = $('.preloader-it');
                loader.fadeIn();
                axios.post('/api/selfquestion/create', {
                    survey_id: this.survey_id,
                    type_question: this.type_question,
                    question_name: this.question_name,
                    question_content: this.question_content,
                    anwsers: this.anwsers,
                    group_sections: this.group_sections,
                    question_childs: this.question_childs,
                    min_value: this.min_value,
                    max_value: this.max_value
                })
                    .then(response => {
                        loader.fadeOut();
                        if (response.data.status) {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            this.$router.push({name: 'SelfQuestionIndex'});

                        } else {
                            toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                        }

                    })
                    .catch(error => {
                        loader.fadeOut();
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });
            }
        },
        mounted() {
            if (this.self_id != 'off') {
                this.survey_id = this.self_id;
            }
            this.getSurveys();
        }
    }
</script>


<style scoped>

</style>
