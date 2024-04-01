<div class="sub_content meal_content">
    <div class="sub_inner">
        <div class="t_tab">
            <div class="today">
                <p><a href="/schoolmeal">오늘의 급식</a></p>
            </div>
            <div class="monthly">
                <p class="active"><a href="/schoolmealmonthly">월간 식단표</a></p>
            </div>
        </div>
        <!-- 월간식단표 -->
        <div class="month_load" id="monthLoad">
            <div class="meal_container monthly" id="containerLoad">
                <?php // echo $html;?>
            </div>
            <!-- 더보기 -->
            <div id="js-btn-wrap_monthly_meal" class="btn-wrap">
                <a class="button" id="schoolmealmonthlymore">더보기<i class="icon_more"></i></a>
            </div>
            <!-- //더보기  -->
            <?php if ($is_teacher == true): ?>
            <!-- [ 교사앱 : 쓰기 ] -->
            <a href="schoolmealmonthly/write" class="request_writer write_btn" style="background: #fff;"><i></i></a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var page = 1;
        var total_page = <?php echo $cnt?>;
        $(document).on('click', '#schoolmealmonthlymore', function(){
            if (total_page >= page){
                $.get('/schoolmealmonthly/ajaxmore' , {more:page}, function( response ){
                    
                })
                .done( function (response){
                    $('#containerLoad').append(response);
                    page++;
                    if ( total_page < page) $('#schoolmealmonthlymore').hide();
                })
                .fail( function (xhr, status, errorThrown){
                    console.log(xhr);
                    console.log(status);
                    console.log(errorThrown);
                })
            }
        })
        $('#schoolmealmonthlymore').trigger('click');
        // 수정
        $(document).on('click' , '#mealEditBtn' , function(){
            var enc = $(this).data('enc');
            location.href="/schoolmealmonthly/edit/" + enc;
        })

        // 삭제
        $(document).on('click', '.mealDeleteBtn', function(){
            var forms = {
                enc : $(this).data('enc')
            }

            Swal.fire({ 
                text : "월간식단표를 삭제하시겠습니까? " , 
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "확인",
                cancelButtonText:"취소"
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    fetch("/schoolmealmonthly/proc/delete", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(forms),
                    })
                    .then((response) => response.json())
                    .then((data) => {
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
                                if ( data.redirect_to ) location.href=data.redirect_to;
                            }
                        });
                    })

                }else{
                    return false;
                }
            });
        })
        // 2024.01.02
        $('body').removeClass('bg_color');
    })
</script>