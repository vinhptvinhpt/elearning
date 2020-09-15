<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'en' ? app()->getLocale() : 'vi' }}">
<head>
    <meta charset="utf-8">

    <meta property="og:title" content="Elearning" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="/images/favicon.ico" />
    <meta property="og:description" content="Elearning" />

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{Config::get('constants.domain.TMS_NAME')}}</title>
    <meta name="description" content="Elearning"/>
    <link rel="shortcut icon" href="/images/favicon.png">
<!-- <link rel="icon" href="{{asset('images/favicon.png')}}" type="image/x-icon"> -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap&subset=vietnamese"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="sso_smsc_apikey" content="bd629ce2de47436e3a9cdd2673e97b17"/>

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <!-- Styles -->
    <link href="/css/all.min.css" rel="stylesheet">
    <link href="/assets/landing/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">
</head>
<body class="">

<!-- Scripts -->
<script src="/assets/landing/js/jquery-3.3.1.min.js"></script>
<script src="/assets/landing/js/popper.min.js"></script>
<script src="/assets/landing/js/owl.carousel.min.js"></script>
<script src="/assets/landing/js/bootstrap.min.js"></script>
<!-- MDB core JavaScript -->
<script type="text/javascript" src="/js/custom.js"></script>

<script src="/sso/sslssso.js"></script>
<script>
    var logoutCookie = '__c2FmYXJpVmVyaWZpY2F0aW9uVG9rZW4UfFzcvye';

    var cookieValue = localStorage.getItem(logoutCookie);
    if (cookieValue == undefined || cookieValue == 'logout') {
        displayLogin();
    }

    // displayLogin();
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


    // Fired after validating JWT on page onLoad process, or after a successful identification
    function onIdentification(operation) {
        console.log('onIdentification');
        if (operation.status === 'SUCCESS' && localStorage.getItem("remember") == 'save') {
            $.ajax({
                type: "POST",
                url: '/loginsso',
                data: {
                    username: operation.eIdentifier,
                    _token: '{{csrf_token()}}'
                },
                success: function (data) {
                    if (data.status === "SUCCESS") {
                        sslssso.login(data.jwt);
                        loginLMS(data);
                        // window.location.href = '/tms/dashboard';
                    } else {
                        swal({
                            title: "Thông báo",
                            text: "Username hoặc Password không đúng!!!",
                            type: "error",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        });
                    }

                }
            });
        } else {
            displayLogin();
        }
    }

    //when logout
    function onLogout() {
        displayLogin();
    }

    function loginLMS(data) {
        $.ajax({
            type: "POST",
            url: '/lms/loginfirst.php',
            data: {
                data: data.data
            },
            success: function (res) {
                if (data.redirect_type.includes("lms")) {
                    window.location.href = '{{Config::get('constants.domain.LMS')}}';
                } else {
                    window.location.href = '/lms/my';
                }
            }
        });
    }

</script>

</body>
</html>
