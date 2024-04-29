
<!-- main -->
<div class="main_container">
    <div class="h_main">
        <div class="h_cont" id="topprofile"></div>

    </div>
    <!-- [] -->
    <div class="main_content">
        <div class="main_menu">
            <div class="menu_cont" id="icon_notice">
                <span href="/notice">
                    <div class="cont_img">
                        <!-- <img src="resources/images/icon/icon_main_note.png" alt="알림장"> -->
                        <i class="menu_icon"></i>
                    </div>
                    <p>알림장</p>
                </span>
            </div>
            <div class="menu_cont">
                <span href="/schoolmeal">
                    <div class="cont_img">
                        <!-- <img src="resources/images/icon/icon_main_meal.png" alt="오늘의급식"> -->
                        <i class="menu_icon meal"></i>
                    </div>
                    <p>오늘의급식</p>
                </span>
            </div>
            <div class="menu_cont">
                <span href="album">
                    <div class="cont_img">
                        <!-- <img src="resources/images/icon/icon_main_album.png" alt="앨범"> -->
                        <i class="menu_icon album"></i>
                    </div>
                    <p>앨범</p>
                </span>
            </div>
            <div class="menu_cont">
                <span href="eduPlan">
                    <div class="cont_img">
                        <!-- <img src="resources/images/icon/icon_main_edu.png" alt="교육계획안"> -->
                        <i class="menu_icon edu"></i>
                    </div>
                    <p>교육계획안</p>
                </span>
            </div>
            <div class="menu_cont" id="icon_Medicine">
                <span href="medicine">
                    <div class="cont_img">
                        <!-- <img src="resources/images/icon/icon_main_medicine.png" alt="투약의뢰서"> -->
                        <i class="menu_icon medicine"></i>
                    </div>
                    <p>투약의뢰서</p>
                </span>
            </div>
            <div class="menu_cont" id="icon_HomeCommingConsent">
                <span href="homeCommingConsent">
                    <div class="cont_img">
                        <!-- <img src="resources/images/icon/icon_main_comming.png" alt="귀가동의서"> -->
                        <i class="menu_icon comming"></i>
                    </div>
                    <p>귀가동의서</p>
                </span>
            </div>
            <div class="menu_cont">
                <span href="appBoard">
                    <div class="cont_img">
                        <!-- <img src="resources/images/icon/icon_main_notice.png" alt="공지사항"> -->
                        <i class="menu_icon notice"></i>
                    </div>
                    <p>공지사항</p>
                </span>
            </div>
            
            <div class="menu_cont">
                <span class="waitingBtn">
                    <div class="cont_img">
                        <!-- <img src="resources/images/icon/icon_main_report.png" alt="report"> -->
                        <i class="menu_icon report"></i>
                    </div>
                    <p>REPORT</p>
                </span>
            </div>
            
            <div class="menu_cont" >
                <span id="mainPaymentBtn">
                    <div class="cont_img">
                        <!-- <img src="resources/images/icon/icon_main_pay.png" alt="수업료 결제"> -->
                        <i class="menu_icon pay"></i>
                    </div>
                    <p>수업료 결제</p>
                </span>
            </div>
            <div class="menu_cont" id="icon_waitingBtn">
                <span class="waitingBtn">
                    <div class="cont_img">
                        <!-- <img src="resources/images/icon/icon_main_after.png" alt="방과후 신청"> -->
                        <i class="menu_icon after"></i>
                    </div>
                    <p>방과후 신청</p>
                </span>
            </div>
            <div class="menu_cont">
                <span id="chatBtn">
                    <div class="cont_img">
                        <!-- <img src="resources/images/icon/icon_main_chat.png" alt="1:1 채팅"> -->
                        <i class="menu_icon chat"></i>
                    </div>
                    <p>1:1 채팅</p>
                </span>
            </div>
        </div>
        <input type="text" name="user_id" id="user_id" value="teacher_h1"><button type="button" id="getUser">확인</button>
    </div>
</div>
<?php 
    $session = session();
    $session->destroy();
    
?>


<!-- // main -->
<script type="text/javascript">
$(document).ready(function(){
    $(document).on('click', '#getUser', function(){
        
    })

    $(document).on('click', 'div.menu_cont > span[href]' , function(e){
        e.preventDefault();

        var token;
        var href = $(this).attr('href');
        var params ;

        var postData = {
            user_id : $('#user_id').val()
        };

        $.ajax({
            type: "POST",
            url : "/api/ajax/UserAccessLogin",
            contentType: "application/json",
            data: JSON.stringify( postData ),
            dataType: "json", 
            async: false,
            success: function(json){
                params = {
                    "token" : json.USERACCESSTOKEN,
                    // "std_id" : 'S00011056',
                    "aca_id" : json.ACA_ID,
                    "aca_year" : '2024',
                    'flag' : 'Y'
                }
            },
            error:function(){  
                //에러가 났을 경우 실행시킬 코드
            }
        })
        if ( typeof href != 'undefined') post(href, params );
       
    })
})

</script>