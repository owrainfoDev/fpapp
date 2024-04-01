<?php
    namespace App\Models;

    use CodeIgniter\Model;
    
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class TempSave extends Model{
        protected $table      = 'TB_TEMPSAVE'; // 테이블명
        protected $primaryKey = 'ID'; // primary key 컬럼명
    
        protected $useAutoIncrement = true; // auto_increment 사용 여부
    
        protected $returnType = 'array'; // 검색 결과 반환 타입
    
        protected $allowedFields = ['TEMP_KEY', 'TEMP_USER_ID' , 'TEMP_VALUE']; // insert, update 시 사용할 컬럼명

        protected $useTimestamps = false;
        protected $createdField  = 'INSERT_DTTM';
        protected $updatedField  = 'UPT_DTTM';

        public function _tempsave_insert($data) {
            $whereData = [
                'TEMP_KEY' => $data['TEMP_KEY'],
                'TEMP_USER_ID' => $data['TEMP_USER_ID']
            ];

            $query = "INSERT INTO " . $this->table . " (TEMP_KEY , TEMP_USER_ID , TEMP_VALUE) values ";
            $query .= "(?,?,?) ON DUPLICATE KEY UPDATE TEMP_VALUE = ? " ;

            $result = $this->db->query($query , array($data['TEMP_KEY'] , $data['TEMP_USER_ID'] , $data['TEMP_VALUE'] , $data['TEMP_VALUE'] ));

            return $result;
        }

        public function _tempsave_delete($data) {   
            $whereData = [
                'TEMP_KEY' => $data['TEMP_KEY'],
                'TEMP_USER_ID' => $data['TEMP_USER_ID']
            ];
            $query = $this->db->table($this->table)
            ->where($whereData)
            ->delete();

            return $query;
        }

        public function _tempsave_get($data) {   
            $whereData = [
                'TEMP_KEY' => $data['TEMP_KEY'],
                'TEMP_USER_ID' => $data['TEMP_USER_ID']
            ];
            $query = $this->db->table($this->table)
            ->where($whereData)
            ->select()->get()->getRow();

            return $query;
        }
    }