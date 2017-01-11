<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class InvSa extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
		$this->jxcsys = $this->session->userdata('jxcsys');
		$this->system  = $this->common_model->get_option('system');
		$this->bfb = floatval($this->system['settlePlaces'])/100;
    }

	public function index() {
	    $action = $this->input->get('action',TRUE);
		switch ($action) {
			case 'initSale':
			    $this->common_model->checkpurview(7);
			    $this->load->view('scm/invSa/initSale');	
				break;
            case 'initShopSales':
                $this->common_model->checkpurview(7);
                $this->load->view('scm/invSa/initShopSales');
                break;
            case 'initSales':
                $this->common_model->checkpurview(7);
                $this->load->view('scm/invSa/initShopQuery');
                break;
            case 'editSale':
			    $this->common_model->checkpurview(6);
			    $this->load->view('scm/invSa/initSale');
				break;
			case 'shSale':
				$this->common_model->checkpurview(6);
				$this->load->view('scm/invSa/initSale');
				break;
            case 'salesEdit':
                $this->common_model->checkpurview(6);
                $this->load->view('scm/invSa/initShopSales');
                break;
            case 'initSaleList':
			    $this->common_model->checkpurview(6);
			    $this->load->view('scm/invSa/initSaleList');
				break; 
			case 'initSaleReturn':
			    $this->common_model->checkpurview(6);
			   	$this->load->view('scm/invSa/initSaleReturn');
				break;
			case 'salesReturnMethod':
				$this->common_model->checkpurview(6);
				$this->salesReturn();
				break;
			case 'initSaleReturnDetail':
				$this->common_model->checkpurview(1);
				$this->load->view('scm/invSa/initSaleReturnDetail');
				break;
			case 'SaleReturnDetailMethod':
				$this->common_model->checkpurview(6);
				$this->salesReturnDetail();
				break;
            case 'initShopSaleList':
                $this->common_model->checkpurview(6);
                $this->load->view('scm/invSa/initShopSaleList');
                break;
            case 'ShopSaleList':
                $this->common_model->checkpurview(6);
                $this->ShopSaleList();
                break;
            default:
			    $this->common_model->checkpurview(6);
			    $this->saleList();	
		}
	}
	
	public function saveAftersale(){
	
		//$data = $this->input->post(null,TRUE);
		$post_data['aftersales_bn'] = $_POST['billReturnNo'];
		$post_data['check_result'] = $_POST['choice'];
		$post_data['shop_explanation'] = $_POST['shop_explanation'];
		$post_data['service_id'] = $this->jxcsys['sid'];
		
		$post_data['method'] = "erp.user.return.audit";
		$data = http_client($post_data);
 		$data = $data['result']['result_code'];
		if ($data == 0) {
			str_alert(200,'success');
		} else {
			str_alert(-1,'添加失败');
		}
		//die(json_encode($data));
	}
	
	public function salesReturn(){
		$v = array();
		$data['status'] = 200;
		$post_data['method'] = 'erp.user.return.list';
		$data['msg']    = 'success';
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),20);
		$typeNumber = $this->input->get_post('typeNumber',TRUE);
		$skey = str_enhtml($this->input->get_post('skey',TRUE));
		/*$sidx = str_enhtml($this->input->get_post('sidx',TRUE));
		 $sord = str_enhtml($this->input->get_post('sord',TRUE));
		 $transType = intval($this->input->get_post('transType',TRUE));
		 $hxState   = intval($this->input->get_post('hxState',TRUE));
		 $salesId   = intval($this->input->get_post('salesId',TRUE));
		$matchCon  = str_enhtml($this->input->get_post('matchCon',TRUE));*/
		$types = array("readyReturn"=>0,"hasReturned"=>1,"hasRejected"=>3);
	
		$post_data['status']  = $types[$typeNumber];
		$post_data['created_time_start'] = strtotime($this->input->get_post('beginDate',TRUE));
		$post_data['created_time_end']   = strtotime($this->input->get_post('endDate',TRUE));
		$post_data['service_id'] = $this->jxcsys['sid'];//总分页数
		
		$post_data['page_size'] = $rows;
		$post_data['page_no'] = $page;
		$post_data['aftersales_bn']  = $skey;
		$result = http_client($post_data);
		
		$list = $result['result']['list'];
		$count = $result['result']['count'];
		
		$data['data']['page']      = $page;
		$data['data']['records']   = $count;           //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);
		
		foreach($list as $arr=>$row) {
            $tid = str_replace(',','',number_format($row['tid'] * 1));
            $bn  = str_replace(',','',number_format($row['aftersales_bn'] * 1));
            $isReturn =  $this->mysql_model->get_row(INVOICE,'(1=1) and sourceOrder="'.$bn.'"','isReturn');
			$v[$arr]['billReturnNo'] = $bn;
			$v[$arr]['billNo'] = $tid;
			$v[$arr]['isReturn'] = $isReturn;
			$v[$arr]['goods'] = $row['title'];
			$v[$arr]['from'] = "shop";
			$v[$arr]['status'] = $row['status'];
			if($row['status'] == 0){
				$v[$arr]['handleProgress'] = "待处理";
			}elseif($row['status'] == 1){
				if($isReturn){
					$v[$arr]['handleProgress'] = "已处理(已退货)";
				}else{
					$v[$arr]['handleProgress'] = "已处理(未退货)";
				}
			}else{
				$v[$arr]['handleProgress'] = "已驳回";
			}
			//$v[$arr]['handleProgress'] = $row['status']==0?'待退货':'已退货';
			$v[$arr]['applyDate'] =  date("Y-m-d H:i:s", $row['created_time']);
			$v[$arr]['operation'] = $row[''];
		}
		$data['data']['rows']        = $v;
		die(json_encode($data));
	}
	
	//退货订单详情
	public function salesReturnDetail() {
		$post_data['aftersales_bn'] = $this->input->get_post("aftersales_bn",true);
		$post_data['method'] = 'erp.user.return.detail';
		$data = http_client($post_data);
		$data = $data['result'];
		$this->load->view('scm/invSa/initSaleReturnDetail',$data);
	}
	
	public function saleList(){
	    $v = array();
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),100);
		$sidx = str_enhtml($this->input->get_post('sidx',TRUE));
		$sord = str_enhtml($this->input->get_post('sord',TRUE));
		$transType = intval($this->input->get_post('transType',TRUE));
		$hxState   = intval($this->input->get_post('hxState',TRUE));
		$salesId   = intval($this->input->get_post('salesId',TRUE));
		$matchCon  = str_enhtml($this->input->get_post('matchCon',TRUE));
		$beginDate = str_enhtml($this->input->get_post('beginDate',TRUE));
		$endDate   = str_enhtml($this->input->get_post('endDate',TRUE));
		$contact = str_enhtml($this->input->get_post('contact',TRUE));
		$order = $sidx ? $sidx.' '.$sord :' a.id desc';
		$where = ' and a.billType="SALE"';
		$where .= $contact 		? ' and b.number ='.$contact : '';
		$where .= $transType>0  ? ' and a.transType='.$transType : ''; 
		$where .= $salesId>0    ? ' and a.salesId='.$salesId : ''; 
		$where .= $hxState>0    ? ' and a.hxStateCode='.$hxState : '';  
		$where .= $beginDate    ? ' and a.billDate>="'.$beginDate.'"' : ''; 
		$where .= $endDate      ? ' and a.billDate<="'.$endDate.'"' : '';
		$where .= 				  ' and a.sid = '.$this->jxcsys['sid'];
		
		if($matchCon){
			$caonidaye = $this->mysql_model->get_row(GOODS,'(1=1) and ( number like "%'.$matchCon.'%" or brand_name like "%'.$matchCon.'%" or skuId like "%'.$matchCon.'%" or name like "%'.$matchCon.'%" or spec like "%'.$matchCon.'%")','id');
			if($caonidaye){
				$caonima = array_column($this->mysql_model->get_results(INVOICE_INFO,'(invId = '.$caonidaye.')'),'billNo');
				if (count($caonima) > 0) {
					$cid = join('","', $caonima);
					$where .= ' and a.billNo in("'.$cid.'")';
				}
			}else{
				$where .= ' and (b.name like "%'.$matchCon.'%" or description like "%'.$matchCon.'%" or billNo like "%'.$matchCon.'%")';
			}
		}
		
		$offset = $rows * ($page-1);
		$data['data']['page']      = $page;
		$data['data']['records']   = $this->data_model->get_invoice($where,3);               //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);                   //总分页数
		$list = $this->data_model->get_invoice($where.' order by '.$order.' limit '.$offset.','.$rows.'');  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['hxStateCode']  = intval($row['hxStateCode']);
		    $v[$arr]['checkName']    = $row['checkName'];
			$v[$arr]['checked']      = intval($row['checked']);
			$v[$arr]['salesId']      = intval($row['salesId']);
			$v[$arr]['salesName']    = $row['salesName'];
			$v[$arr]['billDate']     = $row['billDate'];
			$v[$arr]['billStatus']   = $row['billStatus'];
			$v[$arr]['totalQty']     = (float)$row['totalQty'];
			$v[$arr]['id']           = intval($row['id']);
		    $v[$arr]['amount']       = (float)abs($row['amount']);
			$v[$arr]['billStatusName']   = $row['billStatus']==0 ? '未出库' : '全部出库'; 
			$v[$arr]['transType']    = intval($row['transType']); 
			$v[$arr]['rpAmount']     = (float)abs($row['rpAmount']);
			$v[$arr]['contactName']  = $row['contactName'];
			$v[$arr]['description']  = $row['description'];
			$v[$arr]['billNo']       = $row['billNo'];
			$v[$arr]['tid']       	 = $row['tid'];
			$v[$arr]['totalAmount']  = (float)abs($row['totalAmount']);
			$v[$arr]['userName']     = $row['userName'];
			$v[$arr]['transTypeName']= $row['transTypeName'];
			$v[$arr]['sourceType']	 = $row['sourceType'];
			$v[$arr]['sourceOrder']  = $row['sourceOrder'];
			$v[$arr]['from']         = $row['from'];
            
		}
		$data['data']['rows']        = $v;
		die(json_encode($data));
	}

	//手工订单查询列表
    public function salesList(){
        $v = array();
        $data['status'] = 200;
        $data['msg']    = 'success';
        $page = max(intval($this->input->get_post('page',TRUE)),1);
        $rows = max(intval($this->input->get_post('rows',TRUE)),100);
        $sidx = str_enhtml($this->input->get_post('sidx',TRUE));
        $sord = str_enhtml($this->input->get_post('sord',TRUE));
        $transType = intval($this->input->get_post('transType',TRUE));
        $hxState   = intval($this->input->get_post('hxState',TRUE));
        $salesId   = intval($this->input->get_post('salesId',TRUE));
        $matchCon  = str_enhtml($this->input->get_post('matchCon',TRUE));
        $beginDate = str_enhtml($this->input->get_post('beginDate',TRUE));
        $endDate   = str_enhtml($this->input->get_post('endDate',TRUE));
        $order = $sidx ? $sidx.' '.$sord :' a.id desc';
        $where = ' and a.billType="SALE"';
        $where .= $transType>0  ? ' and a.transType='.$transType : '';
        $where .= $salesId>0    ? ' and a.salesId='.$salesId : '';
        $where .= $hxState>0    ? ' and a.hxStateCode='.$hxState : '';
        $where .= $matchCon     ? ' and (b.name like "%'.$matchCon.'%" or description like "%'.$matchCon.'%" or billNo like "%'.$matchCon.'%")' : '';
        $where .= $beginDate    ? ' and a.billDate>="'.$beginDate.'"' : '';
        $where .= $endDate      ? ' and a.billDate<="'.$endDate.'"' : '';
        $where .= 				  ' and a.sid = '.$this->jxcsys['sid'];
        $offset = $rows * ($page-1);
        $data['data']['page']      = $page;
        $data['data']['records']   = $this->data_model->get_sales($where,3);               //总条数
        $data['data']['total']     = ceil($data['data']['records']/$rows);                   //总分页数
        $list = $this->data_model->get_sales($where.' order by '.$order.' limit '.$offset.','.$rows.'');
        foreach ($list as $arr=>$row) {
            $v[$arr]['hxStateCode']  = intval($row['hxStateCode']);
            $v[$arr]['checkName']    = $row['checkName'];
            $v[$arr]['checked']      = intval($row['checked']);
            $v[$arr]['salesId']      = intval($row['salesId']);
            $v[$arr]['salesName']    = $row['salesName'];
            $v[$arr]['billDate']     = $row['billDate'];
            $v[$arr]['billStatus']   = $row['billStatus'];
            $v[$arr]['totalQty']     = (float)$row['totalQty'];
            $v[$arr]['id']           = intval($row['id']);
            $v[$arr]['amount']       = (float)abs($row['amount']);
            $v[$arr]['billStatusName']   = $row['billStatus']==0 ? '未出库' : '全部出库';
            $v[$arr]['transType']    = intval($row['transType']);
            $v[$arr]['rpAmount']     = (float)abs($row['rpAmount']);
            $v[$arr]['contactName']  = $row['contactName'];
            $v[$arr]['description']  = $row['description'];
            $v[$arr]['billNo']       = $row['billNo'];
            $v[$arr]['totalAmount']  = (float)abs($row['totalAmount']);
            $v[$arr]['userName']     = $row['userName'];
            $v[$arr]['transTypeName']= $row['transTypeName'];
        }
        $data['data']['rows']        = $v;
        die(json_encode($data));
    }

	//订单列表
	public function ShopSaleList() {
        $v = array();
        $data['status'] = 200;
        $data['msg']    = 'success';
        $page = max(intval($this->input->get_post('page',TRUE)),1);
        $rows = max(intval($this->input->get_post('rows',TRUE)),20);
        $typeNumber = $this->input->get_post('typeNumber',TRUE);
        $types = array(
            "all"		  =>0,
            "daiqueren"   =>1,
            "daishenhe"   =>2,
            "daifahuo"    =>3,
            "yifahuo"     =>4,
            "yishouhuo"   =>5,
            "yiquxiao"    =>7,
            "yiguanbi"    =>8,
            "yiwancheng"  =>9,
            "weiwancheng" =>10,
        );
        
        
        /*$sidx = str_enhtml($this->input->get_post('sidx',TRUE));
        $sord = str_enhtml($this->input->get_post('sord',TRUE));
        $transType = intval($this->input->get_post('transType',TRUE));
        $hxState   = intval($this->input->get_post('hxState',TRUE));
        $salesId   = intval($this->input->get_post('salesId',TRUE));*/
        $matchCon  = str_enhtml($this->input->get_post('billNo',TRUE));
        $post_data['page_size'] = $rows;
        $post_data['page_no'] = $page;
        $post_data['status'] = $types[$typeNumber];
        $post_data['method'] = "erp.user.order.list";
        $post_data['service_id'] = $this->jxcsys['sid'];
        $post_data['tid'] = $matchCon;
        
        $result = http_client($post_data);
        $list = $result['result']['data']['data'];
        $count = $result['result']['data']['count'];
        
        $data['data']['page']      = $page;
        $data['data']['records']   = $count;               //总条数
        $data['data']['total']     = ceil($count / $rows);
        
        foreach($list as $arr=>$row) {
            $r = $row['tid'];
            $r = $r*1;
            $r = number_format($r);
            $r = str_replace(',','',$r);
            $v[$arr]['billNo'] 		= $r;
            $v[$arr]['contactName'] = $row['account_name'];
            $v[$arr]['billDate'] 	= date("Y-m-d H:i:s", $row['created_time']);
            $v[$arr]['totalAmount'] = $row['total_fee'];
            $v[$arr]['hxStateCode'] = $row['status'];
            $v[$arr]['description'] = $row['shop_memo'];
            $v[$arr]['isPayEnd']    = $row['isPayEnd'];
            $v[$arr]['trade']       = $row;
        }
        $data['data']['rows']       = $v;
        
        die(json_encode($data));
        
    }
    
    
    //订单详情
    public function shopSaleDetail() {
        $post_data['tid'] = $this->input->get_post("billNo",true);
        $post_data['service_id'] = $this->jxcsys['sid'];
        $post_data['method'] = "erp.user.order.list";
        $data =  http_client($post_data);
        $data = $data['result']['data'];
        $this->load->view('scm/invSa/initSaleDetail',$data);
    }

    //取消订单

    public function shopOrderCancel() {
        $post_data['tid']   = $this->input->get('id',TRUE);
        $post_data['method'] = "erp.user.order.cancel";
        $data = http_client($post_data);
        $data = $data['result']['result_code'];
        if ($data == 0) {
            str_alert(200,'success');
        } else {
            str_alert(-1,'取消失败');
        }
    }

    public function storage(){
        $storage = $this->db->query('select * from '.STORAGE.' where isDefault = 1 and sid = '.$this->jxcsys['sid'])->row_array();
        if($storage){
            str_alert(200,"success");
        }else{
            str_alert(-1,"请添加默认仓库");
        }
    }


    //确认发货
    public function shopWait() {
    	$this->common_model->checkpurview(7);
    	$data = $this->input->post('postData',TRUE);
    	if (strlen($data)>0) {
    		$data = (array)json_decode($data, true);
    		$data = $this->validform($data,'trade');
    		$info = elements(array(
    				'billNo',
    				'billType',
    				'transType',
    				'transTypeName',
    				'buId',
    				'billDate',
    				'description',
    				'totalQty',
    				'amount',
    				'arrears',
    				'rpAmount',
    				'totalAmount',
    				'hxStateCode',
    				'totalArrears',
    				'disRate',
    				'disAmount',
    				'salesId',
    				'uid',
    				'sid',
    				'userName',
    				'accId',
    				'modifyTime',
    				'wayId',
    				'sourceOrder',
    				'from'
    		),$data);
    		$info['sourceType'] = 2;
    		$iid = $this->mysql_model->insert(INVOICE,$info);
    		$this->invoice_info($iid,$data);
    		$this->account_info($iid,$data);
    		//$this->storage_info($data);
    		
    		if ($this->db->trans_status() === FALSE) {
    			$this->db->trans_rollback();
    			str_alert(-1,'SQL错误或者提交的是空数据',$data);
    		} else {
    			
    			$post_data['tid']   = $data['id'];
    			$post_data['method'] = "erp.user.order.delivery";
    			$result = http_client($post_data);
    			$result = $result['result']['result_code'];
    			
    			if($result != 0){
    				str_alert(-1,'调用商城接口失败！');
    			}
    			
    			$this->db->trans_commit();
    			$this->common_model->logs('新增销售 单据编号：'.$data['billNo']);
    			str_alert(200,'success',array('id'=>intval($iid)));
    		}
    		
    	}
    	str_alert(-1,'提交的是空数据');

    }
    //确认收货
    public function reciept() {
    	$post_data['tid']   = $this->input->get('id',TRUE);
    	$post_data['method'] = "app.xiulichang.order.confirm";
    	$data = http_client($post_data);
    	$data = $data['result']['result_code'];
    	if ($data == 0) {
    		str_alert(200,'success');
    	} else {
    		str_alert(-1,'确认失败');
    	}
    }

    //同意退货
    public function returnYes() {
    	
    	$this->common_model->checkpurview(7);
    	$data = $this->input->post('postData',TRUE);
    	if (strlen($data)>0) {
    		$data = (array)json_decode($data, true);
    		$data = $this->validform($data,'trade');
    		$data['isReturn'] = 1;
    		$info = elements(array(
    				'billNo',
    				'billType',
    				'transType',
    				'transTypeName',
    				'buId',
    				'billDate',
    				'description',
    				'totalQty',
    				'amount',
    				'arrears',
    				'rpAmount',
    				'totalAmount',
    				'hxStateCode',
    				'totalArrears',
    				'disRate',
    				'disAmount',
    				'salesId',
    				'uid',
    				'sid',
    				'userName',
    				'accId',
    				'modifyTime',
    				'wayId',
    				'sourceOrder',
    				'from',
    				'isReturn'
    		),$data);
    		$info['sourceType'] = 2;
    		$iid = $this->mysql_model->insert(INVOICE,$info);
    		$this->invoice_info($iid,$data);
    		$this->account_info($iid,$data);
    		//$this->storage_info($data);
    	
    		if ($this->db->trans_status() === FALSE) {
    			$this->db->trans_rollback();
    			str_alert(-1,'SQL错误或者提交的是空数据',$data);
    		} else {
    			 
    			
    			/* $post_data['aftersales_bn']   = $data['id'];
    			$post_data['service_id']   = $this->jxcsys['sid'];
    			$post_data['check_result'] = "true";
    			$post_data['method'] = "erp.user.return.audit";
    			$result = http_client($post_data);
    			$result = $result['result']['result_code'];
    			 
    			if($result != 0){
    				str_alert(-1,'调用商城接口失败！');
    			} */
    			 
    			$this->db->trans_commit();
    			$this->common_model->logs('新增销售退货单据编号：'.$data['billNo']);
    			str_alert(200,'success',array('id'=>intval($iid)));
    		}
    	
    	}
    	str_alert(-1,'提交的是空数据');
    	
       /*  $post_data['aftersales_bn']   = $_POST['postData']['id'];
        $post_data['service_id']   = $this->jxcsys['sid'];
        $post_data['method'] = "erp.user.return.list";
        $list = http_client($post_data);
        $list = $list['result']['data']['data']['0'];
        $modelwhere = "";
        $inventory = $this->data_model->get_invoice_info_inventory();
        foreach($list['order'] as $arr=>$row){
            $where = "and a.id = '".$row['item_id']."'";
            $data['entries'][$arr] = $this->data_model->get_goods_kz2($modelwhere,$where,$type=1);
            $data['entries'][$arr]['invId'] = $row['item_id'];
            $data['entries'][$arr]['price'] = $row['price'];
            foreach($_POST['postData']['entries'] as $k=>$v){
                $data['entries'][$arr]['locationId'] = $v['locationId'];
                $data['entries'][$arr]['amount']     = $v['amount'];
                $data['entries'][$arr]['qty']        = $v['qty'];
            }
        }
        $post_data['check_result'] = "true";
        $post_data['method'] = "erp.user.return.audit";
        $result = http_client($post_data);
        $result = 0;
        if ($result == 0) {
            $info['uid'] = $this->jxcsys['uid'];
            $info['sid'] = $this->jxcsys['sid'];
            $info['userName'] = $this->jxcsys['name'];
            $info['billType'] = "SALE";
            $info['transType'] = "150602";
            $info['transTypeName'] = "销退";
            $info['buId'] = $_POST['postData']['buId'];
            $info['billNo'] = $_POST['postData']['billNo'];
            $info['accId'] = $_POST['postData']['accId'];
            $info['wayId'] = $_POST['postData']['wayId'];
            $info['from'] = "shop";
            $info['tid'] = $_POST['postData']['id'];
            $info['billDate'] = date("Y-m-d",time());
            $info['modifyTime'] = date("Y-m-d H:i:s",time());
            $info['description'] = $list['shop_memo'];
            $info['totalQty'] = $_POST['postData']['totalQty'];
            $info['totalAmount'] = $_POST['postData']['amount'];
            $info['amount'] = $_POST['postData']['amount'];
            $info['rpAmount'] = $_POST['postData']['rpAmount'];
            $info['arrears'] = $_POST['postData']['arrears'];
            $data['sid'] = $info['sid'];
            $data['accId'] = $info['accId'];
            //$data['account'] = array('accid' =>"13" ,'payment' => $info['payment'] );
            $data['billNo'] = $info['billNo'];
            $data['billDate'] = $info['billDate'];
            $data['buId'] = $info['buId'];
            $data['transType'] = $info['transType'];
            $data['transTypeName'] = $info['transTypeName'];
            $data['billType'] = $info['billType'];
            $this->db->trans_begin();
            $iid = $this->mysql_model->insert(INVOICE,$info);
            $this->invoice_info($iid,$data);
            $this->account_info($iid,$data);
            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                str_alert(-1,"SQL错误或者提交的是空数据",$data);
            }else{
                $this->db->trans_commit();
                $this->common_model->logs('新增销售 单据编号：'.$info['billNo']);
                str_alert(200,'success',$data);
            }
        } else {
            str_alert(-1,'确认失败',$result);
        } */
    }

    //确认订单
    public function shopOrderConfirm() {
        $post_data['tid']   = $this->input->get('id',TRUE);
        $post_data['method'] = "erp.user.order.confirm";
        $data = http_client($post_data);
        $data = $data['result']['result_code'];
        if ($data == 0) {
            str_alert(200,'success');
        } else {
            str_alert(-1,'确认失败');
        }
    }

    //付款方式页面
    public function finish(){
        $tid = $this->input->get('tid',TRUE);
        $way = $this->db->query('select * from '.CATEGORY.' where typeNumber ="PayMethod" and sid = '.$this->jxcsys['sid'])->result_array();
        $account = $this->db->query('select * from '.ACCOUNT.' where sid ='.$this->jxcsys['sid'])->result_array();
        $data['tid'] = $tid;
        $data['way'] = $way;
        $data['account'] = $account;
        $this->load->view('scm/invSa/payWay',$data);
    }


    //已付款完成订单
    public function shopOrderFinish() {
        $post_data['tid']   = $this->input->post('tid',TRUE);
        $wayId['wayId']   = $this->input->post('wayId',TRUE);
        $accId = $this->input->post('accId',TRUE);
        $post_data['method'] = "erp.user.order.paid.finish";
        $data = http_client($post_data);
        $data = $data['result']['result_code'];
        if ($data == 0) {
            $post_data['method'] = "erp.user.order.list";
            $list = http_client($post_data);
            $list = $list['result']['data']['data'];
            if (count($list)>0) {
                foreach($list as $row) {
                    $userId = $row['user_name'];
                    $bu = $this->mysql_model->get_row(CONTACT,'(1=1) and type="-10" and number='.$userId.' and isDelete=0');
                    $info['billNo'] = str_no('SKD');
                    $info['billType'] = 'RECEIPT';
                    $info['transType'] = 153001;
                    $info['transTypeName'] = '收款';
                    $info['buId'] = $bu['id'];
                    $info['billDate'] = date("Y-m-d",time());
                    $info['description'] = $row['shop_memo'];
                    $info['uid'] = $this->jxcsys['uid'];
                    $info['sid'] = $this->jxcsys['sid'];
                    $info['userName'] = $this->jxcsys['name'];
                    $info['from'] = "shop";
                    $info['wayId'] = $wayId['wayId'];
                    $info['sourceOrder'] = $post_data['tid'];
                }
                $this->db->trans_begin();
                $iid = $this->mysql_model->insert(INVOICE,$info);
               if (is_array($data['entries']) && count($data['entries'])>0) {
                 foreach ($data['entries'] as $arr=>$row) {
                     $v[$arr]['iid']         = $iid;
                     $v[$arr]['sid']         = $info['sid'];
                     $v[$arr]['billId']      = $row['billId'];
                     $v[$arr]['billNo']      = $info['billNo'];
                     $v[$arr]['billDate']    = $info['billDate'];
                     $v[$arr]['transType']   = $info['transType'];
                     $v[$arr]['billType']    = $row['billType'];
                     $v[$arr]['billPrice']   = (float)$row['billPrice'];
                     $v[$arr]['hasCheck']    = (float)$row['hasCheck'];
                     $v[$arr]['notCheck']    = (float)$row['notCheck'];
                     //$rpAmount         +=    $v[$arr]['nowCheck']    = (float)$row->nowCheck;
                 }
                 if (isset($v)) {
                     $this->mysql_model->insert(RECEIPT_INFO,$v);
                 }
             }
             $amount = 0;
             $arr = "0";
             $s[$arr]['iid']           = $iid;
             $s[$arr]['sid']           = $info['sid'];
             $s[$arr]['billNo']        = $info['billNo'];
             $s[$arr]['buId']          = $info['buId'];
             $s[$arr]['billType']      = $info['billType'];
             $s[$arr]['billDate']      = $info['billDate'];
             $s[$arr]['transType']     = $info['transType'];
             $s[$arr]['transTypeName'] = $info['transTypeName'];
                $s[$arr]['accId'] = $accId;
                $s[$arr]['payment']       = (float)$list['0']['payment'];
                $s[$arr]['wayId'] = $wayId['wayId'];
                $r = $list['0']['tid'];
                $r = $r*1;
                $r = number_format($r);
                $r = str_replace(',','',$r);
                $s[$arr]['settlement']    = $r;
                $s[$arr]['remark']        = $list['0']['shop_memo'];
                $amount            +=     (float)$row['payment'];
                if (isset($s)) {
                    $this->mysql_model->insert(ACCOUNT_INFO,$s);
                }
             $info['amount']    =  0;
             $info['rpAmount']  =  $amount;
             $info['arrears']   =   -$amount;
                $invoice['rpAmount'] = $amount;
                $invoice['arrears'] = 0;
                $invoice['wayId'] = $wayId['wayId'];
                $invoice['accId'] = $accId;
                $this->mysql_model->update(INVOICE,$info,'(id='.$iid.')');
                $this->mysql_model->update(INVOICE,$invoice,'(sourceOrder="'.$post_data['tid'].'")');
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    str_alert(-1,'SQL错误回滚',$invoice);
                } else {
                    $this->db->trans_commit();
                    $this->common_model->logs('新增收款单 单据编号：'.$info['billNo']);
                    str_alert(200,'success',array('id'=>$iid));
                }
            }else{
                str_alert(-1,'提交的是空数据');
            }
        } else {
            str_alert(-1,'修改失败');
        }
    }
    //未付款完成
    public function shopOrderUnFinish() {
        $post_data['tid']   = $this->input->get('id',TRUE);
        $post_data['method'] = "erp.user.order.unpaid.finish";
        $data = http_client($post_data);
        $data = $data['result']['result_code'];
        if ($data == 0) {
            str_alert(200,'success');
        } else {
            str_alert(-1,'修改失败');
        }
    }

    //修改备注

    public function beiZu() {
        $post_data['tid'] = $_POST['tid'];
        $post_data['beizu'] = $_POST['shop_memo'];
        $post_data['method'] = "erp.user.order.remark";
        $data = http_client($post_data);
        $data = $data['result']['result_code'];
        if ($data == 0) {
            str_alert(200,'success');
        } else {
            str_alert(-1,'添加失败');
        }
    }
    //修改价格
    public function shopSalePrice() {
        $post_data['tid'] = $this->input->get_post("billNo",true);
        $post_data['method'] = "erp.user.order.list";
        $data = http_client($post_data);
        $post_data['method'] = "erp.user.order.history.price";
        $list = http_client($post_data);
        $list = $list['result']['data'];
        $data = $data['result']['data'];
        $data['list'] = $list;
        $this->load->view('scm/invSa/initSaleEditPrice',$data);
    }
    public function priceEdit() {
        $oid = $_POST['oid'];
        $post_data['oid'] = implode(",",$oid);
        $post_data['tid'] = $_POST['tid'];
        $price = $_POST['price'];
        $post_data['price'] = implode(",",$price);
        $post_data['method'] = "erp.user.order.price";
        $data = http_client($post_data);
        $data = $data['result']['result_code'];
        if ($data == 0) {
            str_alert(200,'success');
        } else {
            str_alert(-1,'修改失败');
        }
    }
	//导出
	public function exportInvSa() { 
	    $this->common_model->checkpurview(10);
		$name = 'sales_record_'.date('YmdHis').'.xls';
		sys_csv($name);
		$this->common_model->logs('导出销售单据:'.$name);
		$sidx = str_enhtml($this->input->get_post('sidx',TRUE));
		$sord = str_enhtml($this->input->get_post('sord',TRUE));
		$transType = intval($this->input->get_post('transType',TRUE));
		$hxState   = intval($this->input->get_post('hxState',TRUE));
		$salesId   = intval($this->input->get_post('salesId',TRUE));
		$matchCon  = str_enhtml($this->input->get_post('matchCon',TRUE));
		$beginDate = str_enhtml($this->input->get_post('beginDate',TRUE));
		$endDate   = str_enhtml($this->input->get_post('endDate',TRUE));
		$order = $sidx ? $sidx.' '.$sord :' a.id desc';
		$where = ' and billType="SALE"';
		$where .= $transType>0  ? ' and a.transType='.$transType : ''; 
		$where .= $salesId>0    ? ' and a.salesId='.$salesId : ''; 
		$where .= $hxState>0    ? ' and a.hxStateCode='.$hxState : ''; 
		$where .= $matchCon     ? ' and (b.name like "%'.$matchCon.'%" or a.description like "%'.$matchCon.'%" or a.billNo like "%'.$matchCon.'%")' : ''; 
		$where .= $beginDate    ? ' and a.billDate>="'.$beginDate.'"' : ''; 
		$where .= $endDate      ? ' and a.billDate<="'.$endDate.'"' : '';
		$where .= 				  ' and a.sid = '.$this->jxcsys['sid'];
		
		$where1 = ' and a.billType="SALE"';
		$where1 .= $transType>0  ? ' and a.transType='.$transType : ''; 
		$where1 .= $salesId>0    ? ' and a.salesId='.$salesId : ''; 
		$where1 .= $hxState>0    ? ' and a.hxStateCode='.$hxState : ''; 
		$where1 .= $beginDate    ? ' and a.billDate>="'.$beginDate.'"' : ''; 
		$where1 .= $endDate      ? ' and a.billDate<="'.$endDate.'"' : '';
		
		$data['transType'] = $transType;
		$data['list1'] = $this->data_model->get_invoice($where.' order by '.$order.'');  
		$data['list2'] = $this->data_model->get_invoice_info($where1.' order by billDate');  
		$this->load->view('scm/invSa/exportInvSa',$data);
	}

	//新增
	public function add(){
	    $this->common_model->checkpurview(7);
	    $data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
            $data = (array)json_decode($data, true);
            $data = $this->validform($data);
            $info = elements(array(
                'billNo',
                'billType',
                'transType',
                'transTypeName',
                'buId',
                'billDate',
				'description',
				'totalQty',
				'amount',
				'arrears',
				'rpAmount',
				'totalAmount',
				'hxStateCode',
				'totalArrears',
				'disRate',
				'disAmount',
				'salesId',
				'uid',
				'sid',	
				'userName',
				'accId',
				'modifyTime',
                'wayId',
				),$data);
            foreach ($data['entries'] as $k=>$v){
                $qty[] = intval($v['qty']);
                $price[] = intval($v['price']);
                $skuId[] = $v['skuId'];
            }
            $post_data['quantity'] = implode(',',$qty);
            $post_data['prices'] = implode(',',$price);
            $post_data['sku_id'] = implode(',',$skuId);
            $contact = $this->mysql_model->get_results(CONTACT,'(isDelete=0) and type = -10 and sid = '.$this->jxcsys['sid']);
            $contactInfo = array_bind_key($contact,'id');
            $post_data['login_account'] = $contactInfo[$info['buId']]['number'];
            $post_data['method'] = "erp.user.order.quickfinish.finish";
            $info['sourceType'] = 1;
			$this->db->trans_begin();
            $result = http_client($post_data);
            $result = $result['result'];
            if($result['result_code'] == 0){
                $info['sourceOrder'] = $result['tid'];
                $iid = $this->mysql_model->insert(INVOICE,$info);
                $this->invoice_info($iid,$data);
                $this->account_info($iid,$data);
                if($this->db->trans_status() === false){
                    $this->db->trans_rollback();
                    str_alert(-1,'SQL错误或者提交的是空数据',$data);
                }else{
                    $this->db->trans_commit();
                    $this->common_model->logs('新增销售 单据编号：'.$data['billNo']);
                    str_alert(200,'success',array('id'=>intval($iid)));
                }
            }else{
                str_alert(-1,"保存失败！");
            }
//			$iid = $this->mysql_model->insert(INVOICE,$info);
//			$this->invoice_info($iid,$data);
//			$this->account_info($iid,$data);
//			//$this->storage_info($data);
//			if ($this->db->trans_status() === FALSE) {
//			    $this->db->trans_rollback();
//				str_alert(-1,'SQL错误或者提交的是空数据',$data);
//			} else {
//			    $this->db->trans_commit();
//				$this->common_model->logs('新增销售 单据编号：'.$data['billNo']);
//				str_alert(200,'success',array('id'=>intval($iid)));
//			}
		}
		str_alert(-1,'提交的是空数据'); 
    }
	
	//新增
	public function addNew(){
	    $this->add();
    }
    //新增手工订单
    public function addSales(){
        $this->common_model->checkpurview(7);
        $data = $this->input->post('postData',TRUE);
        if (strlen($data)>0) {
            $data = (array)json_decode($data, true);
            $data = $this->validform($data);
            $info = elements(array(
                'billNo',
                'billType',
                'transType',
                'transTypeName',
                'buId',
                'billDate',
                'description',
                'totalQty',
                'amount',
                'arrears',
                'rpAmount',
                'totalAmount',
                'hxStateCode',
                'totalArrears',
                'disRate',
                'disAmount',
                'salesId',
                'uid',
                'sid',
                'userName',
                'accId',
                'modifyTime',
                'wayId'
            ),$data);
            $this->db->trans_begin();
            $iid = $this->mysql_model->insert(SALES,$info);
            $this->sales_info($iid,$data);
            $this->sales_accountInfo($iid,$data);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1,'SQL错误或者提交的是空数据');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('新增手工订单编号：'.$data['billNo']);
                str_alert(200,'success',array('id'=>intval($iid)));
            }
        }
        str_alert(-1,'提交的是空数据');
    }
	
	//修改
	public function updateInvSa(){
	    $this->common_model->checkpurview(8);
	    $data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
		    $data = (array)json_decode($data, true); 
			$id   = intval($data['id']);
			$data = $this->validform($data);
		    $info = elements(array(
				'billType',
				'transType',
				'transTypeName',
				'buId',
				'billDate',
				'description',
				'totalQty',
				'amount',
				'arrears',
				'rpAmount',
				'totalAmount',
				'hxStateCode',
				'totalArrears',
				'disRate',
				'disAmount',
				'salesId',
				'uid',
				'userName',
				'accId',
				'modifyTime',
                'wayId'
				),$data);
		    
		    $info['sourceOrder'] = $data['sourceOrder'];
		    if(empty($info['sourceOrder'])){
		    	$info['sourceType'] = 1;
		    }else{
		    	$info['sourceType'] = 2;
		    }
			$this->db->trans_begin();
			$this->mysql_model->update(INVOICE,$info,'(id='.$id.')');
			$this->invoice_info($id,$data);
			$this->account_info($id,$data);
			if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				str_alert(-1,'SQL错误或者提交的是空数据'); 
			} else {
			    $this->db->trans_commit(); 
				$this->common_model->logs('修改销售 单据编号：'.$data['billNo']);
				str_alert(200,'success',array('id'=>$id,"info"=>$info)); 
			}
		}
		str_alert(-1,'提交的数据不为空'); 
    }

    //修改
    public function updateSale(){
        $this->common_model->checkpurview(8);
        $data = $this->input->post('postData',TRUE);
        if (strlen($data)>0) {
            $data = (array)json_decode($data, true);
            $id   = intval($data['id']);
            $data = $this->validforms($data);
            $info = elements(array(
                'billType',
                'transType',
                'transTypeName',
                'buId',
                'billDate',
                'description',
                'totalQty',
                'amount',
                'arrears',
                'rpAmount',
                'totalAmount',
                'hxStateCode',
                'totalArrears',
                'disRate',
                'disAmount',
                'salesId',
                'uid',
                'userName',
                'accId',
                'modifyTime',
                'wayId'
            ),$data);
            $this->db->trans_begin();
            $this->mysql_model->update(SALES,$info,'(id='.$id.')');
            $this->sales_info($id,$data);
            $this->sales_accountInfo($id,$data);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1,'SQL错误或者提交的是空数据');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('修改手工订单编号：'.$data['billNo']);
                str_alert(200,'success',array('id'=>$id));
            }
        }
        str_alert(-1,'提交的数据不为空');
    }


    //获取库位
    public function area(){
    	$qty = $_POST['num'];
        $invId = $_POST['invId'];
        $locationName = $_POST['locationId'];
        $oid = $_POST['id'];
        $transType = intval($this->input->get_post('transType',TRUE));
        
        $areaStorage = $this->data_model->get_area_info_inventory();
        
        if($oid){//如果是修改加上原来单据上的库存
        	$isEx = $this->mysql_model->get_results(INVOICE_INFO,'(isDelete=0) and iid="'.$oid.'" and  invId='.$invId);
        	foreach ($isEx as $key => $value){
        		$areaStorage[$invId][$locationName][$value['areaId']] += abs($value['qty']);
        	}
        }
        
        $data = $areaStorage[$invId][$locationName];
        $id = array_keys($data);
        
         //退货逻辑
        if($transType == '150602'){
        	if(empty($data)){
        		$area_id = $this->mysql_model->get_row(GOODS_AREA_REL,'(1=1) and item_id="'.$invId.'" and storage_id='.$locationName,"area_id");
        	}else{
        		$area_id = $id[0];
        	}
        	
        	if(empty($area_id)){
        		$areaInfo = $this->mysql_model->get_row(STORAGE_AREA,'(1=1) and str_id='.$locationName . ' and area_code = "IN-00-01"');
        	}else{
        		$areaInfo = $this->mysql_model->get_row(STORAGE_AREA,'(1=1) and str_id='.$locationName . ' and id = "'.$area_id.'"');
        	}
        	
        	$list['arr'] = $areaInfo['id'];
        	$list['areaInfo'] = $areaInfo;
        	die(json_encode($list));
        } 
        
        //判断是否有库存
        if(empty($data)){
        	$list['storage'] = "0";
        	$list['cur_num'] = 0;
        	$list['arr'] = "noStorage";
        	die(json_encode($list));
        }
        
        //判断是否有库存
        if($data[$id[0]] < $qty){
        	$list['storage'] = "0";
        	$list['cur_num'] = $data[$id[0]];
        	$list['arr'] = "noStorage";
        	die(json_encode($list));
        }
        
        
      	$areaInfo = $this->db->query('select * from '.STORAGE_AREA.' where id = '.$id[0])->row_array();
       	$areaInfo['num'] = $data[$id[0]];
       	$list['areaInfo'] = $areaInfo;
       	$list['arr'] = $id[0];
       	$list['storage'] = "1";
	   	die(json_encode($list));
    }

    //配置选择库位
    public function set(){
        $invId = $_GET['invId'];
        $locationId = $_GET['locationId'];
        $list = $this->db->query('select * from '.GOOODS_STORAGE.$this->jxcsys['sid'].' where itemId ='.$invId.' and storageId ='.$locationId)->result_array();
        foreach($list as $arr=>$row){
            $area = $this->db->query('select * from '.STORAGE_AREA.' where id = '.$row['areaId'])->row_array();
            $list[$arr]['areaName'] = $area['area_name'];
        }
        $data['list'] = $list;
        $data['name'] = $_GET['name'];
        $this->load->view('scm/invSa/set',$data);
    }


	//获取修改信息
	public function update() {
	    $this->common_model->checkpurview(6);
	    $id   = intval($this->input->get_post('id',TRUE));
		$data =  $this->data_model->get_invoice('and (a.id='.$id.') and billType="SALE"',1);
		if (count($data)>0) {
			$s = $v = array();
			$info['status'] = 200;
			$info['msg']    = 'success'; 
			$info['data']['id']                 = intval($data['id']);
			$info['data']['from']                 = $data['from'];
			$info['data']['buId']               = intval($data['buId']);
			$info['data']['wayId']               = intval($data['wayId']);
			$info['data']['cLevel']             = 0;
			$info['data']['contactName']        = $data['contactName'];
			$info['data']['salesId']            = intval($data['salesId']);
			$info['data']['date']               = $data['billDate'];
			$info['data']['billNo']             = $data['billNo'];
			$info['data']['billType']           = $data['billType'];
			$info['data']['transType']          = intval($data['transType']);
			$info['data']['totalQty']           = (float)$data['totalQty'];
			$info['data']['modifyTime']         = $data['modifyTime'];
			$info['data']['checkName']          = $data['checkName'];
			$info['data']['disRate']            = (float)$data['disRate'];
			$info['data']['disAmount']          = (float)$data['disAmount'];
			$info['data']['amount']             = (float)abs($data['amount']);
			$info['data']['rpAmount']           = (float)abs($data['rpAmount']);
			$info['data']['customerFree']       = (float)$data['customerFree'];
			$info['data']['arrears']            = (float)abs($data['arrears']);
			$info['data']['userName']           = $data['userName'];
			$info['data']['status']             = intval($data['checked'])==1 ? 'view' : 'edit'; //edit
			$info['data']['totalDiscount']      = (float)$data['totalDiscount'];
			$info['data']['totalAmount']        = (float)abs($data['totalAmount']); 
			$info['data']['description']        = $data['description'];
			$info['data']['sourceOrder']        = $data['sourceOrder'];
			$info['data']['sourceType']         = $data['sourceType'];
			$info['data']['from']         		= $data['from'];
			
			$where3 = ' and billType="SALE"';
			$oprice = $this->data_model->get_settle_price3($where3);
			
			$list = $this->data_model->get_invoice_info('and (iid='.$id.') order by id');
			foreach ($list as $arr=>$row) {
				$retailPrice = $this->mysql_model->get_row(RETAILPRICE,'(goodsId='.$row['invId'].') and (isDelete=0)','retailPrice');
				$v[$arr]['infoId']            = $row['id'];
				$v[$arr]['invSpec']           = $row['invSpec'];
				$v[$arr]['taxRate']           = (float)$row['taxRate'];
				$v[$arr]['srcOrderEntryId']   = intval($row['srcOrderEntryId']);
				$v[$arr]['srcOrderNo']        = $row['srcOrderNo'];
				$v[$arr]['retailPrice']   	  = empty($retailPrice) ? '{}' : $retailPrice;
				$v[$arr]['srcOrderId']        = intval($row['srcOrderId']);
				$v[$arr]['goods']             = $row['invNumber'].' '.$row['brand_name'].' '.$row['skuId'].' '.$row['invName'].' '.$row['invSpec'];
				$v[$arr]['invName']           = $row['invName'];
				$v[$arr]['qty']               = (float)abs($row['qty']);
				$v[$arr]['amount']            = (float)abs($row['amount']);
				$v[$arr]['taxAmount']         = (float)$row['taxAmount'];
				$v[$arr]['price']             = (float)$row['price'];
				$v[$arr]['oprice'] 		      = !empty($row['oprice']) ? $row['oprice'] : !empty($oprice[$row['invId']][$info['data']['buId']]) ? str_money($oprice[$row['invId']][$info['data']['buId']]) : '最近无销售';
				$v[$arr]['lprice'] 		      = str_money($row['lprice']);
				$v[$arr]['tax']               = (float)$row['tax'];
				$v[$arr]['mainUnit']          = $row['mainUnit'];
				$v[$arr]['deduction']         = (float)$row['deduction'];
				$v[$arr]['invId']             = $row['invId'];
				$v[$arr]['skuId']             = $row['skuId'];
				$v[$arr]['invNumber']         = $row['invNumber'];
				$v[$arr]['locationId']        = intval($row['locationId']);
				$v[$arr]['locationName']      = $row['locationName'];
				$v[$arr]['locationAreaId']	  = intval($row['areaNo']);
				$v[$arr]['locationArea']   	  = $row['areaName'];
				$v[$arr]['discountRate']      = $row['discountRate'];
				$v[$arr]['description']       = $row['description'];
				$v[$arr]['unitId']       = intval($row['unitId']);
				$v[$arr]['mainUnit']     = $row['mainUnit'];
			}

			$info['data']['entries']     = $v;
			$info['data']['accId']       = (float)$data['accId'];
			$accounts = $this->data_model->get_account_info('and (iid='.$id.') order by id');  
			foreach ($accounts as $arr=>$row) {
				$s[$arr]['invoiceId']     = intval($id);
				$s[$arr]['billNo']        = $row['billNo'];
				$s[$arr]['buId']          = intval($row['buId']);
			    $s[$arr]['billType']      = $row['billType'];
				$s[$arr]['transType']     = $row['transType'];
				$s[$arr]['transTypeName'] = $row['transTypeName'];
				$s[$arr]['billDate']      = $row['billDate']; 
			    $s[$arr]['accId']         = intval($row['accId']);
				$s[$arr]['account']       = $row['accountNumber'].' '.$row['accountName']; 
				$s[$arr]['payment']       = (float)abs($row['payment']); 
				$s[$arr]['wayId']         = (float)$row['wayId'];
				$s[$arr]['way']           = $row['categoryName']; 
				$s[$arr]['settlement']    = $row['settlement']; 
		    }  
			$info['data']['accounts']     = $s;
			die(json_encode($info));
		}
		str_alert(-1,'单据不存在、或者已删除');  
    }

    //获取修改信息
    public function updateInfo() {
        $this->common_model->checkpurview(6);
        $post_data['tid']   = $this->input->get_post('id',TRUE);
        $post_data['form']  = $this->input->get_post('from',true); 
        $post_data['method'] = "erp.user.order.list";
        $post_data['service_id'] = $this->jxcsys['sid'];
        $result = http_client($post_data);
        $data = $result['result']['data']['data']['0'];
        
        if (count($data)>0) {
            $v = array();
            
            //获取最新结算价
            $oprice = $this->get_sale_price();
            $userId = $data['user_name'];
            $bu = $this->mysql_model->get_row(CONTACT,'(1=1) and type="-10" and number='.$userId.' and isDelete=0');
            $info['status'] = 200;
            $info['msg']    = 'success';
            $info['data']['id']					= $post_data['tid'];
            $info['data']['from']               = "qr";
            $info['data']['cLevel']             = 0;
            $info['data']['salesId']            = intval($data['salesId']);
            $info['data']['date']               = date("Y-m-d",time());
            $info['data']['buId']     			= $bu['id'];
            $info['data']['contactName']        = $bu['number'] .' '. $bu['name'];
            $info['data']['billNo']             = str_no("XS");
            $info['data']['billType']           = "SALE";
            $info['data']['transType']          = "150601";
            $info['data']['totalQty']           = (float)$data['itemnum'];
            $info['data']['checkName']          = $data['checkName'];
            $info['data']['disRate']            = (float)$data['disRate'];
            $info['data']['disAmount']          = (float)$data['disAmount'];
            $info['data']['amount']             = (float)abs($data['total_fee']);
            $info['data']['rpAmount']           = (float)abs($data['rpAmount']);
            $info['data']['customerFree']       = (float)$data['customerFree'];
            $info['data']['arrears']            = (float)abs($data['total_fee']);
            $info['data']['userName']           = $data['userName'];
            $info['data']['status']             = intval($data['checked'])==1 ? 'view' : 'edit'; //edit
            $info['data']['totalDiscount']      = (float)$data['totalDiscount'];
            $info['data']['totalAmount']        = (float)abs($data['total_fee']);
            $info['data']['description']        = $data['description'];
            $info['data']['salesId']            = intval($data['salesId']);
            $info['data']['accId']            	= 0;
            $info['data']['wayId']            	= 0;
            $info['data']['sourceOrder']        = str_replace(',','',number_format($data['tid']*1));
            $info['data']['form']				= $post_data['form'];

            $item_id = array_column($data['order'], 'item_id');
            $where .= ' and a.id in ('.join(',',$item_id).')';
            $orders = $this->data_model->get_goods($where, 2);
            $odata = array_bind_key($data['order'], "item_id");

            foreach ($orders as $arr=>$row) {
            	$retailPrice = $this->mysql_model->get_row(RETAILPRICE,'(goodsId='.$row['id'].') and (isDelete=0)','retailPrice');
                $v[$arr]['tid'] 			  = $arr;
                $v[$arr]['invSpec']           = $row['invSpec'];
                $v[$arr]['taxRate']           = (float)$row['taxRate'];
                $v[$arr]['srcOrderEntryId']   = intval($row['srcOrderEntryId']);
                $v[$arr]['srcOrderNo']        = $row['srcOrderNo'];
                $v[$arr]['retailPrice']   	  = empty($retailPrice) ? '{}' : $retailPrice;
                $v[$arr]['srcOrderId']        = intval($row['srcOrderId']);
                $v[$arr]['qty']               = (float)abs($odata[$row['id']]['num']);
                $v[$arr]['amount']            = (float)abs($odata[$row['id']]['total_fee']);
                $v[$arr]['taxAmount']         = (float)$row['taxAmount'];
                $v[$arr]['lprice'] 		      = str_money($row['settlePrice'] * $this->bfb);
                $v[$arr]['oprice']         	  = !empty($list[$buId][$row['id']]['price']) ? floatval($list[$buId][$row['id']]['price']) : '最近无销售';
                $v[$arr]['price']             = (float)str_money($odata[$row['id']]['price']);
                $v[$arr]['tax']               = (float)$row['tax'];
                $v[$arr]['mainUnit']          = $row['mainUnit'];
                $v[$arr]['deduction']         = (float)$row['deduction'];
                $v[$arr]['invId']             = $row['id'];
                $v[$arr]['skuId']             = $row['skuId'];
                $v[$arr]['goods']             = $row['number'].' '.$row['brand_name'].' '.$row['skuId'].' '.$row['name'].' '.$row['spec'];
                $v[$arr]['invName']           = $row['name'];
                $v[$arr]['mainUnit']          = $row['unitName'];
                $v[$arr]['unitId']     	      = $row['unitId'];
                $v[$arr]['invNumber']         = $row['number'];
                //查询默认仓库
                $storage = $this->mysql_model->get_row(STORAGE,'(1=1) and sid = '.$this->jxcsys['sid'] . ' and isDefault=1');
                $v[$arr]['locationId']        = intval($storage['id']);
                $v[$arr]['locationName']      = $storage['name'];
                $v[$arr]['locationAreaId']    = intval($row['areaNo']);
                $v[$arr]['locationArea']      = $row['areaName'];
                $v[$arr]['discountRate']      = $row['discountRate'];
                $v[$arr]['description']       = $row['description'];
            }
            $info['data']['entries']     = $v;
            die(json_encode($info));
        }
        str_alert(-1,'单据不存在、或者已删除');
    }
    
    //获取收货修改信息
    public function updateSh() {
    	$this->common_model->checkpurview(6);
    	$post_data['tid']   = $this->input->get_post('id',TRUE);
    	$post_data['method'] = "erp.user.order.list";
    	$post_data['service_id'] = $this->jxcsys['sid'];
    	$result = http_client($post_data);
    	$data = $result['result']['data']['data']['0'];
    
    	if (count($data)>0) {
    		$v = array();
    
    		//获取最新结算价
    		$oprice = $this->get_sale_price();
    		$userId = $data['user_name'];
    		$bu = $this->mysql_model->get_row(CONTACT,'(1=1) and type="-10" and number='.$userId.' and isDelete=0');
    
    		$info['status'] = 200;
    		$info['msg']    = 'success';
    		$info['data']['id'] = $post_data['tid'];
    		$info['data']['from']               = "sh";
    		$info['data']['cLevel']             = 0;
    		$info['data']['salesId']            = intval($data['salesId']);
    		$info['data']['date']               = date("Y-m-d",time());
    		$info['data']['buId']     			= $bu['id'];
    		$info['data']['contactName']        = $bu['number'] .' '. $bu['name'];
    		$info['data']['billNo']             = str_no("XS");
    		$info['data']['billType']           = "SALE";
    		$info['data']['transType']          = "150601";
    		$info['data']['totalQty']           = (float)$data['itemnum'];
    		$info['data']['checkName']          = $data['checkName'];
    		$info['data']['disRate']            = (float)$data['disRate'];
    		$info['data']['disAmount']          = (float)$data['disAmount'];
    		$info['data']['amount']             = (float)abs($data['total_fee']);
    		$info['data']['rpAmount']           = (float)abs($data['rpAmount']);
    		$info['data']['customerFree']       = (float)$data['customerFree'];
    		$info['data']['arrears']            = (float)abs($data['total_fee']);
    		$info['data']['userName']           = $data['userName'];
    		$info['data']['status']             = intval($data['checked'])==1 ? 'view' : 'edit'; //edit
    		$info['data']['totalDiscount']      = (float)$data['totalDiscount'];
    		$info['data']['totalAmount']        = (float)abs($data['total_fee']);
    		$info['data']['description']        = $data['description'];
    		$info['data']['salesId']            = intval($data['salesId']);
    		$info['data']['accId']            	= 0;
    		$info['data']['wayId']            	= 0;
    
    
    		$item_id = array_column($data['order'], 'item_id');
    		$where .= ' and a.id in ('.join(',',$item_id).')';
    		$orders = $this->data_model->get_goods($where, 2);
    		$odata = array_bind_key($data['order'], "item_id");
    
    		$where3 = ' and billType="SALE"';
    		$temp = $this->data_model->get_settle_price3($where3);
    		$list = array_bind_key($temp, "invId");
    		
    
    		foreach ($orders as $arr=>$row) {
    			$retailPrice = $this->mysql_model->get_row(RETAILPRICE,'(goodsId='.$row['id'].') and (isDelete=0)','retailPrice');
    			$tid = $row['tid']*1;
    			$tid = number_format($tid);
    			$tid = str_replace(',','',$tid);
    			$v[$arr]['tid'] = $tid;
    			$v[$arr]['invSpec']           = $row['invSpec'];
    			$v[$arr]['taxRate']           = (float)$row['taxRate'];
    			$v[$arr]['srcOrderEntryId']   = intval($row['srcOrderEntryId']);
    			$v[$arr]['srcOrderNo']        = $row['srcOrderNo'];
    			$v[$arr]['retailPrice']   	  = empty($retailPrice) ? '{}' : $retailPrice;
    			$v[$arr]['srcOrderId']        = intval($row['srcOrderId']);
    			$v[$arr]['qty']               = (float)abs($odata[$row['id']]['num']);
    			$v[$arr]['amount']            = (float)abs($odata[$row['id']]['total_fee']);
    			$v[$arr]['taxAmount']         = (float)$row['taxAmount'];
    			$v[$arr]['oprice']         	  = !empty($list[$buId][$row['id']]['price']) ? floatval($list[$buId][$row['id']]['price']) : '最近无销售';
    			$v[$arr]['lprice'] 		      = $row['settlePrice'] * $this->bfb;
    			$v[$arr]['price']             = (float)$odata[$row['id']]['price'];
    			$v[$arr]['tax']               = (float)$row['tax'];
    			$v[$arr]['mainUnit']          = $row['mainUnit'];
    			$v[$arr]['deduction']         = (float)$row['deduction'];
    			$v[$arr]['invId']             = $row['id'];
    			$v[$arr]['goods']             = $row['number'].' '.$row['brand_name'].' '.$row['skuId'].' '.$row['name'].' '.$row['spec'];
    			$v[$arr]['invName']           = $row['name'];
    			$v[$arr]['mainUnit']          = $row['unitName'];
    			$v[$arr]['unitId']     	      = $row['unitId'];
    			$v[$arr]['invNumber']         = $row['number'];
    			$v[$arr]['locationId']        = intval($row['locationId']);
    			$v[$arr]['locationName']      = $row['locationName'];
    			$v[$arr]['locationAreaId']    = intval($row['areaNo']);
    			$v[$arr]['locationArea']      = $row['areaName'];
    			$v[$arr]['discountRate']      = $row['discountRate'];
    			$v[$arr]['description']       = $row['description'];
    		}
    		$info['data']['entries']     = $v;
    		die(json_encode($info));
    	}
    	str_alert(-1,'单据不存在、或者已删除');
    }
    //获取退货修改信息
    public function updateInfos() {
        $this->common_model->checkpurview(6);
        $post_data['aftersales_bn']   = $this->input->get_post('id',TRUE);
        $post_data['method'] = "erp.user.return.list";
        $post_data['service_id'] = $this->jxcsys['sid'];
        $result = http_client($post_data);
        $data = $result['result']['list']['0'];
        if (count($data)>0) {
        	$v = array();
        	
        	
        	//获取最新结算价
        	$oprice = $this->get_sale_price();
        	$userId = $data['user_name'];
        	$bu = $this->mysql_model->get_row(CONTACT,'(1=1) and type="-10" and number='.$userId.' and isDelete=0');
        
        	$info['status'] = 200;
        	$info['msg']    = 'success';
        	$info['data']['id']					= $post_data['aftersales_bn'];
        	$info['data']['from']               = "th";
        	$info['data']['cLevel']             = 0;
        	$info['data']['salesBackId']        = intval($data['salesBackId']);
        	$info['data']['date']               = date("Y-m-d",time());
        	$info['data']['buId']     			= $bu['id'];
        	$info['data']['contactName']        = $bu['number'] .' '. $bu['name'];
        	$info['data']['billNo']             = str_no("XS");
        	$info['data']['billType']           = "SALE";
        	$info['data']['transType']          = "150602";
        	$info['data']['totalQty']           = (float)$data['num'];
        	$info['data']['checkName']          = $data['checkName'];
        	$info['data']['disRate']            = (float)$data['disRate'];
        	$info['data']['disAmount']          = (float)$data['disAmount'];
        	$info['data']['amount']             = (float)abs($data['total_fee']);
        	$info['data']['rpAmount']           = (float)abs($data['rpAmount']);
        	$info['data']['customerFree']       = (float)$data['customerFree'];
        	$info['data']['arrears']            = (float)abs($data['total_fee']);
        	$info['data']['userName']           = $data['userName'];
        	$info['data']['status']             = intval($data['checked'])==1 ? 'view' : 'edit'; //edit
        	$info['data']['totalDiscount']      = (float)$data['totalDiscount'];
        	$info['data']['totalAmount']        = (float)abs($data['total_fee']);
        	$info['data']['description']        = $data['description'];
        	$info['data']['salesId']            = intval($data['salesId']);
        	$info['data']['accId']            	= 0;
        	$info['data']['wayId']            	= 0;
        	$info['data']['sourceOrder']        = str_replace(',','',number_format($data['aftersales_bn'] * 1));
        	$info['data']['form']				= $post_data['form'];
        
        	$item_id = array_column($data['order'], 'item_id');
        	$where .= ' and a.id in ('.join(',',$item_id).')';
        	$orders = $this->data_model->get_goods($where, 2);
        	$odata = array_bind_key($data['order'], "item_id");
        
        	foreach ($orders as $arr=>$row) {
        		$retailPrice = $this->mysql_model->get_row(RETAILPRICE,'(goodsId='.$row['id'].') and (isDelete=0)','retailPrice');
        		$v[$arr]['tid'] 			  = $arr;
        		$v[$arr]['invSpec']           = $row['invSpec'];
        		$v[$arr]['taxRate']           = (float)$row['taxRate'];
        		$v[$arr]['srcOrderEntryId']   = intval($row['srcOrderEntryId']);
        		$v[$arr]['srcOrderNo']        = $row['srcOrderNo'];
        		$v[$arr]['retailPrice']   	  = empty($retailPrice) ? '{}' : $retailPrice;
        		$v[$arr]['srcOrderId']        = intval($row['srcOrderId']);
        		$v[$arr]['qty']               = (float)abs($odata[$row['id']]['num']);
        		$v[$arr]['amount']            = (float)abs($odata[$row['id']]['total_fee']);
        		$v[$arr]['taxAmount']         = (float)$row['taxAmount'];
        		$v[$arr]['oprice']         	  = !empty($oprice[$row['id']][$bu['id']]['price']) ? floatval($oprice[$row['id']][$bu['id']]['price']) : '最近无销售';
        		$v[$arr]['lprice'] 		      = str_money($row['settlePrice'] * $this->bfb);
        		$v[$arr]['price']             = (float)str_money($odata[$row['id']]['price']);
        		$v[$arr]['tax']               = (float)$row['tax'];
        		$v[$arr]['mainUnit']          = $row['mainUnit'];
        		$v[$arr]['deduction']         = (float)$row['deduction'];
        		$v[$arr]['invId']             = $row['id'];
        		$v[$arr]['goods']             = $row['number'].' '.$row['brand_name'].' '.$row['skuId'].' '.$row['name'].' '.$row['spec'];
        		$v[$arr]['invName']           = $row['name'];
        		$v[$arr]['mainUnit']          = $row['unitName'];
        		$v[$arr]['unitId']     	      = $row['unitId'];
        		$v[$arr]['invNumber']         = $row['number'];
        		$storage = $this->mysql_model->get_row(STORAGE,'(1=1) and sid = '.$this->jxcsys['sid'] . ' and isDefault=1');
        		$v[$arr]['locationId']        = intval($storage['id']);
        		$v[$arr]['locationName']      = $storage['name'];
        		$v[$arr]['locationAreaId']    = intval($row['areaNo']);
        		$v[$arr]['locationArea']      = $row['areaName'];
        		$v[$arr]['discountRate']      = $row['discountRate'];
        		$v[$arr]['description']       = $row['description'];
        	}
        	$info['data']['entries']     = $v;
        	die(json_encode($info));
        }
        str_alert(-1,'单据不存在、或者已删除');
    }

	//获取手工订单修改信息
    public function updateSales() {
        $this->common_model->checkpurview(6);
        $id   = intval($this->input->get_post('id',TRUE));
        $data =  $this->data_model->get_sales('and (a.id='.$id.') and billType="SALE"',1);
        if (count($data)>0) {
            $s = $v = array();
            $info['status'] = 200;
            $info['msg']    = 'success';
            $info['data']['id']                 = intval($data['id']);
            $info['data']['buId']               = intval($data['buId']);
            $info['data']['wayId']               = intval($data['wayId']);
            $info['data']['cLevel']             = 0;
            $info['data']['contactName']        = $data['contactName'];
            $info['data']['salesId']            = intval($data['salesId']);
            $info['data']['date']               = $data['billDate'];
            $info['data']['billNo']             = $data['billNo'];
            $info['data']['billType']           = $data['billType'];
            $info['data']['transType']          = intval($data['transType']);
            $info['data']['totalQty']           = (float)$data['totalQty'];
            $info['data']['modifyTime']         = $data['modifyTime'];
            $info['data']['checkName']          = $data['checkName'];
            $info['data']['disRate']            = (float)$data['disRate'];
            $info['data']['disAmount']          = (float)$data['disAmount'];
            $info['data']['amount']             = (float)abs($data['amount']);
            $info['data']['rpAmount']           = (float)abs($data['rpAmount']);
            $info['data']['customerFree']       = (float)$data['customerFree'];
            $info['data']['arrears']            = (float)abs($data['arrears']);
            $info['data']['userName']           = $data['userName'];
            $info['data']['status']             = intval($data['checked'])==1 ? 'view' : 'edit'; //edit
            $info['data']['totalDiscount']      = (float)$data['totalDiscount'];
            $info['data']['totalAmount']        = (float)abs($data['totalAmount']);
            $info['data']['description']        = $data['description'];
            $list = $this->data_model->get_sales_info('and (iid='.$id.') order by id');
            foreach ($list as $arr=>$row) {
                $retailPrice = $this->mysql_model->get_row(RETAILPRICE,'(goodsId='.$row['invId'].') and (isDelete=0)','retailPrice');
                $v[$arr]['infoId']            = $row['id'];
                $v[$arr]['invSpec']           = $row['invSpec'];
                $v[$arr]['taxRate']           = (float)$row['taxRate'];
                $v[$arr]['srcOrderEntryId']   = intval($row['srcOrderEntryId']);
                $v[$arr]['srcOrderNo']        = $row['srcOrderNo'];
                $v[$arr]['retailPrice']   	  = empty($retailPrice) ? '{}' : $retailPrice;
                $v[$arr]['srcOrderId']        = intval($row['srcOrderId']);
                $v[$arr]['goods']             = $row['invNumber'].' '.$row['invName'].' '.$row['invSpec'];
                $v[$arr]['invName']      = $row['invName'];
                $v[$arr]['qty']          = (float)abs($row['qty']);
                $v[$arr]['locationName'] = $row['locationName'];
                $v[$arr]['amount']       = (float)abs($row['amount']);
                $v[$arr]['taxAmount']    = (float)$row['taxAmount'];
                $v[$arr]['price']        = (float)$row['price'];
                $v[$arr]['lprice'] 		 = $row['settlePrice'] * $this->bfb;
                $v[$arr]['tax']          = (float)$row['tax'];
                $v[$arr]['mainUnit']     = $row['mainUnit'];
                $v[$arr]['deduction']    = (float)$row['deduction'];
                $v[$arr]['invId']        = $row['invId'];
                $v[$arr]['invNumber']    = $row['invNumber'];
                $v[$arr]['locationId']   = intval($row['locationId']);
                $v[$arr]['locationName'] = $row['locationName'];
                $v[$arr]['locationAreaId']= intval($row['areaNo']);
                $v[$arr]['locationArea']  = $row['areaName'];
                $v[$arr]['discountRate'] = $row['discountRate'];
                $v[$arr]['description']  = $row['description'];

                $v[$arr]['unitId']       = intval($row['unitId']);
                $v[$arr]['mainUnit']     = $row['mainUnit'];
            }

            $info['data']['entries']     = $v;
            $info['data']['accId']       = (float)$data['accId'];
            $accounts = $this->data_model->get_sales_accountInfo('and (iid='.$id.') order by id');
            foreach ($accounts as $arr=>$row) {
                $s[$arr]['invoiceId']     = intval($id);
                $s[$arr]['billNo']        = $row['billNo'];
                $s[$arr]['buId']          = intval($row['buId']);
                $s[$arr]['billType']      = $row['billType'];
                $s[$arr]['transType']     = $row['transType'];
                $s[$arr]['transTypeName'] = $row['transTypeName'];
                $s[$arr]['billDate']      = $row['billDate'];
                $s[$arr]['accId']         = intval($row['accId']);
                $s[$arr]['account']       = $row['accountNumber'].' '.$row['accountName'];
                $s[$arr]['payment']       = (float)abs($row['payment']);
                $s[$arr]['wayId']         = (float)$row['wayId'];
                $s[$arr]['way']           = $row['categoryName'];
                $s[$arr]['settlement']    = $row['settlement'];
            }
            $info['data']['accounts']     = $s;
            die(json_encode($info));
        }
        str_alert(-1,'单据不存在、或者已删除');
    }


    //打印
    public function toPdf() {
	    $this->common_model->checkpurview(88);
	    $id   = intval($this->input->get('id',TRUE));
		$data = $this->data_model->get_invoice('and (a.id='.$id.') and billType="SALE" and a.sid='.$this->jxcsys['sid'],1);  
		if (count($data)>0) { 
			$data['num']    = 8;
			$data['system'] = $this->common_model->get_option('system'); 
			$list = $this->data_model->get_invoice_info('and (iid='.$id.') and sid='.$this->jxcsys['sid'].' order by id');  
			$data['countpage']  = ceil(count($list)/$data['num']);   //共多少页
			
			
            $result = $this->db->query("select * from ".CONTACT." where number ='".$data['contactNo']."' and isDelete = 0")->row_array();
            $link = json_decode($result['linkMans'],true);
            $link = array_bind_key($link, 'linkFirst');
            $data['contact'] = empty($link[1]) ? $link[0] : $link[1];
            $data['contactNo'] = $result['number'];
            
			foreach($list as $arr=>$row) {
                $res = $this->db->query('select * from '.GOODS.' where id ='.$row['invId'])->row_array();
                $area_code = $this->mysql_model->get_row(STORAGE_AREA,"(1=1) and id ='".$row['areaId']."'","area_code");
                
                $discountRate = "";
                $index = strpos($res['title'],'适用');
                if($index > 0){
                	$discountRate = substr($res['title'], $index+6);//截取商城商品名称中的适用车型
                }
                
			    $data['list'][] = array(
				'i'=>$arr + 1,
				'goods'=>$res,
				'invSpec'=>$row['invSpec'],
				'qty'=>abs($row['qty']),
				'price'=>$row['price'],
			    'carModel'=>$discountRate,
			    'description'=>$row['description'],
				'discountRate'=>$row['discountRate']>0?$row['discountRate']:'',
				'deduction'=>$row['deduction']>0?$row['deduction']:'',
				'amount'=>$row['amount'],
				'locationName'=>$row['locationName'],
			   	'area_code'=>$area_code
				);
			}
			$info = $this->db->query('select * from '.OPTIONS." where option_name = 'system' and sid = ".$this->jxcsys['sid'])->row_array();
            $data['info'] = get_object_vars(json_decode($info['option_value']));
		    ob_start();
			$this->load->view('scm/invSa/toPdf',$data);
			$content = ob_get_clean();
			require_once('./application/libraries/html2pdf/html2pdf.php');
			try {
			    $html2pdf = new HTML2PDF('P', 'A4', 'en');
				$html2pdf->setDefaultFont('javiergb');
				$html2pdf->pdf->SetDisplayMode('fullpage');
				$html2pdf->writeHTML($content, '');
				$html2pdf->Output('invSa_'.date('ymdHis').'.pdf');
			}catch(HTML2PDF_exception $e) {
				echo $e;
				exit;
			}
		} else {
		    str_alert(-1,'单据不存在、或者已删除');  	  
		}  
	}

	//打印确认发货出库单
    public function toPdfs() {
        $data = $this->input->post('postData',TRUE);
        $data = (array)json_decode($data, true);
        $data['system'] = $this->common_model->get_option('system');
        $result = $this->db->query("select * from ".CONTACT." where name ='".$data['contactName']."'")->row_array();
        $link = json_decode($result['linkMans'],true);
        $link = array_bind_key($link, 'linkFirst');
        $data['contact'] = empty($link[1]) ? $link[0] : $link[1];
        $data['contactNo'] = $result['number'];
        $data['billDate'] = $data['date'];
        
        foreach($data['entries'] as $arr=>$row){
            $res = $this->db->query('select * from '.GOODS.' where id ='.$row['invId'])->row_array();
            $area_code = $this->mysql_model->get_row(STORAGE_AREA,"(1=1) and id ='".$row['locationAreaId']."'","area_code");
            $data['list'][] = array(
                'i'=>$arr + 1,
                'goods'=>$res,
                'invSpec'=>$row['invSpec'],
                'qty'=>abs($row['qty']),
                'price'=>$row['price'],
                'discountRate'=>$row['discountRate']>0?$row['discountRate']:'',
                'deduction'=>$row['deduction']>0?$row['deduction']:'',
                'amount'=>$row['amount'],
                'locationName'=>$row['locationName'],
            	'area_code' => $area_code,
            );
        }
        
        ob_start();
        $this->load->view('scm/invSa/toPdf',$data);
        $content = ob_get_clean();
        require_once('./application/libraries/html2pdf/html2pdf.php');
        try {
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->setDefaultFont('javiergb');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->writeHTML($content, '');
            $html2pdf->Output('invSa_'.date('ymdHis').'.pdf');
        }catch(HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
    }

    //打印手工订单
    public function toPdfSales() {
        $this->common_model->checkpurview(88);
        $id   = intval($this->input->get('id',TRUE));
        $data = $this->data_model->get_sales('and (a.id='.$id.') and billType="SALE"',1);
        if (count($data)>0) {
            $data['num']    = 8;
            $data['system'] = $this->common_model->get_option('system');
            $list = $this->data_model->get_sales_info('and (iid='.$id.') order by id');
            $data['countpage']  = ceil(count($list)/$data['num']);   //共多少页
            foreach($list as $arr=>$row) {
                $data['list'][] = array(
                    'i'=>$arr + 1,
                    'goods'=>$row['invNumber'].' '.$row['invName'],
                    'invSpec'=>$row['invSpec'],
                    'qty'=>abs($row['qty']),
                    'price'=>$row['price'],
                    'discountRate'=>$row['discountRate']>0?$row['discountRate']:'',
                    'deduction'=>$row['deduction']>0?$row['deduction']:'',
                    'amount'=>$row['amount'],
                    'locationName'=>$row['locationName']
                );
            }
            ob_start();
            $this->load->view('scm/invSa/toPdfSales',$data);
            $content = ob_get_clean();
            require_once('./application/libraries/html2pdf/html2pdf.php');
            try {
                $html2pdf = new HTML2PDF('P', 'A4', 'en');
                $html2pdf->setDefaultFont('javiergb');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->writeHTML($content, '');
                $html2pdf->Output('invSa_'.date('ymdHis').'.pdf');
            }catch(HTML2PDF_exception $e) {
                echo $e;
                exit;
            }
        } else {
            str_alert(-1,'单据不存在、或者已删除');
        }
    }
	
	
	
	//删除 
    public function delete() {
	    $this->common_model->checkpurview(9);
	    $id   = intval($this->input->get('id',TRUE));
		$data = $this->mysql_model->get_row(INVOICE,'(id='.$id.') and billType="SALE"');  
		if (count($data)>0) {
		    $data['checked'] >0 && str_alert(-1,'已审核的不可删除'); 
		    $info['isDelete'] = 1;
		    $this->db->trans_begin();
			$this->mysql_model->update(INVOICE,$info,'(id='.$id.')');   
			$this->mysql_model->update(INVOICE_INFO,$info,'(iid='.$id.')');  
			$this->mysql_model->update(ACCOUNT_INFO,$info,'(iid='.$id.')');    
			if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				str_alert(-1,'删除失败'); 
			} else {
			    $this->db->trans_commit();
				$this->common_model->logs('删除采购订单 单据编号：'.$data['billNo']);
				str_alert(200,'success'); 	 
			}
		}
		str_alert(-1,'单据不存在、或者已删除');  
	}

	//删除手工订单
    public function deleteSales() {
        $this->common_model->checkpurview(9);
        $id   = intval($this->input->get('id',TRUE));
        $data = $this->mysql_model->get_row(SALES,'(id='.$id.') and billType="SALE"');
        if (count($data)>0) {
            $data['checked'] >0 && str_alert(-1,'已审核的不可删除');
            $info['isDelete'] = 1;
            $this->db->trans_begin();
            $this->mysql_model->update(SALES,$info,'(id='.$id.')');
            $this->mysql_model->update(SALES_INFO,$info,'(iid='.$id.')');
            $this->mysql_model->update(SALES_ACCOUNTINFO,$info,'(iid='.$id.')');
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1,'删除失败');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('删除手工订单编号：'.$data['billNo']);
                str_alert(200,'success');
            }
        }
        str_alert(-1,'单据不存在、或者已删除');
    }
	
	//库存查询全部
	public function justIntimeInv() {
	    $v = array();
		$qty = 0;
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$page  = max(intval($this->input->get_post('page',TRUE)),1);
		$rows  = max(intval($this->input->get_post('rows',TRUE)),100);
		$invid = $this->input->get_post('invId',TRUE);
		
		$where = $invid > 0 ? ' and a.invId='.$invid.'' : '';
		$where .=      	      ' and a.sid = '.$this->jxcsys['sid'];
		$data['data']['total']     = 1;                         
		$data['data']['records']   = $this->data_model->get_inventory($where.' GROUP BY locationId',3);    
		$list = $this->data_model->get_inventory($where.' GROUP BY locationId');    
		foreach ($list as $arr=>$row) {
		    $i = $arr + 1;
			$v[$arr]['locationId']   = intval($row['locationId']);
			$qty += $v[$arr]['qty']  = (float)$row['qty'];
			$v[$arr]['locationName'] = $row['locationName'];
			$v[$arr]['invId']        = $row['invId'];
		}
		$v[$i]['locationId']   = 0;
		$v[$i]['qty']          = $qty;
		$v[$i]['locationName'] = '合计';
		$v[$i]['invId']        = 0;
		$data['data']['rows']  = $v;
		die(json_encode($data));
	}
	
	
	
	//库存查询
	public function justIntimeInvAll() {
		$v = array();
		$qty = 0;
		$data['status'] = 200;
		$data['msg']    = 'success';
		$page  = max(intval($this->input->get_post('page',TRUE)),1);
		$rows  = max(intval($this->input->get_post('rows',TRUE)),100);
		$locationId = $this->input->get_post('locationId',TRUE);
		$invid = $this->input->get_post('invId',TRUE);
		
		$where = 				     ' and a.sid = '.$this->jxcsys['sid'];
		$where .=  $invid > 0 ?      ' and a.invId='.$invid.'' : '';
		$where .=  $locationId > 0 ? ' and a.locationId = '.$locationId : '' ;
		
		$data['data']['total']     = 1;
		$data['data']['records']   = $this->data_model->get_inventoryAll($where.' GROUP BY b.id',3);
		$list = $this->data_model->get_inventoryAll($where.' GROUP BY  b.id');
		foreach ($list as $arr=>$row) {
			$v[$arr]['id'] 	 		 = $row['id'];
			$v[$arr]['number']       = $row['number'];
			$v[$arr]['name']         = $row['name'];
			$v[$arr]['unitId']       = $row['unitId'];
			$v[$arr]['unitName']     = $row['unitName'];
			$v[$arr]['spec']         = $row['spec'];
			$v[$arr]['skuId']         = $row['skuId'];
			$v[$arr]['locationId']   = $row['locationId'];
			$v[$arr]['locationName'] = $row['locationName'];
			$qty += $v[$arr]['qty']  = (float)$row['qty'];
		}
		$data['data']['rows']  = $v;
		die(json_encode($data));
	}
	
	
	
	
	
	
	
	public function findNearSaEmp() {
		die('{"status":200,"msg":"success","data":{"empId":0}}');
		
	}

	
	public function get_settle_price(){
		$result = $this->get_sale_price();
		str_alert(200,'success',$result);
	}
	
	private function get_sale_price(){
		$where = ' and billType="SALE"';
		$result = $this->data_model->get_settle_price3($where);
		return $result;
	}
	
	//公共验证
	private function validform($data,$type) {
	    if (isset($data['id']) && intval($data['id'])>0 && empty($type)) {
		    $invoice = $this->mysql_model->get_row(INVOICE,'(id='.$data['id'].') and billType="SALE" and isDelete=0');  //修改的时候判断
			count($invoice)<1 && str_alert(-1,'单据不存在、或者已删除');
			$invoice['checked']>0 && str_alert(-1,'审核后不可修改');
			$data['billNo']      =  $invoice['billNo'];	
			$data['update']     = true;
		} else {
		    //$data['billNo']      = str_no('XS');    //修改的时候屏蔽
		    $data['update']     = false;
		}
		
		
		(float)$data['arrears'] < 0 	|| !is_numeric($data['arrears'])  && str_alert(-1,'本次欠款要为数字，请输入有效数字！');
		(float)$data['disRate'] < 0 	|| !is_numeric($data['arrears'])  && str_alert(-1,'折扣率要为数字，请输入有效数字！');
		(float)$data['rpAmount'] < 0 	|| !is_numeric($data['arrears'])  && str_alert(-1,'本次收款要为数字，请输入有效数字！');
		(float)$data['customerFree']< 0 || !is_numeric($data['arrears'])  && str_alert(-1,'客户承担费用要为数字，请输入有效数字！');
		
	
	    $data['buId']            = intval($data['buId']);
		$data['salesId']         = intval($data['salesId']);
		$data['billType']        = 'SALE';
		$data['billDate']        = $data['date'];
		$data['transType']       = intval($data['transType']);
		$data['transTypeName']   = $data['transType']==150601 ? '销售' : '销退';
		$data['description']     = $data['description'];
		$data['totalQty']        = (float)$data['totalQty'];
		$data['totalTax']        = isset($data['totalTax']) ? (float)$data['totalTax'] : 0;
		$data['totalTaxAmount']  = isset($data['totalTaxAmount']) ? (float)$data['totalTaxAmount'] : 0; 
			 
		if ($data['transType']==150601) {
			$data['amount']      = abs($data['amount']);
			$data['arrears']     = abs($data['arrears']);
			$data['rpAmount']    = abs($data['rpAmount']);
			$data['totalAmount'] = abs($data['totalAmount']);
		} else {
			$data['amount']      = -abs($data['amount']);
			$data['arrears']     = -abs($data['arrears']);
			$data['rpAmount']    = -abs($data['rpAmount']);
			$data['totalAmount'] = -abs($data['totalAmount']);
		} 
			 
			 
		$data['disRate']        = (float)$data['disRate'];
		$data['disAmount']      = (float)$data['disAmount'];
		$data['hxStateCode']    = $data['rpAmount']==$data['amount'] ? 2 : ($data['rpAmount']>0 ? 1 : 0); 
		$data['totalArrears']   = (float)$data['totalArrears'];
		$data['totalDiscount']  = (float)$data['totalDiscount'];
		$data['customerFree']   = (float)$data['customerFree'];
		$data['accId']          = (float)$data['accId'];
		$data['uid']            = $this->jxcsys['uid'];
		$data['sid']            = $this->jxcsys['sid'];
		$data['userName']       = $this->jxcsys['name'];  
		$data['modifyTime']     = date('Y-m-d H:i:s');
	
	
		
		
		
		//str_alert(-1,$data['rpAmount']);
		//(float)$data['amount'] < (float)$data['rpAmount']  && str_alert(-1,'折扣率要为[0-100]之间数字，请输入有效数字！'); 
		//(float)$data['amount'] < (float)$data['disAmount'] && str_alert(-1,'折扣额不能大于合计金额！'); 
 
			
		//选择了结算账户 需要验证 
		if (isset($data['accounts']) && count($data['accounts'])>0) {
			foreach ($data['accounts'] as $arr=>$row) {
				(float)$row['payment'] < 0 && str_alert(-1,'结算金额要为数字，请输入有效数字！');
			}  
        }
        
		//商品录入验证
		if (is_array($data['entries'])) {
		    $system    = $this->common_model->get_option('system'); 
		    if ($system['requiredCheckStore']==1) {  //开启检查时判断
				$item = array();                     
				foreach($data['entries'] as $k=>$v){
				    !isset($v['invId']) && str_alert(-1,'参数错误');    
					!isset($v['locationId']) && str_alert(-1,'参数错误'); 
					if(!isset($item[$v['invId'].'-'.$v['locationId']])){    
						$item[$v['invId'].'-'.$v['locationId']] = $v;
					}else{
						$item[$v['invId'].'-'.$v['locationId']]['qty'] += $v['qty'];        //同一仓库 同一商品 数量累加
					}
				}
				//$inventory = $this->data_model->get_invoice_info_inventory();
				$inventory = $this->data_model->get_area_info_inventory();
				
			} else {
			    $item = $data['entries'];	
			}
			//获取门店ID
			$storage   = array_column($this->mysql_model->get_results(STORAGE,'(disable=0) and sid='.$this->jxcsys['sid']),'id');  
			
			foreach ($item as $arr=>$row) {
			    !isset($row['invId']) && str_alert(-1,'参数错误');    
				!isset($row['locationId']) && str_alert(-1,'参数错误'); 
				(float)$row['qty'] < 0 || !is_numeric($row['qty']) && str_alert(-1,'商品数量要为数字，请输入有效数字！'); 
				(float)$row['price'] < 0 || !is_numeric($row['price']) && str_alert(-1,'商品销售单价要为数字，请输入有效数字！'); 
				//(float)$row['discountRate'] < 0 || !is_numeric($row['discountRate']) && str_alert(-1,'折扣率要为数字，请输入有效数字！');
				intval($row['locationId']) < 1 && str_alert(-1,'请选择相应的仓库！'); 
				!in_array(intval($row['locationId']),$storage) && str_alert(-1,$row['locationName'].'不存在或不可用！');
				
				//库存判断
				if ($system['requiredCheckStore']==1) {  
				    if (intval($data['transType'])==150601) {                        //销售才验证 
						if (isset($inventory[$row['invId']][$row['locationId']][$row['locationAreaId']])) {
							$isEx = $this->mysql_model->get_row(INVOICE_INFO,'(isDelete=0) and billNo="'.$data['billNo'].'" and  invId='.$row['invId'],'qty');
							if(empty($isEx)){
								$inventory[$row['invId']][$row['locationId']][$row['locationAreaId']] < (float)$row['qty'] && str_alert(-1,$row['locationName']." ".$row['invNumber']." ".$row['invName'].'商品库存不足！');
							}else{
								if(($inventory[$row['invId']][$row['locationId']][$row['locationAreaId']] + abs($isEx)) < (float)$row['qty']){
									str_alert(-1,$row['locationName']." ".$row['invNumber']." ".$row['invName'].'商品库存不足！');
								}
							}
						} else {
							str_alert(-1,$row['locationName']." ".$row['invNumber']." ".$row['invName'].'商品库存不足.！');
						}
					}
				}
			}
			
		} else {	 
			str_alert(-1,'提交的是空数据'); 
		} 
		//供应商验证
		$this->mysql_model->get_count(CONTACT,'(id='.intval($data['buId']).')')<1 && str_alert(-1,'客户不存在'); 
		return $data;
		
	}

    private function validforms($data) {
        if (isset($data['id']) && intval($data['id'])>0) {
            $invoice = $this->mysql_model->get_row(SALES,'(id='.$data['id'].') and billType="SALE" and isDelete=0');  //修改的时候判断
            count($invoice)<1 && str_alert(-1,'单据不存在、或者已删除');
            $invoice['checked']>0 && str_alert(-1,'审核后不可修改');
            $data['billNo']      =  $invoice['billNo'];
        } else {
            $data['billNo']      = str_no('XS');    //修改的时候屏蔽
        }

        (float)$data['arrears'] < 0 	|| !is_numeric($data['arrears'])  && str_alert(-1,'本次欠款要为数字，请输入有效数字！');
        (float)$data['disRate'] < 0 	|| !is_numeric($data['arrears'])  && str_alert(-1,'折扣率要为数字，请输入有效数字！');
        (float)$data['rpAmount'] < 0 	|| !is_numeric($data['arrears'])  && str_alert(-1,'本次收款要为数字，请输入有效数字！');
        (float)$data['customerFree']< 0 || !is_numeric($data['arrears'])  && str_alert(-1,'客户承担费用要为数字，请输入有效数字！');


        $data['buId']            = intval($data['buId']);
        $data['salesId']         = intval($data['salesId']);
        $data['billType']        = 'SALE';
        $data['billDate']        = $data['date'];
        $data['transType']       = intval($data['transType']);
        $data['transTypeName']   = $data['transType']==150601 ? '销售' : '销退';
        $data['description']     = $data['description'];
        $data['totalQty']        = (float)$data['totalQty'];
        $data['totalTax']        = isset($data['totalTax']) ? (float)$data['totalTax'] : 0;
        $data['totalTaxAmount']  = isset($data['totalTaxAmount']) ? (float)$data['totalTaxAmount'] : 0;

        if ($data['transType']==150601) {
            $data['amount']      = abs($data['amount']);
            $data['arrears']     = abs($data['arrears']);
            $data['rpAmount']    = abs($data['rpAmount']);
            $data['totalAmount'] = abs($data['totalAmount']);
        } else {
            $data['amount']      = -abs($data['amount']);
            $data['arrears']     = -abs($data['arrears']);
            $data['rpAmount']    = -abs($data['rpAmount']);
            $data['totalAmount'] = -abs($data['totalAmount']);
        }


        $data['disRate']        = (float)$data['disRate'];
        $data['disAmount']      = (float)$data['disAmount'];
        $data['hxStateCode']    = $data['rpAmount']==$data['amount'] ? 2 : ($data['rpAmount']>0 ? 1 : 0);
        $data['totalArrears']   = (float)$data['totalArrears'];
        $data['totalDiscount']  = (float)$data['totalDiscount'];
        $data['customerFree']   = (float)$data['customerFree'];
        $data['accId']          = (float)$data['accId'];
        $data['uid']            = $this->jxcsys['uid'];
        $data['sid']            = $this->jxcsys['sid'];
        $data['userName']       = $this->jxcsys['name'];
        $data['modifyTime']     = date('Y-m-d H:i:s');





        //str_alert(-1,$data['rpAmount']);
        //(float)$data['amount'] < (float)$data['rpAmount']  && str_alert(-1,'折扣率要为[0-100]之间数字，请输入有效数字！');
        //(float)$data['amount'] < (float)$data['disAmount'] && str_alert(-1,'折扣额不能大于合计金额！');


        //选择了结算账户 需要验证
        if (isset($data['accounts']) && count($data['accounts'])>0) {
            foreach ($data['accounts'] as $arr=>$row) {
                (float)$row['payment'] < 0 && str_alert(-1,'结算金额要为数字，请输入有效数字！');
            }
        }

        //商品录入验证
        if (is_array($data['entries'])) {
            $system    = $this->common_model->get_option('system');

            if ($system['requiredCheckStore']==1) {  //开启检查时判断
                $item = array();
                foreach($data['entries'] as $k=>$v){
                    !isset($v['invId']) && str_alert(-1,'参数错误');
                    !isset($v['locationId']) && str_alert(-1,'参数错误');
                    if(!isset($item[$v['invId'].'-'.$v['locationId']])){
                        $item[$v['invId'].'-'.$v['locationId']] = $v;
                    }else{
                        $item[$v['invId'].'-'.$v['locationId']]['qty'] += $v['qty'];        //同一仓库 同一商品 数量累加
                    }
                }
                $inventory = $this->data_model->get_sales_info_inventory();
            } else {
                $item = $data['entries'];
            }

            //获取门店ID
            $storage   = array_column($this->mysql_model->get_results(STORAGE,'(disable=0) and sid='.$this->jxcsys['sid']),'id');

            foreach ($item as $arr=>$row) {
                !isset($row['invId']) && str_alert(-1,'参数错误');
                !isset($row['locationId']) && str_alert(-1,'参数错误');
                (float)$row['qty'] < 0 || !is_numeric($row['qty']) && str_alert(-1,'商品数量要为数字，请输入有效数字！');
                (float)$row['price'] < 0 || !is_numeric($row['price']) && str_alert(-1,'商品销售单价要为数字，请输入有效数字！');
                //(float)$row['discountRate'] < 0 || !is_numeric($row['discountRate']) && str_alert(-1,'折扣率要为数字，请输入有效数字！');
                intval($row['locationId']) < 1 && str_alert(-1,'请选择相应的仓库！');
                !in_array(intval($row['locationId']),$storage) && str_alert(-1,$row['locationName'].'不存在或不可用！');
            }
        } else {
            str_alert(-1,'提交的是空数据');
        }

        //供应商验证
        $this->mysql_model->get_count(CONTACT,'(id='.intval($data['buId']).')')<1 && str_alert(-1,'客户不存在');

        return $data;

    }



    //组装数据
	private function invoice_info($iid,$data) {
	    if (is_array($data['entries'])) {
	    	
			foreach ($data['entries'] as $arr=>$row) {
				
			    if ($row['invId']>0) {
			    	
					$v[$arr]['iid']           = $iid;
					$v[$arr]['sid']           = $data['sid'];
					$v[$arr]['billNo']        = $data['billNo'];
					$v[$arr]['billDate']      = $data['billDate']; 
					$v[$arr]['buId']          = $data['buId'];
					$v[$arr]['transType']     = $data['transType'];
					$v[$arr]['transTypeName'] = $data['transTypeName'];
					$v[$arr]['billType']      = $data['billType'];
					$v[$arr]['salesId']       = $data['salesId'];
					$v[$arr]['invId']         = $row['invId'];
					$v[$arr]['skuId']         = $row['skuId'];
					$v[$arr]['unitId']        = intval($row['unitId']);
					$v[$arr]['locationId']    = intval($row['locationId']);
					$v[$arr]['areaId']    	  = intval($row['locationAreaId']);
					if ($data['transType']==150601) {
						$v[$arr]['qty']       = -abs($row['qty']); 
						$v[$arr]['amount']    = abs($row['amount']); 
					} else {
						$v[$arr]['qty']       = abs($row['qty']);  
						$v[$arr]['amount']    = -abs($row['amount']); 
					} 
					$v[$arr]['price']         = abs($row['price']);
					$v[$arr]['lprice']        = abs($row['lprice']);
					$v[$arr]['oprice']        = abs($row['oprice']);
					$v[$arr]['discountRate']  = $row['discountRate'];  
					$v[$arr]['deduction']     = $row['deduction'];  
					$v[$arr]['description']   = $row['description'];   
					//$v[$arr]['srcOrderEntryId']    = intval($row['srcOrderEntryId']);
	//				$v[$arr]['srcOrderNo']         = $row['srcOrderNo'];
	//				$v[$arr]['srcOrderId']         = intval($row['srcOrderId']); 
				} 
			} 
			
			
			if (isset($v)) {
			    if (isset($data['id']) && $data['id']>0) {                    //修改的时候   
				    $this->mysql_model->delete(INVOICE_INFO,'(iid='.$iid.')');
				}
				$this->mysql_model->insert(INVOICE_INFO,$v);
			}
		} 
	}
	
	//组装数据
	private function account_info($iid,$data) {
	    if (isset($data['accounts']) && count($data['accounts'])>0) {
			foreach ($data['accounts'] as $arr=>$row) {
			    if (intval($row['accId'])>0) {
					$v[$arr]['iid']           = intval($iid);
					$v[$arr]['sid']        	  = $data['sid'];
					$v[$arr]['billNo']        = $data['billNo'];
					$v[$arr]['buId']          = $data['buId'];
					$v[$arr]['billType']      = $data['billType'];
					$v[$arr]['transType']     = $data['transType'];
					$v[$arr]['transTypeName'] = $data['transType']==150601 ? '普通销售' : '销售退回';
					$v[$arr]['billDate']      = $data['billDate']; 
					$v[$arr]['accId']         = $row['accId']; 
					$v[$arr]['payment']       = $data['transType']==150601 ? abs($row['payment']) : -abs($row['payment']); 
					$v[$arr]['wayId']         = $row['wayId'];
					$v[$arr]['settlement']    = $row['settlement'] ;
				} 
			} 
			if (isset($v)) {
			    if (isset($data['id']) && $data['id']>0) {                      //修改的时候
				    $this->mysql_model->delete(ACCOUNT_INFO,'(iid='.$iid.')');
				}
			    $this->mysql_model->insert(ACCOUNT_INFO,$v);
			}
		} 
	}
	
	/**
	 * 更新库存
	 * @param unknown $data
	 */
	private function storage_info($data){
		$cur_time = time();
		if (!is_array($data['entries'])) return ;
		foreach ($data['entries'] as $arr=>$row) {
			$s[$arr]['num']           = abs($row['qty']);
			$s[$arr]['itemId']        = $row['invId'];
			$s[$arr]['skuId']      	  = $row['skuId'];
			$s[$arr]['areaId']    	  = intval($row['locationareaId']);
			$s[$arr]['storageId']     = $row['locationId'];
			$s[$arr]['modified_time'] = $cur_time;
		}
	
		foreach ($s as $arr=>$row) {
				
			$where  = '(1=1)';
			$where .= ' and (itemId    = "'.$row['itemId']   .'")';
			$where .= ' and (skuId     = "'.$row['skuId']    .'")';
			$where .= ' and (storageId = "'.$row['storageId'].'")';
			$rs = $this->mysql_model->get_row(GOOODS_STORAGE.$this->jxcsys['sid'],$where);
			if(empty($rs)){
				try {
					$iid = $this->mysql_model->insert(GOOODS_STORAGE.$this->jxcsys['sid'],$row);
				} catch (Exception $e) {
					if ($data['transType']==150601) {
						$row['num']  = '-'.$row['num'];
					} else {
						$row['num']  = '+'.$row['num'];
					}
					$this->mysql_model->update(GOOODS_STORAGE.$this->jxcsys['sid'],' num = num'.$row['num'],$where);
				}
			}else{
				if ($data['transType']==150601) {
					$row['num']  = '-'.$row['num'];
				} else {
					$row['num']  = '+'.$row['num'];
				}
				$this->mysql_model->update(GOOODS_STORAGE.$this->jxcsys['sid'],' num = num'.$row['num'],$where);
	
			}
				
		}
	}
	

    //组装数据
    private function invoices_info($iid,$data) {
        if (is_array($data['entries'])) {
            foreach ($data['entries'] as $arr=>$row) {
                foreach($row as $key=>$vo){
                if ($vo['invId']>0) {
                    $v[$arr]['iid']           = $iid;
                    $v[$arr]['sid']           = $data['sid'];
                    $v[$arr]['billNo']        = $data['billNo'];
                    $v[$arr]['billDate']      = $data['billDate']; 
                    $v[$arr]['buId']          = $data['buId'];
                    $v[$arr]['transType']     = $data['transType'];
                    $v[$arr]['transTypeName'] = $data['transTypeName'];
                    $v[$arr]['billType']      = $data['billType'];
                    $v[$arr]['salesId']       = $data['salesId'];
                    $v[$arr]['invId']         = $vo['invId'];
                    $v[$arr]['skuId']         = intval($vo['skuId']);
                    $v[$arr]['unitId']        = intval($vo['unitId']);
                    $v[$arr]['locationId']    = intval($vo['locationId']);
                    $v[$arr]['areaId']        = intval($vo['locationAreaId']);
                    if ($data['transType']==150601) {
                        $v[$arr]['qty']       = -abs($vo['qty']); 
                        $v[$arr]['amount']    = abs($vo['amount']); 
                    } else {
                        $v[$arr]['qty']       = abs($vo['qty']);  
                        $v[$arr]['amount']    = -abs($vo['amount']); 
                    } 
                    $v[$arr]['price']         = abs($vo['price']);  
                    $v[$arr]['discountRate']  = $vo['discountRate'];  
                    $v[$arr]['deduction']     = $vo['deduction'];  
                    $v[$arr]['description']   = $vo['description'];   
                    //$v[$arr]['srcOrderEntryId']    = intval($row['srcOrderEntryId']);
    //              $v[$arr]['srcOrderNo']         = $row['srcOrderNo'];
    //              $v[$arr]['srcOrderId']         = intval($row['srcOrderId']); 
                } 
            } 
        }
            if (isset($v)) {
                if (isset($data['id']) && $data['id']>0) {                    //修改的时候   
                    $this->mysql_model->delete(INVOICE_INFO,'(iid='.$iid.')');
                }
                $this->mysql_model->insert(INVOICE_INFO,$v);
            }
        } 
    }
    
    
    //组装数据
    private function sales_accountInfo($iid,$data) {
        if (isset($data['accounts']) && count($data['accounts'])>0) {
            foreach ($data['accounts'] as $arr=>$row) {
                if (intval($row['accId'])>0) {
                    $v[$arr]['iid']           = intval($iid);
                    $v[$arr]['sid']        	  = $data['sid'];
                    $v[$arr]['billNo']        = $data['billNo'];
                    $v[$arr]['buId']          = $data['buId'];
                    $v[$arr]['billType']      = $data['billType'];
                    $v[$arr]['transType']     = $data['transType'];
                    $v[$arr]['transTypeName'] = $data['transType']==150601 ? '普通销售' : '销售退回';
                    $v[$arr]['billDate']      = $data['billDate'];
                    $v[$arr]['accId']         = $row['accId'];
                    $v[$arr]['payment']       = $data['transType']==150601 ? abs($row['payment']) : -abs($row['payment']);
                    $v[$arr]['wayId']         = $row['wayId'];
                    $v[$arr]['settlement']    = $row['settlement'] ;
                }
            }
            if (isset($v)) {
                if (isset($data['id']) && $data['id']>0) {                      //修改的时候
                    $this->mysql_model->delete(SALES_ACCOUNTINFO,'(iid='.$iid.')');
                }
                $this->mysql_model->insert(SALES_ACCOUNTINFO,$v);
            }
        }
    }
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */