<?php
    namespace App\Models\Model\Cimodel;

    use CodeIgniter\Model;
    
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class NoticeModel extends Model{
        
        protected $db;

        public function __construct()
        {
            parent::__construct();
            $this->db = \Config\Database::connect();
        }
        
        public function noticelist(int $cnt = 20){
            $data = $this->db->query("SELECT 
                TN.NOTI_SEQ, TN.TITLE , TN.CNTS, TN.ENT_DTTM, TN.APP_PUSH_YN , TNR.READ_DTTM, TNR.CONF_YN , TNR.CONT_DTTM 
                FROM TB_NOTI TN
                JOIN TB_NOTI_READ TNR ON TN.NOTI_SEQ = TNR.NOTI_SEQ AND TNR.USER_ID = 'P00011305'
                WHERE TN.VIEW_YN = 'Y'
            ORDER BY TN.NOTI_SEQ DESC ");    

            return $data;
        }
    }