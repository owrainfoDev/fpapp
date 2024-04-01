<?php 
namespace App\Controllers;

use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;
use CodeIgniter\Files\File;


class Schoolmealmonthly extends BaseController
{
    public $pagename = '월간식단표';
    public $pn = 'Schoolmealmonthly';

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

    public function proc($func){
        $content = trim(file_get_contents("php://input"));
        $this->data = json_decode($content, true);


        if ($content == "" && ( $_REQUEST ) ){
            $this->data = $_REQUEST;
        }
        return $this->{$func}();
    }

    public function list(){
        $content = $this->getList();

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'html' => $content['html'],
            'cnt' => $content['cnt'],
            'auth' => $this->userinfo,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('schoolmeal/schoolmealmonthly', $data , 'sub');
    }

    public function getList(){
        $meal = new \App\Models\SchoolMeal();
        $request = \Config\Services::request();
        $today = date("Y-m");
        $page = $request->getVar('more') == null || $request->getVar('more') == '' ? 1 : $request->getVar('more') ;
        $params = ['ACA_ID' => $this->aca_id,'MEAL_YM' => $today , 'page' => $page];


        $cnt = $meal->_aca_meal_monthly_list($params , 1);

        $list = $meal->_aca_meal_monthly_list($params);
        

        ob_start();
        foreach ($list as $dd){
            $data1 = [
                'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
                'list' => $dd,
                'auth' => $this->userinfo,
                'is_teacher' => $this->is_teacher,
            ];

            $this->template('schoolmeal/schoolmealmonthlymore', $data1 , 'none');
        }

        $content = ob_get_contents();
        ob_end_clean();
        return [ 'html' => $content , 'cnt' => $cnt ];
    }

    public function morelist(){
        $content = $this->getList();
        return $content['html'];
    }

    public function write(){
        $content = [];
        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'html' => $content,
            'auth' => $this->userinfo,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('schoolmeal/schoolmealmonthlywrite', $data , 'sub');
    }

    public function writeproc(){
        $meal = new \App\Models\SchoolMeal();
        date_default_timezone_set('ASIA/SEOUL');
        
        $params = [
            "ACA_ID"   => $this->data['ACA_ID'] ,
            "MEAL_YM"   => $this->data['MEAL_YM'] ,
            "MEAL_NM"   => $this->data['ACA_NM'] . " " . $this->data['MEAL_YM'] . "식단표",
            "VIEW_YN" => 'Y',
            "ENT_DTTM" => date("Y-m-d H:i:s"),
            "ENT_USER_ID" => $this->data['USER_ID']
        ];

        $result = $meal->_aca_meal_monthly_insert($params);

        if (!$result) {
            return json_encode(['status' => 'fail' , 'msg' => "이미 등록된 월간식단표가 있습니다. "]);
        }

        // 파일 등록 
        if ( isset( $this->data['files'] ) && $this->data['files'] != '' ) {
            $files = json_decode( $this->data['files'] , true );
            // var_dump($files);
            foreach ( $files as $file){
                $fileparams = [
                    'ACA_ID' => $this->data['ACA_ID'],
                    'MEAL_YM' => $this->data['MEAL_YM'],
                    'FILE_ORG_NAME' => $file['ORIGIN_FILE_NM'],
                    'FILE_NAME' => $file['FILE_NM'],
                    'FILE_SIZE' => $file['FILE_SIZE'],
                    'FILE_PATH' => $file['FILE_PATH'],
                    'FILE_EXT' => $file['FILE_EXT'],
                    'FILE_URL' => $file['FILE_URL']
                ];
                $meal->_aca_meal_monthly_file_update($fileparams);
            }
        }
        
        return json_encode(['status' => 'success' , 'msg' => "등록되었습니다." , 'redirect_to' => "/schoolmealmonthly/"]);
    }

    public function edit($enc){
        $meal = new \App\Models\SchoolMeal();

        $enc_request = json_decode( base64_decode($enc) , true );

        $params['ACA_ID'] = $enc_request['ACA_ID'];
        $params['MEAL_YM'] = $enc_request['MEAL_YM'];
        
        $content = $meal->_aca_meal_monthly_detail($params);

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'data' => $content,
            'auth' => $this->userinfo,
            'is_teacher' => $this->authinfo->is_teacher(),
            'enc' => $enc
        ];
        return $this->template('schoolmeal/schoolmealmonthlyedit', $data , 'sub');
    }

    public function editproc(){
        $meal = new \App\Models\SchoolMeal();
        date_default_timezone_set('ASIA/SEOUL');
        
        $params = [
            "ACA_ID"   => $this->data['ACA_ID'] ,
            "MEAL_YM"   => $this->data['MEAL_YM'] ,
            "MEAL_NM"   => $this->data['ACA_NM'] . " " . $this->data['MEAL_YM'] . "식단표",
            "VIEW_YN" => 'Y',
            "UPT_DTTM" => date("Y-m-d H:i:s"),
            "UPT_USER_ID" => $this->data['USER_ID']
        ];

        $result = $meal->_aca_meal_monthly_update($params);

        // 파일 등록 
        if ( isset( $this->data['files'] ) && $this->data['files'] != '' ) {
            $files = json_decode( $this->data['files'] , true );
            // var_dump($files);
            foreach ( $files as $file){
                $fileparams = [
                    'ACA_ID' => $this->data['ACA_ID'],
                    'MEAL_YM' => $this->data['MEAL_YM'],
                    'FILE_ORG_NAME' => $file['ORIGIN_FILE_NM'],
                    'FILE_NAME' => $file['FILE_NM'],
                    'FILE_SIZE' => $file['FILE_SIZE'],
                    'FILE_PATH' => $file['FILE_PATH'],
                    'FILE_EXT' => $file['FILE_EXT'],
                    'FILE_URL' => $file['FILE_URL']
                ];
                $meal->_aca_meal_monthly_file_update($fileparams);
            }
        }
        
        return json_encode(['status' => 'success' , 'msg' => "수정되었습니다.." , 'redirect_to' => "/schoolmealmonthly/edit/" . $this->data['enc']]);
    }

    public function delete(){

        $enc = $this->data['enc'];
        $meal = new \App\Models\SchoolMeal();
        $enc_request = json_decode( base64_decode($enc) , true );

        $params['ACA_ID'] = $enc_request['ACA_ID'];
        $params['MEAL_YM'] = $enc_request['MEAL_YM'];

        $content = $meal->_aca_meal_monthly_detail($params);

        $f = (array)$content;
        $file = new \App\Controllers\FileUpload();
        $param = ['seq' => $enc , 'url' => $f['FILE_PATH'] . "/" . $f['FILE_NAME'] . '.' . $f['FILE_EXT'] , 'tb' => '_ACA_MEAL_MONTHLY'] ;
        $file->removeFile($param);

        $result = $meal->_aca_meal_monthly_delete($params);

        if ($result == false){
            return json_encode(['status' => 'fail' , 'msg' => "삭제실패"]);
        } else {
            return json_encode(['status' => 'success' , 'msg' => "삭제되었습니다." , 'redirect_to' => "/schoolmealmonthly"]);
        }
    }
    
}