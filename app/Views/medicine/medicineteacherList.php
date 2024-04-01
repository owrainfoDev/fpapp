<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content request_content t_content">
    <div class="top_util">
        <div class="select_form">
            <div class="select_option option01">
                <select name="selectClass" id="selectClass">
                    <option value="">전체반</option>
                    <?php foreach ($classList as $class): ?>
                    <option value="<?php echo $class->CLASS_CD?>" <?php echo ( $class->CLASS_CD == $search['selectClass'] ? "selected" : "" ) ?>><?php echo $class->CLASS_NM?></option>
                    <?php endforeach ;?>
                </select>
            </div>
            <div class="select_option option02">
                <select name="selectChild" id="selectChild">
                </select>
            </div>
        </div>
        <div class="count_form">
            <!-- [ 투약의뢰서 생성 된 갯수 만큼 카운트 ] -->
            <p>투약의뢰서 <span class="count"><?php echo $data['total_row'];?></span></p>
            <div class="unconfirm">
                <span>미확인만 보기</span>
                <input type="checkbox" name="unConfirm" id="unConfirm">
                <label for="unConfirm"></label>
            </div>
        </div>
    </div>
    <div class="sub_inner">
        <div class="request_list" id="requestLoad">
            <?php /*
            <?php foreach ($data['data'] as $list) : ?>
            <div class="list js-load block">
                <a href="<?php echo base_url('/medicine/' . $list->MEDI_REQ_NO );?>">
                    <div class="request_info">
                        <!-- [ 투약의뢰서 : 아이 이미지 불러오기 ] -->
                        <div class="info_img"><img src="../resources/images/children-img1.png" alt="원생 이미지"></div>
                        <div class="request_name name"><span><?php echo $list->STD_NM?></span></div>
                        <div class="request_date date"><span><?php echo $list->REQ_DT?></span></div>
                        <!--  [투약의뢰서 미확인 : .send class 추가 ] -->
                        <?php if ( $list->MEDI_REQ_STATUS == "01") : ?>
                            <div class="request_status status send"><span>미확인</span></div>
                        <?php elseif ($list->MEDI_REQ_STATUS == "02") : ?>
                            <div class="request_status status send"><span>확인</span></div>
                        <?php elseif ($list->MEDI_REQ_STATUS == "03") : ?>
                            <div class="request_status status done"><span>투약조치</span></div>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
            <?php endforeach ;?>
            */?>

            
        </div>
        <?php if ( $data['total_page'] > 1 ) : ?>
        <!-- 더보기 -->
        <div id="js-btn-wrap_request" class="btn-wrap">
            <a href="javascript:goPage();" class="button">더보기<i class="icon_more"></i></a>
        </div>
        <!-- //더보기  -->
        <?php endif;?>
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
        var selectClass = $('#selectClass').val();
        var selectChild = $('#selectChild').val();
        var unConfirm = $('input#unConfirm').is(':checked') ? '01' : '';
        var data = { 
            page : page ,
            selectClass : selectClass,
            selectChild : selectChild,
            unConfirm : unConfirm
        };

        $.ajax({
            url: "medicine/ajax/onScrollLoadMore",
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
            
            $('#requestLoad').append(data.html).fadeIn(1000);
            $('.count').html(data.total);

            // if ( data.total ) 

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            console.log('Nothing to display');
        });
    }

    $(document).on('change' , '#selectClass', function(){
        var forms = {
            class_cd : $('#selectClass').val() ,
        }
        fetch("/api/ajax/getstudentsFromClass", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(forms) })
        .then((response) => response.json())
        .then((data) => {
            console.log(data);
            $('#selectChild').empty();
            $('#requestLoad').empty();
            $('#selectChild').append($("<option value=''>전체 원아</option>"));
            $.each(data, function (index , item){
                $('#selectChild').append($("<option value='" + item.STD_ID + "' >"+item.STD_NM+"</option>"));
            })
            $('#requestLoad').empty();
            initLoadMore(1);
        });
    })

    $(document).on('change' , '#selectChild' , function(){
        $('#requestLoad').empty();
        initLoadMore(1);
    })

    $(document).on('click', '#unConfirm', function(){
        if ( $(this).is(':checked') == true ) {
            $('#requestLoad').empty();
            initLoadMore(1);
        } else {
            $('#requestLoad').empty();
            initLoadMore(1);
        }
    })

    initLoadMore(1);
    

</script>