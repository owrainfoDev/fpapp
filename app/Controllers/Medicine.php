<?php

namespace App\Controllers;

use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\RequestInterface;


class Medicine extends BaseController
{
    public $pagename = '투약의뢰서';
    public $pn = 'medicine';

    protected $data;
    protected $meal;
    protected $authinfo;
    protected $user_id;
    protected $userinfo;
    protected $is_teacher;
    protected $year;
    protected $class_list;
    protected $medicineModel;
    protected $stdInfo;
    protected $limit = 5;
    protected $stu;
    protected $aca_id;

    public function __construct()
    {
        $session = session();
        $this->user_id = $session->get('_user_id');
        $this->authinfo = new \App\Models\AuthorInfo($this->user_id);
        $this->year = $session->get("year");
        $this->authinfo->year = $session->get("year");
        $this->userinfo = $this->authinfo->info();
        $this->is_teacher = $this->authinfo->is_teacher();
        

        $students = new \App\Models\Students();
        $params = [
            'userid' => $this->user_id,
            'aca_id' => $this->userinfo->ACA_ID,
            'is_teacher' => $this->is_teacher === true ? "Y" : "N",
            'year' => $this->year
        ];

        // 선택된 학생 정보
        $this->aca_id = $this->userinfo->ACA_ID;
        if ($this->is_teacher !== true) {
            $this->stdInfo = $this->authinfo->stdInfo($session->get('_std_id'));
            $params['std_id'] = $session->get("_std_id");
            $this->aca_id = $this->stdInfo['ACA_ID'];
        }
        $this->class_list = $students->getClassListFromTeacher($params);  // 학원 리스트
        $this->stu = $students;
    }

    /** 
     * routes 분기 처리
     */
    public function func($func){
        $content = trim(file_get_contents("php://input"));
        $this->data = json_decode($content, true);

        if ($content == "" && ( $_REQUEST ) ){
            $this->data = $_REQUEST;
        }
        return $this->{$func}();
    }

    public function index(){
        $this->list();
    }

    public function teacherList() {

        $page = 1 ;
        
        $params = [
            'is_teacher' => $this->is_teacher === true ? "Y" : "N",
            'user_id' => $this->user_id,
            'ACA_ID' => $this->aca_id,
            'ACA_YEAR' => $this->year
        ];
        
        // $teacherModel = new \App\Models\Teacher(); 
        // $classinfo = $teacherModel->getClassfromTeacherId($params);
        
        $classcd = [];
        foreach ( $this->class_list as $class){
            $classcd[] = $class->CLASS_CD;
        }

        $params = [
            'limit'   => $this->limit,
            'user_id' => $this->user_id,
            'page' => $page,
            'classcd' => $classcd
        ];

        $medicineModel = new \App\Models\Medicine();
        $content = $medicineModel->getTeacherList($params);

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'data' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->is_teacher
        ];

        return $data;
    }

    public function list(){

        if ($this->is_teacher == true) { // 교사 일때
            $data = $this->teacherList();

            return $this->template('medicine/medicineteacherList', $data , 'sub');     
        }

        $page = isset($this->data['page']) ? $this->data['page'] : 1 ;
        $params = [
            'limit'   => $this->limit,
            'user_id' => $this->user_id,
            'page' => $page
        ];

        $medicineModel = new \App\Models\Medicine();
        $content = $medicineModel->getList($params);

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'data' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('medicine/medicine', $data , 'sub');    
    }

    public function onScrollLoadMore(){
        $request = \Config\Services::request();
        $page = $request->getVar('page');
        $selectClass = $request->getVar('selectClass');
        $selectChild = $request->getVar('selectChild');
        $unConfirm = $request->getVar('unConfirm');

        if ( $this->is_teacher == true) {
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
                'limit'   => $this->limit,
                'user_id' => $this->user_id,
                'page' => $page,
                'selectClass' => $selectClass,
                'selectChild'=> $selectChild,
                'unConfirm' => $unConfirm,
                'classcd' => $classcd
            ];

            $medicineModel = new \App\Models\Medicine();
            $content = $medicineModel->getTeacherList($params);

            $data = [
                'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
                'classList' => $this->class_list,
                'data' => $content,
                'auth' => $this->userinfo,
                // 'search' => $search,
                'is_teacher' => $this->authinfo->is_teacher()
            ];

            ob_start();
            echo $this->template('medicine/medicinTeacherListemore', $data , 'none');
            $html = ob_get_contents();
            ob_end_clean();

            return json_encode([
                    'html' => $html,
                    'total' => $content['total_row']
            ]);

        } else {

            $params = [
                'limit'   => $this->limit,
                'user_id' => $this->user_id,
                'selectClass' => $selectClass,
                'selectChild'=> $selectChild,
                'page' => $page
            ];
            $medicineModel = new \App\Models\Medicine();
            $content = $medicineModel->getList($params);

        
            $data = [
                'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
                'classList' => $this->class_list,
                'data' => $content,
                'auth' => $this->userinfo,
                // 'search' => $search,
                'is_teacher' => $this->authinfo->is_teacher()
            ];

            return $this->template('medicine/medicinemore', $data , 'none');    
        }
        
    }

    public function detail($seq){
        $medicineModel = new \App\Models\Medicine();
        $content = $medicineModel->getDetail($seq);

        if ($this->is_teacher) {
            if ($content['data']->MEDI_REQ_STATUS == "01"){     // 투약의료일때
                $params['MEDI_REQ_NO'] = $seq;
                $params['MEDI_REQ_STATUS'] = '02';
                $params['READ_USER_ID'] = $this->user_id;
                $params['READ_DTTM'] = date("Y-m-d H:i:s");
                $result = $medicineModel->updateProc($params);
            }
        }
        
        if (!$content['data']) {
            redirectto('/medicine');
        } 

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'data' => $content['data'],
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];

        if ($this->is_teacher) {
            $p = $content['data']->STD_ID;

            $data['CURRENT_CLASS_INFO'] = $this->stu->getClassCDfromSTDID(['STD_ID' => $p , 'ACA_YEAR' => $this->year ]);
            return $this->template('medicine/medicineTeacherDetail', $data , 'sub');    
        } else {

            $p = $content['data']->STD_ID;

            $data['CURRENT_CLASS_INFO'] = $this->stu->getClassCDfromSTDID(['STD_ID' => $p , 'ACA_YEAR' => $this->year ]);

            return $this->template('medicine/detail', $data , 'sub');    
        }
    }

    public function teacherConfirmProc(){

        $params['MEDI_REQ_NO'] = $this->data['seq'];
        $params['MEDI_ID'] = $this->data['USER_ID'];
        $params['MEDI_RSLT_COMMENT'] = $this->data['MEDI_RSLT_COMMENT'];
        $params['MEDI_DTTM'] = date("Y-m-d H:i:s");
        $params['MEDI_REQ_STATUS'] = '03';  // 완료


        $medicineModel = new \App\Models\Medicine();
        $result = $medicineModel->updateProc($params);
        
        if ($result){
            return json_encode(['status'=> 'success','msg'=> '투약의뢰서 완료하였습니다.' , 'redirect_to' => "/medicine/" . $params['MEDI_REQ_NO'] ]);
        } else {
            return json_encode(['status'=> 'success','msg'=> '수정실패' ]);
        }

    }

    public function write(){
        $content = [
            'user' => $this->userinfo,
            'stdInfo' => $this->stdInfo 
        ];
        
        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'data' => $content,
            'pager' => $content['pager'],
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('medicine/write', $data , 'sub');    
    }

    public function writeProc(){
        
        $params['formName']                 = $this->data['formName'];
        $params['STD_ID']                   = $this->data['formStdId'];
        $params['ACA_ID']                   = $this->data['ACA_ID'];
        $params['REQ_DT']                   = $this->data['formDate'];
        $params['SYMP_DESC']                = $this->data['formSymptoms'];
        $params['DRUG_TYPE']                = $this->data['formType'];
        $params['DRUG_DOSE']                = $this->data['formType_amount'];
        $params['DRUG_STORAGE_METHOD']      = $this->data['formKeep'];
        $params['DRUG_TM']                  = $this->data['formTime'];
        $params['DRUG_TIMES']               = $this->data['formNum'];
        $params['REQ_COMMENT']              = $this->data['REQ_COMMENT'];
        $params['REQ_USER_ID']              = $this->user_id;
        $params['ENT_DTTM']                 = date("Y-m-d H:i:s");
        $params['ENT_USER_ID']              = $this->user_id;
        $params['USE_YN']                   = 'Y';

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

        $medicineModel = new \App\Models\Medicine();
        $medicineModel->insert( $params );
        $insert_id = $medicineModel->insertID();

        $students = new \App\Models\Students();
        $stdInfo = $students->getUserInfo($params['STD_ID']);

        $formClass = $this->data['formClass'];

        if ( empty($formClass) ) {
            return json_encode(['status' => 'fail' , 'msg'=>'선택된 클래스가 없습니다.' , 'redirect_to' => 'reload' ] );
        }

        if ( ! is_array($formClass) ) $formClass = (array)$formClass;

        $teacherModel = new \App\Models\Teacher();  
        $teacher_id = []; // push message
        foreach ($formClass as $class){
            $teacherarray = $teacherModel->ClassTimeTableInfoFromClassCd( $class );
            $classInfo = $teacherModel->ClassInfofromClassCd( $class );
            $class_name = $classInfo->CLASS_NM;
            foreach ( $teacherarray as $teacher){
                $teacher_id[] = array( "teacher_id" => $teacher->TEACHER_ID , "class_name" => $class_name );
                $teacher_id[] = array( "teacher_id" => $teacher->TEACHER_ID3 , "class_name" => $class_name );
            }
        }

        $teacher_id = array_unique($teacher_id);

        $pushmessage = new \App\Models\PushMessage();
        foreach ( $teacher_id as $teacher ) {
            $pushparams = [
                'SENDER' => $this->user_id, 
                'USER_ID' => $teacher['teacher_id'], 
                'ACA_ID' => $params['ACA_ID'] , 
                'TITLE' => '[투약의뢰서]' . $teacher['class_name'] . "-" . $stdInfo->USER_NM .'원생의 투약의뢰서', 
                'MESSAGE' => '[투약의뢰서]' . $teacher['class_name'] . "-" . $stdInfo->USER_NM .'원생의 투약의뢰서가 등록되었습니다. ' , 
                'REQUEST_PATH' => '/medicine/' . $insert_id, 
                "INSERT_USER_ID" => $this->user_id,
                "INSERT_DTTM" => date("Y-m-d H:i:s")
            ];
            $pushmessage->insert($pushparams);
        }

        return json_encode(['status' => 'success' , 'msg'=>'등록성공' , 'redirect_to' => '/medicine/' . $insert_id ]);
    }

    public function edit($seq){
        $medicineModel = new \App\Models\Medicine();
        $content = $medicineModel->getDetail($seq);

        $content = [
            'user' => $this->userinfo,
            'stdInfo' => $this->stdInfo ,
            'data' => $content
        ];

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'data' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('medicine/edit', $data , 'sub');    
    }

    public function deleteProc(){
        $medicineModel = new \App\Models\Medicine();
        
        if ($this->data['csrf_token_name'] != csrf_hash()){
            return json_encode(['status'=> 'error','msg'=> '토큰 정보가 다릅니다.']);
        }

        $detail = $medicineModel->getDetail($this->data['seq']);
        $STD_ID = $detail['data']->STD_ID;
        $ACA_ID = $detail['data']->ACA_ID;

        $result = $medicineModel
        ->set('USE_YN' , 'N')
        ->set('UPT_DTTM' , date("Y-m-d H:i:s"))
        ->set("UPT_USER_ID" , $this->data['USER_ID'])
        ->where('MEDI_REQ_NO', $this->data['seq'])
        ->where('REQ_USER_ID', $this->data['USER_ID'])
        ->update();
        if ($result){
        
        $students = new \App\Models\Students();
        $stdInfo = $students->getUserInfo($STD_ID);    

        $teacherModel = new \App\Models\Teacher();  
        $teacher_id = []; // push message
        foreach ($this->stdInfo['CLASSNOR'] as $class){
            $teacherarray = $teacherModel->ClassTimeTableInfoFromClassCd( $class['CLASS_CD'] );
            $classInfo = $teacherModel->ClassInfofromClassCd( $class['CLASS_CD'] );
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
                'ACA_ID' => $ACA_ID , 
                'TITLE' => '[투약의뢰서]' . $teacher['class_name'] . "-" . $stdInfo->USER_NM .'원생의 투약의뢰서', 
                'MESSAGE' => '[투약의뢰서]' . $teacher['class_name'] . "-" . $stdInfo->USER_NM .'원생의 투약의뢰서가 취소되었습니다. ' , 
                'REQUEST_PATH' => '/medicine/' . $this->data['seq'], 
                "INSERT_USER_ID" => $this->user_id,
                "INSERT_DTTM" => date("Y-m-d H:i:s")
            ];

            $pushmessage->insert($pushparams);
        }


            return json_encode(['status'=> 'success','msg'=> '삭제되었습니다.' , 'redirect_to' => "/medicine"]);
        } else {
            return json_encode(['status'=> 'fail','msg'=> '삭제 실패']);
        }
        

    }

    public function editProc(){
        $medicineModel = new \App\Models\Medicine();
        
        if ($this->data['csrf_token_name'] != csrf_hash()){
            return json_encode(['status'=> 'error','msg'=> '토큰 정보가 다릅니다.']);
        }

        $detail = $medicineModel->getDetail($this->data['seq']);
        $STD_ID = $detail['data']->STD_ID;
        $ACA_ID = $detail['data']->ACA_ID;

        $params['MEDI_REQ_NO']               = $this->data['seq'];
        // $params['formName']                 = $this->data['formName'];
        $params['STD_ID']                   = $this->data['formStdId'];
        $params['ACA_ID']                   = $this->data['ACA_ID'];
        $params['REQ_DT']                   = $this->data['formDate'];
        $params['SYMP_DESC']                = $this->data['formSymptoms'];
        $params['DRUG_TYPE']                = $this->data['formType'];
        $params['DRUG_DOSE']                = $this->data['formType_amount'];
        $params['DRUG_STORAGE_METHOD']      = $this->data['formKeep'];
        $params['DRUG_TM']                  = $this->data['formTime'];
        $params['DRUG_TIMES']               = $this->data['formNum'];
        $params['REQ_COMMENT']              = $this->data['REQ_COMMENT'];
        $params['REQ_USER_ID']              = $this->user_id;
        $params['UPT_DTTM']                 = date("Y-m-d H:i:s");
        $params['UPT_USER_ID']              = $this->user_id;

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
        $result = $medicineModel->updateProc($params);
        
        if ($result){

        $students = new \App\Models\Students();
        $stdInfo = $students->getUserInfo($STD_ID);    

        $teacherModel = new \App\Models\Teacher();  
        $teacher_id = []; // push message
        foreach ($this->stdInfo['CLASSNOR'] as $class){
            $teacherarray = $teacherModel->ClassTimeTableInfoFromClassCd( $class['CLASS_CD'] );
            $classInfo = $teacherModel->ClassInfofromClassCd( $class['CLASS_CD'] );
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
                'ACA_ID' => $ACA_ID , 
                'TITLE' => '[투약의뢰서]' . $teacher['class_name'] . "-" . $stdInfo->USER_NM .'원생의 투약의뢰서', 
                'MESSAGE' => '[투약의뢰서]' . $teacher['class_name'] . "-" . $stdInfo->USER_NM .'원생의 투약의뢰서가 수정등록 되었습니다. ' , 
                'REQUEST_PATH' => '/medicine/' . $this->data['seq'], 
                "INSERT_USER_ID" => $this->user_id,
                "INSERT_DTTM" => date("Y-m-d H:i:s")
            ];

            $pushmessage->insert($pushparams);
        }


            return json_encode(['status'=> 'success','msg'=> '수정되었습니다.' , 'redirect_to' => "/medicine/" . $params['MEDI_REQ_NO'] ]);
        } else {
            return json_encode(['status'=> 'success','msg'=> '수정실패' ]);
        }
        
    }
}

