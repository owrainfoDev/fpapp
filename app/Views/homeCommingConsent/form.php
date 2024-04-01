<?php 

    $students = $html['students'];
    $detail = (array)$html['detail'];
    $mode = isset( $html['mode'] ) ? $html['mode'] : '' ;

    $class_cd = '';
    $class_nm = '';
    
    foreach ($students['CLASSNOR'] as $class){
        $class_cd .= "," . $class['CLASS_CD'];
        $class_nm .= "," . $class['CLASS_NM'];
    }
    $class_cd = substr($class_cd , 1 , strlen($class_cd));
    $class_nm = substr($class_nm , 1 , strlen($class_nm));

    $today = date("Y-m-d");
    $tomorrow = date("Y-m-d" , strtotime($today . " +1 day") );  
    if ( date("H") < 10 ) {
        $todayCheck = "checked";
        $tomorrowCheck = '';
    } else {
        $todayCheck = "disabled";
        $tomorrowCheck = 'checked';
    }

    if (!empty($detail)) {
        $leave_dt = $detail['LEAVE_DT'];
        
        if ( $today == $leave_dt) {
            $todayCheck = "checked";
            $tomorrowCheck = '';
            
        }
        else if ( $tomorrow == $leave_dt ) {
            $todayCheck = "";
            $tomorrowCheck = 'checked';
        }

        if ( date("Ymd" , strtotime($leave_dt . " -1 day") ) <= date("Ymd", strtotime($today) ) && date("H") > 10 ) {
            $todayCheck = "disabled";
        }

        $leave_tm = explode(":", $detail['LEAVE_TM'] );
        $leave_hour = $leave_tm[0];
        $leave_min = $leave_tm[1];
    }

    if ($mode == 'edit') {
        $top_util_p_sub = "<p>귀가동의서 수정</p>";
        $submitBtnConfirmText = $header['title'] . "를 수정하시겠습니까?";
        $submitBtnConfirmTextResult = $header['title'] . "가 수정되었습니다.";
    } else {
        $submitBtnConfirmText = $header['title'] . "를 등록하시겠습니까?";
        $submitBtnConfirmTextResult = $header['title'] . "가 등록되었습니다.";
    }

    
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content request_content request_write home_write">
    <div class="top_util">
        <p>자녀의 귀가를 아래와 같이 동의합니다.</p>
        <?php echo $top_util_p_sub; ?>
    </div>
    <div class="sub_inner">
        <div class="request_form">
            <form action="" method="post"  id="fileupload" enctype="multipart/form-data">
                <input type="hidden" name="STD_ID" value="<?php echo $students['STD_ID'];?>">
                <?php foreach ($students['CLASSNOR'] as $class): ?>
                    <input type="hidden" name="classCD" value="<?php echo $class['CLASS_CD'];?>">
                <?php endforeach ?>
                <?php if ($mode == 'edit') : ?> 
                    <input type="hidden" name="AGREE_NO" value="<?php echo $detail['AGREE_NO'];?>">
                <?php endif; ?>
                <div class="form_name formBox">
                    <label for="formName"><span class="title">원아</span></label>
                    <input type="text" name="formName" id="formName" value="<?php echo $students['USER_NM'];?>" class="required" data-msg-required="원아명을 입력하여 주십시요" readonly>
                </div>
                <div class="form_class formBox">
                    <label for="formClass"><span class="title">반</span></label>
                    <input type="text" name="formClass" id="formClass" value="<?php echo $class_nm;?>" class="required" data-msg-required="반을 입력하여 주십시요" readonly>
                </div>
                <div class="form_date formBox radioBox">
                    <span class="title">해당 날짜</span>
                    <div class="date_option option01">
                        <input type="radio" name="formDate" id="formDate01" value="<?php echo $today;?>" <?php echo $todayCheck;?>>
                        <label for="formDate01">오늘(<?php echo $today;?>)</label>
                    </div>
                    <div class="date_option option02">
                        <input type="radio" name="formDate" id="formDate02" value="<?php echo $tomorrow;?>" <?php echo $tomorrowCheck;?>>
                        <label for="formDate02">내일(<?php echo $tomorrow;?>)</label>
                    </div>
                </div>
                <div class="form_time formBox selectBox">
                    <span class="title">귀가 시간</span>
                    <div class="time_option option01">
                        <select name="timeOption" id="timeOption" class="required">
                            <option value="">시</option>
                            <option value="9"  <?php echo ($leave_hour == "9") ? "selected" : "" ;?>>오전 9시</option>
                            <option value="10" <?php echo ($leave_hour == "10") ? "selected" : "" ;?>>오전 10시</option>
                            <option value="11" <?php echo ($leave_hour == "11") ? "selected" : "" ;?>>오전 11시</option>
                            <option value="12" <?php echo ($leave_hour == "12") ? "selected" : "" ;?>>오전 12시</option>
                            <option value="13" <?php echo ($leave_hour == "13") ? "selected" : "" ;?>>오후 1시</option>
                            <option value="14" <?php echo ($leave_hour == "14") ? "selected" : "" ;?>>오후 2시</option>
                            <option value="15" <?php echo ($leave_hour == "15") ? "selected" : "" ;?>>오후 3시</option>
                            <option value="16" <?php echo ($leave_hour == "16") ? "selected" : "" ;?>>오후 4시</option>
                            <option value="17" <?php echo ($leave_hour == "17") ? "selected" : "" ;?>>오후 5시</option>
                            <option value="18" <?php echo ($leave_hour == "18") ? "selected" : "" ;?>>오후 6시</option>
                            <option value="19" <?php echo ($leave_hour == "19") ? "selected" : "" ;?>>오후 7시</option>
                        </select>
                    </div>
                    <div class="time_option option02">
                        <select name="minuteOption" id="minuteOption" class="required">
                            <option value="">분</option>
                            <option value="00" <?php echo ($leave_min == "00") ? "selected" : "" ;?>>00분</option>
                            <option value="10" <?php echo ($leave_min == "10") ? "selected" : "" ;?>>10분</option>
                            <option value="20" <?php echo ($leave_min == "20") ? "selected" : "" ;?>>20분</option>
                            <option value="30" <?php echo ($leave_min == "30") ? "selected" : "" ;?>>30분</option>
                            <option value="40" <?php echo ($leave_min == "40") ? "selected" : "" ;?>>40분</option>
                            <option value="50" <?php echo ($leave_min == "50") ? "selected" : "" ;?>>50분</option>
                        </select>
                    </div>
                </div>
                <div class="form_return formBox selectBox">
                    <span class="title">귀가 방법</span>
                    <div class="return_option">
                        <select name="returnOption" id="returnOption">
                            <?php foreach ( getCodeName('LEAVE_TP') as $tp) :?>
                            <option value="<?php echo $tp->CODE;?>" <?php echo ($detail['LEAVE_TP'] == $tp->CODE) ? "selected" : "" ;?>><?php echo $tp->CODE_NM;?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="form_deputy formBox">
                    <label for="formDeputy"><span class="title">대리인</span></label>
                    <div class="type1">
                        <input type="text" name="formDeputy" id="formDeputy_name" value="<?php echo $detail['DEPUTY_NM'];?>" placeholder="성함" class="required"> 
                        
                        <input type="text" name="formDeputy_rel" id="formDeputy_rel" value="<?php echo $detail['DEPUTY_REL_CD'];?>" placeholder="원아와의 관계" class="required">
                        <img src="/resources/images/icon/icon_select_arrow.png" title="arrow" id="formDeputy_rel_arrow" style="position: absolute; width:10px ;height:6px;"/>
                        <ul id="DEPUTY_REL_CD" class="rel_cd">
                            <?php foreach ( getCodeName('DEPUTY_REL_CD') as $tp) :?>
                            <li data-code="<?php echo $tp->CODE;?>"><?php echo $tp->CODE_NM;?></li>
                            <?php endforeach;?>
                        </ul>
                        
                        <input type="text" name="formDeputy_num" id="formDeputy_num" value="<?php echo $detail['DEPUTY_TEL_NO'];?>" placeholder="전화번호" maxlength="14" class="required">
                    </div>
                </div>
                <div class="form_emergency formBox">
                    <label for="formEmergency"><span class="title">비상연락망</span></label>
                    <div class="type1">
                        <input type="text" name="formEmergency_name" id="formEmergency_name" value="<?php echo $detail['EMG_CALL_NM'];?>" placeholder="성함" class="required">
                        <input type="text" name="formEmergency_rel" id="formEmergency_rel" value="<?php echo $detail['EMG_CALL_REL_CD'];?>" placeholder="원아와의 관계" class="required">
                        <img src="/resources/images/icon/icon_select_arrow.png" title="arrow" id="formEmergency_rel_arrow" style="position: absolute; width:10px ;height:6px;"/>
                        <ul id="EMERGENCY_REL_CD" class="rel_cd">
                            <?php foreach ( getCodeName('DEPUTY_REL_CD') as $tp) :?>
                            <li data-code="<?php echo $tp->CODE;?>"><?php echo $tp->CODE_NM;?></li>
                            <?php endforeach;?>
                        </ul>
                        
                        <input type="text" name="formEmergency_num" id="formEmergency_num" value="<?php echo $detail['EMG_CALL_TEL_NO'];?>" placeholder="전화번호" maxlength="14" class="required">
                    </div>

                </div>
                <div class="form_txt">
                    <span class="title">특이사항 및 전달사항</span>
                    <textarea name="formText" id="formText" placeholder="전달할 메세지를 입력하세요" class="required"><?php echo $detail['REQ_MEMO']?></textarea>
                </div>
                <div class="form_file form_txt">
                    <span class="title">파일첨부</span>
                    
                    <div style="width:100%; " id="dropzone" class="dropzone"> 파일 첨부 </div>
                    <!-- [ 카메라 앱으로 이동? ] -->
                    
                    <div class="camera">
                        <i class="icon_image"></i>
                    </div>
                </div>
                <div class="form_agree">
                    <p>대리인 귀가에 동의합니다.</p>
                    <span class="des">원에서는 부모가 희망하더라도<br>
                        영유아를 혼자 귀가시키지 않습니다.
                    </span>
                    
                    <div class="chckBox">
                        <span>동의합니다.</span>
                        <input type="checkbox" name="agree" id="agree" value="Y" class="required">
                        <label for="agree"></label>
                    </div>
                </div>
                <div id="signin_errors" ></div>
                <div class="send_btn btn_box">
                    <button type="submit" class="" id="submitBtn">보내기</button>
                </div>
                <!-- <div class="modal">
                    <div class="cont">
                        <p>귀가동의서를 등록하시겠습니까?</p>
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
<div class="backdrop" style="display:none"></div>

<script>
    // dropzone Setting
    var _maxfiles = 1;
    var _uploadMultiple = false;
    var _parallelUploads = 1;
    var _thumbnailHeight = 50;
    var _thumbnailWidth = 40;
</script>
<?php echo $this->include('./layout/common/dropzoneCustom');?>
<!-- dropzone -->
<style>
  

    .form-img-section { padding: 0 }
    .t_content .write_list_name .name_list { width: 140px }
    .mb15 { margin-bottom: 15px }
    .myErrorClass { border: 1px solid #FF0000 !important}
    #signin_errors { font-size:12px; color:#FF0000 ; text-align: center }
    .myErrorClass + label { border: 1px solid #FF0000 !important}
    
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

<?php if ($mode == 'edit') : ?> 
<?php
$f = $detail;
$file = (object)$f;
$editfiles = [];
if (isset($file->FILE_NAME) ){

    $filepath = WRITEPATH . $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT;
    $filepath = str_replace('//','/',$filepath);
    $thumbnail = getThumbnailPreview($filepath);
    $editfiles[] = [
        'link' => $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT,
        'orgfilename' => $file->FILE_ORG_NAME,
        'size' => $file->FILE_SIZE,
        'file_seq' => $file->AGREE_NO,
        'ext' => $file->FILE_EXT,
        'thumbnail' => $thumbnail
    ];
}
?>
<script>
    var images = [
        <?php foreach($editfiles as $file): ?>
        {name:"<?php echo $file['orgfilename']?>", url: "<?php echo $file['link']?>", size: "<?php echo $file['size']?>", fileSeq: "<?php echo $file['file_seq'] ?>" , tb:"_LEAVE_AGREE" , thumbnail:"<?php echo $file['thumbnail']?>"},
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
<?php endif ; ?>

<script>
    $('.rel_cd').hide();
    $('#formDeputy_rel_arrow').on('click', function(){
        if ($('#DEPUTY_REL_CD').css('display') == 'none'){
            $('#DEPUTY_REL_CD').show();
        } else {
            $('#DEPUTY_REL_CD').hide();
        }
        $('.backdrop').show();
    })

    $('#formEmergency_rel_arrow').on('click', function(){
        if ($('#EMERGENCY_REL_CD').css('display') == 'none'){
            $('#EMERGENCY_REL_CD').show();
        } else {
            $('#EMERGENCY_REL_CD').hide();
        }
        $('.backdrop').show();
    })

    $(document).on('click' , 'ul.rel_cd > li' , function (){
        var ta =  $(this).parent().attr('id');
        var code = $(this).data('code') ;
        if ( ta == "DEPUTY_REL_CD"){
            if ( code == '99') {
                $('#formDeputy_rel').val('');
                $('#formDeputy_rel').attr('placeholder', '관계를 입력하여 주십시요');
                $('#formDeputy_rel').focus();
                $('.backdrop').hide()
                $('.rel_cd').hide();
            } else {
                $('#formDeputy_rel').val($(this).text());
                $('.backdrop').hide()
                $('.rel_cd').hide();
            }
        } else {
            if ( code == '99') {
                $('#formEmergency_rel').val('');
                $('#formEmergency_rel').attr('placeholder', '관계를 입력하여 주십시요');
                $('#formEmergency_rel').focus();
                $('.backdrop').hide()
                $('.rel_cd').hide();
            } else {
                $('#formEmergency_rel').val($(this).text());
                $('.backdrop').hide()
                $('.rel_cd').hide();
            }
        }
    });

    function selectPosition(parent_el , target_el1 , target_el2){
        var p = $('#' + parent_el ).offset();
        var w = $('#' + parent_el ).width();

        $('#' + target_el1 ).offset({top:p.top + 13 ,left:p.left+(w-5)})
        $('#' + target_el2 ).css({position : "absolute" , width: w+22})
        $('#' + target_el2 ).css('z-index' , 999);
        $('#' + target_el2 ).offset({top:p.top-70 , left:p.left});

        
    }

    $(window).on('resize' , function(){
        selectPosition('formDeputy_rel' , 'formDeputy_rel_arrow' , 'DEPUTY_REL_CD');
    })
    $(document).on('click', '.backdrop' , function(){
        $(this).hide()
        $('.rel_cd').hide();
    })
    selectPosition('formDeputy_rel' , 'formDeputy_rel_arrow', 'DEPUTY_REL_CD');
    selectPosition('formEmergency_rel' , 'formEmergency_rel_arrow' , 'EMERGENCY_REL_CD');

    var submitted;

    var validobj = $("#fileupload").validate({
        messages:{
			agree : {required: "귀가 동의를 체크해주세요."},
		},
        onkeyup: false,
        errorClass: "myErrorClass",
        errorPlacement: function(error, element) {
            var placement = $(element).data('error');
            // if ( placement == 'errNm1'){
            //     error.insertAfter($('#formKeepDiv'));
            // } else if (placement == 'errNm2'){      // 동의 체크 위치 
            //     error.insertAfter($('#errNm2AfterError'));
            //     $('#errNm2AfterError').next().css('text-align' , 'right')
            // } else if (placement == 'errNm3'){      // 동의 체크 위치 
            //     error.insertAfter($('#formNum2Div'));
            //     // $('#errNm3AfterError').next().css('text-align' , 'right')
            // } else {
            //     var elem = $(element);
            //     error.insertAfter(element);
            // }
            
            // error.insertAfter($('.btn_box'));

            // if( element.is(':radio') || element.is(':checkbox')) {
            //     error.appendTo(element.parent());
            // }
        },
        invalidHandler: function(form, validator) {
            submitted = true;
        },
        showErrors: function(errorMap, errorList) {

            if (submitted) {
                var summary = "* 필수 항목을 입력하여 주십시요";

                $("#signin_errors").html(summary);
                submitted = false;
            }

            this.defaultShowErrors();
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
                text : "<?php echo $submitBtnConfirmText;?>" , 
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

        fetch("/homeCommingConsent/proc/formProc", {
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
                    title: '<?php echo $submitBtnConfirmTextResult; ?>'
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