<?php
    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\BaseBuilder;
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class Medicine extends Model{
        protected $table      = 'TB_MEDI_REQ'; // 테이블명
        protected $primaryKey = 'MEDI_REQ_NO'; // primary key 컬럼명
    
        protected $useAutoIncrement = true; // auto_increment 사용 여부
    
        protected $returnType = 'array'; // 검색 결과 반환 타입
    
        protected $allowedFields = ['STD_ID','ACA_ID','MEDI_REQ_STATUS','REQ_DT','SYMP_DESC','DRUG_TYPE','DRUG_DOSE','DRUG_STORAGE_METHOD','DRUG_TM','DRUG_TIMES','REQ_COMMENT','REQ_USER_ID','READ_USER_ID','READ_DTTM','MEDI_ID','MEDI_DTTM','MEDI_RSLT_COMMENT','FILE_ORG_NAME','FILE_NAME','FILE_PATH','FILE_EXT','FILE_SIZE','FILE_URL','USE_YN','ENT_USER_ID','UPT_USER_ID','APEND']; // insert, update 시 사용할 컬럼명

        protected $useSoftDeletes = false;

        protected $useTimestamps = true;
        protected $dateFormat    = 'datetime';
        protected $createdField  = 'ENT_DTTM';
        protected $updatedField  = 'UPT_DTTM';
        protected $deletedField  = 'DEL_DTTM';

        public function getList($params){
            
            $start = $params['limit'] * ($params['page']-1);
            $this->builder()->select('*');
            $this->builder()->where('USE_YN' , 'Y');
            $this->builder()->where('REQ_USER_ID' , $params['user_id']);
            $this->builder()->orderBy("REQ_DT", "DESC");
            $this->builder()->orderBy("MEDI_REQ_NO", "DESC");
            $this->builder()->limit($params['limit'] , $start);
            // var_dump($this->builder()->getCompiledSelect());
            // die();
            $total_row = $this->builder()->countAllResults(false);
            $query = $this->builder()->get();
            
            
            $total_page = ceil( $total_row / $params['limit']);
            
            // var_dump($this->builder()->getCompiledSelect());
            return [
                'data' => $query->getResult(),// 'data' => $data->getResult(),
                'pager' => $this->pager,
                'total_page' => $total_page,
                'total_row' => $total_row
            ];
        }
        // 선생님 투약 의뢰서 목록
        public function getTeacherList($params){
            $db = db_connect();
            $start = $params['limit'] * ($params['page']-1);

            $selectClass = $params['selectClass'];
            $selectChild = $params['selectChild'];

            
            if ( $selectClass != '' ) $params['classcd'] = [$selectClass];
            $subQuery = $db->table('TB_CLASS_STD')->select("STD_ID");
            if ( $selectClass != '' ) $params['class_cd'] = [$selectClass];
            if ( $selectChild != '' ) $subQuery = $subQuery->where('STD_ID' , $selectChild);
            if ( !is_array($params['classcd'] ) ) $params['classcd'] = (array) $params['classcd'];
            if ( empty($params['classcd'] ) ) $params['classcd'] = ['999999999999999'];

            $subQuery = $subQuery->whereIn("CLASS_CD" , $params['classcd'])->groupBy('STD_ID');
            $subQuery = $subQuery->getCompiledSelect();

            $classcd = '';
            foreach ($params['classcd'] as $class){
                $classcd .= ",'" . $class . "'";
            }
            $classcd = substr($classcd , 1, strlen($classcd));
            $this->builder()->select('' . $this->table .'.* , TSI.STD_NM as STD_NM , TSI.STD_URL ');
            $this->builder()->join('TB_STD_INFO AS TSI' , 'TSI.STD_ID = ' . $this->table .'.STD_ID' , 'left');
            $this->builder()->where($this->table .'.USE_YN' , 'Y');
            $this->builder()->where($this->table .".STD_ID IN (".$subQuery.")" , NULL , FALSE);
            if ( $params["unConfirm"] ) $this->builder()->where($this->table .".MEDI_REQ_STATUS", $params["unConfirm"] );
            $this->builder()->orderBy($this->table .".REQ_DT", "DESC");
            $this->builder()->orderBy($this->table .".MEDI_REQ_NO", "DESC");
            $this->builder()->limit($params['limit'] , $start);
            
            $total_row = $this->builder()->countAllResults(false);
            $query = $this->builder()->get();
            $data = $query->getResult();
            
            $total_page = ceil( $total_row / $params['limit']);
            // var_dump($this->builder()->getCompiledSelect());
            return [
                'data' => $data ,// 'data' => $data->getResult(),
                'pager' => $this->pager,
                'total_page' => $total_page,
                'total_row' => $total_row
            ];
        }

        public function getDetail($no){
            $this->builder()->select($this->table . '.* , TU1.USER_NM as STUDENT_NAME, TU2.USER_NM as REQ_PARENTS_NM' );
            $this->builder()->where($this->table . '.MEDI_REQ_NO', $no);
            $this->builder()->where($this->table . '.USE_YN','Y');
            $this->builder()->join('TB_USER TU1' , $this->table . ".STD_ID = TU1.USER_ID ");
            $this->builder()->join('TB_USER TU2' , $this->table . ".REQ_USER_ID = TU2.USER_ID ");
            // echo $this->builder()->getCompiledSelect();
            // die();
            $query = $this->builder()->get();   
            return [
                'data' => $query->getRow()// 'data' => $data->getResult(),
            ];
        }

        public function updateProc($params){
            $MEDI_REQ_NO = $params['MEDI_REQ_NO'];
            unset($params['MEDI_REQ_NO']);
            
            $this->builder()->where('MEDI_REQ_NO' , $MEDI_REQ_NO);
            $this->builder()->set($params);
            // $sql =  $this->builder()->getCompiledUpdate();
            // echo $sql;
            $result = $this->builder()->update($params);

            return ['result' => $result ];
        }

        public function _apnd_file_remove($seq){
            $params['MEDI_REQ_NO'] = $seq;
            $params['FILE_ORG_NAME'] = null;
            $params['FILE_NAME'] = null;
            $params['FILE_PATH'] = null;
            $params['FILE_EXT'] = null;
            $params['FILE_SIZE'] = null;
            $params['FILE_URL'] = null;
            $this->updateProc($params);
        }

        public function getTeacherListMedi($params){
            
            
            
        }

        
    }

