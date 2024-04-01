<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content edu_content">
    <div class="sub_inner">
        <div class="t_tab">
            <div class="weekly">
                <p><a href="/eduPlan/weekly">주간 교육계획</a></p>
            </div>
            <div class="monthly">
                <p class="active"><a href="/eduPlan/monthly">월간 교육계획</a></p>
            </div>
        </div>
        <div class="search_form">
            <!-- [ 학부모앱 : 검색/셀렉트박스 ] -->
            <form action="" class="p_form">
                <div class="select_form">
                    <!-- <div class="select_option option02" style="margin-left: 0; width:calc(50% - 2px);">
                        <select name="selectChild" id="selectChild">                             
                            <option value="김파커" selected="">김파커</option>
                            <option value="김둘째">김둘째</option>
                            <option value="김셋쩨">김셋쩨</option>
                            <option value="김넷째">김넷째</option>
                        </select>
                    </div> -->
                    <div class="select_option option01" style="margin-left: 0;  width:calc(50% - 2px);">
                        <select name="selectClass" id="selectClass">                             
                            <?php foreach ( $classList as $class ) : ?>
                            <option value="<?php echo $class->CLASS_CD;?>"><?php echo $class->CLASS_NM;?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <!-- 월간교육계획 -->
        <div class="monthly_load" id="monthlyLoad">
            <div class="edu_container monthly" id="eduListDiv">


            </div>
            <!-- 더보기 -->
            <div id="js-btn-wrap_request" class="btn-wrap" style="display:none">
                <a href="javascript:goPage();" class="button">더보기<i class="icon_more"></i></a>
            </div>
            <!-- //더보기  --> 
            <!-- [ 교사앱 : 월간교육계획 쓰기 ] -->
            <?php if ( $is_teacher ) : ?>
            <a href="<?php echo current_url();?>/write" class="request_writer write_btn" style="background: #fff;"><i></i></a>
            <?php endif; ?>
        </div>

    </div>
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

        var selectClass = $('#selectClass').val();

        var data = { 
            page : page ,
            selectClass : selectClass
        };

        $.ajax({
            url: baseURL + "eduPlan/ajax/monthlyListMore",
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
            
            $('#eduListDiv').append(data.html).fadeIn(1000);
            total_page = data.total;

            if (total_page <= page ) $('#js-btn-wrap_request').hide();
            else $('#js-btn-wrap_request').show();
            
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            console.log('Nothing to display');
        });
    }

    $(document).on('change' , '#selectClass', function(){
        $('#eduListDiv').empty();
        page = 1;
        initLoadMore(1);
    })

    initLoadMore(1);

    
    $(document).on("click","#editBtn" , function(){
        var seq = $(this).data('seq');
        location.href="<?php echo current_url();?>/" + seq + "/edit";
    })
    
    $(document).on('click' , '#DeleteBtn' , function(){
        var seq = $(this).data('seq');
        var forms = {
            seq : seq,
            <?php echo csrf_token();?> : "<?php echo csrf_hash() ?>",
            page : '<?php echo current_url(); ?>'
        };
        Swal.fire({ 
            text : "삭제 하시겠습니까?" , 
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "확인",
            cancelButtonText:"취소"
        }).then((result) => {
            if (result.isConfirmed) {
                loadingShowHide();
                
                fetch("/eduPlan/monthly/deleteProc", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(forms),
                    }).then((response) => response.json())
                    .then((data) => {
                        loadingShowHide();
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'center-center',
                            showConfirmButton: false,
                            timer: 1000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })
                        Toast.fire({
                            icon: 'success',
                            title: '삭제 되었습니다.'
                        }).then(function (result) {
                            if (true) {
                                $("#list-" + seq).remove();
                            }
                        });
                    })
                

            }
        }); 
    });
    

</script>