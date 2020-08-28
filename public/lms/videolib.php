<?php
require_once('config.php');
require_once('vendor/autoload.php');

require_login();

$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/videolib.php');
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('my-index');
$PAGE->set_title("LIST VIDEOS LIBRARY");
$PAGE->set_heading($header);

echo $OUTPUT->header();

?>
    <div class="container" id="app">
        <div class="view-account">
            <section class="module">
                <div class="module-inner">
                    <div class="content-panel">
                        <div class="content-header-wrapper">
                            <h2 class="title"><i class="fa fa-video-camera"></i> VIDEOS LIBRARY</h2>
                        </div>
                        <div class="content-utilities">
                            <div class="page-nav">
                                <span class="indicator">Refresh:</span>
                                <div class="btn-group" role="group">
                                    <button class="active btn btn-default" data-toggle="tooltip" data-placement="bottom"
                                            title="" data-original-title="Grid View" id="refresh_page"
                                            @click="reloadPage('reload', 1)"><i class="fa fa-refresh"></i></button>
                                </div>
                            </div>
                            <div class="actions">
                                <div class="btn-group">
                                </div>
                            </div>
                        </div>
                        <div class="drive-wrapper drive-grid-view">
                            <div class="col-md-12">
                                <template v-for="(video,index) in videos">
                                    <div class="grid-items-wrapper">
                                        <div class="drive-item module text-center">
                                            <div class="drive-item-inner module-inner">
                                                <div class="drive-item-title"><a class="title-video" :title="video.name">{{ video.name }}</a></div>
                                                <div class="drive-item-thumb">
                                                    <a href=""><i class="fa fa-file-video-o text-warning"></i></a>
                                                </div>
                                            </div>
                                            <div class="drive-item-footer module-footer">
                                                <ul class="utilities list-inline">
                                                    <li><a data-toggle="tooltip" data-placement="top" title=""
                                                           data-original-title="Copy link"
                                                           @click="copyToClipboard(video.name)"><i
                                                                class="fa fa-external-link"></i></a></li>
                                                    <li><a data-toggle="tooltip" data-placement="top" title=""
                                                           data-original-title="Copy link"
                                                           @click="deleteVideo(video.name)"><i
                                                                class="fa fa-trash"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="col-md-12 d-flex">
                                <div class="pagination" v-if="totalPage > 1">
                                    <v-pagination
                                        v-model="current"
                                        :page-count="totalPage"
                                        :classes="bootstrapPaginationClasses"
                                        :labels="customLabels"
                                        @input="onPageChange"
                                    ></v-pagination>
                                </div>
                            </div>
                        </div>
                        <form id="form_upload_file" action="videolib.php" method="post" enctype="multipart/form-data">
                            Select file to upload:
                            <input accept="video/*" type="file" name="fileazure" ref="file" name="file"
                                   class="form-control" id="fileazure" multiple
                                   style="display: inline-block;width: 300px; margin: 10px;height: auto;">
                            <!-- <input type="submit" value="Upload file" name="submit"> -->
                            <button class="btn btn-success btnUpload" type="button" id="btnUpload"
                                    @click="uploadVideo"><i class="fa fa-plus"></i><i
                                    class="fa fa-spinner fa-spin"></i> Upload file
                            </button>
                            <span class="div-progress">{{percent}} %</span>
                            <!-- Progress bar -->
                            <!--                        <div class="div-progress">-->
                            <!--                            <progress :value="percent" max="95" style="width: 90%"> </progress> % {{percent}}-->
                            <!--                        </div>-->

                        </form>
                        <div class="notice">
                            <p style="font-style: italic; color: red">(*) Note: Uploading video to Azure will take time-based on the video size. Please wait ...</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

        <script src="/lms/theme/bgtlms/js/azure-storage-blob.min.js"></script>
<!--    <script src="/elearning-easia/public/lms/theme/bgtlms/js/azure-storage-blob.min.js"></script>-->
    <script type="text/javascript">
        Vue.component('v-pagination', window['vue-plain-pagination'])
        var app = new Vue({
            el: '#app',
            data: {
                bootstrapPaginationClasses: { // http://getbootstrap.com/docs/4.1/components/pagination/
                    ul: 'pagination',
                    li: 'page-item',
                    liActive: 'active',
                    liDisable: 'disabled',
                    button: 'page-link'
                },
                customLabels: {
                    first: false,
                    prev: '<',
                    next: '>',
                    last: false
                },
                url: '<?php echo $CFG->wwwroot; ?>',
                videos: [],
                additional_param: '?sv=2017-04-17&sr=c&si=746d77ad-7266-4fc9-a957-9bfbac043930&sig=nFSTSk7bpjBou7LVrrqETZdxWOfYXXq4%2Bkyp6mJ53U8%3D&st=2020-03-06T12%3A07%3A20Z&se=2120-03-06T12%3A07%3A20Z',
                current: 1,
                totalPage: 0,
                recordPerPage: 12,
                file: '',
                percent: 0,
                message: "",
                urlVideo: 'https://elearningdata.blob.core.windows.net/'
            },
            methods: {
                onPageChange: function () {
                    this.reloadPage('get', this.current);
                },
                reloadPage: function (type, page) {
                    var _this = this;
                    let currentType = type;
                    const params = new URLSearchParams();
                    if (type == 'reload')
                        type = 'get';
                    params.append('type', type || 'get');
                    params.append('current', page || this.current);
                    params.append('recordPerPage', this.recordPerPage);
                    axios({
                        method: 'post',
                        url: this.url + '/videolib_api.php',
                        data: params,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        }
                    })
                        .then(response => {
                            _this.videos = response.data.videos;
                            _this.totalPage = response.data.totalPage;
                            if (currentType == 'reload')
                                _this.current = 1;
                            _this.percent = 0;
                            _this.message = "";
                            _this.$refs.file.value = '';
                            //hide progress
                            $('.div-progress').css('display', 'none');
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },
                copyToClipboard: function (name) {
                    var _this = this;
                    let formData = new FormData();
                    formData.append('type', 'generate');
                    formData.append('nameFile', name);

                    axios.post(_this.url + '/videolib_api.php', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(response => {
                            var url = response.data.url;
                            var $temp = $("<input>");
                            $("body").append($temp);
                            $temp.val(url).select();
                            document.execCommand("copy");
                            $temp.remove();
                            alert("Copied to clipboard");
                            return false;
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },
                uploadVideo: function () {
                    var _this = this;
                    var file = this.$refs.file.files[0];
                    var validate = this.validateFile(file);
                    if(validate){
                        var sasToken = '?sv=2019-12-12&ss=bfqt&srt=sco&sp=rwdlacupx&se=2030-07-29T11:45:28Z&st=2020-07-29T03:45:28Z&spr=https&sig=GcW9fcjuCWEaql8U6pe%2FX%2FbRuY9T5OcQXBVifmZ4HnI%3D';
                        var containerURL = 'https://elearningdata.blob.core.windows.net/asset-f8418a8e-bf70-44d8-bba0-b4c3144d7dd6/';
                        const container = new azblob.ContainerURL(containerURL + sasToken, azblob.StorageURL.newPipeline(new azblob.AnonymousCredential));
                        try {
                            //show progress
                            $('.div-progress').css('display', 'inline');
                            $('#btnUpload').attr("disabled", true);
                            $('#btnUpload').addClass('loadding');
                            const blockBlobURL = azblob.BlockBlobURL.fromContainerURL(container, file.name);
                            var result = azblob.uploadBrowserDataToBlockBlob(
                                azblob.Aborter.none, file, blockBlobURL, {
                                    blockSize: 4 * 1024 * 1024, // 4MB block size
                                    parallelism: 20, // 20 concurrency
                                    progress: ev => _this.percent = Math.round((ev.loadedBytes / file['size']) * 100)
                                });

                            result.then(function (result) {
                                let formData = new FormData();
                                formData.append('type', 'put');
                                formData.append('nameFile', file.name);
                                axios.post(_this.url + '/videolib_api.php', formData, {
                                    headers: {
                                        'Content-Type': 'multipart/form-data'
                                    }
                                })
                                    .then(response => {
                                        if (response.data.result == 1) {
                                            _this.percent = 100;
                                            $('#btnUpload').removeAttr("disabled");
                                            $('#btnUpload').removeClass('loadding');
                                            toastr['success']("Uploaded videos successfully", "Success");
                                            _this.reloadPage('get');
                                        }
                                    })
                                    .catch(error => {
                                        _this.percent = 0;
                                        $('#btnUpload').removeAttr("disabled");
                                        $('#btnUpload').removeClass('loadding');
                                        toastr['error']("An error occurred, please try again later", "Error");
                                    });
                            }, function (err) {
                                _this.percent = 0;
                                $('#btnUpload').removeAttr("disabled");
                                $('#btnUpload').removeClass('loadding');
                                toastr['error']("An error occurred, please try again later", "Error");
                            });
                        } catch (error) {
                            console.log(error);
                        }
                    }
                },
                deleteVideo: function (name) {
                    var confirmDelete = confirm("Are you sure you want to delete this video?");
                    if (confirmDelete == true) {
                        var _this = this;
                        let formData = new FormData();
                        formData.append('type', 'delete');
                        formData.append('nameFile', name);
                        axios.post(_this.url + '/videolib_api.php', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        })
                            .then(response => {
                                if (response.data.result == 1) {
                                    toastr['success']("Delete video successfully", "Success");
                                    _this.reloadPage('get');
                                } else {
                                    toastr['error']("An error occurred, please try again later", "Error");
                                }
                            })
                            .catch(error => {
                                toastr['error']("An error occurred, please try again later", "Error");
                            });
                    }
                },
                sleep: function (ms) {
                    return new Promise(resolve => setTimeout(resolve, ms));
                },
                validateFile: function (file) {
                    //not selected file
                    if (!file) {
                        alert("Please choose a video file.");
                        return false;
                    }
                    //get variable
                    var name = file.name;
                    var size = file.size;
                    var ext = name.toLowerCase().split('.');
                    var fileExt = ext[ext.length - 1];
                    var extensions = ["webm", "mp4", "ogv"];
                    //validate
                    if (extensions.indexOf(fileExt) < 0) {
                        alert("Extension not allowed, please choose a video file.");
                        const input = this.$refs.file;
                        input.type = 'file';
                        this.$refs.file.value = '';
                        return false;
                    }

                    if (size > 1536715200) {
                        alert('Maximum file size of 1.5GB');
                        return false;
                    }
                    return true;
                }
            },
            mounted() {
                this.reloadPage('get');
            }
        });
    </script>
    <style scoped>
        .view-account .content-panel {
            min-height: auto !important;
        }

        .utilities li:hover {
            cursor: pointer;
        }

        .div-progress {
            display: none;
        }

        .fa-spin {
            -webkit-animation: fa-spin 2s infinite linear;
            animation: fa-spin 2s infinite linear
        }

        .fa-pulse {
            -webkit-animation: fa-spin 1s infinite steps(8);
            animation: fa-spin 1s infinite steps(8)
        }

        @-webkit-keyframes fa-spin {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg)
            }
            100% {
                -webkit-transform: rotate(359deg);
                transform: rotate(359deg)
            }
        }

        @keyframes fa-spin {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg)
            }
            100% {
                -webkit-transform: rotate(359deg);
                transform: rotate(359deg)
            }
        }

        .btnUpload .fa-spin {
            display: none;
        }

        .loadding .fa-spin {
            display: inherit !important;
        }

        .loadding .fa-plus {
            display: none !important;
        }

        .title-video:hover{
            cursor: pointer;
        }
        .utilities{
            display: inline-flex;
        }
        .utilities li{
            margin: 0 10px;
        }
    </style>
    </body>

<?php

echo $OUTPUT->footer();
