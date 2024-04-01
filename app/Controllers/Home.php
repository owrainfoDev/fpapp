<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;
use CodeIgniter\Files\File;

class Home extends BaseController
{
    public $pagename = '메인';
    public $pn = 'Home';

    protected $data;

    public function index()
    {
        $data = [
            'header' => ['title'=>'title']
        ];
        return $this->template('main', $data);
    }

    public function getProfile(){
        $session = session();

        $_is_teacher = $session->get('_is_teacher');

        $user_id = $session->get("_user_id");

        if ( $_is_teacher ) {
            $userModel = new \App\Models\User();
            $user = $userModel->find($user_id);
            $aca_nm = $userModel->getAca($user['ACA_ID'], 'ACA_NM');   
            $profile = $user['PROFILE'] ;
            echo '
                <div class="cont_img">
                    <img src="'.$profile.'" alt="profile image111" id="mainProfileImage"  onError="javascript:this.src=\'/resources/images/png_human.png\'">
                    <span class="main_img_edit" id="mainProfileImageBtn"></span>
                </div>
                <div id="mainViewProfile">
                    <div class="cont_txt">
                        <p class="name">'.$user['USER_NM'].'<span class="parent">('.getAuthName($user_id).')</span></p>
                        <p class="office">'.$aca_nm.'</p>
                    </div>
                </div>
            ';
        } else {
            $userModel = new \App\Models\User();
            $user = $userModel->find($user_id);
            $std_id = $session->get("_std_id");
            $children = $userModel->getChildrenInfoFromParentID($user_id , $std_id, $user['ACA_ID']);
            
            // 선택된 학생 ACA_ID
            $children0 = $children[0];
            $aca_nm = $userModel->getAca($children0->ACA_ID, 'ACA_NM');    
            
            // echo $std_id;
            $year = $session->get("year");
            

            $students = new \App\Models\Students();
            $params = [
                'userid' => $user_id,
                'aca_id' => $children0->ACA_ID,
                'is_teacher' => "N",
                'year' => $year,
                'std_id' => $std_id
            ];
            $class_list = $students->getClassListFromTeacher($params);  // 학원 리스트

            $classname = [];
            foreach ( $class_list as $class ){
                $classname[] = $class->CLASS_NM;
            }

            $profile = $children[0]->STD_URL ;

            echo '
                <div class="cont_img">
                    <img src="'.$profile.'" alt="profile image11" id="mainProfileImage" onError="javascript:this.src=\'/resources/images/png_human.png\'">
                    <span class="main_img_edit" id="mainProfileImageBtn"></span>
                </div>
                <div id="mainViewProfile">
                    <div class="cont_txt">
                        <p class="name">'.$children[0]->USER_NM.' <span class="parent">학부모</span></p>
                        <p class="office">'.$aca_nm.'</p>
                        <p class="level">'.$classname[0].'</p>
                    </div>
                    <div class="child_edit">
                        <ul>
                ';
                foreach ($children as $child) {
                echo '      
                            <li>
                                <a href="">
                                    <img src="'.$child->STD_URL.'" alt="children image" onError="javascript:this.src=\'/resources/images/png_human.png\'" onclick="javascript:setStdBtn(\''. $child->USER_ID  .'\')">
                                </a>
                            </li>
                ';
                }
                echo '
                            <!--
                            <li>
                                <a href=""></a>
                            </li>
                            -->
                        </ul>
                    </div>
                </div>
            ';
        }


        $html = ob_get_contents();
        ob_end_clean();


        return json_encode([
            'html' => $html
        ]);
        
    }
    
}

