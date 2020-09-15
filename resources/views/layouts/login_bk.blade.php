<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap&subset=vietnamese"
          rel="stylesheet">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="sso_smsc_apikey" content="bd629ce2de47436e3a9cdd2673e97b17"/>
    <meta name="ssls.validate" content="false"/>

{{--    <title>Đào tạo trực tuyến nhân viên bán hàng</title>--}}
{{--    <link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
<!-- Font Awesome -->
    <link href="/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="/design/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/dist/css/font-awesome.all.min.css" rel="stylesheet">

{{--    <link href="{{ asset('template/colors/dist/css/style.min.css') }}" rel="stylesheet">--}}
<!-- Your custom styles (optional) -->
    <link href="/css/custom.css" rel="stylesheet">

    <link href="/css/sweetalert.css" rel="stylesheet">
    {{--    <link href="{{ asset('css/style.css') }}" rel="stylesheet">--}}
    <link href="/template/colors/dist/css/pages/login-register-lock.css" rel="stylesheet">
</head>

<body class="">
<style>
    body:before {
        content: '';
        height: 1px;
        background: #eee;
        position: absolute;
        left: 0;
        right: 0;
        top: 0px;
    }
</style>

<section class="login-block d-flex align-items-center">
    @yield('content')
</section>

<!-- SCRIPTS -->
<!-- JQuery -->


<script src="/template/assets/node_modules/jquery/jquery-3.2.1.min.js"></script>

<script src="/template/assets/node_modules/popper/popper.min.js"></script>
<script src="/template/assets/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- MDB core JavaScript -->
<!-- MDB core JavaScript -->
<script src="/assets/landing/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="/js/custom.js"></script>
<script src="/js/sweetalert.min.js"></script>
<script src="/sso/sslssso.js"></script>
<script>
    $(function () {
        $(".preloader").fadeOut();
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


    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    // ==============================================================
    // Login and Recover Password
    // ==============================================================
    $('#to-recover').on("click", function () {
        $("#loginform").slideUp();
        $("#recoverform").fadeIn();
    });

</script>

@stack('scripts')
</body>

</html>
