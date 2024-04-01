<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<div class="sub_content teacher_album note_content t_write_cont t_content">
    <div class="sub_inner">
        <form action="" method="post"  id="fileupload" enctype="multipart/form-data">
            <div class="form_cont">
                <div class="class">
                    <!-- 선생님이 맡은 반 -->
                    <select name="classSelect" id="classSelect" class="required" data-msg-required="앨범 입력 할 반을 선택하여 주십시요">
                        <option value="">반 선택</option>
                        <?php foreach ($classList as $class): ?>
                            <option value="<?php echo $class->CLASS_CD?>"><?php echo $class->CLASS_NM?></option>
                        <?php endforeach ;?>
                    </select>
                </div>
                <div class="all">
                    <span>전체</span>
                    <input type="checkbox" name="stName" id="allChck">
                    <label for="allChck"></label>
                </div>
                <div class="student_list write_list" id="_student_list">
                    <div class="student_name write_list_name" id="student_list">
                        <!-- 반별 학생 리스트 -->
                    </div>
                </div>
            </div>
            <div class="note_txt">
                <input type="text" name="noteTitle" id="noteTitle" placeholder="제목을 입력해주세요." class="required" data-msg-required="제목을 입력해주세요.">
                <div class="txt_box">
                    <textarea name="noteTxt" id="noteTxt" placeholder="내용을 입력해 주세요." class="required" data-msg-required="내용을 입력해 주세요"></textarea>
                </div>
            </div>
            <div class="form_file">
                <span class="title">파일첨부</span>
                
                <!-- [ 카메라 앱으로 이동? ] -->
                <div class="camera" style="margin-bottom:4px">
                    <span id="phocnt" style="margin-right:5px">사진0</span><span id="vidcnt" style="margin-right:5px">동영상0</span>
                    <i class="icon_image"></i>
                </div>
                <div style="width:100%" id="dropzone" class="dropzone"> 파일 첨부 </div>
                <p class="comm">
                    동영상 1개, 사진 100개까지 첨부할 수 있습니다.
                    <span>(동영상 30MB이하, 총 용량 500MB 이하)</span>
                </p>
            </div>
            
            <div class="btn_box" style="margin-top: 30px;">
                <button type="button" id="tempSaveBtn" data-target-area='{"name":"<?php echo $header['pn']; ?>","target":["noteTitle","noteTxt"]}' class="save left">임시저장</button>
                <button type="submit" class="send right">보내기</button>
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

<style>
    /* div:where(.swal2-container).swal2-center>.swal2-popup{
        width:95%;
        height:150px;
        background-color: #EDEDED;
        padding: 26px 0 0;
    }

    div:where(.swal2-container) button:where(.swal2-styled){
        margin:0;
        width:50%;
        background-color:transparent !important;
        padding:0;
    }

    div:where(.swal2-container) button:where(.swal2-styled).swal2-confirm{
        color:#86704D;
        border-top:1px solid #86704D;
        border-right: 1px solid #86704D;
        border-radius:0;
    }

    div:where(.swal2-container) button:where(.swal2-styled).swal2-cancel{
        color: #FF3120;
        border-radius:0;
        border-top:1px solid #86704D;
    }

    div:where(.swal2-container) div:where(.swal2-actions):not(.swal2-loading) .swal2-styled:hover{
        background-image: none;
        background-color:#EDEDED;
    }

    div:where(.swal2-container) button:where(.swal2-styled).swal2-default-outline:focus{
        box-shadow:none;
    }

    div:where(.swal2-container) .swal2-html-container{
        margin:0;
        font-size:15px;
    }

    div:where(.swal2-icon).swal2-question{
        font-size:0;
    }

    div:where(.swal2-container) div:where(.swal2-actions){
        width:100%;
        align-items:initial;
    } */
</style>

<script>
    // dropzone Setting
    var _maxfiles = 30;
    // var _uploadMultiple = false;
    // var _parallelUploads = 1;
</script>
<?php echo $this->include('./layout/common/dropzoneCustom');?>


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
            text : "앨범을 등록하시겠습니까? " , 
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
                    Swal.fire({ text : "파일을 첨부해 주세요" , icon: "warning"});
                    return false;
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

    fetch("/album/proc/writeProc", {
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
            tempSave.delete(); // 임시 저장 삭제
            loadingShowHide();
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

<style>
    div#_student_list + label.myErrorClass { margin-top: -10px ; margin-bottom:10px}
</style>

<!-- dropzone -->
<?php echo $this->include('./layout/common/select2Custom');?>
<!-- dropzone -->