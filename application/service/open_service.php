<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Open_service extends MY_Service {

	
    public function __construct(){
        parent::__construct();
        $this->apis = get_apis();
    }
    
    
    /**
     * 调用生成接口类
     * @param unknown $params
     */
    public function parse($params){
    	
    	try {
    		
    		$this->checkParams($params);
    		$tradeCode = $params['transCode'];
    		$object = $this->getService($tradeCode);
    		$object->service($params['data']);
    		
    	} catch (Exception $e) {
    		print_r($e);
    	}
    	
    }
    
    
    /**
     * 根据交易码得到接口类
     * @param unknown $tradeCode
     * @return unknown
     */
    private function getService($tradeCode){
    	
    	$obj_str = $this->apis[$tradeCode];
    	 
    	$service =  str_replace(".", "/", $obj_str);
    	$subdir = '';
    	 
    	// Is the service in a sub-folder? If so, parse out the filename and path.
    	if (($last_slash = strrpos($service, '/')) !== FALSE) {
    		// The path is in front of the last slash
    		$subdir = substr($service, 0, $last_slash + 1);
    		 
    		// And the service name behind it
    		$service = substr($service, $last_slash + 1);
    	}
    	
    	$filepath = APPPATH .'service/api/'.$subdir.$service.'.php';
    	 
    	 
    	if ( ! file_exists($filepath)) {
    		$this->_error("E50001",$service."接口类不存在!");
    	}
    	
    	include_once($filepath);
    	
    	$service = ucfirst($service);
    	 
    	$obj =  new $service;
		
    	return $obj;
    }
    
    
    
    
    /**
     * 检查参数
     * @param unknown $data
     */
    private function checkParams($params){
    	
    	empty($params) 			&& $this->_error("E10001");
    	empty($params['data'])  && $this->_error("E10002");
    	empty($params['transCode']) 	&& $this->_error("E10003");;
    	empty($params['reqCode']) 		&& $this->_error("E10004");
    	empty($params['targetCode']) 	&& $this->_error("E10005");
    	empty($this->apis[$params['transCode']]) && $this->_error("E10006");
    
    }
    

	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */