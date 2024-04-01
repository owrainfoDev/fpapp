<?php foreach ($data['data'] as $list): ?>
<?php 
    if ( ! $list->FILE_URL ) {
        $filepath = $list->FILE_PATH . "/" . $list->FILE_NAME . "." . $list->FILE_EXT;
        $filepath =  str_replace( _ROOT_PATH , '' , $filepath ) ;
    }else {
        $filepath = $list->FILE_URL;
    }
    $filesrc = str_replace("//" , "/" , $filepath);
    $filepath = WRITEPATH . $filesrc;

    $thumbnail = getThumbnailPreview($filepath);

    if ( is_file_flag($filesrc) === false )  {
        $filesrc = '';
    }

?>   
<div class="plan js-load block" id="list-<?php echo $list->EDU_PLAN_NO?>">
    <div class="t_info">
        <div class="edu_title title"><span><?php echo $list->EDU_PLAN_NM;?></span></div>
        <div class="edu_author author"><span><?php echo getUserName($list->ENT_USER_ID); ?></span></div>
        <div class="edu_date date"><span><?php echo substr($list->ENT_DTTM , 0, 10); ?></span></div>
        <!-- [ 교사앱 : 수정/삭제 버튼 ] -->
        <?php if ( $is_teacher ) : ?>
        <div class="t_edit_btn btn_box">
            <button type="button" class="edit left" id="editBtn" data-seq="<?php echo $list->EDU_PLAN_NO?>">수정</button>
            <button type="button" class="del right" id="DeleteBtn" data-seq="<?php echo $list->EDU_PLAN_NO?>">삭제</button>
        </div>
        <?php endif; ?>
    </div>
    <div class="plan_img">
        
        <?php if ($filesrc == "") :?>
            <div class="img_none">등록된 이미지가 없습니다.</div>
        <?php else:?>
            <img src="<?php echo base_url($thumbnail) ?>" alt="123" class="previewImage previewPhoto-<?php echo $list->EDU_PLAN_NO;?>" <?php if ($file_path == "") :?>image_active<?php endif;?>  data-src="<?php echo base_url($filesrc)?>" data-id="<?php echo $list->EDU_PLAN_NO;?>" >
        <?php endif; ?>
    </div>

</div>

<?php endforeach; ?>