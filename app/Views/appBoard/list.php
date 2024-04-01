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
    </div>

    
    <!-- 더보기 -->
    <div id="js-btn-wrap_notice" class="btn-wrap">
        <a href="javascript:goPage();" class="button">더보기<i class="icon_more"></i></a>
    </div>
    <!-- //더보기  -->

    <?php if ( $is_teacher ) :?>
    <!-- [ 교사앱 : 공지사항 쓰기 ] -->
        <a href="<?php echo base_url();?>appBoard/write" class="request_writer write_btn" style="background: #fff;"><i></i></a>
    <?php endif; ?>
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

        console.log(page + " 여기 페이지 번호 ");
        var searchText = $('#noticeSearch').val();
        if (searchText != '' && searchText.length < 2) {
            Swal.fire({ 
                text : "2글자 이상 검색이 가능합니다." , 
                icon: "warning",
            });
            return;
        }

        var data = { 
            page : page,
            searchText : searchText,
            auth : '<?php echo $auth->USER_ID;?>',
            auth2 : '<?php echo $auth2;?>',
        };

        var queryString = Object.entries(data).map( ([key,value]) => ( value && key+'='+value )).filter(v=>v).join('&');
        
        $.ajax({
            url: "/appBoard/ajax/ListMore",
            type: "get",
            dataType: "json",
            data : data,
            success : function (data){
                // console.log('success:' , JSON.stringify(data));

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
            },
            error:function(request, status, error){  
                //에러가 났을 경우 실행시킬 코드
                console.log("code: " , request.status)
                console.log("message: " , request.responseText)
                console.log("error: " , error);
            }
        })

        // fetch(baseURL + "appBoard/ajax/ListMore" , { 
        //     method : "POST", 
        //     headers : { "Content-Type" : "application/json" }, 
        //     body: JSON.stringify(data)
        //     }
        // )
        // .then((response) => response.json())
        // .then((data) => {
        //     console.log(data);
        //     // isLoading = false;
        //     // if (data.length == 0) {
        //     //     triggerScrollLoader = false;
        //     //     $('#loader').hide();
        //     //     return;
        //     // }
        //     // $('#loader').hide();
            
        //     // $('#noticeLoad').append(data.html).fadeIn(1000);
        //     // total_page = data.total;
        //     // if (total_page <= page ) $('#js-btn-wrap_notice').hide();
        //     // else $('#js-btn-wrap_notice').show();
        // })
    }

    $(document).on('change' , '#selectClass', function(){
        $('#noticeLoad').empty();
        page = 1;
        initLoadMore(1);
    })

    
    // 검색
    function keyEnterSearch(e){
    // e.preventDefault();
        if (e.keyCode == 13){
            var searchText = $('#noticeSearch').val();
            
            if (searchText != '' && searchText.length < 2) {
                Swal.fire({ 
                    text : "2글자 이상 검색이 가능합니다." , 
                    icon: "warning",
                });

                return;
            }

            $('#noticeLoad').empty();
            page = 1;
            initLoadMore(1);
        }
    }

    $(document).on('click', '#SearchBtn', function(){
        var searchText = $('#noticeSearch').val();
        if (searchText != '' && searchText.length < 2) {
            Swal.fire({ 
                text : "2글자 이상 검색이 가능합니다." , 
                icon: "warning",
            });
            return;
        }
        $('#noticeLoad').empty();
        page = 1;
        initLoadMore(1);
    })

    // window.onload = function(){
        
    // }

    initLoadMore(1);
    
</script>