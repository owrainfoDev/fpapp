<?php
    namespace App\Models;

    use CodeIgniter\Model;
    
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class PushMessage extends Model{
        protected $table      = 'TB_FCM'; // 테이블명
        protected $primaryKey = 'SEQ'; // primary key 컬럼명
    
        protected $useAutoIncrement = true; // auto_increment 사용 여부
    
        protected $returnType = 'array'; // 검색 결과 반환 타입
    
        protected $allowedFields = ['SENDER', 'USER_ID' , 'ACA_ID' , 'TITLE' , 'MESSAGE' , 'REQUEST_PATH', "INSERT_USER_ID","INSERT_DTTM"]; // insert, update 시 사용할 컬럼명

        protected $useTimestamps = false;
        protected $createdField  = 'INSERT_DTTM';
        protected $updatedField  = 'UPT_DTTM';


    }