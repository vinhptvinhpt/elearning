<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'en' ? app()->getLocale() : 'vi' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TMS</title>
    <meta name="description" content="Đào tạo trực tuyến nhân viên bán hàng"/>
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="icon" href="/images/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap&subset=vietnamese"
          rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="sso_smsc_apikey" content="bd629ce2de47436e3a9cdd2673e97b17"/>

    <link href="/css/app.css" rel="stylesheet">
    <link href="/css/sweetalert.css" rel="stylesheet">
    <link href="/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="/assets/vendors/morris.js/morris.css" rel="stylesheet">
    <link href="/assets/vendors/jquery-toggles/css/toggles.css" rel="stylesheet">
    <link href="/assets/vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet">
    <link href="/assets/dist/css/font-awesome.all.min.css" rel="stylesheet">
    <link href="/assets/vendors/dropify/dist/css/dropify.min.css" rel="stylesheet">
    <link href="/assets/vendors/dropzone/dist/dropzone.css" rel="stylesheet">
    <link href="/assets/vendors/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="/assets/dist/css/style.css" rel="stylesheet">
    <link href="/assets/dist/css/custom.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/responsive.css" rel="stylesheet">
    <link href="/assets/css/laraspace.css" rel="stylesheet" type="text/css">
</head>

<body class="">
<div class="preloader-it">
    <div class="loader-pendulums"></div>
</div>

<div class="roam_message">
    <div class="roam_content">
        <div class="roam_icon">
            <i class="fa"></i>
        </div>
        <div class="roam_text">
            <strong></strong>
        </div>
    </div>
</div>


<div id="app">
    <router-view></router-view>
</div>

<script type="text/javascript" src="{{mix("/assets/js/app.js")}}"></script>

<script src="/sso/sslssso.js"></script>
<script src="/js/sweetalert.min.js"></script>
<script src="/js/bootstrap-select.min.js"></script>
<script src="/assets/vendors/jquery-toggles/toggles.min.js"></script>
<script src="/assets/dist/js/toggle-data.js"></script>

<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/assets/dist/js/dropdown-bootstrap-extended.js"></script>
<script src="/assets/vendors/dropzone/dist/dropzone.js"></script>
<script src="/assets/dist/js/form-file-upload-data.js"></script>
<script src="/assets/vendors/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="/assets/vendors/dropify/dist/js/dropify.min.js"></script>
<script src="/assets/vendors/select2/dist/js/select2.full.min.js"></script>
<script src="/assets/dist/js/select2-data.js"></script>
<script src="/js/main.js"></script>
{{--<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>--}}
@php
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
@endphp
<script>

    function roam_message(status, message) {
        $('.roam_message').removeClass('message_success');
        $('.roam_message').removeClass('message_warning');
        $('.roam_message').removeClass('message_error');
        $('.roam_message').addClass('message_' + status);
        $('.roam_text strong').html(message);
        setTimeout(function () {
            $('.roam_message').removeClass('message_' + status);
        }, 5000);
        if ($('.btn_fillter').length > 0) {
            $('.btn_fillter').trigger('click');
        }
    }

    $('body').on('input', '.form-control', function () {
        $(this).removeClass('error');
    });

    var userAgent = window.navigator.userAgent.toLowerCase(),
        safari = /safari/.test(userAgent),
        ios = /iphone|ipod|ipad/.test(userAgent);

    if (ios) {
        if (safari) {
            $('body').addClass('not-webview');
        } else if (!safari) {
            $('body').addClass('webview');
        }
    } else {
        $('body').addClass('not-ios');
    }

    function getCookieByName(o) {
        var e;
        if (2 === (e = ("; " + document.cookie).split("; " + o + "=")).length) return e.pop().split(";").shift()
    }

    function isSafariBrowser() {
        var o = navigator.userAgent.indexOf("Safari") > -1;
        return navigator.userAgent.indexOf("Chrome") > -1 && o && (o = !1), o
    }

    var jwtsso;
    var tokenId = 'ssls.sso.jwt.bd629ce2de47436e3a9cdd2673e97b17';
    var safariCookie = '__c2FmYXJpVmVyaWZpY2F0aW9uVG9rZW4UfL';
    var logoutCookie = '__c2FmYXJpVmVyaWZpY2F0aW9uVG9rZW4UfFzcvye';

    if (isSafariBrowser()) {
        jwtsso = getCookieByName(safariCookie);
    } else {
        jwtsso = getCookieByName(safariCookie);
    }
    jQuery.ajax({
        type: "POST",
        url: '/sso/checklogin',
        data: {
            jwt: jwtsso
        },
        success: function (data) {
            if (data.status === 'LOGOUT') {
                window.location.href = '/sso/authenticate?apiKey=bd629ce2de47436e3a9cdd2673e97b17&callback=' + '{{$actual_link}}';
            }
        }
    });
    $('.datepicker').datepicker();

    function logout() {
        $.ajax({
            type: "POST",
            url: '/bgtlogout',
            data: {
                _token: '{{csrf_token()}}'
            },
            success: function (data) {
                if (data.status) {
                    localStorage.setItem(logoutCookie, 'logout');
                    sslssso.logout();
                    window.location.href = "/";
                } else {
                    swal({
                        title: "Lỗi hệ thống",
                        text: "Logout Error!!!",
                        type: "error",
                        showCancelButton: false,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    });
                }

            }
        });
    }

    $('.preloader-it').fadeOut();
</script>
@stack('scripts')
</body>
</html>
