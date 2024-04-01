<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content home_content request_content t_content">
    <div class="top_util">
        <div class="select_form">
            <div class="select_option option01">
                <select name="selectClass" id="selectClass">
                    <option value="">전체반</option>
                    <?php foreach ( $classList as $class ) : ?>
                    <option value="<?php echo $class->CLASS_CD;?>"><?php echo $class->CLASS_NM;?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="select_option option02">
                <select name="selectChild" id="selectChild"></select>
            </div>
        </div>
        <div class="count_form">
            <!-- [ 귀가동의서 생성 된 갯수 만큼 카운트 ] -->
            <p>귀가동의서 <span class="count total_row"></span></p>
            <div class="unconfirm">
                <span>미확인만 보기</span>
                <input type="checkbox" name="unConfirm" id="unConfirm" value="Y">
                <label for="unConfirm"></label>
            </div>
        </div>
    </div>
    <div class="sub_inner">
        <div class="request_list" id="requestLoad">

        </div>

        <!-- 더보기 -->
        <div id="js-btn-wrap_request" class="btn-wrap">
            <a href="javascript:goPage();" class="button">더보기<i class="icon_more"></i></a>
        </div>
        <!-- //더보기  -->
    </div>

</div>

<script>
    var baseURL = "<?php echo base_url(); ?>";
    var page = 1;
    var triggerScrollLoader = true;
    var isLoading = false;
    var total_page = 0;
    var currentPage = "<?php echo current_url();?>"

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
        var selectClass = $('#selectClass').val();
        var selectChild = $('#selectChild').val();
        var unConfirm = $('input#unConfirm').is(':checked') ? '01' : '';
        var data = { 
            page : page ,
            selectClass : selectClass,
            selectChild : selectChild,
            unConfirm : unConfirm
        };
        // console.log(data);
        fetch( currentPage + "/ajax/ListMore" , { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(data) })
        .then((response) => response.json())
        .then((data) => {
            isLoading = false;
            if (data.length == 0) {
                triggerScrollLoader = false;
                $('#loader').hide();
                return;
            }
            $('#loader').hide();
            $('.js-load').show();
            $('#requestLoad').append(data.html).fadeIn(1000);
            total_page = data.total;
            // 총갯수
            $('.total_row').html(data.total_row);
            
            if (total_page <= page ) $('#js-btn-wrap_request').hide();
            else $('#js-btn-wrap_request').show();
        });
    }

    

    $(document).on('change' , '#selectClass' , function(){
        var forms = {
            class_cd : $('#selectClass').val() ,
        }
        fetch("/api/ajax/getstudentsFromClass", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(forms) })
        .then((response) => response.json())
        .then((data) => {
            $('#selectChild').empty();
            $('#selectChild').append($("<option value=''>전체 원아</option>"));
            $.each(data, function (index , item){
                $('#selectChild').append($("<option value='" + item.STD_ID + "'>"+item.STD_NM+"</option>"));
            })
        });
    })

    $(document).on('change' , '#selectChild' , function(){
        $('#requestLoad').empty();
        initLoadMore(1);
    })

    $(document).on('click' , '#unConfirm' , function(){
        $('#requestLoad').empty();
        initLoadMore(1);
    })

    initLoadMore(1);

</script>