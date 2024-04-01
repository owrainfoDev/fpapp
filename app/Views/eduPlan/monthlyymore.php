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

?>   
<div class="plan js-load block">
    <div class="t_info">
        <div class="edu_title title"><span><?php echo $list->EDU_PLAN_NM;?></span></div>
        <div class="edu_author author"><span><?php echo getUserName($list->ENT_USER_ID); ?></span></div>
        <div class="edu_date date"><span><?php echo substr($list->ENT_DTTM , 0, 10); ?></span></div>
        <!-- [ 교사앱 : 수정/삭제 버튼 ] -->
        <!-- <div class="t_edit_btn btn_box">
            <button type="" class="edit left">수정</button>
            <button type="" class="del right">삭제</button>
        </div> -->
    </div>
    <div class="plan_img">
        <!-- 월간교육계획 이미지 가져오기 : 등록된 이미지 없을경우 .image_active 클래스 추가 -->
        <img src="" alt="" class="image_active">
        <!-- 등록된 이미지 없을경우 .image_active 클래스 제거 -->
        <div class="img_none">Image</div>
    </div>

</div>

<?php endforeach; ?>