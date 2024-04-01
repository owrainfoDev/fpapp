<?php

namespace App\Controllers;

use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;
use CodeIgniter\Files\File;


class Ajax extends BaseController
{
    public $pagename = '프로세스';
    public $pn = 'ajax';

    protected $data;
    protected $meal;
    protected $authinfo;
    protected $user_id;
    protected $userinfo;
    protected $is_teacher;
    protected $year;
    protected $tempsavemodel;

    public function __construct()
    {
        $session = session();
        $this->user_id = $session->get('_user_id');
        $this->authinfo = new \App\Models\AuthorInfo($this->user_id);
        $this->userinfo = $this->authinfo->info();
        $this->is_teacher = $this->authinfo->is_teacher();
        $this->year = $session->get("year");

        $this->tempsavemodel = new \App\Models\TempSave();
    }

    public function ajaxProc($func){
        $content = trim(file_get_contents("php://input"));
        $this->data = json_decode($content, true);

        if ($content == "" && ( $_REQUEST ) ){
            $this->data = $_REQUEST;
        }
        return $this->{$func}();
    }

    public function getstudentsFromClass(){
        $params = [
            'class_cd' => $this->data['class_cd']
        ];

        $students = new \App\Models\Students();
        $student_list = $students->getStudentfromClass($params);  // 학원 리스트

        return json_encode( $student_list );
    }

    public function tempSave_save() {
        $params = [ 
            'TEMP_KEY'=> $this->data['TEMP_KEY'],
            'TEMP_VALUE' => json_encode( $this->data['TEMP_VALUE'] ),
            'TEMP_USER_ID' => $this->user_id
        ];
        $this->tempsavemodel->_tempsave_insert($params);
        return json_encode(
                array(
                    'status' => 'success',
                    'msg'   => '성공'
                )
                );
    }

    public function tempSave_get() {
        $params = [ 
            'TEMP_KEY'=> $this->data['TEMP_KEY'],
            'TEMP_USER_ID' => $this->user_id
        ];
        $row = $this->tempsavemodel->_tempsave_get($params);
        return json_encode($row);
    }

    public function tempSave_remove() {
        $params = [ 
            'TEMP_KEY'=> $this->data['TEMP_KEY'],
            'TEMP_USER_ID' => $this->user_id
        ];
        
        $this->tempsavemodel->_tempsave_delete( $params );
        return json_encode(array(
            'status' => 'success',
            'msg'   => 'remove'
        ));
    }
}