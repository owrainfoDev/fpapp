<?php
namespace App\Controllers;

use \Hermawan\DataTables\DataTable;
use CodeIgniter\Model;

use CodeIgniter\Files\File;

class FileUpload extends BaseController
{
    public $pagename = '파일업로드';
    public $pn = 'fileupload';

    protected $data;

    public function __construct()
    {
        
    }

    public function fileUpload(){
        
        $validationRule = [
            'userfile' => [
                'label' => 'File',
                // 'rules' => 'uploaded[file]|ext_in[zip,xlsx,jpg,jpeg,gif,webp,png,ppt,pptx,docx,doc,xls,pdf]'
                'rules' => 'uploaded[file]'
                    // . '|is_image[file]'
                    . '|mime_in[file,image/jpg,image/jpeg,image/gif,image/png,image/webp,application/mp4,video/mp4,application/pdf]'
                    . '|max_size[file,300000]'
                    . '|max_dims[file,204800,204800]',
            ],
        ];

        

        if (! @$this->validate($validationRule)) {
            $data = ['errors' => $this->validator->getErrors() , 'validationRule' => $validationRule];

            return json_encode($data);
        }

        $pn = $this->request->getPost('pn');

        

        if ($imagefile = $this->request->getFiles()) {
            
            if ( is_array( $imagefile['file'] ) ) {
                foreach ($imagefile['file'] as $img) {

                    if ($img->isValid() && ! $img->hasMoved()) {

                        $fileparam['ORIGIN_FILE_NM'] = $img->getName();
                        $filepath = WRITEPATH . 'uploads/' . $img->store( $pn . '/'.date("Ymd"));
                        $f = new File($filepath);
                        // $data = ['uploaded_flleinfo' => new File($filepath)];
                        
                        $fileparam["FILE_SIZE"] = $f->getSize();
                        $ext = pathinfo($filepath);
                        $fileparam["FILE_EXT"] = $ext['extension'];
                        // $fileparam["FILE_NM"] = $img->getName();
                        $fileparam["FILE_NM"] = $ext['filename'];
                        $fileparam['FILE_PATH'] = "/uploads/".$pn."/" . date("Ymd");
                        $allpath = $fileparam['FILE_PATH'] . "/".$ext['basename']  ;
                        if ( file_exists( WRITEPATH . substr($allpath ,1,strlen($allpath)) ) ){
                            $fileparam['FILE_URL'] = $allpath;
                        } else {
                            $fileparam['FILE_URL'] = '';
                        }
                        
                        $return_file[] = $fileparam;
                    }
                }
            } else {    // 단일 파일
                 $img = $imagefile['file'];
                if ($img->isValid() && ! $img->hasMoved()) {

                    $fileparam['ORIGIN_FILE_NM'] = $img->getName();
                    $filepath = WRITEPATH . 'uploads/' . $img->store( $pn . '/'.date("Ymd"));
                    $f = new File($filepath);
                    // $data = ['uploaded_flleinfo' => new File($filepath)];
                    
                    $fileparam["FILE_SIZE"] = $f->getSize();
                    $ext = pathinfo($filepath);
                    $fileparam["FILE_EXT"] = $ext['extension'];
                    // $fileparam["FILE_NM"] = $img->getName();
                    $fileparam["FILE_NM"] = $ext['filename'];
                    $fileparam['FILE_PATH'] = "/uploads/".$pn."/" . date("Ymd");
                    $allpath = $fileparam['FILE_PATH'] . "/".$ext['basename']  ;
                    if ( file_exists( WRITEPATH . substr($allpath ,1,strlen($allpath)) ) ){
                        $fileparam['FILE_URL'] = $allpath;
                    } else {
                        $fileparam['FILE_URL'] = '';
                    }
                    
                    $return_file[] = $fileparam;
                }
            }
        }
        return json_encode($return_file);
    }

    public function photoView(){
        $files = json_decode( $_POST['data1'] , true);

        $data = [
            "list"  => $files
        ];

        return view('photoview', $data);
    }

    public function removeFile($params = null){

        if ($params == null) {
            $content = trim(file_get_contents("php://input"));
            $data = json_decode($content, true);
        } else {
            $data = $params;
        }
        helper('filesystem');
        
        // 업로드 된 파일이 있을때
        if (isset($data['url'])) {
            
            $filepath = WRITEPATH . $data['url'];
            $filepath = str_replace('//','/' , $filepath);
            
            if ( is_file($filepath) &&  file_exists($filepath)){
                if (! unlink($filepath) ) {
                    echo json_encode(['status' => 'fail' , 'msg' => '파일삭제실패' , 'file' => $filepath]);
                    die();
                }
            }
            // 썸네일 삭제
            $without_extension = substr($filepath, 0, strrpos($filepath, ".")); 
            if ( file_exists( $without_extension . ".jpg")  ){
                unlink($without_extension . ".jpg");
            }
            
            if ( $data['tb'] == '_ACA_MEAL_DAILY_APND_FILE' ){
                $Model = new \App\Models\SchoolMeal();
                $Model->_aca_meal_daily_file_remove($data['seq']);
            } else if ($data['tb'] == '_ACA_MEAL_MONTHLY') {
                $Model = new \App\Models\SchoolMeal();
                $Model->_aca_meal_monthly_file_remove($data['seq']);
            } else if ( $data['tb'] == '_ALBUM_APND_FILE'){
                $Model = new \App\Models\Album();
                $Model->_album_apnd_file_remove($data['seq']);
            } else if ( $data['tb'] == '_MEDI_REQ'){
                $Model = new \App\Models\Medicine();
                $Model->_apnd_file_remove($data['seq']);
            } else if ( $data['tb'] == '_CLASS_EDU_PLAN'){
                $Model = new \App\Models\EduPlan();
                $Model->_apnd_file_remove($data['seq']);
            } else if ( $data['tb'] == '_LEAVE_AGREE'){
                $Model = new \App\Models\HomeCommingConsent();
                $Model->_apnd_file_remove($data['seq']);    
            } else if ( $data['tb'] == '_APP_BOARD_APND_FILE'){
                $Model = new \App\Models\AppBoard();
                $Model->_apnd_file_remove($data['seq']);                    
            } else {
                $Model = new \App\Models\Notice();
                $Model->_noti_apnd_file_delete($data['seq']);
            }
            
            return json_encode( ['status' => 'success' , 'msg' => '파일 삭제']);
        } else {
            return json_encode( ['status' => 'success' , 'msg' => '파일 삭제'] );
        }
    }
}