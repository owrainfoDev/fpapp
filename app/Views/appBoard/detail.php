<?php 
    $photos = [];
    if ( isset( $files ) ) {
        
        foreach ($files as $file){
            if ( ! $file->FILE_URL ) {
                $filepath = $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT;
                $filepath =  str_replace( _ROOT_PATH , '' , $filepath ) ;
            }else {
                $filepath = $file->FILE_URL;
            }

            $filepath = WRITEPATH . $filepath;
            
            // echo $filepath;
            if (!file_exists($filepath)) continue;
            $filepath = str_replace('//', '/', $filepath);
            $f = new \CodeIgniter\Files\File($filepath);
            $type = $f->getMimeType();
            if ( $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT == "/.") continue;

            $photos[] = [
                'link' => $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT,
                'orgfilename' => $file->FILE_ORG_NAME,
                'size' => $file->FILE_SIZE,
                'file_seq' => $file->SEQ,
                'ext' => $type,
                // 'thumbnail' => $file->THUMBNAIL == "Y" ? $file->FILE_PATH . "/" . $file->FILE_NAME . ".jpg" : $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT
                'thumbnail' => getThumbnailPreview($filepath)
            ];

        }
        
    }
?>
<link rel="stylesheet" href="/resources/justifiedGallery.min/justifiedGallery.min.css" />
<script src="/resources/justifiedGallery.min/jquery.justifiedGallery.min.js"></script>


<div class="sub_content notice_content notice_detail">
    <div class="sub_inner">
        <div class="notice_detail_list detail_list">
            <div class="detail_cont">
                <div class="t_info">
                    <div class="notice_title title"><span><?php echo $data->TITLE;?></span></div>
                    <div class="notice_author author"><span><?php echo getUserName($data->ENT_USER_ID);?></span></div>
                    <div class="notice_date date"><span><?php echo date("Y-m-d" , strtotime($data->ENT_DTTM) );?></span></div>
                </div>
                <div class="detail_img" id="mygallery">
                    <!-- 앨범 목록에 있는 사진 불러오기 -->

                    <?php foreach ( $photos as $photo ) : ?>
                    <a>
                        <img alt="<?php echo $photo['orgfilename']?>" src="<?php echo base_url($photo['thumbnail'])?>" class="previewImage previewPhoto-<?php echo $data->AB_NO;?>" data-src="<?php echo base_url($photo['link'])?>" data-id="<?php echo $data->AB_NO;?>" />
                    </a>
                    <?php endforeach; ?>
                </div>
                <div class="detail_txt">
                    <p><?php echo $data->CONTENTS;?> </p>
                </div>
            </div>
            <!-- [ 교사앱 : 교사앱에서만 보이기 - 알림장 수정/삭제 ] -->
            <?php if ( $is_teacher && $data->ENT_USER_ID == $user_id ) :?>
            <div class="btn_box" style="margin-top: auto; ">
                <button type="button" class="edit left">수정</button>
                <button type="button" class="del right">삭제</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
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
        location.href="<?php echo base_url("/appBoard/" . $data->AB_NO . "/edit" );?>";
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
                    AB_NO : '<?php echo $data->AB_NO; ?>',
                    <?php echo csrf_token();?> : "<?php echo csrf_hash() ?>"
                }

                fetch("/appBoard/proc/deleteProc", {
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
                        title: '삭제 되었습니다.'
                    }).then(function (result) {
                        if (true) {
                            if ( data.redirect_to == 'reload' ){
                                location.reload();
                            } else {
                                location.href=data.redirect_to;
                            }
                            
                        }
                    });
                });
            }
        });
    })
</script>