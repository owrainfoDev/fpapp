
<script type="text/template" id="listTemplate">
    <% if ( total > 0 ){ %>
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
    <% } else { %>
    <div style="font-size:14px; text-align:center">
        검색 결과가 없습니다.
    </div>
    <% } %>

</script>

<script type="text/template" id="writeButton">
    <% if ( content_data.is_teacher == true ) {  %>
    <a href="/notice/write" class="request_writer write_btn" style="background: #fff;"><i></i></a>    
    <% } %>
</script>

<!-- content -->
<div class="sub_content notice_content">
    <div class="sub_inner">
        <!-- 검색 -->
        <div class="search_form">
            <div class="search">
                <input type="search" id="noticeSearch" name="noticeSearch" placeholder="검색어 입력" onKeyPress="keyEnterSearch(event)">
                <button type="button" class="search_btn" id="SearchBtn">
                    <span></span>
                </button>
            </div>
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
    
</div>
<!-- //content -->


<script>
    (function(){
    
    $(document).on('click', '.more' , function(){
        LoadContentTemplate();
    })
})(jQuery);
</script>        

<script type="text/javascript">
var page = 1;
var total_page = 0;
function LoadContentTemplate(){
    var search = $('#noticeSearch').val();
    search = atrim(search);
    console.log(search);
    content_data.search = search;
    content_data.page = page;

    var perPage = 10;
    var pageCnt = 5;

    content_data.perPage = perPage;
    content_data.pageCnt = pageCnt;
    content_data.action = "getLists";

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
                    list: data.data,
                    total : data.total_row
            } );
        if ( page == 1 ) {
            $("#noticeLoad").html( result );

        } else {
            $("#noticeLoad").append( result );
        }
        total_page = data.total_page;
        if (total_page <= page ) {
            $('#js-btn-wrap_notice').hide();
        }
        page = page+1;
        
        return data
    })
    .then((data) => {
        page_load();
    });
}

function page_load(){
    var template = _.template($('#writeButton').html());
    var result = template( {
            content_data
            } );
    $(".sub_inner").append( result );
}



function keyEnterSearch(e){
    // e.preventDefault();
    if (e.keyCode == 13){
        page = 1;
        LoadContentTemplate();
    }
}

</script>   