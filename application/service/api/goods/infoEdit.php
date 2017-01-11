<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 商品信息修改
 *
 */
class infoEdit extends MY_Service {

	private $saleModel;
	
    public function __construct(){
        parent::__construct();
        $this->saleModel = json_decode(SALEMODEL,true);
    }
    
    public function service($params){ 
    	
    	
    	//业务逻辑
    	$ncdata = array(
    		"InventoryCode"=>trim($params['sku']['sku_id']),
    	);//接口参数
    	$post_data['data'] = json_encode($ncdata);
    	$post_data['method'] = 'saleOrg';//接口方法
    	$result = http_client($post_data,'NC');
    	$returns = $result->return;
    	$return =  json_decode($returns,true);
    	
    	if($return['IsSuccess'] == 'false'){
    		$this->_error("E0001","NC返回：".$return['ErrMsg']);
    	}
    	
    	$data['skuId'] 		    = $params['sku']['sku_id'];
        $data['title'] 		    = $params['sku']['title'];
        $data['salePrice']	    = $params['sku']['price'];
        $data['brand_id']	    = $params['brand_id'];
        $data['brand_name']	    = $params['brand_name'];
        $data['match_id']	    = $params['match_id'];
        $data['categoryId']	    = $params['cat_id'];
        $data['categoryName']	= $params['cat_name'];
        $data['quantity']		= 9999;
        $data['id']				= $params['item_id'];
        
        $data['saleModel'] 		= $this->saleModel[$return['Data']['code']];
        $data['packSpec']       = $return['Data']['spec'];
        $data['number']		    = $return['Data']['graphid'];
        $data['minNum']         = $return['Data']['orderbatch'];
        $data['skuStatus']      = $return['Data']['materialstatus'];
        $data['stockStatus']    = $return['Data']['stockstatus'];
        $data['skuHot']         = $return['Data']['materialhot'];
        $data['name']           = $return['Data']['materialname'];
        $data['ncname']         = $return['Data']['materialname'];
        $data['spec']           = $return['Data']['materialspec'];
        $data['productCode']    = $return['Data']['graphid'];
        $data['unitName']       = $params['unitName'];
        $data['unitId'] 		= null;
        if($params['unitName']){
        	$uid = $this->mysql_model->get_row(UNIT,'(sid=1) and name="'.$params['unitName'].'"',"id");
        	$data['unitId'] = $uid;
        }
        $data['isSelf'] = 1;
        $data['saleType'] = 1;
        $data['sid'] = 1;
        
        //数据插入数据库
        $this->db->trans_begin();
        $count = $this->mysql_model->get_count(GOODS,'(1=1) and skuId="'.$params['sku']['sku_id'].'" and id='.$params['item_id']);
        if(!empty($params['item_id']) && $count > 0){
        	$iid = $this->mysql_model->update(GOODS,$data,'(1=1) and skuId="'.$params['sku']['sku_id'].'" and id='.$params['item_id']);
        }else{
        	$iid = $this->mysql_model->insert(GOODS,$data);
        }
        
        if ($this->db->trans_status() === FALSE) {     
            $this->db->trans_rollback();
            $this->_error("E10001",'SQL错误'.$this->db->last_query());
        } else {      
            $this->db->trans_commit(); 
            $this->_success($params,"新增成功");
        }
    	$this->_success($params);
   }
}
   

    
    

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */