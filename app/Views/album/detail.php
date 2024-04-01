<?php
    $files = $data['albumFile'];
    $title_photo = null;
    $photos = array();
    if ( isset( $files ) ) {
        $photos = [];
        foreach ($files as $file){
            if ( ! $file->FILE_URL ) {
                $filepath = $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT;
                $filepath =  str_replace( _ROOT_PATH , '' , $filepath ) ;
            }else {
                $filepath = $file->FILE_URL;
            }
            $filepath = WRITEPATH . $filepath;
            
            if (!file_exists($filepath)) continue;
            $filepath = str_replace('//', '/', $filepath);
            $f = new \CodeIgniter\Files\File($filepath);
            $type = $f->getMimeType();
            if ( $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT == "/.") continue;
            
            $photos[] = [
                'link' => $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT,
                'orgfilename' => $file->FILE_ORG_NAME,
                'size' => $file->FILE_SIZE,
                'file_seq' => $file->APND_FILE_SEQ,
                'ext' => $type,
                // 'thumbnail' => $file->THUMBNAIL == "Y" ? $file->FILE_PATH . "/" . $file->FILE_NAME . ".jpg" : $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT
                'thumbnail' => getThumbnailPreview($filepath)
            ];
        }
        // $upload = uploadfileType($photos);
    }
    $is_auth_permit = is_auth_permit($is_teacher , $auth->USER_ID ,$data['album']->ENT_USER_ID );
?>
<link rel="stylesheet" href="/resources/justifiedGallery.min/justifiedGallery.min.css" />
<script src="/resources/justifiedGallery.min/jquery.justifiedGallery.min.js"></script>
<div class="sub_content album_content album_detail">
    <div class="sub_inner">
        <div class="album_detail_list detail_list">
            <div class="detail_cont">
                <div class="t_info">
                    <!-- [ 학부모앱 : 댓글 ] -->
                    <!-- <div class="comment">
                        <i class="icon_icon_comment"></i>
                        <span class="comment_num">2</span>
                    </div> -->
                    <div class="album_title title"><span><?php echo $data['album']->ALBUM_NM;?></span></div>
                    <div class="album_author author"><span><?php echo $data['album']->USER_NM;?></span></div>
                    <div class="album_date date"><span><?php echo substr($data['album']->ENT_DTTM,0,10);?></span></div>
                    <!-- [ 교사앱 : 수정/삭제 버튼 ] -->
                    <?php if ($is_auth_permit == true) : ?>
                    <div class="t_edit_btn btn_box">
                        <button type="button" class="edit left">수정</button>
                        <button type="button" class="del right">삭제</button>
                    </div>
                    <?php endif; ?>

                </div>
                <div class="detail_txt">
                    <p>
                        <?php echo $data['album']->CNTS;?>
                    </p>
                </div>
                <div class="detail_img" id="mygallery">
                    <!-- 앨범 목록에 있는 사진 불러오기 -->
                    <!-- 앨범 목록에 있는 사진 불러오기 -->

                    <?php foreach ( $photos as $photo ) : ?>
                    <a>
                        <img alt="<?php echo $photo['orgfilename']?>" src="<?php echo base_url($photo['link'])?>" class="previewImage previewPhoto-<?php echo $data->AB_NO;?>" data-src="<?php echo base_url($photo['link'])?>" data-id="<?php echo $data->AB_NO;?>" />
                    </a>
                    <?php endforeach;?>

                    <?php /* if ( count($upload['photo']) ) : ?>
                        <div class="_df_thumb" tags="<?php echo count($upload['photo']);?> 사진" id="df_images" thumb="<?php echo $upload['photo'][0]['link'] ?>" >
                            PHOTOS
                        </div>
                    <?php endif; ?>
                    <?php if (is_array( $upload['video'])) :?>
                    <?php foreach ( $upload['video'] as $vid ) : ?>
                        <video width="100%" height="*" controls>
                            <source src="<?php echo $vid['link']?>" type="<?php echo $vid['ext']?>">
                        </video>
                    <?php endforeach ;?>
                    <?php endif; */?>
                    <!-- <img src="../resources/images/note_detail.png" alt="앨범 이미지"> -->
                    <!-- <img src="../resources/images/note_detail.png" alt="앨범 이미지"> -->
                </div>
            </div>
        </div>
        <!-- [ 교사앱 : 삭제 모달 ]  -->
        <!-- <div class="modal">
            <div class="cont">
                <p>삭제하시겠습니까?</p>
                <div class="btn">
                    <button class="cancel">취소</button>
                    <button type="" class="confirm">확인</button>
                </div>
            </div>
        </div> -->
    </div>
</div>


<script type="text/javascript">
var _rowHeight = 450;
    $("#mygallery").justifiedGallery({
                rowHeight: _rowHeight,
                maxRowHeight: 0,
                margins: 1,
                border: 0,
                lastRow: 'left',
                captions: true,
                randomize: false
            });

$(document).on('click' , '.edit' , function(){
    location.href="/album/<?php echo $data['album']->ALBUM_NO; ?>/edit";
})

$(document).on('click' , '.del' , function(){
    Swal.fire({
        title: "앨범",
        text: "현재 앨범을 삭제 하시겠습니까?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "확인",
        cancelButtonText: "취소"

    }).then((result) => {
        if (result.isConfirmed) {
            var content_data = {
                album_no : '<?php echo $data['album']->ALBUM_NO; ?>'
            }

            fetch("/album/proc/deleteProc", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(content_data),
                    })
            .then((response) => response.json())
            .then((data) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'center-center',
                    showConfirmButton: false,
                    timer: 1000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })
                Toast.fire({
                    icon: 'success',
                    title: '알림장이 삭제 되었습니다.'
                }).then(function (result) {
                    if (true) {
                        location.href=data.redirect_to;
                    }
                });
            });
        }
    });
})
</script>

<style>
    .detail_list .detail_txt p{ white-space :unset;}
    .album_content .img_prev ul li:nth-child(n+3)::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(3px);
    left: 0;
    top: 0
    }
</style>