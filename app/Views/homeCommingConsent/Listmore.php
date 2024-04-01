<?php if ( $is_teacher ) : ?>
    <?php foreach ($html['data'] as $list):?>
        <?php $agree = getCodeName('LEAVE_AGREE_STATUS',$list->LEAVE_AGREE_STATUS); ?>
        <?php $profileImage = getStdInfo($list->STD_ID , 'STD_URL');?>
        <?php $profileImage = is_file_flag($profileImage) == true ? $profileImage : '/resources/images/input_bg.png';?>
<div class="list js-load">
    <a href="<?php echo base_url()."homeCommingConsent/" . $list->AGREE_NO;?>">
        <div class="request_info">
            <!-- [ 귀가동의서 : 아이 이미지 불러오기 ] -->
            <div class="info_img"><img src="<?php echo $profileImage;?>" alt="원생 이미지"></div>
            <div class="request_name name"><span><?php echo getUserName($list->STD_ID);?></span></div>
            <div class="request_date date"><span><?php echo date("Y-m-d" , strtotime($list->LEAVE_DT) );?></span></div>
            <!-- [ 투약의뢰서 확인완료 : .done class 추가 ] -->
            <div class="request_status status done<?php echo $agree->CODE;?>"><span><?php echo $agree->CODE_NM;?></span></div>
        </div>
    </a>
</div>
    <?php endforeach; ?>
<?php else :?>
    <?php foreach ($html['data'] as $list):?>
        <?php $agree = getCodeName('LEAVE_AGREE_STATUS',$list->LEAVE_AGREE_STATUS); ?>
    <div class="list js-load block">
        <a href="<?php echo base_url()."homeCommingConsent/" . $list->AGREE_NO;?>">
            <div class="request_info">
                <div class="request_date date"><span><?php echo $list->LEAVE_DT?></span></div>
                <div class="request_txt txt"><span>귀가동의서</span></div>
                <!-- [ 귀가동의서 확인완료 : .done class 추가 ] -->
                <div class="request_status status done<?php echo $agree->CODE;?>"><span><?php echo $agree->CODE_NM;?></span></div>
            </div>
        </a>
    </div>
    <?php endforeach ;?>
<?php endif; ?>

