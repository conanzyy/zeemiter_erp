<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class goodsInfo extends MY_Service {
	
	private $saleModel;
	public function __construct(){
		$this->saleModel = json_decode(SALEMODEL,true);
		parent::__construct();
	}

	public function service($params){
		
		if(empty($params)) $this->_error("E0001","业务参数data不能为空");
		
		$list = array();
		
		if (is_array($params)){
			
			if (array_keys($params) !== range(0, count($params) - 1)){
				$list[] = $params;
			}else{
				$list = $params;
			}
			
		}else{
			$this->_error("E0001","业务参数不能为字符串");
		}
		
		$public = array();//修改公共结算价
		$prvate = array();//修改私有结算价
		$return = array();//返回结果
		
		//拆分参数
		foreach ($list as $key => $value){
				
			if(empty($value['sku_id'])){
				$return[] = array("code"=>"EG001","info"=>"sku_id不能为空");
				continue;
			}
			
			if(empty($value['sale_model'])){
				$return[] = array("code"=>"EG002","info"=>"物料：".$value['sale_model']."销售组织不能为空");
				continue;
			}
			
			$value['sale_model'] = $this->saleModel[$value['sale_model']];
			
			if(empty($value['settle_price'])){
				$return[] = array("code"=>"EG002","info"=>"物料：".$value['sku_id']."结算价为空");
				continue;
			}
			
			
			if(empty($value['sid'])){
				$public[] = $value;
			}else{
				$prvate[] = $value;
			}
		}
		
		/*
		 * 修改公共结算价
		 */
		foreach ($public as $key => $value){
			/* $id = $this->mysql_model->get_row(GOODS,'(skuId="'.$value['sku_id'].'")','id');
			if(empty($id)){
				continue;
			}
			$iid = $this->mysql_model->update(GOODS,array("purPrice"=>$value['settle_price'],'settlePrice'=>$value['settle_price']),'(skuId="'.$value['sku_id'].'")');
			if(empty($iid)){
				$return[] = array("code"=>"EG003","info"=>"修改公工结算价失败! 物料：".$value['sku_id']);
			} */
			
			$id = $this->mysql_model->get_row(GOODS_INFO_P,'(skuId="'.$value['sku_id'].'") and (sale_model="'.$value['sale_model'].'")','id');
			
			if(empty($id)){
			
				$data['sale_model']    = $value['sale_model'];
				$data['skuId'] 		   = $value['sku_id'];
				$data['settle_price']  = $value['settle_price'];
				$data['modified_time'] = time();
					
				$iid = $this->mysql_model->insert(GOODS_INFO_P,$data);
			
			}else{
				$iid = $this->mysql_model->update(GOODS_INFO_P,array("settle_price"=>$value['settle_price']),'(skuId="'.$value['sku_id'].'") and (sale_model="'.$value['sale_model'].'")');
			}
				
			if(empty($iid)){
				$return[] = array("code"=>"EG004","info"=>"修改sid:".$value['sid']."结算价失败：物料：".$value['sku_id']);
			}
			
		}
		
		
		
		/*
		 * 修改私有结算价
		 */
		foreach ($prvate as $key => $value){
			
			$id = $this->mysql_model->get_row(GOODS_INFO,'(skuId="'.$value['sku_id'].'") and (sid="'.$value['sid'].'") and (sale_model="'.$value['sale_model'].'")','id');
				
			if(empty($id)){
				
				$data['sale_model']    = $value['sale_model'];
				$data['sid'] 		   = $value['sid'];
				$data['skuId'] 		   = $value['sku_id'];
				$data['settle_price']  = $value['settle_price'];
				$data['modified_time'] = time();
			
				$iid = $this->mysql_model->insert(GOODS_INFO,$data);
				
			}else{
				$iid = $this->mysql_model->update(GOODS_INFO,array("settle_price"=>$value['settle_price']),'(skuId="'.$value['sku_id'].'") and (sid="'.$value['sid'].'") and (sale_model="'.$value['sale_model'].'")');
			}
			
			if(empty($iid)){
				$return[] = array("code"=>"EG004","info"=>"修改sid:".$value['sid']."结算价失败：物料：".$value['sku_id']);
			}
			
		}
		
		
		$this->_success($return);
	}
	
	
	
	
	
	
	
	/* public function service($params){
		 
		//业务逻辑
		if(empty($params['skuId'])){
	   	
	   		die("{status:error,code:shmE0001,message:商品编号不能为空}");
	   		
	   }else{
	   	   
		   switch ($params['update_type']) {
		   	
		   	///////////////通过商品编码修改销售组织////////////
		   	case "1" :	   		
		   		$skuId = $params['skuId'];
		   		//$idd = $this->mysql_model->get_limit('md5','0','5');
		   		//$idd = $this->mysql_model->get_count('zee_goods_info','(skuId='.$skuId.')');
		   		//echo"<pre>";
		   		//$iid = $this->db->query("select * from zee_goods_info");
		   		//$a=$iid->num_rows();
		   		//print_r($idd);
		   		//die();
				$id = $this->mysql_model->get_row(zee_goods,'(skuId='.$skuId.')','id');
				$data['goodsId']=$id;
				$data['skuId']=$skuId;
		   		$data['modified_time'] = time();
		   		$data['sale_type'] = $params['sale_type'];
		   		$id2 = $this->mysql_model->get_row(zee_goods_info,'(skuId='.$skuId.')','skuId');
		   		if(empty($id2)){
		   			$this->db->trans_begin();
	   				$this->mysql_model->insert("zee_goods_info",$data);
	   				if($this->db->trans_status() === FALSE){
	   					$this->db->trans_rollback();
	   					die("{status:error,message:添加失败}");
	   				}else{
	   					$this->db->trans_commit();
	   					die("{status:success,message:添加成功}");
	   				}
		   		}else{
		   			$this->db->trans_begin();
		   			$this->mysql_model->update("zee_goods_info",$data,'(skuId='.$skuId.')');
		   			if($this->db->trans_status() === FALSE){
		   				$this->db->trans_rollback();
		   				die("{status:error,message:修改失败}");
		   			}else{
		   				$this->db->trans_commit();
		   				die("{status:success,message:修改成功}");
		   			}
		   		}
		   		
		   		break;
		   		
		   		/////////////通过商品编码添加,修改结算价格/////////////
	   		case "2" :
		   		$skuId = $params['skuId'];	   		
				$id = $this->mysql_model->get_row(zee_goods,'(skuId='.$skuId.')','id');
				$data['goodsId']=$id;
				$data['skuId']=$skuId;
		   		$data['modified_time'] = time();
		   		$data['settle_price'] = $params['settle_price'];
		   		$id2 = $this->mysql_model->get_row(zee_goods_info,'(skuId='.$skuId.')','skuId');
		   		if(empty($id2)){
		   			$this->db->trans_begin();
	   				$this->mysql_model->insert("zee_goods_info",$data);
	   				if($this->db->trans_status() === FALSE){
	   					$this->db->trans_rollback();
	   					die("{status:error,message:添加失败}");
	   				}else{
	   					$this->db->trans_commit();
	   					die("{status:success,message:添加成功}");
	   				}
		   		}else{
		   			$this->db->trans_begin();
		   			$this->mysql_model->update("zee_goods_info",$data,'(skuId='.$skuId.')');
		   			if($this->db->trans_status() === FALSE){
		   				$this->db->trans_rollback();
		   				die("{status:error,message:修改失败}");
		   			}else{
		   				$this->db->trans_commit();
		   				die("{status:success,message:修改成功}");
		   			}
		   		}
		   		break;	   			
	   			   		
		   }

		}
		$this->_success($params);
	} */
}