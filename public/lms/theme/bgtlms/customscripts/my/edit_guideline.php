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
$PAGE->set_title(get_string('edit_guideline'));
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
</style>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="../../js/ckeditor/ckeditor.js"></script>
<p></p>
<div id="app">
    <div class="content mb-2">
        <ckeditor v-model="content" :config="editorConfig" :editor-url="editorUrl"></ckeditor>
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
            editorUrl: "<?php echo $CFG->wwwtmsbase; ?>js/ckeditor/ckeditor-full.js",
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

</script>


<?php
echo $OUTPUT->footer();
die;
?>
