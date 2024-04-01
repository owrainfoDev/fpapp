<?php foreach ($data['data'] as $d):?>
<div class="list js-load block">
    <a href="/medicine/<?php echo $d->MEDI_REQ_NO;?>">
        <div class="request_info">
            <div class="request_date date"><span><?php echo $d->REQ_DT;?></span></div>
            <div class="request_txt txt"><span>투약의뢰서</span></div>
            <!-- [ 투약의뢰서 투약완료 : .done class 추가 ] -->
            <?php if ( $d->MEDI_REQ_STATUS == '01') : ?>
                <div class="request_status status send"><span>투약의뢰</span></div>
            <?php elseif ($d->MEDI_REQ_STATUS == "02") : ?>
                <div class="request_status status send"><span>의뢰확인</span></div>
            <?php elseif ($d->MEDI_REQ_STATUS == "03") : ?>
                <div class="request_status status done"><span>투약조치</span></div>
            <?php endif;?>
        </div>
    </a>
</div>
<?php endforeach; ?>