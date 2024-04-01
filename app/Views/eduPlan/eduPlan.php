<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content edu_content">
    <div class="sub_inner">
        <div class="t_tab">
            <div class="weekly">
                <p class="active"><a href="edu-plan-weekly.html">주간 교육계획</a></p>
            </div>
            <div class="monthly">
                <p><a href="edu-plan-monthly.html">월간 교육계획</a></p>
            </div>
        </div>
        <div class="search_form">
            <!-- [ 학부모앱 : 검색/셀렉트박스 ] -->
            <form action="" class="p_form">
                <div class="select_form">
                    <div class="select_option option01" style="width: 120px;">
                        <select name="selectClass" id="selectClass">                             
                            <option value="7B-IRIS">7B-IRIS</option>
                            <option value="7B-Brown" selected="">7B-Brown</option>
                            <option value="7B-IRIS">7B-IRIS</option>
                            <option value="7B-Brown">7B-Brown</option>
                        </select>
                    </div>
                </div>
            
            </form>
        </div>
        <!-- 주간교육계획 -->
        <div class="weekly_load active" id="weeklyLoad">
            <div class="edu_container weekly active">
                <!-- [ 더보기로 컨텐츠 로드할 경우 .js-load class 추가 ] -->
                <div class="plan js-load block">
                    <div class="t_info">
                        <div class="edu_title title"><span>9월 둘째 주 주간 교육계획</span></div>
                        <div class="edu_author author"><span>김교사</span></div>
                        <div class="edu_date date"><span>2023-09-01</span></div>
                        <!-- [ 교사앱 : 수정/삭제 버튼 ] -->
                        <!-- <div class="t_edit_btn btn_box">
                            <button type="" class="edit left">수정</button>
                            <button type="" class="del right">삭제</button>
                        </div> -->
                    </div>
                    <div class="plan_cont">
                        <div class="plan_title">
                            <p class="title bg_comm">주차</p>
                            <p class="title_des des">2주차</p>
                        </div>
                    </div>
                    
                    <div class="plan_img">
                        <!-- 주간교육계획 이미지 가져오기 : 등록된 이미지 없을경우 .image_active 클래스 추가 -->
                        <img src="" alt="" class="image_active">
                        <!-- 등록된 이미지 없을경우 .image_active 클래스 제거 -->
                        <div class="img_none">Image</div>
                    </div>                     
                </div>

                <div class="plan js-load">
                    <div class="t_info">
                        <div class="edu_title title"><span>9월 첫째 주 주간 교육계획</span></div>
                        <div class="edu_author author"><span>김교사</span></div>
                        <div class="edu_date date"><span>2023-09-01</span></div>
                        <!-- [ 교사앱 : 수정/삭제 버튼 ] -->
                        <!-- <div class="t_edit_btn btn_box">
                            <button type="" class="edit left">수정</button>
                            <button type="" class="del right">삭제</button>
                        </div> -->
                    </div>
                    <div class="plan_cont">
                        <div class="plan_title">
                            <p class="title bg_comm">주차</p>
                            <p class="title_des des">1주차</p>
                        </div>
                    </div>
                    
                    <div class="plan_img">
                        <!-- 주간교육계획 이미지 가져오기 : 등록된 이미지 없을경우 .image_active 클래스 추가 -->
                        <img src="" alt="" class="image_active">
                        <!-- 등록된 이미지 없을경우 .image_active 클래스 제거 -->
                        <div class="img_none">Image</div>
                    </div>                     
                </div>
                <!-- 더보기 -->
                <div id="js-btn-wrap_weekly" class="btn-wrap">
                    <a href="javascript:;" class="button">더보기<i class="icon_more"></i></a>
                </div>
                <!-- //더보기  -->       
            </div>
            <!-- [ 교사앱 : 주간교육계획 쓰기 ] -->
            <!-- <a href="teacher-weekly-edu-write.html" class="request_writer write_btn" style="background: #fff;"><i></i></a> -->
        </div>
        <!-- [ 교사앱 : 삭제 모달 ]  -->
        <!-- <div class="modal">
            <div class="cont">
                <p>삭제하시겠습니까?</p>
                <div class="btn">
                    <button class="cancel">취소</button>
                    <button type="" class="confirm">확인</button>
                </div>
            </div>
        </div> -->
    </div>
</div>