<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 预订审核
 *
 */
class examine extends MY_Service {

	private $saleModel;
	
    public function __construct(){
        parent::__construct();
        $this->saleModel = json_decode(SALEMODEL,true);
    }
    
    public function service($params){
    	$id=$params['id'];
    	$status=$params['status'];
    	$auditDate=$params['auditDate'];
    	$desc=$params['desc'];
    	$rs=$this->mysql_model->update(PREORDER,array('status'=>$status,'auditDate'=>$auditDate,'desc'=>$desc),'(id="'.$id.'")');
    	if($rs){
//     		/* 预订单生成 调NC  start */
//     		$rowinfo=$this->mysql_model->get_row(PREORDER,'(id="'.$id.'")','*');
// 				//接口参数
// 				$postData = array(
// 						"sid"=>$rowinfo['sid'],
// 						"billNo"=>$rowinfo['billNo'],
// 						"quota" => $rowinfo['quota'],
// 						"auditDate"=>$data['auditDate'],
// 				);
// 				$post_data['data'] = json_encode($postData);
// 				$post_data['method'] = 'advanceOrder';//接口方法
// 				$result = http_client($post_data,'NC');
				
// 				$returns = $result->return;
// 				if(empty($returns)) str_alert(-1,'调用NC接口失败');
// 				$return =  json_decode($returns,true);
// 				if($return['IsSuccess'] == 'false'){
// 					str_alert(-1,"NC返回".$return['ErrMsg']);
// 				}	
// 				/* 预订单生成 调NC end */
    		
    		$this->_success($rs);
    	}else{
    		$this->_error($rs);
    	}
    	
   }
}