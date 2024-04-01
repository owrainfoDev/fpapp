<html>
    <head>
    <?php echo $this->include('./layout/common/head');?>
    <?php echo $this->include('./layout/common/js');?>
    <?php echo $this->include('./layout/common/css');?>
    </head>
    <body>
        <div>
            아이디 : <input type="text" name="userId" id="userId" value="asdfasfd">
        </div>
        <div>
            비밀번호 : <input type="password" name="password" id="password" value="123131">
        </div>
        <div>
            타입 : 
            학부모 <input type="radio" name="loginType" id="loginType" value="P" checked>
            선생님 <input type="radio" name="loginType" id="loginType" value="T">
        </div>
        <div>
            <button type="button" id="loginBtn">로그인</button>
        </div>
        <div id="accessToken"></div>
    </body>
    <script>
        $(document).ready(function(){
            $(document).on('click', '#loginBtn', function(){
                if ($('#userId').val() == ""){
                    alert('아이디 입력 ');
                    return ;
                }

                if ($('#password').val() == ""){
                    alert('비밀번호 입력 ');
                    return ;
                }

                var formdata = {
                    userId : $('#userId').val(),
                    password : btoa($('#password').val()),
                    loginType : $('input[name="loginType"]:checked').val()
                };

                $.ajax({
                    type : 'post',           // 타입 (get, post, put 등등)
                    url : '/api/loginCheck',
                    async : false,            // 비동기화 여부 (default : true)
                    dataType : 'json', 
                    data : formdata,
                    success : function(response){
                        if (response.resultCode != '1000') {
                            alert('로그인 실패');
                            return false;
                        } else {
                            $('#accessToken').html(response.accessToken);
                        }

                    },
                    error: function(request , status , error){
                        console.log(error);
                    }
                })
            })
        });
    </script>
</html>


http://192.168.10.227:8800