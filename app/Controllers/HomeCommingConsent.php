<?php

namespace App\Controllers;

use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;
use CodeIgniter\Files\File;


class HomeCommingConsent extends BaseController
{ 
    public $pagename = '귀가동의서';
    public $pn = 'homeCommingConsent';

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

        if ($this->is_teacher !== true) {
            $this->stdInfo = $this->authinfo->stdInfo($session->get('_std_id'));
            $params['std_id'] = $session->get("_std_id");
        }
        $this->class_list = $students->getClassListFromTeacher($params);  // 학원 리스트
    }

    public function func($func){
        $content = trim(file_get_contents("php://input"));
        $this->data = json_decode($content, true);
        if ($content == "" && ( $_REQUEST ) ) $this->data = $_REQUEST;
        return $this->{$func}();
    }

    public function index(){
        if ($this->is_teacher === true) $this->teacherList();
        else $this->parentList();
    }

    public function parentList(){
        $content = [];
        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'html' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('homeCommingConsent/parentList', $data , 'sub');
    }

    public function teacherList(){

        $content = [];
        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'html' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('homeCommingConsent/teacherList', $data , 'sub');
    }

    public function ListMore(){

        $request = \Config\Services::request();
        $page = $request->getVar('page');
        $students = $this->stdInfo;
        
        $HomeCommingConsent = new \App\Models\HomeCommingConsent();
        if ($this->is_teacher){
            $selectClass = $this->data['selectClass'];
            $selectChild = $this->data['selectChild'];
            $unConfirm = $this->data['unConfirm'];

            $params = [
                'is_teacher' => $this->is_teacher === true ? "Y" : "N",
                'user_id' => $this->user_id,
                'ACA_ID' => $this->userinfo->ACA_ID,
                'ACA_YEAR' => $this->year
            ];

            // $teacherModel = new \App\Models\Teacher(); 
            // $classinfo = $teacherModel->getClassfromTeacherId($params);
            $classcd = [];
            foreach ( $this->class_list as $class){
                $classcd[] = $class->CLASS_CD;
            }

            $params = [
                'page'   => isset($page) ? $page : 1,
                'limit' => $this->limit,
                'class_cd' => $classcd,
                'selectClass' => $this->data['selectClass'],
                'selectChild' => $this->data['selectChild'],
                'unConfirm' => $this->data['unConfirm']
            ];

            $content = $HomeCommingConsent->teacherList($params);
        } else {

            $params = [
                'page'   => isset($page) ? $page : 1,
                'STD_ID' => $students['STD_ID'],
                'limit' => $this->limit
            ];

            $content = $HomeCommingConsent->parentList($params);
        }
        
        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'html' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];

        ob_start();
        echo $this->template('homeCommingConsent/Listmore', $data , 'none');
        $html = ob_get_contents();
        ob_end_clean();
       
        return json_encode([
                'html' => $html,
                'total' => $content['total_page'],
                'total_row' => $content['total_row'],
                'sql' => $content['sql']
        ]);
    }

    public function detail($seq) {

        $HomeCommingConsent = new \App\Models\HomeCommingConsent();
        $content = $HomeCommingConsent->find($seq);

        if ($this->is_teacher) {
            $HomeCommingConsent->set(
                    [
                        'LEAVE_AGREE_STATUS' => '02',
                        'READ_USER_ID' => $this->user_id,
                        'READ_DTTM' => date("Y-m-d H:i:s")
                    ]
                )->where('LEAVE_AGREE_STATUS','01')->where('AGREE_NO' , $seq)->update();
        }

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'html' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('homeCommingConsent/detail', $data , 'sub');
    }

    public function CompletedBtnProc(){
        if ($this->data['csrf_token_name'] != csrf_hash()){
            return json_encode(['status'=> 'error','msg'=> '토큰 정보가 다릅니다.']);
        }
        $HomeCommingConsent = new \App\Models\HomeCommingConsent();
        try {
        $HomeCommingConsent->set([
                'CONF_MEMO' => $this->data['CONF_MEMO'],
                'CONF_ID' => $this->user_id,
                'CONF_DTTM' => date("Y-m-d H:i:s"),
                'LEAVE_AGREE_STATUS' => "03"
            ])->where("AGREE_NO" , $this->data['seq'])->update();
        } catch (\Exception $e) {
            return json_encode(['status'=> 'error','msg' => '등록실패']);
        }

        return json_encode(['status'=> 'success','msg'=> '등록성공' , 'redirect_to' => 'reload']);
        
    }

    public function deleteProc() {
        if ($this->data['csrf_token_name'] != csrf_hash()){
            return json_encode(['status'=> 'error','msg'=> '토큰 정보가 다릅니다.']);
        }
        $seq = $this->data['seq'];
        $HomeCommingConsent = new \App\Models\HomeCommingConsent();
        $params = [
            'USE_YN'   => "N",
            'DEL_DTTM' => date("Y-m-d H:i:s"),
            'UPT_DTTM' => date("Y-m-d H:i:s"),
            'UPT_USER_ID' => $this->user_id
        ];
        $result = $HomeCommingConsent->set($params)->where("AGREE_NO" , $seq)->update();
        if ($result){
            return json_encode(['status' => 'success' , 'msg'=>'삭제성공' , 'redirect_to' => base_url() . 'homeCommingConsent/']);
        } else {
            return json_encode(['status' => 'fail' , 'msg'=>'실패']);
        }
    }


    public function forms($seq = null){
        if ($seq){
            $HomeCommingConsent = new \App\Models\HomeCommingConsent();
            $detail = $HomeCommingConsent->find($seq);
        }
        
        $content['students'] = $this->stdInfo;
        if (isset($detail) && !empty($detail)) {
            $content['detail'] = $detail;
            $content['mode'] = 'edit';
        }

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'html' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('homeCommingConsent/form', $data , 'sub');
    }

    public function formProc(){
     
        // $parms[""] = $this->data["formName"];
        $params["STD_ID"] = $this->data["STD_ID"];
        $params["ACA_ID"] = $this->data["ACA_ID"];
        // $params["LEAVE_DT"] = $this->data["formClass"];
        $params["LEAVE_DT"] = $this->data["formDate"];
        $params["LEAVE_TM"] = $this->data["timeOption"] . ":" . $this->data["minuteOption"];
        $params["REQ_ID"] = $this->data["USER_ID"];
        $params["LEAVE_TP"] = $this->data["returnOption"];
        $params["DEPUTY_NM"] = $this->data["formDeputy"];
        $params["DEPUTY_REL_CD"] = $this->data["formDeputy_rel"];
        $params["DEPUTY_TEL_NO"] = $this->data["formDeputy_num"];
        $params["EMG_CALL_NM"] = $this->data["formEmergency_name"];
        $params["EMG_CALL_REL_CD"] = $this->data["formEmergency_rel"];
        $params["EMG_CALL_TEL_NO"] = $this->data["formEmergency_num"];
        $params["REQ_MEMO"] = $this->data["formText"];
        // $params[""] = $this->data["agree"];
        // $params["ACA_ID"] = $this->data["ACA_ID"];
        $params["USE_YN"] = "Y";
        $params["ENT_DTTM"] = date("Y-m-d H:i:s");
        $params["ENT_USER_ID"] = $this->user_id;
        // $parms[""] = $this->data["is_teacher"];
        $params['LEAVE_AGREE_STATUS'] = '01';

        $params['classCD']  = is_array($this->data['classCD']) ? $this->data['classCD'] : (array)$this->data['classCD'];

        

        // 파일 등록 
        if ( isset( $this->data['files'] ) && $this->data['files'] != '' ) {
            $files = json_decode( $this->data['files'] , true );
            // var_dump($files);
            foreach ( $files as $file){
                $params['FILE_ORG_NAME'] = $file['ORIGIN_FILE_NM'];
                $params['FILE_NAME'] = $file['FILE_NM'];
                $params['FILE_PATH'] = $file['FILE_PATH'];
                $params['FILE_EXT'] = $file['FILE_EXT'];
                $params['FILE_SIZE'] = $file['FILE_SIZE'];
                $params['FILE_URL'] = $file['FILE_URL'];
            }
        }

        $HomeCommingConsent = new \App\Models\HomeCommingConsent();

        $students = new \App\Models\Students();
        $stdInfo = $students->getUserInfo($params['STD_ID']);

        if ( isset($this->data['AGREE_NO']) && $this->data['AGREE_NO'] != "" ){

            $teacherModel = new \App\Models\Teacher();  
            $teacher_id = []; // push message
            foreach ($params['classCD'] as $class){
                $teacherarray = $teacherModel->ClassTimeTableInfoFromClassCd( $class );
                $classInfo = $teacherModel->ClassInfofromClassCd( $class );
                $class_name = $classInfo->CLASS_NM;
                foreach ( $teacherarray as $teacher){
                    $teacher_id[] = array( "teacher_id" => $teacher->TEACHER_ID , "class_name" => $class_name );
                    $teacher_id[] = array( "teacher_id" => $teacher->TEACHER_ID3 , "class_name" => $class_name );
                }
            }

            $pushmessage = new \App\Models\PushMessage();
            foreach ( $teacher_id as $teacher ) {
                $pushparams = [
                    'SENDER' => $this->user_id, 
                    'USER_ID' => $teacher['teacher_id'], 
                    'ACA_ID' => $params['ACA_ID'] , 
                    'TITLE' => '[귀가동의서]' . $teacher['class_name'] . "-" . $stdInfo->USER_NM .'원생의 귀가동의서', 
                    'MESSAGE' => '[귀가동의서]' . $teacher['class_name'] . "-" . $stdInfo->USER_NM .'원생의 귀가동의서가 수정되었습니다. ' , 
                    'REQUEST_PATH' => '/homeCommingConsent/' . $this->data["AGREE_NO"],
                    "INSERT_USER_ID" => $this->user_id,
                    "INSERT_DTTM" => date("Y-m-d H:i:s")
                ];
                $pushmessage->insert($pushparams);
            }

            unset($params['STD_ID']);
            unset($params['ACA_ID']);
            unset($params['classCD']);
            $return = $HomeCommingConsent->set($params)->where("AGREE_NO" , $this->data["AGREE_NO"])->update();
            $msg = "수정성공";
            $seq = $this->data['AGREE_NO'];

            

        } else {
            
            $return = $HomeCommingConsent->insert( $params );
            $msg = "등록성공";
            $seq = $HomeCommingConsent->insertID;

            $teacherModel = new \App\Models\Teacher();  
            $teacher_id = []; // push message
            foreach ($params['classCD'] as $class){
                $teacherarray = $teacherModel->ClassTimeTableInfoFromClassCd( $class );
                $classInfo = $teacherModel->ClassInfofromClassCd( $class );
                $class_name = $classInfo->CLASS_NM;
                foreach ( $teacherarray as $teacher){
                    $teacher_id[] = array( "teacher_id" => $teacher->TEACHER_ID , "class_name" => $class_name );
                    $teacher_id[] = array( "teacher_id" => $teacher->TEACHER_ID3 , "class_name" => $class_name );
                }
            }

            $pushmessage = new \App\Models\PushMessage();
            foreach ( $teacher_id as $teacher ) {
                $pushparams = [
                    'SENDER' => $this->user_id, 
                    'USER_ID' => $teacher['teacher_id'], 
                    'ACA_ID' => $params['ACA_ID'] , 
                    'TITLE' => '[귀가동의서]' . $teacher['class_name'] . "-" . $stdInfo->USER_NM .'원생의 귀가동의서', 
                    'MESSAGE' => '[귀가동의서]' . $teacher['class_name'] . "-" . $stdInfo->USER_NM .'원생의 귀가동의서가 등록되었습니다. ' , 
                    'REQUEST_PATH' => '/homeCommingConsent/' . $seq,
                    "INSERT_USER_ID" => $this->user_id,
                    "INSERT_DTTM" => date("Y-m-d H:i:s")
                ];
                $pushmessage->insert($pushparams);
            }
        }
        if ($return){
            return json_encode(['status' => 'success' , 'msg'=>$msg , 'redirect_to' => base_url() . '/homeCommingConsent/' . $seq ]);
        } else {
            return json_encode(['status' => 'fail' , 'msg'=>'실패']);
        }

    }
}