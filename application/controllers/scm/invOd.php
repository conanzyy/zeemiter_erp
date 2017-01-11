<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class InvOd extends CI_Controller {

	private $saleModel;
    
	public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
		$this->jxcsys  = $this->session->userdata('jxcsys');
		$this->saleModel = json_decode(SALEMODEL,true);
		$this->load->library('excel/excel');
		$this->load->helper('download');
    }
	
	public function index() {
		
	    $action = $this->input->get('action',TRUE);

		switch ($action) {
			case 'uploadexcelfile':
				$this->uploadexcelfile();
				break;
			case 'getExecel':
				$this->getExecel();
				break;
			case 'download':
				$this->download();
				break;
			case 'downError':
				$this->downError();
				break;
			case 'initPur':
			    $this->common_model->checkpurview(181);
			    $this->load->view('scm/invOd/initPur');	
				break;  
			case 'editPur':
			    $this->common_model->checkpurview(182);
			    $this->load->view('scm/invOd/initPur');	
				break;  	
			case 'initPurList':
			    $this->common_model->checkpurview(180); 
			    $this->load->view('scm/invOd/initPurList');
				break;
			case 'orderList' :
				$this->orderList();
				break;  	
			case 'returnGoodsList': //退货订单详情页
			    $this->common_model->checkpurview(182); 
			    $this->load->view('scm/invOd/returnGoodsList');
				break; 
			case 'returnGoods' : // 退货选择页面
				$this->returnGoods();
				break;
			case 'returnGoodsData' : // 退货单页面数据
				$this->returnGoodsData();
				break;
			case 'returnGoodsOrder' : // 退货单详情
				$this->returnGoodsOrder();
				break;
			case 'orderDetailOut' : // 出库明细
				$this->load->view('scm/invOd/orderDetailOut');
				break; 
			default: 
			    $this->common_model->checkpurview(180); 
			    $this->purList();	
		}
	}
	
	
	
	/**
	 * 出库明细
	 */
	public function orderDetailOut(){
		/* 订单发货 调NC */
		//接口参数
		$skey = $this->input->get_post('skey',TRUE);
		$page = max($this->input->get_post('page',TRUE),1);
		$rows = max($this->input->get_post('rows',TRUE),20);
		$beginDate=$this->input->get_post('beginDate',TRUE);
		$endDate=$this->input->get_post('endDate',TRUE);
		$postData = array(
				"StationCode"=>$this->jxcsys['sid'],
				"PageNo"=>$page,
				"PageSize"=>$rows,
				"PoCode" => $skey,
				"PoDate"=> $beginDate."|".$endDate,
		);						
		$post_data['data'] = json_encode($postData);
		//echo '<pre>';print_r(json_encode($post_data['data']));die;
		$post_data['method'] = "dispatchList";//接口方法
		$result = http_client($post_data,'NC');
		$returns = $result->return;
		if(empty($returns)) str_alert(-1,'调用NC接口失败');
		$return =  json_decode($returns,true);
		if($return['IsSuccess'] == 'false'){
			str_alert(-1,"NC返回".$return['ErrMsg']);
		}
		//print_r($return);
		//die;
		
		$v=array();
		$rows = max(intval($this->input->get_post('rows',TRUE)),20);
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$data['status'] = 200;
		$data['msg']    = 'success';
		$data['data']['records'] = $return['RecCnt'];//总条数
		$data['data']['page'] = $page;
		$data['data']['total']     = ceil($data['data']['records']/$rows);                  //总分页数
		
		$type = array(
			"1001"=>'快准车配',
			"P0001"=>'PDM',
		);
		foreach($return['List'] as $arr=>$row){
			$v[$arr] = $row;
			$v[$arr]['SaleOrgCode'] = $type[$row['SaleOrgCode']];
		}
		$data['data']['rows']=$v;
		die(json_encode($data));
		
		
		//success($data);
	}
	
	
	//excel
	public function uploadexcelfile() {
		$data = $this->upload_file("fileupload");
		echo json_encode($data);
	}
	
	public function download(){
		$info = read_file('./data/download/order.xls');
		$this->common_model->logs('采购订单导入模板:order.xls');
		force_download('采购订单导入模板.xls', $info);
	}
	
	public function initPur() {
	
		$this->common_model->checkpurview(2);
			
		$this->load->view('scm/invOd/initPur');
	}
	
	
	
	function parseExcel($filename){
	
		$filePath['file_url'] = './data/upfile/excels/'.$filename;
		if($rfile['error']) die($rfile['error']);
		$this->excel->setOutputEncoding('utf-8');
		$this->excel->read($filePath['file_url']);
		$list = $this->excel->sheets[0]['cells'];
	
		$data = array();$header = $list[1];$header[] = '错误原因';
		//获取转成数据
		foreach ($list as $arr=>$row) {
			if($arr == 1) continue;
			$i = 1;
			foreach ($row as $key => $value){
				$data[$arr][chr(64 + $i)] = @$row[$i];
				$i++;
			}
		}
	
		$return['data'] = $data;
		$return['header'] = $header;
		return $return;
		
	}
	
	function importError($error,$header){
	
		if(empty($error)) return;
		$this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		$objPHPExcel = new PHPExcel();
		$iofactory = new IOFactory();
		//设置excel列名
	
		foreach ($header as $key=>$value){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(64 + $key) . 1,$value);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(chr(64 + $key))->setWidth(20);
		}
	
		//把数据循环写入excel中
		foreach($error as $key => $value){
			if(empty($value)) continue;
			$key += 2;$i = 0;
			foreach ($value as $k => $v){
				$i += 1;
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(64 + $i).$key,$v);
			}
		}
		//excel保存在根目录下  如要导出文件，以下改为注释代码
		$objPHPExcel->getActiveSheet() -> setTitle('ERROR_INFO');
		$objPHPExcel-> setActiveSheetIndex(0);
		$objWriter = $iofactory -> createWriter($objPHPExcel, 'Excel5');
	
		$dir = './data/download/import/error/'.$this->jxcsys['sid'];
		$filename = 'import_error_'.$this->jxcsys['sid'].time().'.xls';
		
		if (!file_exists($dir)){ mkdir ($dir,0775); }
		$objWriter -> save($dir.'/'.$filename);
		return $filename;
	}
	
	
	
	public function getExecel() {
		
		$excelname =  $this->input->get('filename');
		$execl = $this->parseExcel($excelname);
		$datalist = $execl['data'];
		$header = $execl['header'];
		
		if(count($datalist) > 1000){
			$data['status'] = 250;
			$data['msg']    = '数据量过大，建议分批次导入！每次导入请小于1000行';
			die(json_encode($data));
		}
		//查询出商品集合
		$goods = $this->data_model->get_goods();
		
		//由于SKUID可能重复将所有SKUID遍历出放来放到一个数据 。
		$ex_goods = array_column($goods, "skuId");
		$goodsInfo = array_bind_key($goods, "skuId");
		
		//检验导入EXEC表，生成EXEC输出。
		$error = array();$success = array();
		foreach ($datalist as $key=>$value){
			if(empty($value['A']) || empty($value['B'])){
				$value['C'] = "物料编码、 采购数量不能为空";
				$error[] = $value;
				continue;
			}
			if(!in_array($value['A'], $ex_goods)){
				$value['C'] = "物料编码".$value['A']."在店管家中不存在";
				$error[] = $value;
				continue;
			}
			
			if(empty($goodsInfo[$value['A']]['saleModel'])){
				$value['C'] = "物料编码".$value['A']."销售组织为空，请检查NC系统中是否有此物料";
				$error[] = $value;
				continue;
			}
			
			if($goodsInfo[$value['A']]['skuStatus'] == "3"){
				$value['C'] = "物料编码".$value['A']."已停供！";
				$error[] = $value;
				continue;
			}
			
			if(empty($goodsInfo[$value['A']]['skuStatus']) || $goodsInfo[$value['A']]['skuStatus'] == "4"){
				$value['C'] = "物料编码".$value['A']."已停用！";
				$error[] = $value;
				continue;
			}
			
			if($goodsInfo[$value['A']]['skuStatus'] == "5"){
				$value['C'] = "物料编码".$value['A']."为新品！暂时不能采购！";
				$error[] = $value;
				continue;
			}
			
			if(!intval($value['B']) > 0){
				$value['C'] = "数量必须大于0";
				$error[] = $value;
				continue;
			}
				
			$success[] = $value;
		}	
		//合并物料及数量
		foreach ($success as $key => $value){
			$flag = true;
			foreach ($list as $i => $row){
				if($row['A'] == $value['A']){
					$flag = false;
					$list[$i]['B'] += $value['B'];
				}
			}
			if($flag){
				$list[] = $value;
			}
		}
		$success = $list;
		
		//生成错误的EXECL
		if(count($error)){
			$filter_name = $this->importError($error,$header);
		}
		foreach ($list as $arr=>$value) {
			$row = $goodsInfo[$value['A']];	
			if(!empty($row['purPrice3'])){
				$row['purPrice'] = $row['purPrice3'];
			}else if(!empty($row['purPrice2'])){
				$row['purPrice'] = $row['purPrice2'];
			}
			
			$v[$arr]['amount']        = (float)$row['iniamount'];
			$v[$arr]['barCode']       = $row['barCode'];
			$v[$arr]['categoryName']  = $row['categoryName'];
			$v[$arr]['currentQty']    = $row['totalqty'];                            //当前库存
			$v[$arr]['delete']        = intval($row['disable'])==1 ? true : false;   //是否禁用
			$v[$arr]['discountRate']  = 0;
			$v[$arr]['retailPrice']   = empty($row['retailPrice']) ? '{}' : $row['retailPrice'];
			$v[$arr]['id']            = $row['id'];
			$v[$arr]['isSerNum']      = intval($row['isSerNum']);
			$v[$arr]['josl']     	  = $row['josl'];
			$v[$arr]['name']          = $row['name'];
			$v[$arr]['number']        = $row['number'];
			$v[$arr]['pinYin']        = $row['pinYin'];
			$v[$arr]['locationId']   = intval($row['locationId']);
			$v[$arr]['locationName'] = $row['locationName'];
			$v[$arr]['locationAreaId'] = intval($row['area_id']);;
			$v[$arr]['locationArea'] = $row['area_name'];
			$v[$arr]['locationNo'] = '';
			$v[$arr]['purPrice']   = $row['purPrice'];
			$v[$arr]['quantity']   = $row['iniqty'];
			$v[$arr]['storageSum'] = $row['storageSum'];
			$v[$arr]['salePrice']  = $row['purPrice'];
			$v[$arr]['skuClassId'] = $row['skuClassId'];
			$v[$arr]['skuId'] 	   = $row['skuId'];
			$v[$arr]['spec']       = $row['spec'];
			$v[$arr]['saleModel']  = $row['saleModel'];
			$v[$arr]['unitCost']   = $row['iniunitCost'];
			$v[$arr]['unitId']     = intval($row['unitId']);
			$v[$arr]['isSelf']     = $row['isSelf'];
			$v[$arr]['unitName']   = $row['unitName'];
			$v[$arr]['productCode']= $row['productCode'];
			$v[$arr]['packSpec']   = $row['packSpec'];
			$v[$arr]['minNum']     = $row['minNum'];
				
			//处理数量
			
			$qty = $value['B'];
			if(!$qty){
				$qty = $row['minNum'];
			}
			#$v[$arr]['qty']        = $nums[$row['skuId']]||$row['minNum'] ||1;
			$v[$arr]['qty']        = $qty;
			$v[$arr]['price']      = $row['purPrice'];
				
			//处理价格，数量x价格
			$v[$arr]['amount']     = $v[$arr]['qty'] * $row['purPrice'];
			$v[$arr]['mainUnit']   = $row['unitName'];
			$v[$arr]['unitId']     = $row['unitId'];
			$v[$arr]['goods']      = $row['number']." ".$row['brand_name']." ".$row['skuId']." ".$row['name']."_".$row['spec'];
			$v[$arr]['goodsId']    = $row['id'];
		}
		
		$qtySum=0;
		$totalAmount=0;
		foreach ($v as $ar => $ro){
			$qtySum 	   = $qtySum + abs($ro['qty']);
			$totalAmount   =  $totalAmount+abs($ro['amount']);
		}
		
		$data['status'] = 200;
		$data['msg']    = 'success';
		$data['data']['totalAmount'] = $totalAmount;
		$data['data']['totalQty'] 	 = $qtySum;
		$data['data']['rows']   	 = $v;
		$data['data']['filename']    	 = $filter_name;
		$data['data']['IAllNumber']    	 = count($datalist);
		$data['data']['ISuccessNumber']  = count($list);
		$data['data']['IErrorNumber']    = count($error);
		
		die(json_encode($data));
			
	}
	
	//商品列表导入
	public function goodsList_excel($ids,$nums){
		
		//$excelfile =
		$info['ids'] = $ids;
		
		$v = array();
		$data['status'] = 200;
		$data['msg']    = 'success';
	
		$where = '';
		$modelwhere = '';
		
		if ($ids) {
			$where .= ' and a.skuId in("'.join('","',$ids).'")';
		}
		
		
		$goods = $this->mysql_model->get_results(GOODS,'(isDelete=0) and saleType=0');
		//由于SKUID可能重复将所有SKUID遍历出放来放到一个数据 。
		$ex_goods = array_column($goods, "skuId");
		
	
		
		//$rows = 20;
		$list = $this->data_model->get_goods($where.' order by a.id',2,$modelwhere);
		
		$i=1;
		foreach ($list as $arr=>$row) {
			
			if(!empty($row['purPrice3'])){
				$row['purPrice'] = $row['purPrice3'];
			}else if(!empty($row['purPrice2'])){
				$row['purPrice'] = $row['purPrice2'];
			}
			//$retailPrice = $this->mysql_model->get_row(RETAILPRICE,'(goodsId='.$row['id'].') and (isDelete=0)','retailPrice');
		    $v[$arr]['amount']        = (float)$row['iniamount'];
			$v[$arr]['barCode']       = $row['barCode'];
			$v[$arr]['categoryName']  = $row['categoryName'];
			$v[$arr]['currentQty']    = $row['totalqty'];                            //当前库存
			$v[$arr]['delete']        = intval($row['disable'])==1 ? true : false;   //是否禁用
			$v[$arr]['discountRate']  = 0;
			$v[$arr]['retailPrice']   = empty($row['retailPrice']) ? '{}' : $row['retailPrice'];
			$v[$arr]['id']            = $row['id'];
			$v[$arr]['isSerNum']      = intval($row['isSerNum']);
			$v[$arr]['josl']     	  = $row['josl'];
			$v[$arr]['name']          = $row['name'];
			$v[$arr]['number']        = $row['number'];
			$v[$arr]['pinYin']        = $row['pinYin'];
			$v[$arr]['locationId']   = intval($row['locationId']);
			$v[$arr]['locationName'] = $row['locationName'];
			$v[$arr]['locationAreaId'] =intval($row['area_id']);;
			$v[$arr]['locationArea'] = $row['area_name'];
			$v[$arr]['locationNo'] = '';
			$v[$arr]['purPrice']   = $row['purPrice'];
			$v[$arr]['quantity']   = $row['iniqty'];
			$v[$arr]['storageSum'] = $row['storageSum'];
			$v[$arr]['salePrice']  = $row['salePrice'];
			$v[$arr]['skuClassId'] = $row['skuClassId'];
			$v[$arr]['skuId'] 	   = $row['skuId'];
			$v[$arr]['spec']       = $row['spec'];
			$v[$arr]['saleModel']  = $row['saleModel'];
			$v[$arr]['unitCost']   = $row['iniunitCost'];
			$v[$arr]['unitId']     = intval($row['unitId']);
			$v[$arr]['isSelf']     = $row['isSelf'];
			$v[$arr]['unitName']   = $row['unitName'];
			$v[$arr]['productCode']= $row['productCode'];
			$v[$arr]['packSpec']   = $row['packSpec'];
			$v[$arr]['minNum']     = $row['minNum'];
			

			//处理数量
			#$v[$arr]['qty']        = $nums[$row['skuId']]||$row['minNum'] ||1;
			$v[$arr]['qty']        = $nums[$row['skuId']];
			$v[$arr]['price']      = $row['purPrice'];
			
			//处理价格，数量x价格
			$v[$arr]['amount']     = $v[$arr]['qty'] * $row['purPrice'];
			$v[$arr]['mainUnit']   = $row['unitName'];
			$v[$arr]['unitId']     = $row['unitId'];
			$v[$arr]['goods']   = $row['number']." ".$row['name']."_".$row['spec'];
			$v[$arr]['goodsId']    = $row['id'];
			$v[$arr]['id']   = $i++;
			
			
		}
		
		$qtySum=0;
		$totalAmount=0;
		foreach ($v as $ar => $ro){
			$qtySum 	   = $qtySum + abs($ro['qty']);
			$totalAmount   =  $totalAmount+abs($ro['amount']);
		}
		
		$ide = array_column($v, 'skuId');
		$error = array_merge(array_diff($ids,$ide));
		$data['data']['totalAmount'] = $totalAmount;
		$data['data']['totalQty'] 	 = $qtySum;
		$data['data']['rows']   	 = $v;
		if(count($ide) > 0 ){
			$data['data']['filename']    	 = $this->create_error($error);
			$data['data']['IAllNumber']    	 = count($ids);
			$data['data']['ISuccessNumber']  = count($ide);
			$data['data']['IErrorNumber']    = count($error);
		}
		
		die(json_encode($data));
		 
	}
	
	
	
	function downError(){
		
		$filename = $this->input->get("filename",true);
		$dir = './data/download/import/error/'.$this->jxcsys['sid'];
		$info = read_file($dir.'/'.$filename);
		$this->common_model->logs('采购订单导出错误列表'.$filename);
		force_download('采购订单导入错误列表_'.time().'.xls', $info);
		
	}
	
	
	
	function create_error($data){
		
		$this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		$objPHPExcel = new PHPExcel();
		$iofactory = new IOFactory();
		//设置excel列名
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','物料编码');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('1','物料编码');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1','错误原因');
		//把数据循环写入excel中
		foreach($data as $key => $value){
			if(empty($value)) continue;
			$key += 2;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$key,$value);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$key,"物料编码不存在");
		}
		//excel保存在根目录下  如要导出文件，以下改为注释代码
		$objPHPExcel->getActiveSheet() -> setTitle('SetExcelName');
		$objPHPExcel-> setActiveSheetIndex(0);
		$objWriter = $iofactory -> createWriter($objPHPExcel, 'Excel5');
		
		$dir = './data/download/import/error/'.$this->jxcsys['sid'];
		
		$filename = 'order_error'.$this->jxcsys['sid'].time().'.xls';
		
		if (!file_exists($dir)){ mkdir ($dir,0775); }
		
		$objWriter -> save($dir.'/'.$filename);
		
		return $filename;
	}
	
	
	
	
	//上传文件
	function upload_file($fieldname){
		
		$config['upload_path']="./data/upfile/excels";
	
		if(!is_dir($config['upload_path'])){
			$old = umask(0);
			mkdir($config['upload_path'],0775,true);
			umask($old);
		}
		$config['allowed_types']= 'xls';
		$config['file_name'] = uniqid();
		$this->load->library('upload', $config);
	
		if (!$this->upload->do_upload($fieldname))
		{
			$error = array('error' =>$this->upload->display_errors());
			return $error;
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
		}
	
		$d = $this->upload->data();
		$rfile['file_name'] = $d['file_name'];
		$file_url  = $config['upload_path']."/".$d['file_name'];
		chmod($file_url, 0775);
		$rfile['file_url'] = $file_url;
	
		return $rfile;
	}
	
	
	
	
	//
	
	public function purList() {
	    $v = array();
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		
		$type   = str_enhtml($this->input->get('typeNumber',TRUE));
		$skey   = str_enhtml($this->input->get('skey',TRUE));
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),20);
		$sidx = str_enhtml($this->input->get_post('sidx',TRUE));
		$sord = str_enhtml($this->input->get_post('sord',TRUE));
		$transType = intval($this->input->get_post('transType',TRUE));
		$bussType = intval($this->input->get_post('bussType',TRUE));
		$beginDate = str_enhtml($this->input->get_post('beginDate',TRUE));
		$endDate   = str_enhtml($this->input->get_post('endDate',TRUE));
		$order = $sidx ? $sidx.' '.$sord :' a.id desc';
		$where = 			   ' and a.billType="PUR"';
		//$where = empty($skey) ? '' : 'and a.billNo="'.$skey.'"';
// 		$where .= $bussType == 'query' ? ' and a.hxStateCode < 2':'';
		$where .= $transType>0  ? ' and a.transType='.$transType : ''; 
		//$where .= $skey  ? ' and (b.name like "%'.$skey.'%" or b.skuId like "%'.$skey.'%" b.number like "%'.$skey.'%" or description like "%'.$skey.'%" or billNo like "%'.$skey.'%")' : ''; 
		$where .= $beginDate ? ' and a.billDate>="'.$beginDate.'"' : ''; 
		$where .= $endDate ?   ' and a.billDate<="'.$endDate.'"' : '';
		$where .= 			   ' and a.sid = '.$this->jxcsys['sid'];

		if($skey){
			$caonima = array_column($this->mysql_model->get_results(INVOICE_ORDER_INFO,'(1=1) and ( skuId like "%'.$skey.'%" or invName like "%'.$skey.'%" or billNo like "%'.$skey.'%")'), 'billNo');
			if (count($caonima) > 0) {
				$cid = join('","', $caonima);
				$where .= ' and a.billNo in("' . $cid . '")';
			}else{
				$where .= ' and a.billNo = "XXXX"';
			}
		}
		switch ($type) {
			case 'allStatus':
				break;
			case 'draft':
				$where .= ' and orderStatus = 0';
				break;
			case 'pending':
				$where .= ' and orderStatus = 1';
				break;
			case 'underReview':
				$where .= ' and orderStatus = 2';
				break;
			case 'reviewPassed':
				$where .= ' and orderStatus = 4';
				break;
			case 'reviewUnPassed':
				$where .= ' and orderStatus = 3';
				break;
			case 'outku':
				$where .= ' and orderStatus = 5';
				break;
			case 'ruku':
				$where .= ' and orderStatus = 6';
				break;
			default:
				break;
		}
		$offset = $rows * ($page-1);
		$data['data']['page']      = $page;
		$data['data']['records']   = $this->data_model->get_invoice_order($where,3);        //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);                  //总分页数
		$list = $this->data_model->get_invoice_order($where.' order by '.$order.' limit '.$offset.','.$rows.''); 
		foreach ($list as $arr=>$row) {
			switch ($row['orderStatus']){
				case 0:
					$v[$arr]['orderStatus'] = '草稿';
					break;
				case 1:
					$v[$arr]['orderStatus'] = '待审核';
					break;
				case 2:
					$v[$arr]['orderStatus'] = '审核中';
					break;
				case 3:
					$v[$arr]['orderStatus'] = '审核未通过';
					break;
				case 4:
					$v[$arr]['orderStatus'] = '待出库';
					break;
				case 5:
					if ($row['outkuStatus'] == 1){
						$v[$arr]['orderStatus'] = '已出库(部分出库)';
					}else if($row['outkuStatus'] == 2){
						$v[$arr]['orderStatus'] = '已出库(全部出库)';
					}else if($row['outkuStatus'] == 3){
						$v[$arr]['orderStatus'] = '已出库(部分入库)';
					}
					break;
				case 6:
					$v[$arr]['orderStatus'] = '已完成';
					break;
			}			
			
			$orderType = $this->data_model->getOrderType(1);
		    $v[$arr]['id']           = intval($row['id']);
		    $v[$arr]['checkName']    = $row['checkName'];
			$v[$arr]['checked']      = intval($row['checked']);
			$v[$arr]['billDate']     = $row['billDate'];
			$v[$arr]['hxStateCode']  = intval($row['hxStateCode']);
		    $v[$arr]['amount']       = (float)abs($row['amount']);
			$v[$arr]['transType']    = intval($row['transType']); 
			$v[$arr]['rpAmount']     = (float)abs($row['rpAmount']);
			$v[$arr]['contactName']  = $row['contactNo'].' '.$row['contactName'];
			$v[$arr]['description']  = $row['description'];
			$v[$arr]['billNo']       = $row['billNo'];
			$v[$arr]['nid']      	 = $row['nid'];
			$v[$arr]['leibie']       = $orderType[$row['leibie']];
			$v[$arr]['totalAmount']  = (float)abs($row['totalAmount']);
			$v[$arr]['userName']     = $row['userName'];
			$v[$arr]['transTypeName']= $row['transTypeName'];
			$v[$arr]['disEditable']  = 0;
			$v[$arr]['orderStatusNum']  = $row['orderStatus'];
			$v[$arr]['outkuStatus']  = $row['outkuStatus'];
			$v[$arr]['closeStatus']  = $row['closeStatus'];
		}
		$data['data']['rows']        = $v;
		die(json_encode($data));
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
				$v['invName']       = $row['invNumber']." ".$row['invName'];
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
				$v['discountRate']  = floatval($row['discountRate']);
				$v['deduction']     = floatval($row['deduction']);
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
				$detail = array();
				foreach ($value['order'] as $arr => $row){
					$temp[$key]['order'][$arr]['billNo'] = $id;
					$qtySum 	= $qtySum + abs($row['qty']);
					$amountSum  = $amountSum + abs($row['amount']);
					$areaidSum  = $areaidSum + abs($row['areaid']);
					$detail[$arr]['InvCode'] = $row['skuId'];
					$detail[$arr]['Num'] = $row['qty'];
					$detail[$arr]['Price'] = $row['price'];
				}
				
				
				
				
// 				/* 订单生成 调NC 确认订单 start */
// 				//接口参数
// 				$postData = array(
// 						"OrderDate"=>$data['date'],
// 						"StationCode"=>$data['sid'],
// 						"SaleOrgCode" => $this->saleModel[$key],
// 						"OrderType"=>$data['leibie'],
// 						"Detail"=>	$detail,
// 				);
// 				echo '<pre>';print_r($postData);die;
// 				$post_data['data'] = json_encode($postData);
// 				$post_data['method'] = 'makeSoOrder';//接口方法
// 				$result = http_client($post_data,'NC');
				
// 				$returns = $result->return;
// 				if(empty($returns)) str_alert(-1,'调用NC接口失败');
// 				$return =  json_decode($returns,true);
// 				if($return['IsSuccess'] == 'false'){
// 					str_alert(-1,"NC返回".$return['ErrMsg']);
// 				}
// 				/* 订单生成 调NC 确认订单 end */
				
				
				
				
				$info = elements(array('billType','transType','transTypeName','buId','billDate','description','totalQty',
						'amount','arrears','rpAmount','totalAmount','hxStateCode','totalArrears','disRate','disAmount',
						'uid','sid','userName','accId','modifyTime','leibie'),$data);
	
				$info['buId']        = $contact[$key]['id'];
				$info['billNo'] 	 = $id;
				$info['totalQty'] 	 = $qtySum;
				$info['totalAmount'] = $amountSum;
				$info['amount'] 	 = $amountSum;
				$info['arrears'] 	 = $amountSum;
				$info['disAmount'] 	 = 0;
				$info['disRate'] 	 = 0;
				$info['rpAmount'] 	 = 0;
				$info['orderStatus'] = 1;
				$info['nid'] 		 = $return['SoCode'];
				$info['saleModel'] 	 = $key;
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
	
	//新增
	public function add(){
		$this->common_model->checkpurview(2);
		$data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
			$data = (array)json_decode($data, true);
			$data = $this->validform($data);
			$this->db->trans_begin();
			
			$list = $this->split_order($data);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				str_alert(-1,'SQL错误');
			} else {
				$this->db->trans_commit();
				foreach ($list as $key => $value){
					$this->common_model->logs('新增采购订单 单据编号：'.$value['trade']['billNo']);
				}
				str_alert(200,'success',array('id'=>''));
			}
		}
		str_alert(-1,'提交的是空数据');
	}
	
	//新增
	public function addnew(){
		$this->add();
	}
	
	//修改草稿订单
	public function updateDraft(){
		$this->common_model->checkpurview(3);
		$data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
			$data = $this->validform((array)json_decode($data, true));
			$invoiceData = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$data['id'].')');
			$orderStatus = $invoiceData['orderStatus'];
// 			echo '<pre>';print_r($invoiceData);die;
			if($orderStatus == 0){
				$info = elements(array(
						'billType','transType','transTypeName','buId','billDate','description',
						'totalQty','amount','arrears','rpAmount','totalAmount','hxStateCode',
						'totalArrears','disRate','disAmount','uid','userName','accId','sid','modifyTime'),$data);
				$this->db->trans_begin();
				$this->mysql_model->update(INVOICE_ORDER,$info,'(id='.$data['id'].')');
				$this->invoice_info($data['id'],$data);
				$this->order_total($data['id'],$data);
				//$this->account_info($data['id'],$data);
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					str_alert(-1,'SQL错误');
				} else {
					$this->db->trans_commit();
					$this->common_model->logs('修改采购订单 单据编号：'.$data['billNo']);
					str_alert(200,'success',array('id'=>$data['id']));
				}
			}else{
				str_alert(-1,'该订单已通过审核，不可修改');
			}
		}
		str_alert(-1,'提交的数据不能为空');
	}
	//修改保存
	public function updateInvPu(){
		$this->common_model->checkpurview(3);
		$data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
			$data = $this->validform((array)json_decode($data, true));
			
			if($orderStatus == 0 || $orderStatus == 1){
				
				$this->updateNc($orderStatus, $data);
				
			}else{
				str_alert(-1,'该订单已通过审核，不可修改');
			}			
		}
		str_alert(-1,'提交的数据不能为空');
	}
	
	public function updateNc($orderStatus,$data){
		$invoiceData = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$data['id'].')');
		$orderStatus = $invoiceData['orderStatus'];
		$saleModel = $invoiceData['saleModel'];
		$nid = $invoiceData['nid'];
		foreach ($data['entries'] as $k => $v){
			$saleModel2 = $this->mysql_model->get_row(GOODS,'(id = '.$v['invId'].') and skuId = "'.$v['skuId'].'"','saleModel');
			if(empty($saleModel)){
				continue;
			}
			if($saleModel2 != $saleModel){
				$entries[] = $v;
				continue;
			}
			$entries_in[] = $v;
			$detail[$k]['InvCode'] 	= $v['skuId'];
			$detail[$k]['Num'] 		= $v['qty'];
			$detail[$k]['Price'] 	= $v['price'];
		}
		if($orderStatus == 0){
			$postData = array(
				"OrderDate"		=> $data['date'],
				"StationCode"	=> $data['sid'],
				"SaleOrgCode" 	=> $this->saleModel[$saleModel],
				"OrderType"		=> $data['leibie'],
				"Detail"		=> $detail,
			);
// 			echo '<pre>';print_r($postData);die;
			$post_data['data'] 	 = json_encode($postData);
			$post_data['method'] = 'makeSoOrder';//接口方法
			$result 			 = http_client($post_data,'NC');
			
			$returns = $result->return;
			if(empty($returns)) str_alert(-1,'调用NC接口失败');
			$return =  json_decode($returns,true);
			if($return['IsSuccess'] == 'false'){
				str_alert(-1,"NC返回".$return['ErrMsg']);
			}
			$orderStatusInfo = array('orderStatus' => '1');
			$this->mysql_model->update(INVOICE_ORDER,$orderStatusInfo,'(1=1) and id='.$data['id']);
		}else if($orderStatus == 1){
			$postData = array(
				"OrderDate"		=> $data['date'],
				"StationCode"	=> $data['sid'],
				"SoCode"		=> $nid,
				"SaleOrgCode" 	=> $this->saleModel[$saleModel],
				"OrderType"		=> $data['leibie'],
				"Detail"		=> $detail,
			);
			$post_data['data'] = json_encode($postData);
// 			echo '<pre>';print_r($post_data);die;
			$post_data['method'] = 'updateSoOrder';//接口方法
			$result 			 = http_client($post_data,'NC');
			$returns 			 = $result->return;
			$return 			 = json_decode($returns,true);
			
			if(empty($return)) str_alert(-1,'调用NC接口失败');
			
			if($return['IsSuccess'] == 'false'){
				str_alert(-1,'NC返回：'.$return['ErrMsg']);
			}
		}
		$info = elements(array(
				'billType','transType','transTypeName','buId','billDate','description',
				'totalQty','amount','arrears','rpAmount','totalAmount','hxStateCode',
				'totalArrears','disRate','disAmount','uid','userName','accId','sid','modifyTime'),$data);
		$this->db->trans_begin();
		$this->mysql_model->update(INVOICE_ORDER,$info,'(id='.$data['id'].')');
		$this->invoice_info($data['id'],$data);
		$this->order_total($data['id'],$data);
		//$this->account_info($data['id'],$data);
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			str_alert(-1,'SQL错误');
		} else {
			$this->db->trans_commit();
			$this->common_model->logs('修改采购订单 单据编号：'.$data['billNo']);
			str_alert(200,'success',array('id'=>$data['id']));
		}
	}
	
	//获取修改信息
	public function update() {
		$this->common_model->checkpurview(1);
		$id   = intval($this->input->get_post('id',TRUE));
		$data =  $this->data_model->get_invoice_order('and (a.id='.$id.') and billType="PUR"',1);
		$list = $this->data_model->get_settle_price(' and billType="PUR"');
		$settle_price = array_bind_key($list, "invId");
		if (count($data)>0) {
			$s = $v = array();
			$info['status'] = 200;
			$info['msg']    = 'success';
			$info['data']['id']                 = intval($data['id']);
			$info['data']['buId']               = intval($data['buId']);
			$info['data']['contactName']        = $data['contactName'];
			$info['data']['date']               = $data['billDate'];
			$info['data']['billNo']             = $data['billNo'];
			$info['data']['billType']           = $data['billType'];
			$info['data']['modifyTime']         = $data['modifyTime'];
			$info['data']['checkName']          = $data['checkName'];
			$info['data']['transType']          = intval($data['transType']);
			$info['data']['totalQty']           = (float)$data['totalQty'];
			$info['data']['totalTaxAmount']     = (float)$data['totalTaxAmount'];
			$info['data']['billStatus']         = intval($data['billStatus']);
			$info['data']['disRate']            = (float)$data['disRate'];
			$info['data']['disAmount']          = (float)$data['disAmount'];
			$info['data']['amount']             = (float)abs($data['amount']);
			$info['data']['rpAmount']           = (float)abs($data['rpAmount']);
			$info['data']['arrears']            = (float)abs($data['arrears']);
			$info['data']['userName']           = $data['userName'];
			$info['data']['checked']            = intval($data['checked']);
			$info['data']['status']             = intval($data['checked'])==1 ? 'view' : 'edit';    //edit
			$info['data']['totalDiscount']      = (float)$data['totalDiscount'];
			$info['data']['totalTax']           = (float)$data['totalTax'];
			$info['data']['totalAmount']        = (float)abs($data['totalAmount']);
			$info['data']['description']        = $data['description'];
			$info['data']['leibie']        		= $data['leibie'];
			$info['data']['saleModel']        	= $data['saleModel'];
			$info['data']['orderStatus']        = $data['orderStatus'];
	
			$list = $this->data_model->get_invoice_order_info('and (iid='.$id.') order by id');
			foreach ($list as $arr=>$row) {
				$v[$arr]['invSpec']             = $row['invSpec'];
				$v[$arr]['srcOrderEntryId']     = $row['srcOrderEntryId'];
				$v[$arr]['srcOrderNo']          = $row['srcOrderNo'];
				$v[$arr]['srcOrderId']          = $row['srcOrderId'];
				$v[$arr]['goods']               = $row['invNumber'].' '.$row['brand_name'].' '.$row['skuId'].' '.$row['invName'].' '.$row['invSpec'];
				$v[$arr]['invName']             = $row['invName'];
				$v[$arr]['qty']                 = (float)abs($row['qty']);
				$v[$arr]['amount']              = (float)abs($row['amount']);
				$v[$arr]['taxAmount']           = (float)abs($row['taxAmount']);
				$v[$arr]['price']               = (float)$row['price'];
				$v[$arr]['oprice']              = $settle_price[$row['invId']]['price'] ? str_money($settle_price[$row['invId']]['price']) : '最近无采购'; 
				$v[$arr]['tax']                 = (float)$row['tax'];
				$v[$arr]['taxRate']             = (float)$row['taxRate'];
				$v[$arr]['mainUnit']            = $row['mainUnit'];
				$v[$arr]['deduction']           = empty($row['deduction']) ? 0 : (float)$row['deduction'];
				$v[$arr]['invId']               = $row['invId'];
				$v[$arr]['invNumber']           = $row['invNumber'];
				$v[$arr]['locationId']          = intval($row['locationId']);
				$v[$arr]['locationName']        = $row['locationName'];
				$v[$arr]['locationAreaId']      = intval($row['areaNo']);
				$v[$arr]['locationArea']        = $row['areaName'];
				$v[$arr]['discountRate']        = $row['discountRate'];
				$v[$arr]['unitId']              = intval($row['unitId']);
				$v[$arr]['description']         = $row['description'];
				$v[$arr]['skuId']               = intval($row['skuId']);
				$v[$arr]['skuName']             = '';
				$v[$arr]['packSpec']   			= $row['packSpec'];
				$v[$arr]['minNum']     			= $row['minNum'];
			}
			$info['data']['entries']            = $v;
			$info['data']['accId']              = (float)$data['accId'];
			$accounts = $this->data_model->get_account_info('and (iid='.$id.') order by id');
			foreach ($accounts as $arr=>$row) {
				$s[$arr]['invoiceId']           = intval($id);
				$s[$arr]['billNo']              = $row['billNo'];
				$s[$arr]['buId']                = intval($row['buId']);
				$s[$arr]['billType']            = $row['billType'];
				$s[$arr]['transType']           = $row['transType'];
				$s[$arr]['transTypeName']       = $row['transTypeName'];
				$s[$arr]['billDate']            = $row['billDate'];
				$s[$arr]['accId']               = intval($row['accId']);
				$s[$arr]['account']             = $row['accountNumber'].''.$row['accountName'];
				$s[$arr]['payment']             = (float)abs($row['payment']);
				$s[$arr]['wayId']               = (float)$row['wayId'];
				$s[$arr]['way']                 = $row['categoryName'];
				$s[$arr]['settlement']          = $row['settlement'];
			}
			$info['data']['accounts']           = $s;
			die(json_encode($info));
		}
		str_alert(-1,'单据不存在、或者已删除');
	}
	

	//购购单删除
	public function delete() {
		$this->common_model->checkpurview(4);
		$id   = intval($this->input->get('id',TRUE));
		$data = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$id.') and billType="PUR"');
		if (count($data)>0) {
			$data['checked'] >0 && str_alert(-1,'已审核的不可删除');
			$info['isDelete'] = 1;
			$this->db->trans_begin();
			$this->mysql_model->update(INVOICE_ORDER,$info,'(id='.$id.')');
			$this->mysql_model->update(INVOICE_ORDER_INFO,$info,'(iid='.$id.')');
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
	
	//公共验证
	private function validform($data) {
	
		(float)$data['arrears'] < 0 || !is_numeric($data['arrears']) && str_alert(-1,'本次欠款要为数字，请输入有效数字！');
		(float)$data['disRate'] < 0 || !is_numeric($data['disRate']) && str_alert(-1,'折扣率要为数字，请输入有效数字！');
		(float)$data['rpAmount'] < 0 || !is_numeric($data['rpAmount']) && str_alert(-1,'本次收款要为数字，请输入有效数字！');
		(float)$data['amount'] < (float)$data['rpAmount']  && str_alert(-1,'本次付款不能大于折后金额！');
		(float)$data['amount'] < (float)$data['disAmount'] && str_alert(-1,'折扣额不能大于合计金额！');
	
		if (isset($data['id'])&&intval($data['id'])>0) {
			$data['id'] = intval($data['id']);
			$invoice = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$data['id'].') and billType="PUR" and isDelete=0');  //修改的时候判断
			count($invoice)<1 && str_alert(-1,'单据不存在、或者已删除');
			$invoice['checked']>0 && str_alert(-1,'审核后不可修改');
			$data['billNo'] =  $invoice['billNo'];
		} else {
			$data['billNo']      = str_no('CGO');    //修改的时候屏蔽
		}
	
		$data['billType']        = 'PUR';
		$data['transType']       = intval($data['transType']);
		$data['transTypeName']   = $data['transType']==150501 ? '采购' : '退货';
		$data['buId']            = intval($data['buId']);
		$data['billDate']        = $data['date'];
		$data['description']     = $data['description'];
		$data['totalQty']        = (float)$data['totalQty'];
		if ($data['transType']==150501) {
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
		$data['hxStateCode']     = $data['rpAmount']==$data['amount'] ? 2 : ($data['rpAmount']>0 ? 1 : 0);
		$data['totalArrears']    = (float)$data['totalArrears'];
		$data['disRate']         = (float)$data['disRate'];
		$data['disAmount']       = (float)$data['disAmount'];
		$data['uid']             = $this->jxcsys['uid'];
		$data['sid']			 = $this->jxcsys['sid'];
		$data['userName']        = $this->jxcsys['name'];
		$data['accId']           = (float)$data['accId'];
	
		$data['modifyTime']      = date('Y-m-d H:i:s');
	
		//选择了结算账户 需要验证
		if (isset($data['accounts']) && count($data['accounts'])>0) {
			foreach ($data['accounts'] as $arr=>$row) {
				(float)$row['payment'] < 0 || !is_numeric($row['payment']) && str_alert(-1,'结算金额要为数字，请输入有效数字！');
			}
		}
	
		//供应商验证
		$this->mysql_model->get_count(CONTACT,'(id='.intval($data['buId']).')')<1 && str_alert(-1,'采购单位不存在');
		
		return $data;
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
				$inventory = $this->data_model->get_invoice_info_inventory();
			} else {
				$item = $data['entries'];
			}
			$storage   = array_column($this->mysql_model->get_results(STORAGE,'(disable=0)'),'id');
			foreach ($item as $arr=>$row) {
				!isset($row['invId']) && str_alert(-1,'参数错误');
				!isset($row['locationId']) && str_alert(-1,'参数错误');
				(float)$row['qty'] < 0 || !is_numeric($row['qty']) && str_alert(-1,'商品数量要为数字，请输入有效数字！');
				(float)$row['price'] < 0 || !is_numeric($row['price']) && str_alert(-1,'商品销售单价要为数字，请输入有效数字！');
				(float)$row['discountRate'] < 0 || !is_numeric($row['discountRate']) && str_alert(-1,'折扣率要为数字，请输入有效数字！');
// 				intval($row['locationId']) < 1 && str_alert(-1,'请选择相应的仓库！');
				!in_array(intval($row['locationId']),$storage) && str_alert(-1,$row['locationName'].'不存在或不可用！');
				//库存判断
				if ($system['requiredCheckStore']==1) {
					if (intval($data['transType'])==150502) {                        //退货才验证
						if (isset($inventory[$row['invId']][$row['locationId']])) {
							$inventory[$row['invId']][$row['locationId']] < (float)$row['qty'] && str_alert(-1,$row['locationName'].$row['invName'].'商品库存不足！');
						} else {
							str_alert(-1,$row['invName'].'库存不足！');
						}
					}
				}
			}
		} else {
			str_alert(-1,'提交的是空数据');
		}
		return $data;
	}
	
	
	//组装数据
	private function invoice_info($iid,$data) {
		if (is_array($data['entries'])) {
			foreach ($data['entries'] as $arr=>$row) {
				$v[$arr]['iid']           = intval($iid);
				$v[$arr]['sid']     	  = $data['sid'];
				$v[$arr]['billNo']        = $data['billNo'];
				$v[$arr]['buId']          = $data['buId'];
				$v[$arr]['billDate']      = $data['billDate'];
				$v[$arr]['billType']      = $data['billType'];
				$v[$arr]['transType']     = $data['transType'];
				
				
				
				$v[$arr]['transTypeName'] = $data['transTypeName'];
				$v[$arr]['invId']         = $row['invId'];
				$v[$arr]['invName']       = $row['invNumber']." ".$row['skuId']." ".$row['invName'];
				$v[$arr]['skuId']         = intval($row['skuId']);
	
				$v[$arr]['unitId']        = intval($row['unitId']);
				$v[$arr]['locationId']    = intval($row['locationId']);
				$v[$arr]['areaid']    = intval($row['locationareaId'] || 0);
				if ($data['transType']==150501) {
					$v[$arr]['qty']       = abs($row['qty']);
					$v[$arr]['amount']    = abs($row['amount']);
				} else {
					$v[$arr]['qty']       = -abs($row['qty']);
					$v[$arr]['amount']    = -abs($row['amount']);
				}
				$v[$arr]['price']         = abs($row['price']);
				$v[$arr]['discountRate']  = $row['discountRate'];
				$v[$arr]['deduction']     = $row['deduction'];
	
				$v[$arr]['description']   = $row['description'];
				//$v[$arr]['srcOrderEntryId']    = intval($row['srcOrderEntryId']);
				//					$v[$arr]['srcOrderNo']         = $row['srcOrderNo'];
				//					$v[$arr]['srcOrderId']         = intval($row['srcOrderId']);
	
			}
			if (isset($v)) {
				if (isset($data['id']) && $data['id']>0) {                    //修改的时候
					$this->mysql_model->delete(INVOICE_ORDER_INFO,'(iid='.$iid.')');
				}
				$this->mysql_model->insert(INVOICE_ORDER_INFO,$v);
			}
		}
	}
	
	//组装数据
	private function account_info($iid,$data) {
		if (isset($data['accounts']) && count($data['accounts'])>0) {
			foreach ($data['accounts'] as $arr=>$row) {
				if (isset($row['accId']) && intval($row['accId'])>0) {
					$v[$arr]['iid']           = intval($iid);
					$v[$arr]['sid']     	  = $data['sid'];
					$v[$arr]['billNo']        = $data['billNo'];
					$v[$arr]['buId']          = $data['buId'];
					$v[$arr]['billType']      = $data['billType'];
					$v[$arr]['transType']     = $data['transType'];
					$v[$arr]['transTypeName'] = $data['transType']==150501 ? '普通采购' : '采购退回';
					$v[$arr]['payment']       = $data['transType']==150501 ? -$row['payment'] : $row['payment'];
					$v[$arr]['billDate']      = $data['billDate'];
					$v[$arr]['accId']         = $row['accId'];
					$v[$arr]['wayId']         = $row['wayId'];
					$v[$arr]['settlement']    = $row['settlement'];
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
	
	//订单出入库表
	private function order_total($iid,$data) {
		if (is_array($data['entries'])) {
			foreach ($data['entries'] as $arr=>$row) {
				$v[$arr]['iid']           = intval($iid);
				$v[$arr]['sid']     	  = $data['sid'];
				$v[$arr]['billNo']        = $data['billNo'];
				$v[$arr]['invNum']        = abs($row['qty']);
				$v[$arr]['waitOut']       = abs($row['qty']);
				$v[$arr]['invId']         = $row['invId'];
			}
			if (isset($v)) {
				if (isset($data['id']) && $data['id']>0) {                      //修改的时候
					$this->mysql_model->delete(ORDER_TOTAL,'(iid='.$iid.')');
				}
				$this->mysql_model->insert(ORDER_TOTAL,$v);
			}
		}
	}
	
	
	
	
	/**
	 * 新订单详情时间参数日以后的单据走此方法
	 * @param unknown $orderid
	 */
	private function orderListNew($order){
		
		$orderid = $order['id'];
		//订单商品
		$time = time();
		$where = 'and a.id ='.$orderid.' and sid = '.$this->jxcsys['sid'];
		$all_goods = $this->data_model->get_order_list($where,2);
		$qty = array_column($all_goods, 'qty');//得到总件数
		
		
		//出库详情
		$accept = $this->mysql_model->get_results(ORDER_ACCEPT,'(iid='.$orderid.') order by time desc');
		if(count($accept) > 0){
			$where2 = 'a.iid ='.$orderid . ' and acceptId in ('.join(",",array_column($accept, "id")).') and sid = '.$this->jxcsys['sid'];
			$out_goods = $this->data_model->get_out_goods_new($where2,2);
		}
		$accept = array_bind_key($accept, "id");
		
		//剩余详情
		$where3 = 'a.iid ='.$orderid.' and a.waitOut > 0 and sid = '.$this->jxcsys['sid'];
		$last_goods = $this->data_model->get_last_goods($where3,2);
		
		$invStatus = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$orderid.')');
		
		$totalList = $this->mysql_model->get_results(ORDER_TOTAL,'(iid='.$orderid.')');
		$totalList = array_bind_key($totalList, "invId");
		
		//得到总数
		foreach ($out_goods as $k => $v){
			$out_goods[$k]['outTime'] = dateTime($v['outTime']);
			$total_data = $totalList[$v['invId']];
			$totalNum = $total_data['invNum'];
			$out_goods[$k]['totalNum'] = $totalNum;
		}
		
		$data = $order;
		$data['orderid'] = $orderid;
		$data['totalAmount'] = str_money_c($invStatus['amount']);
		$data['sum'] = array_sum($qty);
		
		$data['last_goods'] = $last_goods; 	// 未出库商品
		$data['all_goods'] 	= $all_goods; 	// 所有商品
		
		//仓库相关
		$ck = $this->mysql_model->get_results(STORAGE,'(sid = '.$this->jxcsys['sid'].') and isDelete = 0 order by id,isDefault desc','id,name,isDefault');
		if(empty($ck) || count($ck) == 0){
			alert2('对不起！，请先在设置->仓库管理中添加仓库！。');
		}
		
		//查询		
		$areaList = $this->mysql_model->get_results(STORAGE_AREA,'(sid = '.$this->jxcsys['sid'].') and isDelete = 0 and str_id = '.$ck[0]['id']);
		$areaList = array_bind_key($areaList, "id");
		//查询关系
		$areaReList = $this->mysql_model->get_results(GOODS_AREA_REL,'(sid = '.$this->jxcsys['sid'].') and storage_id = '.$ck[0]['id']);
		$areaReList = array_bind_key($areaReList, "item_id");
		//		
		$defArea = $this->mysql_model->get_row(STORAGE_AREA,'(sid = '.$this->jxcsys['sid'].') and isDelete = 0 and area_code = "IN-00-01" and str_id = '.$ck[0]['id']);
		foreach ($out_goods as $k => $v){
			$areaInfo = $areaReList[$v['invId']];
			if(!empty($areaInfo)){
				$out_goods[$k]['storageId']   = $areaInfo['storage_id'];
				$out_goods[$k]['storageName'] = $ck[0]['name'];
				$out_goods[$k]['areaId'] 	  = $areaInfo['area_id'];
				$out_goods[$k]['areaName']    = $areaList[$areaInfo['area_id']]['area_name'];
			}else{
				$out_goods[$k]['storageId'] = $ck[0]['id'];
				$out_goods[$k]['storageName'] = $ck[0]['name'];
				$out_goods[$k]['areaId'] = $defArea['id'];
				$out_goods[$k]['areaName'] = $defArea['area_name'];
			}
			$accept[$out_goods[$k]['acceptId']]['detail'][] = $out_goods[$k];
		}
		
		$data['out_goods'] 	= $out_goods; 	// 已出库商品
		$data['accept'] 	= array_values($accept); 	// 已出库商品
		$this->load->view('scm/invOd/orderListNew',$data);
	}
	
	
	/**
	 * 订单详情旧方法
	 * @param unknown $orderid
	 */
	private function orderListOld($order){

		$orderid = $order['id'];
		
		$invStatus = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$orderid.')');
		
		//订单商品
		$time = time();
		$where = 'and a.id ='.$orderid.' and sid = '.$this->jxcsys['sid'];
		$all_goods = $this->data_model->get_order_list($where,2);
		
		//得到总件数
		$qty = array_column($all_goods, 'qty');
		
		//出库详情
		$where2 = 'a.iid ='.$orderid . ' and sid = '.$this->jxcsys['sid'];
		$out_goods = $this->data_model->get_out_goods($where2,2);
		
		//剩余详情
		$where3 = 'a.iid ='.$orderid.' and a.waitOut > 0 and sid = '.$this->jxcsys['sid'];
		$last_goods = $this->data_model->get_last_goods($where3,2);
		
		$totalList = $this->mysql_model->get_results(ORDER_TOTAL,'(iid='.$orderid.')');
		$totalList = array_bind_key($totalList, "invId");
		
		//得到总数
		foreach ($out_goods as $k => $v){
			$out_goods[$k]['outTime'] = dateTime($v['outTime']);
			$total_data = $totalList[$v['invId']];
			$waitOut = $total_data['waitOut'];//待出
			$waitInto = $total_data['waitInto'];//待入
			$haveInto = $total_data['haveInto'];//已入
			$out_goods[$k]['waitInto'] = $waitInto;
				
			if($waitOut == 0){
				if($waitInto == 0){
					$out_goods[$k]['status'] = '全部入库';
				}else{
					$out_goods[$k]['status'] = '全部出库';
				}
			}else{
				if($waitInto == 0 && $haveInto > 0){
					$out_goods[$k]['status'] = '部分入库';
				}else if($waitInto == 0 && $haveInto == 0){
					$out_goods[$k]['status'] = '待出库';
				}else{
					$out_goods[$k]['status'] = '部分出库';
				}
			}
		}
		$data = $order;
		$data['orderid'] = $orderid;
		
		$data['totalAmount'] = str_money_c($invStatus['amount']);
		$data['sum'] = array_sum($qty);
		
		$data['last_goods'] = $last_goods; 	// 未出库商品
		$data['all_goods'] 	= $all_goods; 	// 所有商品
		
		//仓库相关
		$ck = $this->mysql_model->get_results(STORAGE,'(sid = '.$this->jxcsys['sid'].') and isDelete = 0 order by id,isDefault desc','id,name,isDefault');
		if(empty($ck) || count($ck) == 0){
			alert2('对不起！，请先在设置->仓库管理中添加仓库！。');
		}
		//
		$areaList = $this->mysql_model->get_results(STORAGE_AREA,'(sid = '.$this->jxcsys['sid'].') and isDelete = 0 and str_id = '.$ck[0]['id']);
		$areaList = array_bind_key($areaList, "id");
		//查询关系
		$areaReList = $this->mysql_model->get_results(GOODS_AREA_REL,'(sid = '.$this->jxcsys['sid'].') and storage_id = '.$ck[0]['id']);
		$areaReList = array_bind_key($areaReList, "item_id");
		//
		$defArea = $this->mysql_model->get_row(STORAGE_AREA,'(sid = '.$this->jxcsys['sid'].') and isDelete = 0 and area_code = "IN-00-01" and str_id = '.$ck[0]['id']);
		//
		foreach ($out_goods as $k => $v){
			$areaInfo = $areaReList[$v['invId']];
			if(!empty($areaInfo)){
				$out_goods[$k]['storageId']   = $areaInfo['storage_id'];
				$out_goods[$k]['storageName'] = $ck[0]['name'];
				$out_goods[$k]['areaId'] 	  = $areaInfo['area_id'];
				$out_goods[$k]['areaName']    = $areaList[$areaInfo['area_id']]['area_name'];
			}else{
				$out_goods[$k]['storageId'] = $ck[0]['id'];
				$out_goods[$k]['storageName'] = $ck[0]['name'];
				$out_goods[$k]['areaId'] = $defArea['id'];
				$out_goods[$k]['areaName'] = $defArea['area_name'];
			}
		}
		$data['out_goods'] 	= $out_goods; 	// 已出库商品
		$this->load->view('scm/invOd/orderList',$data);
		
	}
	
	
	
	/**
	 * 订单详情页面
	 */
	public function orderList(){
		
		$this->common_model->checkpurview(180);
		
		$orderid = $this->input->get_post('id',TRUE);
		$order = $this->mysql_model->get_row(INVOICE_ORDER,'(id="'.$orderid.'")');
		
		if(empty($orderid)){
			$number = $this->input->get_post('number',TRUE);
			$order = $this->mysql_model->get_row(INVOICE_ORDER,'(billNo="'.$number.'" or nid = "'.$number.'")');
			$orderid = $order['id'];
			if(empty($orderid)){
				alert2('订单不存在！');
			}
		}
		//如果是2016-12-29日以后的采购订单执行新的订单详情逻辑
		//if(strtotime($order['billDate']) >= strtotime("2016-12-29")){
		if(false){
			$this->orderListNew($order);			
		}else{
			$this->orderListOld($order);
		}
		
	}
	
	
    /**
     * 打印
     */
	public function toPdf(){
		$data = $this->input->get_post("print_data",true);
		$data = json_decode($data,true);
        foreach ($data as $key => $value){
            $data['list'][$key]['goods'] = $value['goods'];
            $data['list'][$key]['waitInto'] =  $value['outNum'];
            $data['list'][$key]['outNum'] = $value['waitInfo'];
            $data['list'][$key]['location'] = $value['ckName'];
            $data['list'][$key]['area'] = $value['areaName'];
        }
        $data['system'] = $this->common_model->get_option('system');
//        print_R($data);die;
        ob_start();
        $this->load->view('scm/invOd/toPdf',$data);
        $content = ob_get_clean();
        require_once('./application/libraries/html2pdf/html2pdf.php');
        try {
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->setDefaultFont('javiergb');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->writeHTML($content, '');
            $html2pdf->Output('invOd_'.date('ymdHis').'.pdf');
        }catch(HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
    }
    
    public function toPdfNew(){
    	$data = $this->input->get_post("print_data",true);
    	$data = json_decode($data,true);
    	
    	
    	foreach ($data as $arr => $row){
    		$result['accept'] = $this->mysql_model->get_row(ORDER_ACCEPT,'(id='.$row['id'].')');
    		foreach ($row['detail'] as $key => $value){
    			$result['list'][$key] = $value;
    		}
    	}
    	$result['system'] = $this->common_model->get_option('system');
    	ob_start();
    	$this->load->view('scm/invOd/toPdfNew',$result);
    	$content = ob_get_clean();
    	require_once('./application/libraries/html2pdf/html2pdf.php');
    	try {
    		$html2pdf = new HTML2PDF('P', 'A4', 'en');
    		$html2pdf->setDefaultFont('javiergb');
    		$html2pdf->pdf->SetDisplayMode('fullpage');
    		$html2pdf->writeHTML($content, '');
    		$html2pdf->Output('invOd_'.date('ymdHis').'.pdf');
    	}catch(HTML2PDF_exception $e) {
    		echo $e;
    		exit;
    	}
    }


	/**
	 *  获取仓库对应的货位
	 */
	public function goods_area (){
		$sid = $this->jxcsys['sid'] ;
		$str_id = $this->input->get_post('strId',TRUE);
		$invId = $this->input->get_post('invId',TRUE);
		$areaId= $this->mysql_model->get_row(GOODS_AREA_REL,'(sid = '.$this->jxcsys['sid'].') and item_id = '.$invId.' and storage_id = '.$str_id,'area_id');
		$areas = $this->mysql_model->get_results(STORAGE_AREA,'(isDelete = 0) and sid ='.$this->jxcsys['sid'].' and str_id='.$str_id,'id,area_name');
		$data['areas'] = $areas;
		if($areaId){
			$data['areaId'] = $areaId;
		}else{
			$data['areaId'] = $areas[0]['id'];
		}

		$data['status'] = 200;
		$data['msg']    = 'success';
		die(json_encode($data));
	}
	
	/**
	 * 订单关闭商品
	 */
	public function closeGoods(){
		// Array([iid]=>236[close_goods]=>Array([0]=>5577[1]=>5578))
		$data = $this->input->get('data',TRUE);
// 		echo '<pre>';print_r($data);die;
		$iid = $data['iid'];
		
	    $this->db->trans_begin();
	    
	    $invOrder = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$iid.')');
	    
	    if(empty($data['close_goods'])){
	    	str_alert(-1,'请选择要关闭的商品！！');
	    }
	    
	    $ids = join(',',$data['close_goods']);
	    $sku = $this->mysql_model->get_results(GOODS,'(1=1) and id in ('.$ids.')',"skuId as InvCode");
	    
	    $postData = array(
	    		"SaleOrgCode" => $this->saleModel[$invOrder['saleModel']],
	    		"SOCode" => $invOrder['nid'],
	    		"List" => $sku,
	    );
	    
	    $post_data['method'] = 'closeSoOrder';//接口方法
	    $post_data['data'] = json_encode($postData);
	    $result = http_client($post_data,'NC');
	    $returns = $result->return;
	    
	    if(empty($returns)) str_alert(-1,'调用NC接口失败');
	    
	    $return =  json_decode($returns,true);
	    if($return['IsSuccess'] == 'false'){
	    	str_alert(-1,$return['ErrMsg']);
	    	/* if($return['ErrCode'] != "-32000"){
	    		str_alert(-1,$return['ErrMsg']);
	    	} */
	    }
	    
		foreach ($data['close_goods'] as $key => $value) {
			
			$goods_total = $this->mysql_model->get_row(ORDER_TOTAL,'(iid='.$iid.') and invId='.$value);
			$time = time();
			$info[$key]['sid'] 			= $this->jxcsys['sid'];
			$info[$key]['iid'] 			= $data['iid'];
			$info[$key]['closeInvId'] 	= $value;
			$info[$key]['closeNum']		= $goods_total['waitOut'];
			$info[$key]['closeTime'] 	= $time;			
			
			//订单数据表 
			$info_total['waitOut'] = 0;
			$info_total['closeNum'] = $goods_total['waitOut'];
			$this->mysql_model->update(ORDER_TOTAL,$info_total,'(iid='.$iid.') and invId='.$value);

			//订单表
			/*判断是否全部关闭*/
			$total_invNum 	= $goods_total['invNum'];
			$total_waitInto = $goods_total['waitInto'];
			$total_waitOut 	= $goods_total['waitOut'];
			$total_haveInto = $goods_total['haveInto'];
			$total_closeNum = $goods_total['closeNum'];

			if($total_invNum == $total_waitOut + $total_closeNum && $total_waitInto == 0 && $total_haveInto == 0){
				$info_order['closeStatus'] = '2';
			}else{
				$info_order['closeStatus'] = '1';
			}

		}
		$this->mysql_model->insert(CLOSE_ORDERGOODS,$info);
		$this->mysql_model->update(INVOICE_ORDER,$info_order,'(id='.$iid.')');
		if($this->db->trans_status() == FALSE){
    		$this->db->trans_rollback();
			str_alert(-1,'关闭失败'); 
		}else{
		    $this->db->trans_commit();
			str_alert(200,'success');
		}
	}
	
	
	
	/**
	 * 新订单入库商品处理
	 */
	public function intoGoodsNew(){
		$data = $this->input->get_post('data',TRUE);
		$oid = $this->input->get_post('oid',TRUE);
		$data = json_decode($data,true);
		if(count($data)>0){
			$this->db->trans_begin();
				
			foreach ($data as $arr => $row){
				if(empty($row["id"])) str_alert(-1,$row["wid"]."未找到订单序列号！请联系运营人员");
				
				$accept = $this->mysql_model->get_row(ORDER_ACCEPT,"(id=".$row["id"].")");
				
				if(empty($accept)) str_alert(-1,$row["wid"]."单据异常！请联系运营人员");
				if($accept['status'] == 2) str_alert(-1,$accept["wid"]."已经入库完成,请刷新页面！");
				
				
				$detail_accept = $this->mysql_model->get_results(ORDER_DETAIL_ACCEPT,'(acceptId='.$row['id'].')');
				$accept = array_bind_key($detail_accept, "id");
				
				foreach ($row['detail'] as $key => $value) {
		
					$order_detail = $accept[$value['id']];
		
					if(empty($order_detail)){
						$this->db->trans_rollback();
						str_alert(-1,'入库失败！未找到出库单！');
					}
					$iid 		= $order_detail['iid'];		//订单id
					$invId 		= $order_detail['invId'];	//商品id
					$goods_out 	= $value['outNum'];			//出库数量
					$goods_id[$key] = $invId;
						
					$entries[$key] = $this->mysql_model->get_row(INVOICE_ORDER_INFO,'(iid='.$iid.') and invId='.$invId);
					$entries[$key]['qty'] = $goods_out;
					$entries[$key]['areaId'] = $value['area'];
					$entries[$key]['amount'] = $goods_out * $entries[$key]['price'];
					$entries[$key]['locationId'] = $value['ck'];
		
					$order_total = $this->mysql_model->get_row(ORDER_TOTAL,'(iid='.$iid.') and invId='.$invId);
					if(!empty($order_total) && $order_total['waitInto'] >= $goods_out){
						// 获取当前 total 表中haveInto/waitInto 的数量
						$info_total['haveInto'] 	= $order_total['haveInto'] + $goods_out;
						$info_total['waitInto'] 	= $order_total['waitInto'] - $goods_out;
						//更改 出入库数据总表
						$this->mysql_model->update(ORDER_TOTAL,$info_total,'(iid='.$iid.') and invId='.$invId );
						$this->mysql_model->update(ORDER_DETAIL_ACCEPT,array('status'=>1,'inNum'=>$goods_out),'(id='.$value['id'].') and invId='.$invId );
					}else{
						$this->db->trans_rollback();
						str_alert(-1,'入库失败！请重新刷新页面。');
					}
				}
				
				$detail_accept = $this->mysql_model->get_results(ORDER_DETAIL_ACCEPT,'(acceptId='.$row['id'].')');
				$haveNum = array_sum(array_column($detail_accept,'status'));
				$accept_count = count($detail_accept);
				
				$accept_update = array("status"=>1);
				if($haveNum == $accept_count){
					$accept_update['status'] = 2;
				}
				
				$rs = $this->mysql_model->update(ORDER_ACCEPT,$accept_update,'(id='.$row["id"].')');
				
			}
			
			
			
			$totalQty = $this->mysql_model->get_results(ORDER_TOTAL,'(iid='.$oid.')');
			$waitNum = array_sum(array_column($totalQty,'waitInto'));
				
			$info_order['orderStatus'] = 6;
			$info_order['outkuStatus'] = null;
				
			if($waitNum > 0){
				$info_order['orderStatus']  = 5;
				$info_order['outkuStatus'] 	= 3;
			}
			$this->mysql_model->update(INVOICE_ORDER,$info_order,'(id='.$oid.')');
				
			//生成采购单begin
			$invoice_order = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$iid.')');
			$invoice_order['areaId'] = $data['area'];
			$invoice_order['billDate'] = date('Y-m-d',time());
			$invoice_order['modifyTime'] = date('Y-m-d H:i:s',time());
			$invoice_order['billNo'] = str_no('CG');
			$totalQty = $totalAmount = '';
			foreach ($entries as $v){
				$totalQty = $v['qty'];
				$totalAmount += $v['amount'];
			}
			$invoice_order['totalQty'] = $totalQty;
			$invoice_order['totalAmount'] = $totalAmount;
			$invoice_order['amount'] = $totalAmount;
			$invoice_order['arrears'] = $totalAmount;
	
			$info = elements(array('billNo','billType','transType','transTypeName','buId',
					'billDate','description','totalQty','amount','arrears',
					'rpAmount','totalAmount','hxStateCode','totalArrears','disRate',
					'disAmount','uid','sid','userName','accId','modifyTime'),$invoice_order);
	
			$sourceOrder = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$oid.')','nid');
			$info['sourceType'] = 2;
			$info['sourceOrder'] = $sourceOrder;
			$iid_new = $this->mysql_model->insert(INVOICE,$info);
	
			$invoice_order['entries'] = $entries;
			$this->invoice_info_new($iid_new,$invoice_order);
			
			//生成采购单 				end
			if($this->db->trans_status() == FALSE){
				$this->db->trans_rollback();
				str_alert(-1,'入库失败');
			}else{
				$this->db->trans_commit();
				str_alert(200,'success');
			}
		}
		str_alert(-1,'选择需要入库的商品');
	
	}
	
	
	
	
	

	/**
	 * 订单入库商品处理
	 */
	//入库处理
	public function intoGoods(){
		$data = $this->input->get_post('data',TRUE);
		$oid = $this->input->get_post('oid',TRUE);
		$data = json_decode($data,true);
		if(count($data)>0){
			$this->db->trans_begin();
			
			foreach ($data as $key => $value) {
				
				$order_detail = $this->mysql_model->get_row(ORDER_DETAIL,'(id='.$value['id'].')');

				if(!$order_detail){
					$this->db->trans_rollback();
					str_alert(-1,'入库失败！未找到出库单！');
				}
				$iid 		= $order_detail['iid'];		//订单id
				$invId 		= $order_detail['invId'];	//商品id
				$goods_out 	= $value['outNum'];			//出库数量
				$goods_id[$key] = $invId;
			
				$entries[$key] = $this->mysql_model->get_row(INVOICE_ORDER_INFO,'(iid='.$iid.') and invId='.$invId);
				$entries[$key]['qty'] = $goods_out;
				$entries[$key]['areaId'] = $value['area'];
				$entries[$key]['amount'] = $goods_out * $entries[$key]['price'];
				$entries[$key]['locationId'] = $value['ck'];
				
				$order_total = $this->mysql_model->get_row(ORDER_TOTAL,'(iid='.$iid.') and invId='.$invId);
				if(!empty($order_total) && $order_total['waitInto'] >= $goods_out){
					// 获取当前 total 表中haveInto/waitInto 的数量
					$info_total['haveInto'] 	= $order_total['haveInto'] + $goods_out;
					$info_total['waitInto'] 	= $order_total['waitInto'] - $goods_out;
					//更改 出入库数据总表
					$this->mysql_model->update(ORDER_TOTAL,$info_total,'(iid='.$iid.') and invId='.$invId );
				}else{
					$this->db->trans_rollback();
					str_alert(-1,'入库失败！请重新刷新页面。'); 
				}
			}
			
			$totalQty = $this->mysql_model->get_results(ORDER_TOTAL,'(iid='.$oid.')');
			$waitNum = array_sum(array_column($totalQty,'waitInto'));
			
			$info_order['orderStatus'] = 6;
			$info_order['outkuStatus'] = null;
			
			if($waitNum > 0){
				$info_order['orderStatus']  = 5;
				$info_order['outkuStatus'] 	= 3;
			}
			$this->mysql_model->update(INVOICE_ORDER,$info_order,'(id='.$oid.')');
			
			//生成采购单begin
			$invoice_order = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$iid.')');
			$invoice_order['areaId'] = $data['area'];
			$invoice_order['billDate'] = date('Y-m-d',time());
			$invoice_order['modifyTime'] = date('Y-m-d H:i:s',time());
			$invoice_order['billNo'] = str_no('CG');
			$totalQty = $totalAmount = '';
			foreach ($entries as $v){
				$totalQty = $v['qty'];
				$totalAmount += $v['amount'];
			}
			$invoice_order['totalQty'] = $totalQty;
			$invoice_order['totalAmount'] = $totalAmount;
			$invoice_order['amount'] = $totalAmount;
			$invoice_order['arrears'] = $totalAmount;

			$info = elements(array('billNo','billType','transType','transTypeName','buId',
				'billDate','description','totalQty','amount','arrears',
				'rpAmount','totalAmount','hxStateCode','totalArrears','disRate',
				'disAmount','uid','sid','userName','accId','modifyTime'),$invoice_order);

			$order_iid = $this->mysql_model->get_row(ORDER_DETAIL,'(id='.$data[0]['id'].')','iid');
			$sourceOrder = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$order_iid.')','nid');
			$info['sourceType'] = 2;
			$info['sourceOrder'] = $sourceOrder;
			$iid_new = $this->mysql_model->insert(INVOICE,$info);

			$invoice_order['entries'] = $entries;
			$this->invoice_info_new($iid_new,$invoice_order);
			//生成采购单 				end
			if($this->db->trans_status() == FALSE){
	    		$this->db->trans_rollback();
				str_alert(-1,'入库失败'); 
			}else{
			    $this->db->trans_commit();
				str_alert(200,'success');
			}
		}
		str_alert(-1,'选择需要入库的商品');
		
	}

	/**
	 * 更新库存
	 * @param unknown $data
	 */
	private function storage_info($data){
		$cur_time = time();
		
		if (!is_array($data['entries'])) return ;
		foreach ($data['entries'] as $arr=>$row) {
			$s[$arr]['num']           =  abs($row['qty']);
			$s[$arr]['itemId']        = $row['invId'];
			$s[$arr]['skuId']      	  = $row['skuId'];
			$s[$arr]['areaId']    	  = intval($row['areaId']);
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
					$row['num']  = '+'.$row['num'];
					$this->mysql_model->update(GOOODS_STORAGE.$this->jxcsys['sid'],' num = num'.$row['num'].' , areaId ='.$row['areaId'],$where);
				}
			}else{
				$row['num']  = '+'.$row['num'];
				$this->mysql_model->update(GOOODS_STORAGE.$this->jxcsys['sid'],' num = num'.$row['num'].' , areaId ='.$row['areaId'],$where);
			}
		}
	}
	
	//组装采购单数据
	private function invoice_info_new($iid,$data) {
		
	    if (is_array($data['entries'])) {
			foreach ($data['entries'] as $arr=>$row) {
				$v[$arr]['iid']           = intval($iid);
				$v[$arr]['sid']     	  = $data['sid'];
				$v[$arr]['billNo']        = $data['billNo'];
				$v[$arr]['buId']          = $data['buId'];
				$v[$arr]['billDate']      = $data['billDate']; 
				$v[$arr]['billType']      = $data['billType'];
				$v[$arr]['transType']     = $data['transType'];
				$v[$arr]['transTypeName'] = $data['transTypeName'];
				$v[$arr]['invId']         = $row['invId'];
				$v[$arr]['skuId']         = intval($row['skuId']);
				
				$v[$arr]['unitId']        = intval($row['unitId']);
				$v[$arr]['locationId']    = intval($row['locationId']);
				$v[$arr]['areaId']    = intval($row['areaId']);
				$v[$arr]['qty']       = abs($row['qty']); 
				$v[$arr]['amount']    = abs($row['amount']); 
				$v[$arr]['price']         = abs($row['price']);  
				$v[$arr]['discountRate']  = $row['discountRate'];  
				$v[$arr]['deduction']     = $row['deduction']; 

				$v[$arr]['description']   = $row['description']; 

			}
// 			echo '<pre>';print_r($v);die;
			if (isset($v)) {
				$this->mysql_model->insert(INVOICE_INFO,$v);
			}
		} 
	}
	

	/**
	 * 订单退货
	 */
	
	// 退货操作 
	public function returnGoods(){
		$this->common_model->checkpurview(58);
		$iid = $this->input->get_post('id',TRUE);
		//获取已入库的商品
		$totalData = $this->mysql_model->get_results(ORDER_TOTAL,'(iid='.$iid.' and haveInto > 0)');
		$inventory = $this->data_model->get_goods_info_inventory();
		if(count($totalData)){
			$goods = $this->mysql_model->get_results(GOODS,'(id in ('.join(',', array_column($totalData, "invId")).'))');
			$goods = array_bind_key($goods, 'id');
		}
		foreach ($totalData as $key => $value) {
			if($value['haveInto'] > 0){
				//获取库存
				$data['returnGoods'][$key]['goods_storage'] = empty($inventory[$value['invId']]['qty']) ? 0 : $inventory[$value['invId']]['qty'];
				//$data['returnGoods'][$key]['goods'] = $this->mysql_model->get_row(GOODS,'(id='.$value['invId'].')');
				$data['returnGoods'][$key]['goods'] = $goods[$value['invId']];
				$data['returnGoods'][$key]['invId'] = $value['invId'];
				$data['returnGoods'][$key]['haveInto'] =$value['haveInto'];
				$data['returnGoods'][$key]['iid'] = $iid;

				if($value['rtNum'] > 0){
					$maxNum = $value['haveInto'] - $value['rtNum'];
					// $data['returnGoods'][$key]['maxNum'] = $goods_storage > $maxNum ? ($maxNum > 0 ? $maxNum : 0) : $goods_storage;
					$data['returnGoods'][$key]['maxNum'] = ($maxNum > 0 ? $maxNum : 0);
					$data['returnGoods'][$key]['rtNum'] =$value['rtNum'];
				}else{
					// $data['returnGoods'][$key]['maxNum'] = $goods_storage > $value['haveInto'] ? $value['haveInto'] : $goods_storage;
					$data['returnGoods'][$key]['maxNum'] = $value['haveInto'];
					$data['returnGoods'][$key]['rtNum'] = 0;
				}
			}
		}
// 		echo '<pre>';print_r($data);die;
		$this->load->view('scm/invOd/returnGoods',$data);
	}
	
	// 退货操作 处理
	public function return_goods(){
		$data = $this->input->get('data',TRUE);
		if(empty($data['detail'])) str_alert(-1,"退货数量不能为零");
		
		$this->db->trans_begin();
		
		$this->split_rtorder($data);
		
		if($this->db->trans_status() == FALSE){
			$this->db->trans_rollback();
			str_alert(-1,'退货失败');
		}else{
			$this->db->trans_commit();
			str_alert(200,'success');
		}
	}
	
	public function split_rtorder($data){
		$temp = [];
		
		foreach ($data['detail'] as $arr => $row){
			$saleModel = $this->mysql_model->get_row(GOODS,'(id = '.$row['invId'].')','saleModel');
			$key  = empty($saleModel) ? 'SELF' : $saleModel ;
			$n = array();
			$n['invId'] = $row['invId'];
			$n['rtNum'] = $row['rtNum'];
			$temp[$key][] = $n;
		}

		$sid = $this->jxcsys['sid'];
		$orderDetail = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$data['iid'].')');
		
		foreach ($temp as $key => $value){
			if(count($value)){
				$invCode = $this->mysql_model->get_results(GOODS,'id in ('.join(',', array_column($value, invId)).')','skuId,id');
				$invCode = array_bind_key($invCode, 'id');
				$price	 = $this->mysql_model->get_results(INVOICE_ORDER_INFO,'invId in ('.join(',', array_column($value, invId)).') and iid='.$data['iid'],'price,invId');
				$price 	 = array_bind_key($price, 'invId');
			}
			foreach ($value as $k => $v) {
				$detail[$k]['InvCode'] 	= $invCode[$v['invId']]['skuId'];
				$detail[$k]['Num'] 		= -abs($v['rtNum']);
				$detail[$k]['Price'] 	= $price[$v['invId']]['price'];
			}
			/* 退货 调NC 生成退货单 start */
			//接口参数
			$postData = array(
				"OrderDate"		=> date('Y-m-d',time()),
				"StationCode"	=> $sid,
				"SaleOrgCode"	=> $this->saleModel[$orderDetail['saleModel']],
				"OrderType"		=> $data['leibie'],
				"Detail"		=> $detail,
			);
			$post_data['data'] 		= json_encode($postData);
			$post_data['method'] 	= 'makeSoOrder';//接口方法
			$result = http_client($post_data,'NC');
			
			$returns = $result->return;
			if(empty($returns)) str_alert(-1,'调用NC接口失败');
			$return =  json_decode($returns,true);
			if($return['IsSuccess'] == 'false'){
				str_alert(-1,$return['ErrMsg']);
			}
			/* 退货 调NC 生成退货单 end */
			$info['nid'] 		= $orderDetail['nid'];
			$info['billNo'] 	= str_no('TH');
			$info['sid'] 		= $sid;
			$info['iid'] 		= $data['iid'];
			$info['leibie'] 	= $data['leibie'];
			$info['rttime'] 	= time();
			$info['status'] 	= '1';
			$info['rtOrderId'] 	= $return['SoCode'];
			
			$rtId = $this->mysql_model->insert(RETURN_GOODS,$info);
			
			foreach ($value as $key => $value) {
				$totalData = $this->mysql_model->get_row(ORDER_TOTAL,'(iid='.$data['iid'].') and invId='.$value['invId']);
				$rtNum['rtNum'] = $value['rtNum'] + $totalData['rtNum'];
				$this->mysql_model->update(ORDER_TOTAL,$rtNum,'(iid='.$data['iid'].') and invId='.$value['invId']);
				
				$updata[$key]['rtId'] 	= $rtId;
				$updata[$key]['iid'] 	= $data['iid'];
				$updata[$key]['invId'] 	= $value['invId'];
				$updata[$key]['rtNum'] 	= $value['rtNum'];
			}
			$this->mysql_model->insert(RETURN_GOODS_DETAIL,$updata);
			
		}
// 		die;
	}
	
	// 退货--查询--页 数据
	public function returnGoodsData(){
		$v = array();
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
			case 'underReview':
				$where .= ' and status = 2';
				break;
			case 'reviewPassed':
				$where .= ' and status = 4';
				break;
			case 'reviewUnPassed':
				$where .= ' and status = 3';
				break;
			default:
				break;
		}
		$where .= $skey ? ' and rtOrderId like "%'.$skey.'%"' :'';
		$where .= ' order by id desc';
//		echo '<pre>';print_r($where);die;
		$rtGoods = $this->mysql_model->get_results(RETURN_GOODS,$where);
	
		foreach ($rtGoods as $key => $value) {
			switch ($value['status']) {
				case '1':
					$rtGoods[$key]['status'] = '待审核';
					break;
				case '2':
					$rtGoods[$key]['status'] = '审核中';
					break;
				case '3':
					$rtGoods[$key]['status'] = '审核未通过';
					break;
				case '4':
					$rtGoods[$key]['status'] = '审核通过';
					break;
			}
	
			$rtGoods[$key]['rttime'] = dateTime($value['rttime']);
		}
	
		$data['data']['items'] = $rtGoods ;
		$data['data']['totalsize']  = $this->mysql_model->get_count(RETURN_GOODS,$where);
		die(json_encode($data));
	}
	
	
	//退货--详情--页
	public function returnGoodsOrder(){
		$this->common_model->checkpurview(58);
		$rtId = $this->input->get_post('id',TRUE);
		
		$where = 'rtId='.$rtId;
		$data['detail'] = $this->data_model->get_return_goods_detail($where,2);
		$status = $data['detail'][0]['status'];
		switch ($status) {
			case '1':
				$data['status'] = '待审核';
				break;
			case '2':
				$data['status'] = '审核中';
				break;
			case '3':
				$data['status'] = '审核未通过';
				break;
			case '4':
				$data['status'] = '审核通过';
				break;
		}
		$data['statusNum'] = $status;
		$data['rtId'] = $rtId;
		$data['rtOrderId'] = $data['detail'][0]['rtOrderId'];
		$ck = $this->mysql_model->get_results(STORAGE,'(sid = '.$this->jxcsys['sid'].') and isDelete = 0','id,name,isDefault');
		if (count($ck) == 1){
			$data['isOnle'] = 1;
			$data['depot'] = $ck[0];
		} else if(count($ck) == 0){
			$data['isOnle'] = 2;
		} else{
			$data['isOnle'] = 0;
			$data['depot'] = $ck;
		}
		$data['isRt'] = $this->mysql_model->get_row(RETURN_GOODS,'(id='.$rtId.')','isRt');
// 		echo '<pre>';print_r($data);die;
		$this->load->view('scm/invOd/returnGoodsOrder',$data);
	}
	
	public function area($qty,$invId,$locationName){
		$areaStorage = $this->data_model->get_area_info_inventory();
		$data = $areaStorage[$invId][$locationName];

		if(empty($data)){
			$list['storage'] = "0";
			$list['cur_num'] = 0;
			return $list;
		}
		$sum = array_sum(array_values($data));
		// 库存不足
		if($sum < $qty){
			$list['cur_num'] = $sum;
			return $list;
		}
		
		// 某一货位可以满足 退货量
		foreach ($data as $k => $v){
			if($v > $qty){
				$list[0]['areaId'] = $k;
				$list[0]['num'] = $qty;
				return $list;
			}
		}
		
		// 多货位 退货 
		asort($data);
		$total = $order = 0;
		foreach ($data as $k => $v){
			$total = $total + $v;
			if($total < $qty){
				$list[$order]['areaId'] = $k;
				$list[$order]['num'] = $v;
				$order = $order + 1;
			}else if($total == $qty){
				$list[$order]['areaId'] = $k;
				$list[$order]['num'] = $v;
				return $list;
			}else{
				$list[$order]['areaId'] = $k;
				$list[$order]['num'] = $v - ( $total - $v ) ;
				return $list;
			}
		}
	}
	
	public function returnForm(){
		$this->common_model->checkpurview(58);
		$data = $this->input->get_post('data',TRUE);
		$rtId = $this->input->get_post('rtId',TRUE);
		$this->db->trans_begin();
		
		$info['isRt'] = 1;
		$this->mysql_model->update(RETURN_GOODS,$info,'(id='.$rtId.')');
		
		$return_goods = $this->mysql_model->get_row(RETURN_GOODS,'(id="'.$rtId.'")');
		$return_detail = $this->mysql_model->get_results(RETURN_GOODS_DETAIL,'(rtId='.$return_goods['id'].')');
		$orderData = $this->mysql_model->get_row(INVOICE_ORDER,'(id='.$return_goods['iid'].')');

		$totalQty = $totalAmount = '';
		$order = 0;
		foreach ($return_detail as $k => $v){
			$area = $this->area($v['rtNum'], $v['invId'], $data[$k]['ck']);
			if($area['cur_num']){
				str_alert(-1,'该仓库库存不足，当前库存：'.$area['cur_num']);
			}
			foreach ($area as $key => $value){
				$orderData['entries'][$order] = $this->mysql_model->get_row(INVOICE_ORDER_INFO,'(iid='.$return_goods['iid'].') and invId='.$v['invId']);
				$orderData['entries'][$order]['qty'] = -abs($value['num']);
				$orderData['entries'][$order]['amount'] = -abs($value['num'] * $orderData['entries'][$k]['price']);
				$orderData['entries'][$order]['locationId'] = $data[$k]['ck'];
				$orderData['entries'][$order]['areaId'] = $value['areaId'];
				$totalQty += abs($v['rtNum']);
				$totalAmount += $orderData['entries'][$order]['amount'];
				$order = $order + 1;
			}
		}
		$orderData['billDate'] 	 	= date('Y-m-d',time());
		$orderData['modifyTime'] 	= date('Y-m-d H:i:s',time());
		$orderData['billNo'] 	 	= str_no('CG');
		$orderData['totalQty'] 	 	= $totalQty;
		$orderData['totalAmount']	= $totalAmount;
		$orderData['amount'] 		= $totalAmount;
		$orderData['arrears'] 		= $totalAmount;
		$orderData['nid'] 			= $params['nid'];
		$orderData['transType'] 	= 150502;
		$orderData['transTypeName'] = '退货';
		
		$info = elements(array('billNo','billType','transType','transTypeName','buId','billDate','description',
        'totalQty','amount','arrears','rpAmount','totalAmount','hxStateCode','totalArrears','disRate','disAmount',
        'uid','sid','userName','accId','modifyTime'),$orderData);
		$info['sourceType'] = 1;
		$iid = $this->mysql_model->insert(INVOICE,$info);
		$this->invoice_info_rf($iid,$orderData);
// 		$this->storage_info_rf($orderData);
		
		if($this->db->trans_status() == FALSE){
	    	$this->db->trans_rollback();
			str_alert(-1,'退货失败'); 
		}else{
			$this->db->trans_commit();
			str_alert(200,'success');
		}
	}
	
	//得到最近结算价
	public function get_settle_price(){
		$where = ' and billType="PUR"';
		$list = $this->data_model->get_settle_price($where);
		$settle_price = array_bind_key($list, "invId");
		str_alert(200,'success',$settle_price);
	}
	
	
	
	public function get_goodsInfo(){
		$invId = $this->input->get("invId",true);
		$where = ' and a.id = '.$invId;
		$goodsInfo = $this->data_model->get_goods($where, 1, $modelwhere);
		if (!empty($goodsInfo['purPrice3'])) {
			$goodsInfo['purPrice'] = $goodsInfo['purPrice3'];
		} else if (!empty($goodsInfo['purPrice2'])) {
			$goodsInfo['purPrice'] = $goodsInfo['purPrice2'];
		}
		str_alert(200,'success',$goodsInfo);
	}
	
	
	
	private function invoice_info_rf($iid,$data) {
		if (is_array($data['entries'])) {
			foreach ($data['entries'] as $arr=>$row) {
				$v[$arr]['iid']           = intval($iid);
				$v[$arr]['sid']     	  = $data['sid'];
				$v[$arr]['billNo']        = $data['billNo'];
				$v[$arr]['buId']          = $data['buId'];
				$v[$arr]['billDate']      = $data['billDate'];
				$v[$arr]['billType']      = $data['billType'];
				$v[$arr]['transType']     = $data['transType'];
				$v[$arr]['transTypeName'] = $data['transTypeName'];
				$v[$arr]['invId']         = $row['invId'];
				$v[$arr]['skuId']         = $row['skuId'];
				$v[$arr]['unitId']        = intval($row['unitId']);
				$v[$arr]['locationId']    = intval($row['locationId']);
				$v[$arr]['areaid']    	  = intval($row['locationareaId']);
				if ($data['transType']==150501) {
					$v[$arr]['qty']       = abs($row['qty']);
					$v[$arr]['amount']    = abs($row['amount']);
					$s[$arr]['num']       = abs($row['qty']);
				} else {
					$v[$arr]['qty']       = -abs($row['qty']);
					$v[$arr]['amount']    = -abs($row['amount']);
					$s[$arr]['num']       = -abs($row['qty']);
				}
				$v[$arr]['price']         = abs($row['price']);
				$v[$arr]['discountRate']  = $row['discountRate'];
				$v[$arr]['deduction']     = $row['deduction'];
				$v[$arr]['description']   = $row['description'];
				$s[$arr]['supplyId']      = $data['buId'];
				$s[$arr]['item_id']       = $data['invId'];
				$s[$arr]['skuId']      	  = $data['skuId'];
				$s[$arr]['storageId']     = $data['locationId'];
				$s[$arr]['modified_time'] = $cur_time;
			}
			if (isset($v)) {
				if (isset($data['id']) && $data['id']>0) {                    //修改的时候
					$this->mysql_model->delete(INVOICE_INFO,'(iid='.$iid.')');
				}
				$this->mysql_model->insert(INVOICE_INFO,$v);
			}
		}
	}
	
	/**
	 * 更新库存
	 * @param unknown $data
	 */
	private function storage_info_rf($data){
		$cur_time = time();
		
		if (!is_array($data['entries'])) return ;
		foreach ($data['entries'] as $arr=>$row) {
			
			$s[$arr]['num']           =  abs($row['qty']);
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
					if ($data['transType']==150501) { 
						$row['num']  = '+'.$row['num'];
					} else { 
						$row['num']  = '-'.$row['num']; 
					}
					$this->mysql_model->update(GOOODS_STORAGE.$this->jxcsys['sid'],' num = num'.$row['num'],$where);
				}
			}else{
				if ($data['transType']==150501) { 
					$row['num']  = '+'.$row['num'];
				} else { 
					$row['num']  = '-'.$row['num']; 
				}
				$this->mysql_model->update(GOOODS_STORAGE.$this->jxcsys['sid'],' num = num'.$row['num'],$where);
				
			}
			
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */ 