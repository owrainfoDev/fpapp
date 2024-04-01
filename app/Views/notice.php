<script type="text/javascript">
var page = 1;
function LoadContentTemplate(){
    // console.log(content_data);
    var search = $('#noticeSearch').val();
    search = atrim(search);
    content_data.search = search;
    content_data.page = page;

    var perPage = 1;
    var pageCnt = 5;

    content_data.perPage = perPage;
    content_data.pageCnt = pageCnt;

    fetch("/api/notice", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(content_data),
            })
    .then((response) => response.json())
    .then((data) => {
        var template = _.template($('#listTemplate').html());
        var result = template( {
                    list: data.data
            } );
        if ( page == 1 ) {
            $("#noticeLoad").html( result );

        } else {
            $("#noticeLoad").append( result );
        }

        page = page+1;
    });

}


function keyEnterSearch(e){
    // e.preventDefault();
    if (e.keyCode == 13){
        page = 1;
        LoadContentTemplate();
    }
}

</script>   
<script type="text/template" id="listTemplate">
    <% _.each(list,function(item,key,list) {%>
    <div class="list ">
        <a href="notice/<%= item.NOTI_SEQ%>">
            <div class="t_info">
                <div class="notice_title title"><span><%= item.TITLE %></span></div>
                <div class="notice_author author"><span><%= item.WRITER_NM %></span></div>
                <div class="notice_date date"><span><%= item.ENT_DTTM %></span></div>
            </div>
            
        </a>
    </div>
    <% }) %>

    
</script>


<!-- content -->
<div class="sub_content notice_content">
    <div class="sub_inner">
        <!-- 검색 -->
        <div class="search_form">
            <form action="">
                <input hidden="hidden" />
                <input type="search" id="noticeSearch" name="noticeSearch" placeholder="검색어 입력" onKeyPress="keyEnterSearch(event)">
            </form>
        </div>
        <!-- 검색 -->
        <div class="notice_list" id="noticeLoad">
            
        </div>

        <!-- 더보기 -->
        <div id="js-btn-wrap_notice" class="btn-wrap">
            <a href="javascript:;" class="button more">더보기<i class="icon_more"></i></a>
        </div>
        <!-- //더보기  -->
    </div>
    <!-- [ 교사앱 : 공지사항 쓰기 ] -->
    <!-- <a href="teacher-notice-write.html" class="request_writer write_btn" style="background: #fff;"><i></i></a> -->
</div>
<!-- //content -->


<script>
    (function(){
    // var num = 0;
    // $(window).scroll(function(){
    //     // 조건식에서 == 보다 >= 를 사용 한 이유
    //     // == 스크롤 높이가 뭐빼기 뭐해서 같으면 끝까지 왔다라고 생각하는건데 >= 는 스크롤높이가 뭐빼기 뭐보다 더 커도(더 밑으로 내려가있어도) 끝까지 간거다 
    //     // 브라우저마다 특성이 다르기 때문에 같다보다는 크거나 같음으로 진행
    //     if($(window).scrollTop() >= $(document).height() - $(window).height()){ // 스크롤의 마지막 값을 인식한다.
    //         var $clone = $('.img').eq(num).first().clone()
    //         num++
    //         $('.infiniteScroll').append($clone)
    //         // console.log(num);
    //     }
    // })

    $(document).on('click', '.more' , function(){
        console.log(page);
        LoadContentTemplate();
    })
})(jQuery);
</script>        