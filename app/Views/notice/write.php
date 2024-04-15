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
                            <option value="<?php echo $class->CLASS_CD;?>"><?php echo $class->CLASS_NM;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="all">
                    <span>전체</span>
                    <input type="checkbox" name="classNotice" id="classNotice">
                    <label for="classNotice"></label>
                </div>
                <div class="class_list write_list" id="stdList">
                    
                    
                </div>
            </div>
            <div class="note_txt">
                <input type="text" name="noteTitle" id="noteTitle" placeholder="제목을 입력해주세요." required>
                <div class="txt_box">
                    <textarea name="noteTxt" id="noteTxt" placeholder="내용을 입력해 주세요." required></textarea>
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
                <button type="button" id="tempSaveBtn" data-target-area='{"name":"<?php echo $header['pn']; ?>","target":["noteTitle","noteTxt"]}' class="save left">임시저장</button>
                <button type="button" class="send right">보내기</button>
            </div>
            <!-- [ 교사앱 : 모달 ]  -->
            <div class="modal">
                <div class="cont">
                    <p>등록하시겠습니까?</p>
                    <div class="btn">
                        <button type="button" class="cancel">취소</button>
                        <button type="submit" class="confirm" id="submit-dropzone">확인</button>
                    </div>
                </div>
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

<style>
     .t_content .form_file .camera i { margin-bottom: 5px;}
    .dropzone .dz-preview.dz-image-preview , .dropzone { background-color: #F1F1F5}
</style>

<script>
    var editor ;

     editor = new FroalaEditor("#noteTxt", {
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
    
</script>

<script type="text/javascript">

    // button trigger for processingQueue
    var submitDropzone = document.getElementById("submit-dropzone");
    submitDropzone.addEventListener("click", function(e) {
        // Make sure that the form isn't actually being sent.
        e.preventDefault();
        e.stopPropagation();

        // if ( checkvalid() ) {
        //     return ;
        // }

        if (myDropzone.files != "") {
            // console.log(myDropzone.files);
            myDropzone.processQueue();
        } else {
        // if no file submit the form    
            $("#fileupload").submit();
        }

        return false;

    });
   

    function goSubmit(){
        var forms = $('form#fileupload').serializeObject();
        forms.action = 'writeProc';
        forms.ACA_ID = '<?php echo $aca_id;?>';
        forms.USER_ID = '<?php echo $user_id;?>';
        forms.is_teacher = '<?php echo $is_teacher;?>'
        forms.files = dropzonefiles;

        console.log(forms);

        fetch("/api/notice", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(forms),
                })
        .then((response) => response.json())
        .then((data) => {
            console.log( data );
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
                    title: '알림장이 등록 되었습니다.'
                }).then(function (result) {
                    if (true) {
                        location.href="/notice"
                    }
                });

                
            } else {
                Swal.fire("등록에 실패하였습니다.");
                location.reload();
            }
        });
    }

    function checkvalid(){
        
        var forms = $('form#fileupload').serializeObject();
        forms.action = 'writeProc';
        forms.ACA_ID = content_data.ACA_ID;
        forms.USER_ID = content_data.USER_ID;
        forms.files = content_data.files
        forms.is_teacher = content_data.is_teacher;
        
        
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
                    timer: 1000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })
                Toast.fire({
                    icon: 'success',
                    title: '알림장이 등록 되었습니다.'
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


<script type="text/javascript">

$(document).ready(function(){
    // 반선택
    $(document).on('change' , '#selctClass', function(){
        let class_cd = $(this).val() ;
        let param = {
            class_cd : class_cd
        };

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
                        html += `<div class="name_list">
                            <input type="checkbox" name="STD_ID" id="STD_ID${item.STD_ID}" value="${item.STD_ID}" class="_std_id">
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
            text : "등록하시겠습니까?" , icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "확인",
            cancelButtonText:"취소"
        }).then((result) => {
            if (result.isConfirmed) {
                e.preventDefault();
                e.stopPropagation();

                if (myDropzone.files != "") {
                    // console.log(myDropzone.files);
                    myDropzone.processQueue();
                } else {
                // if no file submit the form    
                    goSubmit();
                }
            }
        });
    })
});
</script>

<script >
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