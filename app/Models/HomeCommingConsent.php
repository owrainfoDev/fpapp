<?php
    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\BaseBuilder;
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class HomeCommingConsent extends Model{
        protected $table      = 'TB_LEAVE_AGREE'; // 테이블명

        protected $primaryKey = 'AGREE_NO'; // primary key 컬럼명
    
        protected $useAutoIncrement = true; // auto_increment 사용 여부
    
        protected $returnType = 'object'; // 검색 결과 반환 타입
    
        protected $allowedFields = ["STD_ID","ACA_ID","LEAVE_AGREE_STATUS","LEAVE_DT","LEAVE_TM","REQ_ID","LEAVE_TP","DEPUTY_NM","DEPUTY_TEL_NO","DEPUTY_REL_CD","EMG_CALL_NM","EMG_CALL_TEL_NO","EMG_CALL_REL_CD","REQ_MEMO","READ_USER_ID","READ_DTTM","CONF_ID","CONF_DTTM","CONF_MEMO","FILE_ORG_NAME","FILE_NAME","FILE_SIZE","FILE_PATH","FILE_EXT","USE_YN","ENT_DTTM","ENT_USER_ID","UPT_DTTM","UPT_USER_ID"]; // insert, update 시 사용할 컬럼명

        protected $useSoftDeletes = false;

        protected $useTimestamps = true;
        protected $dateFormat    = 'datetime';
        protected $createdField  = 'ENT_DTTM';
        protected $updatedField  = 'UPT_DTTM';
        protected $deletedField  = 'DEL_DTTM';

        public function parentList($params){

            $start = $params['limit'] * ($params['page']-1);
            $this->builder()->select("*");
            $this->builder()->where("STD_ID" , $params['STD_ID']);
            $this->builder()->where('USE_YN', 'Y');
            $this->builder()->orderBy('LEAVE_DT','DESC')->orderBy('LEAVE_TM','DESC');
            $this->builder()->limit($params['limit'] , $start);
            $sql = $this->builder()->getCompiledSelect(false);
            $total_row = $this->builder()->countAllResults(false);
            $query = $this->builder()->get();
            $total_page = ceil( $total_row / $params['limit']);
            return [
                'data' => $query->getResult(),// 'data' => $data->getResult(),
                'pager' => $this->pager,
                'total_page' => $total_page,
                'total_row' => $total_row,
                'sql' => $sql
            ];
        }

        public function teacherList($params){
            $start = $params['limit'] * ($params['page']-1);
            $class_cd_ids = [];
            foreach ($params['class_cd'] as $class){
                $class_cd_ids[] = $class;
            }
            // S00011056
            $this->builder()->select('*');
            // $this->builder()->whereIn('STD_ID', ['S00011056']); 
            $this->builder()->whereIn('STD_ID', function (BaseBuilder $subQueryBuilder) use($class_cd_ids){
                return $subQueryBuilder->select("TCS.STD_ID")->from("TB_CLASS_STD TCS")->whereIn('class_cd' , $class_cd_ids);   
            } );
            $this->builder()->where('USE_YN', 'Y');
            if ( $params["unConfirm"] ) $this->builder()->where("LEAVE_AGREE_STATUS", $params["unConfirm"] );
            $this->builder()->orderBy("LEAVE_DT", "DESC");
            $this->builder()->orderBy("LEAVE_TM", "DESC");
            $this->builder()->limit($params['limit'] , $start);
            $query =  $this->builder()->getCompiledSelect(false);

            $total_row = $this->builder()->countAllResults(false);
            $query = $this->builder()->get();
            $data = $query->getResult();
            
            $total_page = ceil( $total_row / $params['limit']);
            return [
                'data' => $data ,// 'data' => $data->getResult(),
                'total_page' => $total_page,
                'total_row' => $total_row
            ];
            
        }

        public function _apnd_file_remove($seq){
            $params['FILE_ORG_NAME'] = null;
            $params['FILE_NAME'] = null;
            $params['FILE_PATH'] = null;
            $params['FILE_EXT'] = null;
            $params['FILE_SIZE'] = null;
            $params['FILE_URL'] = null;
            $this->builder()->where('AGREE_NO', $seq)->set($params)->update();
        }
    }