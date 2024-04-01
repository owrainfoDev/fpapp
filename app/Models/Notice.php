<?php
    namespace App\Models;

    use CodeIgniter\Model;
    
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class Notice extends Model{
        
        protected $db;
        protected $userModel;


        public function __construct()
        {
            parent::__construct();
            $this->db = \Config\Database::connect();
            
        }

        public function lists($params){
            $userid = $params['userid'];
            $aca_id = $params['aca_id'];
            $is_teacher = $params['is_teacher'];
            $search = $params['search'] != "" ? $params['search'] : '' ;

            $checkAuth = $params['checkAuth'];

            $cntquery = "SELECT COUNT(*) as cnt ";

            if ( $is_teacher == 'Y' ) {

            

            $getquery = "SELECT NT.NOTI_SEQ
                            , NT.NOTI_TP
                            , FC_GET_CODE_NM('NOTI_TP', NT.NOTI_TP) AS NOTI_TP_NM
                            , NT.TITLE
                            , DATE_FORMAT(NT.ENT_DTTM, '%Y-%m-%d %H:%i')	AS ENT_DTTM
                            , NT.ENT_USER_ID
                            , CONCAT(NVL(VR.READ_CNT, 0), ' / ', NVL(VR.TOT_CNT, 0)) AS VIEW_CNT
                            , NT.VIEW_YN
                            , NT.USE_YN
                            , NT.CNTS
                            , TU.USER_NM as WRITER_NM
                    ";
            
            $query = " FROM TB_NOTI NT             
                            LEFT OUTER JOIN (
                                    SELECT NOTI_SEQ
                                            , COUNT(1) AS TOT_CNT
                                            , SUM(CASE WHEN NVL(USER_ID, 'X') = 'X' THEN NULL ELSE 1 END ) AS READ_CNT 
                                    FROM TB_NOTI_READ
                                    GROUP BY NOTI_SEQ
                            ) VR  ON VR.NOTI_SEQ = NT.NOTI_SEQ
                            LEFT JOIN TB_USER TU ON NT.ENT_USER_ID = TU.USER_ID
                    WHERE NT.ACA_ID = '" . $aca_id . "'
                    AND	(
                                NT.ENT_USER_ID = '". $userid  ."'
                                OR
                                (	
                                    ( '".$checkAuth."' = 'Y' AND 
                                    EXISTS (SELECT 1
                                                    FROM TB_CLASS_TIME_TABLE CTT 
                                                    WHERE CTT.ACA_ID = '" . $aca_id . "'
                                                    AND 	(
                                                                CTT.TEACHER_ID = '" . $userid . "'
                                                                OR CTT.TEACHER_ID2 = '" . $userid . "'
                                                                OR CTT.TEACHER_ID3 = '" . $userid . "'
                                                            )	
                                                    AND	CTT.CLASS_CD = NT.CLASS_CD
                                                    )                    
                                    )
                                    OR ( '".$checkAuth."' = 'N' AND 1= 1)
                                    AND  NT.VIEW_YN = 'Y'
                                    AND	 NT.USE_YN = 'Y'      
                                )
                            )
                    AND NT.USE_YN = 'Y'
                    ";
            } else { // 직원이 아닐때
                $std_id = $params['std_id'];
                $getquery = "SELECT 
                    NT.NOTI_SEQ,
                    NT.NOTI_TP,
                    FC_GET_CODE_NM('NOTI_TP', NT.NOTI_TP) AS NOTI_TP_NM,
                    NT.TITLE,
                    DATE_FORMAT(NT.ENT_DTTM, '%Y-%m-%d %H:%i')	AS ENT_DTTM,
                    NT.ENT_USER_ID,
                    CONCAT(NVL(VR.READ_CNT, 0), ' / ', NVL(VR.TOT_CNT, 0)) AS VIEW_CNT,
                    NT.VIEW_YN,
                    NT.USE_YN,
                    NT.CNTS,
                    TU.USER_NM as WRITER_NM";

                    $query = " FROM TB_NOTI NT
                    LEFT JOIN TB_USER TU ON NT.ENT_USER_ID = TU.USER_ID
                    LEFT OUTER JOIN (
                                                SELECT NOTI_SEQ
                                                        , COUNT(1) AS TOT_CNT
                                                        , SUM(CASE WHEN NVL(USER_ID, 'X') = 'X' THEN NULL ELSE 1 END ) AS READ_CNT 
                                                FROM TB_NOTI_READ
                                                GROUP BY NOTI_SEQ
                                        ) VR  ON VR.NOTI_SEQ = NT.NOTI_SEQ
                    where
                        EXISTS ( 
                            SELECT 1 FROM TB_NOTI_READ WHERE std_id = '" . $std_id . "' AND noti_seq = NT.NOTI_SEQ
                        )
                    AND NT.ACA_ID = '" . $aca_id . "'
                    AND NT.VIEW_YN = 'Y'
                    AND NT.use_YN = 'Y'";
            }

            if ($search != '') {
                $query .= " AND NT.TITLE like '%". $search ."%' ";
            }

            
            $countData = $this->db->query( $cntquery . $query )->getRow()->cnt ;

            $current = $params['current'];
            $perPage = $params['perPage'];

            $total_page = ceil($countData/$perPage);
            $start = ($current-1)*$perPage;

            
            $limitquery = " limit " . $start . " , " . $perPage  . " ";
            $orderQuery = " ORDER BY NT.NOTI_SEQ DESC ";
            $query = $getquery . $query . $orderQuery . $limitquery  ;


            $data = $this->db->query($query);    

            
            // get_compiled_select
            return array( 
                    'data' => $data->getResult() , 
                    // "sql" => str_replace("  " , '' , str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $query) )
                    "sql" => $query,
                    'count' => count($data->getResult()),
                    'total_row' => $countData,
                    'total_page' => $total_page
            );

        }

        public function detail($noti_seq){

            
            $query = "SELECT TB_NOTI.*,
                (select count(*) from TB_NOTI_READ where TB_NOTI.NOTI_SEQ = TB_NOTI_READ.NOTI_SEQ ) as TCNT,
                (select count(*) from TB_NOTI_READ where TB_NOTI.NOTI_SEQ = TB_NOTI_READ.NOTI_SEQ and TB_NOTI_READ.READ_DTTM is not null  ) as RCNT,
                TU.USER_NM AS WRITE_NM,
                TU.USER_ID 
             FROM TB_NOTI 
             LEFT JOIN TB_USER TU ON TB_NOTI.ENT_USER_ID = TU.USER_ID
             WHERE TB_NOTI.NOTI_SEQ = '" . $noti_seq . "' AND TB_NOTI.USE_YN = 'Y' ";
            $row = $this->db->query($query)->getRow();

            // 수신자 정보 
            $query = "select * from TB_NOTI_READ where NOTI_SEQ = '" . $noti_seq . "' ";
            $read = $this->db->query($query)->getResult();

            return [
                'data' => $row,
                'read' => $read
            ];
        }

        public function get_noti_apnd_file($noti_seq){
            $imageType = ['jpg','gif','png','jpeg','bmp'];
            // 전체
            $query = "SELECT * FROM TB_NOTI_APND_FILE TNAF WHERE TNAF.NOTI_SEQ = '". $noti_seq ."' ";

            $rows = $this->db->query($query)->getResult();
            return ['data' => $rows];
        }

        public function _noti_main_insert($params){
            return $this->db->table('TB_NOTI')->insert($params);
        }

        public function _noti_main_update($params){
            $query = "UPDATE TB_NOTI SET 
                        TITLE           = '" . $params['TITLE'] ."' ,
                        CNTS            = '" . $params['CNTS'] . "' ,
                        UPT_DTTM        = '" . $params['UPT_DTTM'] . "',
                        UPT_USER_ID     = '" . $params['UPT_USER_ID'] ."' 
                    WHERE NOTI_SEQ      = '" . $params['NOTI_SEQ'] ."' 
            ";
            $result = $this->db->query($query);

            return $result;
        }

        public function _noti_main_delete($params){
            $query = "UPDATE TB_NOTI SET 
                        UPT_DTTM        = '" . $params['UPT_DTTM'] . "',
                        UPT_USER_ID     = '" . $params['UPT_USER_ID'] ."' ,
                        USE_YN          = 'N' 
                    WHERE NOTI_SEQ      = '" . $params['NOTI_SEQ'] ."' 
                    AND ENT_USER_ID     = '" . $params['USER_ID'] . "'
            ";
            $result = $this->db->query($query);

            return $result;
        
        }

        public function _noti_std_insert($params){
            return $this->db->table('TB_NOTI_READ')->insert($params);
        }

        public function _getSeq(){
            $noti_seq = $this->db->query("select FN_GET_JOB_SEQ('TB_NOTI') as NOTI_SEQ ")->getRow(0);
            return $noti_seq->NOTI_SEQ;
        }

        public function _noti_apnd_file($params){
            return $this->db->table('TB_NOTI_APND_FILE')->insert($params);
        }

        public function _noti_apnd_file_delete($fileSeq){

            $query = "DELETE from TB_NOTI_APND_FILE WHERE APND_FILE_SEQ = '" . $fileSeq. "'";
            $result = $this->db->query($query);

            if ($result){
                return array(
                    'status'   => "success",
                    "fileSeq" => $fileSeq
                );
            } else {
                return array(
                    'status'   => "fail",
                    "debug" => $query
                );
            }
            
        }

        public function _read_noti_update($noti_seq , $std_id , $user_id , $read_dttm){
            return $this->db->query("
                UPDATE TB_NOTI_READ SET
                    USER_ID = '".$user_id."',
                    READ_DTTM = '".$read_dttm."'
                    WHERE 
                        NOTI_SEQ = '".$noti_seq."' 
                    AND 
                        STD_ID = '".$std_id."'
            ");
        }
    }
