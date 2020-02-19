<template>
    <div class="row">
        <div class="col-12 col-lg-3 mb-2">
            <div class="card">
                <div href="" class="image-box ratio-16-9" v-if="avatar.length > 0">
                    <img :src="avatar" class="image"/>
                </div>
                <div class="card-body">
                    <p>
                        <input type="file" @change="previewImage()" ref="file" name="file" class="dropify"/>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-9">
            <form action="" class="form-row">
                <div class="col-sm-4 form-group">
                    <label for="inputText1-1">{{trans.get('keys.ma_thu_vien')}} *</label>
                    <input v-model="shortname" type="text" id="inputText1-1" :placeholder="trans.get('keys.nhap_ma_thu_vien')"
                           class="form-control mb-4">
                    <label v-if="!shortname" class="required text-danger shortname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-sm-4 form-group">
                    <label for="inputText6">{{trans.get('keys.ten_thu_vien')}} *</label>
                    <input v-model="fullname" type="text" id="inputText6"
                           :placeholder="trans.get('keys.nhap_ten_thu_vien')"
                           class="form-control mb-4">
                    <label v-if="!fullname" class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>

                <div class="col-sm-4 form-group">
                    <label for="inputText1-1">{{trans.get('keys.diem_qua_mon')}} *</label>
                    <input v-model="pass_score" type="number" id="inputText1-2" :placeholder="trans.get('keys.vi_du')+': 50'"
                           class="form-control mb-4">
                    <label v-if="!pass_score" class="required text-danger pass_score_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                </div>
                <div class="col-12 form-group">
                    <label for="inputText6">{{trans.get('keys.mo_ta')}}</label>
                    <textarea v-model="description" class="form-control" rows="3" id="article_ckeditor"
                              :placeholder="trans.get('keys.noi_dung')"></textarea>
                </div>
            </form>
            <div class="button-list text-right">
                <button type="button" @click="goBack()" class="btn btn-secondary btn-sm">{{trans.get('keys.huy')}}</button>
                <button @click="createCourse()" type="button" class="btn btn-primary btn-sm">{{trans.get('keys.tao')}}</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                fullname: '',
                shortname: '',
                pass_score: '',
                description: '',
                avatar: '',
                language : this.trans.get('keys.language')
            }
        },
        methods: {
            previewImage: function (event) {
                var input = event.target;
                // Ensure that you have a file before attempting to read it
                if (input.files && input.files[0]) {
                    // create a new FileReader to read this image and convert to base64 format
                    var reader = new FileReader();
                    // Define a callback function to run, when FileReader finishes its job
                    reader.onload = (e) => {
                        // Note: arrow function used here, so that "this.imageData" refers to the imageData of Vue component
                        // Read image as base64 and set to imageData
                        this.avatar = e.target.result;
                    };
                    // Start the reader job - read file as a data url (base64 format)
                    reader.readAsDataURL(input.files[0]);
                }
            },
            createCourse() {
                if (!this.shortname) {
                    $('.shortname_required').show();
                    return;
                }
                if (!this.fullname) {
                    $('.fullname_required').show();
                    return;
                }

                if (!this.pass_score) {
                    $('.pass_score_required').show();
                    return;
                }


                var editor_data = CKEDITOR.instances.article_ckeditor.getData();
                this.formData = new FormData();
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('fullname', this.fullname);
                this.formData.append('shortname', this.shortname);
                this.formData.append('startdate', '01-01-2019'); //gán cứng 2 giá trị do sử dụng cùng 1 api với tạo mới khóa đào tạo, không sử dụng 2 giá trị này trên server
                this.formData.append('enddate', '01-01-2119');
                this.formData.append('pass_score', this.pass_score);
                this.formData.append('description', editor_data);
                this.formData.append('course_place', '');
                this.formData.append('allow_register', 1);
                this.formData.append('is_end_quiz', 0);
                this.formData.append('total_date_course', 0);// truyền giá trị để nhận biết đây không phải khóa học tập trung
                this.formData.append('category_id', 2); //gắn cứng giá trị quy định đây là id danh mục mãu
                this.formData.append('sample', 1);// truyền giá trị để nhận biết đây là khóa học mẫu

                axios.post('/api/courses/create', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        var language =  this.language;
                        if (response.data.status) {
                            swal({
                                    title: response.data.message,
                                    // text: response.data.message,
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }
                                , function () {
                                    this.$router.push({ name: 'SampleCourseIndex' });
                                }
                            );
                        } else {
                            swal({
                                title: response.data.message,
                                // text: response.data.message,
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }
                    })
                    .catch(error => {
                        swal({
                            title: "Thông báo",
                            text: " Lỗi hệ thống.",
                            type: "error",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        });
                    });


            },
            goBack() {
                this.$router.push({ name: 'SampleCourseIndex' });
            }
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
