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
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" v-model="searchQuery" placeholder="Search by name or type...">
                                </div>
                            </div>
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
                                <table class="table table-hover video-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">#</th>
                                            <th @click="sortBy('name')" style="cursor:pointer;">
                                                File Name
                                                <i v-if="sortKey==='name'" :class="sortAsc ? 'fa fa-sort-alpha-asc' : 'fa fa-sort-alpha-desc'"></i>
                                            </th>
                                            <th @click="sortBy('type')" style="cursor:pointer;">
                                                File Type
                                                <i v-if="sortKey==='type'" :class="sortAsc ? 'fa fa-sort-alpha-asc' : 'fa fa-sort-alpha-desc'"></i>
                                            </th>
                                            <th @click="sortBy('upload_time')" style="cursor:pointer;">
                                                Upload Time
                                                <i v-if="sortKey==='upload_time'" :class="sortAsc ? 'fa fa-sort-alpha-asc' : 'fa fa-sort-alpha-desc'"></i>
                                            </th>
                                            <th style="width: 140px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(video, index) in filteredVideos" :key="video.name">
                                            <td>{{ index + 1 }}</td>
                                            <td>
                                                <span class="video-title" :title="video.name" v-html="highlightedName(video.name)"></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info text-uppercase">{{ video.name.split('.').pop() }}</span>
                                            </td>
                                            <td>
                                                <span class="upload-time">{{ formatDate(video.upload_time) }}</span>
                                            </td>
                                            <td>
                                                <a href="#" class="mx-2" data-toggle="tooltip" title="Copy link" @click.prevent="copyToClipboard(video.name, 'link')">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                                <a v-if="video.stream_link" href="#" class="mx-2" data-toggle="tooltip" title="Copy stream link" @click.prevent="copyToClipboard(video.stream_link, 'stream_link')">
                                                    <i class="fa fa-play"></i>
                                                </a>
                                                <a href="#" class="mx-2 text-danger" data-toggle="tooltip" title="Delete" @click.prevent="deleteVideo(video.name, video.stream_link)">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
                                    :disabled="isUploading" @click="uploadVideo">
                                <i class="fa fa-plus"></i><i class="fa fa-spinner fa-spin"></i> Upload file
                            </button>
                            <div class="div-progress" v-show="isUploading" style="display:inline-block; min-width:200px; vertical-align:middle;">
                                <div class="progress" style="height: 20px; margin-bottom: 0;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" :style="{width: percent + '%'}" :aria-valuenow="percent" aria-valuemin="0" aria-valuemax="100">{{ percent }}%</div>
                                </div>
                            </div>

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
                recordPerPage: 10,
                file: '',
                percent: 0,
                message: "",
                urlVideo: 'https://elearningdata.blob.core.windows.net/',
                searchQuery: '',
                sortKey: 'name',
                sortAsc: true,
                isUploading: false,
            },
            computed: {
                filteredVideos() {
                    let filtered = this.videos.filter(video => {
                        const name = video.name ? video.name.toLowerCase() : '';
                        const type = video.name ? video.name.split('.').pop().toLowerCase() : '';
                        const query = this.searchQuery.toLowerCase();
                        return name.includes(query) || type.includes(query);
                    });
                    
                    // Apply sorting
                    filtered.sort((a, b) => {
                        let aVal, bVal;
                        
                        switch(this.sortKey) {
                            case 'name':
                                aVal = a.name.toLowerCase();
                                bVal = b.name.toLowerCase();
                                break;
                            case 'type':
                                aVal = a.name.split('.').pop().toLowerCase();
                                bVal = b.name.split('.').pop().toLowerCase();
                                break;
                            case 'upload_time':
                                aVal = a.upload_time ? new Date(a.upload_time).getTime() : 0;
                                bVal = b.upload_time ? new Date(b.upload_time).getTime() : 0;
                                break;
                            default:
                                aVal = a[this.sortKey];
                                bVal = b[this.sortKey];
                        }
                        
                        if (this.sortAsc) {
                            return aVal > bVal ? 1 : -1;
                        } else {
                            return aVal < bVal ? 1 : -1;
                        }
                    });
                    
                    return filtered;
                },
                highlightedName() {
                    return (name) => {
                        if (!this.searchQuery) return name;
                        const re = new RegExp(`(${this.searchQuery})`, 'gi');
                        return name.replace(re, '<mark>$1</mark>');
                    }
                },
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
                            _this.videos = response.data.videos.map(v => ({
                                ...v,
                                upload_time: v.upload_time || v.created_at // fallback for naming
                            }));
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
                copyToClipboard: function (name, linkType) {

                    if (linkType === 'stream_link') {
                        let link = this.url + '/streamingvideo/index.html?streaminglink=' + name
                        var $temp = $("<input>");
                        $("body").append($temp);
                        $temp.val(link).select();
                        document.execCommand("copy");
                        $temp.remove();
                        alert("Copied to clipboard");
                        return false;
                    }

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

                    //Upload with blob video created
                    if(validate){
                        this.isUploading = true;
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
                                formData.append('file', file);
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
                                        _this.isUploading = false;
                                    })
                                    .catch(error => {
                                        _this.percent = 0;
                                        $('#btnUpload').removeAttr("disabled");
                                        $('#btnUpload').removeClass('loadding');
                                        toastr['error']("An error occurred, please try again later", "Error");
                                        _this.isUploading = false;
                                    });
                            }, function (err) {
                                _this.percent = 0;
                                $('#btnUpload').removeAttr("disabled");
                                $('#btnUpload').removeClass('loadding');
                                toastr['error']("An error occurred, please try again later", "Error");
                                _this.isUploading = false;
                            });
                        } catch (error) {
                            console.log(error);
                            $('#btnUpload').removeAttr("disabled");
                            $('#btnUpload').removeClass('loadding');
                            _this.isUploading = false;
                        }
                        // Always reset loading state in case of unexpected errors
                        finally {
                            $('#btnUpload').removeAttr("disabled");
                            $('#btnUpload').removeClass('loadding');
                            _this.isUploading = false;
                        }
                    }
                },
                deleteVideo: function (name, stream_link) {
                    var confirmDelete = confirm("Are you sure you want to delete this video?");
                    if (confirmDelete == true) {
                        var _this = this;
                        let formData = new FormData();
                        formData.append('type', 'delete');
                        formData.append('nameFile', name);
                        formData.append('stream_link', stream_link);
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
                    var extensions = ["webm", "mp4", "ogv", "mp3"];
                    //validate
                    if (extensions.indexOf(fileExt) < 0) {
                        alert("Extension not allowed, please choose a media file (webm, mp4, ogv, mp3).");
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
                },
                sortBy(key) {
                    if (this.sortKey === key) {
                        this.sortAsc = !this.sortAsc;
                    } else {
                        this.sortKey = key;
                        this.sortAsc = true;
                    }
                },
                formatDate(timestamp) {
                    if (!timestamp) return 'N/A';
                    const date = new Date(timestamp);
                    const pad = n => n.toString().padStart(2, '0');
                    let day = pad(date.getDate());
                    let month = pad(date.getMonth() + 1);
                    let year = date.getFullYear();
                    let hours = date.getHours();
                    let minutes = pad(date.getMinutes());
                    let seconds = pad(date.getSeconds());
                    let ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    hours = pad(hours);
                    return `${day}/${month}/${year} ${hours}:${minutes}:${seconds} ${ampm}`;
                },
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

        .video-table th {
            background: #f8f9fa;
            font-weight: 600;
            vertical-align: middle;
        }
        .video-table th, .video-table td {
            vertical-align: middle !important;
        }
        .video-table th[style*='cursor:pointer'] {
            user-select: none;
        }
        .badge-info {
            background: #17a2b8;
        }
        .video-title {
            font-weight: 500;
            word-break: break-all;
        }
        .mx-2 {
            margin-left: 0.5rem !important;
            margin-right: 0.5rem !important;
        }
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
        .drive-wrapper .drive-item {
            width: 100% !important;
        }
        .drive-wrapper .drive-item-title{
            max-width: 100% !important;
        }
        .progress {
            background-color: #e9ecef;
            border-radius: 0.25rem;
            box-shadow: none;
        }
        .progress-bar {
            transition: width 0.4s ease;
            font-size: 14px;
            line-height: 20px;
        }
    </style>
    </body>

<?php

echo $OUTPUT->footer();
