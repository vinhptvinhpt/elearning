@extends('layouts.login')
@section('content')
    <div class="container container-login">
        <div class="row">
            <div class="col-md-4 login-sec" style="display: block;margin: 0 auto;">
                <div class="logo_box">
                    <img src="/assets/dist/img/logo-black.png" class="img-fluid" alt=""
                         style="margin-right: auto; margin-left: auto;">
                </div>
                <h3 class="login_title">Đào tạo trực tuyến </h3>
                <div class="form_box">

                    <form method="POST" class="login-form" id="loginform">
                        @csrf
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="text-uppercase">Tài Khoản</label>
                            <input oninput="changeInput(this)" id="username" class="form-control" name="username"
                                   type="text" placeholder="Username"
                                   value="{{session('username') ? session('username'):''}}">
                            <label class="text-danger message error userFail" style="display: none;">Tài khoản không
                                chính
                                xác.</label>
                            <label class="text-danger message error userEmpty errorEmpty" style="display: none;">Vui
                                lòng
                                nhập tài khoản.</label>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1" class="text-uppercase">Mật khẩu</label>
                            <input oninput="changeInput(this)" id="pass" class="form-control" name="password"
                                   type="password" placeholder="Password">
                            <label class="text-danger message error passFail" style="display: none;">Mật khẩu không
                                chính
                                xác.</label>
                            <label class="text-danger message error passEmpty errorEmpty" style="display: none;">Vui
                                lòng
                                nhập mật khẩu.</label>
                        </div>

                        <div class="form-check">
                            <label class="form-check-label d-flex">
                                <input type="checkbox" checked id="ip-checkbox" class="form-check-input">
                                <small>Ghi nhớ đăng nhập</small>
                            </label>
                        </div>
                        <button style="position: relative;" type="submit" class="btn btn-login btn-block mt-3">Đăng nhập
                            <i
                                    class="fa fa-spinner" aria-hidden="true"></i></button>

                        <!-- Error Alert -->
                        <div class="alert alert-danger alert-dismissible message error loginFail"
                             style="display: none;margin-top: 1rem">
                            <strong>Lỗi!</strong>&nbsp;Tài khoản hoặc Mật khẩu chưa chính xác
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <div class="alert alert-danger alert-dismissible message error accountDisabled"
                             style="display: none;margin-top: 1rem">
                            <strong>Lỗi!</strong>&nbsp;Tài khoản đang bị khóa, vui lòng liên hệ với quản trị viên để mở
                            khóa
                            tài khoản
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <div class="alert alert-danger alert-dismissible message error internalServerError"
                             style="display: none;margin-top: 1rem">
                            <strong>Lỗi!</strong>&nbsp;Lỗi hệ thống hoặc định dạng dữ liệu không hợp lệ.
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <div class="alert alert-danger alert-dismissible message error invalidParam"
                             style="display: none;margin-top: 1rem">
                            <strong>Lỗi!</strong>&nbsp;Dữ liệu nhập vào sai định dạng
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>

                    </form>
                    <form class="form-horizontal" id="recoverform" method="POST">
                        {{--                    {{csrf_field()}}--}}
                        @csrf

                        <div class="form-group ">
                            <label for="iprs-user" class="text-uppercase">Tài Khoản</label>
                            <input class="form-control" id="iprs-user" name="username" type="text" required=""
                                   placeholder="Username">
                            <label class="text-danger message error missingUsernameForget" style="display: none;">Vui
                                lòng
                                nhập tài khoản</label>
                        </div>
                        <div class="form-group ">
                            <label for="iprs-email" class="text-uppercase">Email</label>
                            <input class="form-control" id="iprs-email" type="email" required="" placeholder="Email"
                                   name="email" value="">
                            <label class="text-danger message error missingEmailForget" style="display: none;">Vui lòng
                                nhập
                                email</label>
                        </div>

                        <div class="form-group text-center m-t-20">
                            <div class="col-xs-12">
                                <button style="position: relative; background: #123462;"
                                        class="btn btn-reset btn-primary btn-block mt-3"
                                        id="btn-reset"
                                        type="button">Lấy lại mật khẩu<i class="fa fa-spinner" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Error Alert -->
                        <div class="alert alert-danger alert-dismissible" id="forgetError"
                             style="margin-top: 1rem; display: none">
                            <strong>Lỗi!</strong>&nbsp;<span id="forgetErrorText">Lấy lại mật khẩu thất bại</span>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <!-- Success Alert -->
                        <div class="alert alert-success alert-dismissible" id="forgetSuccess"
                             style="margin-top: 1rem;display: none">
                            <strong>Thành công!</strong>&nbsp;<span id="forgetSuccessText">Hãy kiểm tra email để thực hiện các bước tiếp theo</span>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>

                    </form>
                    <div class="copy-text" id="div-recover">Bạn quên mật khẩu? <a href="javascript:void(0)"
                                                                                  id="to-recover">Lấy
                            lại mật khẩu</a></div>
                    <div class="copy-text" id="div-login" style="display: none;">Bạn đã có tài khoản? <a
                                href="javascript:void(0)" id="to-login">Đăng nhập</a></div>
                </div>
            </div>
            <div class="col-md-8 d-none d-md-block banner_layout">
                <div class="banner-sec">

                </div>
            </div>

        </div>
    </div>
    <div class="form_confirm_wrap">
        <div class="form_confirm">
            <h3 class="confirm_title">HỆ THỐNG<br><span>Đào tạo trực tuyến nhân viên bán hàng</span></h3>
            <div class="content">
                <p style="font-size: 1.1rem;font-weight: 500;">Bạn đã có giấy chứng nhận chưa ?</p>
                <ul>
                    <li>
                        <label><input checked type="radio" onclick="changeConfirm(2)" class="confirm_status"
                                      name="confirm" value="2"> Chưa có</label>
                    </li>
                    <li>
                        <label><input type="radio" onclick="changeConfirm(1)" class="confirm_status" name="confirm"
                                      value="1"> Đã có</label>
                        <div id="div_confirm_code">
                            <div class="item">
                                <label><span>Nhập mã giấy chứng nhận *</span></label>
                                <input type="text" id="confirm_code" oninput="removeError()" class="form-control"
                                       value="" placeholder="">
                                <label class="message error hide message_confirm_code"></label>
                            </div>
                            <div class="item">
                                <label><span>Ngày cấp</span></label>
                                <input type="date" id="confirm_time" class="form-control">
                            </div>
                        </div>
                    </li>
                </ul>
                <div style="text-align: center;">
                    <div class="btn_list">
                        <div class="item">
                            <button style="position: relative;" type="button" class="btn btn-login btn-block mt-3"
                                    id="contLogin">Tiếp tục Đăng nhập <i
                                        class="fa fa-spinner" aria-hidden="true"></i></button>
                        </div>
                        <div class="item">
                            <button style="position: relative;" type="button" class="btn btn-default" id="cancel_login">
                                Hủy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        var logoutCookie = '__c2FmYXJpVmVyaWZpY2F0aW9uVG9rZW4UfFzcvye';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var confirm = 2;

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

        $('#btn-reset').click(function () {
            $('#btn-reset').addClass('loadding');

            var username = $('#iprs-user').val();
            var email = $('#iprs-email').val();
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

            if (email.length === 0) {
                $('#btn-reset').removeClass('loadding');
                emailMissingText.show();
                return;
            }

            $.ajax({
                type: "POST",
                url: '/bgtresetpassword',
                data: {
                    username: username,
                    email: email,
                    _token: '{{csrf_token()}}'
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
        });

        function loginSso(order) {
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
                            username: data.username,
                            avatar: data.avatar
                        };
                        localStorage.setItem(logoutCookie, '');
                        localStorage.setItem('auth.token', data.jwt);
                        localStorage.setItem('auth.user', JSON.stringify(userinfo));
                        localStorage.setItem('auth.lang', 'vi');
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
                        data.status === "FAILUSER" || data.status === "FAILPASSWORD" || data.status === "FAILBANNED" ||
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
                            window.location.href = '/dashboard';
                        } else {
                            window.location.href = callback;
                        }

                    }
                }
            });
        }

    </script>
@endpush
