<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 采购订单状态修改
 *
 */
class OrderDelete extends MY_Service {

	
    public function __construct(){
        parent::__construct();
    }
    

    public function service($params){
    	
    	if(empty($params)) $this->_error("E0001","业务参数data不能为空");
    	
    	if (is_array($params)){
    	
    		if (array_keys($params) !== range(0, count($params) - 1)){
    			$list[] = $params;
    		}else{
    			$list = $params;
    		}
    	
    	}else{
    		$this->_error("E0001","业务参数不能为字符串");
    	}
    	
    	$return = array();
    	
    	foreach ($list as $key => $value){
    			
    		if(empty($value['nid'])){
    			$return[] = array("code"=>"EGD001","info"=>"订单编码不能为空！");
    			continue;
    		}
    			
    		$iid = $this->mysql_model->update(INVOICE_ORDER,array("isDelete"=>"1",'nid'=>'','description'=>"NC删除"),'(nid="'.$value['nid'].'")');
    			
    		if(empty($iid)){
    			$return[] = array("code"=>"EGD004","info"=>"删除订单失败! 订单编号：".$value['nid']);
    		}
    			
    	}
    	
    	$this->_success($return);
    	
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */