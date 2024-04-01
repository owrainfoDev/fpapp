<?php
    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\BaseBuilder;
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class EduPlan extends Model{
        protected $table      = 'TB_CLASS_EDU_PLAN'; // 테이블명

        protected $primaryKey = 'EDU_PLAN_NO'; // primary key 컬럼명
    
        protected $useAutoIncrement = false; // auto_increment 사용 여부
    
        protected $returnType = 'object'; // 검색 결과 반환 타입
    
        protected $allowedFields = ['EDU_PLAN_NO','ACA_ID','EDU_PLAN_GB','EDU_PLAN_YM','EDU_PLAN_WEEK','EDU_PLAN_NM','EDU_PLAN_DESC','PLAN_FILE_PATH1','PLAN_FILE_PATH2','FILE_ORG_NAME','FILE_NAME','FILE_SIZE','FILE_PATH','FILE_EXT','COMM_CHK','OPEN_YN','USE_YN','ENT_DTTM','ENT_USER_ID','UPT_DTTM','UPT_USER_ID','DEL_DTTM']; // insert, update 시 사용할 컬럼명

        protected $useSoftDeletes = false;

        protected $useTimestamps = true;
        protected $dateFormat    = 'datetime';
        protected $createdField  = 'ENT_DTTM';
        protected $updatedField  = 'UPT_DTTM';
        protected $deletedField  = 'DEL_DTTM';

        public function _getSeq(){
            $seq = $this->db->query("select FN_GET_JOB_SEQ('". $this->table."') as SEQ ")->getRow(0);
            return $seq->SEQ;
        }

        public function writeProc($params){
            $class_cd = $params['class_cd'];
            $mode = $params['mode'];
            $edu_plan_no = $params['EDU_PLAN_NO'];
            unset($params['class_cd']);
            unset($params['mode']);
            if ( $mode == "edit" ) {
                unset($params['EDU_PLAN_NO']);

                $this->builder()->set($params)->where('EDU_PLAN_NO',$edu_plan_no)->update();
                
                $db = db_connect();
                $db->table('TB_CLASS_EDU_PLAN_CLASS')->where('EDU_PLAN_NO',$edu_plan_no)->delete();
                foreach ($class_cd as $class){
                    $db->table('TB_CLASS_EDU_PLAN_CLASS')->set(['EDU_PLAN_NO' => $edu_plan_no , 'CLASS_CD'=> $class])->insert();
                }
            } else {

                $this->builder()->insert($params);
                foreach ($class_cd as $class){
                    $db = db_connect();
                    $db->table('TB_CLASS_EDU_PLAN_CLASS')->set(['EDU_PLAN_NO' => $params['EDU_PLAN_NO'] , 'CLASS_CD'=> $class])->insert();
                }
            }
            
            return ['data' => $edu_plan_no];
        }

        public function getWeeklyList($params){

            $start = $params['limit'] * ($params['page']-1);
            $classCd = $params['class_cd'];
            $this->builder()->select("TB_CLASS_EDU_PLAN.*");
            $this->builder()->join("TB_CLASS_EDU_PLAN_CLASS as TCEPC" , "TB_CLASS_EDU_PLAN.EDU_PLAN_NO = TCEPC.EDU_PLAN_NO AND TCEPC.CLASS_CD = '" . $classCd . "'");
            $this->builder()->where("TB_CLASS_EDU_PLAN.USE_YN" , 'Y');
            $this->builder()->where('TB_CLASS_EDU_PLAN.EDU_PLAN_GB', 'WEEK');
            $this->builder()->orderBy('TB_CLASS_EDU_PLAN.EDU_PLAN_YM' , 'DESC');
            $this->builder()->orderBy('TB_CLASS_EDU_PLAN.EDU_PLAN_WEEK' , 'DESC');
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

        public function getmonthlyList($params){
            $start = $params['limit'] * ($params['page']-1);
            $classCd = $params['class_cd'];
            $this->builder()->select("TB_CLASS_EDU_PLAN.*");
            $this->builder()->join("TB_CLASS_EDU_PLAN_CLASS as TCEPC" , "TB_CLASS_EDU_PLAN.EDU_PLAN_NO = TCEPC.EDU_PLAN_NO AND TCEPC.CLASS_CD = '" . $classCd . "'");
            $this->builder()->where("TB_CLASS_EDU_PLAN.USE_YN" , 'Y');
            $this->builder()->where('TB_CLASS_EDU_PLAN.EDU_PLAN_GB', 'MON');
            $this->builder()->orderBy('TB_CLASS_EDU_PLAN.EDU_PLAN_YM' , 'DESC');
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

        public function getRelation($seq){
            $db = db_connect();
            $query = $db->table('TB_CLASS_EDU_PLAN_CLASS')->where('EDU_PLAN_NO',$seq)->get();
            return $query->getResult();
        }

        public function _apnd_file_remove($seq){
            
            $params['FILE_ORG_NAME'] = null;
            $params['FILE_NAME'] = null;
            $params['FILE_SIZE'] = null;
            $params['FILE_PATH'] = null;
            $params['FILE_EXT'] = null;
            $params['FILE_URL'] = null;

            $this->builder()->where('EDU_PLAN_NO' , $seq);
            $result = $this->builder()->set($params)->update();
            return ['result' => $result ];
        }

        public function deleteProc($seq){
            return  $this->builder()->where('EDU_PLAN_NO',$seq)->set('USE_YN', 'N')->update();
        }
    }