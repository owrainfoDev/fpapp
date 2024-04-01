<?php foreach ( $list['data'] as $data ) : ?>
<div class="list js-load block">
    <!-- 해당 공지사항 게시글로 이동 -->
    <a href="<?php echo base_url('/appBoard/' . $data->AB_NO);?>">
        <div class="t_info">
            <div class="notice_title title"><span><?php echo $data->TITLE;?></span></div>
            <div class="notice_author author"><span><?php echo getUserName($data->ENT_USER_ID);?></span></div>
            <div class="notice_date date"><span><?php echo date("Y-m-d", strtotime($data->ENT_DTTM));?></span></div>
        </div>
    </a>
</div>
<?php endforeach ; ?>