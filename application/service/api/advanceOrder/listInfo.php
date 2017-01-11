<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 预订单列表查询
 *
 */
class listInfo extends MY_Service {

	private $saleModel;
	
    public function __construct(){
        parent::__construct();
        $this->saleModel = json_decode(SALEMODEL,true);
    }
    
    public function service($params){
    	$where=$params['status'];
    	$limit=$params['limit'];
    	$offset=$params['offset'];
    	$sql='select * from '.PREORDER.' where status='.$where.' LIMIT '.($offset-1)*$limit.',' .$limit. ';';
    	$result=$this->mysql_model->query(PREORDER,$sql,2);
    	$data=$result;
    	$this->_success($data);
   }
}
   