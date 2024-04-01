<?php 
$photos = array();

if ( isset( $file['data'] ) ) {
    $title_photo = $file['data'][0]->FILE_PATH . "/" . $file['data'][0]->FILE_NAME . "." . $file['data'][0]->FILE_EXT;
    
    $photos = [];
    foreach ($file['data'] as $file){
        if ( ! $file->FILE_URL ) {
            $filepath = $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT;
            $filepath =  str_replace( _ROOT_PATH , '' , $filepath ) ;
        }else {
            $filepath = $file->FILE_URL;
        }

        $fileurl = $filepath;
        $filepath = WRITEPATH . $filepath;



        $filepath = str_replace('//', '/', $filepath);

        if ( file_exists($filepath) ) {
            $photos[] = [
                'link' => $fileurl,
                'orgfilename' => $file->FILE_ORG_NAME,
                'size' => $file->FILE_SIZE,
                'file_seq' => $file->ALBUM_FILE_SEQ,
                'ext' => $file->FILE_EXT,
                'thumbnail' => getThumbnailPreview($filepath)
            ];
        }
    }

    if (is_array($photos)){
        $title_photo = $photos[0]['link'];
    }
}
?>

<div class="list js-load block">
    <!-- 해당 앨범 게시글로 이동 -->
    <a href="/album/<?php echo $list->ALBUM_NO?>">
        <div class="t_info">
            <div class="comment">
                <!-- <i class="icon_icon_commnet"></i> -->
                <span class="comment_num"><?php echo $is_teacher == false ? '': $list->VIEW_CNT;?></span>
            </div>
            <div class="album_title title"><span><?php echo $list->ALBUM_NM;?></span></div>
            <div class="album_author author"><span><?php echo $list->USER_NM;?></span></div>
            <div class="album_date date"><span><?php echo $list->ENT_DTTM;?></span></div>
        </div>
        <div class="list_txt">
            <p class="detail_prev"><?php echo $list->CNTS; ?></p>
        </div>
        <div class="img_prev">
            <ul>
                <!-- [ 앨범이미지 가져오기 : 등록된 이미지 없을 경우 li에 .afterNone class 추가 ] -->
                <?php $j = 0;
                    foreach ($photos as $i => $f):?>
                    <?php if ( strpos( $f['ext'] , 'video') !== false ) : ?><?php continue; ?><?php endif;?>
                <li>
                    <img src="<?php echo $f['link']; ?>" alt="앨범이미지">
                    <?php if ( $j == 2 ) : ?>
                        <span>더보기<i class="icon_plus_w"></i></span>
                        <?php break;?>
                    <?php endif;?>
                </li>
                <?php $j++;
                    endforeach; ?>
                <!-- <li>
                    <img src="../resources/images/note_detail.png" alt="앨범이미지">
                </li>
                <li>
                    <img src="../resources/images/note_detail.png" alt="앨범이미지">
                    <span>더보기<i class="icon_plus_w"></i></span>
                </li> -->
            </ul>
        </div>
    </a>
</div>