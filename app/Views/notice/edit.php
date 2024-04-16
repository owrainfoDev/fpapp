<?php
    $content = $data['detail']['data'];
    $read = $data['detail']['read'];
    $current_class_cd = json_decode( $data['current_students'] );

    $current_std_id = [];
    foreach ( $read as $r ){
        $current_std_id[] = $r->STD_ID;
    }

?>
<div class="sub_content t_write_cont t_content notice_write">
    <div class="sub_inner">
        <form action="/api/notice" id="fileupload" method="POST" enctype="multipart/form-data">
            <!-- <input type="hidden" name="action" value="writeProc"> -->
            <input type="hidden" id="file" name="file" value="">
            <div class="form_cont">
                <div class="class" id="selclass">
                    <!-- 선생님이 맡은 반 -->
                    <select name="selctClass" id="selctClass">
                        <option value="선택">선택</option>
                        <?php foreach ( $class_list as $class ) :?>
                            <option value="<?php echo $class->CLASS_CD;?>" <?php echo $class->CLASS_CD == $content->CLASS_CD ? "selected" : "" ;?>><?php echo $class->CLASS_NM;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="all">
                    <span>전체</span>
                    <input type="checkbox" name="classNotice" id="classNotice">
                    <label for="classNotice"></label>
                </div>
                <div class="class_list write_list" id="stdList">
                    <div class="class_name write_list_name">
                    <?php foreach ( $current_class_cd as $student ) : ?>
                        <div class="name_list">
                            <input type="checkbox" name="STD_ID" id="STD_ID<?php echo $student->STD_ID;?>" value="<?php echo $student->STD_ID;?>" class="_std_id" <?php echo in_array($student->STD_ID , $current_std_id) ? "checked" : ""; ?>>
                            <label for="STD_ID<?php echo $student->STD_ID;?>"></label>
                            <span><?php echo $student->STD_NM;?></span>
                        </div>
                    <?php endforeach ;?>
                    </div>
                </div>
            </div>
            <div class="note_txt">
                <input type="text" name="noteTitle" id="noteTitle" value="<?php echo $content->TITLE;?>" placeholder="제목을 입력해주세요." required>
                <div class="txt_box">
                    <textarea name="noteTxt" id="noteTxt" placeholder="내용을 입력해 주세요." required><?php echo $content->CNTS;?></textarea>
                </div>
            </div>

            

            <div class="form_file">
                <span class="title">파일첨부</span>
                <!-- [ 카메라 앱으로 이동? ] -->
                <div class="camera">
                <span id="phocnt" style="margin-right:5px">사진0</span><span id="vidcnt" style="margin-right:5px">동영상0</span>
                    <i class="icon_image"></i>
                </div>
                
                <div style="width:100%" id="dropzone" class="dropzone"> 파일 첨부 </div>

                <p class="comm">
                    동영상 1개, 사진 100개까지 첨부할 수 있습니다.
                    <span>(동영상 30MB이하, 총 용량 500MB 이하)</span>
                </p>
            </div>

            <div class="btn_box" style="margin-top: 30px;">
                <button type="button" id="cancleDetail">취소</button>
                <button type="button" class="send right">수정</button>
            </div>

        </form>
    </div>
</div>

<script>
    // dropzone Setting
    var _maxfiles = 30;
    // var _uploadMultiple = false;
    // var _parallelUploads = 1;
</script>
<?php echo $this->include('./layout/common/dropzoneCustom');?>

<?php 

    $files = array_merge(
        isset($data['file']['image']) ? $data['file']['image'] : [],
        isset($data['file']['file']) ? $data['file']['file'] : []
    );

    $photos = [];
    if ( isset( $files ) ) {
        
        foreach ($files as $file){

            if ( $file->FILE_URL == '' ||  ! file_exists( substr(WRITEPATH , 0 , -1) . $file->FILE_URL) ) {
                $filepath = $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT;
                $filepath =  str_replace( _ROOT_PATH , '' , $filepath ) ;
                
            }else {
                $filepath = $file->FILE_URL;
            }

            
            $filepath = WRITEPATH . $filepath;

            // if (!file_exists($filepath)) continue;

            // $filepath = str_replace('//', '/', $filepath);
            // $f = new \CodeIgniter\Files\File($filepath);
            // $type = $f->getMimeType();
            if ( $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT == "/.") continue;

            $photos[] = [
                'link' => $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT,
                'orgfilename' => $file->FILE_ORG_NAME,
                'size' => $file->FILE_SIZE,
                'file_seq' => $file->SEQ,
                'ext' => $file->FILE_EXT,
                // 'thumbnail' => $file->THUMBNAIL == "Y" ? $file->FILE_PATH . "/" . $file->FILE_NAME . ".jpg" : $file->FILE_PATH . "/" . $file->FILE_NAME . "." . $file->FILE_EXT
                'thumbnail' => getThumbnailPreview($filepath)
            ];
        }
    }
?>

<script type="text/javascript">
    var images = [
        <?php foreach($photos as $file): ?>
        {name:"<?php echo $file['orgfilename']?>", url: "<?php echo $file['link']?>", size: "<?php echo $file['size']?>", fileSeq: "<?php echo $file['file_seq'] ?>" , tb:"_NOTI_APND_FILE" , thumbnail:"<?php echo $file['thumbnail']?>"},
        <?php endforeach; ?>
    ] 

    for(let i = 0; i < images.length; i++) {

        let img = images[i];
        //console.log(img.url);

        // Create the mock file:
        var mockFile = {name: img.name, size: img.size, url: img.url, seq:img.fileSeq , tb:img.tb};
        // Call the default addedfile event handler
        myDropzone.emit("addedfile", mockFile);
        // And optionally show the thumbnail of the file:
        myDropzone.emit("thumbnail", mockFile, img.thumbnail);
        // Make sure that there is no progress bar, etc...
        myDropzone.emit("complete", mockFile);
        // If you use the maxFiles option, make sure you adjust it to the
        // correct amount:
        var existingFileCount = 1; // The number of files already uploaded
        myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;

    }
</script>

<style>
     .t_content .form_file .camera i { margin-bottom: 5px;}
    .dropzone .dz-preview.dz-image-preview , .dropzone { background-color: #F1F1F5}
</style>

<script>
    var editor ;

     editor = new FroalaEditor("#noteTxt", {
	        'key': '5OA4gF4D3I3G3B6C4D-13TMIBDIa2NTMNZFFPFZe2a1Id1f1I1fA8D6C4F4G3H3I2A18A15A6=='
	        ,'height': 290
            ,'attribution': false
	        // , imageUploadParam: 'uploadImg'

	        // // Set the image upload URL.
	        // , imageUploadURL: '/app/manage/noticeMng/editorImgUpload'
	        // // Additional upload params.
	        // , imageUploadParams: {id: 'contents'}
	        // // Set request type.
	        // , imageUploadMethod: 'POST'
	        // // Set max image size to 5MB.
	        // , imageMaxSize: 20 * 1024 * 1024
	        // // Allow to upload PNG and JPG.
	        // , imageAllowedTypes: ['jpeg', 'jpg', 'png']
		});
    
</script>

<script type="text/javascript">
  

    function goSubmit(){
        var forms = $('form#fileupload').serializeObject();
        forms.action = 'editProc';
        forms.ACA_ID = '<?php echo $aca_id;?>';
        forms.USER_ID = '<?php echo $user_id;?>';
        forms.is_teacher = '<?php echo $is_teacher;?>'
        forms.files = dropzonefiles;
        forms.noti_seq = '<?php echo $content->NOTI_SEQ;?>';

        loadingShowHide();

        fetch("/api/notice", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(forms),
                })
        .then((response) => response.json())
        .then((data) => {
            loadingShowHide();
            if ( data.status == 'success'){
                
                if ( typeof tempSave == "object" )  tempSave.delete(); // 임시 저장 삭제

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'center-center',
                    showConfirmButton: false,
                    timer: 500,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })
                Toast.fire({
                    icon: 'success',
                    title: '알림장이 수정 되었습니다.'
                }).then(function (result) {
                    if (true) {
                        location.href="/notice"
                    }
                });

                
            } else {
                loadingShowHide();
                Swal.fire("수정에 실패하였습니다.");
                location.reload();
            }
        });
    }
</script>


<script type="text/javascript">

$(document).ready(function(){
    // 반선택
    $(document).on('change' , '#selctClass', function(){
        let class_cd = $(this).val() ;
        let param = {
            class_cd : class_cd
        };

        var stdjson = JSON.parse( '<?php echo json_encode($current_std_id);?>' );
        var checked = '';
        var current_content_class_cd = '<?php echo $content->CLASS_CD ;?>';

        $.ajax({
            type : 'post',
            url : "/notice/proc/getStudentfromclasscd",
            async : true,
            dataType : 'json',
            data : param,
            beforeSend : function(){
                loadingShowHide();
            },
            success : function(response)  {
                loadingShowHide();
                let html = '';
                if (response.length > 0) {
                    
                     html = '<div class="class_name write_list_name">';
                    $.each( response , function ( key , item){
                        
                        if ( class_cd == current_content_class_cd ) {
                            if ( $.inArray(item.STD_ID , stdjson ) < 0 ) checked = '';
                            else checked = 'checked';
                        } else checked = '';
                        

                        html += `<div class="name_list">
                            <input type="checkbox" name="STD_ID" id="STD_ID${item.STD_ID}" value="${item.STD_ID}" class="_std_id" ${checked}>
                            <label for="STD_ID${item.STD_ID}"></label>
                            <span>${item.STD_NM}</span>
                        </div>`;
                    })
                    html += "</div>";
                } else {
                    html = "<span>선택된 학생이 없습니다.</span>";
                }
                $('#stdList').html(html);
            },
            error : function(request, status, error){
                console.log(error);
            }
        })
    });

    $(document).on('click' , '#classNotice' , function(){
        
        if ( $('#classNotice').prop('checked')){
            $("input[name*=STD_ID]").prop('checked' , true);
        } else {
            $("input[name*=STD_ID]").prop('checked' , false);
        }
    });

    $(document).on('click' , "input[name*=STD_ID]" , function(){
        if ( $("input[name*=STD_ID]").length == $("input[name*=STD_ID]:checked").length ) {
            $('#classNotice').prop('checked', true);
        } else {
            $('#classNotice').prop('checked', false);
        }
    })

    $(document).on('click', '.btn_box .send' , function(e){
        
        var forms = $('form#fileupload');
        var alertTitle = "알림장";
        var alerticon = "warning";

        if ( $("input[name='STD_ID']:checked").length < 1){
            Swal.fire({
                title: alertTitle,
                text: "학생을 선택하여 주십시요",
                icon: alerticon
            });
            $('#noteTxt').focus();
            return false;
        }

        if ( $('#noteTitle').val() == ''){
            Swal.fire({
                title: alertTitle,
                text: "제목을 입력하여 주십시요",
                icon: alerticon
            });
            $('#noteTitle').focus();
            return false;
        }

        if ( $('#noteTxt').val() == ''){
            Swal.fire({
                title: alertTitle,
                text: "내용을 입력하여 주십시요",
                icon: alerticon
            });
            $('#noteTxt').focus();
            return false;
        }

        
        Swal.fire({ 
            text : "수정하시겠습니까?" , icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "확인",
            cancelButtonText:"취소"
        }).then((result) => {
            if (result.isConfirmed) {
                e.preventDefault();
                e.stopPropagation();
                loadingShowHide();        

                if (myDropzone.files != "") {
                    // console.log(myDropzone.files);
                    
                    myDropzone.processQueue();

                } else {
                // if no file submit the form    
                    goSubmit();
                }
            } else {
                // loadingShowHide();
            }
        });
    })

    $(document).on('click', '#cancleDetail', function(){
        $('#viewList').hide();
        $('.mode_view').hide();
        $('#viewForm').empty();
        $('#viewDetail').show();
    })
});
</script>

<script type="text/javascript">
var tempSave;

var $btn = $('#tempSaveBtn');
var SessionData = '';
if ($('#tempSaveBtn').length > 0) {
    tempSave = {
        init: async function () {
            SessionData = this.data();
            if (SessionData == "") return;
            let name = SessionData.name;
            let loadData = '';
            loadData = await this.get(name);
            if (loadData == null) return;

            let j = JSON.parse(loadData.TEMP_VALUE);
            let now = new Date();
            let currentTime = now.getTime();

            if (currentTime > j.expireTime) {
                this.delete();
                return;
            }

            let value = j.value;
            this.loadData(value);
        },

        set: function (name, value) {
            let now = new Date();
            let item = {
                value: value,
                expireTime: now.getTime() + 7200000,
            };
            let data = {
                TEMP_KEY: name,
                TEMP_VALUE: item,
            }
            fetch("/api/ajax/tempSave_save", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                .then((response) => response.json())
                .then((data) => {
                    console.log('임시저장 성공');
                });
        },
        get: async function (name) {
            let data = {
                TEMP_KEY: name
            }
            const response = await fetch("/api/ajax/tempSave_get", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
            const result = await response.json();
            // console.log(result);
            return result;
        },
        data: function () {
            let j = $('#tempSaveBtn').data('target-area');
            return j;
        },
        loadData: function (data) {
            Swal.fire({
                text: "임시저장 내용을 불러옵니다.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "확인",
                cancelButtonText: "취소"
            }).then((result) => {
                if (result.isConfirmed) {
                    let json = JSON.parse(data);
                    $.each(json, function (i, k) {

                        if ( i == 'noteTxt'){
                            editor.html.set(k);
                        } else {
                            $('#' + i).val(k);
                        }

                       
                    })
                }
            })
        },
        delete: function () {
            let data = {
                TEMP_KEY: SessionData.name,
            }
            console.log(data);
            fetch("/api/ajax/tempSave_remove", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                .then((response) => response.json())
                .then((data) => {
                    console.log(data);
                });

        }

    };
    tempSave.init();

    // $('#tempSaveBtn').on('click', function () {
    $(document).on('click', '#tempSaveBtn', function(){
        Swal.fire({
            text: "해당내용을 임시 저장 하시겠습니까?",
            // icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#00341E",
            cancelButtonColor: "#dddd",
            confirmButtonText: "확인",
            cancelButtonText: "취소"
        }).then((result) => {
            if (result.isConfirmed) {
                var data = tempSave.data();
                let name = data.name;
                let value = {};
                $.each(data.target, function (i, v) {
                    value[v] = $('#' + v).val();
                });
                tempSave.set(name, JSON.stringify(value));
            }
        });
    })
}

</script>