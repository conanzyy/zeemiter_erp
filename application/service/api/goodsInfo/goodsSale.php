<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class goodsSale extends MY_Service {
		
	private $type;
	
	public function __construct(){
		
		$this->type = json_decode(SALEMODEL,true);
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
			
			if(empty($value['sku_id'])){
				$return[] = array("code"=>"EGS001","info"=>"sku_id不能为空！");
				continue;
			}
			if(empty($value['sale_type'])){
				$return[] = array("code"=>"EGS002","info"=>"物料：".$value['sku_id']."供货类型不为空");
				continue;
			}
			
			$sale_type = $this->type[$value['sale_type']];
			$pack_spec = $value['pack_spec'];
			$min_number = $value['min_number'];
			
			$skuStatus = $value['materialstatus'];
			$stockStatus = $value['stockstatus'];
			$skuHot = $value['materialhot'];
			//$name = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence',str_replace($value['materialname']));
			$name = $value['materialname'];
			
			if(empty($sale_type)) {
				$return[] = array("code"=>"EGS003","info"=>"物料：".$value['sku_id']."供货类型参数错误，请确认是否为1001或P0001");
			} 
			
			$inset = array();
			$sale_type && $inset["saleModel"] = $sale_type;//销售组织
			$pack_spec && $inset["packSpec"] = $pack_spec;//包装规格
			$min_number && $inset["minNum"] = $min_number;//最小起定数量。
			$skuStatus && $inset["skuStatus"] = $skuStatus;//物料状态。
			$stockStatus && $inset["stockStatus"] = $stockStatus;//备货状态。
			$skuHot && $inset["skuHot"] = $skuHot;//物料热度。
			$name && $inset["name"] = $name;//物料名称。
			
			$iid = $this->mysql_model->update(GOODS,$inset,'(skuId="'.$value['sku_id'].'")');
			
			if(empty($iid)){
				$return[] = array("code"=>"EG004","info"=>"修改供货类型失败! 物料：".$value['sku_id']);
			}
			
		}
		
		$this->_success($return);
	}
}