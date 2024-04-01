<?php 
    $st = [];
    foreach ( $data['albumStd'] as $s ) {
        $st[] = $s->STD_ID;
    }
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<div class="sub_content teacher_album note_content t_write_cont t_content">
    <div class="sub_inner">
        <form action="" method="post"  id="fileupload" enctype="multipart/form-data">
        <input type="hidden" name="album_no" value="<?php echo $album_no?>">
            <div class="form_cont">
                <div class="class">
                    <!-- 선생님이 맡은 반 -->
                    <select name="classSelect" id="classSelect" class="required" data-msg-required="앨범 입력 할 반을 선택하여 주십시요" disabled>
                        <option value="">반 선택</option>
                        <?php foreach ($classList as $class): ?>
                            <option value="<?php echo $class->CLASS_CD?>" <?php echo ($class->CLASS_CD == $data['album']->CLASS_CD ? "selected":"");?>><?php echo $class->CLASS_NM?></option>
                        <?php endforeach ;?>
                    </select>
                </div>
                <div class="all">
                    <span>전체</span>
                    <input type="checkbox" name="stName" id="allChck" disabled>
                    <label for="allChck"></label>
                </div>
                <div class="student_list write_list" id="_student_list">
                    <div class="student_name write_list_name" id="student_list">
                        <!-- 반별 학생 리스트 -->
                        <?php foreach ( $studentList as $student ) :?>
                            <div class="name_list">
                            <input type="checkbox" name="STD_ID" id="student_<?php echo $student->STD_ID; ?>" value="<?php echo $student->STD_ID; ?>" data-error="errNm1" 
                            <?php echo ( in_array( $student->STD_ID , $st ) ? "checked":''); ?> disabled>
                            <label for="student_<?php echo $student->STD_ID; ?>"></label>
                            <span><?php echo $student->STD_NM; ?></span>
                        </div>
                        <?php endforeach ; ?>
                    </div>
                </div>
            </div>
            <div class="note_txt">
                <input type="text" name="noteTitle" id="noteTitle" placeholder="제목을 입력해주세요." value="<?php echo $data['album']->ALBUM_NM; ?>" class="required" data-msg-required="제목을 입력해주세요.">
                <div class="txt_box">
                    <?php 
                        $content = $data['album']->CNTS;
                        $content = str_replace("<br />" , "\r\n" , $content);
                    ?>
                    <textarea name="noteTxt" id="noteTxt" placeholder="내용을 입력해 주세요." class="required" data-msg-required="내용을 입력해 주세요"><?php echo $content ?></textarea>
                </div>
            </div>
            <div class="form_file">
                <span class="title">파일첨부</span>
                
                <!-- [ 카메라 앱으로 이동? ] -->
                <div class="camera">
                    <span>사진2 동영상1/1</span>
                    <i class="icon_image"></i>
                </div>
                <div style="width:100%" id="dropzone" class="dropzone"> 파일 첨부 </div>
                <p class="comm">
                    동영상 1개, 사진 100개까지 첨부할 수 있습니다.
                    <span>(동영상 30MB이하, 총 용량 500MB 이하)</span>
                </p>
            </div>

            <div class="btn_box" style="margin-top: 30px;">
                <button type="button" class="cancel left">취소</button>
                <button type="submit" class="send right">수정</button>
            </div>
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
    var _maxfiles = 30;
    // var _uploadMultiple = false;
    // var _parallelUploads = 1;
</script>
<?php echo $this->include('./layout/common/dropzoneCustom');?>

<?php 
$editfile = $data['albumFile'];
$editfiles = [];
if (isset($editfile) ){
    foreach ($editfile as $file){
        if ( ! $file->FILE_URL ) {
            $filepath = $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT;
            $filepath =  str_replace( _ROOT_PATH , '' , $filepath ) ;
        }else {
            $filepath = $file->FILE_URL;
        }
        $filepath = WRITEPATH . $filepath;
        $filepath = str_replace('//','/',$filepath);
        $thumbnail = getThumbnailPreview($filepath);
        $editfiles[] = [
            'link' => $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT,
            'orgfilename' => $file->FILE_ORG_NAME,
            'size' => $file->FILE_SIZE,
            'file_seq' => $file->ALBUM_FILE_SEQ,
            'ext' => $file->FILE_EXT,
            'thumbnail' => $thumbnail
        ];
    }
}
?>
<script type="text/javascript">
    var images = [
        <?php foreach($editfiles as $file): ?>
        {name:"<?php echo $file['orgfilename']?>", url: "<?php echo $file['link']?>", size: "<?php echo $file['size']?>", fileSeq: "<?php echo $file['file_seq'] ?>" , tb:"_ALBUM_APND_FILE" , thumbnail:"<?php echo $file['thumbnail']?>"},
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

<script type="text/javascript">
var write = {
    changeClassSelect: function(){
        var forms = {
            class_cd : $('#classSelect').val() ,
        }
        fetch("/api/ajax/getstudentsFromClass", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(forms) })
        .then((response) => response.json())
        .then((data) => {
            $('#student_list').empty();
            // $('#selectChild').append($());
            $.each(data, function (index , item){
                $('#student_list').append($(`<div class="name_list">
                            <input type="checkbox" name="STD_ID" id="student_${item.STD_ID}" value="${item.STD_ID}" data-error="errNm1" checked>
                            <label for="student_${item.STD_ID}"></label>
                            <span>${item.STD_NM}</span>
                        </div>`));
            })
        });
    },
    checkboxCheckedAll: function(){
        if ( $('#allChck').prop('checked')){
            $("input[name*=STD_ID]").prop('checked' , true);
        } else {
            $("input[name*=STD_ID]").prop('checked' , false);
        }
    },
    checkboxCheck: function(){
        if ( $("input[name*=STD_ID]").length == $("input[name*=STD_ID]:checked").length ) {
            $('#allChck').prop('checked', true);
        } else {
            $('#allChck').prop('checked', false);
        }
    }
};
$(document).on('change' , "#classSelect" , function(){
    write.changeClassSelect();
    $('#allChck').prop('checked' , true);
} );
$(document).on('click' , '#allChck' , function(){
    write.checkboxCheckedAll();
});
$(document).on('click' , "input[name*=STD_ID]" , function(){
    write.checkboxCheck();
})

$(document).on('click','button.cancel' , function(){
    location.href="/album";
})

var validobj = $("#fileupload").validate({
    rules: {
        'STD_ID': {
            required: true
        }
    },
    messages: {
        'STD_ID': {
            required: "원생을 선택하여 주세요",
        }
    },
    onkeyup: false,
    errorClass: "myErrorClass",
    errorPlacement: function(error, element) {
        var placement = $(element).data('error');
        if ( placement == 'errNm1'){
            error.insertAfter($('#_student_list'));
            // $('#student_list').append(element);
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
            text : "앨범을 수정하시겠습니까? " , 
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "확인",
            cancelButtonText:"취소"
        }).then((result) => {
            if (result.isConfirmed) {
                if (myDropzone.files != "") {
                    loadingShowHide();
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
    
    var forms = $('form#fileupload').serializeObject();
    forms.ACA_ID = content_data.ACA_ID;
    forms.USER_ID = content_data.USER_ID;
    forms.files = content_data.files
    forms.is_teacher = content_data.is_teacher;
    
    fetch("/album/proc/editProc", {
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
                title: '수정 되었습니다.'
            }).then(function (result) {
                if (true) {
                    location.reload();
                }
            });
        }
    });
}
</script>


<style>
    div#_student_list + label.myErrorClass { margin-top: -10px ; margin-bottom:10px}
</style>

<!-- dropzone -->
<?php echo $this->include('./layout/common/select2Custom');?>
<!-- dropzone -->