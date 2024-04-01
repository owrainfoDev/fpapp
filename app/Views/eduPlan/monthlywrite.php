<?php 
    if (! empty($content)) {    // 수정 일때
        $mode = "edit";
        $confirmText = "월간 교육계획안을 수정하시겠습니까? ";
        $resultText = "수정 되었습니다.";
    } else {
        $mode = "write";
        $confirmText = "월간 교육계획안을 등록하시겠습니까? ";
        $resultText = "등록 되었습니다.";
        
    }
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<div class="sub_content t_write_cont t_content">
    <div class="sub_inner">
        <form action="" method="post"  id="fileupload" enctype="multipart/form-data">
            <?php if ( $mode == "edit" ) :?>
            <input type="hidden" name="EDU_PLAN_NO" id="EDU_PLAN_NO" value="<?php echo $content->EDU_PLAN_NO?>">
            <?php endif; ?>
            <div class="form_cont">
                <div class="class" id="selectClassDiv">
                    <!-- 선생님이 맡은 반 -->
                    <select name="classSelect" id="classSelect">
                        <option value="">원 전체선택</option>
                        <?php foreach ($classList as $class): ?>
                        <option value="<?php echo $class->CLASS_CD?>"><?php echo $class->CLASS_NM?></option>
                        <?php endforeach ;?>
                    </select>
                </div>
                <div class="all" id="allCheckDiv">
                    <span>전체</span>
                    <input type="checkbox" name="allChck" id="allChck" onclick="selectAll(this)">
                    <label for="allChck"></label>
                </div>
                <div class="student_list write_list" id="class_cd_list">
                    <div class="student_name  write_list_name" id="student_list">
                        <?php foreach ($classList as $class): ?>
                            <div class="name_list">
                                <input type="checkbox" name="class_cd" id="student_<?php echo $class->CLASS_CD?>" value="<?php echo $class->CLASS_CD?>" data-error="errNm1" checked>
                                <label for="student_<?php echo $class->CLASS_CD?>"></label>
                                <span><?php echo $class->CLASS_NM?></span>
                            </div>
                        <?php endforeach ;?>
                    </div>
                </div>
            </div>
            <div class="note-week mb15">
                <input type="month" name="month" id="month" data-placeholder="월 선택" class="required" data-msg-required="년월을 입력해 주세요">
            </div>
            <div class="note_txt">
                <input type="text" name="noteTitle" id="noteTitle" placeholder="제목을 입력해주세요." class="required" data-msg-required="제목을 입력해 주세요">
            </div>
            <div class="form_file">
                <span class="title">파일첨부</span>
                
                
                <div class="dropzone form-img-section" id="dropzone" ></div>
                <!-- [ 카메라 앱으로 이동? ] -->
                <div class="camera">
                    <span>사진</span>
                    <i class="icon_image"></i>
                </div>
                <!-- <p class="comm">
                    동영상 1개, 사진 100개까지 첨부할 수 있습니다.
                    <span>(동영상 30MB이하, 총 용량 500MB 이하)</span>
                </p> -->
            </div>
            <?php if ( $mode == "edit" ) :?>
                <div class="btn_box" style="margin-top: 30px;">
                    <button type="submit" class="send right">수정하기</button>
                </div>
            <?php elseif ($mode == "write") :?>
            <div class="btn_box" style="margin-top: 30px;">
                <button type="button" id="tempSaveBtn" data-target-area='{"name":"<?php echo $header['pn']; ?>_monthly","target":["month","selctWeek","noteTitle"]}' class="save left">임시저장</button>
                <button type="submit" class="send right">보내기</button>
            </div>
            <?php endif; ?>


                <!-- [ 교사앱 : 모달 ]  -->
            <!-- <div class="modal">
                <div class="cont">
                    <p>등록하시겠습니까?</p>
                    <div class="btn">
                        <button class="cancel">취소</button>
                        <button type="submit" class="confirm">확인</button>
                    </div>
                </div>
            </div> -->
        </form>
    </div>
</div>

<script>
    // dropzone Setting
    var _maxfiles = 1;
    var _uploadMultiple = false;
    var _parallelUploads = 1;
</script>
<?php echo $this->include('./layout/common/dropzoneCustom');?>
<!-- dropzone -->
<style>
    .t_content .form_file .camera i { margin-bottom: 5px;}
    .dropzone .dz-preview.dz-image-preview , .dropzone { background-color: #F1F1F5}
    .select2-container--default .select2-selection--multiple  { background-color: #fff ; border: 0}
    .select2-container--default .select2-selection--multiple .select2-selection__choice { padding-left: 0 }
    .form-img-section { padding: 0 }
    .t_content .write_list_name .name_list { width: 140px }
    .mb15 { margin-bottom: 15px }
</style>

<script>
$(document).on('change', '#classSelect' , function(){
    if ( $('#classSelect').val() != ""){
        $('#classSelect').addClass('mb15');
        $('#class_cd_list').hide();
        $('#allCheckDiv').hide();
    } else {
        $('#classSelect').removeClass('mb15');
        $('#class_cd_list').show();
        $('#allCheckDiv').show();
    }
})

$(document).on('click' , '#allChck' , function(){
    if ( $('#allChck').prop('checked')){
        $("input[name*=class_cd]").prop('checked' , true);
    } else {
        $("input[name*=class_cd]").prop('checked' , false);
    }
});

$(document).on('click' , "input[name*=STD_ID]" , function(){
    if ( $("input[name*=class_cd]").length == $("input[name*=class_cd]:checked").length ) {
        $('#allChck').prop('checked', true);
    } else {
        $('#allChck').prop('checked', false);
    }
})

var validobj = $("#fileupload").validate({

onkeyup: false,
errorClass: "myErrorClass",
errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if ( placement == 'errNm1'){
        error.insertAfter($('#formKeepDiv'));
    } else if (placement == 'errNm2'){      // 동의 체크 위치 
        error.insertAfter($('#errNm2AfterError'));
        $('#errNm2AfterError').next().css('text-align' , 'right')
    } else if (placement == 'errNm3'){      // 동의 체크 위치 
        error.insertAfter($('#formNum2Div'));
        // $('#errNm3AfterError').next().css('text-align' , 'right')
    } else {
        var elem = $(element);
        error.insertAfter(element);
    }
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
        text : "<?php echo $confirmText;?>" , 
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "확인",
        cancelButtonText:"취소"
    }).then((result) => {
        if (result.isConfirmed) {
            loadingShowHide();
            if (myDropzone.files != "") {
                myDropzone.processQueue();
            } else {
                goSubmit();
            }
        }else{
            
            return false;
        }
    });
}
});

function goSubmit(){
$("#formClass").attr('disabled',false);
var forms = $('form#fileupload').serializeObject();
forms.ACA_ID = content_data.ACA_ID;
forms.USER_ID = content_data.USER_ID;
forms.files = content_data.files
forms.is_teacher = content_data.is_teacher;
forms.<?php echo csrf_token();?> = "<?php echo csrf_hash() ?>";
forms.gb = 'MON';

fetch("<?php echo base_url("/eduPlan/proc/writeProc")?>", {
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
        if ( typeof tempSave == "object" )  tempSave.delete(); // 임시 저장 삭제
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
            title: '<?php echo $resultText;?>'
        }).then(function (result) {
            if (true) {
                location.href=data.redirect_to;
            }
        });
    }
});
}
</script>

<style>
    div#_student_list + label.myErrorClass { margin-top: -10px ; margin-bottom:10px}
    select#selctWeek + label.myErrorClass  { margin-top: -10px ; margin-bottom:10px}
</style>

<!-- dropzone -->
<?php echo $this->include('./layout/common/select2Custom');?>
<!-- dropzone -->

<?php if ( $mode == "edit" ) :?>
<script>
    
    <?php if (count($select_class_cd) > 1) : ?>
        $('#classSelect').val('');
        $('#classSelect').removeClass('mb15');
        $('#class_cd_list').show();
        $('#allCheckDiv').show();
    <?php else: ?>
        $('#classSelect').val('<?php echo $select_class_cd[0]->CLASS_CD;?>');
        $('#classSelect').addClass('mb15');
        $('#class_cd_list').hide();
        $('#allCheckDiv').hide();
    <?php endif; ?>

    $('#month').val('<?php echo $content->EDU_PLAN_YM;?>');
    $('#selctWeek').val('<?php echo $content->EDU_PLAN_WEEK;?>');
    $('#noteTitle').val('<?php echo $content->EDU_PLAN_NM;?>');
    

    var images = [
        <?php foreach($editfiles as $file): ?>
        {name:"<?php echo $file['orgfilename']?>", url: "<?php echo $file['link']?>", size: "<?php echo $file['size']?>", fileSeq: "<?php echo $file['file_seq'] ?>" , tb:"_CLASS_EDU_PLAN" , thumbnail:"<?php echo $file['thumbnail']?>"},
        <?php endforeach; ?>
    ] 

    for(let i = 0; i < images.length; i++) {

        let img = images[i];
        //console.log(img.url);

        // Create the mock file:
        var mockFile = {name: img.name, size: img.size, url: img.url, seq:img.fileSeq , tb:img.tb };
        // Call the default addedfile event handler
        myDropzone.emit("addedfile", mockFile);
        // And optionally show the thumbnail of the file:
        myDropzone.emit("thumbnail", mockFile, img.thumbnail);
        // Make sure that there is no progress bar, etc...
        myDropzone.emit("complete", mockFile);
        // If you use the maxFiles option, make sure you adjust it to the
        // correct amount:
        var existingFileCount = 1; // The number of files already uploaded
        myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;

    }

    $('#dropzone').contents()[0].textContent = '';

</script>
<?php endif; ?>