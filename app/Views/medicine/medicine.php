<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content request_content">
    <!-- [ 학부모앱 : 검색 ] -->
    <!-- 위즈_v0.4ppt 내용에서 빠져있음. 필요할 경우 아래부터 주석 해제해서 사용 -->
    <!-- <div class="search_form">
        <form action="">
            <div class="select_form t_select">
                <div class="select_option option01">
                    <select name="selectClass" id="selectClass" style="background-color: #F1F1F5;">
                        [ 학부모앱 : 기본값- 등록된 아이 반 선택 ]
                        <option value="7B-IRIS" >7B-IRIS</option>
                        <option value="7B-Brown" selected>7B-Brown</option>
                        <option value="7B-IRIS">7B-IRIS</option>
                        <option value="7B-Brown">7B-Brown</option>
                    </select>
                </div>
                <div class="select_option option02">
                    <select name="selectChild" id="selectChild" style="background-color: #F1F1F5;">
                        [ 학부모앱 : 기본값- 등록된 아이 이름 선택 ]
                        <option value="원아명1">원아명1</option>
                        <option value="원아명2">원아명2</option>
                        <option value="원아명3" selected>원아명3</option>
                        <option value="원아명4">원아명4</option>
                        <option value="원아명5">원아명5</option>
                        <option value="원아명6">원아명6</option>
                        <option value="원아명7">원아명7</option>
                        <option value="원아명8">원아명8</option>
                    </select>
                </div>
            </div>
        </form>
    </div> -->
    <div class="top_util">
        <!-- [ 투약의뢰서 생성 된 갯수 만큼 카운트 ] -->
        <p>투약의뢰서 <span class="count"><?php echo $data['total_row']?></span></p>
    </div>
    <div class="sub_inner">
        <div class="request_list" id="requestLoad">
            <?php foreach ($data['data'] as $d):?>
            <div class="list js-load block">
                <a href="/medicine/<?php echo $d->MEDI_REQ_NO;?>">
                    <div class="request_info">
                        <div class="request_date date"><span><?php echo $d->REQ_DT?></span></div>
                        <div class="request_txt txt"><span>투약의뢰서</span></div>
                        <!--  [투약의뢰서 전송 완료(미확인) : .send class 추가 ] -->
                        <?php if ( $d->MEDI_REQ_STATUS == '01') : ?>
                            <div class="request_status status send"><span>투약의뢰</span></div>
                        <?php elseif ($d->MEDI_REQ_STATUS == "02") : ?>
                            <div class="request_status status check"><span>의뢰확인</span></div>
                        <?php elseif ($d->MEDI_REQ_STATUS == "03") : ?>
                            <div class="request_status status done"><span>투약조치</span></div>
                        <?php endif;?>
                    </div>
                </a>
            </div>
            <?php endforeach ;?>
        </div>
        <!-- 더보기 -->
        <?php if ( $data['total_page'] > 1 ) : ?>
        <div id="js-btn-wrap_request" class="btn-wrap">
            <a href="javascript:goPage()" class="button">더보기<i class="icon_more"></i></a>
        </div>
        <?php endif; ?>
        <!-- //더보기  -->

        <a href="/medicine/write" class="request_writer write_btn"><i></i></a>

    </div>

</div>

<script>
    var baseURL = "<?php echo base_url(); ?>";
    var page = 1;
    var triggerScrollLoader = true;
    var isLoading = false;
    var total_page = "<?php echo $data['total_page'];?>";

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 555) {
            if (isLoading == false) {
                isLoading = true;
                page++;
                console.log(page);
                if (triggerScrollLoader) {
                    initLoadMore(page);
                }
            }
        }
    });

    function goPage(){
        page++;
        initLoadMore(page);
    }

    function initLoadMore(page) {
        
        if (total_page <= page ) $('#js-btn-wrap_request').hide();

        var data = { page : page }

        $.ajax({
            url: "medicine/ajax/onScrollLoadMore",
            type: "GET",
            dataType: "html",
            data : data,
        }).done(function (data) {
            isLoading = false;
            if (data.length == 0) {
                triggerScrollLoader = false;
                $('#loader').hide();
                return;
            }
            $('#loader').hide();
            $('#requestLoad').append(data).fadeIn(1000);
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            console.log('Nothing to display');
        });
    }

</script>