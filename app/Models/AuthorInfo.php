<?php
    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Exceptions\ModelException;
    
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class AuthorInfo extends Model{
        
        protected $db;
        protected $userid;
        public $year;

        public function __construct($userid)
        {
            parent::__construct();
            $this->db = \Config\Database::connect();
            // if ($userid === null ) redirect('/');
            $this->userid = $userid;
        }
        
        // 직원 정보 정보
        public function teacherinfo(){
            $data = $this->db->query("
                SELECT  TU.* , TA.ACA_NM
                FROM  TB_USER TU
                    JOIN TB_EMP TE ON TE.EMP_ID = TU.USER_ID AND TE.EMP_TP IN ('01', '02')
                    LEFT JOIN TB_ACA TA ON TU.ACA_ID = TA.ACA_ID
            WHERE 	TU.USER_ID = '".$this->userid."'
                AND  TU.USE_YN  = 'Y'
            ;
            ");    

            return $data->getRow();
        }
        // 학부모 정보
        public function parentInfo(){

            $sql = "SELECT TU.* , TA.ACA_NM FROM TB_USER TU
                        JOIN (
                            SELECT TP.parent_id , GROUP_CONCAT(DISTINCT TP.STD_ID) AS chlid_ids , COUNT(*) FROM TB_PARENTS TP GROUP BY TP.PARENT_ID 
                        ) AS ttt ON TU.USER_ID = ttt.parent_id
                        LEFT JOIN TB_ACA TA ON TU.ACA_ID = TA.ACA_ID
            WHERE TU.user_ID = '". $this->userid ."' ";

            $data = $this->db->query($sql);
            return $data->getRow();
        }

        public function childinfo(){
            $sql = "SELECT TP.STD_ID FROM TB_PARENTS TP
                        LEFT JOIN TB_USER TU ON TU.USER_ID = TP.PARENT_ID 
                        LEFT JOIN TB_STD_INFO SI ON SI.STD_ID = TP.STD_ID 
                    WHERE TP.PARENT_ID = '".$this->userid."'
                        AND SI.STD_STATUS NOT IN ( '03' , '99' )
                    ";

            $data = $this->db->query($sql);
            return $data->getResult();
        }

        // 학생 정보
        public function stdInfo($userid){
            $data = $this->db->query(   "SELECT 
                TU.ACA_ID , TU.USER_NM, TU.ADDR, TU.ADDR_DTL, TU.EMAIL, TU.ZIP_CODE, TU.ENT_DTTM, TU.HP_NO , TU.PROFILE , TSI.STD_URL
                , TA.ACA_NM , TSI.STD_ID FROM TB_USER TU
            JOIN TB_STD_INFO TSI ON TU.USER_ID = TSI.STD_ID
            LEFT JOIN TB_ACA TA ON TU.ACA_ID = TA.ACA_ID
            WHERE TU.USER_ID = '".$userid."' AND TSI.STD_STATUS = '01' ");    
            
            $data1 = $data->getRowArray();
            $data1 = ! empty($data1) ? $data1 : [];
            $param = array('userid' => $userid , 'year' => $this->year);
            $classes =  $this->stdClassInfo($param);
            // 담임 클래스
            $classnor = [];
            foreach ($classes as $class){
                if ( $class['CLASS_TP'] == 'NOR') {
                    $classnor[] = $class;
                } else continue;
            }
            $data2 = array('CLASS' => $classes , 'CLASSNOR' => $classnor);
            $data2 = ! empty($data2) ? $data2 : [];

            $data = array_merge($data1 , $data2);

            return $data;
        }

        public function stdClassInfo($param){
            $userid = $param['userid'];
            $year = $param['year'];
            $sql = "SELECT TCS.CLASS_CD , TCS.STD_ID , TCS.ACA_ID , TCS.START_DT , TC.CLASS_NM , TCS.CLASS_APPLY_STATUS , TC.CLASS_TP , 
                        CASE 
                            WHEN TCS.CLASS_APPLY_STATUS = '01' THEN '수강중'
                            WHEN TCS.CLASS_APPLY_STATUS = '03' THEN '종강'
                            ELSE ''
                        END AS STATUS_NM,
                        TCO.CODE_NM
                        FROM TB_CLASS_STD TCS
                        JOIN TB_CLASS TC ON TCS.CLASS_CD = TC.CLASS_CD
                        LEFT JOIN TB_CODE TCO ON TCS.CLASS_APPLY_STATUS = TCO.CODE AND TCO.CODE_GRP_CD = 'CLASS_APPLY_STATUS'
                    WHERE TCS.Class_apply_status in ( '01' , '03' )  AND TCS.STD_ID = '".$userid."' AND TC.ACA_YEAR = '".$year."' ";

            $data = $this->db->query($sql);
            return $data->getResultArray();
        }

        public function getChildrenInfo(){
            $child = $this->childinfo();
            $stdinfo = [];
            foreach ($child as $c){
                $stdinfo[] = $this->stdInfo($c->STD_ID);
            }

            return $stdinfo;
        }

        // 교사 인지 아닌지 체크 
        public function is_teacher(){
            $sql = "SELECT count(*) as CNT
            FROM TB_EMP a
            LEFT JOIN TB_USER tu ON a.emp_id = tu.user_id AND tu.use_yn = 'Y'
            LEFT JOIN TB_USER_TP_AUTH_GRP b ON a.EMP_ID = b.USER_ID
            LEFT JOIN TB_CODE c ON c.code_grp_cd = 'AUTH_GRP_CD' AND c.CODE = b.AUTH_GRP_CD
            LEFT JOIN TB_CODE d ON d.code_grp_cd = 'EMP_TP' AND d.CODE = a.EMP_TP
            WHERE a.EMP_TP IN ( '02' , '01' )
            and a.EMP_ID = '" . $this->userid . "' ";
            $data = $this->db->query($sql);    
            $row = $data->getRow();
            return ($row->CNT > 0 ) ? true : false;
        }

        public function info(){
            if ($this->is_teacher() == true){
                $array = $this->teacherinfo();
            } else {
                $array = $this->parentInfo();
            }
            return $array;
        }

    }