<?php
    namespace App\Models;

    use CodeIgniter\Model;
    
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class Menu extends Model{
        
        protected $db;

        public function __construct()
        {
            parent::__construct();
            $this->db = \Config\Database::connect();
        }
        
        public function menuList($params){

            // $userid = $params['userid'];
            $is_teacher = $params['is_teacher'];
            $userid = $params['USER_ID'];
            $aca_id = $params['ACA_ID'];
            $year = $params['year'];
            if ($is_teacher == "Y"){
            // 교사일 경우는 반 목록 
            $query =    "SELECT	CLS.CLASS_CD, CLS.CLASS_NM					
              FROM 	TB_CLASS CLS
                    JOIN  (
                                SELECT 	DISTINCT CLASS_CD 
                                  FROM 	TB_CLASS_TIME_TABLE TT										
                                 WHERE	TT.ACA_ID = ?
                                   AND  ( EXISTS (SELECT 	1 
                                                     FROM 	TB_USER TU 
                                                            JOIN TB_USER_TP_AUTH_GRP AG ON AG.USER_ID = TU.USER_ID
                                                                        AND AG.AUTH_GRP_CD IN ('AG0102','AG0104', 'AG0101', 'AG0108')
                                                            JOIN TB_EMP TE ON TE.EMP_ID = TU.USER_ID   
                                                     WHERE 	TU.USER_ID = ?
                                                       AND	( 
                                                                (TE.EMP_TP = '01' AND	TU.USER_ID = TT.TEACHER_ID)
                                                                OR 
                                                                (TE.EMP_TP = '01' AND	TU.USER_ID = TT.TEACHER_ID3)
                                                                or 
                                                                (TE.EMP_TP = '02' AND TU.ACA_ID = TT.ACA_ID)
                                                            )
                                                )
                                        )
                    ) CTT ON CTT.CLASS_CD = CLS.CLASS_CD				
             WHERE 	CLS.ACA_ID 		= ?
               AND  CLS.ACA_YEAR 	= ?
               AND 	CLS.USE_YN = 'Y'
               AND 	CLS.CLASS_STATUS IN ('00','01', '03', '04') 
               AND CLS.CLASS_TP = 'NOR'
                        ;";
                        $data = $this->db->query($query , array($aca_id , $userid , $aca_id , $year ));
                        $result = $data->getResult();    
            } else {
                $user = new \App\Models\AuthorInfo($userid);
                $user->year = $year;
                $result = $user->getChildrenInfo();
            }
            
            return $result;

        }

        // 기준 학사년도
        public function baseAcademicyear(){
            $data = $this->db->query(   "SELECT * FROM TB_CODE WHERE CODE_grp_cd = 'ACA_YEAR' AND USE_YN = 'Y' AND VIEW_YN = 'Y' ORDER BY ORD_NO ASC " );
            $d = [];
            foreach ($data->getResult() as $row) {
                $d[] = [
                    'CODE' => $row->CODE,
                    'CODE_NM' => $row->CODE_NM,
                    'CHK_CD1' => $row->CHK_CD1,
                    'ORD_NO' => $row->ORD_NO
                ];
            }
            return $d;
        }
    }