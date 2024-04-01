<?php

namespace App\Controllers;

class Menu extends BaseController
{
    protected $data;
    public function ajaxMainData(){
       return $this->academyDefault();
    }

    // 알림 표시 
    public function getAlramCount(){
        $content = trim(file_get_contents("php://input"));
        $this->data = json_decode($content, true);
        if ($content == "" && ( $_REQUEST ) ) $this->data = $_REQUEST;

        if ($this->data['csrf_token_name'] != csrf_hash()){
            return json_encode(['status'=> 'error','msg'=> '토큰 정보가 다릅니다.']);
        }
        $pushmessageModel = new \App\Models\PushMessage();
        $messageCnt = $pushmessageModel->where('USER_ID' , $this->data['USER_ID'] )
                        ->where("READ_YN" , 'N')
                        ->where('SEND_YN','Y')
                        ->where('VIEW_YN','Y')
                        ->where('SENDER IS NOT NULL',null , false )
                        ->where("INSERT_DTTM BETWEEN DATE_FORMAT(DATE_ADD(NOW(),INTERVAL -14 DAY), '%Y-%m-%d 00:00:00')  AND DATE_FORMAT(NOW(), '%Y-%m-%d 23:59:59')", null , false )
                        ->countAllResults();

        return json_encode($messageCnt);
    }
}
