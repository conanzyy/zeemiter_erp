<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class InvPre extends CI_Controller {

	private $saleModel;
	
    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
		$this->jxcsys  = $this->session->userdata('jxcsys');
		$this->saleModel = json_decode(SALEMODEL,true);
    }
	
    private $status = array(
    		'1' => '待审核',
    		'2' => '审核未通过',
    		'3' => '审核通过',
    		'4' => '已采购'
    );
    
	public function index() {
	    $action = $this->input->get('action',TRUE);
		switch ($action) {
			case 'preOrder':
			    $this->common_model->checkpurview(2);
			    $this->preOrder();
				break; 
			case 'preOrderQuery':
				$this->common_model->checkpurview(2);
				$this->load->view('scm/invPre/preOrderQuery');
				break;
			case "preQueryData":
				$this->preQueryData();
				break;
			case "preDetail":
				$this->preDetail();
				break;
			case "delivery":
				$this->delivery();
				break;
			default:
			    $this->common_model->checkpurview(1); 
				$this->preOrder();
		}
	}
	// 订单页面初始数据
	public function preOrder(){
		$id = $this->input->get_post('id',TRUE);
		$category = $this->mysql_model->get_results(CATEGORY,'(parentId = 0)','id,name');
		foreach ($category as $k => $v){
			$nextNode = $this->mysql_model->get_results(CATEGORY,'(parentId ='.$v['id'].')');
			if(empty($nextNode)) $category[$k]['haschild'] = 0 ;
		}
		$data['category'] = $category;
		if(!empty($id)){
			$categorys = $this->mysql_model->get_row(PREORDER,'(id = '.$id.')');
			$category_have = explode(',', $categorys['category']);
			foreach ($category_have as $k => $v){
				$category_has[$k]['id']   = $v;
				$category_has[$k]['name'] = $this->mysql_model->get_row(CATEGORY,'(id = '.$v.')','name');
			}
			$data['preMoney'] = $categorys['quota'];
			$data['category_has'] = $category_has;
			$data['id'] = $id;
		}
		$this->load->view('scm/invPre/preOrder',$data);
	}

	//预订单查询页面初始数据
	public function preQueryData(){
		$data['status'] = 200;
		$data['msg']    = 'success';
		$type   = str_enhtml($this->input->get('typeNumber',TRUE));
		$skey   = str_enhtml($this->input->get('skey',TRUE));
		$where = '(sid='.$this->jxcsys['sid'].')';
		switch ($type) {
			case 'allStatus':
				break;
			case 'pending':
				$where .= ' and status = 1';
				break;
			case 'reviewPassed':
				$where .= ' and status in (3,4)';
				break;
			case 'reviewUnPassed':
				$where .= ' and status = 2';
				break;
			case 'hasPurchase':
				$where .= ' and status = 4';
				break;
		}
		$where .= $skey ? ' and billNo like "%'.$skey.'%"' :'';
		$where .= ' order by id desc';
		$preData = $this->mysql_model->get_results(PREORDER,$where);
	
		foreach ($preData as $key => $value) {
			$preData[$key]['status_cn'] = $this->status[$value['status']];
			$preData[$key]['billDate'] = dateTime($value['billDate']);
		}
		$data['data']['items'] = $preData ;
		$data['data']['totalsize']  = $this->mysql_model->get_count(PREORDER,$where);
		die(json_encode($data));
	}
	
	//提货页面初始数据
	public function delivery(){
		$id = $this->input->get_post('id',TRUE);
		$categorys = $this->mysql_model->get_row(PREORDER,'(id = '.$id.')');
		$category_have = explode(',', $categorys['category']);
		foreach ($category_have as $k => $v){
			$category_has[$k]['id']   = $v;
			$category_has[$k]['name'] = $this->mysql_model->get_row(CATEGORY,'(id = '.$v.')','name');
		}
		$data['quota'] 		= $categorys['quota'];
		$data['laveQuota']  = $categorys['laveQuota'];
		$data['billNo'] 	= $categorys['billNo'];
		$data['CGOId']  	= str_no('CGO');
		$data['category_has'] = $category_has;
		$data['id'] = $id;
		// 		echo '<pre>';print_r($data);die;
		$this->load->view('scm/invPre/delivery',$data);
	}
	
	// 预订单详情页面 
	public function preDetail (){
		$id = $this->input->get_post('id',TRUE);
		$preOrder = $this->mysql_model->get_row(PREORDER,'(id='.$id.')');
// 		echo '<pre>';print_r($preOrder);die;
		$data['billNo'] = $preOrder['billNo'];
		$data['quota'] = $preOrder['quota'];
		$data['laveQuota'] = $preOrder['laveQuota'];
		$data['billDate'] = dateTime($preOrder['billDate']);
		if($preOrder['auditDate']){
			$data['auditDate'] = dateTime($preOrder['auditDate']);
		}
		$data['status'] = $this->status[$preOrder['status']];
		$data['name'] = $this->mysql_model->get_row(ADMIN,'(sid='.$preOrder['sid'].')','name'); 
		
		$goodsList = $this->mysql_model->get_results(PREORDER_INFO,'(iid='.$id.')','name,price');
		$data['goodsList'] = $goodsList;
		$cgList = $this->mysql_model->get_results(PREORDER_SPEND,'(iid='.$id.')','cgoId,invId,num,cgtime');
		foreach ($cgList as $k => $v){
			$goods_list = $this->mysql_model->get_row(GOODS,'(skuId='.$v['invId'].')');
			$cgList[$k]['name'] = $goods_list['brand_name'].' '.$goods_list['name'].' '.$goods_list['number'].' '.$goods_list['skuId'].' '.$goods_list['spec'];
			$cgList[$k]['cgtime'] = dateTime($v['cgtime']);
		}
		$data['cgList'] = $cgList;
// 		echo '<pre>';print_r($cgList);die;
		$this->load->view('scm/invPre/preDetail',$data);
	}
	  
	// 订单页面 获取下级类别
	public function category(){
		$rel = $this->input->get_post('rel',TRUE);
		$data['status'] = 200;
		$data['msg']    = 'success';
		$nextCategory = $this->mysql_model->get_results(CATEGORY,'(parentId ='.$rel.')','id,name,level');
		if(empty($nextCategory)){
			$goodsList = $this->mysql_model->get_results(GOODS,'(categoryId='.$rel.')');
			foreach ($goodsList as $k => $v){
				$goodsList[$k]['title'] = $v['brand_name'].' '.$v['name'].' '.$v['number'].' '.$v['skuId'].' '.$v['spec'];
			}
			$data['goodsList'] = $goodsList;
			$data['nextCategory'] = $this->mysql_model->get_row(CATEGORY,'(id ='.$rel.')');
			$data['level'] = '4';
			die(json_encode($data));
		}else{
			foreach ($nextCategory as $k => $v){
				$nextNode = $this->mysql_model->get_results(CATEGORY,'(parentId ='.$v['id'].')');
				if(empty($nextNode)) $nextCategory[$k]['haschild'] = 0 ;
			}
			$data['nextCategory'] = $nextCategory;
			$data['level'] = $nextCategory[0]['level'];
			die(json_encode($data));
		}
	}
	
	// 提货页面获取商品信息
	public function category_delivery(){
		$rel = $this->input->get_post('rel',TRUE);
		$data['status'] = 200;
		$data['msg']    = 'success';
		$nextCategory = $this->mysql_model->get_results(CATEGORY,'(parentId ='.$rel.')','id,name,level');
		$goodsList = $this->mysql_model->get_results(PREORDER_INFO,'(categoryId='.$rel.')','invId,price,name');
		foreach ($goodsList as $k => $v){
			$goods = $this->mysql_model->get_row(GOODS,'(skuId ='.$v['invId'].')');
			$goodsList[$k]['minNum'] = $goods['minNum'];
		}
		$data['goodsList'] = $goodsList;
		$data['nextCategory'] = $this->mysql_model->get_row(CATEGORY,'(id ='.$rel.')');
		$data['level'] = '4';
		die(json_encode($data));
	}
	
	// 保存订单
	public function savePreOrder(){
		$data = $this->input->get_post('data',TRUE);
		$category = implode(',',$data['preCategory']);
		$sid = $this->jxcsys['sid'];
		$billNo = str_no('PO');
		$time = time();
		$order_data = array(
				'sid' => $sid,	
				'billNo' => $billNo,
				'quota' => abs($data['preMoney']),
				'billDate' => $time,	
				'status' => '1',
				'laveQuota' => abs($data['preMoney']),
				'category' => $category,
		); 
		$this->db->trans_begin();
		if(empty($data['id'])){
			$order_id = $this->mysql_model->insert(PREORDER,$order_data);
		}else{
			$status = $this->mysql_model->get_row(PREORDER,'(id='.$data['id'].')','status');
			if($status == 1){
				$order_id = $data['id'];
				$this->mysql_model->update(PREORDER,$order_data,'(id = '.$order_id.')');
				$this->mysql_model->delete(PREORDER_INFO,'(iid='.$order_id.')');
			}else{
				str_alert(-1,'该预订单已审核完成，不能再更改');
			}
		}
		foreach ( $data['preCategory'] as $k => $v){
			$goodsList = $this->mysql_model->get_results(GOODS,'(categoryId = '.$v.')');
			foreach ($goodsList as $n => $m){
				$s['iid'] 		 = $order_id;
				$s['billNo'] 	 = $billNo;
				$s['categoryId'] = $v;
				$s['invId'] 	 = $m['skuId'];
				$s['price'] 	 = $m['settlePrice'];
				$s['name'] 	 	 = $m['brand_name'].' '.$m['name'].' '.$m['number'].' '.$m['skuId'].' '.$m['spec'];
				$this->mysql_model->insert(PREORDER_INFO,$s);
			}
		}
		if($this->db->trans_status() == FALSE){
			$this->db->trans_rollback();
			str_alert(-1,'保存预订单失败');
		}else{
			$this->db->trans_commit();
			str_alert(200,'success');
		}
	}
	
	// 保存提货单
	public function saveDelivery(){
		$data = $this->input->get_post('data',TRUE);
// 		echo '<pre>';print_r($data);die;
		// 订单表修改状态 采购明细表录入
		$preInfo = array(
				'status' => 4,
				'laveQuota' => abs($data['laveQuota']),
		);
		$this->db->trans_begin();
		
		$this->mysql_model->update(PREORDER,$preInfo,'(id='.$data['id'].')');
		$totalQty = 0;
		foreach ($data['goods'] as $k => $v){
			$s[$k]['invId'] = $v['invId'];
			$s[$k]['price'] = abs($v['price']);
			$s[$k]['num'] 	= abs($v['qty']);
			$s[$k]['iid'] 	= $data['id'];
			$s[$k]['cgoId'] = $data['CGOId'];
			$s[$k]['cgtime'] = time();
			$s[$k]['totalPrice'] = abs($data['totalPrice']);
			$totalQty += $v['qty'];
			$goods = $this->mysql_model->get_row(GOODS,'(skuId = '.$v['invId'].')');
			$entries[$k]['invId'] 	= $goods['id'];
			$entries[$k]['skuId'] 	= $goods['skuId'];
			$entries[$k]['unitId'] 	= $goods['unitId'];
			$entries[$k]['skuId'] 	= $goods['skuId'];
			$entries[$k]['qty'] 	= $v['qty'];
			$entries[$k]['discountRate']	= 0;
			$entries[$k]['deduction'] 		= 0;
			$entries[$k]['price']	= $v['price'];
			$entries[$k]['amount']	= abs($v['price']) * abs($v['qty']);
			$entries[$k]['locationId'] 		= 0;
			$entries[$k]['description']		= '';
		};
		if(isset($s)){
			$this->mysql_model->insert(PREORDER_SPEND,$s);
		}
// 		echo '<pre>';print_r($entries);die;
		// 生成 采购订单
		$sid = $this->jxcsys['sid'];
		$admin = $this->mysql_model->get_row(ADMIN,'(sid = '.$sid.')');
		$orderData = array(
				'id'	=> -1,
				'buId'	=> 1,
				'contactName' => 快准车配,
				'date' => date('Y-m-d',time()),
				'billNo' => $data['billNo'],
				'transType' => 150501,
				'totalQty' => $totalQty,				// 总数量
				'totalAmount' => $data['totalPrice'],	// 购货总金额
    			'description' => '',					// 描述
				'disRate' => 0,							// 折扣率
				'disAmount' => 0,						// 折扣金额
				'amount' => $data['totalPrice'],		// 折扣后金额
				'rpAmount' => 0,						// 本次付款
				'arrears' => $data['totalPrice'],		// 本次欠款
				'totalArrears' => 0,					// totalArrears
				'leibie' => '30-Cxx-01',
				'accId' => 0,							// 结算账户ID
				'billType' => PUR,						// 订单类型
				'transTypeName' => '采购',
				'billDate' => date('Y-m-d',time()),
				'hxStateCode' => 0,						// 0未付款  1部分付款  2全部付款
				'uid' => $admin['uid'],					
				'sid' => $this->jxcsys['sid'],
				'userName' => $admin['name'],
				'modifyTime' => dateTime(time()),
				'entries' => $entries,
		);
// 		echo '<pre>';print_r($orderData);die;
		$this->split_order($orderData);
		if($this->db->trans_status() == FALSE){
			$this->db->trans_rollback();
			str_alert(-1,'保存提货单失败');
		}else{
			$this->db->trans_commit();
			str_alert(200,'success');
		}
	}
	
	public function split_order($data){
	
		if (is_array($data['entries'])) {
	
			$temp = array();
	
			$where  = '(1=1) and sid=1 and type=10 ';
			$rs = $this->mysql_model->get_results(CONTACT,$where);
			$contact = array_bind_key($rs,'number');
			foreach ($data['entries'] as $arr=>$row) {
				$saleModel = $this->mysql_model->get_row(GOODS,'(id = '.$row['invId'].') and skuId = "'.$row['skuId'].'"','saleModel');
				$key  = empty($saleModel) ? 'SELF' : $saleModel ;
				$v = array();$t = array();
				$v['sid']     	  	= $data['sid'];
				$v['buId']          = $contact[$key]['id'];
				$v['billDate']      = $data['billDate'];
				$v['billType']      = $data['billType'];
				$v['transType']     = $data['transType'];
				$v['transTypeName'] = $data['transTypeName'];
				$v['invId']         = $row['invId'];
				$v['skuId']         = $row['skuId'];
				$v['unitId']        = intval($row['unitId']);
				$v['locationId']    = intval($row['locationId']);
				//$v['areaid']    	= intval($row['locationareaId']);
				if ($data['transType'] == 150501) {
					$v['qty']       = abs($row['qty']);
					$v['amount']    = abs($row['amount']);
				} else {
					$v['qty']       = -abs($row['qty']);
					$v['amount']    = -abs($row['amount']);
				}
				$v['price']         = abs($row['price']);
				$v['discountRate']  = $row['discountRate'];
				$v['deduction']     = $row['deduction'];
				$v['description']   = $row['description'];
	
	
				$t['sid']     	  	= $data['sid'];
				$t['invNum']        = abs($row['qty']);
				$t['waitOut']       = abs($row['qty']);
				$t['invId']         = $row['invId'];
	
				$temp[$key]['order'][] = $v;
				$temp[$key]['total'][] = $t;
	
			}
				
// 			echo '<pre>';print_r($temp);die;
			foreach ($temp as $key => $value){
	
				$id = str_no('CGO');
	
				$qtySum = 0;
				$amountSum = 0;
				$areaidSum = 0;
	
				foreach ($value['order'] as $arr => $row){
					$temp[$key]['order'][$arr]['billNo'] = $id;
					$qtySum 	= $qtySum + abs($row['qty']);
					$amountSum  = $amountSum + abs($row['amount']);
					$areaidSum  = $areaidSum + abs($row['areaid']);
					$detail[$arr]['InvCode'] = $row['skuId'];
					$detail[$arr]['Num'] = $row['qty'];
					$detail[$arr]['Price'] = $row['price'];
				}
	
				/* 订单生成 调NC 确认订单 start */
				//接口参数
				$postData = array(
						"OrderDate"	=>$data['date'],
						"StationCode"=>$data['sid'],
						"SaleOrgCode" => $this->saleModel[$key],
						"OrderType"=>$data['leibie'],
						"Detail"=>	$detail,
				);
// 				echo '<pre>';print_r($postData);die;
				$post_data['data'] = json_encode($postData);
				$post_data['method'] = 'makeSoOrder';//接口方法
				$result = http_client($post_data,'NC');
	
				$returns = $result->return;
				if(empty($returns)) str_alert(-1,'调用NC接口失败');
				$return =  json_decode($returns,true);
				if($return['IsSuccess'] == 'false'){
					str_alert(-1,"NC返回".$return['ErrMsg']);
				}
				
				/* 订单生成 调NC 确认订单 end */
				$info = elements(array('billType','transType','transTypeName','buId','billDate','description','totalQty',
						'amount','arrears','rpAmount','totalAmount','hxStateCode','totalArrears','disRate','disAmount',
						'uid','sid','userName','accId','modifyTime','leibie'),$data);
	
				$info['buId']        = $contact[$key]['id'];
				$info['billNo'] 	 = $id;
				$info['totalQty'] 	 = $qtySum;
				$info['totalAmount'] = $amountSum;
				$info['amount'] 	 = $amountSum;
				$info['arrears'] 	 = $amountSum;
				$info['disAmount']   = 0;
				$info['disRate'] 	 = 0;
				$info['rpAmount'] 	 = 0;
				//
				$info['nid'] = $return['SoCode'];
				$info['saleModel'] = $key;
				//
				$temp[$key]['trade'] = $info;
	
				$iid = $this->mysql_model->insert(INVOICE_ORDER,$temp[$key]['trade']);
	
				foreach ($value['order'] as $arr => $row){
					$temp[$key]['order'][$arr]['iid'] = $iid;
						
				}
				foreach ($value['total'] as $arr => $row){
					$temp[$key]['total'][$arr]['iid'] = $iid;
					$temp[$key]['total'][$arr]['billNo'] = $id;
				}
	
				$this->mysql_model->insert(INVOICE_ORDER_INFO,$temp[$key]['order']);
				$this->mysql_model->insert(ORDER_TOTAL,$temp[$key]['total']);
			}
		}
	
		return $temp;
	}
	
}