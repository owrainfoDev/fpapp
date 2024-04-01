<?php 
    $upload['photo'] = [];
    if ($data->FILE_NAME <> '') {
        $file_path = $data->FILE_PATH ."/" . $data->FILE_NAME . "." . $data->FILE_EXT;
        if ( is_file_flag($file_path) )  {
            $upload['photo'][] = $file_path;
        }
    }

    $class_nm = '';
    // var_dump($CURRENT_CLASS_INFO);
    if ( is_array( $CURRENT_CLASS_INFO )){
        foreach ( $CURRENT_CLASS_INFO as $class ){
            $class_nm .= ","    . $class->CLASS_NM;
        }
    }
    $class_name = substr( $class_nm , 1 , strlen($class_nm));

?>
<!-- Flipbook StyleSheet -->
<link href="/resources/dflip/css/dflip.min.css" rel="stylesheet" type="text/css">
<!-- Icons Stylesheet -->
<link href="/resources/dflip/css/themify-icons.min.css" rel="stylesheet" type="text/css">

<style>
._df_thumb {
    border: 0;
    width: 100%;
    text-align: center;
    margin:0;
    height:65px;
    /* object-fit:fill; */
}
._df_book-cover {
    background-size: cover;
    background-repeat: no-repeat;
    /* height:40px; */
}
</style>
<!-- content -->
<style>body:not(.background){ background-color: #fff;}</style>
<div class="sub_content request_content request_confirm request_done">
    <div class="top_util">
        <div class="util_info">
            <p class="name"><?php echo $data->STUDENT_NAME;?> (<?php echo $class_name;?>)</p>
            <span><?php echo $data->REQ_DT;?></span>
        </div>
        <p>자녀의 투약을 선생님께 의뢰합니다.</p>
        <?php if ( $data->MEDI_REQ_STATUS == "03" ) : ?>
        <p class="done_msg">(투약이 완료되어 수정 및 삭제가 불가합니다.)</p>
        <?php endif; ?>
    </div>
    <div class="sub_inner">
        <div class="confirm_cont">
            <div class="confirm_list">
                <dl>
                    <dt class="t_head">증상</dt>
                    <dd class="t_cont"><?php echo $data->SYMP_DESC;?></dd>
                </dl>
                <dl>
                    <dt class="t_head">약 종류</dt>
                    <dd class="t_cont"><?php echo $data->DRUG_TYPE;?></dd>
                </dl>
                <dl>
                    <dt class="t_head">용량</dt>
                    <dd class="t_cont"><?php echo $data->DRUG_DOSE;?></dd>
                </dl>
                <dl>
                    <dt class="t_head">보관방법</dt>
                    <dd class="t_cont"><?php echo get_DRUG_STORAGE_METHOD($data->DRUG_STORAGE_METHOD);?></dd>
                </dl>
                <dl>
                    <dt class="t_head">투약시간</dt>
                    <dd class="t_cont"><?php echo $data->DRUG_TM;?></dd>
                </dl>
                <dl>
                    <dt class="t_head">투약횟수</dt>
                    <dd class="t_cont"><?php echo $data->DRUG_TIMES;?></dd>
                </dl>
                <div class="txt_cont">
                    <span class="title">특이사항 및 전달사항</span>
                    <textarea name="" id="" readonly><?php echo $data->REQ_COMMENT;?> </textarea>
                </div>
                <div class="add_file">
                    <span class="title">첨부파일</span>
                    <div class="add_img">
                        <!-- 첨부한 이미지 있을경우 이미지 불러오기 -->
                        <?php if ( count( $upload['photo'] ) > 0 ):?>
                            <div class="_df_thumb" tags="" id="df_images" thumb="<?php echo $upload['photo'][0];?>" ></div>
                        <?php endif;?>
                    </div>
                </div>
                <!-- 선생님이 투약완료 후 입력한 내용 불러오기 -->
                <div class="txt_cont t_comment">
                    <span class="title">처리 및 전달사항</span>
                    <textarea name="formText" id="formText" readonly><?php echo $data->MEDI_RSLT_COMMENT;?></textarea>
                </div>
            </div>
            <div class="agree_cont">
                <p>투약으로 인한 책임은 의뢰자가 집니다.</p>
                <span class="des">투약의뢰서를 확인 중입니다.</span>
                <!-- 의뢰한 학부모 명 -->
                <p class="parent_des">의뢰자: <span class="p_name"><?php echo $data->REQ_PARENTS_NM;?></span></p>
            </div>
            <?php if ( $data->MEDI_ID == '' ) : ?>
            <div class="btn_box" style="margin-top: 30px;">
                <button type="button" class="send left">수정</button>
                <button type="button" class="cancel right">삭제</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>

</style>


<script src="/resources/dflip/js/dflip.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var insImage = [];
    <?php foreach ($upload['photo'] as $photo):?>
        insImage.push('<?php echo $photo; ?>');
    <?php endforeach;?>
    var option_df_images = {
        source : insImage,
        webgl:false,
    };
    
    <?php if ( $data->MEDI_ID == '' ) : ?>
    
    $(document).on('click','.left' , function(){
        location.href="/medicine/<?php echo $data->MEDI_REQ_NO ; ?>/edit";
    })

    $(document).on('click','.right' , function(){
        Swal.fire({ 
            text : "투약의뢰서를 삭제하시겠습니까? " , 
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "확인",
            cancelButtonText:"취소"
        }).then((result) => {
            if (result.isConfirmed) {
                
                var forms = {
                    ACA_ID : content_data.ACA_ID,
                    USER_ID : content_data.USER_ID,
                    is_teacher : content_data.is_teacher,
                    seq : "<?php echo $data->MEDI_REQ_NO;?>",
                    <?php echo csrf_token();?> : "<?php echo csrf_hash() ?>"
                }

                fetch("/medicine/proc/deleteProc", {
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
                        title: '알림장이 삭제 되었습니다.'
                    }).then(function (result) {
                        if (true) {
                            location.href=data.redirect_to;
                        }
                    });
                });
            }
        });
    })

    <?php endif; ?>
</script>