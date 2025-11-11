<?php

/**
 * Page FAQs
 *
 * [VinhPT]
 */

require_once(__DIR__ . '/../../../../config.php');

require_login();
global $DB, $USER;

$sql_tabs = "select @s:=@s+1 stt,tms_faq_tabs.id as tab_id, tms_faqs.id, tms_faq_tabs.name as tab_name, tms_faqs.name, tms_faqs.content from tms_faq_tabs left join tms_faqs on tms_faq_tabs.id = tms_faqs.tab_id, (SELECT @s:= 0) AS s";
$tabs = array_values($DB->get_records_sql($sql_tabs));

$faq_data = [];
foreach ($tabs as $tab) {
    if (array_key_exists($tab->tab_id, $faq_data)) {
        $faq_data[$tab->tab_id]['items'][] = [
            'name' => $tab->name,
            'content' => $tab->content
        ];
    } else {
        $faq_data[$tab->tab_id] = [
            'name' => $tab->tab_name,
            'items' => []
        ];
        if (is_numeric($tab->id) && $tab->id != 0) {
            $faq_data[$tab->tab_id]['items'] = [
                [
                    'name' => $tab->name,
                    'content' => $tab->content
                ]
            ];
        }
    }
}

$key_first = '' ;
if(function_exists('array_key_first')){

    $key_first = array_key_first($faq_data);

}else{

    foreach ($faq_data as $key => $faq_data_item ){
        $key_first = $key;
        break;
    }
}

echo $OUTPUT->header();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">

    <title>FAQ with Categories</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <style type="text/css">
        body {
            padding-top: 30px;
        }

        .faq-cat-content {
            margin-top: 25px;
        }

        .faq-cat-tabs li a {
            padding: 15px 10px 15px 10px;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            color: #777777;
        }

        .nav-tabs li a:focus,
        .panel-heading a:focus {
            outline: none;
        }

        .panel-heading a,
        .panel-heading a:hover,
        .panel-heading a:focus {
            text-decoration: none;
            color: #777777;
        }

        .faq-cat-content .panel-heading:hover {
            background-color: #efefef;
        }

        .active-faq {
            border-left: 5px solid #888888;
        }

        .panel-faq .panel-heading .panel-title span {
            font-size: 13px;
            font-weight: normal;
        }

        .faq-cat-tabs {
            border: 0;
        }

        .faq-cat-tabs li a {
            display: block;
            padding: 10px;
            font-size: 14px;
            font-weight: 500;
            color: #000;
        }

        .faq-cat-tabs li.active a {
            border-bottom: 1px solid #fff;
        }

        .faq-cat-content {
            margin: 0;
            padding: 10px;
            border: 1px solid #dddddd;
            margin-top: -1px;
        }

        .faq-cat-content > .tab-pane.active {
            opacity: 1;
        }

        .faq-cat-content .tab-pane .panel-heading {
            padding: 5px;
        }

        .faq-cat-content .tab-pane h4.panel-title {
            font-size: 14px;
            margin: 0;
            color: #3659a2;
            padding: 5px 25px 5px 0;
            position: relative;
        }

        .faq-cat-content .tab-pane .ic_push {
            display: inline-block;
            font: normal normal normal 14px/1 FontAwesome;
            font-size: inherit;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-size: 14px !important;
            position: absolute;
            right: 0;
            top: 50%;
            margin-top: -7px;
        }

        .faq-cat-content .tab-pane .collapsed .ic_push:before{
            content: "\f067";
        }

        .faq-cat-content .tab-pane .ic_push:before{
            content: "\f068";
        }

        .faq-cat-content .tab-pane .panel-body {
            padding: 5px;
            font-size: 14px;
        }

        .faq-cat-content .tab-pane .panel-collapse.collapse {
            overflow: hidden;
        }

    </style>
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript">
        window.alert = function() {};
        var defaultCSS = document.getElementById('bootstrap-css');

        function changeCSS(css) {
            if (css) $('head > link').filter(':first').replaceWith('<link rel="stylesheet" href="' + css + '" type="text/css" />');
            else $('head > link').filter(':first').replaceWith(defaultCSS);
        }
        $(document).ready(function() {
            var iframe_height = parseInt($('html').height());
            window.parent.postMessage(iframe_height, '/');
        });
    </script>
</head>

<body>
<!--    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">-->
<!--    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>-->
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->

    <div class="container">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <!-- Nav tabs category -->
                <ul class="nav nav-tabs faq-cat-tabs">
                    <?php foreach ($faq_data as $tab_id => $tab) { ?>
                        <li class="<?php if ($tab_id == $key_first) echo 'active' ?>"><a href="#faq-cat-<?php echo $tab_id ?>" data-toggle="tab"><?php echo $tab['name'] ?></a></li>
                    <?php } ?>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content faq-cat-content">

                    <?php foreach ($faq_data as $tab_id => $tab) { ?>
                        <div class="tab-pane <?php if ($tab_id == $key_first) echo 'active in' ?> fade" id="faq-cat-<?php echo $tab_id ?>">
                            <div class="panel-group" id="accordion-cat-<?php echo $tab_id ?>">
                                <?php foreach ($tab['items'] as $key => $faq) { ?>
                                <div class="panel panel-default panel-faq">
                                    <div class="panel-heading">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion-cat-<?php echo $tab_id ?>" href="#faq-cat-<?php echo $tab_id ?>-sub-<?php echo $key ?>">
                                            <h4 class="panel-title">
                                                <?php echo $faq['name'] ?>
                                                <span class="ic_push"></span>
                                            </h4>
                                        </a>
                                    </div>
                                    <div id="faq-cat-<?php echo $tab_id ?>-sub-<?php echo $key ?>" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <?php echo $faq['content'] ?>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    </script>
</body>

</html>



<?php
echo $OUTPUT->footer();
die;
?>
