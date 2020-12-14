<?php
if(!isloggedin()){
    require_login();
}
require_once(__DIR__ . '/../../../../config.php');
$user_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $USER->id;
?>

<html>
<title>Update profile</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../../">
<link rel="shortcut icon" href="images/favicon.png">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script src="//unpkg.com/vue-plain-pagination@0.2.1"></script>

<style>
    @font-face {
        font-family: Roboto-Bold;
        src: url('fonts/Roboto-Bold.ttf');
    }
    @font-face {
        font-family: Roboto-Regular;
        src: url('fonts/Roboto-Regular.ttf');
    }
    @font-face {
        font-family: Roboto-Italic;
        src: url('fonts/Roboto-Italic.ttf');
    }

    a{
        text-decoration: none;
    }
    a:hover{
        text-decoration: none;
    }
    img{
        width: 100%;
    }
    body {
        font-size: 14px;
        font-family: Roboto-Bold !important;
        background-color: #F1F1F1;
    }

    ul{
        list-style: none;
    }
    ul li{
        list-style: none;
    }
    a{
        text-decoration: none;
    }
    .clear-fix{
        clear: both;
    }
    #page-wrapper .navbar{
        padding: 7px 1rem 9px .5rem !important;
    }
    .navbar .count-container{
        top: 2px !important;
    }
    /*css*/
    .btn-click{
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 0px 3px 6px #00000029;
        border: 1px solid <?=$_SESSION["color"]?>;
        border-radius: 4px;
        color: <?=$_SESSION["color"]?>;
        padding: 5px;
    }
    .btn-click a{
        color: <?=$_SESSION["color"]?>;
    }
    .btn-update{
        background-color: <?=$_SESSION["color"]?> !important;
        color: #ffffff !important;
    }
    .div-btn{

    }
    .div-btn button{
        width: 100%;
    }
    #region-main{
        margin: 2%;
    }
    .avt-block{
        width: 60%;
        margin: 0 auto;
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 0px 3px 6px #00000029;
        border-radius: 20px;
    }
    .info-edit__block input{
        height: calc(2.25rem + 2px) !important;
    }
    .change-password p, .change-password .collapse{
        width: 100%;
    }
    .change-password p a{
        color: <?=$_SESSION["color"]?> !important;
        letter-spacing: 0.5px;
    }
    .choose-file{
        margin: 0 auto;
        width: 60%;
        margin-top: 5%;
        overflow: hidden;
    }
    .btn-collapse i{
        border: 1px solid <?=$_SESSION["color"]?>;
        padding: 5px;
        border-radius: 50%;
    }
    .collapse-div{
        display: none;
    }
    .collapse-show{
        display: block !important;
    }
    .btn-collapse:hover{
        cursor: pointer;
    }
    .btn-collapse-active i{
        background: <?=$_SESSION["color"]?>;
        color: #ffffff;
    }

    @media (min-width: 1600px) {
        .col-xxl-8{
            flex: 0 0 66.666667% !important;
            max-width: 66.666667% !important;
        }
        .col-xxl-4{
            flex: 0 0 33.333333% !important;
            max-width: 33.333333% !important;
        }
        .col-xxl-2{
            flex: 0 0 16.666667% !important;
            max-width: 16.666667% !important;
        }
        .avt-block, .choose-file{
            width: 35% !important;
        }
    }

</style>
<body>
<?php

?>
<div class="wrapper" >
    <?php echo $OUTPUT->header(); ?>
    <!--    body-->
    <div id="app">
        <div class="container">
            <div class="row col-12">
                <div class="col-12 col-xs-12 col-md-4 col-xl-4 col-lg-4">
                    <div class="avt-block">
                        <img :src="user.avatar" alt="">
                    </div>
                    <div class="choose-file">
<!--                        <input type="file" ref="file" name="file" class="dropify" />-->
                        <input type="file" ref="file" name="file" class="dropify" id="file" accept="image/*"
                               @change="selectedFile"/>
                    </div>
                </div>
                <div class="col-12 col-xs-12 col-md-8 col-xl-8 col-lg-8">
                    <div class="row info-edit">
                        <div class="col-12 col-xs-12 col-md-6 col-xl-6 col-lg-6 col-xxl-4 info-edit__block form-group">
                            Full name <input type="text" class="form-control" v-model="user.fullname">
                        </div>
                        <div class="col-12 col-xs-12 col-md-6 col-xl-6 col-lg-6 col-xxl-4 info-edit__block form-group">
                            Date of Birth <input type="date" class="form-control" v-model="user.dob">
                        </div>
                        <div class="col-12 col-xs-12 col-md-6 col-xl-6 col-lg-6 col-xxl-4 info-edit__block form-group">
                            Address <input type="text" class="form-control" v-model="user.address" readonly onfocus="this.removeAttribute('readonly');">
                        </div>
                        <div class="col-12 col-xs-12 col-md-6 col-xl-6 col-lg-6 col-xxl-4 info-edit__block form-group">
                            Email <input type="text" class="form-control" v-model="user.email" readonly>
                        </div>
                        <div class="col-12 col-xs-12 col-md-6 col-xl-6 col-lg-6 col-xxl-4 info-edit__block form-group">
                            Contact Number <input type="text" class="form-control" v-model="user.phone" readonly onfocus="this.removeAttribute('readonly');">
                        </div>
                        <div class="col-12 col-xs-12 col-md-6 col-xl-6 col-lg-6 col-xxl-4 info-edit__block form-group">
                            Gender
                            <select class="form-control" v-model="user.sex">
                                <option value="-1">Select gender</option>
                                <option value="0">Female</option>
                                <option value="1">Male</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-xs-12 col-md-6 col-xl-3 col-lg-4 col-xxl-2 div-btn form-group">
                            <button type="button" class="btn-update btn-click" @click="updateProfile('')">Update</button>
                        </div>
                        <div class="col-12 col-xs-12 col-md-6 col-xl-3 col-lg-4 col-xxl-2 div-btn form-group">
                            <button type="button" class="btn-cancel btn-click btn-cancel-update"><a href="lms/user/profile.php" class="a-link">Cancel</a></button>
                        </div>
                    </div>

                    <div class="row change-password">
                        <p class="col-12">
                            <a class="btn-collapse">
                                <i class="fa fa-cog" aria-hidden="true"></i> Change Password
                            </a>
                        </p>
                        <div class="col-12 collapse-div" id="">
                            <div class="row">
                                <div class="col-12 col-md-12 col-xl-6 col-lg-6">
                                    <div class="row">
                                        <div class="col-12 col-xs-12 col-md-12 col-xl-12 col-lg-12 col-xxl-8 info-edit__block form-group">
                                            <input type="password" class="form-control" placeholder="Password" v-model="password">
                                        </div>
                                        <div class="col-12 col-xs-12 col-md-12 col-xl-12 col-lg-12 col-xxl-8 info-edit__block form-group">
                                            <input type="password" class="form-control" placeholder="Re-enter Password" v-model="re_password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-xs-12 col-md-6 col-xl-3 col-lg-4 col-xxl-2 div-btn form-group">
                                    <button type="button" class="btn-update btn-click" id="password" @click="updateProfile('password')">Change</button>
                                </div>
                                <div class="col-12 col-xs-12 col-md-6 col-xl-3 col-lg-4 col-xxl-2 div-btn form-group">
                                    <button type="button" class="btn-cancel btn-click btn-cancel-pass">
                                        <a href="lms/user/profile.php" class="a-link">Cancel</a>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $OUTPUT->footer(); ?>
</div>

<script>
    $(document).ready(function(){
        $('.btn-collapse').click(function(){
            $(this).toggleClass('btn-collapse-active');
            $('.collapse-div').toggleClass('collapse-show');
        });

        $('.btn-cancel-pass').click(function(){
            $('.collapse-div').removeClass('collapse-show');
            $('.btn-collapse').removeClass('btn-collapse-active');
        });

        $('.btn-cancel-update').click(function(){
            location.href = $('.a-link').attr('href');
        });
    });

    Vue.component('v-pagination', window['vue-plain-pagination'])
    var app = new Vue({
        el: '#app',
        data: {
            user: {},
            user_id: <?php echo $user_id ?>,
            url: '<?php echo $CFG->wwwroot; ?>',
            password: '',
            re_password: ''
        },
        methods: {
            selectedFile() {
                let file = this.$refs.file.files[0];
                const validFileTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/jpg'];
                if (!file || (validFileTypes.indexOf(file.type) == -1)) {
                    const input = this.$refs.file;
                    input.type = 'file';
                    this.$refs.file.value = '';
                    alert('Invalid file format');
                }
            },
            getProfile: function(){
                const params = new URLSearchParams();
                params.append('user_id', this.user_id);
                axios({
                    method: 'post',
                    url: this.url + '/pusher/profile.php',
                    data: params,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                })
                    .then(response => {
                        this.user = response.data.profile;
                    })
                    .catch(error => {
                        console.log("Error ", error);
                    });
            },
            updateProfile: function (type) {
                //validate password
                if(type == 'password'){
                    if(this.password.length == 0){
                        alert('Please enter the new password');
                        return;
                    }
                    if( this.re_password.length == 0 ){
                        alert('Please enter the re-password');
                        return;
                    }
                    if( this.password != this.re_password){
                        alert('Password not match. Try it again');
                        return;
                    }
                }

                let formData = new FormData();
                formData.append('user_id', this.user_id);
                formData.append('fullname', this.user.fullname);
                formData.append('dob', this.user.dob);
                if(!this.user.address)
                    this.user.address = '';
                formData.append('address', this.user.address);
                formData.append('email', this.user.email);
                if(!this.user.phone)
                    this.user.phone = '';
                formData.append('phone', this.user.phone);
                formData.append('sex', this.user.sex);
                formData.append('password', this.password);
                formData.append('re_password', this.re_password);
                formData.append('btnType', type);
                formData.append('file', this.$refs.file.files[0]);

                axios.post(this.url + '/pusher/update_profile.php', formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    console.log(response.data);
                    if(response.data.status)
                    {
                        alert(response.data.msg);
                        location.reload();
                    }
                    else
                        alert(response.data.msg);
                })
                .catch(error => {
                    console.log("Error ", error);
                });
            }
        },
        mounted() {
            this.getProfile();
            // this.user.dob = '05/05/2020';
        }
    });

</script>
</body>
</html>


<?php
die;
?>
