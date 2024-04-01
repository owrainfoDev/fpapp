<!DOCTYPE html>
<html lang="ko">
<head>
    <?php echo $this->include('./layout/common/head');?>
    <?php echo $this->include('./layout/common/css');?>
    <?php echo $this->include('./layout/common/js');?>
    <script type="text/javascript" src="resources/js/main.js"></script>
    <?php echo $this->include('./layout/common/authjs');?>
</head>
<body class="home">

<div class="wrap">
    <div id="page">
        <!-- header -->
        <div class="header_wrap">
            <div class="inner">
                <!-- 메뉴 탭 -->
                <div class="mobile_menu">
                    <!-- 열기 -->
                    <div class="btnAll">
                        <span class="btn-icon"></span>
                    </div>
                </div>

                <h1><img src="resources/images/logo.png" alt="logo"></h1>

                <!-- [ 메인 > 알람 ] : 알람 있는 경우 > .active 추가(빨간점) -->
                <span href="alarm/alarm.html" class="top_info alarm_type" id="notificationBtn">
                    <i class="icon-icon_alarm" title="알림"></i>
                </span>
            </div>
        </div>

<?php echo $this->include('./layout/main/menu');?>        
        