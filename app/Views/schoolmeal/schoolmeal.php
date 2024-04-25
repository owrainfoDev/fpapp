
<div class="sub_content meal_content">
    <div class="sub_inner">
        <div class="t_tab">
            <div class="today">
                <p class="active"><a href="/schoolmeal">오늘의 급식</a></p>
            </div>
            <div class="monthly">
                <p><a href="/schoolmealmonthly">월간 식단표</a></p>
            </div>
        </div>
        <!-- 오늘의 급식 식단 -->
        <div class="today_load" id="todayLoad">
            <!-- 등록된 급식 없을 때 none class 제거 -->
            <p class="noMeal none">등록된 급식이 없습니다.</p>
            <!-- 등록된 급식 있을 때 active class 추가 -->
            <div class="meal_container today active">
                <?php echo $html;?>
            </div>
        </div>
        <!-- [ 교사앱 : 오늘의급식 쓰기 ] -->

        <!-- 더보기 -->
        <div id="js-btn-wrap_meal" class="btn-wrap">
            <a href="javascript:;" class="button" id="moreSchoolMeal">더보기<i class="icon_more"></i></a>
        </div>
        <!-- //더보기  -->
        <?php if ( $is_teacher == true) : ?>
        <a href="/schoolmeal/write" class="request_writer write_btn" style="background: #fff;"><i></i></a>
        <?php endif; ?>
        
    </div>

</div>

<script>
    $(document).ready(function(){
        var page = 2;
        $(document).on('click','#moreSchoolMeal', function(){
            $.get('/schoolmeal/ajaxmoreschoolmeal' , {more:page}, function( response ){
                
            })
            .done( function (response){
                $('#todayLoad').append(response);
                page++;
            })
            .fail( function (xhr, status, errorThrown){
                console.log(xhr);
                console.log(status);
                console.log(errorThrown);
            })
        })
        // $('#moreSchoolMeal').trigger('click');

        $(document).on('click','#mealEditBtn', function(){
            var enc = $(this).data('enc');
            location.href="/schoolmeal/edit/" + enc;
        })

        $(document).on('click', '.mealDeleteBtn', function(){
            var forms = {
                enc : $(this).data('enc')
            }

            Swal.fire({ 
                text : "식단을 삭제하시겠습니까? " , 
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "확인",
                cancelButtonText:"취소"
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    fetch("/schoolmeal/proc/todaywriteDelete", {
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
                            timer: 500,
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
        // 2024.01.02 배경색 없애기
        $('body').removeClass('bg_color');
    })
</script>

