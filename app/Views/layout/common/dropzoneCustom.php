<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
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
	height:40px
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
    // disable autodiscover
    
    Dropzone.autoDiscover = false;
    var pcnt = 0;
    var vcnt = 0;

    var myDropzone = new Dropzone("#dropzone", {
        url: "/fileupload",
        method: "POST",
        paramName: "file",
        autoProcessQueue : false,
        acceptedFiles: "image/*",
        maxFiles: typeof _maxfiles !== "undefined" ? _maxfiles : 30,
        maxFilesize: 300, // MB
        uploadMultiple: typeof _uploadMultiple !== "undefined" ? _uploadMultiple : true,
        parallelUploads: typeof _parallelUploads !== "undefined" ? _parallelUploads : 100 , // use it with uploadMultiple
        
        
        // resizeWidth: "1024" ,
        // resizeMethod: "contain",
        // resizeQuality: "1.0",

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

        // Hookup the start button
        var MAX_WIDTH = 1024;
        var MAX_HEIGHT = 1024;

        var reader = new FileReader();
        // Convert file to img

        reader.addEventListener("load", function(event) {

            var origImg = new Image();
            origImg.src = event.target.result;

            origImg.addEventListener("load", function(event) {

                var width = event.target.width;
                var height = event.target.height;

                // Don't resize if it's small enough
                if (width <= MAX_WIDTH && height <= MAX_HEIGHT) {
                     myDropzone.enqueueFile(file);
                     return;
                }
                // Calc new dims otherwise

                if (width > height) {
                        if (width > MAX_WIDTH) {
                            height *= MAX_WIDTH / width;
                            width = MAX_WIDTH;
                        }
                } else {
                        if (height > MAX_HEIGHT) {
                            width *= MAX_HEIGHT / height;
                            height = MAX_HEIGHT;
                        }
                }
                // Resize
                var canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                var ctx = canvas.getContext("2d");
                ctx.drawImage(origImg, 0, 0, width, height);
                var resizedFile = base64ToFile(canvas.toDataURL(), file);
                // Replace original with resized
                var origFileIndex = myDropzone.files.indexOf(file);
                myDropzone.files[origFileIndex] = resizedFile;
                // Enqueue added file manually making it available for
                // further processing by dropzone
                myDropzone.enqueueFile(resizedFile);
            });
        });
        
        reader.readAsDataURL(file);

        }

    });

    myDropzone.on('accept' , function( file , done){

        // console.log(file.type);
    })

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
                console.log(data);
            });
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
        formData.append("pn", "<?php echo ( isset($header['pn']) && $header['pn'] == '' ? "common" : $header['pn'] ) ?>"); // $_POST["dropzone"]
        // console.log('--전송--')
        // formData.append('')
    });

    myDropzone.on("error", function(file, response) {
        // console.log('--에러--')
        // console.log(response);
        // console.log('--에러--')
    });

    // on success
    myDropzone.on("successmultiple", function(file, response) {
        // get response from successful ajax request
        console.log('--성공--')
        // console.log(response);
        // $('#file').val(JSON.stringify(response));
        content_data.files = response;
        // console.log('--성공--')
        // submit the form after images upload
        // (if u want yo submit rest of the inputs in the form)
		goSubmit();
        
    });

    myDropzone.on('thumbnail' , function(file, response){
        // console.log('--썸네일--')
        // console.log(file)
        // console.log( response );
        
    });

    myDropzone.on('complete' , function(data){
        
    })

    myDropzone.on('success' , function(file , response){
       
        if ( typeof _maxfiles !== "undefined" && _maxfiles == 1 ) {
            // console.log('성공11');
            content_data.files = response;
            goSubmit();
        }
    })
    myDropzone.on("maxfilesexceeded", function (data) {
        this.removeFile(data);
        // myDropzone.addFile(data);
        console.log(myDropzone.options.maxFiles);
        // alert('최대 업로드 파일 수는 '+ myDropzone.options.maxFiles +'개 입니다.');
    });

    myDropzone.on('resetFiles', function() {
        this.removeAllFiles();
    });

    function getFileExtension(fileName){
        return fileName.split('.').pop();
    };

	$('#dropzone').contents()[0].textContent = '';


    function base64ToFile(dataURI, origFile) {
        var byteString, mimestring;

        if(dataURI.split(',')[0].indexOf('base64') !== -1 ) {
            byteString = atob(dataURI.split(',')[1]);
        } else {
            byteString = decodeURI(dataURI.split(',')[1]);
        }

        mimestring = dataURI.split(',')[0].split(':')[1].split(';')[0];

        var content = new Array();
        for (var i = 0; i < byteString.length; i++) {
            content[i] = byteString.charCodeAt(i);
        }

        var newFile = new File(
            [new Uint8Array(content)], origFile.name, {type: mimestring}
        );


        // Copy props set by the dropzone in the original file

        var origProps = [
            "upload", "status", "previewElement", "previewTemplate", "accepted"
        ];

        $.each(origProps, function(i, p) {
            newFile[p] = origFile[p];
        });

        return newFile;
    }

</script>