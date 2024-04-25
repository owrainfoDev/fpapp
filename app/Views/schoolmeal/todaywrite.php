<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content meal_content t_content">
    <div class="sub_inner">
        <div class="t_tab">
            <div class="today">
                <p class="active"><a href="/schoolmeal">오늘의 급식</a></p>
            </div>
            <div class="monthly">
                <p><a href="/schoolmealmonthly">월간 식단표</a></p>
            </div>
        </div>
        <!-- 오늘의 급식 식단 등록 -->
        <div class="today_meal_write">
            <form action="" method="post" id="fileupload" enctype="multipart/form-data">
                <div class="meal_cont">
                    <div class="date">
                        <p class="bg_comm">날짜</p>
                        <input type="date" name="MEAL_DT" id="MEAL_DT" class="required" data-msg-required="날짜를 선택하여 주십시요">
                    </div>
                    <div class="meal_title">
                        <p class="bg_comm">제공급식</p>
                        <input type="text" id="MEAL_NM" name="MEAL_NM" class="required" data-msg-required="제공급식 제목을 입력하여 주십시요">
                    </div>
                    <div class="meal_des">
                        <p class="bg_comm">식단</p>
                        <select style="width:100%" multiple="multiple" id="MEAL_DESC" name="MEAL_DESC" class="required" data-msg-required="식단을 입력하여 주십시요">
                        
                        </select>
                        
                    </div>
                    <div class="meal_des">
                        <p class="bg_comm">간식</p>
                        <select style="width:100%" multiple="multiple" name="SNACK_DESC" id="SNACK_DESC"></select>
                    </div>
                </div>
                <div class="form_file">
                <span class="title">파일첨부</span>
                <!-- [ 카메라 앱으로 이동? ] -->
                <div class="camera">
                    <i class="icon_image"></i>
                </div>
                <div style="width:100%" id="dropzone" class="dropzone"> 파일 첨부 </div>
                
                </div>
                <div class="btn_box">
                    <button type="button" id="confirmSubmitBtn">등록</button>
                </div>
            </form>
        </div>
    </div>
    
</div>

<script>
    // dropzone Setting
    var _maxfiles = 30;
    // var _uploadMultiple = false;
    // var _parallelUploads = 1;
</script>
<!-- dropzone -->
<?php echo $this->include('./layout/common/dropzoneCustom');?>
<!-- dropzone -->
<style>
    .dropzone .dz-preview.dz-image-preview , .dropzone { background-color: #F1F1F5}
    .select2-container--default .select2-selection--multiple  { background-color: #F1F1F5 ; border : 0}
</style>

<script type="text/javascript">

$(document).on('change' , '#MEAL_DT' , function(){
    var date1 = new Date( $(this).val() );
    var date2 = new Date();
    if ( date1 > date2 ) {
        Swal.fire({
            text: "금일과 이전 식단만 등록하실 수 있습니다.",
            icon: "error"
        }).then(()=>{
            $(this).val('')
        });
    }
})


$('#currentDate').val(new Date().toISOString().substring(0, 10));

$("#MEAL_DESC").select2({
    tokenSeparators: ["/"],
    tags: true,
    insertTag: function (data, tag) {
        // Insert the tag at the end of the results
        data.push(tag);
    }
});

$("#SNACK_DESC").select2({
    language: "ko",
    tags: true,
    insertTag: function (data, tag) {
        // Insert the tag at the end of the results
        data.push(tag);
    }
});

function goSubmit(){
    var forms = $('form#fileupload').serializeObject();
    forms.ACA_ID = '<?php echo $aca_id;?>';
    forms.USER_ID = '<?php echo $user_id;?>';
    forms.is_teacher = '<?php echo $is_teacher;?>'
    forms.files = dropzonefiles;

    fetch("/schoolmeal/proc/todaywriteProc", {
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
        } else {
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
                title: '등록 되었습니다.'
            }).then(function (result) {
                if (true) {
                    location.href=data.redirect_to;
                }
            });
        }
    });
}

$(document).on("change", ".select2-offscreen", function() {
    if (!$.isEmptyObject(validobj.submitted)) {
        
    validobj.form();
    }
});

$(document).on("select2-opening", function(arg) {
    var elem = $(arg.target);
    if ($("#s2id_" + elem.attr("id") + " ul").hasClass("myErrorClass")) {
    //jquery checks if the class exists before adding.
        $(".select2-drop ul").addClass("myErrorClass");
    } else {
        $(".select2-drop ul").removeClass("myErrorClass");
    }
});
    
$(document).ready(function(){
    
    $(document).on('click' , '#confirmSubmitBtn' , function(){
        var alertTitle = "오늘의 급식";
        var alerticon = "error";

        if ( $('#MEAL_DT').val() == ''){
            Swal.fire({
                title: alertTitle,
                text: "급식일자를 입력하여 주십시요",
                icon: alerticon,
                didClose: () => {
                    $("#MEAL_DT").focus();
                }
            });
            return false;
        }

        if ( $('#MEAL_NM').val() == ''){
            Swal.fire({
                title: alertTitle,
                text: "제공급식 제목을 입력하여 주십시요",
                icon: alerticon,
                didClose: () => {
                    $("#MEAL_NM").focus();
                }
            });
            return false;
        }

        if ( $('#MEAL_DESC').val() == null){
            Swal.fire({
                title: alertTitle,
                text: "식단을 입력하여 주십시요",
                icon: alerticon,
                didClose: () => {
                    $("#MEAL_DESC").focus();
                }
            });
            return false;
        }

        if ( $('#SNACK_DESC').val() == null){
            Swal.fire({
                title: alertTitle,
                text: "간식을 입력하여 주십시요",
                icon: alerticon,
                didClose: () => {
                    $("#SNACK_DESC").focus();
                }
            });
            return false;
        }
        
        if ( myDropzone.files.length < 1 ) {
            Swal.fire({
                title: alertTitle,
                text: "오늘의 급식 사진을 등록하여 주십시요",
                icon: alerticon
            });

            return false;
        }

        Swal.fire({ 
            text : "오늘의 급식 식단을 등록하시겠습니까? " , 
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "확인",
            cancelButtonText:"취소"
        }).then((result) => {
            loadingShowHide();
            if (result.isConfirmed) {
                if (myDropzone.files != "") {
                    // console.log(myDropzone.files);
                    myDropzone.processQueue();
                } else {
                    Swal.fire({
                        title: alertTitle,
                        text: "오늘의 급식 사진을 등록하여 주십시요",
                        icon: alerticon
                    });
                    return false;
                }
            }else{
                return false;
            }
        });
    } )
})



    
</script>

<!-- select2Custom -->
<?php echo $this->include('./layout/common/select2Custom');?>
<!-- select2Custom -->