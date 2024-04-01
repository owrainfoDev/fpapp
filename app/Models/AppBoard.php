<?php
    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\BaseBuilder;
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class AppBoard extends Model{
        protected $table      = 'TB_APP_BOARD'; // 테이블명

        protected $primaryKey = 'AB_NO'; // primary key 컬럼명
    
        protected $useAutoIncrement = true; // auto_increment 사용 여부
    
        protected $returnType = 'object'; // 검색 결과 반환 타입
    
        protected $allowedFields = ["BOARD_TP","ACA_ID","TITLE","CONTENTS","STATUS","VIEW","COMM_CHK","VIEW_YN","USE_YN","ENT_DTTM","ENT_USER_ID","UPT_DTTM","UPT_USER_ID","DEL_USER_ID","DEL_DTTM"]; // insert, update 시 사용할 컬럼명

        protected $useSoftDeletes = false;

        protected $useTimestamps = true;
        protected $dateFormat    = 'datetime';
        protected $createdField  = 'ENT_DTTM';
        protected $updatedField  = 'UPT_DTTM';
        // protected $deletedField  = 'DEL_DTTM';

        public function getList($params){

            $start = $params['limit'] * ($params['page']-1);
            $class_cd = $params['class_cd'];
            $search = $params['search'];
            $aca_id = $params['aca_id'];
            
            
            $this->builder()->select("*");
            $this->builder()->groupStart();
                $this->builder()->groupStart();
                    $this->builder()->whereIn("AB_NO" , function (BaseBuilder $subQueryBuilder) use($class_cd){
                        return $subQueryBuilder->select("TABC.AB_NO")->from("TB_APP_BOARD_CLASS TABC")->whereIn('class_cd' , $class_cd);   
                    });
                    $this->builder()->where('BOARD_TP' , '02');
                $this->builder()->groupEnd();
                $this->builder()->orGroupStart();
                    $this->builder()->orWhere('BOARD_TP' ,'01');
                    $this->builder()->where('ACA_ID',$aca_id);
                $this->builder()->groupEnd();
                $this->builder()->orGroupStart();
                    $this->builder()->orwhere('COMM_CHK' , 'Y');
                $this->builder()->groupEnd();
            $this->builder()->groupEnd();
            $this->builder()->where('USE_YN' , 'Y');
            $this->builder()->where('view_YN' , 'Y');
            $this->builder()->where('STATUS' , '01');
            if ($search){
                $this->builder()->like('CONCAT(TITLE,CONTENTS)', $search);
            }
            $this->builder()->orderBy('AB_NO' , 'DESC');
        
            $this->builder()->limit($params['limit'] , $start);
            $sql = $this->builder()->getCompiledSelect(false);
            
            $total_row = $this->builder()->countAllResults(false);
            $query = $this->builder()->get();
            $data = $query->getResult();
            
            $total_page = ceil( $total_row / $params['limit']);
            return [
                'data' => $data ,// 'data' => $data->getResult(),
                'total_page' => $total_page,
                'total_row' => $total_row,
                'sql' => $sql
            ];
        }

        public function _deleteAppBoardClass($SEQ){
            return $this->builder('TB_APP_BOARD_CLASS')->where("AB_NO", $SEQ)->delete();
        }
        public function _insertAppBoardClass($params){
            return $this->builder('TB_APP_BOARD_CLASS')->insert($params);
        }

        public function _listAppBoardClass($SEQ){
            return $this->builder('TB_APP_BOARD_CLASS')->where("AB_NO", $SEQ)->select("CLASS_CD")->get()->getResultArray();
        }

        public function _apnd_file_insert($params){
            return $this->builder('TB_APP_BOARD_APND_FILE')->insert($params);
        }

        public function _get_file_list($SEQ){
            
            return $this->builder('TB_APP_BOARD_APND_FILE')
                        ->where('AB_NO', $SEQ)
                        ->select("*")
                        ->get()
                        ->getResult();
        }

        public function _apnd_file_remove($SEQ){
            return $this->builder('TB_APP_BOARD_APND_FILE')->where('SEQ', $SEQ)->delete();
        }
    }