<?php

$user_id = $_GET['userid'];
$item_id = $_GET['itemid'];

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <link href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/vue"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script>
        var USER_ID = "<?= $user_id ?>",
            ITEM_ID = "<?= $item_id ?>"
    </script>
</head>
<body>
<div id="app">
    <div :class="{'nav-active':navActive}" class="mt-3">
        <template v-if="state === 3">
            <div style="text-align: center; padding: 30px 0; ">
                <div style="font-size: 50px; color: #2f9e44;font-weight: bold; margin-bottom: 2px;">Hoàn Thành</div>
                <div style="font-size: 18px;">Điểm số: {{score}}</div>
            </div>
        </template>
        <template v-else>
            <div id="overlay" @click="navActive = !navActive"></div>
            <div class="container">
                <div class="row no-gutter">
                    <div class="col-12 col-md-3" id="nav">
                        <div class="mt-3">
                            <div class="text-center">
                                <div class="h5"><span class="text-muted">Câu hỏi:</span> {{questionIndex +
                                    1}}/{{questions.length}}
                                </div>
                                <div class="h5"><span class="text-muted">Điểm số:</span> {{score}}</div>
                            </div>
                        </div>
                        <hr>
                        <div class="mt-3">
                            <div class="row no-gutters">
                                <template v-for="(question, index) in questions">
                                    <div class="col-6">
                                        <a v-bind:class="{active: index === questionIndex, error: questions[index].state === 1, success: questions[index].state === 2}"
                                           class="question">{{question.name}}</a>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" id="wrapper">
                        <nav class="navbar navbar-light bg-light mb-3">
                            <button @click="navActive = !navActive" class="navbar-toggler" type="button">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <span class="navbar-brand">Điểm số: {{score}}</span>
                        </nav>
                        <div class="alert alert-info" role="alert">
                            <strong>{{questions[questionIndex].name}}.</strong> {{questions[questionIndex].description}}
                        </div>
                        <div id="main" class="mt-3">
                            <div class="wrap" style="position: relative; -transform-origin: 0 0;">
                                <div style="position: absolute; z-index: 9999; top: 10px; left: 150px; width: 340px;">
                                    <transition enter-active-class="animated slideInDown faster"
                                                leave-active-class="animated slideOutUp faster">
                                        <div v-if="alert.show" :class="{['alert-'+alert.type]:true}" class="alert mb-0"
                                             role="alert">
                                            {{alert.message}}
                                        </div>
                                    </transition>
                                </div>
                                <div v-if="state !== 0" class="overlay"
                                     style="position:absolute; width: 640px; height: 480px; top: 0; left: 0; background-color: rgba(0,0,0,0.3); z-index: 99999">
                                    <div style="background-color: #000; width: 300px; height: 200px; position: absolute; top: 140px; left: 170px;">
                                        <div v-if="state === 2"
                                             style="position: relative; color: #40c057; text-align: center; font-size: 20px; padding: 15px;">
                                            Đáp án chính xác. Chuyển sang câu kế tiếp.
                                        </div>
                                        <div v-if="state === 1"
                                             style="position: relative; color: #e03131;  text-align: center; font-size: 20px; padding: 15px;">
                                            Câu trả lời chưa chính xác. Chuyển sang câu kế tiếp.
                                        </div>
                                        <div style="position: relative; text-align: center; margin-top: 10px;">
                                            <a class="ti ti-arrow-right"
                                               style="position: relative; color: #ffffff; font-size: 50px;"
                                               @click.prevent="next()"></a>
                                        </div>
                                    </div>
                                </div>
                                <component :is="componentFile" ref="component" @event="handler" @message="alertShow"></component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
<script type="module" src="app.js"></script>
</body>
</html>

