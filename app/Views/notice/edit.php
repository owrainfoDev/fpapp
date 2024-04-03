<?php
    $detail = $data['detail']['data'];
    $files = $data['file'];
    $read = $data['detail']['read'];
    $noti_seq = $data['noti_seq'];

    
    $editfile = array_merge(
            isset($data['file']['image']) ? $data['file']['image'] : [],
            isset($data['file']['file']) ? $data['file']['file'] : []
    );

    $title_photo = null;
    $photos = array();
    if ( isset( $files['image'] ) ) {
        $title_photo = $files['image'][0]->FILE_PATH . "/" . $files['image'][0]->FILE_NM . "." . $files['image'][0]->FILE_EXT;

        $photos = [];
        foreach ($files['image'] as $file){
            $photos[] = [
                'link' => $file->FILE_PATH . "/" . $file->FILE_NM . "." . $file->FILE_EXT,
                'orgfilename' => $file->ORIGIN_FILE_NM,
                'size' => $file->FILE_SIZE,
                'file_seq' => $file->APND_FILE_SEQ
            ];
        }
    }

    $editfiles = [];
    if (isset($editfile) ){
        foreach ($editfile as $file){
            $filepath = WRITEPATH . $file->FILE_PATH . "/" . $file->FILE_NM . "." . $file->FILE_EXT;
            $filepath = str_replace('//','/',$filepath);
            $thumbnail = getThumbnailPreview($filepath);
            $editfiles[] = [
                'link' => $file->FILE_PATH . "/" . $file->FILE_NM . "." . $file->FILE_EXT,
                'orgfilename' => $file->ORIGIN_FILE_NM,
                'size' => $file->FILE_SIZE,
                'file_seq' => $file->APND_FILE_SEQ,
                'thumbnail' => $thumbnail
            ];
        }
    }

    $ii = $data['file']['image'] ;
    $i1 = 0;
    $i2 = 0;
    if (! empty($ii)) {
  
        foreach ( $ii as $is){
            if ( in_array( $is->FILE_EXT ,array( 'jpg', 'jpeg' , 'gif' , 'png' , 'bmp' , 'webp' ) ) ) $i1++;
            if ( in_array( $is->FILE_EXT ,array( 'mp4' ) ) ) $i2++;
        }
    }

?>

<link href="https://cdn.jsdelivr.net/npm/froala-editor@3.1.0/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@3.1.0/js/froala_editor.pkgd.min.js"></script>

<script type="text/javascript">
function LoadContentTemplate(){
    content_data.action = 'getwrite';
    content_data.noti_seq = '';
    content_data.year = '2023';
    console.log(JSON.stringify(content_data));
    fetch("/api/notice", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(content_data),
            })
    .then((response) => response.json())
    .then((data) => {
        // console.log(JSON.stringify(data));
        var template = _.template($('#selclassTemplate').html());
        var result = template( { classList: data.classList } );
        $("#selclass").html( result );
        return data;
    }).then(data => {
    
    <?php if ( $detail->CLASS_CD ) : ?>
        
        $('#selctClass').val('<?php echo $detail->CLASS_CD; ?>')
        $('#selctClass').trigger('change');
        // getStudentList('<?php echo $detail->CLASS_CD; ?>');
    <?php endif; ?>
    });
}
function getStudentList(class_cd){
    content_data.action = 'getStudentfromclasscd';
    content_data.class_cd = class_cd;
    fetch("/api/notice", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(content_data),
            })
    .then((response) => response.json())
    .then((data) => {
        var template = _.template($('#stdListTtemplate').html());
        var result = template( { studentList: data } );
        $("#stdList").html( result );
        return data;
    }).then(data => {
        <?php foreach( $read as $r ) : ?>
            $('#STD_ID<?php echo $r->STD_ID; ?>').prop('checked' , true);
        <?php endforeach ;?>
        if ( $("input[name*=STD_ID]").length == $("input[name*=STD_ID]:checked").length ) {
            $('#classNotice').prop('checked', true);
        } else {
            $('#classNotice').prop('checked', false);
        }
    });
}

$(document).ready(function(){
    
    $(document).on('change' , '#selctClass', function(){
        var class_cd = $(this).val() ;
        getStudentList(class_cd);
    });

    $(document).on('click' , '#classNotice' , function(){
        
        if ( $('#classNotice').prop('checked')){
            $("input[name*=STD_ID]").prop('checked' , true);
        } else {
            $("input[name*=STD_ID]").prop('checked' , false);
        }
    });

    $(document).on('click' , "input[name*=STD_ID]" , function(){
            console.log( $("input[name*=STD_ID]").length )
            console.log( $("input[name*=STD_ID]:checked").length )
        if ( $("input[name*=STD_ID]").length == $("input[name*=STD_ID]:checked").length ) {
            $('#classNotice').prop('checked', true);
        } else {
            $('#classNotice').prop('checked', false);
        }
    })

    
});
</script>
<!-- 선생님이 맡은 반 -->
<script type="text/template" id="selclassTemplate">
    <select name="selctClass" id="selctClass" disabled>
    <option value="선택">선택</option>
<% _.each(classList,function(item,key,list) { %>
    <option value="<%= item.CLASS_CD %>"><%= item.CLASS_NM %></option>
<% }) %>
    </select>
</script>
<script type="text/template" id="stdListTtemplate">
<div class="class_name write_list_name">
    <!-- 반별 리스트 -->
    <% _.each(studentList , function( item , key, list ) { %>
    <div class="name_list">
        <input type="checkbox" name="STD_ID" id="STD_ID<%= item.STD_ID %>" value="<%= item.STD_ID %>" class="_std_id" disabled>
        <label for="STD_ID<%= item.STD_ID %>"></label>
        <span><%= item.STD_NM %></span>
    </div>
    <% }) %>
    <% if (studentList.length < 1 ) {%>
        <span>선택된 학생이 없습니다.</span>
    <% } %>
</div>
</script>
<!-- //content -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<div class="sub_content t_write_cont t_content notice_write">
    <div class="sub_inner">
        <form action="/api/notice" id="fileupload" method="POST" enctype="multipart/form-data">
            <!-- <input type="hidden" name="action" value="writeProc"> -->
            <input type="hidden" id="file" name="file" value="">
            <div class="form_cont">
                <div class="class" id="selclass">

                </div>
                <div class="all">
                    <span>전체</span>
                    <input type="checkbox" name="classNotice" id="classNotice" disabled>
                    <label for="classNotice"></label>
                </div>
                <div class="class_list write_list" id="stdList">
                    
                </div>
            </div>
            <div class="note_txt">
                <input type="text" name="noteTitle" id="noteTitle" placeholder="제목을 입력해주세요." required value="<?php echo $detail->TITLE ;?>">
                <div class="txt_box">
                    <?php 
                        $content = $detail->CNTS;
                        $content = str_replace("<br />" , "\r\n" , $content);
                    ?>

                    <textarea id="noteTxt" name="noteTxt"><?php echo $detail->CNTS;?></textarea>
                    
                </div>
            </div>

            <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
            <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

            <div class="form_file">
                <span class="title">파일첨부</span>
                <!-- <input type="file" name="files[]" id="form_img" accept="image/*" multiple>
                <!-- <span class="place">Image</span> ->
                <div class="form-img-remove del_img" onClick=""><i></i></div>
                <div class="form-img-section" style="display: none;">
                    <img class="form-img-preview" src="#" />
                </div> -->
               
                
                <!-- [ 카메라 앱으로 이동? ] -->
                <div class="camera">
                    <span id="phocnt" style="margin-right:5px">사진<?php echo $i1;?></span><span id="vidcnt" style="margin-right:5px">동영상<?php echo $i2;?></span>
                    <i class="icon_image"></i>
                </div>
                
                <div style="width:100%" id="dropzone" class="dropzone"> 파일 첨부 </div>

                <p class="comm">
                    동영상 1개, 사진 100개까지 첨부할 수 있습니다.
                    <span>(동영상 30MB이하, 총 용량 500MB 이하)</span>
                </p>
            </div>

            <div class="btn_box" style="margin-top: 30px;">
                <!-- <button type="button" class="save left" id="tempsave">임시저장</button> -->
                <button type="button" class="send right" id="editBtn">수정</button>
            </div>
            
        </form>
    </div>
</div>


<style>
label.error
{
    color:red;
    font-family:verdana, Helvetica;
    font-size:11px;
}
/* textarea.error input.error { border-color: red;} */
.dropzone{
	border:none;
	display:flex;
	flex-wrap:wrap;
	margin-top:10px;
}

.dropzone.dz-clickable{
	min-height: auto;
    	margin: 0;
    	display: flex;
}

.dropzone .dz-preview{
	margin:0;
	min-height:auto;
}

.dropzone .dz-preview .dz-progress{
	display:none;

}

.dropzone .dz-preview .dz-details{
	display:none;
}

.dropzone .dz-preview .dz-image{
	border-radius:0;
	width:50px;
	height:40px;
	margin: 0 10px;
}

.dropzone .dz-preview .dz-image img{
	width:100%;
}

.dropzone .dz-preview .dz-remove{
	font-size:0;
	position: absolute;
    	bottom: 0;
    	z-index: 11;
    	right: 0px;
	background:url(/resources/images/icon/icon_colse_img.png) no-repeat center / 100%;
	width: 15px;
    	height: 15px;
}
.dropzone .dz-preview .dz-image img {
    border: 1px solid #EDEDED;
    margin: 2px;
    border-radius: 15%;
}

.dropzone .dz-preview .dz-error-mark{
    display:none;
}
</style>

<script>
$(function () {
    var editor = new FroalaEditor("#noteTxt", {
	        'key': '5OA4gF4D3I3G3B6C4D-13TMIBDIa2NTMNZFFPFZe2a1Id1f1I1fA8D6C4F4G3H3I2A18A15A6=='
	        ,'height': 290
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

});

</script>

<script>
    // dropzone Setting
    var _maxfiles = 30;
    // var _uploadMultiple = false;
    // var _parallelUploads = 1;
</script>


<script>
    function getFileExtension(fileName){
        return fileName.split('.').pop();
    };
    var pcnt = <?php echo $i1;?>;
    var vcnt = <?php echo $i2;?>;
    // disable autodiscover
    // https://gist.github.com/kreativan/83febc214d923eea34cc6f557e89f26c
    Dropzone.autoDiscover = false;

    var myDropzone = new Dropzone("#dropzone", {
        url: "/fileupload",
        method: "POST",
        paramName: "file",
        autoProcessQueue : false,
        acceptedFiles: "image/*",
        maxFiles: typeof _maxfiles !== "undefined" ? _maxfiles : 30,
        maxFilesize: 30, // MB
        uploadMultiple: typeof _uploadMultiple !== "undefined" ? _uploadMultiple : true,
        parallelUploads: typeof _parallelUploads !== "undefined" ? _parallelUploads : 100 , // use it with uploadMultiple
        createImageThumbnails: true,
        thumbnailWidth: typeof _thumbnailWidth !== "undefined" ? _thumbnailWidth : 50 , // use it with uploadMultiple 
        thumbnailHeight: typeof _thumbnailHeight !== "undefined" ? _thumbnailHeight : 40 , // use it with uploadMultiple 
        addRemoveLinks: true,
        timeout: 180000,
        dictRemoveFileConfirmation: "삭제하시겠습니까?", // ask before removing file
        // acceptedFiles : "image/*,application/pdf,.doc,.docx,.xls,.xlsx,.csv,.tsv,.ppt,.pptx,.pages,.odt,.rtf,.zip,.tar",
        acceptedFiles : ".jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF,.MP4,.mp4,.pdf",
        // Language Strings
        // dictFileTooBig: "File is to big ({{filesize}}mb). Max allowed file size is {{maxFilesize}}mb",
        dictFileTooBig: "파일 크기가 너무 큽니다({{filesize}}mb). 최대 허용 파일 크기는 {{maxFilesize}}mb입니다.",
        dictInvalidFileType: "업로드 가능한 파일이 아닙니다.",
        dictCancelUpload: "취소",
        dictRemoveFile: "삭제",
        dictMaxFilesExceeded: "{{maxFiles}}개 파일까지 업로드 가능합니다.",
        dictDefaultMessage: " ",
    });
    myDropzone.on("addedfile", function(file) {
        // console.log('--추가--')
        // console.log(file);
        if (file.url){
            
        } else {
            
            let ext = getFileExtension(file.upload.filename);
            let accept_ext = this.options.acceptedFiles;
            
            let pos = accept_ext.indexOf(ext);
            
            if ( pos < 0 ){
                Swal.fire({ text : "해당파일은 업로드 하실 수 없습니다." , icon: "question" });
                this.removeFile(file);        
            } else {
                
                let videoext = '.mp4,.MP4';
                if (videoext.indexOf(ext) > 0){
                    vcnt++;
                    if ( document.getElementById('vidcnt') ) {
                        document.getElementById('vidcnt').innerHTML = '동영상' + vcnt;
                    }
                } else {
                    pcnt++;
                    if ( document.getElementById('phocnt') ) {
                        document.getElementById('phocnt').innerHTML = '사진' + pcnt;
                    }
                }
                
            }
        }
        
        
    });

    myDropzone.on("removedfile", function(file) {

        if (file.url){
            
            
            fetch("/removeFile", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(file),
                    })
            .then((response) => response.json())
            .then((data) => {
                

            });

            let ext = getFileExtension(file.url);
            let videoext = '.mp4,.MP4';
            if (videoext.indexOf(ext) > 0){
                vcnt--;
                if ( document.getElementById('vidcnt') ) {
                    document.getElementById('vidcnt').innerHTML = '동영상' + vcnt;
                }
            } else {
                pcnt--;
                if ( document.getElementById('phocnt') ) {
                document.getElementById('phocnt').innerHTML = '사진' + pcnt;
                }
            }

        } else {
            let ext = getFileExtension(file.upload.filename);
            let videoext = '.mp4,.MP4';
            if (videoext.indexOf(ext) > 0){
                vcnt--;
                if ( document.getElementById('vidcnt') ) {
                    document.getElementById('vidcnt').innerHTML = '동영상' + vcnt;
                }
            } else {
                pcnt--;
                if ( document.getElementById('phocnt') ) {
                document.getElementById('phocnt').innerHTML = '사진' + pcnt;
                }
            }
        }

    });

    // Add mmore data to send along with the file as POST data. (optional)
    myDropzone.on("sending", function(file, xhr, formData) {
        // console.log('--전송--')
        // formData.append("dropzone", "1"); // $_POST["dropzone"]
        // console.log('--전송--')
        formData.append("pn", "<?php echo ( isset($header['pn']) && $header['pn'] == '' ? "common" : $header['pn'] ) ?>"); // $_POST["dropzone"]
    });

    myDropzone.on("error", function(file, response) {
        // console.log('--에러--')
        // console.log(response);
        // console.log('--에러--')
    });

    // on success
    myDropzone.on("successmultiple", function(file, response) {
        // get response from successful ajax request
        // console.log('--성공--')
        // console.log(response);
        // $('#file').val(JSON.stringify(response));
        content_data.files = response;
        // console.log('--성공--')
        // submit the form after images upload
        // (if u want yo submit rest of the inputs in the form)
        checkvalid();
    });

    myDropzone.on('thumbnail' , function(file, response){
        // console.log('--썸네일--')
        // console.log(file)
        // console.log( response );
        
    });



    var images = [
        <?php foreach($editfiles as $file): ?>
        {name:"<?php echo $file['orgfilename']?>", url: "<?php echo $file['link']?>", size: "<?php echo $file['size']?>", fileSeq: "<?php echo $file['file_seq'] ?>" , tb:"_NOTI_APND_FILE", thumbnail:"<?php echo $file['thumbnail']?>"},
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

    $('#dropzone').contents()[0].textContent = '';
    // button trigger for processingQueue
    // var submitDropzone = document.getElementById("submit-dropzone");
    // submitDropzone.addEventListener("click", function(e) {
    //     // Make sure that the form isn't actually being sent.
    //     e.preventDefault();
    //     e.stopPropagation();

    //     // if ( checkvalid() ) {
    //     //     return ;
    //     // }

    //     if (myDropzone.files != "") {
    //         // console.log(myDropzone.files);
    //         myDropzone.processQueue();
    //     } else {
    //     // if no file submit the form    
    //         $("#fileupload").submit();
    //     }

    //     return false;

    // });


    
    $("#fileupload").validate({
        rules: {
            noteTitle: "required"
        },
        messages: {
            noteTitle: "제목을 입력해 주세요"
        },
        submitHandler: function(form) {
            // return false;
            Swal.fire({
                title: "알림장",
                text: "알림장을 수정하시겠습니까?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "확인",
                cancelButtonText: "취소",
            }).then((result) => {
                if (result.isConfirmed) {

                    

                    if (myDropzone.files != "") {
                        myDropzone.processQueue();
                    } else {
                        checkvalid();
                    }
                    
                    // checkvalid();
                } else {    // 취소
                    return false;
                    
                }
            });

            // if (myDropzone.files != "") {
            //     // console.log(myDropzone.files);
            //     myDropzone.processQueue();
            // } else {
            // // if no file submit the form    
            //     $("#fileupload").submit();
            // }

            // // console.log(content_data);
            // checkvalid();
            return false;
        }
    });

    $('#dropzone').contents()[0].textContent = ''

    $(document).on('click', '#editBtn' , function(){
        $("form#fileupload").submit();
    });
    

 

    

    function checkvalid(){
        
        var forms = $('form#fileupload').serializeObject();
        forms.action = 'editProc';
        forms.USER_ID = content_data.USER_ID;
        forms.is_teacher = content_data.is_teacher;
        forms.noti_seq = '<?php echo $noti_seq;?>';
        forms.files = content_data.files;
        
        fetch("/api/notice", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(forms),
                })
        .then((response) => response.json())
        .then((data) => {
            if ( data.status == 'success'){

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'center-center',
                    showConfirmButton: false,
                    timer: 3000,
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
                        location.href=data.redirect_to;
                    }
                });

                
            } else {
                Swal.fire("등록에 실패하였습니다.");
                location.reload();
            }
        });
    }

</script>