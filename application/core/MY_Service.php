<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Service
{
	
    public function __construct()
    {
        log_message('debug', "Service Class Initialized");
    }
 
    function __get($key)
    {
        $CI = &get_instance();
        return $CI->$key;
    }
    
    
    
    
    protected function _error($code = "",$message = "",$data = array()){
    	
    	$errors = get_errors();
    	$errors = empty($errors)  ? $code : $errors;
    	$message = empty($message) ?  $errors[$code] : $message;
    	$params = str_enhtml($this->input->post(NULL,TRUE));
    	
    	$params['params'] = $params['data'];
    	$params['status'] = "error";
    	$params['error_code'] = $code;
    	$params['message'] = $message;
    	$params['result'] = $data;
    	
    	$this->_saveLog($params,$data,$params['reqCode']);
    	die(json_encode($params));
    }
    
    
    protected function _success($data = array(),$message = ""){
    	
    	$params = str_enhtml($this->input->post(NULL,TRUE));
    	$message = empty($message) ?  "调用成功！" : $message;
    	
    	$params['params'] = $params['data'];
    	$params['status'] = "success";
    	$params['message'] = $message;
    	$params['result'] = $data;
    	
    	$this->_saveLog($params,$data,$params['reqCode']);
    	unset($params['data']);
    	die(json_encode($params));
    	
    }
    
    protected  function _saveLog($data = array(),$result = array(),$source_type = "NC"){
    	
    	$log['params'] = json_encode($data);
    	$log['status'] = $data['status'];
    	$log['time'] = time();
    	$log['result'] = json_encode($result);
    	$log['source_type'] = $source_type;
    	
    	$this->db->insert('zee_apis_log',$log);
    	//echo $this->db->last_query();
    }
}