<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 商品上下架修改
 *
 */
class disabled extends MY_Service {

	private $saleModel;
	
    public function __construct(){
    	 $this->saleModel = json_decode(SALEMODEL,true);
        parent::__construct();
    }
      
    public function service($params){
    	
    	//业务逻辑
        //商品上下架管理   
        $data['status']=$params['status'];
        $sku_id  = $params['sku_id'];
        $item_id = $params['item_id'];
        //instock 为下架
        
        $this->db->trans_begin();
        if($data['status']=='onsale'){
        	$data = array(
        			"InventoryCode"=>$params['sku_id'],
        	);//接口参数
        	$post_data['data'] = json_encode($data);
        	$post_data['method'] = 'saleOrg';//接口方法
        	$result = http_client($post_data,'NC');
        	$returns = $result->return;
        	$return =  json_decode($returns,true);
        	 
        	if($return['IsSuccess'] == 'false'){
        		$this->_error("E0001","NC返回：".$return['ErrMsg']);
        	}
        	$info['saleModel'] = $this->saleModel[$return['Data']['code']];
        	$info['packSpec'] = $return['Data']['spec'];
        	$info['minNum'] = $return['Data']['orderbatch'];
        	$info['skuStatus'] = $return['Data']['materialstatus'];
        	$info['stockStatus'] = $return['Data']['stockstatus'];
        	$info['skuHot'] = $return['Data']['materialhot'];
        	$info['name'] = $return['Data']['materialname'];
        	//修改结算价
        	$this->update_settle($sku_id);
        	$info['saleType']=0;
        	
        }else{
        	$info['saleType']=1;
        }
        $iid = $this->mysql_model->update(GOODS,$info,'(skuId="'.$sku_id.'")');
        //$this->_error("E0001",$this->db->last_query());
        if ($this->db->trans_status() === FALSE) {     
             $this->db->trans_rollback();
             $this->_error("E0001","修改店管家物料失败:".$this->db->last_query());
        } else {      
             $this->db->trans_commit(); 
        }
        
    	
    	$this->_success($params);
    }
    
    
    
    
    
    /**
     * 修改结算价
     * @param unknown $sku_id
     */
    private function update_settle($sku_id){
    	
    	$data = array(
    			"InventoryCode"=>$sku_id,
    	);//接口参数
    	$post_data['data'] = json_encode($data);
    	$post_data['method'] = 'settlePrice';//接口方法
    	$result = http_client($post_data,'NC');
    	$returns = $result->return;
    	$return =  json_decode($returns,true);
    	 
    	if($return['IsSuccess'] == 'false'){
    		$this->_error("E0001",$return['ErrMsg']);
    	}
    	 
    	if(empty($return['list'])){
    		$this->_error("E0001","结算价为空");
    	}
    	 
    	$list = $return['list'];
    	
    	foreach ($list as $key => $value){
    		
    		if($value['code'] == '1001' || $value['code'] == 'P0001' && empty($value['cuscode'])){
    			$inset = array(
    				'skuId' => $sku_id,
    				'sale_model'=>$this->saleModel[$value['code']] ,
    				'settle_price'=>$value['price'],
    				'modified_time'=>time(),
    			);
    			
    			$id = $this->mysql_model->get_row(GOODS_INFO_P,'(1=1) and skuId="'.$sku_id.'" and sale_model = "'.$inset['sale_model'].'"','id');
    			
    			if(empty($id)){
    				$rs = $this->mysql_model->insert(GOODS_INFO_P,$inset);
    			}else{
    				$rs = $this->mysql_model->update(GOODS_INFO_P,$inset,'(1=1) and skuId="'.$sku_id.'" and sale_model = "'.$inset['sale_model'].'"');
    			}
    			if($rs){
    				return false;
    			}
    			return true;
    			
    		}else if($value['code'] == '1001' || $value['code'] == 'P0001' && !empty($value['cuscode'])){
    			
    			$inset = array(
    					'sid' => $value['cuscode'],
    					'skuId' => $sku_id,
    					'sale_model'=>$this->saleModel[$value['code']] ,
    					'settle_price'=>$value['price'],
    					'modified_time'=>time(),
    			);
    			
    			$id = $this->mysql_model->get_row(GOODS_INFO,'(1=1) and skuId="'.$inset['sku_id'].'" and sale_model = "'.$inset['sale_model'].'" and  sid = "'.$inset['sid'].'"','id');
    			
    			if(empty($id)){
    				$rs = $this->mysql_model->insert(GOODS_INFO,$inset);
    			}else{
    				$rs = $this->mysql_model->update(GOODS_INFO,$inset,'(1=1) and skuId="'.$inset['sku_id'].'" and sale_model = "'.$inset['sale_model'].'" and  sid = "'.$inset['sid'].'"');
    			}
    			
    			if($rs){
    				return false;
    			}
    			return true;
    			
    		}
    		
    	}
    	
    	
    	/*  
    	$public = $list['1001']['price'];
    	$public2 = $list['P0001']['price'];
    	unset($list['1001']);
    	unset($list['P0001']);
    	$list = array_values($list);
    	
    	$public && $rs = $this->settle_price($sku_id,'1001',$public);
    	$public2 && $rs2 = $this->settle_price($sku_id,'P0001',$public2);
    	
    	foreach ($list as $key => $value){
    		$inset = array(
    				'sid' => $value['code'],
    				'skuId' => $sku_id,
    				'sale_model'=>$this->saleModel[$value['vcode']] ,
    				'settle_price'=>$value['price'],
    				'modified_time'=>time(),
    		);
    		
    		$id = $this->mysql_model->get_row(GOODS_INFO,'(1=1) and skuId="'.$inset['sku_id'].'" and sale_model = "'.$inset['sale_model'].'" and  sid = "'.$inset['sid'].'"','id');
    		
    		if(empty($id)){
    			$rs = $this->mysql_model->insert($inset);
    		}else{
    			$rs = $this->mysql_model->update($inset,'(1=1) and skuId="'.$inset['sku_id'].'" and sale_model = "'.$inset['sale_model'].'" and  sid = "'.$inset['sid'].'"');
    		} 
    	}*/
    	
    	return true;
    }
    
    
    
    
    
    private function settle_price($sku_id,$sale_model,$price){
	    $inset = array(
	    		'skuId' => $sku_id,
	    		'sale_model'=>$this->saleModel[$sale_model] ,
	    		'settle_price'=>$price,
	    		'modified_time'=>time(),
	    );
	    
	    $id = $this->mysql_model->get_row(GOODS_INFO_P,'(1=1) and skuId="'.$sku_id.'" and sale_model = "'.$inset['sale_model'].'"','id');
	    
	   	if(empty($id)){
	   		$rs = $this->mysql_model->insert($inset);
	   	}else{
	   		$rs = $this->mysql_model->update($inset,'(1=1) and skuId="'.$sku_id.'" and sale_model = "'.$inset['sale_model'].'"');
	   	}
	    if($rs){
	    	return false;
	    }
    	return true;
    }
}
    
    

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */