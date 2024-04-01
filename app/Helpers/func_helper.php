<?php 
    function aes256Encode($plainText, $secretKey='kyowonkyowonkyowonkyowonkyowonky') {
        $ivBytes = chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00);
        $ivBytes = "kyowonkyowonkyow";
        // $ivBytes = substr(hash('sha256', $ivBytes), 0, 32);
        return base64_encode(openssl_encrypt($plainText, "AES-256-CBC", $secretKey, true, $ivBytes));
    }
    
    function aes256Decode($encrypt_text, $secretKey='kyowonkyowonkyowonkyowonkyowonky') {
        $ivBytes = chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00);
        $ivBytes = "kyowonkyowonkyow";
        $ivBytes = substr(hash('sha256', $ivBytes), 0, 32);
        return openssl_decrypt(base64_decode($encrypt_text), "AES-256-CBC", $secretKey, true, $ivBytes);
    }

    function curlSend(string $sendurl , $jsonStringData , string $method = 'POST' ){
        $curl = curl_init();
 
        curl_setopt_array($curl, array(
        CURLOPT_URL => $sendurl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $jsonStringData,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json;utf-8',
        ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return $response;
    }

    function curlBearSend(string $sendurl , string $access_token  , string $method = 'POST' ){
        $curl = curl_init();
 
        curl_setopt_array($curl, array(
        CURLOPT_URL => $sendurl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        // CURLOPT_POSTFIELDS => '{}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json;utf-8',
            'Authorization: Bearer ' . $access_token
        ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return $response;
    }

function compress_png($path_to_png_file,$dest_to_path, $max_quality = 90)
{
	if (!file_exists($path_to_png_file)) {
		throw new Exception("File does not exist: $path_to_png_file");
	}

	// guarantee that quality won't be worse than that.
	$min_quality = 60;

	// '-' makes it use stdout, required to save to $compressed_png_content variable
	// '<' makes it read from the given file path
	// escapeshellarg() makes this safe to use with any path
	$compressed_png_content = shell_exec("pngquant --quality=$min_quality-$max_quality - < ".escapeshellarg(    $path_to_png_file));

	if (!$compressed_png_content) {
		throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
		return false;
	}

	file_put_contents($dest_to_path, $compressed_png_content);
	return true;
}

function compress_image($path_to_file, $dest_to_path,$quality=70) {
	$info = getimagesize($path_to_file);

	if ($info['mime'] == 'image/jpeg'){
		$image = imagecreatefromjpeg($path_to_file);
		imagejpeg($image, $dest_to_path, $quality);
	} elseif ($info['mime'] == 'image/gif') {
		$image = imagecreatefromgif($path_to_file);
		imagetruecolortopalette($image, false, 16);
		imagegif($image, $dest_to_path);
	} else return false;

	//return destination file
	return true;
}

function photoOrVideoReturnTag($file , $ext){
    if ( strpos($ext , 'video') !== false ) {
        $html = '<video width="100%" height="*" controls>
                    <source src="'.$file.'" type="'.$ext.'">
                </video>';
    } 
    if ( strpos($ext , 'image') !== false ) {
        $html = '<img src="'.$file.'">';
    }
    return $html;
}

function uploadfileType($file){

    $pho = [];
    $vid = [];
    $oth = [];


    foreach ( $file as $f ) {

        if ( is_array( $f )) {

            if (strpos($f['ext'] , 'image') !== false ){
                $pho[] = $f;
            } else if (strpos($f['ext'] , 'video') !== false ){
                $vid[] = $f;
            } else {
                $oth[] = $f;
            }
        }
    }
    return [ "photo" => $pho , 'video' => $vid , 'other' => $oth ];
}

function is_auth_permit($is_teacher, $login_id , $write_user_id){

    if ($is_teacher == true && $write_user_id == $login_id){
        return true;
    } else {
        return false;
    }
}

function getThumbnailPreview($path){
    $furl = str_replace("//","/" , $path);
    if (! file_exists($furl) ) return '/resources/images/file-icon.png';

    $f = new \CodeIgniter\Files\File($path);
    $type = $f->getMimeType();
    $furl = str_replace("//","/" , $path);
    $furl = str_replace(WRITEPATH , '' , $furl);

    $without_extension = substr($furl, 0, strrpos($furl, ".")); 
    
    if ( file_exists(WRITEPATH . $without_extension . "_thumb.jpg" ) ){
        return "/" . $without_extension . "_thumb.jpg";
    } else if ( file_exists(WRITEPATH . $without_extension . ".jpg" ) ) {
        return "/" . $without_extension . ".jpg";
    }

    if ( strpos($type, 'image') !== false){
        return "/" .$furl;
    } else if ( strpos($type, 'video') !== false){
        return "/resources/images/video-icon.png";
    } else if ( strpos($type, 'application/pdf') !== false){
        return "/resources/images/pdf-svgrepo-com.svg";
    } else {
        return "/resources/images/file-icon.png";
    }
}

function str_nl2br($str){
    // $str = str_replace(chr(13) , "<br />" , $str);
    $str = str_replace("\r\n","<br />", $str);
    $str = str_replace("\n" , "<br />" , $str);
    
    return $str;
}

function saveTypeReturn($type){
    switch ($type){ 
        case "publish":
            return "Y";
            break;
        case "draft":
            return "D";
            break;
    }
}

function is_file_flag($filepath){
    $filepath = str_replace(WRITEPATH ,"/" , $filepath);
    $realFilepath = WRITEPATH . $filepath;
    $realFilepath = str_replace('//' , '/' , $realFilepath);
    $realFilepath = str_replace('/.' , '' , $realFilepath);
    $flag = false;
    
    if (is_file($realFilepath) && file_exists($realFilepath)){
        $flag = true;
    } 

    return $flag;
}

function redirectto($url){
    echo "<script>location.replace('" . $url . "');</script>";
}

function getUserName($user_id , $column = 'USER_NM'){
    $db      = \Config\Database::connect();
    try {
    $builder = $db->table('TB_USER');
    $builder = $builder->select('*');
    $builder = $builder->where('USER_ID' , $user_id);
        return $builder->get()->getRow()->{$column};
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function getStdInfo($std_id , $column = 'STD_NM'){
    $db      = \Config\Database::connect();
    try {
    $builder = $db->table('TB_STD_INFO');
    $builder = $builder->select('*');
    $builder = $builder->where('STD_ID' , $std_id);
        return $builder->get()->getRow()->{$column};
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function getCodeName($code_grp_cd , $value = null){
    $db      = \Config\Database::connect();

    try {
        if ( $value === null ) {
            return $db->table('TB_CODE')
                    ->where('code_grp_cd' , $code_grp_cd)
                    ->where('USE_YN','Y')
                    ->where('VIEW_YN','Y')
                    ->orderBy('ORD_NO', "ASC")
                    ->get()->getResult();
        } else {
            return $db->table('TB_CODE')
                    ->where('code_grp_cd' , $code_grp_cd)
                    ->where('CODE' , $value)
                    ->where('USE_YN','Y')
                    ->where('VIEW_YN','Y')
                    ->orderBy('ORD_NO', "ASC")
                    ->get()->getRow();
        }
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}


function mp4tojpg($filepath, $filename , $fileext ){
    if ( $filename == '' || $fileext == '' ) return false;
    
    // if (! in_array( strtolower($fileext) , array( "mp4" , "pdf") ) ) return false;
    
    $filepath = WRITEPATH . $filepath;
    $filepath = str_replace('//','/', $filepath);
    $filepath = str_replace('','/', $filepath);
    $source = $filepath . "/" . $filename . "." . $fileext;
    if ( ! file_exists($source) ) return false;

    switch ( strtolower($fileext) ){
        case "mp4":
            $dest = $filepath . "/" . $filename . "_thumb.jpg";
            exec( "ffmpeg -ss 00:00:02 -i '".$source."' -an -s 256x144 -vframes 1 -y '".$dest."' ");
        break;
        case "pdf":
            $dest = $filepath . "/" . $filename . "_thumb.jpg";
            $imagick = new Imagick();
            $imagick->readImage($source);
            $imagick->writeImage($dest);
        break;
        default:
            $dest = $filepath . "/" . $filename . "_thumb.jpg";
            \Config\Services::image('imagick')
            ->withFile($source)
            ->resize(336, 476, true, 'auto')
            ->save($dest);

            // return false;
        break;
        
    }
    
    return ".jpg";
}

function getAuthName($userid , $columnName = 'auth_grp_nm'){
    $db      = \Config\Database::connect();

    $query = "SELECT a.EMP_ID , tu.USER_NM, tu.aca_id , a.emp_tp , group_concat(b.auth_grp_cd) as auth_grp_cd , group_concat(c.CODE_NM) AS auth_grp_nm , d.CODE_NM AS emp_tp_nm 
            FROM TB_EMP a
            LEFT JOIN TB_USER tu ON a.emp_id = tu.user_id AND tu.use_yn = 'Y' 
            LEFT JOIN TB_USER_TP_AUTH_GRP b ON a.EMP_ID = b.USER_ID AND b.AUTH_GRP_CD IN ('AG0102','AG0104', 'AG0101', 'AG0108', 'AG0107')
            LEFT JOIN TB_CODE c ON c.code_grp_cd  = 'AUTH_GRP_CD' AND c.CODE = b.AUTH_GRP_CD AND c.CODE IN ('AG0102','AG0104', 'AG0101', 'AG0108','AG0107')  
            LEFT JOIN TB_CODE d ON d.code_grp_cd  = 'EMP_TP' AND d.CODE = a.EMP_TP
        WHERE a.EMP_TP IN ( '02' , '01' )
        AND a.emp_id = ? 
        ";
    $data = $db->query($query , array($userid));
    $col = $data->getRow()->{$columnName};

    return $col;
}

function get_DRUG_STORAGE_METHOD($type){
    switch ( $type ){
        case "R":
                return "실온";
            break;
        case "C":
            return "냉장";
            break;
    }
}