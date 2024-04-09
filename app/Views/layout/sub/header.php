<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="og:type" content="">
    <meta name="og:title" content="">
    <meta name="og:description" content="">
    <meta name="og:image" content="">
    <meta name="og:url" content="">
    <link rel="shortcut icon" type="image/x-icon" href="/resources/css/" >
    
    

    <?php echo $this->include('./layout/common/head');?>
    <?php echo $this->include('./layout/common/css');?>
    <?php echo $this->include('./layout/common/js');?>
    <script type="text/javascript" src="/resources/js/main.js"></script>
    <?php // echo $this->include('./layout/common/authjs');?>

    
</head>
<body class="bg_color">
    <div id="page">
        <!-- header -->
        <div class="header_wrap">
            <div class="inner">
                <!-- 메뉴 탭 -->
                
                <div class="header_title">
                    <h2><?php echo $header['title']?></h2>
                    <?php
                        $request = service('request');
                        $uri = $request->uri;
                        for( $i = 1 ; $i <= 3 ; $i++){
                            if ($uri->getSegment($i) == '' ) break;
                            $array[] = $uri->getSegment($i);
                        }
                        array_pop($array);
                        if ( in_array( 'eduPlan' , $array ) ) $array = '/';

                        if (  uri_string() != "payment" ) {
                    ?>
                    <a href="<?php echo site_url($array); ?>" class="top_back"><i class="icon-icon_back" title="뒤로가기"></i></a>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
<?php echo $this->include('./layout/sub/menu');?>                