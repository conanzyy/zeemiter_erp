<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 商品出库 处理
 *
 */
class stockOut extends MY_Service {

	
    public function __construct(){
        parent::__construct();
    }
    
    
    /**
     * 出库商品信息
     *  订单id 		-- iid
     *  商品id 		-- skuId
     *  出库累计数量 	-- outNum
     *  出库时间 	-- outTime
     */
    
    public function service($params){
//     	echo '<pre>';print_r($params);die;
    	
		// 判断数据是否全
    	// 判断 该次出库是否为 关闭出库 (若为负  判断 是否可以关闭)
    	//转 nc订单号 为 erp订单号
    	//由于NC处理逻辑此接口按行进行调用
    	foreach ($params['detail'] as $k => $v){
    		if(empty($v['nid']))  $this->_error('E0001','缺少参数：销售订单编号');
    		if(empty($v['outstorecode']))  $this->_error('E0001','缺少参数：销售出库编号');
			if(empty($v['sku_id']))    $this->_error('E0001','缺少参数:skuId');
			if(empty($v['c_num'])) 	   $this->_error('E0001','缺少参数:c_num');
			$invData = $this->mysql_model->get_row(INVOICE_ORDER,'(nid="'.$v['nid'].'")');
			$params['detail'][$k]['iid'] = $iid = $invData['id'];
			$params['detail'][$k]['sid'] = $sid = $invData['sid'];
			$params['detail'][$k]['wid'] = $v['outstorecode'];
			$params['detail'][$k]['boxnum'] = $v['boxnum'] == 'null' ? '' : $v['boxnum'];
			//if(empty($v['box_num'])) $this->_error('E0001',$v['sku_id'].'缺少参数:box_num(箱号)');
			if($v['c_num'] < 0){
				$invid = $this->mysql_model->get_row(INVOICE_ORDER_INFO,'(skuId="'.$v['sku_id'].'" and iid='.$iid.')','invId');
				$totalData = $this->mysql_model->get_row(ORDER_TOTAL,'(iid='.$iid.') and invId='.$invid);
				$totalData['waitInto'] >= abs($v['c_num']) ?  '' : $this->_error('ESO001','删除出库失败，库存不足！');
			}
    	}
    	
        $this->db->trans_begin();
        
    	foreach ($params['detail'] as $k => $v){
    		
    		$iid 			= $v['iid'];
    		$sid 			= $v['sid'];
    		$skuId 	 		= $v['sku_id'];
    		$outNum  		= $v['c_num'];
    		$totalNum  		= $v['total'];
    		$boxNum			= $v['boxnum'];
    		$outTime 		= time();
    		
    		$invId = $this->mysql_model->get_row(INVOICE_ORDER_INFO,'(skuId="'.$v['sku_id'].'" and iid='.$iid.')','invId');
    		
    		$totalData = $this->mysql_model->get_row(ORDER_TOTAL,'(iid='.$iid.') and invId='.$invId);
    		//更改 total 的 waitOut 以及 waitInto
    		$newinfo = array(
    				'waitInto' => $totalData['waitInto'] + $outNum,
    				'waitOut' => $totalData['waitOut'] - $outNum,
    		);
    		
            // 更改 total 表的 相关出入库数据
			$this->mysql_model->update(ORDER_TOTAL,$newinfo,'(iid='.$iid.') and invId='.$invId);
			
			$acceptId = $this->mysql_model->get_row(ORDER_ACCEPT,'(wid="'.$v['wid'].'") and iid="'.$iid.'"',"id");
			if(empty($acceptId)){
				//添加到出库详情表
				$accept = array(
						"iid"=>$iid,
						"nid"=>$v['nid'],
						"wid"=>$v['wid'],
						"time"=>time(),
						"status"=>0
				);
				$acceptId = $this->mysql_model->insert(ORDER_ACCEPT,$accept);
			}
			
			// 拼接 accept、detail 表的数据
    		$accept_detail = array(
    				'sid' 	  => $sid,	 'iid' 	   => $iid,
    				'skuId'   => $skuId, 'invId'   => $invId,
    				'outNum'  => $outNum,'outTime' => $outTime,
    				'boxNum'  => $boxNum,'acceptId'=> $acceptId
    		);
    		
    		$detail = array(
    				'sid' 	  => $sid,	'iid' 	  => $iid,
    				'skuId'   => $skuId,'invId'  => $invId,
    				'totalNum'=> $totalNum,
    		);
    		
    		$isHave = $this->mysql_model->get_row(ORDER_DETAIL,'(skuId='.$skuId.') and iid='.$iid);
    		
    		if(empty($isHave)){
    			$detail['outNum'] = $outNum;
    			$this->mysql_model->insert(ORDER_DETAIL,$detail);
    		}else{
    			$detail['outNum'] = $outNum + $isHave['outNum'];
    			$this->mysql_model->update(ORDER_DETAIL,$detail,'(id='.$isHave['id'].')');
    		}
    		
    		//判断签字，取消签字，再签字。处理逻辑。
    		$ex_detail = $this->mysql_model->get_row(ORDER_DETAIL_ACCEPT,'(1=1) and acceptId='.$acceptId.' and iid = '.$iid.' and invId='.$invId);
    		
    		
    		if(empty($ex_detail)){
    			$this->mysql_model->insert(ORDER_DETAIL_ACCEPT,$accept_detail);
    		}else{
    			$accept_detail['outNum'] = $ex_detail['outNum'] + floatval($outNum);
    			if($accept_detail['outNum'] == 0){
    				$accept_detail['status'] = 2;
    			}else{
    				$accept_detail['status'] = 0;
    			}
    			$this->mysql_model->update(ORDER_DETAIL_ACCEPT,$accept_detail,'(1=1) and acceptId='.$acceptId.' and iid = '.$iid.' and invId='.$invId);
    		}
    		
    		//如果所有出库数量都为零，修改出库单状态为已取消
    		$acceptList = $this->mysql_model->get_results(ORDER_DETAIL_ACCEPT,'(1=1) and acceptId='.$acceptId.' and iid = '.$iid);
    		if(array_sum(array_column($acceptList, "outNum")) == 0){
    			$order_accept = array('status'=>3);
    		}else if(array_sum(array_column($acceptList, "inNum")) == array_sum(array_column($acceptList, "outNum"))){
    			$order_accept = array('status'=>2);
    		}else if(array_sum(array_column($acceptList, "inNum")) > 0 ){
    			$order_accept = array('status'=>1);
    		}else{
    			$order_accept = array('status'=>0);
    		}
    		
    		$this->mysql_model->update(ORDER_ACCEPT,$order_accept,'(wid="'.$v['wid'].'") and iid="'.$iid.'"',"id");
    		
    		// 更改 order 表的订单状态 是否已全部出库
    		$total = $this->mysql_model->get_results(ORDER_TOTAL,'(iid='.$iid.')');
    		$detailData = $this->mysql_model->get_results(ORDER_DETAIL,'(iid='.$iid.')');
    		
    		
    		//修改订单状态，如果已出库数据大于0，修改订单为已出库状态，如果0：刚将状态改为待出库
    		$invTotal = 0;$haveOut = 0;
    		foreach ($total as $value) $invTotal += $value['invNum'];
    		foreach ($detailData as $value) $haveOut += $value['outNum'];
    		 
    		if($haveOut > 0){
    			$data['orderStatus'] = 5;
    			if($invTotal == $haveOut){
    				$data['outkuStatus'] = 2;
    			}else{
    				$data['outkuStatus'] = 1;
    			}
    		}else{
    			$data['orderStatus'] = 4;
    			$data['outkuStatus'] = null;
    		}
    		 
    		//更改 订单状态
    		$this->mysql_model->update(INVOICE_ORDER,$data,'(id='.$iid.')');
    	}
    	
    	if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$this->_error('E00001','出库商品失败');
    	}else{
			$this->db->trans_commit();
			$this->_success('出库商品成功');
    	}
    	$this->_error("E10001");
    }
}
    
    

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php *