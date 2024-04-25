<?php
    namespace App\Models;

    use CodeIgniter\Model;
    
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class SchoolMeal extends Model{
        
        protected $db;

        public function __construct()
        {
            parent::__construct();
            $this->db = \Config\Database::connect();
        }

        public function list($params){

            $ACA_ID = $params['ACA_ID'];
            $TODAY = $params['TODAY'];

            $sql = "SELECT  CASE WHEN MEAL_TP = 'L' THEN '중식' ELSE '간식' END AS GB
                                , MEAL_TP
                                , MEAL_NM
                                , MEAL_DESC
                                , SNACK_DESC
                                , MEAL_DT
                                , TB_ACA_MEAL_DAILY.ENT_DTTM as ENT_DTTM
                                , TU.USER_NM AS TEACHER_NM
                                , TB_ACA_MEAL_DAILY.ACA_ID as ACA_ID
                        FROM 	TB_ACA_MEAL_DAILY 
                        LEFT JOIN TB_USER TU ON TB_ACA_MEAL_DAILY.ENT_USER_ID = TU.USER_ID
                        WHERE 	TB_ACA_MEAL_DAILY.ACA_ID = ?
                            AND  TB_ACA_MEAL_DAILY.MEAL_DT = ?
                            AND TB_ACA_MEAL_DAILY.VIEW_YN = 'Y'
                        ";
            
            $query = $this->db->query($sql , [$ACA_ID , $TODAY]);

            return $query->getResult();
        }

        public function detail($params){


            $ACA_ID = $params['ACA_ID'];
            $MEAL_TP = $params['MEAL_TP'];
            $MEAL_DT = $params['MEAL_DT'];

            $sql = "SELECT  CASE WHEN MEAL_TP = 'L' THEN '중식' ELSE '간식' END AS GB
                                , MEAL_TP
                                , MEAL_NM
                                , MEAL_DESC
                                , SNACK_DESC
                                , MEAL_DT
                                , TB_ACA_MEAL_DAILY.ENT_DTTM as ENT_DTTM
                                , TU.USER_NM AS TEACHER_NM
                                , TB_ACA_MEAL_DAILY.ACA_ID as ACA_ID
                        FROM 	TB_ACA_MEAL_DAILY 
                        LEFT JOIN TB_USER TU ON TB_ACA_MEAL_DAILY.ENT_USER_ID = TU.USER_ID
                        WHERE 	TB_ACA_MEAL_DAILY.ACA_ID = ?
                            AND  TB_ACA_MEAL_DAILY.MEAL_DT = ?
                            AND  TB_ACA_MEAL_DAILY.MEAL_TP = ?
                            AND TB_ACA_MEAL_DAILY.VIEW_YN = 'Y'
                        ";
            
            $query = $this->db->query($sql , [$ACA_ID , $MEAL_DT , $MEAL_TP]);
            return $query->getRow();
        }
        
        public function _aca_meal_daily_insert($params){

            $query = $this->db->table('TB_ACA_MEAL_DAILY')->select('count(*) as cnt')
                    ->where('ACA_ID', $params['ACA_ID'])
                    ->where('MEAL_DT', $params['MEAL_DT'])
                    ->where('MEAL_TP', $params['MEAL_TP'])
                    ->get();
            $row = $query->getRow();
            if ($row->cnt > 0){
                return false;
            } else {
                return $this->db->table('TB_ACA_MEAL_DAILY')->insert($params);
            }
        }

        public function _aca_meal_daily_update($params){
            $query = $this->db->table('TB_ACA_MEAL_DAILY')
                        ->set('MEAL_NM' , $params['MEAL_NM'])
                        ->set('MEAL_DESC' , $params['MEAL_DESC'])
                        ->set('SNACK_DESC' , $params['SNACK_DESC'])
                        ->set('UPT_DTTM' , $params['UPT_DTTM'])
                        ->set('UPT_USER_ID' , $params['UPT_USER_ID'])
                        ->set('VIEW_YN' , $params['VIEW_YN'])
                        ->where('ACA_ID', $params['ACA_ID'])
                        ->where('MEAL_DT', $params['MEAL_DT'])
                        ->where('MEAL_TP', $params['MEAL_TP'])
                        ->update();
            return $query;
        }

        public function _aca_meal_daily_file_insert($params){
            return $this->db->table('TB_ACA_MEAL_DAILY_APND_FILE')->insert($params);
        }

        

        public function _aca_meal_daily_file_select($params){
            $query = $this->db->table('TB_ACA_MEAL_DAILY_APND_FILE')
                    ->select("APND_FILE_SEQ AS SEQ , FILE_NM AS FILE_NAME , FILE_PATH , FILE_EXT , FILE_URL , THUMBNAIL , FILE_SIZE , ORIGIN_FILE_NM AS FILE_ORG_NAME")
                    ->where('ACA_ID', $params['ACA_ID'])
                    ->where('MEAL_DT', $params['MEAL_DT'])
                    ->where('MEAL_TP', $params['MEAL_TP']);
            $result = $query->get()->getResult();
            if (!$result){
                return false;
            } else {
                return $result;
            }
        }

        /**
         * 첨부 파일 삭제
         */
        public function _aca_meal_daily_file_remove($APND_FILE_SEQ){
            $result = $this->db->table('TB_ACA_MEAL_DAILY_APND_FILE')
                    ->where('APND_FILE_SEQ' , $APND_FILE_SEQ)
                    ->delete();
            if ($result){
                return $result;
            } else {
                return false;
            }
        }

        public function _aca_meal_daily_delete($params){
            $result = $this->db->table('TB_ACA_MEAL_DAILY')
                    ->where('ACA_ID', $params['ACA_ID'])
                    ->where('MEAL_DT', $params['MEAL_DT'])
                    ->where('MEAL_TP', $params['MEAL_TP'])
                    ->delete();
            if ($result){
                return $result;
            } else {
                return false;
            }
        }
        

        // 월간 급식
        public function _aca_meal_monthly_list($params , $count = 0){

            $page = $params['page'];
            $per_page = 1;
            $page_first = ($page-1)*$per_page;
            

            $query = $this->db->table('TB_ACA_MEAL_MONTHLY')
                    ->where("TB_ACA_MEAL_MONTHLY.ACA_ID" , $params['ACA_ID'])
                    // ->where("TB_ACA_MEAL_MONTHLY.MEAL_YM" , $params['MEAL_YM'])
                    ->select("TB_ACA_MEAL_MONTHLY.* , TB_USER.USER_NM ")
                    ->join('TB_USER' , "TB_ACA_MEAL_MONTHLY.ENT_USER_ID = TB_USER.USER_ID")
                    ->orderBy('TB_ACA_MEAL_MONTHLY.MEAL_YM DESC');
            
            if ($count > 0) {
                $count = $query->countAllResults();
                return $count;
            }

            if ($page == 1) {
                $query = $query->limit(1);
            } else {
                $query = $query->limit( $page_first , $per_page );
            }
            $query = $query->get()->getResult();
            return $query;
        }

        public function _aca_meal_monthly_detail($params){
            $query = $this->db->table('TB_ACA_MEAL_MONTHLY')
                    ->where("TB_ACA_MEAL_MONTHLY.ACA_ID" , $params['ACA_ID'])
                    ->where("TB_ACA_MEAL_MONTHLY.MEAL_YM" , $params['MEAL_YM'])
                    ->select("TB_ACA_MEAL_MONTHLY.* , TB_USER.USER_NM ")
                    ->join('TB_USER' , "TB_ACA_MEAL_MONTHLY.ENT_USER_ID = TB_USER.USER_ID");
            $query = $query->get()->getRow();

            return $query;
        }

        public function _aca_meal_monthly_insert($params){
            $query = $this->db->table('TB_ACA_MEAL_MONTHLY')->select('count(*) as cnt')
                    ->where('ACA_ID', $params['ACA_ID'])
                    ->where('MEAL_YM', $params['MEAL_YM'])
                    // ->getCompiledSelect();
                    // echo $query;
                    // die();
                    ->get();
            $row = $query->getRow();
            if ($row->cnt > 0){
                return false;
            } else {
                return $this->db->table('TB_ACA_MEAL_MONTHLY')->insert($params);
            }
        }

        public function _aca_meal_monthly_file_update($params){
            $data = [
                'FILE_ORG_NAME' => $params['FILE_ORG_NAME'],
                'FILE_NAME' => $params['FILE_NAME'],
                'FILE_SIZE' => $params['FILE_SIZE'],
                'FILE_PATH' => $params['FILE_PATH'],
                'FILE_EXT' => $params['FILE_EXT'],
                'FILE_URL' => $params['FILE_URL']
            ];
            $query = $this->db->table("TB_ACA_MEAL_MONTHLY")
                        ->where('ACA_ID', $params['ACA_ID'])
                        ->where('MEAL_YM', $params['MEAL_YM'])
                        ->update($data);

            if (!$query) return false;
            else return $query;
        }

        public function _aca_meal_monthly_file_remove($enc){
            $enc_request = json_decode( base64_decode($enc) , true );
            $params['ACA_ID'] = $enc_request['ACA_ID'];
            $params['MEAL_YM'] = $enc_request['MEAL_YM'];

            $data = [
                'FILE_ORG_NAME' => '',
                'FILE_NAME' => '',
                'FILE_SIZE' => '',
                'FILE_PATH' => '',
                'FILE_EXT' => '',
                'FILE_URL' => ''
            ];

            $query = $this->db->table('TB_ACA_MEAL_MONTHLY')
            ->where('ACA_ID' , $params['ACA_ID'])
            ->where('MEAL_YM' , $params['MEAL_YM'])
            ->set($data)
            ->update();

            if (!$query) return false;
            else return $query;
        }

        public function _aca_meal_monthly_update($params){
            
            $dataset = [
                "MEAL_NM"       => $params["MEAL_NM"],
                "VIEW_YN"       => $params["VIEW_YN"],
                "UPT_DTTM"      => $params["UPT_DTTM"],
                "UPT_USER_ID"   => $params["UPT_USER_ID"]
            ];
            $where = [
                "ACA_ID"   => $params['ACA_ID'],
                "MEAL_YM"   => $params['MEAL_YM']
            ];

            $query = $this->db->table('TB_ACA_MEAL_MONTHLY')
                ->where($where)
                ->set($dataset)
                ->update();
            if (!$query) return false;
            else return $query;
        }

        public function _aca_meal_monthly_delete($params){
            $where = [
                "ACA_ID"   => $params['ACA_ID'],
                "MEAL_YM"   => $params['MEAL_YM']
            ];

            $query = $this->db->table("TB_ACA_MEAL_MONTHLY")
                ->where($where)
                ->delete();
                
            if (!$query) return false;
            else return $query;
        }
    }