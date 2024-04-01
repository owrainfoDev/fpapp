<?php
    namespace App\Models;

    use CodeIgniter\Model;
       
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class Album extends Model{
        
        protected $db;

        public function __construct()
        {
            parent::__construct();
            $this->db = \Config\Database::connect();
        }

        public function getlist($params){
            
            $page = $params['page'];
            $per_page = 5;
            $page_first = ($page-1)*$per_page;

            $user_id = $params['user_id'];
            $aca_id = $params['aca_id'];
            $year = $params['year'];
            $is_teacher = $params['is_teacher'];
            $classList = $params['classList'];

            $c = [];
            foreach( $classList as $class ){
                $c[] = $class->CLASS_CD;
            }

            if ($params['search']){
                if ($params['search']['selectClass']){
                    $c = [$params['search']['selectClass']];
                }
                if ($params['search']['selectChild']) {
                    $std_id = $params['search']['selectChild'];
                }
                if ($params['search']['noteSearch']){
                    $noteSearch = $params['search']['noteSearch'];
                }
            }

            $cntQuery = $this->db->table('TB_ALBUM as TA')
                    ->select("count(*) as cnt")
                    ->join('TB_USER AS TU' , 'TA.ENT_USER_ID = TU.USER_ID')
                    ->join('(SELECT 	ALBUM_NO
                                , COUNT(1) AS TOT_CNT
                                , SUM(CASE WHEN NVL(READ_DTTM, \'X\') = \'X\' THEN NULL ELSE 1 END ) AS READ_CNT 
                            FROM 	TB_ALBUM_STD GROUP 	BY ALBUM_NO ) VR' , "VR.ALBUM_NO = TA.ALBUM_NO", "left outer")
                    ->wherein('TA.CLASS_CD' , $c) 
                    ->where('TA.ACA_ID' , $aca_id)
                    ->where('TA.VIEW_YN' , 'Y')
                    ->where('TA.USE_YN' , 'Y');
            if ( $std_id ){
                $cntQuery = $cntQuery->where("EXISTS ( SELECT 1 FROM TB_ALBUM_STD TAS WHERE TAS.ALBUM_NO = TA.ALBUM_NO AND TAS.STD_ID = '".$std_id."' )" );
            }
            if ($noteSearch){
                $cntQuery = $cntQuery->like("concat( TA.ALBUM_NM , TA.CNTS ) ", $noteSearch , 'both');
            }
            $countData = $cntQuery->get()->getRow()->cnt;

            $query = $this->db->table('TB_ALBUM as TA')
                    ->select("TA.* , TU.USER_NM , CONCAT(NVL(VR.READ_CNT, 0), ' / ', NVL(VR.TOT_CNT, 0)) AS VIEW_CNT , NVL(VR.READ_CNT, 0) AS READ_CNT , NVL(VR.TOT_CNT, 0) AS TOT_CNT ")
                    ->join('TB_USER AS TU' , 'TA.ENT_USER_ID = TU.USER_ID')
                    ->join('(SELECT 	ALBUM_NO
                                , COUNT(1) AS TOT_CNT
                                , SUM(CASE WHEN NVL(READ_DTTM, \'X\') = \'X\' THEN NULL ELSE 1 END ) AS READ_CNT 
                            FROM 	TB_ALBUM_STD GROUP 	BY ALBUM_NO ) VR' , "VR.ALBUM_NO = TA.ALBUM_NO", "left outer")
                    ->wherein('TA.CLASS_CD' , $c) 
                    ->where('TA.ACA_ID' , $aca_id)
                    ->where('TA.VIEW_YN' , 'Y')
                    ->where('TA.USE_YN' , 'Y');
            if ( $std_id ){
                $query = $query->where("EXISTS ( SELECT 1 FROM TB_ALBUM_STD TAS WHERE TAS.ALBUM_NO = TA.ALBUM_NO AND TAS.STD_ID = '".$std_id."' )" );
            }
            if ($noteSearch){
                $query = $query->like("concat( TA.ALBUM_NM , TA.CNTS ) ", $noteSearch , 'both');
            }

            $query = $query->orderBy('TA.ALBUM_NO DESC');            
            $total_page = ceil($countData/$per_page);
            $query = $query->limit($per_page , $page_first );     
            // echo $query->getCompiledSelect();
            // die();
            $rows = $query->get()->getResult();

            $sql = $this->db->getLastQuery()->getQuery();

            return [ 'data' => $rows , 'total_row' => $countData , 'total_page' => $total_page, 'params' => $params ] ;
        }

        public function get_album_apnd_file_from_album_no($album_no){
            // 전체
            $query = "SELECT * FROM TB_ALBUM_APND_FILE WHERE ALBUM_NO = '". $album_no ."' ";

            $rows = $this->db->query($query)->getResult();
            return ['data' => $rows];
        }

        public function _getSeq(){
            $seq = $this->db->query("select FN_GET_JOB_SEQ('TB_ALBUM') as SEQ ")->getRow(0);
            return $seq->SEQ;
        }

        public function _album_insert($params){
            return $this->db->table('TB_ALBUM')->insert($params);
        }

        public function _album_update($params , $ALBUM_NO){
           return  $this->db->table('TB_ALBUM')
                    ->set($params)
                    ->where('ALBUM_NO' , $ALBUM_NO)
                    ->update();
        
        }

        public function _album_std_insert($params){
            return $this->db->table('TB_ALBUM_STD')->insert($params);
        }

        public function _album_apnd_file_insert($params){
            return $this->db->table('TB_ALBUM_APND_FILE')->insert($params);
        }

        public function getDetail($no){
            $album = $this->db->table('TB_ALBUM as TA')
                        ->select("TA.* , TU.USER_NM , CONCAT(NVL(VR.READ_CNT, 0), ' / ', NVL(VR.TOT_CNT, 0)) AS VIEW_CNT , NVL(VR.READ_CNT, 0) AS READ_CNT , NVL(VR.TOT_CNT, 0) AS TOT_CNT ")
                        ->join('TB_USER AS TU' , 'TA.ENT_USER_ID = TU.USER_ID')
                        ->join('(SELECT 	ALBUM_NO
                                    , COUNT(1) AS TOT_CNT
                                    , SUM(CASE WHEN NVL(READ_DTTM, \'X\') = \'X\' THEN NULL ELSE 1 END ) AS READ_CNT 
                                FROM 	TB_ALBUM_STD GROUP 	BY ALBUM_NO ) VR' , "VR.ALBUM_NO = TA.ALBUM_NO" , 'left outer')
                        ->where('TA.ALBUM_NO' , $no)
                       ->where('TA.VIEW_YN' , 'Y')
                        ->where('TA.USE_YN' , 'Y')
                        ->get()->getRow();

            
            $albumStd = $this->db->table('TB_ALBUM_STD')
                        ->select("*")
                        ->where('ALBUM_NO' , $no)
                        ->get()->getResult();

            $albumFile = $this->db->table('TB_ALBUM_APND_FILE')
                        ->select("*")
                        ->where('ALBUM_NO' , $no)
                        ->get()->getResult();
            return [
                'album' => $album,
                'albumStd' => $albumStd,
                'albumFile' => $albumFile,
            ];
        }

        public function _album_apnd_file_remove($seq){
            // $result = $this->db->table('TB_ALBUM_APND_FILE')
            //         ->where('ALBUM_FILE_SEQ' , $seq)
            //         ->delete();
            $result = true;
            if ($result){
                return $result;
            } else {
                return false;
            }
        }

        public function _delete($ALBUM_NO){
            $params = [
                'USE_YN'   => 'N',
                'VIEW_YN'   => 'N'
            ];

            $flag = $this->db->table('TB_ALBUM')
                ->set($params)
                ->where('ALBUM_NO' , $ALBUM_NO)
                ->update();

            // $flag = $this->db->table('TB_ALBUM')
            //     ->where('ALBUM_NO' , $ALBUM_NO)
            //     ->delete();
            return $flag;
        }

        public function _read_noti_update($params){
            $update_set = [
                "READ_USER_ID" => $params['user_id'],
                "READ_DTTM" => $params['read_dttm']
            ];

            $result = $this->db->table('TB_ALBUM_STD')
                    ->set($update_set)
                    ->where('ALBUM_NO' , $params['album_no'])
                    ->whereIn('STD_ID' , $params['std_id'])
                    ->update();

            return $result;
        }

    }