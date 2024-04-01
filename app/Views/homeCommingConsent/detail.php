<?php 
    
    $list = $html;
    $file_path = $list->FILE_PATH ."/" . $list->FILE_NAME . "." . $list->FILE_EXT;
    
    if ( is_file_flag($file_path) === false )  {
        $file_path = '';
    } else {
        $file_path = str_replace("//" , "/" , $file_path);
    }
?>
<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content request_content request_confirm">
    <div class="top_util">
        <div class="util_info">
            <p class="name"><?php echo getUserName($html->STD_ID);?> (<?php echo $html->STD_ID;?>)</p>
            <span><?php echo substr($html->ENT_DTTM, 0, 10);?></span>
        </div>
        <p>자녀의 귀가 시 아래의 보호자에게 인도하여 주시기 바랍니다.</p>
    </div>
    <div class="sub_inner">
        <div class="confirm_cont">
            <div class="confirm_list">
                <dl>
                    <dt class="t_head">해당 날짜</dt>
                    <dd class="t_cont"><?php echo $html->LEAVE_DT;?></dd>
                </dl>
                <dl>
                    <dt class="t_head">귀가시간</dt>
                    <dd class="t_cont"><?php echo $html->LEAVE_TM;?></dd>
                </dl>
                <dl>
                    <dt class="t_head">귀가방법</dt>
                    <dd class="t_cont"><?php echo getCodeName('LEAVE_TP' , $html->LEAVE_TP)->CODE_NM;?></dd>
                </dl>
                <dl>
                    <dt class="t_head">대리인</dt>
                    <dd class="t_cont"><?php echo $html->DEPUTY_NM;?>(<?php echo $html->DEPUTY_REL_CD?>) <?php echo $html->DEPUTY_TEL_NO;?></dd>
                </dl>
                <dl>
                    <dt class="t_head">비상연락망</dt>
                    <dd class="t_cont"><?php echo $html->EMG_CALL_NM;?>(<?php echo $html->EMG_CALL_REL_CD?>) <?php echo $html->EMG_CALL_TEL_NO;?></dd>
                </dl>
                <div class="txt_cont">
                    <span class="title">특이사항 및 전달사항</span>
                    <textarea name="" id="" readonly><?php echo $html->REQ_MEMO;?></textarea>
                </div>
                <div class="add_file">
                    <span class="title">첨부파일</span>
                    <div class="add_img">
                        <!-- 첨부한 이미지 있을경우 이미지 불러오기 -->
                        <?php if ( $file_path ) :?>
                        <img src="<?php echo base_url($file_path) ?>" alt="" class="previewImage" <?php if ($file_path == "") :?>image_active<?php endif;?> style="width:95px;height:65px;object-fit:cover">
                            <!-- 등록된 이미지 없을경우 .image_active 클래스 제거 -->
                        <?php endif; ?>
                        <?php if ($file_path == "") :?>
                            <div class="img_none" style="font-size:12px"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if ($is_teacher == false && $html->LEAVE_AGREE_STATUS == "03") :?>
            <div class="txt_cont t_comment">
                <span class="title">처리 및 전달사항</span>
                <textarea name="CONF_MEMO" id="CONF_MEMO" readonly><?php echo $html->CONF_MEMO;?></textarea>
            </div>
            <?php endif; ?>
            <div class="agree_cont">
                <p>위 원아의 금일 귀가시<br>
                    대리인에게 인도하여 주시기 바랍니다.</p>
                <?php if ( in_array( $html->LEAVE_AGREE_STATUS  , array("02","03") ) ) : ?>
                <p class="parent_des">의뢰자: <span class="p_name"><?php echo getUserName($html->REQ_ID);?></span></p>
                <?php endif; ?>
            </div>
            <?php if ( $is_teacher ) : ?>
                <?php if ( $html->LEAVE_AGREE_STATUS == "02") : ?>
                    <div class="btn_box">
                        <button class="done" id="CompletedBtn">귀가 완료</button>
                    </div>    
                <?php endif; ?>
            <?php else:?>
                <?php if ( $html->LEAVE_AGREE_STATUS != "03") : ?>
                    <div class="btn_box">
                        <button type="button" id="editBtn">수정</button>
                        <button type="button" class="del" id="DeleteBtn">귀가동의서 취소</button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* div:where(.swal2-container) h2:where(.swal2-title){
        font-size:15px;
        color: #000;
        font-weight: 500;
        padding-top:26px;
    }
    div:where(.swal2-container).swal2-center>.swal2-popup{
        width: 95%;
        height:230px;
        background-color: #EDEDED;
        padding: 0 16px 13px;
    }
    div.swal2-container div.swal2-actions{
        margin:0;
    }
    .sweet-alert-button {
        background: #00341E !important;
        width: 125px !important;
        height:35px;
        border-radius : 23px !important;
        padding:0;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16);
        margin: 10px 0 0 0;
        font-size:15px;
    }
    .swal2-textarea {
        font-size:13px !important;
        border: 1px solid #707070 !important;
        box-shadow : unset !important;
        background-color: #fff;
        height:109px !important;
        width: 100%;
        margin: 16px auto 0;
        color: #333;
        font-weight:400;
    }

    .swal2-textarea::placeholder{
        color: #999;
        font-size:12px;
    } */


    /* div:where(.swal2-container) button:where(.swal2-close):hover{
        background: url(/resources/images/icon/icon_colse_img.png) no-repeat center / contain;
        text-indent:-9999px;
        color:#fff;
        width:18px;
        height:18px;
    }
    div:where(.swal2-container) button:where(.swal2-close){
        background: url(/resources/images/icon/icon_colse_img.png) no-repeat center / contain;
        color:#fff;
        width:18px;
        height:18px;
        position: absolute;
        top: 10px;
        right: 10px;
        text-indent:-9999px;
    } */
</style>

<script>

    $(document).on('click', '#editBtn' , function(){
        location.href="<?php echo current_url()?>/edit";
    })

    $(document).on('click','#CompletedBtn',function(){
        Swal.fire({
            title : '귀가동의서 확인',
            input : 'textarea',
            inputValue : `<?php echo $html->CONF_MEMO;?>`,
            // confirmButtonColor: "#86704d",
            confirmButtonText: "확인",
            customClass: {
                confirmButton: 'sweet-alert-button',
            },
            showCloseButton: true,
            preConfirm: function(){
                
            }
        
        }).then(function(result){
            var value = result.value;
            if (result.value){
                Swal.fire({ 
                    text : "등록하시겠습니까? " , 
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "확인",
                    cancelButtonText:"취소",
                }).then((result) => {
                    if (result.isConfirmed) {
                        var forms = {
                            ACA_ID : content_data.ACA_ID,
                            USER_ID : content_data.USER_ID,
                            is_teacher : content_data.is_teacher,
                            seq : "<?php echo $html->AGREE_NO;?>",
                            CONF_MEMO : value,
                            <?php echo csrf_token();?> : "<?php echo csrf_hash() ?>"
                        }

                        fetch("/homeCommingConsent/proc/CompletedBtnProc", {
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
                                title: '등록 되었습니다.'
                            }).then(function (result) {
                                if (true) {
                                    if ( data.redirect_to == 'reload'){
                                        location.reload();
                                    } else {
                                        location.href=data.redirect_to;
                                    }
                                    
                                }
                            });
                        });
                    }
                });
            }
        })
        
    });
    
    $(document).on('click' , '#DeleteBtn' , function(){
        var seq = '<?php echo $html->AGREE_NO;?>';
        var forms = {
            seq : seq,
            <?php echo csrf_token();?> : "<?php echo csrf_hash() ?>",
            page : '<?php echo current_url(); ?>'
        };
        Swal.fire({ 
            html : "귀가동의서를 취소 하시겠습니까?" , 
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "확인",
            cancelButtonText:"취소"
        }).then((result) => {
            if (result.isConfirmed) {
                loadingShowHide();
                
                fetch("/homeCommingConsent/proc/deleteProc", {
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
                            title: '귀가동의서가 취소 되었습니다.'
                        }).then(function (result) {
                            if (true) {
                               location.href=data.redirect_to;
                            }
                        });
                    })
                

            }
        }); 
    });

</script>