<?php
require_once(__DIR__ . '/../../../../config.php');
?>

<html>
<title>Đăng nhập</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../../">
<link rel="shortcut icon" href="images/favicon.ico">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/highcharts.js"></script>


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
    @font-face{
        font-family:UTM Bebas;
        src:url("fonts/UTM Bebas.ttf") format("woff"),
        url("fonts/UTM Bebas.ttf") format("opentype"),
        url("fonts/UTM Bebas.ttf") format("truetype");
    }

    body{
        font-size: 14px;
        font-family: Roboto-Bold;
    }
    a{
        text-decoration: none;
    }
    a:hover{
        text-decoration: none;
    }

    .main-bg{
        height: 100%;
        width: 100%;
        background-image: url('images/vietnam-halong-bay-58597.jpg');
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
    }

    .before-main-content{
        position: absolute;
        top: 16%;
        right: 19%;
        height: 80%;
        width: 35%;
        z-index: 1;
        border-radius: 10px;
        background-color: white;
        /*background-image: url('images/vietnam-halong-bay-58597.png');*/
        background: transparent url('images/vietnam-halong-bay-58597.png') 0% 0% no-repeat padding-box;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        padding: 8%;
    }

    .before-main-content p, .before-main-content h3{
        font-family: UTM Bebas;
        color: #FFFFFF;
        font-size: 86px;
        position: absolute;
    }
    .before-main-content p{
        -webkit-text-fill-color: #1d151500;
        -webkit-text-stroke-width: 2px;
        -webkit-text-stroke-color: #FFFFFF;
        top: 45%;
        right: 5%;
    }

    .before-main-content h3{
        text-shadow: 0px 3px 6px #00000029;
        top: 33%;
        left: 17%;
    }

    .main-content{
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background-color: #2020207A;
        opacity: 1;
    }

    .wrap-login100{
        position: absolute;
        top: 23%;
        left: 20%;
        z-index: 1;
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 3px 3px 6px #00000029;
        border-radius: 10px;
        width: 29%;
    }

    .wrap-content{
        padding: 5% 10%;
    }

    .title-login{
        text-align: center;
        letter-spacing: 0.78px;
        color: #202020;
        text-transform: uppercase;
        font-size: 22px;
        margin-bottom: 10%;
    }

    .wrap-input100{
    width: 100%;
        margin: 5% 0;
    }

    .wrap-input100 input{
        text-align: left;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #202020;
        border: none;
        border-bottom: 1px solid #707070;
        width: 90%;
        margin-left: 5%;;
    }

    .wrap-btn100{
        margin-bottom: 5%;
        width: 100%;
    }
    .wrap-btn100 a{
        /*width: 130px;*/
        margin-left: 35%;
        padding: 0% 8%;
        background: #862055 0% 0% no-repeat padding-box;
        border-radius: 4px;
        color: #ffffff;
    }
    .btn-click{
        background: #862055 0% 0% no-repeat padding-box;
        border-radius: 4px;
        color: #FFFFFF;
        text-align: center;
        letter-spacing: 0.6px;
        font-size: 17px;
    }

    .wrap-forgot100{
        text-align: center;
        letter-spacing: 0.45px;
    }

    .wrap-forgot100 p span{
        font-family: Roboto-Regular;
        color: #202020;
        font-size: 13px;
    }

    .wrap-forgot100 p a{
        font-family:  Roboto-Italic;
        letter-spacing: 0.45px;
        color: #0080EF;
        font-size: 13px;
    }

    .wrap-remember100{
        font-family: Roboto-Regular;
        letter-spacing: 0.45px;
        color: #202020;
        margin-left: 5%;
        padding: 2% 0 5% 0;
    }

    .wrap-content .logo{
        margin: 5% 0 5% 31%;
    }

    /*1920*/
    @media screen and (max-width: 1920px){
        .wrap-content .logo {
            margin: 5% 0 5% 27%;
            width: 50%;
        }
        .before-main-content p, .before-main-content h3{
            font-size: 120px;
        }
        .wrap-content p{
            font-size: 31px;
        }
        .wrap-input100 input{
            font-size: 20px;
        }
        .wrap-remember100, .wrap-forgot100 p span, .wrap-forgot100 p a{
            font-size: 18px;
        }
        .wrap-btn100 a{
            font-size: 24px;
        }
    }

    /*1368*/
    @media screen and (max-width: 1368px){
        .before-main-content p, .before-main-content h3{
            font-size: 86px;
        }
        .wrap-content p{
            font-size: 21px;
        }
        .wrap-input100 input{
            font-size: 20px;
        }
        .wrap-input100 input, .wrap-remember100, .wrap-forgot100 p span, .wrap-forgot100 p a, .wrap-btn100 a{
            font-size: 16px;
        }
        .wrap-btn100 a{
            font-size: 20px;
        }
    }

    /*Ipad ngang(1024 x 768)*/
    @media screen and (max-width: 1024px){
        .wrap-login100{
            width: 40%;
            left: 10%;
        }
        .before-main-content{
            height: 80%;
            width: 48%;
            right: 8%;
        }
        .before-main-content p, .before-main-content h3{
            font-size: 70px;
        }
        .wrap-input100 input, .wrap-remember100, .wrap-forgot100 p span, .wrap-forgot100 p a, .wrap-btn100 a{
            font-size: 14px;
        }
        .before-main-content h3{
            left: 15%;
        }
        .before-main-content p{
            top: 44%;
            -webkit-text-fill-color: #1d151500;
            -webkit-text-stroke-width: 1px;
            -webkit-text-stroke-color: #FFFFFF;
        }
        .wrap-content .logo{
            width: 50%;
        }
        .btn-click{
            font-size: 10px;
            margin-left: 20%;
        }
    }
    /*Ipad dọc(768 x 1024)*/
    @media screen and (max-width: 768px){
        .before-main-content{
            display: none;
        }
        .wrap-login100{
            width: 70%;
            position: absolute;
            left: 15%;
            top: 10%;
        }
        .wrap-content p{
            font-size: 21px;
        }
    }
    /*Tablet nhỏ(480 x 640)*/
    @media screen and (max-width: 480px){
        .wrap-login100{
            width: 85%;
            left: 7%;
        }
        .before-main-content{
            width: 70%;
            height: 70%;
            right: 4%;
        }
        .before-main-content p, .before-main-content h3{
            font-size: 30px;
        }
        .before-main-content h3{
            left: 52%;
        }
        .before-main-content p{
            top: 40%;
            -webkit-text-fill-color: #1d151500;
            -webkit-text-stroke-width: 1px;
            -webkit-text-stroke-color: #FFFFFF;
        }
        .wrap-login100, .title-login, .wrap-forgot100 span, .wrap-forgot100 a{
            font-size: 16px !important;
        }

        .wrap-content .logo{
            /*margin: 15% 0;*/
            width: 50%;
        }

        .btn-click{
            font-size: 10px;
            margin-left: 20%;
        }
    }
    /*Iphone(480 x 640)*/
    @media screen and (max-width: 320px){
        .wrap-login100{
            width: 85%;
            left: 7%;
        }
        .before-main-content{
             width: 70%;
            height: 70%;
            right: 4%;
        }
        .before-main-content p, .before-main-content h3{
            font-size: 30px;
        }
        .before-main-content h3{
            right: 30%;
        }
        .before-main-content p{
            top: 40%;
            -webkit-text-fill-color: #1d151500;
            -webkit-text-stroke-width: 1px;
            -webkit-text-stroke-color: #FFFFFF;
        }
        .wrap-login100, .title-login, .wrap-forgot100 span, .wrap-forgot100 a{
            font-size: 16px !important;
        }

        .wrap-content .logo{
            margin: 15% 0;
            width: 100%;
        }

        .btn-click{
            font-size: 10px;
            margin-left: 20%;
        }

    }
    /*Smart phone nhỏ*/
    @media screen and (max-width: 240px){

    }
</style>
<body>
<!--<div id="container1" style="min-width: 300px; height: 400px; margin: 0 auto"></div>-->

<div class="wrapper"><!-- wrapper -->
    <div class="main-bg"></div>
    <div class="main-content"></div>
    <div class="before-main-content">
        <h3>Easia</h3>
        <p>ACADEMY</p>
    </div>
    <div class="wrap-login100">
        <div class="wrap-content">
            <img src="images/logo-black.png" class="logo" alt="">
            <p class="title-login">Welcome <br/> sign in to continue</p>
            <div class="wrap-input100">
                <input type="text" class="input100" placeholder="User name">
            </div>
            <div class="wrap-input100">
                <input type="password" class="input100" placeholder="Password">
            </div>
            <div class="wrap-remember100">
                <input type="checkbox">
                <span>Remeber login</span>
            </div>
            <div class="wrap-btn100">
                <a href="lms/my/index.php" class="btn btn-click">Login</a>
            </div>
            <div class="wrap-forgot100">
                <p><span>Forgot your password?</span> <a href=""> Password retrieval</a></p>
            </div>
        </div>
    </div>
    <div class="block">

    </div>
</div>

<script>
</script>
</body>
</html>


<?php
die;
?>
