<?php

namespace App\Controllers;

use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;
use CodeIgniter\Files\File;


class AppBoard extends BaseController
{ 
    public $pagename = '공지사항';
    public $pn = 'appBoard';

    protected $data;
    protected $meal;
    protected $authinfo;
    protected $user_id;
    protected $userinfo;
    protected $is_teacher;
    protected $year;
    protected $class_list;
    protected $limit = 10;
    protected $stdInfo;
    protected $aca_id;
    protected $stu;

    public function __construct()
    {
        $session = session();
        $this->user_id = $session->get('_user_id');
        $this->authinfo = new \App\Models\AuthorInfo($this->user_id);
        $this->authinfo->year = $session->get("year");
        $this->userinfo = $this->authinfo->info();
        $this->is_teacher = $this->authinfo->is_teacher();
        $this->year = $session->get("year");
        

        $students = new \App\Models\Students();
        $params = [
            'userid' => $this->user_id,
            'aca_id' => $this->userinfo->ACA_ID,
            'is_teacher' => $this->is_teacher === true ? "Y" : "N",
            'year' => $this->year
        ];

        $this->aca_id = $this->userinfo->ACA_ID;
        if ($this->is_teacher !== true) {
            $this->stdInfo = $this->authinfo->stdInfo($session->get('_std_id'));
            $params['std_id'] = $session->get("_std_id");
            $this->aca_id = $this->stdInfo['ACA_ID'];
        }
        $this->class_list = $students->getClassListFromTeacher($params);  // 학원 리스트

        $this->stu = $students;
    }

    public function func($func){
        $content = trim(file_get_contents("php://input"));
        $this->data = json_decode($content, true);
        if ($content == "" && ( $_REQUEST ) ) $this->data = $_REQUEST;
        return $this->{$func}();
    }

    public function index(){
        $session = session();
        $data = [];
        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'data' => $data,
            'auth' => $this->userinfo,
            'auth2' => $session->get('_std_id'),
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('appBoard/list', $data , 'sub');
    }

    public function ListMore(){
        $session = session();
        // var_dump( $session->get());
        // if ($this->data['csrf_token_name'] != csrf_hash()){
        //     return json_encode(['status'=> 'error','msg'=> '토큰 정보가 다릅니다.']);
        // }

        $page = $this->data['page'];
        $searchText = $this->data['searchText'];

        if ( empty( $this->user_id ) ) {
            $this->user_id = $this->data['auth'];
            $this->authinfo = new \App\Models\AuthorInfo($this->user_id);
            $this->authinfo->year = date("Y");
            $this->userinfo = $this->authinfo->info();
            $this->is_teacher = $this->authinfo->is_teacher();
            $this->year = date("Y");

            $students = new \App\Models\Students();
            $params = [
                'userid' => $this->user_id,
                'aca_id' => $this->userinfo->ACA_ID,
                'is_teacher' => $this->is_teacher === true ? "Y" : "N",
                'year' => $this->year
            ];
            return json_encode( [json_encode(  $params )] );
            if ($this->is_teacher !== true) {
                $this->stdInfo = $this->authinfo->stdInfo($this->data['auth2']);
                $params['std_id'] = $this->data['auth2'];
            }
            $this->class_list = $students->getClassListFromTeacher($params);  // 학원 리스트
        }

        // var_dump($this->class_list);

        foreach ($this->class_list as $class){
            $class_cd[] = $class->CLASS_CD;
        }
        $params = [
            'page'   => isset($page) ? $page : 1,
            'limit' => $this->limit,
            'class_cd' => $class_cd,
            'aca_id' => $this->aca_id,
            'is_teacher' => $this->is_teacher,
            'search' => $searchText
        ];

        

        // var_dump($params);
        // die();

        // return json_encode( [json_encode( $class_cd )] );

        $appBoardModel = new \App\Models\AppBoard();
        $result = $appBoardModel->getList($params);
        
        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'list' => $result,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];

        ob_start();
        echo $this->template('appBoard/Listmore', $data , 'none');
        $html = ob_get_contents();
        ob_end_clean();
        
        return json_encode([
                'html' => $html,
                'total' => $result['total_page'],
                'total_row' => $result['total_row']
        ]);
    }

    public function forms($seq = null){
        $mode = $seq == null ? "write" : "edit";
        $appBoardModel = new \App\Models\AppBoard();

        $data = $files = $c = [];
        if ( $mode == "edit"){
            $data = $appBoardModel->find( $seq );
            $files = $appBoardModel->_get_file_list( $seq );
            $selectClassList = $appBoardModel->_listAppBoardClass( $seq );
            $c = [];
            foreach ( $selectClassList as $cd ){
                $c[] = $cd['CLASS_CD'];
            }
        }

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'data' => $data,
            'files' => $files,
            'selectClassList' => $c,
            'auth' => $this->userinfo,
            'mode' => $mode,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('appBoard/form', $data , 'sub');
    }

    public function formsProc(){
        if ($this->data['csrf_token_name'] != csrf_hash()){
            return json_encode(['status'=> 'error','msg'=> '토큰 정보가 다릅니다.']);
        }

        $mode = isset($this->data['AB_NO']) ? "edit" : "write";
        $board_tp = isset( $this->data['allChck'] ) ? "01" : "02";
        $params = [
            "ACA_ID" => $this->data['ACA_ID'],
            "TITLE" => $this->data['noteTitle'],
            'CONTENTS' => str_nl2br($this->data['noteTxt']),
            'BOARD_TP' => $board_tp,
            "COMM_CHK" => 'N',
            "USE_YN" => 'Y',
            "VIEW_YN" => 'Y',
        ];

        $class_cd = $board_tp == "02" ? (array)$this->data["class_cd"] : [];
        $appBoardModel = new \App\Models\AppBoard();
        if ( $mode == "write" ) {
            $params["ENT_USER_ID"] = $this->user_id;
            $params["ENT_DTTM"] = date("Y-m-d H:i:s");
            $params['STATUS'] = '01';
            $appBoardModel->insert($params);
            $AB_NO = $appBoardModel->getInsertID();
            $redirect_to = base_url('/appBoard/' . $AB_NO );
            $msg = "등록되었습니다.";
        } else if ( $mode == "edit" ) {
            $AB_NO = $this->data['AB_NO'];
            $params["UPT_USER_ID"] = $this->user_id;
            $params["UPT_DTTM"] = date("Y-m-d H:i:s");
            $appBoardModel->update($AB_NO , $params);
            $redirect_to = "reload";
            $msg = "수정되었습니다.";
        }

        $pushmessage = new \App\Models\PushMessage();
        $appBoardModel->_deleteAppBoardClass($AB_NO);
        if ( $board_tp == "02") {
            foreach( $class_cd as $cd ) {
                $appBoardModel->_insertAppBoardClass(["AB_NO" => $AB_NO , "CLASS_CD" => $cd]);
                
                $senderId = $this->stu->getParentidfromClassCd($cd);
                foreach ( $senderId as $sendid){
                    $pushparams = [
                        'SENDER' => $this->user_id, 
                        'USER_ID' => $sendid->PARENT_ID, 
                        'ACA_ID' => $this->data['ACA_ID'] , 
                        'TITLE' => '[공지사항] - 공지사항', 
                        'MESSAGE' => '[공지사항] - 공지사항 - ' . $this->data['noteTitle'] , 
                        'REQUEST_PATH' => '/appBoard/' . $AB_NO, 
                        "INSERT_USER_ID" => $this->user_id,
                        "INSERT_DTTM" => date("Y-m-d H:i:s")
                    ];
    
                    $pushmessage->insert($pushparams);
                }
            }
        } else {  

            // 원 전체 공지사항 
            $senderId = $this->stu->getAcaStudentsInfo($this->data['ACA_ID']);
            foreach ( $senderId as $sendid){
                $pushparams = [
                    'SENDER' => $this->user_id, 
                    'USER_ID' => $sendid->PARENT_ID, 
                    'ACA_ID' => $this->data['ACA_ID'] , 
                    'TITLE' => '[공지사항] - 원 전체 공지사항', 
                    'MESSAGE' => '[공지사항] - 원 전체 공지사항 - ' . $this->data['noteTitle'] , 
                    'REQUEST_PATH' => '/appBoard/' . $AB_NO, 
                    "INSERT_USER_ID" => $this->user_id,
                    "INSERT_DTTM" => date("Y-m-d H:i:s")
                ];

                $pushmessage->insert($pushparams);
            }

        }
        
        if ( isset( $this->data['files'] ) && $this->data['files'] != '' ) {
            $files = json_decode( $this->data['files'] , true );
            foreach ( $files as $file){
                $fileparams = [
                    'AB_NO' => $AB_NO,
                    'FILE_TP' => null,
                    'FILE_NAME' => $file['FILE_NM'],
                    'FILE_PATH' => $file['FILE_PATH'],
                    'FILE_EXT' => $file['FILE_EXT'],
                    'ENT_DTTM' => date("Y-m-d H:i:s"),
                    'ENT_USER_ID' => $this->user_id,
                    'FILE_ORG_NAME' => $file['ORIGIN_FILE_NM'],
                    'FILE_URL' => $file['FILE_URL'],
                    'FILE_SIZE' => $file['FILE_SIZE']
                ];

                if ( $thumbnail = mp4tojpg( $file['FILE_PATH'] , $file['FILE_NM'] , $file['FILE_EXT'] ) ) {
                    $fileparams['THUMBNAIL'] = "Y";
                }

                $appBoardModel->_apnd_file_insert($fileparams);
            }
        }

        return json_encode(['status' => 'success' , 'msg'=>$msg , 'redirect_to' => $redirect_to]);
    }

    public function detail($seq){
        
        $appBoardModel = new \App\Models\AppBoard();
        $data = $appBoardModel->find( $seq );
        $files = $appBoardModel->_get_file_list( $seq );
        // var_dump($files);

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'data' => $data,
            'files' => $files,
            'auth' => $this->userinfo,
            'user_id' => $this->user_id,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('appBoard/detail', $data , 'sub');
    }

    public function deleteProc(){
        if ($this->data['csrf_token_name'] != csrf_hash()){
            return json_encode(['status'=> 'error','msg'=> '토큰 정보가 다릅니다.']);
        }

        $appBoardModel = new \App\Models\AppBoard();
        $AB_NO = $this->data['AB_NO'];

        // 파일 삭제 
        $fpa = $appBoardModel->_get_file_list($AB_NO);
        $file = new \App\Controllers\FileUpload();
        foreach ( $fpa as $f ){
            $param = ['seq' => $f->SEQ , 'url' => $f->FILE_PATH . "/" . $f->FILE_NAME . '.' . $f->FILE_EXT , 'tb' => '_APP_BOARD_APND_FILE'] ;
            $file->removeFile($param);
        }
        // 클래스 삭제
        $appBoardModel->_deleteAppBoardClass($AB_NO);
        $appBoardModel->update($AB_NO , [
            'VIEW_YN' => 'N' , 'USE_YN' => 'N' , 'DEL_USER_ID' => $this->user_id , 'DEL_DTTM' => date("Y-m-d H:i:s")
        ]);

        return json_encode(["status"=> "success","msg"=> "삭제성공" , 'redirect_to' => base_url('/appBoard/') ]);
    }
}