<?php
// Allow from any origin
/*if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}*/

/* CORS godeg
//header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: ".@$req);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
*/


//encrypt querystring
class Encryption{

    /**
    *
    *
    * ----------------------------------------------------
    * @param string
    * @return string
    *
    **/
    public static function safe_b64encode($string='') {
        $data = base64_encode($string);
        $data = str_replace(['+','/','='],['-','_',''],$data);
        return $data;
    }

    /**
    *
    *
    * -------------------------------------------------
    * @param string
    * @return string
    *
    **/
    public static function safe_b64decode($string='') {
        $data = str_replace(['-','_'],['+','/'],$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
    *
    *
    * ------------------------------------------------------------------------------------------
    * @param string
    * @return string
    *
    **/
    public static function encode($value=false){
        if(!$value) return false;
        $iv_size = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($iv_size);
        $crypttext = openssl_encrypt($value, 'aes-256-cbc', 'ayamgoreng', OPENSSL_RAW_DATA, $iv);
        return self::safe_b64encode($iv.$crypttext);
    }

    /**
    *
    *
    * ---------------------------------
    * @param string
    * @return string
    *
    **/
    public static function decode($value=false){
        if(!$value) return false;
        $crypttext = self::safe_b64decode($value);
        $iv_size = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($crypttext, 0, $iv_size);
        $crypttext = substr($crypttext, $iv_size);
        if(!$crypttext) return false;
        $decrypttext = openssl_decrypt($crypttext, 'aes-256-cbc', 'ayamgoreng', OPENSSL_RAW_DATA, $iv);
        return rtrim($decrypttext);
    }
}

function input_data($data){
$filter = stripslashes(strip_tags(htmlspecialchars($data,ENT_QUOTES)));
return $filter;
}

//get parameter url
$param = explode("/", $_SERVER['REQUEST_URI']);

//get url
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";

//uuid
function uuid($data = null) {
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

//GUID
function GUID()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}


//token
$app_id     = 'N3spr3ss0';
$date       = date('Ymd');
$token      = hash("sha256",$app_id.$date);

//staging
/*$db_server        = '10.6.160.197';
$db_user          = 'adminuser';
$db_password      = 'En%@fr125UXK';
$db_name          = 'nespresso';*/

//live
$db_server        = '10.6.160.63';
$db_user          = 'root';
$db_password      = '>(PymG4]asgU(AaD';
$db_name          = 'nespresso';


$conn 			  = new mysqli($db_server,$db_user,$db_password,$db_name) or die (mysqli_error());
?>
