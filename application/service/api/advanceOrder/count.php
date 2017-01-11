<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 预订单列表查询
 *
 */
class count extends MY_Service {

	private $saleModel;
	
    public function __construct(){
        parent::__construct();
        $this->saleModel = json_decode(SALEMODEL,true);
    }
    
    public function service($params){
    	$where=$params['status'];
    	$count=$this->mysql_model->get_count(PREORDER,'(status="'.$where.'")');
    	$data=$count;
    	$this->_success($data);
   }
}
   