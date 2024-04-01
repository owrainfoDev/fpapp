<?php
    namespace App\Models;

    use CodeIgniter\Model;
    
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class Students extends Model{
        
        protected $db;

        public function __construct()
        {
            parent::__construct();
            $this->db = \Config\Database::connect();
        }

        /**
         * 교사 학급 리스트
         */
        public function getClassListFromTeacher($params){
  
            $userid = $params['userid'];
            $aca_id = $params['aca_id'];
            $is_teacher = $params['is_teacher'];
            $year = $params['year'];

            if ( $is_teacher == "Y" ){
                $query = "SELECT	CLS.CLASS_CD, CLS.CLASS_NM					
                FROM 	TB_CLASS CLS
                      JOIN  (
                                  SELECT 	DISTINCT CLASS_CD 
                                    FROM 	TB_CLASS_TIME_TABLE TT										
                                   WHERE	TT.ACA_ID = ?
                                     AND  ( EXISTS (SELECT 	1 
                                                       FROM 	TB_USER TU 
                                                              JOIN TB_USER_TP_AUTH_GRP AG ON AG.USER_ID = TU.USER_ID AND AG.AUTH_GRP_CD IN ('AG0102','AG0104', 'AG0101', 'AG0108')
                                                              JOIN TB_EMP TE ON TE.EMP_ID = TU.USER_ID   
                                                       WHERE 	TU.USER_ID = ?
                                                         AND	( 
                                                                    (TE.EMP_TP = '01' AND	TU.USER_ID = TT.TEACHER_ID)
                                                                    OR 
                                                                    (TE.EMP_TP = '01' AND	TU.USER_ID = TT.TEACHER_ID3)
                                                                    OR
                                                                    (TE.EMP_TP = '02' AND TU.ACA_ID = TT.ACA_ID)
                                                              )
                                                  )
                                          )
                      ) CTT ON CTT.CLASS_CD = CLS.CLASS_CD				
               WHERE 	CLS.ACA_ID 		= ?
                 AND  CLS.ACA_YEAR 	= ?
                 -- AND	CLS.CLASS_TP = 'NOR'			-- 방과후 수업도 포함 
                 AND 	CLS.USE_YN = 'Y'
                 AND 	CLS.CLASS_STATUS IN ('00','01', '03', '04') 
                 ; ";

                $classList = $this->db->query($query , array($aca_id , $userid, $aca_id , $year))->getResult();
                // echo $this->db->lastQuery;
                // die();

            } else {
                $std_id = $params['std_id'];
                $query = "SELECT CLS.ACA_YEAR , CLS.ACA_ID , CLS.CLASS_CD , CLS.CLASS_NM 
                            FROM TB_CLASS CLS 
                                JOIN TB_CLASS_STD TCS 
                                    ON CLS.CLASS_CD = TCS.CLASS_CD 
                                    AND TCS.STD_ID = ?
                                    AND TCS.CLASS_APPLY_STATUS = '01'

                            WHERE 
                                CLS.USE_YN = 'Y' AND CLS.ACA_YEAR = ?
                            ORDER BY 
                                CASE
                                    WHEN CLS.CLASS_TP = 'NOR' THEN 1
                                    ELSE 2
                                END ASC ,
                                CLS.CLASS_CD ASC
                    ";
                    // echo $query;
                    $classList = $this->db->query($query , array( $std_id , $year ))->getResult();
            }

            
            return $classList;
        }

        public function getStudentfromClass($params){

            $class = $params['class_cd'];

            $query = "SELECT * FROM 
            TB_CLASS_STD TCD
            LEFT JOIN TB_STD_INFO TSI ON TCD.STD_ID = TSI.STD_ID AND TSI.STD_STATUS IN ('01', '04', '06', '07')
            WHERE TCD.CLASS_CD = '" . $class . "' AND TCD.CLASS_APPLY_STATUS = '01' ";

            $stdlist = $this->db->query($query)->getResult();

            return $stdlist;
            
        }

        public function getClassCDfromSTDID($params){

            $STD_ID = $params['STD_ID'];
            $YEAR = $params['ACA_YEAR'];
            $query = "SELECT * , TC.CLASS_NM FROM 
            TB_CLASS_STD TCD
            JOIN TB_CLASS TC ON TCD.CLASS_CD = TC.CLASS_CD and TC.ACA_YEAR = '".$YEAR."'
            LEFT JOIN TB_STD_INFO TSI ON TCD.STD_ID = TSI.STD_ID AND TSI.STD_STATUS IN ('01', '04', '06', '07')
            WHERE TCD.STD_ID = '".$STD_ID."' AND TCD.CLASS_APPLY_STATUS = '01'  ";

            $stdlist = $this->db->query($query)->getResult();

            return $stdlist;
            
        }

        public function getParentsInfoFromStudents($userid){
            $query = "SELECT tp.PARENT_ID FROM TB_USER tu 
            LEFT JOIN TB_PARENTS tp ON tp.STD_ID = tu.USER_ID AND tp.REP_PARENT_YN = 'Y'
            WHERE tu.USER_ID = '". $userid ."' ";
            $row = $this->db->query($query)->getrow(0);

            return $row->PARENT_ID;
        }

        public function getUserInfo($userid){
            $query = "SELECT * FROM TB_USER WHERE USER_ID = '" . $userid . "' ";
            $row = $this->db->query($query)->getrow(0);
            return $row;
        }

        public function getStudentsInfo($userid){
            $query = "SELECT * FROM TB_STD_INFO WHERE STD_ID = '" . $userid . "' ";
            $row = $this->db->query($query)->getrow(0);
            return $row;
        }

        public function getClassInfofromClassCd($classCd){
            $query = "select * from TB_CLASS WHERE CLASS_CD = '".$classCd."'";
            return $this->db->query($query)->getrow(0);
        }
        
        // 학원 재원생
        public function getAcaStudentsInfo($ACA_ID){
            $query = "SELECT tp.PARENT_ID FROM TB_STD_INFO tsi
                                JOIN TB_PARENTS tp on tp.STD_ID = tsi.STD_ID AND REP_PARENT_YN = 'Y'
                            WHERE tsi.ACA_ID = ?
                            AND tsi.STD_STATUS = '01'
                        GROUP BY tp.PARENT_ID";
            $rows = $this->db->query($query , [$ACA_ID])->getResult();
            return $rows;
        }

        // 클래스 학원생 학부모
        public function getParentidfromClassCd($cd){
            $query = "SELECT tp.PARENT_ID FROM TB_PARENTS tp WHERE tp.std_id IN (
                            SELECT TCD.STD_ID FROM 
                                        TB_CLASS_STD TCD
                                        LEFT JOIN TB_STD_INFO TSI ON TCD.STD_ID = TSI.STD_ID AND TSI.STD_STATUS IN ('01', '04', '06', '07')
                                        WHERE TCD.CLASS_CD = ? AND TCD.CLASS_APPLY_STATUS = '01' 
                                        )
                                        AND tp.REP_PARENT_YN = 'Y'
                                        GROUP BY tp.PARENT_ID
                            ";
            $rows = $this->db->query($query , [$cd] )->getResult();
            return $rows;
        }
    }
