<?php
    namespace App\Models;

    use CodeIgniter\Model;
    use CodeIgniter\Database\BaseBuilder;
    
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class User extends Model{
        protected $table      = 'TB_USER'; // 테이블명
        protected $primaryKey = 'USER_ID'; // primary key 컬럼명
    
        protected $useAutoIncrement = false; // auto_increment 사용 여부
    
        protected $returnType = 'array'; // 검색 결과 반환 타입
    
        protected $allowedFields = ['ACA_ID','USER_PWD','USER_NM','USER_GB_CD','LINK_USER_ID','AUTH_GRP_CD','HP_NO','TEL_NO','EMAIL','SMS_RECV_YN','APP_PUSH_RCV_YN','MSG_RCV_CONF_DTTM','ZIP_CODE','ADDR','ADDR_DTL','UI_THEME','LST_CONN_IP','LST_CONN_DTTM','PROFILE','USE_YN','ENT_DTTM','ENT_USER_ID','UPT_DTTM','UPT_USER_ID','MIG_UNION_UID','MIG_ORGNO']; // insert, update 시 사용할 컬럼명

        protected $useTimestamps = false;
        protected $createdField  = 'ENT_DTTM';
        protected $updatedField  = 'UPT_DTTM';

        public function getAca($ACA_ID , $column = null){
            return ($column) ? $this->builder('TB_ACA')->where('ACA_ID' , $ACA_ID)->get()->getRow()->{$column} : $this->builder('TB_ACA')->where('ACA_ID')->get()->getRow();
        }

        public function getChildrenInfoFromParentID($PARENT_ID , $STD_ID , $ACA_ID){
            $this->builder()->select("TB_USER.* , TSI.STD_URL ");
            $this->builder()->join("TB_STD_INFO TSI", "TB_USER.USER_ID = TSI.STD_ID");
            $this->builder()->whereIn("TB_USER.USER_ID" , function (BaseBuilder $subQueryBuilder) use($PARENT_ID){
                return $subQueryBuilder->select("TP.STD_ID")->from("TB_PARENTS TP")->where('TP.PARENT_ID' , $PARENT_ID);   
            });
            // $this->builder()->where('TB_USER.USE_YN', 'Y');
            // $this->builder()->where('TB_USER.ACA_ID', $ACA_ID);
            $this->builder()->orderBy(" CASE
                                        WHEN TB_USER.USER_ID = '".$STD_ID."' THEN 1
                                        ELSE 2
                                    END");
            $sql = $this->builder()->getCompiledSelect(false);
            // echo $sql;
            $query = $this->builder()->get();
            $data = $query->getResult();

            return $data;
        }

    }

    