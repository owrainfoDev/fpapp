<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content home_content request_content">
    <div class="top_util">
        <!-- [ 귀가동의서 생성 된 갯수 만큼 카운트 ] -->
        <p>귀가동의서 <span class="count">0</span></p>
    </div>
    <div class="sub_inner">
        <div class="request_list" id="homeReturnLoad">
            
        </div>
        <!-- 더보기 -->
        <div id="js-btn-wrap_home_list" class="btn-wrap">
            <a href="javascript:goPage();" class="button">더보기<i class="icon_more"></i></a>
        </div>
        
        <!-- //더보기  -->
        <a href="<?php echo current_url();?>/write" class="request_writer write_btn"><i></i></a>
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
        var data = { 
            page : page
        };

        $.ajax({
            url: baseURL + "homeCommingConsent/ajax/ListMore",
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
            
            $('#homeReturnLoad').append(data.html).fadeIn(1000);
            total_page = data.total;

            $('.count').html( data.total_row );

            if (total_page <= page ) $('#js-btn-wrap_home_list').hide();
            else $('#js-btn-wrap_home_list').show();
            
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            console.log('Nothing to display');
        });
    }

    $(document).on('change' , '#selectClass', function(){
        $('#homeReturnLoad').empty();
        page = 1;
        initLoadMore(1);
    })

    initLoadMore(1);

    $(document).on("click","#editBtn" , function(){
        var seq = $(this).data('seq');
        location.href="<?php echo base_url("/eduPlan/weekly")?>/" + seq + "/edit";
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
                
                fetch("/eduPlan/weekly/deleteProc", {
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
                                $("#weekly-" + seq).remove();
                            }
                        });
                    })
                

            }
        }); 
    });

</script>