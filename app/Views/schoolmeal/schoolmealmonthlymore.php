<?php 
    $filename = $list->FILE_PATH . "/" . $list->FILE_NAME . "." . $list->FILE_EXT;
    $filenamet = WRITEPATH . substr($filename , 1 , strlen($filename));
?>
<?php 
    $json_seq = json_encode([
        'ACA_ID' => $list->ACA_ID,
        'MEAL_YM' => $list->MEAL_YM,
    ]);
?>
<?php $enc = base64_encode($json_seq)?>
<div class="meal monthly_meal js-load block">
    <div class="t_info">
        <div class="meal_title title"><span><?php echo $list->MEAL_YM;?></span></div>
        <div class="meal_author author"><span><?php echo $list->USER_NM;?></span></div>
        <div class="meal_date date"><span><?php echo substr($list->ENT_DTTM,0,10);?></span></div>
        <?php if ( $is_teacher == true ): ?>
        <!-- [ 교사앱 : 수정/삭제 버튼 ] -->
        <div class="t_edit_btn btn_box">
            <button type="button" class="edit left" id="mealEditBtn" data-enc="<?php echo $enc; ?>">수정</button>
            <button type="button" class="del right mealDeleteBtn"  data-enc="<?php echo $enc; ?>">삭제</button>
        </div>
        <?php endif; ?>
    </div>
    <div class="meal_cont">
        <div class="meal_img">
            <?php if (file_exists($filenamet)) : ?>
                <!-- [ 월간식단표 이미지 가져오기 : 등록된 이미지 없을 경우 .image_active 클래스 제거] -->
                <img src="<?php echo $filename ?>" alt="" class="previewImage previewPhoto-<?php echo $list->MEAL_YM ?>" data-src="<?php echo base_url($filename) ?>">
                <!-- 등록된 이미지 없을 경우 기본이미지 .image_active 클래스 추가 -->
            <?php else : ?>
                <!-- [ 월간식단표 이미지 가져오기 : 등록된 이미지 없을 경우 .image_active 클래스 제거] -->
                <img src="" alt="" class="" class="image_active">
                <!-- 등록된 이미지 없을 경우 기본이미지 .image_active 클래스 추가 -->
                <div class="img_none">Image</div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

