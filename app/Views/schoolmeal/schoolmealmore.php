<?php if ($list['data']) : ?>
<?php foreach ($list['data'] as $li ) :?>
    <?php 
        $json_seq = json_encode([
            'ACA_ID' => $li['ACA_ID'],
            'MEAL_TP' => $li['MEAL_TP'],
            'MEAL_DT' =>  $li['MEAL_DT']
        ]);
    ?>
    <?php $enc = base64_encode($json_seq)?>
<div class="meal today_meal js-load block">
    <div class="t_info">
        <div class="meal_title title"><span><?php echo DateWithWeekName($li['MEAL_DT']);?></span></div>
        <div class="meal_author author"><span><?php echo $li['TEACHER_NM']; ?></span></div>
        <div class="meal_date date"><span><span><?php echo $li['ENT_DTTM']; ?></span></div>
        <?php if ( $is_teacher == true ): ?>
        <!-- [ 교사앱 : 수정/삭제 버튼 ] -->
        <div class="t_edit_btn btn_box">
            <button type="button" class="edit left" id="mealEditBtn" data-enc="<?php echo $enc; ?>" data-aca_id="<?php echo $li['ACA_ID']?>" data-tp="<?php echo $li['MEAL_TP']?>" data-dt="<?php echo $li['MEAL_DT']?>">수정</button>
            <button type="button" class="del right mealDeleteBtn"  data-enc="<?php echo $enc; ?>">삭제</button>
        </div>
        <?php endif; ?>
    </div>
    <div class="meal_cont">
        <div class="meal_title">
            <p class="title bg_comm">제공급식</p>
            <p class="title_des des"><?php echo $li['MEAL_NM'];?></p>
        </div>
        <div class="meal_list">
            <p class="list_title bg_comm">식단</p>
            <p class="list_des des"><?php echo $li['MEAL_DESC']?></p>
        </div>
        <div class="snack_list">
            <p class="title bg_comm">오전간식</p>
            <p class="title_des des"><?php echo $li['SNACK_DESC']?></p>
        </div>
        <div class="meal_img">
            <!-- [오늘의 급식 이미지 가져오기 : 등록된 이미지 없을 경우 .image_active 클래스 제거 ] -->
            <?php if ( $li['images'] ) :?>
            <?php foreach ( (array)$li['images'] as $image ): ?>
                <?php
                    $file = (object)$image;
                    if ( ! $file->FILE_URL ) {
                        $filepath = $file->FILE_PATH . "/" . $file->FILE_NM . "." . $file->FILE_EXT;
                        $filepath =  str_replace( _ROOT_PATH , '' , $filepath ) ;
                    }else {
                        $filepath = $file->FILE_URL;
                    }

                    $filesrc = str_replace("//","/" , $filepath);
                    $filepath = WRITEPATH . $filesrc;
                    // $file = WRITEPATH . $image['FILE_PATH'] . "/" . $image['FILE_NM'] ."." . $image['FILE_EXT'] ;
                    $thumbnail = getThumbnailPreview($filepath);
                    // $file = str_replace("//","/" , $file);
                    if (!file_exists($filepath)){
                        continue;
                    }
                ?>
                
                <img alt="<?php echo $li['MEAL_NM'];?>" src="<?php echo base_url($thumbnail)?>" class="previewImage previewPhoto-<?php echo $li['MEAL_DT']?>" data-src="<?php echo base_url($filesrc)?>" data-id="<?php echo $li['MEAL_DT']?>" />
                
            <?php endforeach; ?>
            <?php endif;?>
           
            <!-- <div class="img_none">Image</div> -->
        </div>
    </div>
</div>
<?php endforeach;?>
<?php else:?>
    <?php 
        $json_seq = json_encode([
            'ACA_ID' => $ACA_ID,
            'MEAL_TP' => 'B',
            'MEAL_DT' =>  $list['today']
        ]);
    ?>
    <?php $enc = base64_encode($json_seq)?>

    <div class="meal today_meal js-load block">
        <div class="t_info">
            <div class="meal_title title"><span><?php echo DateWithWeekName($list['today']);?></span></div>
            <div class="meal_author author"><span>&nbsp;</span></div>
        <div class="meal_date date"><span><span>&nbsp;</span></div>
            <?php if ( $is_teacher == true ): ?>
            <!-- [ 교사앱 : 수정/삭제 버튼 ] -->
            <div class="t_edit_btn btn_box">
                <button type="button" class="edit left mealWriteBtn" id="mealWriteBtn" data-enc="<?php echo $enc; ?>" data-aca_id="<?php echo $ACA_ID?>" data-tp="B" data-dt="<?php echo $list['today']?>" style="width:50px">둥록</button>
            </div>
            <?php endif; ?>
        </div>
        <div class="meal_cont">
            <p class="noMeal">등록된 급식이 없습니다.</p>
        </div>
    </div>
<?php endif;?>

