<?php foreach ( $list->data as $data ) :?>
    <div class="list post-list" data-listno='<?php echo $data->NOTI_SEQ;?>'>
        <a href="/notice/<?php echo $data->NOTI_SEQ; ?>" class='detailhref'>
            <div class="t_info">
                <div class="notice_title title"><span><?php echo $data->TITLE; ?></span></div>
                <div class="notice_author author"><span><?php echo $data->WRITER_NM; ?></span></div>
                <div class="notice_date date"><span><?php echo $data->ENT_DTTM; ?></span></div>
            </div>

        </a>
    </div>
<?php endforeach ;?>