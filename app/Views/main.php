<script type="text/javascript">
function LoadContentTemplate(){
    data = content_data;
    
    fetch("/api/profile", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    })
    .then((response) => response.json())
    .then((data) => {
        $('#topprofile').html(data.html);
    })
}

</script>


<script type="text/template" id="mainViewProfileTemplate2">
<% if ( is_teacher == false ) { %>
<div class="cont_txt">
    <p class="name"><%= USER_NM %> <span class="parent">(학부모)</span></p>
    <p class="office"><%= ACA_NM %></p>
    <p class="level">FIRESTONE</p>
</div>
<div class="child_edit">
    <ul>
        <li>
            <a href="">
                <img src="resources/images/children-img1.png" alt="children image">
            </a>
        </li>
        <li>
            <a href="">
                <img src="resources/images/children-img2.png" alt="children image">
            </a>
        </li>
        <li>
            <a href=""></a>
        </li>
    </ul>
</div>
<!-- [ 교사앱 : 교사앱에서만 보이기 - 로그인한 교사정보 ] -->
<% } else { %>
<div class="cont_txt">
    <p class="name"><%= USER_NM %><span class="parent">(선생님)</span></p>
    <p class="office"><%= ACA_NM %></p>
</div>
<% } %>

</script>

<style>
    #icon_HomeCommingConsent , #icon_notice , #icon_waitingBtn  { display: none }
</style>
<!-- main -->
<div class="main_container">
    <div class="h_main">
        <div class="h_cont" id="topprofile">
                
                
        </div>

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
    </div>
</div>

<!-- // main -->
<script type="text/javascript">
$(document).ready(function(){
    $(document).on('click', 'div.menu_cont > span[href]' , function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        if ( typeof href != 'undefined'){
            location.href=href;
        }
    })
})

</script>