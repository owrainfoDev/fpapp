<?php
    namespace App\Models;

    use CodeIgniter\Model;
    
    /**
     * Description of CrudModel
     *
     * @author hoksi
     */
    class Payment extends Model{
        
        protected $db;

        public function __construct()
        {
            parent::__construct();
            $this->db = \Config\Database::connect();
        }

        public function list($params){
            $params['STD_ID'] = $params['STD_ID'];

            $page = $params['page'];
            $per_page = 10;
            $page_first = ($page-1)*$per_page;

            $query = "
            SELECT SQL_CALC_FOUND_ROWS
                    TI.STD_ID        
                    , TI.INVOICE_NM 														AS INVOICE_NM          
                    , TI.INVOICE_NO 			AS invoice_no
                    , TI.INVOICE_ENC 			AS invoice_enc
                    , SUBSTR(DATE_FORMAT(IFNULL(TI.LEARN_FDT, TI.ISSUE_DT), '%Y-%m-%d'),1,7) AS INVOICE_MONTH
                    , DATE_FORMAT(TI.ISSUE_DT, '%Y-%m-%d') 									AS ISSUE_DT
                    , CASE WHEN TC.CLASS_OPR_TP = 'TIME' THEN CONCAT(IFNULL(TI.CLASS_CNT, 0), ' 회')
                            ELSE CONCAT(IFNULL(DATE_FORMAT(TI.LEARN_FDT, '%y-%m-%d'), ''), '~', IFNULL(DATE_FORMAT(TI.LEARN_TDT, '%y-%m-%d'), ''))
                    END				                                           AS	invoice_learn_dt				/*-- 수업기간  --*/									  	
                    , TI.CLASS_AMT
                    , NVL(TID.GOODS_NM, '')		AS GOODS_NM
                    , NVL(TID.GOODS_TOT_AMT, 0)	AS GOODS_TOT_AMT		
                    , TI.AMT
                    , TI.VAT
                    , TI.TOT_AMT
                    , CASE FN_GET_INVOICE_PAY_STAT (TI.INVOICE_NO) WHEN 'Y' THEN '완납' WHEN 'N' THEN '미결제' WHEN 'D' THEN '취소' ELSE '부분결제' END PAY_STAT
                    , FN_GET_INVOICE_PAY_STAT (TI.INVOICE_NO) as PAY_STAT_CODE
                    , TU.USER_NM AS STD_NM
--                    ,TSM.PG_TRADE_NO AS TRADE_NO
            FROM 	TB_INVOICE  TI
                    LEFT OUTER JOIN TB_CLASS TC ON TC.CLASS_CD = TI.CLASS_CD
                    JOIN TB_ACA TA ON TA.ACA_ID = TI.ACA_ID
                    LEFT OUTER JOIN (
                            SELECT  INVOICE_NO
                                    , CONCAT(MAX(GM.GOODS_NM) , CASE WHEN COUNT(1) <= 1 THEN '' ELSE CONCAT(' 외',  COUNT(1)-1 , ' 건') END)  AS GOODS_NM
                                    , SUM(ID.AMT)			AS GOODS_AMT
                                    , SUM(ID.VAT)			AS GOODS_VAT
                                    , SUM(ID.TOT_AMT)		AS GOODS_TOT_AMT
                            FROM 	TB_INVOICE_DTL ID
                                    JOIN TB_CORP_GOODS GM ON GM.GOODS_CD = ID.GOODS_CD
                            GROUP	BY INVOICE_NO 
                    ) TID ON TID.INVOICE_NO = TI.INVOICE_NO		
                    LEFT JOIN TB_USER TU on TU.USER_ID = TI.STD_ID
--                    LEFT OUTER JOIN TB_SALES_MST TSM ON TI.INVOICE_NO = TSM.INVOICE_NO
            WHERE 	1=1 
            AND 	TI.STD_ID = '".$params['STD_ID']."'
            AND 	TI.INVOICE_TP IN ('EDU', 'REP') 
--            AND 	TI.USE_YN != 'D'
            AND         TI.USE_YN = 'Y'
            AND         TI.PAY_YN IN ('Y', 'P', 'N')
            ORDER 	BY TI.INVOICE_NO DESC
            ";
            
        //     $query .= " limit " . $page_first . ", " . $per_page . " ";
            $rows = $this->db->query($query)->getResult();

            return $rows;
        }
    }