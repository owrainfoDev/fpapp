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
        <!-- //검색 -->
        <div class="notice_list" id="noticeLoad">
            
            
        </div>

        <!-- 더보기 -->
        <div id="js-btn-wrap_notice" class="btn-wrap">
            <a href="javascript:;" class="button">더보기<i class="icon_more"></i></a>
        </div>
        <!-- //더보기  -->
    </div>
    <!-- [ 교사앱 : 공지사항 쓰기 ] -->
    <!-- <a href="teacher-notice-write.html" class="request_writer write_btn" style="background: #fff;"><i></i></a> -->
</div>

<script>
    var baseURL = "<?php echo base_url(); ?>";
    var page = 1;
    var triggerScrollLoader = true;
    var isLoading = false;
    var total_page = 0;

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 555) {
            if (isLoading == false) {
                isLoading = true;
                if (total_page > page ) {
                    page++;
                    if (triggerScrollLoader) {
                        initLoadMore(page);
                    }
                }
            }
        }
    });

    function goPage(){
        page++;
        initLoadMore(page);
    }

    function initLoadMore(page) {
        var data = { 
            page : page
        };

        $.ajax({
            url: baseURL + "AppBoard/ajax/ListMore",
            type: "GET",
            dataType: "json",
            data : data,
        }).done(function (data) {
            isLoading = false;
            if (data.length == 0) {
                triggerScrollLoader = false;
                $('#loader').hide();
                return;
            }
            $('#loader').hide();
            $('#noticeLoad').append(data.html).fadeIn(1000);
            total_page = data.total;
            if (total_page <= page ) $('#js-btn-wrap_notice').hide();
            else $('#js-btn-wrap_notice').show();
            
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            console.log('Nothing to display');
        });
    }

    $(document).on('change' , '#selectClass', function(){
        $('#noticeLoad').empty();
        page = 1;
        initLoadMore(1);
    })

    initLoadMore(1);

</script>