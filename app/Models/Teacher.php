<?php
    namespace App\Models;

    use CodeIgniter\Model;
    
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class Teacher extends Model{
        
        protected $db;

        public function __construct()
        {
            parent::__construct();
            $this->db = \Config\Database::connect();
        }

        public function ClassInfofromClassCd($class_cd , $params = []){
            $query = $this->db->table("TB_CLASS")
                    ->where("CLASS_CD", $class_cd);
            if(! empty($param) ){
                $query = $query->getWhere($param);
            }
            $query = $query->select("*")->get()->getRow();
            return $query;
        }

        public function ClassTimeTableInfoFromClassCd($class_cd , $param = []){
            $query = $this->db->table('TB_CLASS_TIME_TABLE')
                    ->where('CLASS_CD',$class_cd);
            
            if(! empty($param) ){
                $query = $query->getWhere($param);
            }
            $query = $query->select('*')->get()->getResult();

            return $query;
        }

        public function getTeacherFromUserId($user_id){
            $query = $this->db->table('TB_USER')
                ->where('USER_ID',$user_id)
                ->select("*")->get()->getResult();
            
            return $query;
        }

        public function getClassfromTeacherId($params){
            $query = $this->db->query("SELECT  DISTINCT CLS.ACA_YEAR
                        , CLS.ACA_ID
                        , CLS.CLASS_CD, CLS.CLASS_NM
                FROM 	TB_CLASS CLS
                        JOIN  TB_CLASS_TIME_TABLE CTT ON CTT.CLASS_CD = CLS.CLASS_CD
                                AND (
                                        ('{$params['is_teacher']}' = 'Y'  AND 
                                                ( 	CTT.TEACHER_ID = '{$params['user_id']}'
                                                    OR CTT.TEACHER_ID2 = '{$params['user_id']}'
                                                    OR CTT.TEACHER_ID3 = '{$params['user_id']}'
                                                )
                                        ) OR
                                        ('{$params['is_teacher']}' ='N' AND  1=1               )
                                    )
                WHERE  CLS.ACA_ID 		= '{$params['ACA_ID']}'
                AND	CLS.ACA_YEAR 	= '{$params['ACA_YEAR']}'
                AND	CLS.CLASS_TP 	= 'NOR'		-- 정규학급만????  확인 필요
                AND  CLS.USE_YN= 'Y'"
            );
            // echo $this->db->getLastQuery();   
            return $query->getResult();
        }

        // 원장 또는 행정실일 경우 강사 우선 
        public function teacherAuthYn($emp_id){

            $query = $this->db->query( "SELECT COUNT(*) as cnt FROM TB_USER_TP_AUTH_GRP WHERE USER_ID = ? AND AUTH_GRP_CD IN ( 'AG0102' ) " , array( $emp_id ));
            if ( $query->getRow()->cnt ) {
                return 0;    
            } else {

                $query = $this->db->query( "SELECT COUNT(*) as cnt FROM TB_USER_TP_AUTH_GRP WHERE USER_ID = ? AND AUTH_GRP_CD IN ( 'AG0101','AG0104','AG0107','AG0108' ) " , array( $emp_id ));
                return $query->getRow()->cnt;
            }
        }

    }