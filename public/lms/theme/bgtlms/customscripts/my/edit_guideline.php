<?php

/**
 * Page guideline for vietlot user
 *
 * [VinhPT]
 */

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/my/lib.php');

require_login();
global $DB, $USER;
// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_heading($header);
$PAGE->set_title(get_string('guideline'));
//echo $CFG->dirroot.'/../../';
//die;
//
$sql = "select content from tms_configs where target = 'guideline'";
$guide_line = array_values($DB->get_records_sql($sql))[0]->content;

echo $OUTPUT->header();
?>

<style>
    iframe, .content {
        width: 100%;
        /*height: calc(100vh - 100px) !important;*/
    }
    .cke_contents{
        height: calc(100vh - 100px) !important;
    }

    /*.content {*/
    /*    border-width: 2px;*/
    /*    border-style: inset;*/
    /*    border-color: initial;*/
    /*    border-image: initial;*/
    /*    background-color: #fff;*/
    /*}*/
</style>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="../../js/ckeditor.js"></script>
<p></p>
<div id="app">
    <div class="content mb-2">
        <ckeditor v-model="content" :config="editorConfig"></ckeditor>
    </div>
    <p align="right">
        <button type="button" class="btn btn-info py-2 px-3" style="font-size: 20px"
                @click="updateGuideLineContent">Update</button>
    </p>
</div>

<script>

    Vue.use( CKEditor );

    var app = new Vue({
        el: '#app',
        name: "Ckeditor",
        data: {
            editorConfig: {
            },
            content: '',
            type: 'get',
            url: '<?php echo $CFG->wwwroot; ?>',
        },
        methods: {
            getGuideLineContent: function () {
                var _this = this;

                let formData = new FormData();
                formData.append('type', this.type);

                axios.post(this.url + '/pusher/guideline.php', formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        _this.content = response.data.content;
                    })
                    .catch(error => {
                        console.log("Error ", error);
                    });
            },
            updateGuideLineContent: function(){
                var _this = this;
                let formData = new FormData();
                formData.append('type', 'update');
                formData.append('content', this.content);

                axios.post(this.url + '/pusher/guideline.php', formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        alert(response.data.msg);
                    })
                    .catch(error => {
                        console.log("Error ", error);
                    });
            }
        },
        mounted()
        {
            this.getGuideLineContent();
        }
    });

    CKEDITOR.editorConfig = function( config ) {
        config.toolbarGroups = [
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
            { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
            { name: 'forms', groups: [ 'forms' ] },
            '/',
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
            { name: 'links', groups: [ 'links' ] },
            { name: 'insert', groups: [ 'insert' ] },
            '/',
            { name: 'styles', groups: [ 'styles' ] },
            { name: 'colors', groups: [ 'colors' ] },
            { name: 'tools', groups: [ 'tools' ] },
            { name: 'others', groups: [ 'others' ] },
            { name: 'about', groups: [ 'about' ] }
        ];
    };
</script>


<?php
echo $OUTPUT->footer();
die;
?>
