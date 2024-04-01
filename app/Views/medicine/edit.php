<?php 
$detail = $data['data']['data'];
$editfiles = [];
if ( $detail->FILE_NAME != ""){
    $editfiles[] = [
        'link' => $detail->FILE_PATH . "/" . $detail->FILE_NAME . "." . $detail->FILE_EXT,
        'orgfilename' => $detail->FILE_ORG_NAME,
        'size' => $detail->FILE_SIZE,
        'file_seq' => $detail->MEDI_REQ_NO,
        'tp' => '_MEDI_REQ',
        'thumbnail' => $detail->FILE_PATH . "/" . $detail->FILE_NAME . "." . $detail->FILE_EXT
    ];
}
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content request_content request_write">
    <div class="top_util">
        <p>자녀의 투약을 선생님께 의뢰합니다. - 수정</p>
    </div>
    <div class="sub_inner">
        <div class="request_form">
            <form action="" method="post"  id="fileupload" enctype="multipart/form-data">
                <input type="hidden" name="seq" id="seq" value="<?php echo $detail->MEDI_REQ_NO;?>">
                <div class="form_name formBox">
                    <label for="formName"><span class="title">원아</span></label>
                    <!-- 로그인한 학부모의 원아명 고정 -->
                    <input type="text" name="formName" id="formName" value="<?php echo $data['stdInfo']['USER_NM']?>" readonly>
                    <input type="hidden" name="formStdId" id="formStdId" value="<?php echo $data['stdInfo']['STD_ID']?>" readonly>
                </div>
                <div class="form_class formBox">
                    <label for="formClass"><span class="title">반</span></label>
                    <select name="formClass" id="formClass" multiple="multiple" style="width:100%; background:#fff" class="required" data-msg-required="반을 입력하여 주십시요">
                        <?php foreach ($data['stdInfo']['CLASSNOR'] as $class) : ?>
                            <?php if ( $class['CLASS_APPLY_STATUS'] == '01') : ?>
                            <option value="<?php echo $class['CLASS_CD'];?>" selected><?php echo $class['CLASS_NM'];?></option>
                            <?php endif ; ?>
                        <?php endforeach ;?>
                    </select>
                </div>
                <?php 
                    $today = date("Y-m-d");
                    $difftime = date("Ymd")."10";
                    $tomorrow = date("Y-m-d" , strtotime("+1 day") );  
                    if ( date("YmdH") <= $difftime ) {
                        $todayCheck = "checked";
                        $tomorrowCheck = '';
                    } else {
                        $todayCheck = "";
                        $tomorrowCheck = 'checked';
                    }
                ?>
                <div class="form_date formBox radioBox">
                    <span class="title">투약요청일</span>
                    <div class="date_option option01">
                        <input type="radio" name="formDate" id="formDate01" value="<?php echo $today?>" <?php echo $todayCheck; ?>>
                        <label for="formDate01">오늘(<?php echo $today?>)</label>
                    </div>
                    <div class="date_option option02">
                        <input type="radio" name="formDate" id="formDate02" value="<?php echo $tomorrow?>" <?php echo $tomorrowCheck ?>>
                        <label for="formDate02">내일(<?php echo $tomorrow?>)</label>
                    </div>
                </div>
                <div class="form_symptoms formBox">
                    <label for="formSymptoms"><span class="title">증상</span>
                        <input type="text" name="formSymptoms" id="formSymptoms" placeholder="감기,콧물" value="<?php echo $detail->SYMP_DESC;?>" class="required" data-msg-required="증상을 입력하여 주십시요">
                    </label>
                    
                </div>
                <div class="form_type formBox">
                    <label for="formType"><span class="title">약 종류</span>
                        <input type="text" name="formType" id="formType" placeholder="예) 물약, 가루약" value="<?php echo $detail->DRUG_TYPE;?>" class="required" data-msg-required="약종류를 입력하여 주십시요">
                    </label>
                    
                </div>
                <div class="form_type formBox">
                    <label for="formType_amount"><span class="title">용량</span>
                        <input type="text" name="formType_amount" id="formType_amount" placeholder="예) 3ml" value="<?php echo $detail->DRUG_DOSE;?>" class="required" data-msg-required="용량을 입력하여 주십시요">
                    </label>
                    
                </div>
                <div class="form_keep formBox radioBox" style="flex-wrap: wrap";>
                    <span class="title">보관방법</span>
                    <div class="keep_option option01">
                        <input type="radio" name="formKeep" id="formKeep01" value="R" data-error="errNm1" class="required" data-msg-required="보관방법 체크하여 주십시요" <?php echo ( $detail->DRUG_STORAGE_METHOD == "R" ? "checked":'');?>>
                        <label for="formKeep01">실온</label>
                    </div>
                    <div class="keep_option option02" id="formKeepDiv">
                        <input type="radio" name="formKeep" id="formKeep02" value="C" data-error="errNm1" class="required" data-msg-required="보관방법 체크하여 주십시요" <?php echo ( $detail->DRUG_STORAGE_METHOD == "C" ? "checked":'');?>>
                        <label for="formKeep02">냉장</label>
                    </div>
                </div>
                <div class="form_time formBox">
                    <label for="formTime"><span class="title">투약시간</span>
                        <input type="text" name="formTime" id="formTime" placeholder="예) 2시" value="<?php echo $detail->DRUG_TM;?>" class="required" data-msg-required="투약시간을 입력하여 주십시요">
                    </label>
                    
                </div>
                <div class="form_num formBox radioBox ">
                    <span class="title">투약횟수</span>
                    <div class="keep_option option01">
                        <input type="radio" name="formNum" id="formNum1" value="1" data-error="errNm2" class="required" data-msg-required="투약횟수 체크하여 주십시요" <?php echo ( $detail->DRUG_TIMES == "1" ? "checked":'');?>>
                        <label for="formNum1">1회</label>
                    </div>
                    <div class="keep_option option02" id="formNum2Div">
                        <input type="radio" name="formNum" id="formNum2" value="2" data-error="errNm2" class="required" data-msg-required="투약횟수 체크하여 주십시요" <?php echo ( $detail->DRUG_TIMES == "2" ? "checked":'');?>>
                        <label for="formNum2">2회</label>
                    </div>
                   
                </div>
                <div class="form_txt">
                    <span class="title">특이사항 및 전달사항</span>
                    <textarea name="REQ_COMMENT" id="REQ_COMMENT" placeholder="전달할 메세지를 입력하세요"><?php echo $detail->REQ_COMMENT;?></textarea>
                </div>
                <div class="form_file formBox" style="flex-wrap: wrap">
                    <span class="title">파일첨부</span>
                    <div id="dropzone" class="dropzone" style="width:90px; height:65px; padding:0;"> 파일 첨부 </div>
                    <div class="camera">
                        <i class="icon_image"></i>
                    </div>
         
                </div>

                <div class="form_agree">
                    <p>투약으로 인한 책임은 의뢰자가 집니다.</p>
                    <div class="chckBox" id="errNm2AfterError">
                        <span>동의합니다.</span>
                        <input type="checkbox" name="agree" id="agree" data-error="errNm2" class="required" data-msg-required="보관방법 체크하여 주십시요">
                        <label for="agree"></label>
                    </div>

                </div>
                <div class="send_btn btn_box">
                    <button type="submit">보내기</button>
                </div>
                <!-- <div class="modal">
                    <div class="cont">
                        <p>투약의뢰서를 등록하시겠습니까?</p>
                        <div class="btn">
                            <button class="cancel">취소</button>
                            <button type="submit" class="confirm">확인</button>
                        </div>
                    </div>
                </div> -->
            </form>
            
        </div>
    </div>
</div>
<script>
    // dropzone Setting
    var _maxfiles = 1;
    var _uploadMultiple = false;
    var _parallelUploads = 1;
</script>

<!-- dropzone -->
<?php echo $this->include('./layout/common/dropzoneCustom');?>
<!-- dropzone -->
<style>
    .t_content .form_file .camera i { margin-bottom:5px;}
    .dropzone .dz-preview.dz-image-preview , .dropzone {
        background: url(/resources/images/add_img_bg.png) no-repeat center / 100%;
        background-color: #F1F1F5;
    }
    .select2-container--default .select2-selection--multiple  { background-color: #fff ; border : 0}
    .select2-container--default .select2-selection--multiple .select2-selection__choice { padding-left:0 }
    .dropzone .dz-preview .dz-image{
        border-radius:0;
        width:90px;
        height:65px;
        margin: 0px;
        background-color: #F1F1F5;
    }
    .dropzone .dz-preview .dz-image img{
        margin:0;
        border-radius:0;
        object-fit:cover;
        width:100%;
        height:100%;
    }
    .dropzone .dz-preview:hover .dz-image img{
        -webkit-transform:scale(1);
        transform:scale(1);
        -webkit-filter:none;
        filter:none;
    }
    .dropzone .dz-preview .dz-remove{
        bottom:-5px;
        right:-5px;
        width:18px;
        height:18px;
    }
</style>

<script>
var images = [
        <?php foreach($editfiles as $file): ?>
        {name:"<?php echo $file['orgfilename']?>", url: "<?php echo $file['link']?>", size: "<?php echo $file['size']?>", fileSeq: "<?php echo $file['file_seq'] ?>" , tb:"<?php echo $file['tp']?>" , thumbnail:"<?php echo $file['thumbnail']?>"},
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


$("#formClass").select2({ disabled: true, readonly: true, tokenSeparators: ["/"] });

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
            text : "투약의뢰서를 수정하시겠습니까? " , 
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
    var forms = $('form#fileupload').serializeObject();
    console.log(forms);
    forms.ACA_ID = content_data.ACA_ID;
    forms.USER_ID = content_data.USER_ID;
    forms.files = content_data.files
    forms.is_teacher = content_data.is_teacher;
    forms.<?php echo csrf_token();?> = "<?php echo csrf_hash() ?>";

    fetch("/medicine/proc/editProc", {
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
                    location.href=data.redirect_to;
                }
            });
        }
    });
}

</script>

<!-- select2Custom -->
<?php echo $this->include('./layout/common/select2Custom2');?>
<!-- select2Custom -->