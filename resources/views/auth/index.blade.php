<html>
<title>Elearning Login</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="/design/css/bootstrap.min.css" rel="stylesheet">

<link href="/assets/dist/css/font-awesome.all.min.css" rel="stylesheet">

<link href="/css/custom.css" rel="stylesheet">

<link href="/css/login.css" rel="stylesheet">

<script src="/template/assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="/design/js/bootstrap.min.js"></script>

<script src="/sso/sslssso.js"></script>

<body>

<div class="wrapper"><!-- wrapper -->
    <div class="main-bg"></div>
    <div class="main-content"></div>
    <div class="before-main-content">
        <div class="img_fix_bg"></div>
        <h3 class="name-organization">PHH</h3>
        <p class="class-name-organization">ACADEMY</p>
    </div>
    <div class="wrap-login100">
        <form method="POST" class="login-form" id="loginform">
            @csrf
            <div class="wrap-content">
                <img src="../images/logo-black-login.png" class="logo" alt="">
                <p class="title-login">Welcome <br/> sign in to continue</p>
                <div class="wrap-input100">
                    <input oninput="changeInput(this)" id="username" class="form-control" name="username"
                           type="text" placeholder="Username"
                           value="{{session('username') ? session('username'):''}}">
                    <label class="text-danger message error userFail" style="display: none;">
                        Incorrect account.</label>
                    <label class="text-danger message error userEmpty errorEmpty" style="display: none;">Please fill
                        username.</label>
                </div>
                <div class="wrap-input100">
                    <input oninput="changeInput(this)" id="pass" class="form-control" name="password"
                           type="password" placeholder="Password">
                    <label class="text-danger message error passFail" style="display: none;">Incorrect password.</label>
                    <label class="text-danger message error passEmpty errorEmpty" style="display: none;">Please fill
                        password.</label>
                </div>
                <div class="wrap-remember100">
                    <input type="checkbox" checked id="ip-checkbox">
                    <span>Remeber login</span>
                </div>
                <div class="wrap-btn100 text-center">
                    <button style="position: relative;" type="submit" class="btn btn-login">Login
                        <i class="fa fa-spinner" aria-hidden="true"></i></button>
                </div>

                <!-- Error Alert -->
                <div class="alert alert-danger alert-dismissible message error organizationFail"
                     style="display: none;margin-top: 1rem">
                    <strong>Error!</strong>&nbsp;You do not have access to this organization
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <div class="alert alert-danger alert-dismissible message error loginFail"
                     style="display: none;margin-top: 1rem">
                    <strong>Error!</strong>&nbsp;Username or password incorrect
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <div class="alert alert-danger alert-dismissible message error accountDisabled"
                     style="display: none;margin-top: 1rem">
                    <strong>Error!</strong>&nbsp;The account is locked, please contact the administrator to open lock up
                    account
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <div class="alert alert-danger alert-dismissible message error internalServerError"
                     style="display: none;margin-top: 1rem">
                    <strong>Error!</strong>&nbsp;System error or invalid data format.
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <div class="alert alert-danger alert-dismissible message error invalidParam"
                     style="display: none;margin-top: 1rem">
                    <strong>Error!</strong>&nbsp;The data entered is in the wrong format
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>

                <div class="wrap-forgot100" id="div-recover">
                    <p><span>Forgot your password?</span> <a href="javascript:void(0)" id="to-recover"> Password
                            retrieval</a></p>
                </div>
            </div>
        </form>

        <form method="POST" class="login-form" id="recoverform" style="display: none;">
            @csrf
            <div class="wrap-content">
                <img src="../images/logo-black-login.png" class="logo" alt="">
                <p class="title-login"><br/>Password retrieval</p>
                <div class="wrap-input100">
                    <input class="form-control" id="iprs-user" name="username" type="email" required=""
                           placeholder="Email">
                    <label class="text-danger message error missingUsernameForget" style="display: none;">Please fill
                        email</label>
                </div>
                <div class="wrap-input100">

                    <div class="wrap-btn100 text-center">
                        <button style="position: relative;" type="submit" id="btn-reset" class="btn btn-login">Recovery
                            <i class="fa fa-spinner" aria-hidden="true"></i></button>
                    </div>

                    <!-- Error Alert -->
                    <div class="alert alert-danger alert-dismissible" id="forgetError"
                         style="margin-top: 1rem; display: none">
                        <strong>Error!</strong>&nbsp;<span id="forgetErrorText">Password recovery failed</span>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <!-- Success Alert -->
                    <div class="alert alert-success" id="forgetSuccess"
                         style="margin-top: 1rem;display: none">
                        <strong>Successfully!</strong>&nbsp;<span id="forgetSuccessText">Please check your email for the next steps</span>
                        <button type="button" class="close" data-dismiss="alert"
                                style="position: absolute;top: 2px;right: 5px;">&times;
                        </button>
                    </div>

                    <div class="wrap-forgot100" id="div-login">
                        <p><span>Do you already have an account?</span> <a href="javascript:void(0)" id="to-login">Sign
                                in</a></p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="block">

</div>

<script>
    var logoutCookie = '__c2FmYXJpVmVyaWZpY2F0aW9uVG9rZW4UfFzcvye';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var confirm = 2;

    // $(document).ready(function () {
    //     var isFirefox = typeof InstallTrigger !== 'undefined';
    //     if (isFirefox) {
    //         alert('We recommend you do not use firefox browser for security');
    //     }
    // });

    //for test
    //modeChangeOrganization();

    function modeChangeOrganization(){
        //
        sessionStorage.setItem('img-logo', '/assets/dist/img/logo-black.png');
        //
        var url = document.URL;
        if(url.includes('tinhvan')){
            var lastChar = url.substr(url.length - 1);
            if(lastChar == '/'){
                $('.name-organization').text('Clever');
                $('.class-name-organization').text('Elearning');
                $('.logo'). attr("src", "../images/logo-clever.png");
                // $('.main-bg').css("background-image", "url(../images/3a.png) !important");
                $('.main-bg').css('background-image', 'url(../images/3a.png)');
                $('.before-main-content .img_fix_bg').css('background-image', 'none');
                $('.before-main-content').css('border', '1px solid rgb(255 255 254 / 0.19)');
                $('.before-main-content').css('background-color', 'rgb(255 255 254 / 0.19)');
                $('.wrap-content .logo').css('width', '50%');
                $('.btn-login').css('background', '#007f48 0% 0% no-repeat padding-box');
                $('.wrap-login100').css('top', '21%');
                sessionStorage.setItem('img-logo', '/images/logo-clever.png.png');
            }
        }
    }

    function removeError() {
        $('#confirm_code').removeClass('error');
        $('.message.error.message_confirm_code').hide();
    }

    function changeConfirm(status) {
        if (status == 1) {
            $('#div_confirm_code').show();
        } else {
            $('#div_confirm_code').hide();
        }
        confirm = status;
    }

    $('body').on('click', '#contLogin', function () {
        $('.btn-login').addClass('loadding');
        loginSso(2);
        return false;
    });

    $('body').on('click', '#cancel_login', function () {
        $('body').removeClass('show_confirm');
    });

    function changeInput(element) {
        var parent = $(element).parent();
        $('.errorEmpty', parent).hide();
    }

    $('#loginform').submit(function () {
        $('.btn-login').addClass('loadding');
        loginSso(1);
        return false;
    });
    $(function () {
        $(".preloader").fadeOut();
    });
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    // ==============================================================
    // Login and Recover Password
    // ==============================================================
    $('#to-recover').on("click", function () {
        $("#loginform").hide();
        $("#recoverform").show();
        $('#div-recover').hide();
        $('#div-login').show();
    });

    $('#to-login').on("click", function () {
        $("#loginform").show();
        $("#recoverform").hide();
        $('#div-recover').show();
        $('#div-login').hide();
    });

    $('#recoverform').submit(function () {
        recoverPassword();
        return false;
    });

    function recoverPassword() {
        if (!$('#btn-reset').hasClass('loadding')) {
            $('#btn-reset').addClass('loadding');

            var username = $('#iprs-user').val();

            var usernameMissingText = $('.message.error.missingUsernameForget');
            var emailMissingText = $('.message.error.missingEmailForget');
            var forgetSuccessBlock = $('#forgetSuccess');
            var forgetErrorBlock = $('#forgetError');

            usernameMissingText.hide();
            emailMissingText.hide();
            forgetSuccessBlock.hide();
            forgetErrorBlock.hide();

            if (username.length === 0) {
                $('#btn-reset').removeClass('loadding');
                usernameMissingText.show();
                return;
            }

            $.ajax({
                type: "POST",
                url: '/bgtresetpassword',
                data: {
                    username: username
                    {{--_token: '{{csrf_token()}}'--}}
                },
                success: function (data) {
                    $('#btn-reset').removeClass('loadding');
                    if (data.status) {
                        forgetSuccessBlock.show();
                        forgetErrorBlock.hide();
                        $('#forgetSuccessText').text('' + data.message);
                    } else {
                        forgetSuccessBlock.hide();
                        forgetErrorBlock.show();
                        $('#forgetErrorText').text('' + data.message);
                    }
                }
            });
        }
    }

    function loginSso(order) {

        // var isFirefox = typeof InstallTrigger !== 'undefined';
        // if (isFirefox) {
        //     alert('We recommend you do not use firefox browser for security, please switch to another browser and try again');
        //     $('button.btn.btn-login').removeClass('loadding');
        //     return;
        // }

        var user = $('#username').val();
        var pass = $('#pass').val();
        var confirm_code = $('#confirm_code').val();
        var confirm_time = $('#confirm_time').val();
        var confirm_status = confirm;
        var remember;

        if (confirm_status == 0 && order == 2) {
            alert('Bạn chưa trả lời câu hỏi.');
            $('button.btn.btn-login').removeClass('loadding');
            return;
        }

        if (confirm_status == 1 && !confirm_code && order == 2) {
            $('#confirm_code').addClass('error');
            $('button.btn.btn-login').removeClass('loadding');
            return;
        }

        var apikey = '{{$api_key}}';
        var callback = '{{$callback}}';
        $('.message.error').hide();
        if (user.length === 0 || pass.length === 0) {
            if (user.length === 0) {
                $('.userEmpty').show();
                $('button.btn.btn-login').removeClass('loadding');
            }

            if (pass.length === 0) {
                $('.passEmpty').show();
                $('button.btn.btn-login').removeClass('loadding');
            }
            return;
        }

        if ($('#ip-checkbox:checkbox:checked').length > 0) {
            remember = 1;
        } else {
            remember = 0;
        }

        var url = "/bgtgoadmin";

        normalSSOLogin(url, user, pass, apikey, callback, remember, confirm_status, confirm_code, order, confirm_time);
    }


    function normalSSOLogin(url, user, pass, apikey, callback, remember, confirm_status, confirm_code, order, confirm_time) {
        $.ajax({
            type: "POST",
            url: url,
            data: {
                username: user,
                password: pass,
                apiKey: apikey,
                callback: callback,
                remember: remember,
                status: confirm_status,
                order: order,
                code: confirm_code,
                confirm_time: confirm_time,
                _token: '{{csrf_token()}}'
            },
            success: function (data) {
                if (data.status === "SUCCESS") {
                    $('.message.error').hide();
                    if (remember === 1) {
                        localStorage.setItem('remember', 'save');
                    } else {
                        localStorage.setItem('remember', 'notsave');
                    }

                    var userinfo = {
                        id: data.id,
                        username: data.username,
                        avatar: data.avatar,
                        fullname: data.fullname,
                        domain: data.domain
                    };
                    localStorage.setItem(logoutCookie, 'login');
                    localStorage.setItem('auth.token', data.jwt);
                    localStorage.setItem('auth.user', JSON.stringify(userinfo));
                    localStorage.setItem('auth.lang', 'en');
                    sslssso.login(data.jwt);
                    loginLMS(data, callback);
                    // // [VinhPT]
                    // // Check if description is student => bgt.tinhvan.com/lms else => bgt.tinhvan.com
                    {{--if (data.redirect_type.includes("lms")) {--}}
                    {{--    if (callback === '{{Config::get('constants.domain.TMS-LOCAL')}}') {--}}
                    {{--        window.location.href = '{{Config::get('constants.domain.LMS')}}';--}}
                    {{--    } else {--}}
                    {{--        window.location.href = callback;--}}
                    {{--    }--}}
                    {{--} else {--}}
                    {{--    if (callback === '{{Config::get('constants.domain.TMS-LOCAL')}}') {--}}
                    {{--        window.location.href = '/tms/dashboard';--}}
                    {{--    } else {--}}
                    {{--        window.location.href = callback;--}}
                    {{--    }--}}
                    {{--}--}}

                } else if (
                    data.status === "FAILUSER" || data.status === "FAILPASSWORD" || data.status === "FAILBANNED" || data.status === "FAILORGANIZATION" ||
                    data.status === "INVALID" || data.status === "FAILCONFIRM" || data.status === "FAILCODE" || data.status === "FAILVALIDATECODE"
                ) {
                    $('.message.error').hide();
                    if (data.status === "FAILPASSWORD" || data.status === "FAILUSER") {
                        $('.message.error.loginFail').show();
                    }
                    if (data.status === "FAILBANNED") {
                        $('.message.error.accountDisabled').show();
                    }
                    if (data.status === "INVALID") {
                        $('.message.error.invalidParam').show();
                    }
                    if (data.status === "FAILCONFIRM") {
                        $('body').addClass('show_confirm');
                    }
                    if (data.status === "FAILCODE") {
                        $('#confirm_code').addClass('error');
                        $('.message.error.message_confirm_code').show();
                        $('.message.error.message_confirm_code').html('Mã giấy chứng nhận đã tồn tại.');
                    }
                    if (data.status === "FAILVALIDATECODE") {
                        $('#confirm_code').addClass('error');
                        $('.message.error.message_confirm_code').show();
                        $('.message.error.message_confirm_code').html('Mã giấy chứng nhận gồm ( a-zA-Z0-9 ), ký tự đặc biệt ( -_./ ).');
                    }

                    if (data.status === "FAILORGANIZATION") {
                        $('.message.error.organizationFail').show();
                    }
                } else {
                    $('.message.error.internalServerError').show();
                }
                $('button.btn.btn-login').removeClass('loadding');

            }
        });
    }

    function loginLMS(data, callback) {
        $.ajax({
            type: "POST",
            url: '/lms/loginfirst.php',
            data: {
                data: data.data
            },
            success: function (res) {
                if (data.redirect_type.includes("lms")) {
                    if (callback.includes('{{Config::get('constants.domain.TMS')}}')) {
                        window.location.href = '{{Config::get('constants.domain.LMS')}}';
                    } else {
                        window.location.href = callback;
                    }
                } else {
                    if (callback.includes('{{Config::get('constants.domain.TMS')}}')) {
                        window.location.href = '/lms/my';
                    } else {
                        window.location.href = callback;
                    }

                }
            }
        });
    }

</script>
</body>
</html>
