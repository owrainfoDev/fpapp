<?php
namespace App\Controllers;

use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;
use CodeIgniter\Files\File;


class Payment extends BaseController
{
    public $pagename = '수업료 결제';
    public $pn = 'payment';

    public function index(){
        $this->list();
    }

    public function list(){
        $request = \Config\Services::request();
        $payment = new \App\Models\Payment();

        $session = session();
        $std_id = $request->getVar('std_id');
        if ($std_id == '') {
            $std_id = $session->get('_std_id');
        }

        $page = $request->getGet('more') == null || $request->getGet('more') == '' ? 1 : $request->getGet('more') ;

        $params = [
            'STD_ID' => $std_id,
            'page' => $page
        ];

        $list = $payment->list($params);

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'list' => $list,
            'is_teacher' => $session->get('_is_teacher')
        ];
        
        return $this->template('payment/payment', $data , 'sub');
    }

    public function listTemplate(){
        
        $request = \Config\Services::request();

        $payment = new \App\Models\Payment();
        $session = session();
        $std_id = $session->get("_std_id");

        $page = $request->getVar('more') == null || $request->getVar('more') == '' ? 1 : $request->getVar('more') ;
        

        $params = [
            'STD_ID' => $std_id,
            'page' => $page
        ];
        $data = $payment->list($params);

        $data = [
            'header' => ['title'=> $this->pagename , 'pn' => $this->pn],
            'list' => $data,
            'is_teacher' => $session->get('_is_teacher')
        ];
        
        ob_start();
        $this->template('payment/paymentTemplate', $data , 'none');
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public function morelist(){
        return $this->listTemplate();
    }
}