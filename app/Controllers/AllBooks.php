<?php

namespace App\Controllers;

use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;
use CodeIgniter\Files\File;


class AllBooks extends BaseController
{
    public $pagename = '올북스_주문정보_전달_STEP';
    public $pn = 'ajax';
    public $ksys;
    public $db;

    public function __construct()
    {
        $custom = [
            'DSN'      => '',
            'hostname' => 'kyowon.cu0nkqepynqr.ap-northeast-2.rds.amazonaws.com',
            'username' => 'infinity_ksys_db',
            'password' => '*infinity5683!@#',
            'database' => 'kyowon',
            'DBDriver' => 'MySQLi',
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug'  => true,
            'charset'  => 'utf8',
            'DBCollat' => 'utf8_general_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => 3306,
        ];
        $this->ksys = \Config\Database::connect($custom);

        $this->db = \Config\Database::connect();
    }

    public function order(){

        $LINK_USER_ID = 'ABS_LINK';
        $SYS_HEADER = $this->_sys_header();
        $ORDER_NO = $this->_order_no();

        // foreach ( $ORDER_NO as $ORDER ){
            
        // }



        $query1 = "select * from kw_order_detail";
        $query = $this->ksys->query($query1);

        var_dump($query->getResult());
    }

    public function _sys_header(){
        return $this->db->query("SELECT CHK_CD1 FROM TB_CODE
                            WHERE CODE_GRP_CD = 'SYSTEM_CODE' AND CODE= 'ABS';")
                            ->getRow(0)->CHK_CD1;

    }

    public function _order_no(){
        return $this->db->query("SELECT  ORD.ORD_NO
                                    FROM 	TB_GOODS_ORD ORD		
                                WHERE 	VENDOR_CORP_CD ='ABS'			-- 올북스 교재에 대해서 만 처리   
                                    AND	ACA_ID <> 'HL00001'
                                    AND	PAY_CHK = 'Y'
                                    AND  ORD_STATUS = '00'                        
                                ORDER 	BY ORD_NO")
                            ->getResult();
    }

    public function _goods_ord($LINK_ORD_NO){
        return $this->db->query("SELECT * FROM TB_GOODS_ORD WHERE ORD_NO = '".$LINK_ORD_NO."' ")
                            ->getRow();        
    }


    // -- CHECK 주문 상태 건 조회 -----------------------------------------------------------
    public function _check_ord_cnt(){
        return $this->db->query("SELECT COUNT(1) as cnt
        FROM	TB_GOODS_ORD ORD
        WHERE ORD.ORD_STATUS = '00'
         AND  ORD.VENDOR_CORP_CD	=  'ABS'
         AND 	ORD.ACA_ID NOT IN ('HL00001', 'FP00128')
         AND	ORD.PAY_CHK = 'Y' ")
                            ->getRow(0)->cnt;
    }

    public function _check_ord_dtl_cnt(){
        return $this->db->query("SELECT 	COUNT(1) as cnt
        FROM	TB_GOODS_ORD_DTL	 DTL
              JOIN TB_GOODS_ORD ORD ON ORD.ORD_NO = DTL.ORD_NO
                              AND ORD.ACA_ID NOT IN ('HL00001', 'FP00128') 
        WHERE ORD.ORD_STATUS = '00'
         AND  ORD.VENDOR_CORP_CD	=  'ABS'
         AND	ORD.PAY_CHK = 'Y' ")
                            ->getRow(0)->cnt;
    }

    /**
     * -- STEP1. 주문건 작업대기 상태로 변경
     * -- 작업 시작 시점 이후의 결제건과 혼선이 발생하지 않게 하기 위함 (주문건이 들어오더라도 Lock 건 리스트만 처리)
     */

    public function _update_good_ord_dtl(){
        return $this->db->query("UPDATE TB_GOODS_ORD_DTL	 DTL
		                    JOIN TB_GOODS_ORD ORD ON ORD.ORD_NO = DTL.ORD_NO AND ORD.ACA_ID NOT IN ('HL00001', 'FP00128')
                                SET	DTL.ORD_STATUS = 'LOCK' 
                            WHERE ORD.ORD_STATUS = '00'
                                AND  ORD.VENDOR_CORP_CD	=  'ABS'
                                AND	ORD.CORP_CD = 'AC00001'
                                AND	ORD.PAY_CHK = 'Y'");
    }

    public function _update_good_ord(){
        return $this->db->query("UPDATE TB_GOODS_ORD ORD
                                    SET	ORD_STATUS = 'LOCK' 
                                    WHERE ORD.ORD_STATUS = '00'
                                    AND  ORD.VENDOR_CORP_CD	=  'ABS'
                                    AND	ORD.CORP_CD = 'AC00001'
                                    AND	ORD.PAY_CHK = 'Y'
                                    AND	ORD.ACA_ID NOT IN ('HL00001', 'FP00128')");
    }
        
}
