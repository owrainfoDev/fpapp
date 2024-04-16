<?php $search = $parameter['search'] ?? ''; ?>
<!-- dropzone-->
<script src="/resources/dropzone/dropzone.min.js"></script>
<link rel="stylesheet" href="/resources/dropzone/dropzone.min.css" type="text/css" />
<!-- dropzone-->
<!-- froala_editor-->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@3.1.0/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@3.1.0/js/froala_editor.pkgd.min.js"></script>
<!-- froala_editor-->
<!-- content -->
<div class="sub_content notice_content" id="viewList" style="overflow-y:auto ; height: 90vh">
    <div class="sub_inner">
        <!-- 검색 -->
        <div class="search_form">
            <div class="search">
                <input type="search" id="noticeSearch" name="noticeSearch" value="<?php echo $search ;?>" placeholder="검색어 입력">
                <button type="button" class="search_btn" id="SearchBtn" style="cursor: pointer;">
                    <span></span>
                </button>
            </div>
        </div>
        
        <!-- 검색 -->
        <div class="notice_list" id="noticeLoad">
            <?php if ( count($list->data) > 0 ) : ?>
                <?php foreach ( $list->data as $data ) :?>
                <div class="list post-list" data-listno='<?php echo $data->NOTI_SEQ;?>'>
                    <a href="/notice/<?php echo $data->NOTI_SEQ; ?>" class='detailhref'>
                        <div class="t_info">
                            <div class="notice_title title"><span><?php echo $data->TITLE; ?></span></div>
                            <div class="notice_author author"><span><?php echo $data->WRITER_NM; ?></span></div>
                            <div class="notice_date date"><span><?php echo $data->ENT_DTTM; ?></span></div>
                        </div>
                
                    </a>
                </div>
                <?php endforeach ;?>
            <?php else : ?>

                <div style="font-size:14px; text-align:center">
                    검색 결과가 없습니다.
                </div>

            <?php endif; ?>
        </div>

        <?php if ( $list->total_page > $list->page ) : ?>
        <!-- 더보기 -->
        <div id="js-btn-wrap_notice" class="btn-wrap" style="cursor:pointer">
            <a class="button more">더보기<i class="icon_more"></i></a>
        </div>
        <!-- //더보기  -->
        <?php endif; ?>
        <!-- 글쓰기 버튼 -->
        <?php if ( $is_teacher ) :?> <a href="/notice/write" class="request_writer write_btn" style="background: #fff;"><i></i></a><?php endif; ?>
        <!-- //글쓰기 버튼 -->
    </div>
    <!-- [ 교사앱 : 공지사항 쓰기 ] -->
</div>
<!-- //content -->

<!-- detail view -->
<div class="sub_content notice_content notice_detail mode_view" id="viewDetail" style="display:none"></div>
<!-- // detail view -->

<!-- write view -->
<div class="sub_content t_write_cont t_content notice_write mode_view" id="viewForm" style="display:none"></div>
<!-- write view -->

<input type="hidden" id="pages" value="<?php echo $list->page;?>">

<script type="text/javascript">
$(document).ready(function(){
    $('#viewDetail').empty();
    
    $(document).on('click' , '#SearchBtn', function(){
        let href = '/notice';
        let search = $('#noticeSearch').val();
        if ( search.length != 0 && search.length < 2){
            alert('검색은 2글자 이상입력하여주세요');
            $('#noticeSearch').focus();
            return ;
        }
        let params = {
            "search" : search
        }
        post(href, params  , 'get');

    });

    $('#noticeSearch').keypress(function(e){
        if ( e.keyCode && e.keyCode == 13) {
            $('#SearchBtn').trigger('click');
            return false;
        }
    })

    $(document).on('click', '#js-btn-wrap_notice', function(){
        let search = $('#noticeSearch').val();
        let page = $('#pages').val();
        page = parseInt(page);
        let param = { search : search, page : page+1 };
        $.ajax({
            type : 'post',
            url : "/notice/proc/moreList",
            async : true,
            dataType : 'json',
            data : param,
            beforeSend : function(){
                loadingShowHide();
            },
            success : function(response)  {
                loadingShowHide();
                $("#noticeLoad").append(response.html);
                $('#pages').val(response.page);
                if ( response.total_page < response.page) $('#js-btn-wrap_notice').hide();
            },
            error : function(request, status, error){
                console.log(error);
            }
        })
    })

    // 상세 보기 
    $(document).on('click', '.detailhref', function(e){
        e.preventDefault();
        let href = $(this).prop('href');
        loadingShowHide();
        $.get( href , function(data , status){
            $('#viewList').hide();
            $('#viewDetail').html(data).show();
            loadingShowHide();
        })

    })

    // 상단 뒤로 가기
    $(document).on('click', '.top_back' , function(e){
        e.preventDefault();
        if ( $('#viewList').css('display') == "block" ) {
            console.log( '--- 홈화면 ---' );
        }
        $('.mode_view').empty().hide();
        $('#viewList').show();
    })

    // writer
    $(document).on('click', '.request_writer', function(e){
        e.preventDefault();
        let href = $(this).prop('href');
        loadingShowHide();
        $.get( href , function(data , status){
            $('#viewList').hide();
            $('#viewForm').html(data).show();
            loadingShowHide();
        })

    })

});

</script>   