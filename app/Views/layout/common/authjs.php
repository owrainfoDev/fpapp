<?php 
    $contpn = isset( $header['pn'] ) ? $header['pn'] : 'main' ;
?>
<script src="https://cdn.jsdelivr.net/npm/underscore@latest/underscore-umd-min.js"></script> 
<script type="text/javascript">

var default_data;
var content_data;
var contpn = '<?php echo $contpn;?>';
var BaseUrl = "<?php echo base_url();?>";

window.onload = function(){
    window.addEventListener("flutterInAppWebViewPlatformReady", function(event) {
        window.flutter_inappwebview.callHandler("get", "accToken").then(function(result) {

            var d = {accToken: result[0]};

            fetch("/api/authCheck", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(d),
                }).then((response) => response.json())
                .then((data) => {
                    // document.querySelector('#token').innerHTML = JSON.stringify(data);
                    if (data.resultCode != '1000') {
                        // 토큰 정보가 올바르지 않을때 
                        console.log("토큰불일치");
                        console.log( data );
                        window.flutter_inappwebview.callHandler("call", "reauth").then(function(result) {
                            console.log('logout' + result); 
                        });
                    } else {

                        if ( data.aca_id != "FP00070" ){
                            if ( $('#icon_HomeCommingConsent').length > 0 ) {
                                $('#icon_HomeCommingConsent').css('display', 'block');
                            }
                            if ( $('#icon_Medicine').length > 0 ) {
                                $('#icon_Medicine').css('display', 'block');
                            }
                            if ( $('#icon_notice').length > 0 ) {
                                $('#icon_notice').css('display', 'block');
                            }
                            if ( $('#icon_waitingBtn').length > 0 ) {
                                $('#icon_waitingBtn').css('display', 'block');
                            }
                        }

                        default_data = data;
                        view('process', data , contpn);
                    }
                    return data;
                }).then((data) => {

                    // 선택된 원생 보내기 
                    if (data.is_teacher == false && data.std_id) {
                        window.flutter_inappwebview.callHandler('call', 'std_id' , data.std_id);
                    }
                    

                    var chatBtns = document.querySelector('#chatBtn');
                    if ( chatBtns !== null ) {
                        chatBtns.addEventListener("click",function(){
                            // document.querySelector('#token').innerHTML = '1241412414124';
                            if (window.flutter_inappwebview) {
                                window.flutter_inappwebview.callHandler('call', 'chat');
                            }
                        }); 
                    }
                    
                    var logoutBtn = document.querySelector('#logoutBtn');
                    if (logoutBtn !== null){
                        logoutBtn.addEventListener("click",function(){
                            if (window.flutter_inappwebview) {
                                    window.flutter_inappwebview.callHandler('call', 'logout');
                            }
                        }); 
                    }   

                    var settingBtn = document.querySelector('#settingBtn');
                    if (settingBtn !== null){
                        settingBtn.addEventListener("click",function(){
                            if (window.flutter_inappwebview) {
                                    window.flutter_inappwebview.callHandler('call', 'setting');
                            }
                        }); 
                    }


                    $(document).on('click', '#mainPaymentBtn' , function(){
                        if (window.flutter_inappwebview) {
                            window.flutter_inappwebview.callHandler('call', 'payment_list', data.std_id);
                        } else {
                            location.href="payment";
                        }
                    })

                })
            });

            
        }); 

        $(document).on('click','.pay_btn > a.done', function(){
            if (window.flutter_inappwebview) {
                window.flutter_inappwebview.callHandler('call', 'invoice' , $(this).data('enc'));
            }
        });

        $(document).on('click','.pay_btn > a.wait', function(){
            if (window.flutter_inappwebview) {
                window.flutter_inappwebview.callHandler('call', 'receipt' , $(this).data('enc'));
            }
        });

        

        // 프로필 사진
        $(document).on('click' , '#mainProfileImageBtn' , function(){
            if (window.flutter_inappwebview) window.flutter_inappwebview.callHandler('call', 'profile');
        })

        // 이미지 상세보기
        $(document).on('click' , ".previewImage" , function(){
            // var arr = [$(this).attr('src')];
            var arr = [];
            var first = $(this).data('src');
            arr.push(first);
            $.each( $(".previewPhoto-" + $(this).data("id") ) , function(key, item) {
                if (first != $(this).data('src') ) arr.push( $(this).data('src') );
            } )

            // console.log(arr);

            if (window.flutter_inappwebview) window.flutter_inappwebview.callHandler('call', 'preview' , arr );
        })

        $(document).on('click' , "#notificationBtn" , function(){
            if (window.flutter_inappwebview) window.flutter_inappwebview.callHandler('call', 'notification') ;
        });

        $(document).on('click' , '.waitingBtn', function(){
            if (window.flutter_inappwebview) window.flutter_inappwebview.callHandler('call', 'waiting') ;
        })
        
<?php if ( in_array( $_SERVER["REMOTE_ADDR"]  , array('106.254.236.154','106.254.236.156') ) ) { ?>    
    
    if (!window.flutter_inappwebview) {
    // pc 용 확인
    fetch("/api/authCheck", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            accToken: "5GVs4381FJ20Cxysysy7Yy5zwTQuATwOjT+yGj0IN5cK3yk3KBlLDiR4xXJX2Jl8lA3CbOlZdozY2GWLqiqCxCV2MouaTdrXzYBl9/4cId0nnr94Qw8y3qjacQMc96b0c89xkOBv77l6wlWgRv7ChUwyesjm/AfBMRcMBykAIa3120iCWhHITkuW/LBLdbq1ZWNueCNzsZUZ1aSZ+IQfgP+wLM/Rif2X6EQtxTajcB2FqhqJW5Xs0pDRRNBSgDkB",
            <?php echo csrf_token();?> : "<?php echo csrf_hash() ?>"
        }),
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.resultCode != '1000') {
                console.log('토큰불일치');
                location.href="/error";
            } else {

                if ( data.aca_id != "FP00070" ){
                    if ( $('#icon_HomeCommingConsent').length > 0 ) {
                        $('#icon_HomeCommingConsent').css('display', 'block');
                    }
                    if ( $('#icon_Medicine').length > 0 ) {
                        $('#icon_Medicine').css('display', 'block');
                    }
                    if ( $('#icon_notice').length > 0 ) {
                        $('#icon_notice').css('display', 'block');
                    }
                    if ( $('#icon_waitingBtn').length > 0 ) {
                        $('#icon_waitingBtn').css('display', 'block');
                    }
                }

                default_data = data;
                view('process', data , contpn);
            }

            return data;
        })
    }
<?php } ?>
}

function view(html , data = null , type = null){
        
    var current_year;
    data.<?php echo csrf_token();?> = "<?php echo csrf_hash() ?>";
    if ( data.year == "undefined" || data.year == null ){
        data.year = sessionStorage.getItem('year') ? sessionStorage.getItem('year') : null;
    }
    current_year = data.year;

    var forms = {
        aca_id : data.aca_id , 
        is_teacher : data.is_teacher,
        resultCode : data.resultCode,
        std_id : data.std_id,
        user_id : data.user_id,
        user_nm : data.user_nm,
        year : data.year,
        <?php echo csrf_token();?> : "<?php echo csrf_hash() ?>",
    }

    if (html == 'process'){
        fetch("/api/menu", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(forms),
        })
        .then((response) => response.json())
        .then((data) => {
            // $('#resultProcess').html(JSON.stringify(data));
            // 최상단 
            var template = _.template($("#menuHeader").html());
            var result = template(data);
            $("#h_val").html( result );
            // menu
            if ( data.is_teacher == true ) {
                var template = _.template($("#teacherMenuList").html());
                var result = template(data);
                $(".menu_container").html( result );

            } else {
                var template = _.template($("#parentMenuList").html());
                var result = template(data);
                $(".menu_container").html( result );
            }
            
            // year logout;
            var template = _.template($("#yearlogout").html());
            var result = template(data);
            $("#academyYear").html( result );
            // main header
            content_data = data.menuHeader;
            content_data.year = current_year;
            if (typeof LoadContentTemplate === 'function'){
                LoadContentTemplate();
            }

            return content_data;
        })
        .then((data) => {
            getAlramCnt(data.USER_ID).then(data => {
                if (data > 0) $('#notificationBtn').addClass('active');
            });
            return data;
        })
    }
}

// 연도 변경 
$(document).on('change', '#academyYear' , function(){
    var year = $(this).val();

    data = default_data;
    data.year = year;
    view('process', data , 'main');
    sessionStorage.setItem('year', year) ;

    $(document).on('click', '.setStdBtn' , function(){
        var data = {
                std_id : $(this).data('std_id') 
            };
        fetch("/api/menuSetStd", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
            }).then((response) => response.json())
            .then((data) => {
                console.log(data);
            });
    });

});


function setStdBtn(std_id){
    var data = { 
        std_id : std_id,
        <?php echo csrf_token();?> : "<?php echo csrf_hash() ?>",
    };

    fetch("/api/menuSetStd", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
        }).then((response) => response.json())
        .then((data) => {
            if (data.status == "success"){
                location.reload();
            }
        });
}

async function getAlramCnt(user_id){
    var data = {
        USER_ID : user_id,
        <?php echo csrf_token();?> : "<?php echo csrf_hash() ?>"
    }
    var data = await fetch("/api/alramCount", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    })
    .then((response) => response.json())
    .then((data) => {
        return data;
    })
    
    return data;
}
</script>

<code>
    <div id="resultProcess"></div>
</code>

<!-- 메뉴 프로파일 사진 교체 이미지  -->

<!-- // 메뉴 프로파일 사진 교체 이미지  -->

<!-- 메뉴 공통 --> 
<script type="text/template" id="menuHeader">
    <% if ( is_teacher == false ) { %>
        <!-- <%= menuHeader.HP_NO %> -->
        <!-- 여기서 부터 코딩 -->
        <div class="userBox">
            <div class="userImage" id="mainProfileImageBtn">
                <img src="<%= menuHeader.PROFILE %>" alt="" onError='javascript:this.src="/resources/images/png_human.png"'>
            </div>
            <div class="userName">
                <!-- 로그인한 학부모 이름 -->
                <span><%= menuHeader.USER_NM %></span>
            </div>
        </div>

    <% } else { %>
        <div class="userBox">
            <div class="userImage" id="mainProfileImageBtn">
                <img src="<%= menuHeader.PROFILE %>" alt="" onError='javascript:this.src="/resources/images/png_human.png"'>
            </div>
            <div class="userName">
                <!-- 로그인한 선생님 이름 -->
                <span><%= menuHeader.USER_ID %></span>
            </div>
        </div>
    <% } %>
</script>

<script type="text/template" id="parentMenuList">
    <!-- [ 학부모앱 : 등록된 아이들별로 목록 생성 ] -->
    <!-- 메뉴 - 등록된 아이 목록 -->
    <% _.each(menuList,function(item,key,list) {%>
        <% 
            if ( item.STD_URL != null ) {
                profile = item.STD_URL
            } else {
                profile = ''
            }
         %>
        <div class="child_list menu_list">
            <div class="cont_box">
                <div class="cont_img" onclick="javascript:setStdBtn('<%= item.STD_ID%>')" style="cursor:pointer">
                    <img src="<%= profile %>" alt="children-img" id="childimage<%= item.STD_ID%>" onError='javascript:this.src="/resources/images/png_human.png"'>
                    <!-- <a href="#" class="image_edit" id="menuProfileEditBtn"></a> -->
                </div>
                <div class="cont_txt">
                    <p class="name"><%= item.USER_NM%></p>
                    <p class="num" onclick="javascript:setStdBtn('<%= item.STD_ID%>')"><%= item.STD_ID%></p>
                    <p class="office"><%= item.ACA_NM%></p>
                </div>
            </div>
            <div class="class_status">
                <ul class="class_list">
                    <% _.each(item.CLASS , function(i,k,l){ %> 
                        <% if (i.CLASS_APPLY_STATUS == '03') { 
                                classEnd = "classEnd"
                            } else {
                                classEnd = ""
                            }
                        %>
                    <li class="list_name <%= classEnd %>"><a href=""><%= i.CLASS_NM %><span class="status <%= classEnd %>"><%= i.STATUS_NM %></span><i class="icon_arrow"></i></a> </li>
                    <% }) %>
                    <!-- <li class="list_name"><a href="">7B-Brown<span class="status">수강중</span><i class="icon_arrow"></i></a></li> -->
                    <!-- [ 메뉴탭(닫기) > 수강리스트 ] : 수강중이 아닐 경우 > .classEnd 추가(흐림처리)  a 태그 onclick="return false;" 추가 -->
                    <!-- <li class="list_name classEnd"><a href="" onclick="return false;" class="end_class">7B-종강<span class="status">종강</span><i class="icon_arrow"></i></a></li> -->
                </ul>
            </div>
        </div>
    <% }); %>
</script>

<script type="text/template" id="teacherMenuList">
    <!-- [ 교사앱 : 교사앱에서만 보이기 - 담당과목 리스트 ] -->
    <div class="teacher_list menu_list">
        <div class="cont_box">
            <div class="cont_img">
                <img src="<%= menuHeader.PROFILE %>" alt="teacher-img" id="teacherImg" onError='javascript:this.src="/resources/images/png_human.png"'>
                
            </div>
            <div class="cont_txt">
                <p class="name t_name"><%= menuHeader.USER_NM %> <span class="">(<%= menuHeader.AUTHNM %>)</span></p>
                <p class="office"><%= menuHeader.ACA_NM %></p>
            </div>
        </div>
        <div class="class_status">
            <ul class="class_list">
                <% _.each(menuList,function(item,key,list){ %>
                <li class="list_name"><a href=""><%= item.CLASS_NM %><i class="icon_arrow"></i></a></li>
                <% }) %>
                <!-- [ 메뉴탭(닫기) > 수강리스트 ] : 수강중이 아닐 경우 > .classEnd 추가(흐림처리)  a 태그 onclick="return false;" 추가 -->
                <!-- <li class="list_name classEnd"><a href="" onclick="return false;" class="end_class">7B-종강<span class="status">종강</span><i class="icon_arrow"></i></a></li> -->
                <!-- [ 메뉴탭(닫기) > 수강리스트 ] : 수강중이 아닐 경우 > .classEnd 추가(흐림처리)  a 태그 onclick="return false;" 추가  -->
                
            </ul>
        </div>
    </div>
</script>

<script type="text/template" id="menulist">
    <% if ( is_teacher == false ) { %>
    <!-- [ 학부모앱 : 등록된 아이들별로 목록 생성 ] -->
    <!-- 메뉴 - 등록된 아이 목록 -->
    <% _.each(menuList,function(item,key,list) {%>
        <div class="child_list menu_list">
            <div class="cont_box">
                <div class="cont_img">
                    <img src="/resources/images/children-img1.png" alt="children-img">
                    <a href="#" class="image_edit" id="menuProfileEditBtn"></a>
                </div>
                <div class="cont_txt">
                    <p class="name"><%= item.USER_NM%></p>
                    <p class="num" onclick="javascript:setStdBtn('<%= item.STD_ID%>')"><%= item.STD_ID%></p>
                    <p class="office"><%= item.ACA_NM%></p>
                </div>
            </div>
            <div class="class_status">
                <ul class="class_list">
                    <% _.each(item.CLASS , function(i,k,l){ %> 
                        <% if (i.CLASS_APPLY_STATUS == '03') { 
                                classEnd = "classEnd"
                            } else {
                                classEnd = ""
                            }
                        %>

                    <li class="list_name <%= classEnd %>"><a href=""><%= i.CLASS_NM %><span class="status <%= classEnd %>"><%= i.STATUS_NM %></span><i class="icon_arrow"></i></a> </li>
                    <% }) %>
                    <!-- <li class="list_name"><a href="">7B-Brown<span class="status">수강중</span><i class="icon_arrow"></i></a></li> -->
                    <!-- [ 메뉴탭(닫기) > 수강리스트 ] : 수강중이 아닐 경우 > .classEnd 추가(흐림처리)  a 태그 onclick="return false;" 추가 -->
                    <!-- <li class="list_name classEnd"><a href="" onclick="return false;" class="end_class">7B-종강<span class="status">종강</span><i class="icon_arrow"></i></a></li> -->
                </ul>
            </div>
        </div>
    <% }); %>
    
    
    <% } else { %>
    <!-- [ 교사앱 : 교사앱에서만 보이기 - 담당과목 리스트 ] -->
    <div class="teacher_list menu_list">
        <div class="cont_box">
            <div class="cont_img">
                <img src="resources/images/children-img1.png" alt="teacher-img">
                <a href="#" class="image_edit"></a>
            </div>
            <div class="cont_txt">
                <p class="name t_name"><%= menuHeader.USER_NM %> <span class="">(선생님)</span></p>
                <p class="office"><%= menuHeader.ACA_NM %></p>
            </div>
        </div>
        <div class="class_status">
            <ul class="class_list">
                <% _.each(menuList,function(item,key,list){ %>
                <li class="list_name"><a href=""><%= item.CLASS_NM %><i class="icon_arrow"></i></a></li>
                <% }) %>
                <!-- [ 메뉴탭(닫기) > 수강리스트 ] : 수강중이 아닐 경우 > .classEnd 추가(흐림처리)  a 태그 onclick="return false;" 추가 -->
                <!-- <li class="list_name classEnd"><a href="" onclick="return false;" class="end_class">7B-종강<span class="status">종강</span><i class="icon_arrow"></i></a></li> -->
                <!-- [ 메뉴탭(닫기) > 수강리스트 ] : 수강중이 아닐 경우 > .classEnd 추가(흐림처리)  a 태그 onclick="return false;" 추가  -->
                
            </ul>
        </div>
    </div>

    <% } %>
</script>

<script type="text/template" id="yearlogout">
    
        <% _.each( menuYear ,function(item,key,list) { %>
        <option value="<%= item.CODE %>" <%= item.SELECTED %> ><%= item.CODE_NM %></option>
        <% }) %>
   
</script>

<script>

// $(window).load(function(){

//     $('img').each(function(){
//         if ( $(this).hasClass('profilephotocheck') ){
//             var errImg = '/resources/images/png_human.png';
//         }

//         $(this).error(function(){
//             $(this).attr('src',errImg);
//         })
        
//     })
// });
</script>