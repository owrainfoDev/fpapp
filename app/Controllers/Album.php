<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;
use CodeIgniter\Files\File;


class Album extends BaseController
{
    public $pagename = '앨범';
    public $pn = 'album';

    protected $data;
    protected $authinfo;
    protected $user_id;
    protected $userinfo;
    protected $is_teacher;
    protected $year;
    protected $class_list;
    protected $stdInfo;
    protected $aca_id;

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

    /** 
     * ajax 분기 처리
     */
    public function func($func){
        $content = trim(file_get_contents("php://input"));
        $this->data = json_decode($content, true);

        if ($content == "" && ( $_REQUEST ) ){
            $this->data = $_REQUEST;
        }

        if ( ! method_exists($this, $func) ) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->{$func}();
    }
    
    public function index(){
        $this->list();
    }

    public function list(){

        $content = [];

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'html' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('album/album', $data , 'sub');
    }

    public function getList(){

        $request = \Config\Services::request();
        $page =  $request->getGet('more')  == null || $request->getGet('more') == '' ? 1 : $request->getGet('more') ;
        $session = session();
        if ( ! $this->is_teacher && ! $this->data['searchselectChild'] ) {
            $std_id = $session->get("_std_id");
        } else {
            $std_id = $this->data['searchselectChild'];
        }
       
        $search = [
            'selectClass' => $this->data['searchselectClass'],
            'selectChild' => $std_id,
            'noteSearch' => $this->data['searchnoteSearch'],
        ];

        $params = [
            'user_id' => $this->user_id,
            'aca_id' => $this->aca_id,
            'classList' => $this->class_list,
            'year' => $this->year,
            'page' => $page,
            'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];


        $albumModel = new \App\Models\Album();
        $data = $albumModel->getlist($params);

        ob_start();
        foreach ($data['data'] as $dd ){
            $data_sub = [
                'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
                'list' => $dd,
                'is_teacher' => $this->is_teacher,
                'auth' => $this->userinfo,
                'file' => $albumModel->get_album_apnd_file_from_album_no($dd->ALBUM_NO)
            ];
            $this->template('album/albummore', $data_sub , 'none');
        }
        
        $content = ob_get_contents();
        ob_end_clean();


        return [ 'html' => $content , 'sql' => $data ];
    }

    public function listMore(){
        $content = $this->getList();
        return json_encode( [ 'html' => $content['html'] , 'sql' => $content['sql'] ] );
    }

    public function write(){
        $content = [];
        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'html' => $content,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('album/write', $data , 'sub');
    }

    public function writeProc(){
        date_default_timezone_set('ASIA/SEOUL');
        $albumModel = new \App\Models\Album();
        $students = new \App\Models\Students();
        $pushmessage = new \App\Models\PushMessage();

        // $classinfo = $students->getClassInfofromClassCd($this->data['selctClass']);

        
        $aca_id = $this->data['ACA_ID'];
        $class_cd = $this->data['classSelect'];
        $std_id = is_array( $this->data['STD_ID'] ) ? $this->data['STD_ID'] : (array)$this->data['STD_ID'] ;
        $noteTitle = $this->data['noteTitle'];
        $noteTxt = $this->data['noteTxt'];
        $user_id = $this->data['USER_ID'];

        $params = [
            'ACA_ID' => $aca_id,
            'CLASS_CD' => $class_cd,
            'ALBUM_NM' => $noteTitle,
            'CNTS' => str_nl2br($noteTxt),
            'VIEW_YN' => 'Y',
            'USE_YN' => 'Y',
            'ENT_USER_ID' => $user_id,
            'ENT_DTTM' => date("Y-m-d H:i:s")
        ];

        $params['ALBUM_NO'] = $albumModel->_getSeq() ;

        $flag = $albumModel->_album_insert($params);

        if ($flag){
            // 파일 업로드
            if ( isset( $this->data['files'] ) && $this->data['files'] != '' ) {
                $files = json_decode( $this->data['files'] , true );
                foreach ( $files as $file){
                    $fileparams = [
                        'ALBUM_NO' => $params['ALBUM_NO'],
                        'FILE_TP' => null,
                        'FILE_NAME' => $file['FILE_NM'],
                        'FILE_PATH' => $file['FILE_PATH'],
                        'FILE_EXT' => $file['FILE_EXT'],
                        'ENT_DTTM' => date("Y-m-d H:i:s"),
                        'ENT_USER_ID' => $user_id,
                        'FILE_ORG_NAME' => $file['ORIGIN_FILE_NM'],
                        'FILE_URL' => $file['FILE_URL'],
                        'FILE_SIZE' => $file['FILE_SIZE']
                    ];

                    if ( $thumbnail = mp4tojpg( $file['FILE_PATH'] , $file['FILE_NM'] , $file['FILE_EXT'] ) ) {
                        $fileparams['THUMBNAIL'] = "Y";
                    }
                    $albumModel->_album_apnd_file_insert($fileparams);
                }
            }

            // 원생
            foreach ($std_id as $std){
                $subparams = [
                    'ALBUM_NO' => $params['ALBUM_NO'],
                    'STD_ID' => $std,
                    'SEND_DTTM' => date("Y-m-d H:i:s")
                ];

                $albumModel->_album_std_insert($subparams);

                $parant_id = $students->getParentsInfoFromStudents($std);
                $stdinfo = $students->getUserInfo($std);
                
                $pushparams = [
                    'SENDER' => $user_id, 
                    'USER_ID' => $parant_id, 
                    'ACA_ID' => $aca_id , 
                    'TITLE' => '[앨범] - ' . $stdinfo->USER_NM .'원생의 앨범', 
                    'MESSAGE' => '[앨범] - ' . $stdinfo->USER_NM .'원생의 앨범 - ' . $noteTitle , 
                    'REQUEST_PATH' => '/album/' . $params['ALBUM_NO'], 
                    "INSERT_USER_ID" => $user_id,
                    "INSERT_DTTM" => date("Y-m-d H:i:s")
                ];

                $pushmessage->insert($pushparams);
            }

            return json_encode(['status' => 'success' , 'msg'=>'등록성공' , 'redirect_to' => '/album/' . $params['ALBUM_NO'] ]);
        } else {
            return json_encode(['status' => 'fail' , 'msg'=>'등록실패']);
        }
    }

    public function detail($no){
        $albumModel = new \App\Models\Album();
        
        $detail = $albumModel->getDetail($no);

        $std_id = [];
        $std = $this->authinfo->getChildrenInfo();
        foreach ( $std as $s){
            $std_id[] = $s['STD_ID'];
        }

        if ( $this->is_teacher == false ){
            $session = session();
            if ( ! empty( $std_id ) ){
                $params = [
                    'user_id' => $this->user_id,
                    'read_dttm' => date("Y-m-d H:i:s"),
                    'std_id' => $std_id,
                    'album_no' => $no
                ];
                $albumModel->_read_noti_update($params);
            }
        }
        
        if ( ! $detail['album'] ) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'data' => $detail,
            'auth' => $this->userinfo,
            // 'search' => $search,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('album/detail', $data , 'sub');
    }

    public function edit($no){

        $albumModel = new \App\Models\Album();
        $students = new \App\Models\Students();

        $detail = $albumModel->getDetail($no);

        $stdInfo = $students->getStudentfromClass(['class_cd' => $detail['album']->CLASS_CD]);

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'classList' => $this->class_list,
            'studentList' => $stdInfo,
            'data' => $detail,
            'auth' => $this->userinfo,
            'album_no' => $no,
            'is_teacher' => $this->authinfo->is_teacher()
        ];
        return $this->template('album/edit', $data , 'sub');
    }

    public function editProc(){

        $albumModel = new \App\Models\Album();
        $ALBUM_NO = $this->data['album_no'];


        $params = [
            'ALBUM_NM' => $this->data['noteTitle'],
            'CNTS' => str_nl2br($this->data['noteTxt']),
            'UPT_USER_ID' => $this->user_id,
            'UPT_DTTM' => date("Y-m-d H:i:s")
        ];

        $flag = $albumModel->_album_update($params , $ALBUM_NO);

        if ($flag){
            if ( isset( $this->data['files'] ) && $this->data['files'] != '' ) {
                $files = json_decode( $this->data['files'] , true );
                foreach ( $files as $file){
                    $fileparams = [
                        'ALBUM_NO' => $ALBUM_NO,
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
                    $albumModel->_album_apnd_file_insert($fileparams);
                }
            }
        }

        if ($flag){
            return json_encode(['status' => 'success' , 'msg'=>'등록성공' , 'redirect_to' => '/album/' . $ALBUM_NO ]);
        } else {
            return json_encode(['status' => 'fail' , 'msg'=>'등록실패']);
        }
    }

    public function deleteProc(){
        $albumModel = new \App\Models\Album();
        $file = new \App\Controllers\FileUpload();
        $ALBUM_NO = $this->data['album_no'];

        // 파일 삭제 
        $fpa = $albumModel->get_album_apnd_file_from_album_no($ALBUM_NO);

        if (is_array($fpa['data'])){
            foreach ( $fpa['data'] as $f ){
                
                $param = ['seq' => $f->ALBUM_FILE_SEQ , 'url' => $f->FILE_PATH . "/" . $f->FILE_NAME . '.' . $f->FILE_EXT , 'tb' => '_ALBUM_APND_FILE'] ;
                $file->removeFile($param);
            }
        }

        $flag = $albumModel->_delete($ALBUM_NO);

        if ($flag){
            return json_encode(['status' => 'success' , 'msg'=>'삭제성공' , 'redirect_to' => '/album/' ]);
        } else {
            return json_encode(['status' => 'fail' , 'msg'=>'등록실패']);
        }
    }

}

