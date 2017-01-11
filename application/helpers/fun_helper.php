<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function sys_skin() {
    $ci = &get_instance();
	return $ci->input->cookie('skin') ? $ci->input->cookie('skin') : 'base';
}

function skin_url() {
	$useragent = $_SERVER['HTTP_USER_AGENT'];
// 	$skin_url = 'statics/saas/scm/app2_beta';
// 	if (strstr($useragent,'Chrome')) {
	    $skin_url = 'statics/saas/scm/app2_release';
// 	}
	return base_url($skin_url);
}

function replace_unicode_escape_sequence($match) {
	return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}

/**
 * 登陆校验
 * @param string $str
 * @return unknown|boolean
 */
function token($str='') {
    $ci = &get_instance();
	if (!$str) {
		$data['token'] = md5(time().uniqid());
		set_cookie('token',$data['token'],120000);
		return $data['token'];
	} else {
// 	    $post   = $ci->input->get_post('token');
		$token  = get_cookie('token');
		
		if (isset($token) && isset($str) && $str == $token) {
		    //set_cookie('token','',120000);
			return true;
		}
		return false;
	}
}

/**
 * 时间转换
 */

function dateTime ($data){
	$times=date('Y-m-d H:i:s',$data);
	return $times;
}

/**
 * 统一返回AJAX JSON字符串
 * @param string $status
 * @param unknown $msg
 * @param unknown $url
 * @param unknown $data
 */
function responseJSON($status="error",$msg="",$url="",$data=array()){
	if(empty($data)) $data = array();
	return json_encode(array($status=>true,"message"=>$msg,"redirect"=>$url,"data"=>$data));
}


function alert($str,$url='') {
    $str = $str ? 'alert("'.$str.'");' : '';
    $url = $url ? 'location.href="'.$url.'";' : 'history.go(-1);';
	die('<script>'.$str.$url.'</script>');
}

function alert2($str) {
	die('
	<style>
			.ui-btn {
				display: inline-block;
			    padding: 0 16px;
			    line-height: 30px;
			    height: 30px;
			    font-size: 13px;
			    border-radius: 2px;
			    background-color: #1c84c6;
			    border-color: #1c84c6;
			    box-shadow: 1px 1px 1px #ccc;
			    color: #FFF;
			    vertical-align: middle;
			    cursor: pointer;
			}
	</style>
	<br/>
	&nbsp;<span style="font-size:13px;" >'.$str.'</span>
	<a onclick=" window.location.reload()" class="ui-btn ui-btn-sp">点我刷新</a>');
}


function str_enhtml($str) {
	if (!is_array($str)) return addslashes(htmlspecialchars(trim($str)));
	foreach ($str as $key=>$val) {
		$str[$key] = str_enhtml($val);
	}
	return $str;
}

function str_nohtml($str) {
	if (!is_array($str)) return stripslashes(htmlspecialchars_decode(trim($str)));
	foreach ($str as $key=>$val) {
		$str[$key] = str_nohtml($val);
	}
	return $str;
} 



function success($data){
	die(json_encode(array($status=>true,"message"=>$msg,"redirect"=>$url,"data"=>$data)));
}

function error($data){
	die(json_encode(array($status=>false,"message"=>$msg,"redirect"=>$url,"data"=>$data)));
}



function str_alert($status=200,$msg='success',$data=array()) {
    $msg = array(
		'status' => $status,
		'msg' => $msg,
		'data' => $data,
	);
	return die(json_encode($msg));
}

function str_check($t0, $t1) {
	if (strlen($t0)<1) return false;   
	switch($t1){
		case 'en':$t2 = '/^[a-zA-Z]+$/'; break;   
		case 'cn':$t2 = '/[\u4e00-\u9fa5]+/u'; break;    
		case 'int':$t2 = '/^[0-9]*$/'; break;        
		case 'price':$t2 = '/^\d+(\.\d+)?$/'; break;  
		case 'username':$t2 = '/^[a-zA-Z0-9_]{5,20}$/'; break;   
		case 'password':$t2 = '/^[a-zA-Z0-9_]{6,16}$/'; break;
		case 'email':$t2 = '/^[\w\-\.]+@[a-zA-Z0-9]+\.(([a-zA-Z0-9]{2,4})|([a-zA-Z0-9]{2,4}\.[a-zA-Z]{2,4}))$/'; break;      
		case 'tel':$t2 = '/^((\(\+?\d{2,3}\))|(\+?\d{2,3}\-))?(\(0?\d{2,3}\)|0?\d{2,3}-)?[1-9]\d{4,7}(\-\d{1,4})?$/'; break; 
		case 'mobile':$t2 = '/^(\+?\d{2,3})?0?1(3\d|5\d|8\d)\d{8}$/'; break; 
		case 'idcard':$t2 = '/(^([\d]{15}|[\d]{18}|[\d]{17}x)$)/'; break; 
		case 'qq':$t2 = '/^[1-9]\d{4,15}$/'; break; 
		case 'url':$t2 = '/^(http|https|ftp):\/\/[a-zA-Z0-9]+\.[a-zA-Z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\'\'])*$/'; break; 
		case 'ip':$t2 = '/^((25[0-5]|2[0-4]\d|(1\d|[1-9])?\d)\.){3}(25[0-5]|2[0-4]\d|(1\d|[1-9])?\d)$/'; break; 
		case 'file':$t2 = '/^[a-zA-Z0-9]{1,50}$/'; break;    
		case 'zipcode':$t2 = '/^\d{6}$/'; break;        
		case 'filename':$t2 = '/^[a-zA-Z0-9]{1,50}$/'; break;       
		case 'date':$t2 = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/'; break;  
		case 'time':$t2 = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/'; break; 
		case 'utf8':$t2 = '%^(?:
						[\x09\x0A\x0D\x20-\x7E] # ASCII
						| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
						| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
						| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
						| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
						| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
						| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
						| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
						)*$%xs'; break;                                   
		default:$t2 = ''; break;      
	}
	$pour = @preg_match($t2, $t0);   
	if ($pour) {  
		return $t0;  
	} else {  
		return false;   
	}  
}

function str_num2rmb($num) {
    $c1 = "零壹贰叁肆伍陆柒捌玖";
    $c2 = "分角元拾佰仟万拾佰仟亿";
    $num = round($num, 2);
    $num = $num * 100;
    if (strlen($num) > 10) {
        return "oh,sorry,the number is too long!";
    }
    $i = 0;
    $c = "";
    while (1) {
        if ($i == 0) {
            $n = substr($num, strlen($num)-1, 1);
        } else {
            $n = $num % 10;
        }
        $p1 = substr($c1, 3 * $n, 3);
        $p2 = substr($c2, 3 * $i, 3);
        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
            $c = $p1 . $p2 . $c;
        } else {
            $c = $p1 . $c;
        }
        $i = $i + 1;
        $num = $num / 10;
        $num = (int)$num;
        if ($num == 0) {
            break;
        }
    }
    $j = 0;
    $slen = strlen($c);
    while ($j < $slen) {
        $m = substr($c, $j, 6);
        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
            $left = substr($c, 0, $j);
            $right = substr($c, $j + 3);
            $c = $left . $right;
            $j = $j-3;
            $slen = $slen-3;
        }
        $j = $j + 3;
    }
    if (substr($c, strlen($c)-3, 3) == '零') {
        $c = substr($c, 0, strlen($c)-3);
    }  
    return $c . "整";
}

function str_money($num,$f=2){
    $str = $num ? number_format($num, $f,'.',',') :'0';
	return $str;
}

function str_money_c($num,$f=2){
	$str = $num ? number_format($num, $f,'.',',') :'0.00';
	return "￥".$str;
}

function str_random($len,$chars='ABCDEFJHIJKMNOPQRSTUVWSYZ'){
	$str = '';
	$max = strlen($chars) - 1;
	for ($i=0;$i<$len;$i++) {
		$str .= $chars[mt_rand(0,$max)];
	}
	return $str;
}

function str_quote($str) {
    $str = explode(',',$str);
	foreach($str as $v) {
	    $arr[] = "'$v'";
	}
	return @join(',',$arr);
}


function str_no($str='') {
	$ci = &get_instance();
	$jxcsys = $ci->session->userdata('jxcsys');
	$date = substr(date("YmdHis"), 2);
	return $str.$jxcsys['sid'].$date;
}

function str_auth($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
	$key_length = 4;
	$key = md5($key != '' ? $key : 'ci');
	$fixedkey = md5($key);
	$egiskeys = md5(substr($fixedkey, 16, 16));
	$runtokey = $key_length ? ($operation == 'ENCODE' ? substr(md5(microtime(true)), -$key_length) : substr($string, 0, $key_length)) : '';
	$keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
	$string = $operation == 'ENCODE' ? sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$egiskeys), 0, 16) . $string : base64_decode(substr($string, $key_length));

	$i = 0; $result = '';
	$string_length = strlen($string);
	for ($i = 0; $i < $string_length; $i++){
		$result .= chr(ord($string{$i}) ^ ord($keys{$i % 32}));
	}
	if($operation == 'ENCODE') {
		return $runtokey . str_replace('=', '', base64_encode($result));
	} else {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$egiskeys), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	}
}

function dir_add($dir,$mode=0777){
    if (is_dir($dir) || @mkdir($dir,$mode)) return true;
    if (!dir_add(dirname($dir),$mode)) return false;
    return @mkdir($dir,$mode);
}

function dir_del($dir) {
    $dir = str_replace('\\', '/', $dir);
	if (substr($dir, -1) != '/') $dir = $dir.'/';
	if (!is_dir($dir)) return false;
	$list = glob($dir.'*');
	foreach($list as $v) {
		is_dir($v) ? dir_del($v) : @unlink($v);
	}
    return @rmdir($dir);
}

function sys_csv($name){
    header("Content-type:text/xls");
    header("Content-Disposition:attachment;filename=".$name);
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Expires:0'); 
	header('Pragma:public');
} 

if (!function_exists('array_column')) {
    function array_column(array $array, $columnKey, $indexKey = null) {
        $result = array();
        foreach ($array as $subArray) {
            if (!is_array($subArray)) {
                continue;
            } elseif (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
                $result[] = $subArray[$columnKey];
            } elseif (array_key_exists($indexKey, $subArray)) {
                if (is_null($columnKey)) {
                    $result[$subArray[$indexKey]] = $subArray;
                } elseif (array_key_exists($columnKey, $subArray)) {
                    $result[$subArray[$indexKey]] = $subArray[$columnKey];
                }
            }
        }
        return $result;
    }
}


function upload($file,$dir,$file_name='',$file_size=20480000,$ext=array('jpg','png','gif','ico')) {
    $name = $_FILES[$file]['name'];                 
    $size = $_FILES[$file]['size'];               
    $type = $_FILES[$file]['type'];  
	$tmp  = $_FILES[$file]['tmp_name'];    
	if (!empty($_FILES[$file]['error'])) {
		switch($_FILES[$file]['error']){
			case '1':$error = '超过php.ini允许的大小';break;
			case '2':$error = '超过表单允许的大小';break;
			case '3':$error = '图片只有部分被上传';break;
			case '4':$error = '请选择图片';break;
			case '6':$error = '找不到临时目录';break;
			case '7':$error = '写文件到硬盘出错';break;
			case '8':$error = 'File upload stopped by extension';break;
			case '999':
			default:$error = '未知错误。';
		}
		alert($error);
	}
	if (!is_dir($dir)) dir_add($dir);  
	if (!is_writable($dir)) return false;   	                                             
    $file_ext = strtolower(trim(substr(strrchr($name,'.'),1)));   
	if (!$file_name) $file_name = date('YmdHis').rand(1000,9999);                           
    $file_name = $file_name.'.'.$file_ext;                  
    $path = $dir.$file_name;                                 
    if (!in_array($file_ext,$ext)) return false;         
    if ($size>$file_size) return false;                             							
    if (!move_uploaded_file($tmp,$path)) {
	    return false;
	} else {
	    $info = array(
		    "old_name"=>$name, 
			"new_name"=>$file_name,   
			"path"=>$path,           
			"size"=>$size,  
			"ext"=>$file_ext,        
			"type"=>$type   
		);
	    return $info;
	} 
}


function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	// 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
	$ckey_length = 4;
	 
	// 密匙
	$key = md5($key ? $key : $GLOBALS['discuz_auth_key']);
	 
	// 密匙a会参与加解密
	$keya = md5(substr($key, 0, 16));
	// 密匙b会用来做数据完整性验证
	$keyb = md5(substr($key, 16, 16));
	// 密匙c用于变化生成的密文
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length):
			substr(md5(microtime()), -$ckey_length)) : '';
	// 参与运算的密匙
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	// 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
	//解密时会通过这个密匙验证数据完整性
	// 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
	sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	// 产生密匙簿
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	// 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	// 核心加解密部分
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		// 从密匙簿得出密匙进行异或，再转成字符
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE') {
		// 验证数据有效性，请看未加密明文的格式
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
				substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
					return substr($result, 26);
				} else {
					return '';
				}
	} else {
		// 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
		// 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}



function encrypt($string,$operation,$key=''){
	$key=md5($key);
	$key_length=strlen($key);
	$string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
	$string_length=strlen($string);
	$rndkey=$box=array();
	$result='';
	for($i=0;$i<=255;$i++){
		$rndkey[$i]=ord($key[$i%$key_length]);
		$box[$i]=$i;
	}
	for($j=$i=0;$i<256;$i++){
		$j=($j+$box[$i]+$rndkey[$i])%256;
		$tmp=$box[$i];
		$box[$i]=$box[$j];
		$box[$j]=$tmp;
	}
	for($a=$j=$i=0;$i<$string_length;$i++){
		$a=($a+1)%256;
		$j=($j+$box[$a])%256;
		$tmp=$box[$a];
		$box[$a]=$box[$j];
		$box[$j]=$tmp;
		$result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
	}
	if($operation=='D'){
		if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){
			return substr($result,8);
		}else{
			return'';
		}
	}else{
		return str_replace('=','',base64_encode($result));
	}
}


/*获取接口配置列表--------------------------------------*/

if ( ! function_exists('get_apis'))
{
	function &get_apis($replace = array())
	{
		static $_apis;

		if (isset($_apis))
		{
			return $_apis[0];
		}

		// Is the config file in the environment folder?
		if ( ! defined('ENVIRONMENT') OR ! file_exists($file_path = APPPATH.'config/'.ENVIRONMENT.'/apis.php'))
		{
			$file_path = APPPATH.'config/apis.php';
		}

		// Fetch the config file
		if ( ! file_exists($file_path))
		{
			exit('The configuration file does not exist.');
		}

		$apis = require($file_path);

		// Does the $config array exist in the file?
		if ( ! isset($apis) OR ! is_array($apis))
		{
			exit('Your config file does not appear to be formatted correctly.');
		}

		return $_apis[0] = &$apis;
	}
}



/*获取ERROR配置列表--------------------------------------*/
if ( ! function_exists('get_error'))
{
	function &get_errors($replace = array())
	{
		static $_errors;

		if (isset($_errors))
		{
			return $_errors[0];
		}
		
		
		// Is the config file in the environment folder?
		if ( ! defined('ENVIRONMENT') OR ! file_exists($file_path = APPPATH.'config/'.ENVIRONMENT.'/errors.php'))
		{
			$file_path = APPPATH.'config/errors.php';
		}

		// Fetch the config file
		if ( ! file_exists($file_path))
		{
			exit('The configuration file does not exist.');
		}

		$apis = require($file_path);

		// Does the $config array exist in the file?
		if ( ! isset($apis) OR ! is_array($apis))
		{
			exit('Your config file does not appear to be formatted correctly.');
		}

		return $_errors[0] = &$apis;
	}
}




/*调用外部接口-------------------------*/
if ( ! function_exists('http_client'))
{
	/**
	 * @param unknown $post_data 业务参数
	 * @param string $code 系统编码
	 * @param string $type 提交类型
	 */
	function &http_client($post_data = array() , $code = 'KZCF' , $type = 'POST'){
		
		$sys_type = strtolower($code);
		
		$result = call_user_func_array("http_client_".$sys_type, array($post_data,$type));
		
		return $result;
	}
}

if ( ! function_exists('http_client_kzcf'))
{
	/**
	 * 调用商城公工方法。
	 * @param unknown $code
	 * @param unknown $post_data
	 * @param string $type
	 * @return array
	 */
	function &http_client_kzcf($post_data = array() , $type = 'POST'){
		
		$url = KZCF_URL;
		
		$post_data['timestamp']= time();
		//$post_data['method']='app.user.item.search';
		$post_data['format']='json';
		$post_data['sign_type']= 'MD5';
		$post_data['v']='v1';
		$post_data['sign']='BEF0798795F077D56D97B9293A7B2E94';
		
		$token = '28062e40a8b27e26ba3be45330ebcb0133bc1d1cf03e17673872331e859d2cd4';
		$o ="";
		foreach ( $post_data as $k => $v )
		{
			$o.= "$k=" . urlencode( $v ). "&" ;
		}
		$sign=strtoupper(md5(strtoupper(md5($post_data['method'])).$token));
		
		$post_data = $o.'sign='.$sign;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data,true);
		return $data;
	}
}


if ( ! function_exists('http_client_nc'))
{
	function &http_client_nc($post_data = array() , $type = 'POST'){
		
		if(empty($post_data['method'])) str_alert(-1,'接口方法名不能为空');
		if(empty($post_data['data'])) str_alert(-1,'提交参数不能为空');
		
		try {
			
			header("content-type:text/html;charset=utf-8");
			
			$client = new SoapClient(NC_URL,array("trace"=>true,'soap_version' => SOAP_1_2,'encoding'=>'utf-8','cache_wsdl' => 0,'compression'=>true));
			
			$params = new SoapParam(array('string'=>$post_data['data']),$post_data['method']);
			
			$time = time();
			$return = $client->__soapCall($post_data['method'], array($params));
			
			
			$service  = new MY_Service();
			$log['params'] = $client->__getLastRequest();
			$log['status'] = $data['status'];
			$log['time']   = $time;
			$log['result'] = $return->return;
			$log['source_type'] = "SMH";
			$log['exec_time'] = time() - $time;
			
			$service->db->insert('zee_apis_log',$log);
			
		} catch (Exception $e) {
			str_alert(-1,'调用接口失败',$e);
		}
		return $return;
	}
}



if ( ! function_exists('http_client_esb'))
{
	function &http_client_esb($post_data = array() , $type = 'POST'){



	}
}

if ( ! function_exists( 'array_bind_key' ) )
{
	/**
	 * 根据传入的数组和数组中值的键值，将对数组的键进行替换
	 *
	 * @param array $array
	 * @param string $key
	 */
	function array_bind_key($array, $key )
	{
		foreach( (array)$array as $value )
		{
			if( !empty($value[$key]) && empty($result[$value[$key]]))
			{
				$k = $value[$key];
				$result[$k] = $value;
			}
		}
		return $result;
	}
}

















 
 
