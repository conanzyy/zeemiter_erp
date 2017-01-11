<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Inventory extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
		$this->load->library('lib_pinyin');
		$this->load->library('lib_cn2pinyin');
		$this->jxcsys  = $this->session->userdata('jxcsys');
		$this->system  = $this->common_model->get_option('system');
    }
    
    public function index(){
    	$action = $this->input->get('action',TRUE);
    	switch ($action) {
    		case 'list':
    			$this->goodsList();
    			break;
    		case 'listAll':
    			$this->goodsListAll();
    			break;
    		case 'kzlist':
    			$this->kzGoodsList();
    			break;
    		default:
    			$this->goodsList();
    			 
    	}
    }


    public function brand(){
        $post_data['brand_sort'] = $_GET['brand_sort'];
        $post_data['brand'] = $_GET['brand'];
        $post_data['factory'] = $_GET['factory'];
        $post_data['cars'] = $_GET['cars'];
        $post_data['method'] = "modelsmatch.api.CarSearch";
        $result = http_client($post_data);
        $data = $result['result'];
        $data['pfilter'] = json_encode($_GET);
        $this->load->view('settings/brand',$data);
    }

    public function getCarList(){
        $post_data = $_POST;
        $post_data['method'] = "modelsmatch.api.CarList";
        $result = http_client($post_data);
        $data['cars'] = $result['result'];
        $data['pagedata'] = $_POST;
        $this->load->view('settings/carlistnew',$data);
    }

    public function goodsListAll(){
    	$this->goodsList('all');
    }



    public function getGoodsRow(){
    	$id = $this->input->get_post('goodsId');
    	$goodsInfo = $this->data_model->get_goods_info( 'and a.id = '.$id);
    	
    	//echo json_encode($goodsInfo);
    	if(!empty($goodsInfo['purPrice3'])){
    		$goodsInfo['purPrice'] = $goodsInfo['purPrice3'];
    	}else if(!empty($goodsInfo['purPrice2'])){
    		$goodsInfo['purPrice'] = $goodsInfo['purPrice2'];
    	}
    	
    	$data['status']  = 200;
    	$data['goodsInfo']   = $goodsInfo;
    	die(json_encode($data));
    }


    //关键字查找
	public function goodsList($type = '')
    {
        $v = array();
        
        $data['status'] = 200;
        $data['msg'] = 'success';
        $page = max(intval($this->input->get_post('page', TRUE)), 1);
        $rows = max(intval($this->input->get_post('rows', TRUE)), 10);
        $skey = str_enhtml($this->input->get_post('skey', TRUE));
        $bussType = $this->input->get_post('bussType', TRUE);
        $storage = $this->input->get_post('storage', TRUE);
        $categoryid = intval($this->input->get_post('assistId', TRUE));
        $barCode = intval($this->input->get_post('barCode', TRUE));
            
        $where = '';
        $where .=  $storage == 'true' ? ' and (b.totalqty > 0)' : '';
        $where .= $barCode ? ' and a.barCode = "' . $barCode . '"' : '';
        $where .= $type == 'all' ? ' and a.sid in ( ' . $this->jxcsys['sid'] . ',1)' : ($type == 'kz' ? ' and a.sid = 1 ' : ' and a.sid = ' . $this->jxcsys['sid']);
        
        //销售出库与采购的物料查询状态不一样
        if($bussType == 'order'){
        	$where .= " and a.skuStatus in (1,2)";
        }else{
        	$where .= " and a.skuStatus in (1,2,3)";
        }
        
        if (!empty($skey)) { //商品模糊查询
        	
        	$post_data['search_keywords'] = $skey;
        	$post_data['method'] = "app.user.item.search";
        	$post_data['page_size'] = 9999999;
        	$post_data['page_no'] = $page;
        	$result = http_client($post_data);
        	$count = $result['result']['data']['count'];
        	$result = $result['result']['data']['data'];
        	$items = array_bind_key($result, "item_id");
        	$item_id = array_column($result, "item_id");
        	
        	if(count($item_id)> 0 ){
        		$where .= ' and a.id in ( " '.join('","',$item_id).'")';
        	}else{
        		$where .= $skey ? ' and (a.name like "%'.$skey.'%" or a.number like "%'.$skey.'%" or a.spec like "%'.$skey.'%" or a.skuId like "%'.$skey.'%" )' : '';
        	}
        	
        }
        
        //商品分类
        if ($categoryid > 0) {
            $cid = array_column($this->mysql_model->get_results(CATEGORY, '(1=1) and find_in_set(' . $categoryid . ',path)'), 'id');
            if (count($cid) > 0) {
                $cid = join(',', $cid);
                $where .= ' and a.categoryid in(' . $cid . ')';
            }
        }
        
        //服务商类型
        $stype = $this->mysql_model->get_row(ADMIN, '(1=1) and sid=' . $this->jxcsys['sid'], 'service_type');
        if (!empty($stype)) {
            $bid = array_column($this->mysql_model->get_results(SERVICE_TYPE, '(1=1) and servicetypeId=' . $stype), 'brandId');
            if (count($bid) > 0) {
                $bid = join(',', $bid);
                $where .= ' and a.brand_id in(' . $bid . ')';
            }
        }
        
        $offset = $rows * ($page - 1);
        $bfb = floatval($this->system['settlePlaces'])/100;
        $data['data']['page'] = $page;                                    //当前页
        $data['data']['records'] = $this->data_model->get_goods($where, 3, $modelwhere);   //总条数
        $data['data']['total'] = ceil($data['data']['records'] / $rows);     //总分页数
        //$rows = 20;
        
        
        
        
        $list = $this->data_model->get_goods($where . ' order by a.id desc limit ' . $offset . ',' . $rows . '', 2, $modelwhere);
        
		//$storage = $this->data_model->get_goods_info_inventory();
		
        foreach ($list as $arr => $row) {
            if (!empty($row['purPrice3'])) {
                $row['purPrice'] = $row['purPrice3'];
            } else if (!empty($row['purPrice2'])) {
                $row['purPrice'] = $row['purPrice2'];
            }
            $items && $v[$arr]['image_url'] = $items[$row['id']]['image_default_id'];
            $v[$arr]['amount'] = (float)$row['iniamount'];
            $v[$arr]['barCode'] = $row['barCode'];
            $v[$arr]['categoryName'] = $row['categoryName'];
            $v[$arr]['currentQty'] = $row['totalqty'];                            //当前库存
            $v[$arr]['delete'] = intval($row['disable']) == 1 ? true : false;   //是否禁用
            $v[$arr]['discountRate'] = 0;
            $v[$arr]['retailPrice'] = empty($row['retailPrice']) ? '{}' : $row['retailPrice'];
            $v[$arr]['id'] = $row['id'];
            $v[$arr]['isSerNum'] = intval($row['isSerNum']);
            $v[$arr]['josl'] = $row['josl'];
            $v[$arr]['name'] = $row['brand_name'] . ' ' . $row['skuId'] . ' ' . $row['name'];
            $v[$arr]['number'] = $row['number'];
            $v[$arr]['pinYin'] = $this->lib_cn2pinyin->encode($row['name']);
            $v[$arr]['locationId'] = intval($row['locationId']);
            $v[$arr]['locationName'] = $row['locationName'];
            $v[$arr]['locationAreaId'] = intval($row['area_id']);;
            $v[$arr]['locationArea'] = $row['area_name'];
            $v[$arr]['locationNo'] = '';
            
            $index = strpos($row['title'],'适用');
            if($index > 0){
            	$v[$arr]['carModel'] = substr($row['title'], $index);//截取商城商品名称中的适用车型
            }
           
            $v[$arr]['purPrice'] = $row['purPrice'];
            $v[$arr]['lprice'] =  str_money($row['purPrice'] * $bfb);
            $v[$arr]['quantity'] = $row['iniqty'];
            $v[$arr]['storageSum'] = empty($row['totalqty']) ?  '0' : $row['totalqty'];;
            $v[$arr]['salePrice'] = $row['salePrice'];
            $v[$arr]['skuClassId'] = $row['skuClassId'];
            $v[$arr]['skuId'] = $row['skuId'];
            $v[$arr]['spec'] = $row['spec'];
            $v[$arr]['saleModel'] = $row['saleModel'];
            $v[$arr]['unitCost'] = $row['iniunitCost'];
            $v[$arr]['unitId'] = intval($row['unitId']);
            $v[$arr]['isSelf'] = $row['isSelf'];
            $v[$arr]['unitName'] = $row['unitName'];
            $v[$arr]['productCode'] = $row['productCode'];
            $v[$arr]['packSpec'] = $row['packSpec'];
            $v[$arr]['skuStatus'] = $row['skuStatus'];
            $v[$arr]['skuHot']  = $row['skuHot'];
            $v[$arr]['minNum'] = $row['minNum'];
            
        }
        
        $data['data']['rows'] = $v;
        die(json_encode($data));
    }
	
    /**
     * 车型查找
     */
    public function carSearch(){
        $v = array();
        $data['status'] = 200;
        $data['msg']    = 'success';
        $query = str_enhtml($this->input->get_post('query', TRUE));
        $compress_id = explode(",",$this->input->get_post('compress_id',TRUE));
        $categoryid = intval($this->input->get_post('assistId', TRUE));
        $page = max(intval($this->input->get_post('page',TRUE)),1);
        $rows = max(intval($this->input->get_post('rows',TRUE)),10);
        $storage = $this->input->get_post('storage', TRUE);
        $bussType = $this->input->get_post('bussType', TRUE);
        
        //销售出库与采购的物料查询状态不一样
        if($bussType == 'out' || $query == 'true'){
        	$where .= " and a.skuStatus in (1,2,3)";
        }else{
        	$where .= " and a.skuStatus in (1,2)";
        }
        
        $rels = $this->db->query('select * from '.MODELSMATCH_RELATIONSHIP_COMP.' where compress_id in("'.join('","',$compress_id).'")')->result_array();
        $models = $this->db->query('select * from '.MODELSMATCH_INFO_COMP.' where compress_id in("'.join('","',$compress_id).'") group by factory,brand,models,model_year,displacement')->result_array();
        
        if($models){
        	$info = $models[0];
        	$filterItems['keyword'] = $info['factory']." ".$info['brand']." ".$info['models']." ".$info['model_year']."款 ";
        	$filterItems['keyword'] .= $info['displacement'];
        	if($info['suction_type'] == '机械增压' || $info['suction_type'] == '涡轮增压' || $info['suction_type'] == '机械+涡轮增压' || $info['suction_type'] == '双涡轮增压'){
        		$filterItems['keyword'] .= "T";
        	}
        }else{
        	$filterItems['keyword'] = '未找到车型';
        }
        
        
        if($rels) {
        	$match_id = array_column($rels, "match_id");
            $modelwhere = '';
            $offset = $rows * ($page - 1);
            $where = '';
            $where = ' and match_id in("'.join('","',$match_id).'")';
            $where .=  $storage == 'true' ? ' and (b.totalqty > 0)' : '';
            if ($categoryid > 0) {
            	$cid = array_column($this->mysql_model->get_results(CATEGORY,'(1=1) and sid =1 and find_in_set('.$categoryid.',path)'),'id');
            	if (count($cid)>0) {
            		$cid = join(',',$cid);
            		$where .= ' and a.categoryid in('.$cid.')';
            	}
            }
            
            $data['data']['page'] = $page;                                    //当前页
            $data['data']['records'] = $this->data_model->get_goods($where, 3, $modelwhere);   //总条数
            $data['data']['total'] = ceil($data['data']['records'] / $rows);     //总分页数
            //$rows = 20;
            $bfb = floatval($this->system['settlePlaces'])/100;
            $list = $this->data_model->get_goods($where . ' order by a.id desc limit ' . $offset . ',' . $rows . '', 2, $modelwhere);
            if ($list) {
                foreach ($list as $arr => $row) {
                    if (!empty($row['purPrice3'])) {
                		$row['purPrice'] = $row['purPrice3'];
		            } else if (!empty($row['purPrice2'])) {
		                $row['purPrice'] = $row['purPrice2'];
		            }
		            $items && $v[$arr]['image_url'] = $items[$row['id']]['image_default_id'];
		            $v[$arr]['amount'] = (float)$row['iniamount'];
		            $v[$arr]['barCode'] = $row['barCode'];
		            $v[$arr]['categoryName'] = $row['categoryName'];
		            $v[$arr]['currentQty'] = $row['totalqty'];                            //当前库存
		            $v[$arr]['delete'] = intval($row['disable']) == 1 ? true : false;   //是否禁用
		            $v[$arr]['discountRate'] = 0;
		            $v[$arr]['retailPrice'] = empty($row['retailPrice']) ? '{}' : $row['retailPrice'];
		            $v[$arr]['id'] = $row['id'];
		            $v[$arr]['isSerNum'] = intval($row['isSerNum']);
		            $v[$arr]['josl'] = $row['josl'];
		            $v[$arr]['name'] = $row['brand_name'] . ' ' . $row['skuId'] . ' ' . $row['name'];
		            $v[$arr]['number'] = $row['number'];
		            $v[$arr]['pinYin'] = $this->lib_cn2pinyin->encode($row['name']);
		            $v[$arr]['locationId'] = intval($row['locationId']);
		            $v[$arr]['locationName'] = $row['locationName'];
		            $v[$arr]['locationAreaId'] = intval($row['area_id']);;
		            $v[$arr]['locationArea'] = $row['area_name'];
		            $v[$arr]['locationNo'] = '';
		            
		            $index = strpos($row['title'],'适用');
		            if($index > 0){
		            	$v[$arr]['carModel'] = substr($row['title'], $index);//截取商城商品名称中的适用车型
		            }
		           
		            $v[$arr]['purPrice'] = $row['purPrice'];
		            $v[$arr]['lprice'] =  str_money($row['purPrice'] * $bfb);
		            $v[$arr]['quantity'] = $row['iniqty'];
		            $v[$arr]['storageSum'] = empty($row['totalqty']) ?  '0' : $row['totalqty'];;
		            $v[$arr]['salePrice'] = $row['salePrice'];
		            $v[$arr]['skuClassId'] = $row['skuClassId'];
		            $v[$arr]['skuId'] = $row['skuId'];
		            $v[$arr]['spec'] = $row['spec'];
		            $v[$arr]['saleModel'] = $row['saleModel'];
		            $v[$arr]['unitCost'] = $row['iniunitCost'];
		            $v[$arr]['unitId'] = intval($row['unitId']);
		            $v[$arr]['isSelf'] = $row['isSelf'];
		            $v[$arr]['unitName'] = $row['unitName'];
		            $v[$arr]['productCode'] = $row['productCode'];
		            $v[$arr]['packSpec'] = $row['packSpec'];
		            $v[$arr]['skuStatus'] = $row['skuStatus'];
		            $v[$arr]['skuHot']  = $row['skuHot'];
		            $v[$arr]['minNum'] = $row['minNum'];
                }
            }
        }
        $data['data']['rows'] = $v;
        $data['data']['models'] = $filterItems['keyword'];
        die(json_encode($data));
    }

	
	//商品列表，查询合并后的商品表
	public function kzGoodsList(){

		$this->goodsList('kz');

		
		
		
		
		/* $v = array();
		$data['status'] = 200;
		$data['msg']    = 'success';
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),10);
		$skey = str_enhtml($this->input->get_post('skey',TRUE));
		$categoryid   = intval($this->input->get_post('assistId',TRUE));
		$barCode = intval($this->input->get_post('barCode',TRUE));
		$where = '';
		$where .= $skey ? ' and (a.name like "%'.$skey.'%" or a.number like "%'.$skey.'%" or a.spec like "%'.$skey.'%")' : '';
		$where .= $barCode ? ' and a.barCode = "'.$barCode.'"' : '';
		$where .= ' and a.sid = 1 ' ;
		
		$modelwhere = '';
		$mBrand = str_enhtml($this->input->get_post('mBrand',TRUE));
		$modelwhere .= $mBrand ?" and d.brand ='".$mBrand."'" : "";
		
		$cars = str_enhtml($this->input->get_post('cars',TRUE));
		$modelwhere .= $cars ?" and d.cars ='".$cars."'" : "";
		
		$models = str_enhtml($this->input->get_post('models',TRUE));
		$modelwhere .= $models ?" and d.models ='".$models."'" : "";
		
		$mYear = str_enhtml($this->input->get_post('mYear',TRUE));
		$modelwhere .= $mYear ?" and d.model_year ='".$mYear."'" : "";
		
		$displacement = str_enhtml($this->input->get_post('displacement',TRUE));
		$modelwhere .= $displacement ?" and d.displacement ='".$displacement."'" : "";
	
		if ($categoryid > 0) {
			$cid = array_column($this->mysql_model->get_results(CATEGORY,'(1=1) and sid =1 and find_in_set('.$categoryid.',path)'),'id');
			if (count($cid)>0) {
				$cid = join(',',$cid);
				$where .= ' and a.categoryid in('.$cid.')';
			}
		}
		$offset = $rows*($page-1);
		$data['data']['page']      = $page;                                                             //当前页
		$data['data']['records']   = $this->data_model->get_goods_kz2($modelwhere,$where,3);   //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);                              //总分页数
		$list = $this->data_model->get_goods_kz2($modelwhere,$where.' order by a.id desc limit '.$offset.','.$rows.'');
		foreach ($list as $arr=>$row) {
			$retailPrice = $this->mysql_model->get_row(RETAILPRICE,'(goodsId='.$row['id'].') and (isDelete=0)','retailPrice');
			$v[$arr]['amount']        = (float)$row['iniamount'];
			$v[$arr]['barCode']       = $row['barCode'];
			$v[$arr]['categoryName']  = $row['categoryName'];
			$v[$arr]['currentQty']    = $row['totalqty'];                            //当前库存
			$v[$arr]['delete']        = intval($row['disable'])==1 ? true : false;   //是否禁用
			$v[$arr]['discountRate']  = 0;
			$v[$arr]['retailPrice']   = empty($retailPrice) ? '{}' : $retailPrice;
			$v[$arr]['id']            = $row['id'];
			$v[$arr]['isSerNum']      = intval($row['isSerNum']);
			$v[$arr]['josl']     = $row['josl'];
			$v[$arr]['name']     = $row['name'];
			$v[$arr]['number']   = $row['number'];
			$v[$arr]['pinYin']   = $row['pinYin'];
			$v[$arr]['productCode']   = $row['match_id'];
			$v[$arr]['locationId']   = intval($row['locationId']);
			$v[$arr]['locationName'] = $row['locationName'];
			$v[$arr]['locationAreaId'] =intval($row['area_id']);;
			$v[$arr]['locationArea'] = $row['area_name'];
			$v[$arr]['locationNo'] = '';
			$v[$arr]['purPrice']   = $row['purPrice'];
			$v[$arr]['quantity']   = $row['iniqty'];
			$v[$arr]['salePrice']  = $row['salePrice'];
			$v[$arr]['skuClassId'] = $row['skuClassId'];
			$v[$arr]['skuId'] 	   = $row['skuId'];
			$v[$arr]['spec']       = $row['spec'];
			$v[$arr]['saleModel']  = $row['saleModel'];
			$v[$arr]['unitCost']   = $row['iniunitCost'];
			$v[$arr]['unitId']     = intval($row['unitId']);
			$v[$arr]['unitName']   = $row['unitName'];
		}
		$data['data']['rows']   = $v;
		die(json_encode($data)); */
		 
	}
	
	//快准商品列表
	public function kzGoodsList1(){
		$v = array();
		$data['status'] = 200;
		$data['msg']    = 'success';
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),10);
		$skey = str_enhtml($this->input->get_post('skey',TRUE));
		
		$categoryid   = intval($this->input->get_post('assistId',TRUE));
		$barCode = intval($this->input->get_post('barCode',TRUE));
		$where = '';
		$where .= $skey ? " and (a.title like '%".$skey."%' or a.bn like '%".$skey."%' or a.item_id like '%".$skey."%')" : "";
		

		$modelwhere = '';
		$mBrand = str_enhtml($this->input->get_post('mBrand',TRUE));
		$modelwhere .= $mBrand ?" and d.brand ='".$mBrand."'" : "";
		
		$cars = str_enhtml($this->input->get_post('cars',TRUE));
		$modelwhere .= $cars ?" and d.cars ='".$cars."'" : "";
		
		$models = str_enhtml($this->input->get_post('models',TRUE));
		$modelwhere .= $models ?" and d.models ='".$models."'" : "";
		
		$mYear = str_enhtml($this->input->get_post('mYear',TRUE));
		$modelwhere .= $mYear ?" and d.model_year ='".$mYear."'" : "";
		
		$displacement = str_enhtml($this->input->get_post('displacement',TRUE));
		$modelwhere .= $displacement ?" and d.displacement ='".$displacement."'" : "";

		if ($categoryid > 0) {
			$cid = array_column($this->mysql_model->get_results(SYSCATEGORY_CAT,'(1=1) and find_in_set('.$categoryid.',parent_id)'),'cat_id');
			if (count($cid)>0) {
				$cid = join(',',$cid);
				$where .= " and a.cat_id in(".$cid.")";
			}
		}

		$offset = $rows*($page-1);
		$data['data']['page']      = $page;                                                             //当前页
		$data['data']['records']   = $this->data_model->get_goods_kz($modelwhere,$where,3);   //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);                              //总分页数
		$list = $this->data_model->get_goods_kz($modelwhere,$where.' order by a.item_id desc limit '.$offset.','.$rows.'');
		foreach ($list as $arr=>$row) {
			$v[$arr]['amount']        = (float)$row['iniamount'];
			$v[$arr]['barCode']       = $row['barCode'];
			$v[$arr]['categoryName']  = $row['categoryName'];
			$v[$arr]['currentQty']    = $row['totalqty'];                            //当前库存
			$v[$arr]['delete']        = intval($row['disable'])==1 ? true : false;   //是否禁用
			$v[$arr]['discountRate']  = 0;
			$v[$arr]['id']            = $row['item_id'];
			$v[$arr]['isSerNum']      = intval($row['isSerNum']);
			$v[$arr]['josl']     = $row['josl'];
			$v[$arr]['name']     = $row['title'];//商品名称
			$v[$arr]['number']   = $row['bn'];//商品编号
			$v[$arr]['pinYin']   = $row['pinYin'];
			$v[$arr]['productCode']   = $row['match_id'];
			$v[$arr]['locationId']   = intval($row['locationId']);
			$v[$arr]['locationName'] = $row['locationName'];
			$v[$arr]['locationNo'] = '';
			$v[$arr]['purPrice']   = $row['purPrice'];
			$v[$arr]['quantity']   = $row['iniqty'];
			$v[$arr]['salePrice']  = $row['price'];//销售单价
			$v[$arr]['skuClassId'] = $row['skuClassId'];

			$v[$arr]['spec']       = $row['match_id'];//规格$row['params']
			$v[$arr]['unitId']     = intval($row['unitId']);
			$v[$arr]['isSelf']     = $row['isSelf'];
			$v[$arr]['unitName']   = $row['unitName'];
			$v[$arr]['productCode']= $row['productCode'];
			$v[$arr]['packSpec']   = $row['packSpec'];
			$v[$arr]['minNum']     = $row['minNum'];
		}
		$data['data']['rows']   = $v;
		die(json_encode($data));

	}
	
	//商品选择 
	public function listBySelected() { 
	    $v = array();
	    $contactid = intval($this->input->post('contactId',TRUE));
		$id = intval($this->input->post('ids',TRUE));
		$data['status'] = 200;
		$data['msg']    = 'success';
		$list = $this->mysql_model->get_results(GOODS,'(isDelete=0) and (disable=0) and id='.$id.''); 
		foreach ($list as $arr=>$row) {
		    $v[$arr]['amount']        = (float)$row['amount'];
			$v[$arr]['barCode']       = $row['barCode'];
			$v[$arr]['categoryName']  = $row['categoryName'];
			$v[$arr]['currentQty']    = 0;                                           //当前库存
			$v[$arr]['delete']        = intval($row['disable'])==1 ? true : false;   //是否禁用
			$v[$arr]['discountRate']  = 0;
			$v[$arr]['id']            = intval($row['id']);
			$v[$arr]['isSerNum']      = intval($row['isSerNum']);
			$v[$arr]['josl']     = '';
			$v[$arr]['name']     = $row['name'];
			$v[$arr]['number']   = $row['number'];
			$v[$arr]['pinYin']   = $row['pinYin'];
			$v[$arr]['locationId']   = intval($row['locationId']);
			$v[$arr]['locationName'] = $row['locationName'];
			$v[$arr]['locationNo'] = '';
			$v[$arr]['productCode']   = $row['match_id'];
			$v[$arr]['purPrice']   = $row['purPrice'];
			$v[$arr]['quantity']   = $row['quantity'];
			$v[$arr]['salePrice']  = $row['salePrice'];
			$v[$arr]['skuClassId'] = $row['skuClassId'];
			$v[$arr]['spec']       = $row['spec'];
			$v[$arr]['unitCost']   = $row['unitCost'];
			$v[$arr]['unitId']     = intval($row['unitId']);
			$v[$arr]['unitName']   = $row['unitName'];
		}
		$data['data']['result']   = $v;
		die(json_encode($data)); 
	}
	
	
	//获取信息
	public function query() {
	    $id = intval($this->input->post('id',TRUE));
		str_alert(200,'success',$this->get_goods_info($id)); 
	}
	
	
	//检测编号
	public function getNextNo() {
		$skey = str_enhtml($this->input->post('skey',TRUE));
		$this->mysql_model->get_count(GOODS,'(isDelete=0) and (number="'.$skey.'")') > 0 && str_alert(-1,'商品编号已经存在');
		str_alert(200,'success');
	}
	
	//检测条码 
	public function checkBarCode() {
		 $barCode = str_enhtml($this->input->post('barCode',TRUE));
		 $this->mysql_model->get_count(GOODS,'(isDelete=0) and (barCode="'.$barCode.'")') > 0 && str_alert(-1,'商品条码已经存在');
		 str_alert(200,'success');
	}
	
	//检测规格
	public function checkSpec() {
		 $spec = str_enhtml($this->input->post('spec',TRUE));
		 $this->mysql_model->get_count(ASSISTSKU,'(isDelete=0) and (skuName="'.$spec.'")') > 0 && str_alert(-1,'商品规格已经存在');
		 str_alert(200,'success');
	}
	
	//检测名称
	public function checkname() {
		 $skey = str_enhtml($this->input->post('barCode',TRUE));
		 echo '{"status":200,"msg":"success","data":{"number":""}}';
	}
	
	//获取图片信息
	public function getImagesById() {
	    $v  = array(); 
	    $id = intval($this->input->post('id',TRUE));
	    $list = $this->mysql_model->get_results(GOODS_IMG,'(invId='.$id.') and isDelete=0');
		foreach ($list as $arr=>$row) {
		    $v[$arr]['pid']          = $row['id'];
			$v[$arr]['status']       = 1;
			$v[$arr]['name']         = $row['name'];
			$v[$arr]['url']          = site_url().'/basedata/inventory/getImage?action=getImage&pid='.$row['id'];
			$v[$arr]['thumbnailUrl'] = site_url().'/basedata/inventory/getImage?action=getImage&pid='.$row['id'];
			$v[$arr]['deleteUrl']    = '';
			$v[$arr]['deleteType']   = '';
		}
		$data['status'] = 200;
		$data['msg']    = 'success';
		$data['files']  = $v;
		die(json_encode($data));  
	}
	
	//获取款准图片信息
	public function getImagesById_kz1() {
		$v  = array();
		$id = intval($this->input->post('id',TRUE));
		$list = $this->mysql_model->get_results(sysitem_item,'(item_id='.$id.')');
		foreach ($list as $arr=>$row) {
			$v[$arr]['pid']          = $row['item_id'];
			$v[$arr]['status']       = 1;
			$v[$arr]['name']         = $row['title'];
			$v[$arr]['url']          = $row['image_default_id'];
			$v[$arr]['thumbnailUrl'] = $row['list_image'];
			$v[$arr]['deleteUrl']    = '';
			$v[$arr]['deleteType']   = '';
		}
		$data['status'] = 200;
		$data['msg']    = 'success';
		$data['files']  = $v;
		die(json_encode($data));
	}
	
	//获取款准图片信息
	public function getImagesById_kz() {
		$v  = array(); 
	    $id = intval($this->input->post('id',TRUE));
	    $list = $this->mysql_model->get_results(GOODS_IMG,'(invId='.$id.') and isDelete=0');
		foreach ($list as $arr=>$row) {
		    $v[$arr]['pid']          = $row['id'];
			$v[$arr]['status']       = 1;
			$v[$arr]['name']         = $row['name'];
			$v[$arr]['url']          = $row['url'];
			$v[$arr]['thumbnailUrl'] = $row['thumbnailUrl'];
			$v[$arr]['deleteUrl']    = '';
			$v[$arr]['deleteType']   = '';
		}
		$data['status'] = 200;
		$data['msg']    = 'success';
		$data['files']  = $v;
		die(json_encode($data)); 
	}
	
	//上传图片信息
	public function uploadImages() {
	    require_once './application/libraries/UploadHandler.php';
		$config = array(
			'script_url' => base_url().'inventory/uploadimages',
			'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']).'/data/upfile/goods/',
			'upload_url' => base_url().'data/upfile/goods/',
			'delete_type' =>'',
			'print_response' =>false
		);
		$uploadHandler = new UploadHandler($config);
		$list  = (array)json_decode(json_encode($uploadHandler->response['files'][0]), true); 
		$newid = $this->mysql_model->insert(GOODS_IMG,$list);
		$files[0]['pid']          = intval($newid);
		$files[0]['status']       = 1;
		$files[0]['size']         = (float)$list['size'];
		$files[0]['name']         = $list['name'];
		$files[0]['url']          = site_url().'/basedata/inventory/getImage?action=getImage&pid='.$newid;
		$files[0]['thumbnailUrl'] = site_url().'/basedata/inventory/getImage?action=getImage&pid='.$newid;
		$files[0]['deleteUrl']    = '';
		$files[0]['deleteType']   = '';
		$data['status'] = 200;
		$data['msg']    = 'success';
		$data['files']  = $files;
        die(json_encode($data)); 
	}
	
	//保存上传图片信息
	public function addImagesToInv() {
	    $data = $this->input->post('postData');
		if (strlen($data)>0) {
		    $v = $s = array();
		    $data = (array)json_decode($data, true); 
			$id   = isset($data['id']) ? $data['id'] : 0;
		    !isset($data['files']) || count($data['files']) < 1 && str_alert(-1,'请先添加图片！'); 
			foreach($data['files'] as $arr=>$row) {
			    if ($row['status']==1) {
					$v[$arr]['id']       = $row['pid'];
					$v[$arr]['invId']    = $id;
				} else {
				    $s[$arr]['id']       = $row['pid'];
					$s[$arr]['invId']    = $id;
					$s[$arr]['isDelete'] = 1;
				}
			}
			$this->mysql_model->update(GOODS_IMG,array_values($v),'id');
			$this->mysql_model->update(GOODS_IMG,array_values($s),'id');
			str_alert(200,'success'); 
	    }
		str_alert(-1,'保存失败'); 
	}
	
	//获取图片信息
	public function getImage() {
	    $id = intval($this->input->get_post('pid',TRUE));
	    $data = $this->mysql_model->get_row(GOODS_IMG,'(id='.$id.')');
		if (count($data)>0) {
		    $url     = './data/upfile/goods/'.$data['name'];
			$info    = getimagesize($url);  
			$imgdata = fread(fopen($url,'rb'),filesize($url));   
			header('content-type:'.$info['mime'].'');  
			echo $imgdata;   
		}	 
	}
    //快准商品设置期初
	public function set(){
        $data = $this->input->post(NULL,TRUE);

    }

	//新增
	public function add(){
		$this->common_model->checkpurview(69);
		$data = $this->input->post(NULL,TRUE);
		if ($data) {
		    $v = '';
			$data = $this->validform($data);
			$this->mysql_model->get_count(GOODS,'(isDelete=0) and (number="'.$data['number'].'") and (sid = '.$this->jxcsys['sid'].')') > 0 && str_alert(-1,'商品编号重复');
			$this->db->trans_begin();
			$data['skuStatus'] = '1';
			$info = array(
			    'barCode','baseUnitId','unitName','categoryId','categoryName',
				'discountRate1','discountRate2','highQty','locationId','pinYin',
				'locationName','lowQty','name','number','purPrice',
				'remark','salePrice','spec','vipPrice','wholesalePrice','sid','skuStatus'
			);
			$info = elements($info, $data,NULL);
			$data['id'] = $invId = $this->mysql_model->insert(GOODS,$info);
			$lsj = array(
					'sid' => $data['sid'],
					'goodsId' => $data['id'],
					'retailPrice' => $data['salePriceKh'],
			);
			$this->mysql_model->insert(RETAILPRICE,$lsj);
			if (strlen($data['propertys'])>0) {                            
				$list = (array)json_decode($data['propertys'],true);
				foreach ($list as $arr=>$row) {
					$v[$arr]['invId']         = $invId;
					$v[$arr]['sid']			  = $this->jxcsys['sid']; 
					$v[$arr]['locationId']    = isset($row['locationId'])?$row['locationId']:0;
					$areaInfo = $this->mysql_model->get_row(STORAGE_AREA,'(1=1) and str_id='.$v[$arr]['locationId'].' and area_code="IN-00-01"');
					$v[$arr]['areaId']    	  = isset($areaInfo['id'])?$areaInfo['id']:0;
					$v[$arr]['qty']           = isset($row['quantity'])?$row['quantity']:0; 
					$v[$arr]['sid']           = $this->jxcsys['sid'];
					$v[$arr]['price']         = isset($row['unitCost'])?$row['unitCost']:0; 
					$v[$arr]['amount']        = isset($row['amount'])?$row['amount']:0; 
					$v[$arr]['skuId']         = isset($row['skuId'])?$row['skuId']:0; 
					$v[$arr]['billDate']      = date('Y-m-d');
					$v[$arr]['billNo']        = '期初数量';
					$v[$arr]['billType']      = 'INI';
					$v[$arr]['transTypeName'] = '期初数量';
				} 
				if (is_array($v)) {
					$this->mysql_model->insert(INVOICE_INFO,$v);
				}
			}
            if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				str_alert(-1,'SQL错误回滚');
			} else {
			    $this->db->trans_commit();
				$this->common_model->logs('新增商品:'.$data['name']);
				$this->common_model->logs('新增客户类别商品零售价:'.$data['salePriceKh']);
				str_alert(200,'success',$data);
			}
		}
		str_alert(-1,'添加失败');
	} 
	
	//修改
	public function update(){
		$this->common_model->checkpurview(70);
		$data = $this->input->post(NULL,TRUE);
		if ($data) {
			$id   = intval($data['id']);
			$data = $this->validform($data);
			$this->mysql_model->get_count(GOODS,'(isDelete=0) and (id<>'.$id.') and (number="'.$data['number'].'") and (sid = '.$this->jxcsys['sid'].')') > 0 && str_alert(-1,'商品编号重复');
			$this->db->trans_begin();
			$info = array(
			    'barCode','baseUnitId','unitName','categoryId','categoryName',
				'discountRate1','discountRate2','highQty','locationId','pinYin',
				'locationName','lowQty','name','number','purPrice',
				'remark','salePrice','spec','vipPrice','wholesalePrice'
			);
			$info = elements($info, $data,NULL);
			$this->mysql_model->update(GOODS,$info,'(id='.$id.')');
			
			$this->mysql_model->delete(RETAILPRICE,'(goodsId='.$id.')');
			$lsj = array(
					'sid' => $this->jxcsys['sid'],
					'goodsId' => $id,
					'retailPrice' => $data['salePriceKh'],
			);
			$this->mysql_model->insert(RETAILPRICE,$lsj);
			
			if (strlen($data['propertys'])>0) {  
			    $v = '';                          
				$list = (array)json_decode($data['propertys'],true);
				foreach ($list as $arr=>$row) {
					$v[$arr]['invId']         = $id;
					$v[$arr]['locationId']    = isset($row['locationId'])?$row['locationId']:0;
					$areaInfo = $this->mysql_model->get_row(STORAGE_AREA,'(1=1) and str_id='.$v[$arr]['locationId'].' and area_code="IN-00-01"');
					$v[$arr]['areaId']    	  = isset($areaInfo['id'])?$areaInfo['id']:0; 
					$v[$arr]['qty']           = isset($row['quantity'])?$row['quantity']:0; 
					$v[$arr]['price']         = isset($row['unitCost'])?$row['unitCost']:0; 
					$v[$arr]['sid']           = $this->jxcsys['sid'];
					$v[$arr]['amount']        = isset($row['amount'])?$row['amount']:0; 
					$v[$arr]['skuId']         = isset($row['skuId'])?$row['skuId']:0;  
					$v[$arr]['billDate']      = date('Y-m-d');
					$v[$arr]['billNo']        = '期初数量';
					$v[$arr]['billType']      = 'INI';
					$v[$arr]['transTypeName'] = '期初数量';
				} 
				$this->mysql_model->delete(INVOICE_INFO,'(invId='.$id.') and billType="INI"');
				if (is_array($v)) {
					$this->mysql_model->insert(INVOICE_INFO,$v);
				}
			}
            if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				str_alert(-1,'SQL错误回滚');
			} else {
			    $this->db->trans_commit();
				$this->common_model->logs('修改商品:ID='.$id.'名称:'.$data['name']);
				$this->common_model->logs('修改客户类别商品零售价:ID='.$id.'价格='.$data['salePriceKh']);
				str_alert(200,'success',$this->get_goods_info($id));
			}	 
		}
		str_alert(-1,'修改失败');
	} 
	
	//删除
	public function delete(){
		$this->common_model->checkpurview(71);
		$id = str_enhtml($this->input->post('id',TRUE));
		$data = $this->mysql_model->get_results(GOODS,'(id in('.$id.')) and (isDelete=0)'); 
		if (count($data) > 0) {
		    $info['isDelete'] = 1;
		    $this->mysql_model->get_count(INVOICE_INFO,'(isDelete=0) and (invId in('.$id.') and sid = '.$this->jxcsys['sid'].' and billType!="INI")')>0 && str_alert(-1,'其中有商品发生业务不可删除');
		    $sql  = $this->mysql_model->update(GOODS,$info,'(id in('.$id.'))');   
		    $retailPrice = $this->mysql_model->update(RETAILPRICE,$info,'(goodsId in('.$id.'))');
		    $info = $this->mysql_model->update(INVOICE_INFO,$info,'(isDelete=0) and (invId in('.$id.') and sid = '.$this->jxcsys['sid'].' and billType="INI")');
		    if ($sql && $retailPrice && $info) {
			    $name = array_column($data,'name'); 
				$this->common_model->logs('删除商品:ID='.$id.' 名称:'.join(',',$name));
				$this->common_model->logs('删除客户类别商品零售价:ID='.$id);
				$id_array = explode(',',$id);
				str_alert(200,'success',array('msg'=>'','id'=>$id_array));
			}
			str_alert(-1,'删除失败');
		}
	}
	
    //导出
	public function exporter() {
	    $this->common_model->checkpurview(72);
		$name = 'goods_'.date('YmdHis').'.xls';
		sys_csv($name);
		$this->common_model->logs('导出商品:'.$name);
		$skey = str_enhtml($this->input->get_post('skey',TRUE));
		$categoryid   = intval($this->input->get_post('assistId',TRUE));
		$barCode      = intval($this->input->get_post('barCode',TRUE));
		$where = '';
		$where .= $skey ? 	 ' and (name like "%'.$skey.'%" or number like "%'.$skey.'%" or spec like "%'.$skey.'%")' : '';
		$where .= $barCode ? ' and barCode="'.$barCode.'"' : '';
		$where .= 			 ' and sid = ' . $this->jxcsys['sid'];
		if ($categoryid > 0) {
		    $cid = array_column($this->mysql_model->get_results(CATEGORY,'(1=1) and find_in_set('.$categoryid.',path)'),'id'); 
			if (count($cid)>0) {
			    $cid = join(',',$cid);
			    $where .= ' and categoryid in('.$cid.')';
			} 
		}
		
		$data['ini']  = $this->data_model->get_invoice_info('and billType="INI"');
		$data['list'] = $this->data_model->get_goods($where.' order by id desc');  
        $this->load->view('settings/goods-export',$data);
		  
	}
	
	//状态
	public function disable(){
		$this->common_model->checkpurview(72);
		$disable = intval($this->input->post('disable',TRUE));
		$skuDisable = intval($this->input->post('skuDisable',TRUE));
		$id = str_enhtml($this->input->post('invIds',TRUE));
		if (strlen($id) > 0) { 
			$info['disable'] = $disable;
			$sql = $this->mysql_model->update(GOODS,$info,'(id in('.$id.'))');
		    if ($sql) {
				$this->common_model->logs('商品'.$disable==1?'禁用':'启用'.':ID:'.$id.'');
				str_alert(200,'success');
			}
		}
		str_alert(-1,'操作失败');
	}
	
	//库存预警 
	public function listinventoryqtywarning() {
	    $v = array();
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),100);
		$where = ''; 
		$where .= ' and a.sid = ' . $this->jxcsys['sid'];
		$data['data']['total']     = 1;                         
		$data['data']['records']   = $this->data_model->get_inventory($where.' GROUP BY invId HAVING qty>highQty or qty<lowQty',3);    
		$list = $this->data_model->get_inventory($where.' GROUP BY invId HAVING qty>highQty or qty<lowQty');    
		foreach ($list as $arr=>$row) {
		    $qty1 = (float)$row['qty'] - (float)$row['highQty'];
			$qty2 = (float)$row['qty'] - (float)$row['lowQty'];
			$v[$arr]['highQty']       = (float)$row['highQty'];
			$v[$arr]['id']            = $row['invId'];
			$v[$arr]['lowQty']        = (float)$row['lowQty'];
			$v[$arr]['name']          = $row['invName'];
			$v[$arr]['number']        = $row['invNumber'];
			$v[$arr]['warning']       = $qty1 > 0 ? $qty1 : $qty2;
			$v[$arr]['qty']           = (float)$row['qty'];
			$v[$arr]['unitName']      = $row['unitName'];
			$v[$arr]['spec']          = $row['invSpec'];
		}
		$data['data']['rows']  = $v;
		die(json_encode($data));
	} 
	
	//通过ID 获取商品信息
	private function get_goods_info($id) {
	    $data = $this->mysql_model->get_row(GOODS,'(id='.$id.') and (isDelete=0)');
	    $retailPrice = $this->mysql_model->get_row(RETAILPRICE,'(goodsId='.$id.') and (isDelete=0) and sid = '.$this->jxcsys['sid'],'retailPrice');
		if (count($data)>0) {
			$v = array();
			$data['id']            = intval($id); 
			$data['count']         = 0;
			$data['unitTypeId']    = intval($data['unitTypeId']);
			$data['baseUnitId']    = intval($data['baseUnitId']);
			$data['categoryId']    = intval($data['categoryId']);
			$data['retailPrice']   = empty($retailPrice) ? '{}' : $retailPrice;
			$data['salePrice']     = (float)$data['salePrice'];
			$data['vipPrice']      = (float)$data['vipPrice'];
			$data['purPrice']      = (float)$data['purPrice'];
			$data['wholesalePrice']= (float)$data['wholesalePrice'];
			$data['discountRate1'] = (float)$data['discountRate1'];
			$data['discountRate2'] = (float)$data['discountRate2'];
			$data['remark']        = $data['remark'];
			$data['locationId']    = intval($data['locationId']);
			$data['baseUnitId']    = intval($data['baseUnitId']);
			$data['unitTypeId']    = intval($data['unitTypeId']);
			$data['unitId']        = intval($data['unitId']);
			$data['highQty']       = (float)$data['highQty'];
			$data['lowQty']        = (float)$data['lowQty'];
			$data['property']      = $data['property'] ? $data['property'] : NULL;
			$data['quantity']      = (float)$data['quantity'];
			$data['isWarranty']    = (float)$data['isWarranty'];
			$data['advanceDay']    = (float)$data['advanceDay'];
			$data['unitCost']      = (float)$data['unitCost'];
			$data['isSerNum']      = (float)$data['isSerNum'];
			$data['amount']        = (float)$data['amount'];
			$data['quantity']      = (float)$data['quantity'];
			$data['unitCost']      = (float)$data['unitCost'];
			$data['delete']        = intval($data['disable'])==1 ? true : false;   //是否禁用
			$propertys = $this->data_model->get_invoice_info('and (a.invId='.$id.') and a.billType="INI"'); 
			foreach ($propertys as $arr=>$row) { 
				$v[$arr]['id']            = intval($row['id']);
				$v[$arr]['locationId']    = intval($row['locationId']);
				$v[$arr]['inventoryId']   = $row['invId'];
				$v[$arr]['locationName']  = $row['locationName'];
				$v[$arr]['quantity']      = (float)$row['qty'];
				$v[$arr]['unitCost']      = (float)$row['price'];
				$v[$arr]['amount']        = (float)$row['amount'];
				$v[$arr]['skuId']         = intval($row['skuId']);
				$v[$arr]['skuName']       = '';
				$v[$arr]['date']          = $row['billDate'];
				$v[$arr]['tempId']        = 0;
				$v[$arr]['batch']         = '';
				$v[$arr]['invSerNumList'] = '';
			} 
			$data['propertys']            = $v;
		}
		return $data;
	}
	
	
	//公共验证
	private function validform($data) {
	    $this->load->library('lib_cn2pinyin');
	    strlen($data['name']) < 1 && str_alert(-1,'商品名称不能为空');
		strlen($data['number']) < 1 && str_alert(-1,'商品编号不能为空');
		intval($data['categoryId']) < 1 && str_alert(-1,'商品类别不能为空');
        intval($data['locationId']) < 1 && str_alert(-1,'首选仓库不能为空');
		intval($data['baseUnitId']) < 1 && str_alert(-1,'计量单位不能为空');
		$data['lowQty']    = (float)$data['lowQty'];
		$data['purPrice']  = (float)$data['purPrice'];
		$data['salePrice'] = (float)$data['salePrice'];
		$data['vipPrice']  = (float)$data['vipPrice'];
		$data['discountRate1']  = (float)$data['discountRate1'];
		$data['discountRate2']  = (float)$data['discountRate2'];
		$data['wholesalePrice'] = (float)$data['wholesalePrice'];
		$data['barCode']  = $data['barCode'] ? $data['barCode'] :NULL;
		$data['remark']   = $data['remark'] ? $data['remark'] :NULL;
		$data['spec']     = $data['spec'] ? $data['spec'] :NULL;
		$data['sid']      = $this->jxcsys['sid'];
		
		
		$data['unitName']     = $this->mysql_model->get_row(UNIT,'(id='.$data['baseUnitId'].')','name');
		$data['categoryName'] = $this->mysql_model->get_row(CATEGORY,'(id='.$data['categoryId'].')','name');
		$data['pinYin'] = $this->lib_cn2pinyin->encode($data['name']); 
		!$data['categoryName'] && str_alert(-1,'商品类别不存在');
	    if (strlen($data['propertys'])>0) {                            
			$list         = (array)json_decode($data['propertys'],true);
			$storage      = $this->mysql_model->get_results(STORAGE,'(disable=0)');  
			$locationId   =  array_column($storage,'id'); 
			$locationName =  array_column($storage,'name','id');
			foreach ($list as $arr=>$row) {
				!in_array($row['locationId'],$locationId) && str_alert(-1,@$locationName[$row['locationId']].'仓库不存在或不可用！'); 
			} 
		}
		return $data;
	}  
	
	
	
	public function get_category (){
		$data = $this->mysql_model->get_results(CATEGORY,'(typeNumber="customertype" and isDelete=0)','id , name');	
		if($data){
			str_alert(200,'success',$data);			
		}else{
			str_alert(-1);
		}
	}
	
	public function updata_price(){
		$data = $this->input->post(NULL,TRUE);
		$id   = intval($data['id']);
		if($data){
			$this->mysql_model->delete(RETAILPRICE,'(goodsId='.$id.')');
			$price_data = array(
					'sid' => $this->jxcsys['sid'],
					'goodsId' => $id,
					'retailPrice' => $data['retailPrice'],
			);
			$sql = $this->mysql_model->insert(RETAILPRICE,$price_data);
			
			if($sql){
				str_alert(200,'success');
			}
			
			str_alert(-1,'配置价格失败232');
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */