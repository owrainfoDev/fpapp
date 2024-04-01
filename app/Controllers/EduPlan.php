<?php

namespace App\Controllers;

use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;
use CodeIgniter\Files\File;


class EduPlan extends BaseController
{
    public $pagename = '교육계획안';
    public $pn = 'eduPlan';

    protected $data;
    protected $meal;
    protected $authinfo;
    protected $user_id;
    protected $userinfo;
    protected $is_teacher;
    protected $year;
    protected $class_list;
    protected $limit = 1;
    protected $stu;

    public function __construct()
    {
        $session = session();
        $this->user_id = $session->get('_user_id');
        $this->authinfo = new \App\Models\AuthorInfo($this->user_id);
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

        if ($this->is_teacher !== true) $params['std_id'] = $session->get("_std_id");

        $this->class_list = $students->getClassListFromTeacher($params);  // 학원 리스트
        $this->stu = $students;
    }

    /** 
     * ajax 분기 처리
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
        $this->weeklyList();
    }

    public function weeklyList(){

        $content = [];
        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'html' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('eduPlan/eduPlan-weekly', $data , 'sub');
    }

    public function weeklyListMore(){

        $eduplan = new \App\Models\EduPlan();

        $page = $this->data['page'];
        $selectClass = $this->data['selectClass'];

        $params = [
            "page" => $page,
            "class_cd"=> $selectClass,
            "limit" => $this->limit
        ];
        $content = $eduplan->getWeeklyList($params);

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'data' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];

        ob_start();
        echo $this->template('eduPlan/weeklymore', $data , 'none');
        $html = ob_get_contents();
        ob_end_clean();
       
        return json_encode([
                'html' => $html,
                'total' => $content['total_row'],
                'sql' => $content['sql']
        ]);
    }

    public function weeklywrite( $seq = null ){

        if ($seq !== null) {
            $eduplan = new \App\Models\EduPlan();
            $content = $eduplan->find( $seq );
            $select_class_cd = $eduplan->getRelation($seq);

            $editfiles = [];
            if (isset($content->FILE_NAME) ){
                $filepath = $content->FILE_PATH . "/" . $content->FILE_NAME . "." . $content->FILE_EXT;
                if (is_file_flag($filepath)){
                    $thumbnail = getThumbnailPreview(WRITEPATH . $filepath);
                    $editfiles[] = [
                        'link' => $filepath,
                        'orgfilename' => $content->FILE_ORG_NAME,
                        'size' => $content->FILE_SIZE,
                        'file_seq' => $content->EDU_PLAN_NO,
                        'ext' => $content->FILE_EXT,
                        'thumbnail' => $thumbnail
                    ];
                }
            } 
        } 

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'content' => isset($content) ? $content : [],
            'select_class_cd' => isset($select_class_cd) ? $select_class_cd : [],
            'auth' => $this->userinfo,
            'editfiles' => $editfiles,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('eduPlan/weeklywrite', $data , 'sub');
    }

    public function writeProc(){
        
        if ($this->data['csrf_token_name'] != csrf_hash()){
            return json_encode(['status'=> 'error','msg'=> '토큰 정보가 다릅니다.']);
        }

        $eduplan = new \App\Models\EduPlan();
        $mode = isset($this->data['EDU_PLAN_NO']) ? "edit" : "write";

        $params = [
            "ACA_ID" => $this->data['ACA_ID'],
            "EDU_PLAN_GB" => $this->data['gb'],
            "EDU_PLAN_YM" => $this->data['month'],
            "EDU_PLAN_NM" => $this->data['noteTitle'],
            "COMM_CHK" => 'N',
            "OPEN_YN" => '01',
            "USE_YN" => 'Y',
            "ENT_USER_ID" => $this->data['USER_ID'],
            "ENT_DTTM" => date('Y-m-d H:i:s'),
            "mode" => $mode
        ];
        $params["EDU_PLAN_WEEK"] = isset( $this->data['selctWeek'] ) ? $this->data['selctWeek'] : '' ;
        $params["class_cd"] = $this->data['classSelect'] == "" ?  $this->data["class_cd"] : [ $this->data['classSelect'] ];
        $params["class_cd"] = is_array( $params["class_cd"] ) ? $params["class_cd"] : (array)$params["class_cd"];


        $params['EDU_PLAN_NO'] = ($mode == "edit") ? $this->data['EDU_PLAN_NO'] : $eduplan->_getSeq() ;
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

        $eduplan->writeProc($params);

        $students = new \App\Models\EduPlan();
        $std_id = [];
        foreach ( $params["class_cd"] as $class_cd ) {
            $std = $this->stu->getStudentfromClass( array( 'class_cd' => $class_cd ) );
            foreach ( $std as $s ){
                $std_id[] = $s->STD_ID;
            }
        }

        $parent_id = [];
        foreach ( $std_id as $ss ){
            $parent_id[] = $this->stu->getParentsInfoFromStudents($ss);
        }

        
        if ( $params['EDU_PLAN_GB'] == "WEEK" ) {
            $sub = "주간";
            $sub_link = "/eduPlan/weekly";
        }
        if ( $params['EDU_PLAN_GB'] == "MON" ) {
            $sub = "월간";
            $sub_link = "/eduPlan/monthly";
        }

        $pushmessage = new \App\Models\PushMessage();
        foreach ( $parent_id as $parent ){

            $pushparams = [
                'SENDER' => $this->user_id, 
                'USER_ID' => $parent, 
                'ACA_ID' => $params['ACA_ID'] , 
                'TITLE' => '['.$sub.'교육계획안] '. $sub .'교육계획안', 
                'MESSAGE' => '['.$sub.'교육계획안] '. $sub .'교육계획안' , 
                'REQUEST_PATH' => $sub_link,
                "INSERT_USER_ID" => $this->user_id,
                "INSERT_DTTM" => date("Y-m-d H:i:s")
            ];
           
            $pushmessage->insert($pushparams);

        }

        if ($params['EDU_PLAN_GB'] == "WEEK") $redirect_to = '/eduPlan/weekly';
        else if ($params['EDU_PLAN_GB'] == 'MON') $redirect_to = '/eduPlan/monthly';

        return json_encode(['status' => 'success' , 'msg'=>'등록성공' , 'redirect_to' => $redirect_to ]);
    }

    public function monthlywrite($seq = null){
        if ($seq !== null) {
            $eduplan = new \App\Models\EduPlan();
            $content = $eduplan->find( $seq );
            $select_class_cd = $eduplan->getRelation($seq);

            $editfiles = [];
            if (isset($content->FILE_NAME) ){
                $filepath = $content->FILE_PATH . "/" . $content->FILE_NAME . "." . $content->FILE_EXT;
                if (is_file_flag($filepath)){
                    $thumbnail = getThumbnailPreview(WRITEPATH . $filepath);
                    $editfiles[] = [
                        'link' => $filepath,
                        'orgfilename' => $content->FILE_ORG_NAME,
                        'size' => $content->FILE_SIZE,
                        'file_seq' => $content->EDU_PLAN_NO,
                        'ext' => $content->FILE_EXT,
                        'thumbnail' => $thumbnail
                    ];
                }
            } 
        } 

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'content' => isset($content) ? $content : [],
            'select_class_cd' => isset($select_class_cd) ? $select_class_cd : [],
            'auth' => $this->userinfo,
            'editfiles' => $editfiles,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('eduPlan/monthlywrite', $data , 'sub');
    }

    public function monthlyList(){

        $content = [];
        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'html' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('eduPlan/eduPlan-monthly', $data , 'sub');
    }

    public function monthlyListMore(){

        $eduplan = new \App\Models\EduPlan();

        $page = $this->data['page'];
        $selectClass = $this->data['selectClass'];

        $params = [
            "page" => $page,
            "class_cd"=> $selectClass,
            "limit" => $this->limit
        ];
        $content = $eduplan->getmonthlyList($params);

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'data' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];

        ob_start();
        echo $this->template('eduPlan/monthlymore', $data , 'none');
        $html = ob_get_contents();
        ob_end_clean();
       
        return json_encode([
                'html' => $html,
                'total' => $content['total_row'],
                'sql' => $content['sql']
        ]);
    }

    public function deleteProc() {
        $content = trim(file_get_contents("php://input"));
        $this->data = json_decode($content, true);

        if ($this->data['csrf_token_name'] != csrf_hash()){
            return json_encode(['status'=> 'error','msg'=> '토큰 정보가 다릅니다.']);
        }

        $eduplan = new \App\Models\EduPlan();
        $result = $eduplan->deleteProc($this->data['seq']);

        return json_encode(['status' => 'success' , 'msg'=>'삭제되었습니다.' , 'redirect_to' => $this->data['page'] ]);

    }

}

