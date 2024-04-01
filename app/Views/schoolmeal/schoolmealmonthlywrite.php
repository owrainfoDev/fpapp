<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content meal_content t_content">
    <div class="sub_inner">
        <div class="t_tab">
            <div class="today">
                <p><a href="/schoolmeal">오늘의 급식</a></p>
            </div>
            <div class="monthly">
                <p class="active"><a href="/schoolmealmonthly">월간 식단표</a></p>
            </div>
        </div>
        <!-- 오늘의 급식 식단 등록 -->
        <div class="today_meal_write">
            <form action="" method="post"  id="fileupload" enctype="multipart/form-data">
                <input type="hidden" name="ACA_NM" value="<?php echo $auth->ACA_NM;?>">
                <div class="meal_cont">
                    <div class="date">
                        <p class="bg_comm">날짜</p>
                        <input type="month" name="MEAL_YM" id="MEAL_YM" data-placeholder="월 선택" class="mealMonth" value="<?php echo date("Y-m")?>"  class="required" data-msg-required="날짜를 선택하여 주십시요">
                    </div>
                </div>
                <div class="form_file">
                    <span class="title">파일첨부</span>
                    
                    <div style="width:100%" id="dropzone" class="dropzone"></div>
                    <!-- [ 카메라 앱으로 이동? ] -->
                    <div class="camera">
                        <!-- <span>사진2 동영상1/1</span> -->
                        <i class="icon_image"></i>
                    </div>
                   
                    <!-- <p class="comm">
                        동영상 1개, 사진 100개까지 첨부할 수 있습니다.
                        <span>(동영상 30MB이하, 총 용량 500MB 이하)</span>
                    </p> -->
                </div>
                <div class="btn_box">
                    <button type="submit" id="confirmSubmitBtn">등록</button>
                </div>
                
            </form>
        </div>
    </div>
</div>

<style>
    .t_content .form_file{
        flex-wrap:nowrap;
    }
</style>

<script>
    // dropzone Setting
    var _maxfiles = 1;
    var _uploadMultiple = false;
    var _parallelUploads = 1;
</script>
<?php echo $this->include('./layout/common/dropzoneCustom');?>
<!-- dropzone -->
<style>
    .t_content .form_file .camera i { margin-bottom:5px;}
    .dropzone .dz-preview.dz-image-preview , .dropzone { background-color: #F1F1F5}
    .select2-container--default .select2-selection--multiple  { background-color: #F1F1F5 ; border : 0}


</style>
<script>

var validobj = $("#fileupload").validate({
    onkeyup: false,
    errorClass: "myErrorClass",
    errorPlacement: function(error, element) {
        var elem = $(element);
        error.insertAfter(element);
    },
    highlight: function(element, errorClass, validClass) {
        var elem = $(element);
        if (elem.hasClass("select2-offscreen")) {
            $("#s2id_" + elem.attr("id") + " ul").addClass(errorClass);
        } else {
            elem.addClass(errorClass);
        }
    },
    unhighlight: function(element, errorClass, validClass) {
        var elem = $(element);
        if (elem.hasClass("select2-offscreen")) {
            $("#s2id_" + elem.attr("id") + " ul").removeClass(errorClass);
        } else {
            elem.removeClass(errorClass);
        }
    },
    submitHandler: function(form) {
        Swal.fire({ 
            text : "월간 식단표를 등록하시겠습니까? " , 
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "확인",
            cancelButtonText:"취소"
        }).then((result) => {
            if (result.isConfirmed) {
                if (myDropzone.files != "") {
                    // console.log(myDropzone.files);
                    myDropzone.processQueue();
                } else {
                    Swal.fire({ text : "파일을 첨부해 주세요" , icon: "warning"});
                    return false;

                    // goSubmit();
                }
            }else{
                
                return false;
            }
        });
    }
});

function goSubmit(){
    var forms = $('form#fileupload').serializeObject();
    forms.ACA_ID = content_data.ACA_ID;
    forms.USER_ID = content_data.USER_ID;
    forms.files = content_data.files
    forms.is_teacher = content_data.is_teacher;

    fetch("/schoolmealmonthly/proc/writeproc", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(forms),
    })
    .then((response) => response.json())
    .then((data) => {
        if (data.status == 'fail'){
            swal.fire({text:data.msg , icon:"error"})
            myDropzone.emit("resetFiles");
        } else {
            const Toast = Swal.mixin({
                toast: true,
                position: 'center-center',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
            Toast.fire({
                icon: 'success',
                title: '등록 되었습니다.'
            }).then(function (result) {
                if (true) {
                    location.href=data.redirect_to;
                }
            });
        }
    });
}
</script>