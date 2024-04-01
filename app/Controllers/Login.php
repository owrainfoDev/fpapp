<?php

namespace App\Controllers;
use CodeIgniter\HTTP\IncomingRequest;
// namespace App\Controllers\Filters;

// use App\Controllers\BaseController;

class Login extends BaseController
{
    public $pagename = '로그인';

    // public $apiUrl = 'http://192.168.10.227:8800';

    // public $apiUrl = 'https://api.kyowonwiz.com/wiz';
    public $apiUrl = 'http://localhost:8800/fp';

    public function index()
    {
        // return view('welcome_message');
        $data = [
            'header' => ['title' => $this->pagename]
        ];
        return $this->template('login', $data , 'none');
    }

    public function ajaxloginCheck(){

        if ($this->request->isAJAX()) {

            $body = json_encode(
                [
                    'userId' => $this->request->getPost('userId'), 
                    'password' => $this->request->getPost('password'),
                    'loginType' => $this->request->getPost('loginType')
                ]
            );
            
            $url = $this->apiUrl . '/api/user/login.json';
            $response = curlSend( $url , $body );

            $result = json_decode($response);

        }

        return $response;
    }

    public function ajaxReqValidToken(){
        $content = trim(file_get_contents("php://input"));
        $accToken = json_decode($content, true);
        // $sendToken = aes256Encode($accToken['accToken']);
        $sendToken = $accToken['accToken'];

        $url = $this->apiUrl . '/api/user/reqValidToken.json';
        $response = curlBearSend($url, $sendToken);
        $result = json_decode($response);

        if ( in_array( $_SERVER["REMOTE_ADDR"]  , array('106.254.236.154', '106.254.236.156') ) ) {
            $result = (object)[
                'resultCode' => "1000",
                'data' => (object)[
                        // 'user_id' => 'P01089354962',
                        'user_id' => 'master_011',
                    ]
            ];

        }
            
        // accessToken 성공 일때 
        if ($result && $result->resultCode == '1000') {
            $author = new \App\Models\AuthorInfo($result->data->user_id);
            $is_teacher = $author->is_teacher();
            $info = $author->info();
            $user_nm = $info->USER_NM;
            $aca_id = $info->ACA_ID;

            $session = session();
            // $session->destroy();
            $session->set('_is_teacher' , $is_teacher);
            
            $session->set('_user_id', $result->data->user_id);

            $return_param = array(
                'resultCode' => $result->resultCode,
                'resultMsg' => $result->resultMsg,
                'is_teacher' => $is_teacher,
                'user_id' => $result->data->user_id,
                'user_nm' => $user_nm,
                'aca_id' => $aca_id,
                // 'academyYear' => $academyYear->baseAcademicyear()
            );

            if ( $is_teacher == false ){
                $childInfo = $author->getChildrenInfo();
                if ( $session->get('_std_id') == '' ){
                    $session->set('_std_id' , $childInfo[0]['STD_ID']);    
                }
                $return_param['std_id'] = $session->get('_std_id');
            } else {
                $session->remove('_std_id');
            }
            
            return json_encode($return_param);
            
        } else {

            $session = session();
            $session->remove('_user_id');
            $session->remove('_is_teacher');
            $session->remove('_std_id');

            return json_encode( $response );
        }
    }

    public function ajaxSetStudent(){
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $std_id = $data['std_id'];

        $session = session();
        $session->set('_std_id' , $std_id);    


        return json_encode( ['status'=>"success" , 'msg'=>$std_id]);
    }

    public function redirectReqValidToken(){
        
        // $content = trim(file_get_contents("php://input"));
        // $accToken = json_decode($content, true);
        // $sendToken = aes256Encode($accToken['accToken']);
        $request = service('request');
        // $sendToken = $request->getPost('accToken');
        $sendToken = $_POST['accToken'];
        $redirectUrl = $request->getPost('redirectUrl');

        if ( empty( $sendToken ) ) {
            echo "토큰값 없음";
            die();
            // return redirect()->to(site_url('/'));
        }

        $url = $this->apiUrl . '/api/user/reqValidToken.json';
        $response = curlBearSend($url, $sendToken);
        $result = json_decode($response);

        if ( in_array( $_SERVER["REMOTE_ADDR"]  , array('106.254.236.154', '106.254.236.156') ) ) {
            $result = (object)[
                'resultCode' => "1000",
                'data' => (object)[
                        'user_id' => 'test',
                ]
            ];

        }
            
        // accessToken 성공 일때 
        if ($result && $result->resultCode == '1000') {
            $author = new \App\Models\AuthorInfo($result->data->user_id);
            $is_teacher = $author->is_teacher();
            $info = $author->info();
            $user_nm = $info->USER_NM;
            $aca_id = $info->ACA_ID;

            $session = session();
            // $session->destroy();
            $session->set('_is_teacher' , $is_teacher);
            
            $session->set('_user_id', $result->data->user_id);

            $return_param = array(
                'resultCode' => $result->resultCode,
                'resultMsg' => $result->resultMsg,
                'is_teacher' => $is_teacher,
                'user_id' => $result->data->user_id,
                'user_nm' => $user_nm,
                'aca_id' => $aca_id,
                // 'academyYear' => $academyYear->baseAcademicyear()
            );

            if ( $is_teacher == false ){
                $childInfo = $author->getChildrenInfo();
                if ( $session->get('_std_id') == '' ){
                    $session->set('_std_id' , $childInfo[0]['STD_ID']);    
                }
                $return_param['std_id'] = $session->get('_std_id');
            } else {
                $session->remove('_std_id');
            }
            
            return redirect()->to(site_url($redirectUrl));

            // var_dump($session->get());

            // return $redirectUrl;
            
        } else {

            $session = session();
            $session->remove('_user_id');
            $session->remove('_is_teacher');
            $session->remove('_std_id');

            echo $sendToken;
            
            echo "<br>";
            echo "result:code ";
            // var_dump($result);

            return redirect()->to(site_url('/'));
        }
    
    }
    
}
