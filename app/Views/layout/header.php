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
    <link rel="shortcut icon" type="image/x-icon" href="resources/css/" >
    
    <title>WIZISLAND</title>

    <!-- css -->
    <link rel="stylesheet" href="resources/css/style.css">
    <link rel="stylesheet" href="resources/css/remodal.css">
    <link rel="stylesheet" href="resources/css/remodal-default-theme.css">
    <!-- js -->
    <script type="text/javascript" src="resources/js/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="resources/js/remodal.js"></script>
	<script type="text/javascript" src="resources/js/common.ui.js"></script>
	<script type="text/javascript" src="resources/js/main.js"></script>
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
                <a href="alarm/alarm.html" class="top_info alarm_type">
                    <i class="icon-icon_alarm" title="알림"></i>
                </a>
            </div>
        </div>

        <!-- 메뉴 탭 (닫기) -->
        <div class="mobile-menu nav-wrap">
            <div class="bg_wrap"></div>
            <div class="menu_content">
                <div class="mobile_menu_header">
                    <div class="btnAll">
                        <span class="btn-icon btn-close"><i class="icon-icon_close"></i></span>
                    </div>
                    <!-- [ 학부모앱 : 학부모앱에서만 보이기 - 학부모 정보 ] -->
                    <p class="h_setting">
                        <span class="h_val">010-****-9149</span>
                        <a href=""><i class="icon-icon_setting"></i></a>
                    </p>
                    <!-- [ 교사앱 : 교사앱에서만 보이기 - 교사정보 ] -->
                    <!-- <p class="h_setting t_setting">
                        <span class="h_val">90000</span>
                        <a href=""><i class="icon-icon_setting"></i></a>
                    </p> -->
                </div>
                <div class="menu_container">
                    <!-- [ 학부모앱 : 등록된 아이들별로 목록 생성 ] -->
                    <!-- 메뉴 - 등록된 아이 목록 -->
                    <div class="child_list menu_list">
                        <div class="cont_box">
                            <div class="cont_img">
                                <img src="resources/images/children-img1.png" alt="children-img">
                                <a href="" class="image_edit"></a>
                            </div>
                            <div class="cont_txt">
                                <p class="name">김위즈</p>
                                <p class="num">28075</p>
                                <p class="office">위즈아일랜드 압구정점</p>
                            </div>
                        </div>
                        <div class="class_status">
                            <ul class="class_list">
                                <li class="list_name"><a href="">7B-IRIS<span class="status">수강중</span><i class="icon_arrow"></i></a> </li>
                                <li class="list_name"><a href="">7B-Brown<span class="status">수강중</span><i class="icon_arrow"></i></a></li>
                                <!-- [ 메뉴탭(닫기) > 수강리스트 ] : 수강중이 아닐 경우 > .classEnd 추가(흐림처리)  a 태그 onclick="return false;" 추가 -->
                                <li class="list_name classEnd"><a href="" onclick="return false;" class="end_class">7B-종강<span class="status">종강</span><i class="icon_arrow"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="child_list menu_list">
                        <div class="cont_box">
                            <div class="cont_img">
                                <img src="resources/images/children-img2.png" alt="children-img">
                                <a href="" class="image_edit"></a>
                            </div>
                            <div class="cont_txt">
                                <p class="name">김둘째</p>
                                <p class="num">28076</p>
                                <p class="office">위즈아일랜드 압구정점</p>
                            </div>
                        </div>
                        <div class="class_status">
                            <ul class="class_list">
                                <li class="list_name"><a href="">7B-IRIS<span class="status">수강중</span><i class="icon_arrow"></i></a></li>
                                <li class="list_name"><a href="">7B-Brown<span class="status">수강중</span><i class="icon_arrow"></i></a></li>
                                <!-- [ 메뉴탭(닫기) > 수강리스트 ] : 수강중이 아닐 경우 > .classEnd 추가(흐림처리)  a 태그 onclick="return false;" 추가 -->
                                <li class="list_name classEnd"><a href="" class="end_class" onclick="return false;">7B-종강<span class="status">종강</span><i class="icon_arrow"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="child_list menu_list">
                        <div class="cont_box">
                            <div class="cont_img">
                                <img src="resources/images/children-img2.png" alt="children-img">
                                <a href="" class="image_edit"></a>
                            </div>
                            <div class="cont_txt">
                                <p class="name">김셋째</p>
                                <p class="num">28077</p>
                                <p class="office">위즈아일랜드 압구정점</p>
                            </div>
                        </div>
                        <div class="class_status">
                            <ul class="class_list">
                                <li class="list_name"><a href="">7B-IRIS<span class="status">수강중</span><i class="icon_arrow"></i></a></li>
                                <li class="list_name"><a href="">7B-Brown<span class="status">수강중</span><i class="icon_arrow"></i></a></li>
                                <!-- [ 메뉴탭(닫기) > 수강리스트 ] : 수강중이 아닐 경우 > .classEnd 추가(흐림처리)  a 태그 onclick="return false;" 추가 -->
                            </ul>
                        </div>
                    </div>
                    
            
                    <!-- [ 교사앱 : 교사앱에서만 보이기 - 담당과목 리스트 ] -->
                   <!-- <div class="teacher_list menu_list">
                        <div class="cont_box">
                            <div class="cont_img">
                                <img src="resources/images/children-img1.png" alt="teacher-img">
                                <a href="" class="image_edit"></a>
                            </div>
                            <div class="cont_txt">
                                <p class="name t_name">Jessica <span class="">(선생님)</span></p>
                                <p class="office">위즈아일랜드 압구정점</p>
                            </div>
                        </div>
                        <div class="class_status">
                            <ul class="class_list">
                                <li class="list_name"><a href="">7B-IRIS<i class="icon_arrow"></i></a></li>
                                <li class="list_name"><a href="">7B-Brown<i class="icon_arrow"></i></a></li>
                                 [ 메뉴탭(닫기) > 수강리스트 ] : 수강중이 아닐 경우 > .classEnd 추가(흐림처리)  a 태그 onclick="return false;" 추가
                                <li class="list_name classEnd"><a href="" onclick="return false;" class="end_class">7B-종강<span class="status">종강</span><i class="icon_arrow"></i></a></li>
                                <li class="list_name"><a href="">7B-IRIS<i class="icon_arrow"></i></a></li>
                                <li class="list_name"><a href="">7B-Brown<i class="icon_arrow"></i></a></li>
                                [ 메뉴탭(닫기) > 수강리스트 ] : 수강중이 아닐 경우 > .classEnd 추가(흐림처리)  a 태그 onclick="return false;" 추가 
                                <li class="list_name classEnd"><a href="" onclick="return false;" class="end_class">7B-종강<span class="status">종강</span><i class="icon_arrow"></i></a></li> 
                            </ul>
                        </div>
                    </div>-->
                </div>
                <div class="mobile_menu_footer">
                    <select name="" id="" size="">
                        <option value="">2024년</option>
                        <option value="">2023년</option>
                        <option value="">2022년</option>
                        <option value="">2021년</option>
                        <option value="">2020년</option>
                    </select>
                    <button>로그아웃</button>
                </div>
            </div>
        </div>
        <!-- //메뉴 탭 (닫기) -->
        <!-- //header -->