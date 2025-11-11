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
                            <router-link to="/tms/question/list">{{ trans.get('keys.danh_sach_cau_hoi') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.sua_cau_hoi') }}</li>
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
                                    class="fal fa-plus mr-3"></i>{{trans.get('keys.sua_cau_hoi')}}</a>
                        </div>
                        <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <form action="" class="form-row">
                                            <div class="col-4 form-group">
                                                <label for="survey_id">{{trans.get('keys.danh_sach_survey')}} *</label>
                                                <select v-model="question.survey_id" class="form-control"
                                                        id="survey_id">
                                                    <option value="">{{trans.get('keys.chon_survey')}}</option>
                                                    <option v-for="sur in surveys" :value="sur.id">
                                                        {{sur.code}} - {{sur.name}}
                                                    </option>
                                                </select>
                                                <label v-if="!question.survey_id"
                                                       class="required text-danger survey_id_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <div class="col-4 form-group">
                                                <label for="type_question">{{trans.get('keys.chon_loai_cau_hoi')}}
                                                    *</label>
                                                <select v-model="question.type_question" class="form-control"
                                                        @change="onChange()"
                                                        id="type_question">
                                                    <option value="">{{trans.get('keys.chon_loai_cau_hoi')}}</option>
                                                    <option value="multiplechoice">
                                                        {{trans.get('keys.lua_chon_dap_an')}}
                                                    </option>
                                                    <option value="ddtotext">{{trans.get('keys.dien_cau_tra_loi')}}
                                                    </option>
                                                    <option value="group">{{trans.get('keys.cau_hoi_nhom')}}</option>
                                                    <option value="minmax">{{trans.get('keys.cau_hoi_min_max')}}
                                                    </option>
                                                    <option value="checkbox">{{trans.get('keys.cau_hoi_checkbox')}}
                                                    </option>
                                                </select>
                                                <label v-if="!question.type_question"
                                                       class="required text-danger type_question_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-4 form-group">
                                                <label for="inputText1-2">{{trans.get('keys.ten_cau_hoi')}} *</label>
                                                <input v-model="question.name" type="text" id="inputText1-2"
                                                       :placeholder="trans.get('keys.nhap_ma')"
                                                       class="form-control mb-4">
                                                <label v-if="!question.name"
                                                       class="required text-danger question_name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-12 form-group">
                                                <label for="inputText1-2">{{trans.get('keys.noi_dung_cau_hoi')}}
                                                    (*)</label>

                                                <ckeditor v-model="question.content"
                                                          :config="editorConfig"></ckeditor>
                                                <label v-if="!question.content"
                                                       class="required text-danger question_content_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                        </form>
                                        <div v-if="question.type_question=='multiplechoice' || question.type_question=='group' || question.type_question=='checkbox'"
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

                                        <div v-if="question.type_question=='minmax'" id="minmax-question">
                                            <div class="row">
                                                <div class="col-12 col-lg-12">
                                                    <form action="" class="form-row">
                                                        <div class="col-4 form-group">
                                                            <label for="inputText1-2">{{trans.get('keys.gia_tri_min')}}
                                                                *</label>
                                                            <input v-model="question.min_value" type="number" min="0"
                                                                   :placeholder="trans.get('keys.nhap_ma')"
                                                                   class="form-control mb-4">
                                                            <label v-if="!question.min_value"
                                                                   class="required text-danger question_name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>

                                                        <div class="col-4 form-group">
                                                            <label for="inputText1-2">{{trans.get('keys.gia_tri_max')}}
                                                                *</label>
                                                            <input v-model="question.max_value" type="number" min="0"
                                                                   :placeholder="trans.get('keys.nhap_ma')"
                                                                   class="form-control mb-4">
                                                            <label v-if="!question.max_value"
                                                                   class="required text-danger question_name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>

                                                    </form>


                                                </div>

                                            </div>

                                        </div>
                                        <br/>

                                        <div v-if="question.type_question=='group' || question.type_question=='minmax'"
                                             id="group-question">
                                            <div class="button-list" style="text-align: center;">
                                                <h5>{{trans.get('keys.cau_hoi_con')}}</h5>
                                                <div class="col-md-12 margin_buttom_0">
                                                    <hr style="border-top: 2px solid #3a55b1; padding-left: 0px !important; padding-right: 0px !important;
                                            padding-top: 20px;">
                                                </div>
                                            </div>

                                            <div v-for="ques in question_childs"
                                                 :key="ques.index">
                                                <question-child :question="ques"
                                                                :lstQuestion="question_childs"></question-child>
                                            </div>

                                            <div class="button-list" style="text-align: center;">
                                                <button @click="addQuestionChild()" type="button"
                                                        class="btn-sm btn-success">
                                                    {{trans.get('keys.them_cau_hoi_con')}}
                                                </button>
                                            </div>
                                        </div>
                                        <br/>


                                        <div class="button-list">
                                            <button @click="updateQuestion()" type="button" class="btn btn-primary">
                                                {{trans.get('keys.sua')}}
                                            </button>
                                            <router-link :to="{name:'QuestionIndex', params:{back_page:'1'}}" class="btn btn-secondary">
                                                {{trans.get('keys.huy')}}
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
    import QuestionChild from "./template/QuestionChildComponent";
    import AnswerAdd from "./template/AnswerComponent";

    export default {
        props: ['ques_id'],
        components: {QuestionChild, AnswerAdd},
        data() {
            return {
                survey_id: '',
                surveys: [],
                question: {
                    min_value: 0,
                    max_value: 0
                },
                anwsers: [],
                index_anwser: 0,
                question_childs: [],
                index_ques_child: 0,
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
            }
        },
        methods: {
            firstLoadLayout() {
                if (this.question.type_question === 'ddtotext') { // TH la cau hoi dien dap an
                    $('#multiple-answer').hide();
                    $('#group-question').hide();
                } else if (this.question.type_question === 'multiplechoice') {
                    $('#multiple-answer').show();
                    $('#group-question').hide();
                } else {
                    $('#multiple-answer').show();
                    $('#group-question').show();
                }
            },
            getSurveys() {
                axios.get('/api/question/listsurvey')
                    .then(response => {
                        this.surveys = response.data;

                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getQuestionDetail() {
                axios.get('/api/question/detail/' + this.ques_id)
                    .then(response => {
                        this.question = response.data;

                        if (this.question.type_question === 'ddtotext') { // TH la cau hoi dien dap an
                            // $('#multiple-answer').hide();
                            // $('#group-question').hide();
                        } else if (this.question.type_question === 'multiplechoice' || this.question.type_question === 'checkbox') {
                            // $('#multiple-answer').show();
                            // $('#group-question').hide();
                            this.getAnswerQuestions();
                        } else if (this.question.type_question === 'minmax') {
                            // $('#multiple-answer').show();
                            // $('#group-question').show();
                            let other_data = response.data.other_data;
                            other_data = JSON.parse(other_data);

                            this.question.max_value = other_data.max;
                            this.question.min_value = other_data.min;

                            this.getAnswerQuestions();
                            this.getQuestionChilds();
                        } else {
                            this.getAnswerQuestions();
                            this.getQuestionChilds();
                        }

                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onChange() {
                this.firstLoadLayout();
            },
            getAnswerQuestions() {
                axios.get('/api/question/getlstanswer/' + this.ques_id)
                    .then(response => {
                        var lstAnswer = response.data;
                        var count_ans = lstAnswer.length;
                        if (count_ans > 0) {
                            this.index_anwser = count_ans;
                            for (var i = 0; i < count_ans; i++) {
                                var anwser = {index: i + 1, content: lstAnswer[i].content};
                                this.anwsers.push(anwser);
                            }
                        }
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getQuestionChilds() {
                axios.get('/api/question/getlstquestionchild/' + this.ques_id)
                    .then(response => {
                        var lstQuestionChilds = response.data;
                        var count_ques = lstQuestionChilds.length;
                        if (count_ques > 0) {
                            this.index_ques_child = count_ques;
                            for (var i = 0; i < count_ques; i++) {
                                var anwser = {index: i + 1, content: lstQuestionChilds[i].content};
                                this.question_childs.push(anwser);
                            }
                        }
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            addQuestionChild() {
                this.index_ques_child++;
                var question = {index: this.index_ques_child, content: ''};
                this.question_childs.push(question);
            },
            addAnswer() {
                this.index_anwser++;
                var anwser = {index: this.index_anwser, content: ''};
                this.anwsers.push(anwser);

            },
            updateQuestion() {

                if (!this.question.survey_id) {
                    $('.survey_id_required').show();
                    return;
                }

                if (!this.question.type_question) {
                    $('.type_question_required').show();
                    return;
                }

                if (!this.question.name) {
                    $('.question_name_required').show();
                    return;
                }

                //  var editor_data = CKEDITOR.instances.article_ckeditor.getData();

                if (!this.question.content) {
                    $('.question_content_required').show();
                    return;
                }

                if (this.question.type_question === 'multiplechoice' && this.anwsers.length === 0) {
                    toastr['warning'](this.trans.get('keys.ban_chua_nhap_cau_tra_loi'), this.trans.get('keys.thong_bao'));

                    return;
                }

                if (this.question.type_question === 'checkbox' && this.anwsers.length === 0) {
                    toastr['warning'](this.trans.get('keys.ban_chua_nhap_cau_tra_loi'), this.trans.get('keys.thong_bao'));
                    return;
                }

                if (this.question.type_question === 'group' && (this.question_childs.length === 0 || this.anwsers.length === 0)) {
                    toastr['warning'](this.trans.get('keys.ban_chua_nhap_cau_tra_loi_hoac_cau_hoi_con'), this.trans.get('keys.thong_bao'));

                    return;
                }

                if (this.question.type_question === 'minmax' && this.question.min_value > this.question.max_value) {
                    toastr['warning'](this.trans.get('keys.gia_tri_ban_nhap_khong_hop_le'), this.trans.get('keys.thong_bao'));
                    return;
                }

                if (this.question.type_question === 'minmax' && this.question_childs.length === 0) {
                    toastr['warning'](this.trans.get('keys.ban_chua_nhap_cau_tra_loi_hoac_cau_hoi_con'), this.trans.get('keys.thong_bao'));
                    return;
                }

                let current_pos = this;
                axios.post('/api/question/update/' + this.ques_id, {
                    survey_id: this.question.survey_id,
                    type_question: this.question.type_question,
                    question_name: this.question.name,
                    question_content: this.question.content,
                    anwsers: this.anwsers,
                    question_childs: this.question_childs,
                    min_value: this.question.min_value,
                    max_value: this.question.max_value
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            this.$router.push({name: 'QuestionIndex', params:{back_page:'1'}});

                        } else {
                            toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                        }

                    })
                    .catch(error => {
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });
            }
        },
        mounted() {
            this.getSurveys();
            this.getQuestionDetail();
        }
    }
</script>


<style scoped>

</style>
