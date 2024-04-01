<?php foreach ($data['data'] as $list) : ?>
<div class="list js-load block">
    <a href="<?php echo base_url('/medicine/' . $list->MEDI_REQ_NO );?>">
        <div class="request_info">
            <!-- [ 투약의뢰서 : 아이 이미지 불러오기 ] -->
            <div class="info_img"><img src="<?php echo $list->STD_URL?>"  onError='javascript:this.src="/resources/images/png_human.png"' alt="원생 이미지"></div>
            <div class="request_name name"><span><?php echo $list->STD_NM?></span></div>
            <div class="request_date date"><span><?php echo $list->REQ_DT?></span></div>
            <!--  [투약의뢰서 미확인 : .send class 추가 ] -->
            <?php if ( $list->MEDI_REQ_STATUS == "01") : ?>
                <div class="request_status status send"><span>미확인</span></div>
            <?php elseif ($list->MEDI_REQ_STATUS == "02") : ?>
                <div class="request_status status check"><span>확인</span></div>
            <?php elseif ($list->MEDI_REQ_STATUS == "03") : ?>
                <div class="request_status status done"><span>투약조치</span></div>
            <?php endif; ?>
        </div>
    </a>
</div>
<?php endforeach ;?>