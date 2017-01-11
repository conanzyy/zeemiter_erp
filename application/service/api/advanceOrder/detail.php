<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 预订单详情
 *
 */
class detail extends MY_Service {

	private $saleModel;
	
    public function __construct(){
        parent::__construct();
        $this->saleModel = json_decode(SALEMODEL,true);
    }
    
    public function service($params){
    	$id=$params['id'];
    	$orderDetail=$this->mysql_model->get_results(PREORDER_INFO,'(iid="'.$id.'")','*');
    	foreach ($orderDetail as $key => $value){
    		foreach($value as $k => $v){
    			$goodsId=$value['invId'];
    			$goodsDetail=$this->mysql_model->get_row(GOODS,'(skuId="'.$goodsId.'")','*');
    			$orderDetail[$key]['categoryId']=$goodsDetail['categoryName'];
    			$orderDetail[$key]['invId']=$goodsDetail['title'];
    		}
    	}
    	$this->_success($orderDetail);
   }
}