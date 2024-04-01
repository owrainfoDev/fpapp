<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['func'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }

    public function template(string $page, array $data , string $layout = 'main', array $options = null )
    {
        if ( $layout != "none") echo view('layout/'.$layout.'/header', $data);
        echo view($page, $data);
        if ( $layout != "none") echo view('layout/'.$layout.'/footer', $data);
    } 

    // 공통 해더
    public function academyDefault(){

        $post_data = trim(file_get_contents("php://input"));
        $post = json_decode($post_data, true);
        $user = new \App\Models\AuthorInfo($post['user_id']);
        $userinfo = $user->info();

        $is_teacher = $user->is_teacher();
        $authNm = getAuthName($post['user_id']);

        $academyYear = new \App\Models\Menu();
        $academyYear = $academyYear->baseAcademicyear();

        if (isset($academyYear)){
            foreach ( $academyYear as $y ) {
                if ($y['CHK_CD1'] == "Y"){
                    $year = $y['CODE'];
                    break;
                } 
            }
        }
        // $year = isset( $year ) ? $year : date("Y");
        $year = isset( $post['year'] ) ? $post['year'] : ( isset($year) ? $year : date("Y") );
        $menuHeader = array(
            'is_teacher'    => $is_teacher,
            'USER_ID'       => $userinfo->USER_ID,
            'ACA_ID'        => $userinfo->ACA_ID,
            'HP_NO'         => substr($userinfo->HP_NO,0,-4) . "****",
            'USER_NM'       => $userinfo->USER_NM,
            'ACA_NM'        => $userinfo->ACA_NM,
            'PROFILE'       => $userinfo->PROFILE,
            'AUTHNM'        => $authNm
        );
        
        $data['is_teacher'] = $is_teacher;
        $data['menuHeader'] = $menuHeader;
           
        // menu 
        $menu = new \App\Models\Menu();
        $menuList = $menu->menuList([
                'is_teacher'    => $is_teacher === true ? "Y" : "N" ,
                'USER_ID'       => $userinfo->USER_ID,
                'ACA_ID'        => $userinfo->ACA_ID,
                'year'          => $year,
            ]);

        $data['menuList'] = $menuList;

        $menuYear = [];
        foreach ( $academyYear as $y ) {
            $menuYear[] = [
                'CODE' => $y['CODE'],
                'CODE_NM' => $y['CODE_NM'],
                'SELECTED' => ($y['CODE'] ==  $year) ? 'SELECTED' : '',
            ];
        }
        $data['menuYear'] = $menuYear;

        $cyear = '';
        foreach ( $data['menuYear'] as $y){
            if ($y['SELECTED'] == "SELECTED"){
                $cyear = $y['CODE'];
                break;
            }
        }

        $session = session();
        $session->set('year' , $cyear);

        return json_encode( $data );
    }

}
