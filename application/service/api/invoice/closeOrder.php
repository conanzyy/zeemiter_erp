<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * NC 关闭订单
 *
 */
class closeOrder extends MY_Service {

	
    public function __construct(){
        parent::__construct();
    }
    
    /**
     *  NC nid  sku_id
     * 
     *  店管家
     */
   
    
    public function service($params){
    	
    	if(empty($params['nid'])) $this->_error("CO0001","订单编码不能为空！");
    	 
    	$order = $this->mysql_model->get_row(INVOICE_ORDER,'(nid="'.$params['nid'].'")');
    	
    	$this->db->trans_begin();
    	
    	if(empty($params['detail'])){
    		$params['detail'] = $this->mysql_model->get_results(ORDER_TOTAL,'(iid='.$order['id'].') and waitOut > 0');
    		foreach ($params['detail'] as $k => $v){
    			$closeData[$k] = array(
    					'sid'=>$order['sid'],
    					'iid'=>$order['id'],
    					'closeTime'=>time(),
    					'closeInvId'=>$v['invId'],
    					'closeNum'=>$v['waitOut'],
    			);
    		
    			$totalData['closeNum'] = $v['waitOut'];
    			$totalData['waitOut'] = 0;
    			$this->mysql_model->update(ORDER_TOTAL,$totalData,'(iid='.$order['id'].') and invId='.$v['invId']);
    		}
    		
    	}else{
    		foreach ($params['detail'] as $k => $v){
    			
    			//$invId = $this->mysql_model->get_row(GOODS,'(skuId='.$v['sku_id'].')','id');
    			$invId = $this->mysql_model->get_row(INVOICE_ORDER_INFO,'(skuId="'.$v['sku_id'].'" and iid='.$order['id'].')','invId');
    			$closeNum = $this->mysql_model->get_row(ORDER_TOTAL,'(iid='.$order['id'].') and invId='.$invId,'waitOut');
    			$closeData[$k] = array(
    					'sid'=>$order['sid'],
    					'iid'=>$order['id'],
    					'closeTime'=>time(),
    					'closeInvId'=>$invId,
    					'closeNum'=>$closeNum,
    			);
    		
    			$totalData['closeNum'] = $closeNum;
    			$totalData['waitOut'] = 0;
    			$this->mysql_model->update(ORDER_TOTAL,$totalData,'(iid='.$order['id'].') and invId='.$invId);
    		}
    	}
    	
    	$this->mysql_model->insert(CLOSE_ORDERGOODS,$closeData);
    	
    	if ($this->db->trans_status() === FALSE) {
    		$this->db->trans_rollback();
    		$this->_error("ECO0001","调用失败");
    	}else{
    		$this->db->trans_commit();
    	}
    	$this->_success($params);
    		
    		/* //echo '<pre>';print_r($params);die;
			
    		$iid = $this->mysql_model->get_row(INVOICE_ORDER,'(nid='.$params['nid'].')','id');
    		$sid = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$iid.')','sid');

    		$this->db->trans_begin();
    		
    		foreach ($params['detail'] as $k => $v){
    			$invId = $this->mysql_model->get_row(GOODS,'(skuId='.$skuId.')','id');
    			$closeNum = $this->mysql_model->get_row(ORDER_TOTAL,'(iid='.$iid.')','waitOut');
    			$closeData[$k] = array(
    					'sid'=>$sid,
    					'iid'=>$iid,
    					'closeTime'=>time(),
    					'closeInvId'=>$invId,
    					'closeNum'=>$closeNum,
    			);
    			
    			$totalData['closeNum'] = $closeNum;
    			$totalData['waitOut'] = 0;
    			$this->mysql_model->update(ORDER_TOTAL,$totalData,'(iid='.$iid.') and invId='.$invId);
    		}
			$this->mysql_model->insert(CLOSE_ORDERGOODS,$closeData);
           
            if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
            	die("{status:error,message:调用失败}");
            }else{
			    $this->db->trans_commit();
            	die("{status:success,message:调用成功}");
            } */
    }
}
    
    

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */