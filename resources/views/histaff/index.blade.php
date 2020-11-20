<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'en' ? app()->getLocale() : 'vi' }}">
<head>
    <meta charset="utf-8">

    <meta property="og:title" content="Elearning"/>
    <meta property="og:type" content="website"/>
    <meta property="og:image" content="/images/favicon.png"/>
    <meta property="og:description" content="Elearning"/>

    <!-- Include this to make the og:image larger -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{Config::get('constants.domain.TMS_NAME')}}</title>
    <meta name="description" content="Elearning"/>
    <link rel="shortcut icon" href="/images/favicon.ico">
    <link rel="icon" href="/images/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap&subset=vietnamese"
          rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="sso_smsc_apikey" content="bd629ce2de47436e3a9cdd2673e97b17"/>


</head>

<body class="">


<script src="/js/jquery.min.js"></script>
<script src="/sso/sslssso.js"></script>
<script src="/js/loadingoverlay.min.js"></script>

<script>
    var logoutCookie = '__c2FmYXJpVmVyaWZpY2F0aW9uVG9rZW4UfFzcvye';
    var safariCookie = '__c2FmYXJpVmVyaWZpY2F0aW9uVG9rZW4UfL';

    $(document).ready(function () {
        login_elearning();
    });


    function loginLMS(data) {
        $.ajax({
            type: "POST",
            url: '/lms/loginfirst.php',
            data: {
                data: data.data
            },
            success: function (res) {
                $.LoadingOverlay("hide");
                window.location.href = '/lms/my';
            }
        });
    }

    function login_elearning() {
        $.LoadingOverlay("show");
        var token = '{{$token}}';
        var username = '{{$username}}';

        $.ajax({
            type: "POST",
            url: '/elearning/v1/auth',
            data: {
                username: username,
                token_histaff: token,
                _token: '{{csrf_token()}}'
            },
            success: function (data) {
                if (data.status === "SUCCESS") {
                    $('.message.error').hide();

                    localStorage.setItem('remember', 'save');

                    var userinfo = {
                        id: data.id,
                        username: data.username,
                        avatar: data.avatar,
                        fullname: data.fullname,
                        domain: data.domain
                    };

                    let d = new Date();
                    d.setTime(d.getTime() + (24 * 60 * 60 * 1000));
                    var expires = "expires=" + d.toUTCString();

                    document.cookie = safariCookie + "=" + data.jwt + ";" + expires + ";path=/";

                    localStorage.setItem(logoutCookie, 'login');
                    localStorage.setItem('auth.token', data.jwt);
                    localStorage.setItem('auth.user', JSON.stringify(userinfo));
                    localStorage.setItem('auth.lang', 'en');
                    sslssso.login(data.jwt);
                    loginLMS(data);

                } else if (
                    data.status === "FAILUSER" || data.status === "FAILPASSWORD" || data.status === "FAILBANNED" || data.status === "FAILORGANIZATION" ||
                    data.status === "INVALID" || data.status === "FAILCONFIRM" || data.status === "FAILCODE" || data.status === "FAILVALIDATECODE"
                    || data.status === "FAILTOKEN"
                ) {
                    $.LoadingOverlay("hide");
                    if (data.status === "FAILPASSWORD" || data.status === "FAILUSER") {
                        alert('Username or password incorrect');
                    }
                    if (data.status === "FAILBANNED") {
                        alert('User is banned');
                    }
                    if (data.status === "FAILTOKEN") {
                        alert('Token is not valid');
                    }

                    window.location.href = '/sso/authenticate?apiKey=bd629ce2de47436e3a9cdd2673e97b17&callback=' + '{{Config::get('constants.domain.TMS')}}';
                }

            }
        });
    }

</script>
@stack('scripts')
</body>
</html>
