<?php

namespace App\Controllers;

use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;
use CodeIgniter\Files\File;


class Schoolmeal extends BaseController
{
    public $pagename = '오늘의급식';
    public $pn = 'schoolmeal';

    protected $data;
    protected $meal;
    protected $authinfo;
    protected $user_id;
    protected $userinfo;
    protected $is_teacher;
    protected $stdInfo;
    protected $aca_id;
    protected $class_list;
    protected $year;

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
    }

    public function index(){
        $this->list();
    }

    public function list(){
        
        $content = $this->getList();

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'html' => $content,
            'auth' => $this->userinfo,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('schoolmeal/schoolmeal', $data , 'sub');
    }

    public function getList(){
        
        $meal = new \App\Models\SchoolMeal();
        $request = \Config\Services::request();
        $today = date("Y-m-d");
        $page = $request->getVar('more') == null || $request->getVar('more') == '' ? 1 : $request->getVar('more') ;

        $params = [
            'ACA_ID' => $this->aca_id,
            'TODAY' => $today
        ];

        if ( $request->getVar('more') == null || $request->getVar('more') == '' || $request->getVar('more') == 0) {
            $list = $meal->list($params) ;
            $c = [];
            foreach ( $list as $a ){
                $fpa = $meal->_aca_meal_daily_file_select([
                    "ACA_ID" => $params['ACA_ID'],
                    'MEAL_DT' => $params['TODAY'],
                    'MEAL_TP'  => $a->MEAL_TP
                ]);
                $c[]     = [
                    'GB'       => $a->GB,
                    'MEAL_TP'       => $a->MEAL_TP,
                    'MEAL_NM'       => $a->MEAL_NM,
                    'MEAL_DESC'       => $a->MEAL_DESC,
                    'SNACK_DESC'       => $a->SNACK_DESC,
                    'MEAL_DT'       => $a->MEAL_DT,
                    'ENT_DTTM'       => $a->ENT_DTTM,
                    'TEACHER_NM'       => $a->TEACHER_NM,
                    'ACA_ID'       => $a->ACA_ID,
                    'images' => $fpa
                ];
            }

            $data[] = [ 'data' => $c , 'today' => $params['TODAY'] ];
        

        } else {
        
            $per_page = 3;
            $page_first = ($page-1)*$per_page + 1;
            for($i = $page_first ; $i <= ($page*$per_page) ; $i++){
                
                $params['TODAY'] = date("Y-m-d" , strtotime($today . " -" . $i . " day"));
                $c = [];
                foreach ( $meal->list($params) as $a ){
                    $fpa = $meal->_aca_meal_daily_file_select([
                        "ACA_ID" => $params['ACA_ID'],
                        'MEAL_DT' => $params['TODAY'],
                        'MEAL_TP'  => $a->MEAL_TP
                    ]);
                    $c[]     = [
                        'GB'       => $a->GB,
                        'MEAL_TP'       => $a->MEAL_TP,
                        'MEAL_NM'       => $a->MEAL_NM,
                        'MEAL_DESC'       => $a->MEAL_DESC,
                        'SNACK_DESC'       => $a->SNACK_DESC,
                        'MEAL_DT'       => $a->MEAL_DT,
                        'ENT_DTTM'       => $a->ENT_DTTM,
                        'TEACHER_NM'       => $a->TEACHER_NM,
                        'ACA_ID'       => $a->ACA_ID,
                        'images' => $fpa
                    ];

                }
                $data[] = [ 'data' => $c , 'today' => $params['TODAY'] ];
            }
        }
        
        
        ob_start();
        foreach ($data as $dd){
            $data1 = [
                'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
                'list' => $dd,
                'auth' => $this->userinfo,
                'is_teacher' => $this->is_teacher
            ];

            $this->template('schoolmeal/schoolmealmore', $data1 , 'none');
        }

        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    // more
    public function moreschoolmeal(){
        $content = $this->getList();
        return $content;
    }

    public function write(){
        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'auth' => $this->userinfo,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('schoolmeal/todaywrite', $data , 'sub');
    }

    public function proc($func){
        $content = trim(file_get_contents("php://input"));
        $this->data = json_decode($content, true);


        if ($content == "" && ( $_REQUEST ) ){
            $this->data = $_REQUEST;
        }

        return $this->{$func}();
    }

    public function todaywriteProc(){
        $meal = new \App\Models\SchoolMeal();

        if ( is_array ( $this->data['MEAL_DESC'] ) ) {
            $MEAL_DESC = implode("/" , $this->data['MEAL_DESC']);
        } else {
            $MEAL_DESC = $this->data['MEAL_DESC'];
        }

        if ( is_array ( $this->data['SNACK_DESC'] ) ) {
            $SNACK_DESC = implode("/" , $this->data['SNACK_DESC']);
        } else {
            $SNACK_DESC = isset($this->data['SNACK_DESC']) ? $this->data['SNACK_DESC'] : '';
        }

        date_default_timezone_set('ASIA/SEOUL');
        $params = [
            "ACA_ID"   => $this->data['ACA_ID'] ,
            "MEAL_DT"   => $this->data['MEAL_DT'] ,
            "MEAL_TP"   => 'B',
            "MEAL_NM"   => $this->data['MEAL_NM'],
            "MEAL_DESC"   => $MEAL_DESC,
            "SNACK_DESC"   => $SNACK_DESC,
            "VIEW_YN" => 'Y',
            "ENT_DTTM" => date("Y-m-d H:i:s"),
            "ENT_USER_ID" => $this->data['USER_ID']
        ];
        $result = $meal->_aca_meal_daily_insert($params);

        if (!$result) {
            return json_encode(['status' => 'fail' , 'msg' => "이미 등록된 급식이 있습니다. "]);
        }

        // 파일 등록 
        if ( isset( $this->data['files'] ) && $this->data['files'] != '' ) {
            $files = json_decode( $this->data['files'] , true );
            // var_dump($files);
            foreach ( $files as $file){
                $fileparams = [
                    'ACA_ID' => $this->data['ACA_ID'],
                    'MEAL_DT' => $this->data['MEAL_DT'],
                    'MEAL_TP' => 'B',
                    'FILE_TP' => null,
                    'FILE_NM' => $file['FILE_NM'],
                    'FILE_PATH' => $file['FILE_PATH'],
                    'FILE_EXT' => $file['FILE_EXT'],
                    'FILE_URL' => $file['FILE_URL'],
                    'ENT_DTTM' => date("Y-m-d H:i:s"),
                    'ORIGIN_FILE_NM' => $file['ORIGIN_FILE_NM'],
                    'FILE_SIZE' => $file['FILE_SIZE']
                ];
                $meal->_aca_meal_daily_file_insert($fileparams);
            }
        }

        return json_encode(['status' => 'success' , 'msg' => "등록되었습니다." , 'redirect_to' => "/schoolmeal/"]);
    }

    public function edit($enc){
        $enc_request = json_decode( base64_decode($enc) , true );

        $params['ACA_ID'] = $enc_request['ACA_ID'];
        $params['MEAL_TP'] = $enc_request['MEAL_TP'];
        $params['MEAL_DT'] = $enc_request['MEAL_DT'];

        $meal = new \App\Models\SchoolMeal();
        
        $list = $meal->detail($params) ;
        
        $fpa = $meal->_aca_meal_daily_file_select([
            "ACA_ID"    => $list->ACA_ID,
            'MEAL_DT'   => $list->MEAL_DT,
            'MEAL_TP'   => $list->MEAL_TP
        ]);
        $c = [
            'GB'            => $list->GB,
            'MEAL_TP'       => $list->MEAL_TP,
            'MEAL_NM'       => $list->MEAL_NM,
            'MEAL_DESC'     => $list->MEAL_DESC,
            'SNACK_DESC'    => $list->SNACK_DESC,
            'MEAL_DT'       => $list->MEAL_DT,
            'ENT_DTTM'      => $list->ENT_DTTM,
            'TEACHER_NM'    => $list->TEACHER_NM,
            'ACA_ID'        => $list->ACA_ID,
            'images'        => $fpa,
            'enc'           => $enc
        ];

        $data = [ 
                'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
                'data' => $c ,
                'auth' => $this->userinfo,
                'is_teacher' => $this->is_teacher
            ];                

        return $this->template('schoolmeal/todayedit', $data , 'sub');
    }
    
    public function todaywriteEdit(){
        $meal = new \App\Models\SchoolMeal();

        if ( is_array ( $this->data['MEAL_DESC'] ) ) {
            $MEAL_DESC = implode("/" , $this->data['MEAL_DESC']);
        } else {
            $MEAL_DESC = $this->data['MEAL_DESC'];
        }

        if ( is_array ( $this->data['SNACK_DESC'] ) ) {
            $SNACK_DESC = implode("/" , $this->data['SNACK_DESC']);
        } else {
            $SNACK_DESC = isset($this->data['SNACK_DESC']) ? $this->data['SNACK_DESC'] : '';
        }

        date_default_timezone_set('ASIA/SEOUL');
        $params = [
            "ACA_ID"   => $this->data['ACA_ID'] ,
            "MEAL_DT"   => $this->data['MEAL_DT'] ,
            "MEAL_TP"   => 'B',
            "MEAL_NM"   => $this->data['MEAL_NM'],
            "MEAL_DESC"   => $MEAL_DESC,
            "SNACK_DESC"   => $SNACK_DESC,
            "VIEW_YN" => 'Y',
            "UPT_DTTM" => date("Y-m-d H:i:s"),
            "UPT_USER_ID" => $this->data['USER_ID']
        ];
        $result = $meal->_aca_meal_daily_update($params);

        if (!$result) {
            return json_encode(['status' => 'fail' , 'msg' => "오늘의 급식의 내용이 수정에 실패하였습니다. "]);
        }

        // 파일 등록 
        if ( isset( $this->data['files'] ) && $this->data['files'] != '' ) {
            $files = json_decode( $this->data['files'] , true );
            // var_dump($files);
            foreach ( $files as $file){
                $fileparams = [
                    'ACA_ID' => $this->data['ACA_ID'],
                    'MEAL_DT' => $this->data['MEAL_DT'],
                    'MEAL_TP' => 'B',
                    'FILE_TP' => null,
                    'FILE_NM' => $file['FILE_NM'],
                    'FILE_PATH' => $file['FILE_PATH'],
                    'FILE_EXT' => $file['FILE_EXT'],
                    'FILE_URL' => $file['FILE_URL'],
                    'ENT_DTTM' => date("Y-m-d H:i:s"),
                    'ORIGIN_FILE_NM' => $file['ORIGIN_FILE_NM'],
                    'FILE_SIZE' => $file['FILE_SIZE']
                ];
                $meal->_aca_meal_daily_file_insert($fileparams);
            }
        }

        return json_encode(['status' => 'success' , 'msg' => "수정되었습니다." , 'redirect_to' => "/schoolmeal/"]);
    }

    public function todaywriteDelete(){
        
        $meal = new \App\Models\SchoolMeal();

        $file = new \App\Controllers\FileUpload();

        $enc = $this->data['enc'];
        $enc_request = json_decode( base64_decode($enc) , true );

        $params['ACA_ID'] = $enc_request['ACA_ID'];
        $params['MEAL_TP'] = $enc_request['MEAL_TP'];
        $params['MEAL_DT'] = $enc_request['MEAL_DT'];

        // 파일 삭제 
        $fpa = $meal->_aca_meal_daily_file_select([
            "ACA_ID"    => $params['ACA_ID'],
            'MEAL_DT'   => $params['MEAL_DT'],
            'MEAL_TP'   => $params['MEAL_TP']
        ]);

        if (is_array($fpa)){
            foreach ( $fpa as $f ){
                $param = ['seq' => $f['APND_FILE_SEQ'] , 'url' => $f['FILE_PATH'] . "/" . $f['FILE_NM'] . '.' . $f['FILE_EXT'] , 'tb' => '_ACA_MEAL_DAILY_APND_FILE'] ;
                $file->removeFile($param);
            }
        }
        
        $result = $meal->_aca_meal_daily_delete($params);        

        if ($result == false){
            return json_encode(['status' => 'fail' , 'msg' => "삭제실패"]);
        } else {
            return json_encode(['status' => 'success' , 'msg' => "삭제되었습니다." , 'redirect_to' => "/schoolmeal/"]);
        }


        // 
    }
}

    
