<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Invlocation extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
		$this->load->library('excel/excel');
		$this->load->helper('download');
		$this->jxcsys  = $this->session->userdata('jxcsys');
    }

    //仓库列表
	public function index(){
		$type = str_enhtml($this->input->get_post('type',TRUE));
		if(empty($type)){
			$list = array();
			$datalever = $this->mysql_model->get_row(ADMIN,'(roleid = 1) and uid = '.$this->jxcsys['uid'],'datalever');
			if(!empty($datalever)){
				$lever = explode(';', $datalever);
				foreach ($lever as $k => $v){
					$lever_rel = explode(',', $v);
					$surname = $this->mysql_model->get_row(MENU_DATA,'(1=1) and id='.$lever_rel[0],'surname');
					if($surname == 'ZCKKJ'){
						$a = 0;
						for ($x = 1;$x < count($lever_rel);$x++){
							$depot = $this->mysql_model->get_row(STORAGE,'(isDelete=0) and id ='.$lever_rel[$x]);
							$list[$a] = $depot;
							$a++;
						}
					}
				}
			}else {
				$list = $this->mysql_model->get_results(STORAGE,'(isDelete=0) and sid = '.$this->jxcsys['sid'].' order by id');
			}
		}else{
			$list = $this->mysql_model->get_results(STORAGE,'(isDelete=0) and sid = '.$this->jxcsys['sid'].' order by id');
		}
		
		$v = array();
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		//$default_id = $this->mysql_model->get_row(ADMIN,'(1=1) and uid = '.$this->jxcsys['uid'],'default_storageId');
		
		foreach ($list as $arr=>$row) {
		    $v[$arr]['address']     = $row['address'];
		    $v[$arr]['isDefault']   = $row['isDefault'];
		  //  $v[$arr]['isDefault']   = $default_id == 0 ? $row['isDefault'] : $row['id'] == $default_id ? 1 : 0;
			$v[$arr]['delete']      = $row['disable'] > 0 ? true : false;
			$v[$arr]['allowNeg']    = false;
			$v[$arr]['deptId']      = intval($row['deptId']);
			$v[$arr]['empId']       = intval($row['empId']);
			$v[$arr]['groupx']      = $row['groupx'];
			$v[$arr]['id']          = intval($row['id']);
			//加载仓库对应区域
			$arealist = $this->mysql_model->get_results(STORAGE_AREA,'(1=1) and str_id = '.intval($row['id']).' order by id,sort',"id,area_name as name,str_id,sort");
			
			$v[$arr]['area1']		= implode(",",array_column($arealist, 'name'));
			$v[$arr]['locationNo']  = $row['locationNo'];
			$v[$arr]['name']        = $row['name'];
			$v[$arr]['phone']       = $row['phone'];
			$v[$arr]['type']        = intval($row['type']);
			//加载仓库对应区域
			$v[$arr]['arealist']  = $arealist;          
		}
		$data['data']['rows']       = $v;
		$data['data']['total']      = 1;
		$data['data']['records']    = $this->mysql_model->get_count(STORAGE,'(isDelete=0)');
		$data['data']['page']       = 1;
		die(json_encode($data));
	}
	
	
	
	//新增
	public function add(){
		$this->common_model->checkpurview(156);
		$data = str_enhtml($this->input->post(NULL,TRUE));
		$isSuccess = '';
		if (count($data)>0) {
			$data = $this->validform($data);
			$areas = $data['inputData'];
			$data['isArea'] = '0';
			if($data['inputData']){
				$data['isArea'] = '1';
			}
			
			$this->db->trans_begin();
			
			$count = $this->mysql_model->get_count(STORAGE,"isDelete = 0 and (sid=".$this->jxcsys['sid'].") ");
			if($count == 0){
				$data['isDefault'] = 1;
			}//如果创建的第一个仓库默认设置为默认仓库
			
			if($data['isDefault'] == 1){
				$this->mysql_model->update(STORAGE,array('isDefault'=>0),'(sid='.$this->jxcsys['sid'].')');
			}
			
			$sql = $this->mysql_model->insert(STORAGE,elements(array('name','locationNo','sid','isArea','address','isDefault'),$data));
			
			if(!$sql){
				str_alert(-1,'添加仓库失败！');
				$this->db->trans_rollback();
			} 
			
			$iareas = array(); //区域集合;
			$defarea = array( "str_id"=>$sql, "sid" => $this->jxcsys['sid'], 'area_code' => 'IN-00-01', 'area_name' => '默认');
			$iareas[] = $defarea;
			for($i = 0; $areas && $i < count($areas) ; $i++){
				$iareas[] = array(
						'str_id' => $sql,
						'sid' => $this->jxcsys['sid'],
						'area_code' => 'IN-00-01',
						'area_name' => $areas[$i]['name'],
						'sort' => $i + 1
				);
			}
			
			foreach ($iareas as $vv){
				$aid = $this->mysql_model->insert(STORAGE_AREA,$vv);
			}
			//$this->mysql_model->insert(STORAGE_AREA,$iareas);
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				str_alert(-1,'添加仓库失败！');
			} else {
				
				$extlist = $this->mysql_model->get_results(STORAGE_AREA,'(1=1) and str_id = '.$sql.' and sort > 0 order by sort',"id,str_id,area_name as name,sort");
				$area1 = implode(",",array_column($extlist, 'name')); 
				$data['area1'] = $area1;
				$data['arealist']= $extlist;
				$data['id'] = $sql;
				$this->db->trans_commit();
				$this->common_model->logs('新增仓库:'.$data['name']);
				$this->common_model->logs('新增仓库区域:'.$area1);
				str_alert(200,'success',$data);
			}
		}
		//str_alert(-1,'添加失败');
	}
	
	
	//新增
	public function add_area(){
		$this->common_model->checkpurview(156);
		$data = str_enhtml($this->input->post(NULL,TRUE));
		$isSuccess = '';
		if (count($data)>0) {
			$data = $this->validform_area($data);
			$this->db->trans_begin();
			
			$sql = $this->mysql_model->insert(STORAGE_AREA,elements(array('sid',"str_id",'area_code','area_name','area_desc'),$data));
				
			if(!$sql){
				str_alert(-1,'添加货位失败！');
				$this->db->trans_rollback();
			}
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				str_alert(-1,'添加货位失败！');
			} else {
				$data['id'] = $sql;
				$this->db->trans_commit();
				$this->common_model->logs('新增货位:'.$data['name']);
				str_alert(200,'success',$data);
			}
		}
	}
	
	
	//修改
	public function update_area(){
		$this->common_model->checkpurview(157);
		$data = str_enhtml($this->input->post(NULL,TRUE));
		$isSuccess = '';
	
		if (count($data)>0) {
			$id   = intval($data['id']);
			$data = $this->validform_area($data);
				
			$sql = $this->mysql_model->update(STORAGE_AREA,elements(array('area_code','area_name','area_desc','str_id'),$data),'(id='.$id.')');
				
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				str_alert(-1,'修改仓库失败！');
			} else {
				$this->db->trans_commit();
				$this->common_model->logs('修改货位:'.$data['name']);
				str_alert(200,'success',$data);
			}
		}
		str_alert(-1,'更新失败');
	}
	
	//删除
	public function delete_area(){
		$this->common_model->checkpurview(158);
		$id   = intval($this->input->post('locationId',TRUE));
		$data = $this->mysql_model->get_row(STORAGE_AREA,'(id='.$id.') and (isDelete=0)');
		if (count($data) > 0) {
			$info['isDelete'] = 1;
			$this->mysql_model->get_count(INVOICE_INFO,'(areaId='.$id.')')>0 && str_alert(-1,'不能删除有业务关联的货位！');
			$sql = $this->mysql_model->update(STORAGE_AREA,$info,'(id='.$id.')');
			if ($sql) {
				$this->common_model->logs('删除货位:ID='.$id.' 名称:'.$data['name']);
				str_alert(200,'success');
			}
		}
		str_alert(-1,'删除失败');
	}
	
	
	
	
	//修改
	public function update(){
		$this->common_model->checkpurview(157);
		$data = str_enhtml($this->input->post(NULL,TRUE));
		$isSuccess = '';
		
		if (count($data)>0) {
			$id   = intval($data['locationId']); 
			$data = $this->validform($data);
			$rs_areas = $areas = $data['inputData'];
			
			$data['isArea'] = '0';
			if($data['inputData']){
				$data['isArea'] = '1';
			}
			$this->db->trans_begin();
			
			if($data['isDefault'] == 1){
				$this->mysql_model->update(STORAGE,array('isDefault'=>0),'(sid='.$this->jxcsys['sid'].')');
			}
			
			$sql = $this->mysql_model->update(STORAGE,elements(array('name','locationNo','isArea','address','isDefault'),$data),'(id='.$id.')');
			
			$extlist = $this->mysql_model->get_results(STORAGE_AREA,'(1=1) and str_id = '.$id.' and sort > 0 order by sort',"id");
			$extIds = array_column($extlist, "id");
			
			for($i = 0; $areas && $i < count($areas) ; $i++){
				$nid = $areas[$i]['id'];
				$areas[$i]['area_name'] = $areas[$i]['name'];
				$areas[$i]['str_id'] = $id;
				$areas[$i]['sort'] = $i+1;
				
				unset($areas[$i]['id']);
				unset($areas[$i]['name']);
				
				if(empty($nid)){
					$this->mysql_model->insert(STORAGE_AREA,$areas[$i]);
				}else if(in_array($nid, $extIds)){
					$this->mysql_model->update(STORAGE_AREA,$areas[$i],'(id='.$nid.')');
				}
			}
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				str_alert(-1,'修改仓库失败！');
			} else {
				$extlist = $this->mysql_model->get_results(STORAGE_AREA,'(1=1) and str_id = '.$id.' and sort > 0 order by sort',"id,str_id,area_name as name,sort");
				$area1 = implode(",",array_column($rs_areas, 'name'));
				$data['area1'] = $area1;
				$data['arealist']= $extlist;
				$data['id'] = $id;
				$this->db->trans_commit();
				$this->common_model->logs('修改仓库:'.$data['name']);
				$this->common_model->logs('修改仓库区域:'.$area1);
				str_alert(200,'success',$data);
			}
		}
		str_alert(-1,'更新失败');
	}
	
	
	
	
	
	//删除
	public function delete(){
		$this->common_model->checkpurview(158);
		$id   = intval($this->input->post('locationId',TRUE));
		$data = $this->mysql_model->get_row(STORAGE,'(id='.$id.') and (isDelete=0)'); 
		if (count($data) > 0) {
		    $info['isDelete'] = 1;
		    $this->mysql_model->get_count(INVOICE_INFO,'(locationId='.$id.')')>0 && str_alert(-1,'不能删除有业务关联的仓库！');
		    $sql = $this->mysql_model->update(STORAGE,$info,'(id='.$id.')');   
		    if ($sql) {
				$this->common_model->logs('删除仓库:ID='.$id.' 名称:'.$data['name']);
				str_alert(200,'success');
			}
		}
		str_alert(-1,'删除失败');
	}
	
	//判断仓库区域是否有相关业务
	public function area_check(){
		$this->common_model->checkpurview(158);
		$id   = intval($this->input->post('locationId',TRUE));
		$area_id   = intval($this->input->post('area_id',TRUE));
		$data = $this->mysql_model->get_row(STORAGE,'(id='.$id.') and (isDelete=0) and (isArea=1)'); 
		if (count($data) > 0) {
			$info['isDelete'] = 1;
			$this->mysql_model->get_count(INVOICE_INFO,'(locationId='.$id.')and(areaid = '.$area_id.')')>0 && str_alert(-1,'不能删除有业务关联的仓库！');
		};
		$this->mysql_model->delete(STORAGE_AREA,'(id='.$area_id.')');
		
		$extlist = $this->mysql_model->get_results(STORAGE_AREA,'(1=1) and str_id = '.$id.' and sort > 0 order by sort',"id,str_id,area_name as name,sort");
		$area1 = implode(",",array_column($extlist, 'name'));
		$data['area1'] = $area1;
		str_alert(200,'success',$data);
	}
	
	//启用禁用
	public function disable(){
		$this->common_model->checkpurview(158);
		$id = intval($this->input->post('locationId',TRUE));
		$data = $this->mysql_model->get_row(STORAGE,'(id='.$id.') and (isDelete=0)'); 
		if (count($data) > 0) {
			$info['disable'] = intval($this->input->post('disable',TRUE));
			$sql = $this->mysql_model->update(STORAGE,$info,'(id='.$id.')');
		    if ($sql) {
			    $actton = $info['disable']==0 ? '仓库启用' : '仓库禁用';
				$this->common_model->logs($actton.':ID='.$id.' 名称:'.$data['name']);
				str_alert(200,'success');
			}
		}
		str_alert(-1,'操作失败');
	}
	
	public function setDefault(){
		$this->common_model->checkpurview(158);
		$id = intval($this->input->post('locationId',TRUE));
		$data = $this->mysql_model->get_row(STORAGE,'(id='.$id.') and (isDelete=0)'); 
		$roleid = $this->mysql_model->get_row(ADMIN,'(uid = '.$this->jxcsys['uid'].')','roleid');
		if (count($data) > 0) {
			if($roleid == 0){
				$info['isDefault'] = 0;
				$sql = $this->mysql_model->update(STORAGE,$info,'(sid='.$this->jxcsys['sid'].')');
				$info['isDefault'] = 1;
				$sql = $this->mysql_model->update(STORAGE,$info,'(id='.$id.')');
			}else if ($roleid == 1){
				$default['default_storageId'] = $id;
				$ret = $this->mysql_model->update(ADMIN,$default,'(uid = '.$this->jxcsys['uid'].')');
			}
		    if ($sql || $ret) {
				$this->common_model->logs("设置默认仓库：".':ID='.$id.' 名称:'.$data['name']);
				str_alert(200,'success',$data);
			}
		}
		str_alert(-1,'操作失败');
	}
	
	
	
	public function areaImport(){
		$this->load->view('settings/areaImport');
	}
	
	public function goodsInitImport(){
		$this->load->view('settings/goodsInitImport');
	}

    public function goodsPriceImport(){
        $data['data'] = $this->db->query('select * from '.CATEGORY.' where sid = '.$this->jxcsys['sid'].' and status = 1 and isDelete = 0 and typeNumber = "customertype"')->result_array();
        $this->load->view('settings/goodsPriceImport',$data);
    }

    public function customerImport(){
        $data['data'] = $this->db->query('select * from '.CATEGORY.' where sid = '.$this->jxcsys['sid'].' and status = 1 and isDelete = 0 and typeNumber = "customertype"')->result_array();
        $this->load->view('settings/customerImport',$data);
    }
	
	public function storageRelImport(){
		$this->load->view('settings/storageRelImport');
	}
	
	
	
	
	
	
	
	
	/**
	 * 上传商品与货位关系检验
	 */
	public function saveImportStorageRel(){
		
		$storageId = $this->input->post("storageId",true);
		$execl = $this->parseExcel('resume_file');

		$datalist = $execl['data'];
		$header = $execl['header'];
		//查询出商品集合
		$goods = $this->mysql_model->get_results(GOODS,'(isDelete=0) and sid=1');
		//由于SKUID可能重复将所有SKUID遍历出放来放到一个数据 。
		$ex_goods = array_column($goods, "skuId");
		$goodsInfo = array_bind_key($goods, "skuId");
		//查询出货位集合
		$areas = $this->mysql_model->get_results(STORAGE_AREA,'str_id = '.$storageId.' and sid='.$this->jxcsys['sid']);
		
		$ex_areas = array_column($areas, "area_code");
		$areasInfo = array_bind_key($areas, "area_code");
		
		
		//检验导入EXEC表，生成EXEC输出。
		$error = array();$success = array();
		foreach ($datalist as $key=>$value){
			
			if(empty($value['A']) || empty($value['B']) ){
				$value['C'] = "物料编码或货位编码不能为空";
				$error[] = $value;
				continue;
			}
			if(!in_array($value['A'], $ex_goods)){
				$value['C'] = "物料编码".$value['A']."在店管家中不存在";
				$error[] = $value;
				continue;
			}
			if(!in_array($value['B'], $ex_areas)){
				$value['C'] = "货位".$value['B']."不存在";
				$error[] = $value;
				continue;
			}
			
			$success[] = $value;
		}
		//生成错误的EXECL
		if(count($error)){
			$filter_name = $this->importError($error,$header);
		}
		
		foreach ($success as $key => $value){
			$insert[$key]['sid'] = $this->jxcsys['sid'];
			$insert[$key]['item_id'] = $goodsInfo[$value['A']]['id'];
			$insert[$key]['storage_id'] = $storageId;
			$insert[$key]['area_id'] = $areasInfo[$value['B']]['id'];
		}
		
		
		$pagedata['status'] = "success";
		$pagedata['message'] = "导入完成！共导入".count($datalist)."条货位，成功".count($insert)."条，失败".count($error)."条!";
			
		if(count($insert) > 0){
			$this->db->trans_begin();
			
			$where = '(1=1) and storage_id = '.$storageId.' and sid = '.$this->jxcsys['sid'].' and item_id in ('.join(',', array_column($insert, "item_id")).')';
			$this->mysql_model->delete(GOODS_AREA_REL,$where);
			$rs = $this->mysql_model->insert(GOODS_AREA_REL,$insert);
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$pagedata['status'] = "error";
				$pagedata['message'] = "导入失败！操作数据库失败！";
			} else {
				$this->db->trans_commit();
			}
		}
		
		$pagedata['file'] = $filter_name;
		die(json_encode($pagedata));
	}

    /**
     * 第三方商品校验
     */
    public function saveGoods(){
        $execl = $this->parseExcel('resume_file');
        $data = $execl['data'];
        $header = $execl['header'];
        //查询出所有类别合集
        $category = $this->mysql_model->get_results(CATEGORY,'(1=1) and status = 1 and typeNumber = "trade" and sid = '.$this->jxcsys['sid']);
        $ex_category = array_column($category,'name');
        $categorys = array_bind_key($category,'name');
        //查询出所有单位合集
        $unit = $this->mysql_model->get_results(UNIT,'(1=1) and status = 1 and sid in(1,'.$this->jxcsys['sid'].')');
        $ex_unit = array_column($unit,'name');
        $units = array_bind_key($unit,'name');

        //查询出仓库合集
        $location = $this->mysql_model->get_results(STORAGE,'(1=1) and isDelete = 0 and sid = '.$this->jxcsys['sid']);
        $ex_location = array_column($location,'name');
        $locations = array_bind_key($location,'name');

        $contact = $this->mysql_model->get_results(CATEGORY,'(isDelete=0) and typeNumber = "customertype" and status = 1 and sid = '.$this->jxcsys['sid']);
        $contacts = array_bind_key($contact,'name');

        foreach($contact as $k=>$v){
            $chr[] = chr(75 + $k);
            $num[] = 11+$k;
        }
        $error = array();$success = array();
        foreach($data as $key=>$value){
            if(empty($value['A']) || empty($value['B']) || empty($value['D']) || empty($value['E']) || empty($value['F']) || empty($value['G'])|| empty($value['H'])){
                $value['Z'] = "商品编号、商品名称、商品类别、首选仓库、最低库存、最高库存、计量单位不能为空";
                $error[] = $value;
                continue;
            }
            if(!in_array($value['D'], $ex_category)){
                $value['Z'] = "商品类别".$value['D']."在店管家中不存在";
                $error[] = $value;
                continue;
            }
            if(!in_array($value['H'],$ex_unit)){
                $value['Z'] = "计量单位".$value['H'].'在电管家中不存在';
                $error[] = $value;
                continue;
            }
            if(!in_array($value['E'],$ex_location)){
                $value['Z'] = "首选仓库".$value['E'].'在电管家中不存在';
                $error[] = $value;
                continue;
            }
            if(!str_check($value['I'],'int')){
                $value['Z'] = "期初数量为必须正整数";
                $error[] = $value;
                continue;
            }

            $success[] = $value;
        }
        if(count($error)){
            $filter_name = $this->importError($error,$header);
        }
        $this->db->trans_begin();
        foreach($success as $k=>$v){
            $r['sid'] = $this->jxcsys['sid'];
            $r['title'] = $v['B'];
            $r['name'] = $v['D'];
            $r['number'] = $v['A'];
            $r['spec'] = $v['C'];
            $r['unitName'] = $v['H'];
            $r['unitId'] = $units[$v['H']]['id'];
            $r['baseUnitId'] = $units[$v['H']]['id'];
            $r['categoryId'] = $categorys[$v['D']]['id'];
            $r['categoryName'] = $v['D'];
            $r['lowQty'] = $v['F'];
            $r['highQty'] = $v['G'];
            $r['purPrice'] = floatval($v['J']);
            $r['settlePrice'] = floatval($v['J']);
            $r['salePrice'] = floatval($v['J']);
            $r['status'] = 1;
            $r['locationId'] = $locations[$v['E']]['id'];
            $r['locationName'] = $v['E'];
            $r['isSelf'] = 0;
            $r['skuStatus'] = 1;
            $invId = $this->mysql_model->insert(GOODS,$r);

            $s[$k]['sid'] = $this->jxcsys['sid'];
            $s[$k]['goodsId'] = $invId;
            foreach($chr as $key=>$value){
                $retail[$contacts[$header[$num[$key]]]['id']] = $v[$value];
            }
            $s[$k]['retailPrice'] = json_encode($retail);
            $insert[$k]['sid'] = $this->jxcsys['sid'];
            $insert[$k]['billNo'] = "期初数量";
            $insert[$k]['amount'] = floatval($v['I']*$v['J']);
            $insert[$k]['invId'] = $invId;
            $insert[$k]['billDate'] = date('Y-m-d');
            $insert[$k]['price']   = floatval($v['J']);
            $insert[$k]['qty'] = $v['I'];
            $insert[$k]['locationId'] = $locations[$v['E']]['id'];
            $initAreaId = $this->mysql_model->get_row(STORAGE_AREA,'str_id = '.$insert[$k]['locationId'].' and area_code="IN-00-01"',"id");
            $insert[$k]['areaId'] = $initAreaId;
            $insert[$k]['entryId'] = 1;
            $insert[$k]['transTypeName'] = "期初数量";
            $insert[$k]['billType'] = "INI";
        }
        $this->mysql_model->insert(RETAILPRICE,$s);

        $this->mysql_model->insert(INVOICE_INFO,$insert);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $pagedata['status'] = "error";
            $pagedata['message'] = "导入失败！";
        } else {
            $this->db->trans_commit();
            $pagedata['status'] = "success";
            $pagedata['message'] = "导入完成！共导入".count($data)."条商品，成功".count($success)."条，失败".count($error)."条!";
        }

        $pagedata['file'] = $filter_name;
        die(json_encode($pagedata));
    }
	
	
	/**
	 * 上传商品期初检验
	 */
	public function saveImportGoodsInit(){

		
		$storageId = $this->input->post("storageId",true);
		$execl = $this->parseExcel('resume_file');
		$data = $execl['data'];
		$header = $execl['header'];
		
		//查询出商品集合
		$goods = $this->data_model->get_goodsInfo_settle('and g.isDelete=0 and g.saleModel is not null');

		//由于SKUID可能重复将所有SKUID遍历出放来放到一个数据 。
		$ex_goods = array_column($goods, "skuId");
		$goodsInfo = array_bind_key($goods, "skuId");

		$areas = $this->mysql_model->get_results(STORAGE_AREA,'str_id = '.$storageId.' and sid='.$this->jxcsys['sid']);
		$initAreaId = $this->mysql_model->get_row(STORAGE_AREA,'str_id = '.$storageId.' and area_code="IN-00-01"',"id");
		$areasInfo = array_bind_key($areas, "area_code");
		
		
		
		$amount = 0;
		$totalQty = 0;
		//检验导入EXEC表，生成EXEC输出。
		$error = array();$success = array();$temp = array();
		foreach ($data as $key=>$value){
			if(empty($value['A']) || empty($value['B']) || empty($value['C'])){
				$value['E'] = "物料编码、期初货位、期初数量不能为空";
				$error[] = $value;
				continue;
			}
			if(!in_array($value['A'], $ex_goods)){
				$value['E'] = "物料编码".$value['A']."在店管家中不存在";
				$error[] = $value;
				continue;
			}
			if(!str_check($value['C'],'int')){
				$value['E'] = "期初数量为必须正整数";
				$error[] = $value;
				continue;
			}
			if(!str_check($value['D'],'price')){
				$value['D'] = $goodsInfo[$value['A']]['settle_price'];
			}
			
			$totalQty += $value['C'];
            $amount   += ($value['C'] * $value['D']);

			$success[] = $value;
		}
		//生成错误的EXECL
		if(count($error)){
			$filter_name = $this->importError($error,$header);
		}
			
		$order_where = '(1=1) and billType = "PUR" and leibie="00-Cxx-00" and locationId = '.$storageId.' and sid = '.$this->jxcsys['sid'];
		$order_exits =  $this->mysql_model->get_row(INVOICE_ORDER,$order_where);
		
		if(!empty($order_exits)){
			$res['billNo']      = $order_exits['billNo'];
			$res['nid']         = $order_exits['nid'];
		}else{
			$res['billNo']      	= str_no('CGO');
			$res['nid']             = str_no('INI');
		}
		
		$res['billType']        = 'PUR';
		$res['orderStatus']     = "6";
		$res['transTypeName']   =  '期初';
		$res['locationId']      = $storageId;
		$res['buId']            = "1";
		$res['billDate']        = date('Y-m-d');
		$res['totalQty']        = (float)$totalQty;
		$res['amount']     		= abs($amount);
	    $res['totalAmount'] 	= abs($amount);
	    $res['rpAmount']        = $res['rpAmount'];
	    $res['hxStateCode']     = $res['hxStateCode'];
	    $res['arrears']         = $amount;
		$res['rpAmount']        = $res['rpAmount'];
		$res['hxStateCode']     = $res['hxStateCode'];
		$res['arrears']         = $amount;
		$res['leibie']			= '00-Cxx-00';
		$res['totalArrears']    = (float)$res['totalArrears'];
		$res['disRate']         = (float)$res['disRate'];
		$res['disAmount']       = (float)$res['disAmount'];
		$res['uid']             = $this->jxcsys['uid'];
		$res['sid']			 	= $this->jxcsys['sid'];
		$res['userName']        = $this->jxcsys['name'];
		$res['modifyTime']      = date('Y-m-d H:i:s');
		$res['leibie']        	= "00-Cxx-00";
		$res['saleModel']    	= "KZCP";
		
		$insert   = array();
		$o_insert = array();
		$r_insert = array();
		//组装数据
		foreach ($success as $key => $value){
			$insert[$key]['invId']         = $goodsInfo[$value['A']]['id'];
			$insert[$key]['sid']		   = $this->jxcsys['sid'];
			$insert[$key]['locationId']    = $storageId;
			$insert[$key]['areaId']    	   = empty($areasInfo[$value['B']]['id']) ? $initAreaId : $areasInfo[$value['B']]['id'];
			$insert[$key]['qty']           = $value['C'];
			$insert[$key]['price']         = floatval($value['D']);
			$insert[$key]['amount']        = floatval($value['C'] * $value['D']);
			$insert[$key]['skuId']         = $value['A'];
			$insert[$key]['billDate']      = date('Y-m-d');
			$insert[$key]['billNo']        = '期初数量';
			$insert[$key]['billType']      = 'INI';
			$insert[$key]['transTypeName'] = '期初数量';
		
			$o_insert[$key]['invId']         = $goodsInfo[$value['A']]['id'];
			$o_insert[$key]['sid']		      = $this->jxcsys['sid'];
			$o_insert[$key]['buId']          = "1";
			$o_insert[$key]['invName']       = $goodsInfo[$value['A']]['number']." ".$goodsInfo[$value['A']]['brand_name']." ".$goodsInfo[$value['A']]['skuId']." ".$goodsInfo[$value['A']]['name'];
			$o_insert[$key]['locationId']    = $storageId;
			$o_insert[$key]['unitId']        = $goodsInfo[$value['A']]['unitId'];
			$o_insert[$key]['areaId']    	   = empty($areasInfo[$value['B']]['id']) ? $initAreaId : $areasInfo[$value['B']]['id'];
			$o_insert[$key]['qty']           = $value['C'];
			$o_insert[$key]['price']         = floatval($value['D']);
			$o_insert[$key]['amount']        = floatval($value['C'] * $value['D']);
			$o_insert[$key]['skuId']         = $value['A'];
			$o_insert[$key]['billDate']      = date('Y-m-d');
			$o_insert[$key]['billNo']        = $res['billNo'];
			$o_insert[$key]['billType']      = 'INI';
			$o_insert[$key]['transTypeName'] = '期初';
		
			$r_insert[$key]['sid'] = $this->jxcsys['sid'];
			$r_insert[$key]['billNo'] = "期初数量";
			$r_insert[$key]['invId']  = $goodsInfo[$value['A']]['id'];
			$r_insert[$key]['invNum'] = $value['C'];
			$r_insert[$key]['waitInto'] = 0;
			$r_insert[$key]['waitOut'] = 0;
			$r_insert[$key]['haveInto'] = $value['C'];
			$r_insert[$key]['closeNum'] = 0;
			$r_insert[$key]['rtNum']   = 0;
		}
		
		if(count($insert)){
				
			$this->db->trans_begin();
			
			if(empty($order_exits)){
				$iid = $this->mysql_model->insert(INVOICE_ORDER,$res);
			}else{
				$iid = $order_exits['id'];
				$this->mysql_model->update(INVOICE_ORDER,$res,'(id='.$iid.')');
			}
			
			foreach($r_insert as $arr=>$v){
				$r_insert[$arr]['iid'] = $iid;
			}
			foreach($o_insert as $k=>$v){
				$o_insert[$k]['iid'] = $iid;
			}
			
			$where = '(1=1) and billType = "INI" and locationId = '.$storageId.' and sid = '.$this->jxcsys['sid'];
			$where1 = '(1=1) and sid = '.$this->jxcsys['sid'].' and iid = '.$iid;
			
			$this->mysql_model->delete(INVOICE_INFO,$where);
			$this->mysql_model->insert(INVOICE_INFO,$insert);
			
			$this->mysql_model->delete(INVOICE_ORDER_INFO,$where);
			$this->mysql_model->insert(INVOICE_ORDER_INFO,$o_insert);
	
			$this->mysql_model->delete(ORDER_TOTAL,$where1);
			$this->mysql_model->insert(ORDER_TOTAL,$r_insert);
			
			$pagedata['status'] = "success";
			$pagedata['message'] = "导入完成！共导入".count($data)."条商品期初，成功".count($data)."条，失败".count($error)."条!";
			
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$pagedata['status'] = "error";
				$pagedata['message'] = "导入失败！".$sql;
			} else {
				$this->db->trans_commit();
			}
		}
		
		$pagedata['file'] = $filter_name;
		die(json_encode($pagedata));
	}

    /**
     * 商品售价校验
     */

    public function saveGoodsPrice(){
        $categoryId = $this->input->post("categoryId",true);
        $execl = $this->parseExcel('resume_file');

        $data = $execl['data'];
        $header = $execl['header'];

        //查询出商品集合
        $goods = $this->mysql_model->get_results(GOODS,'(isDelete=0) and sid=1');
        //由于SKUID可能重复将所有SKUID遍历出放来放到一个数据 。
        //$ex_goods = array_column($goods, "skuId");
        $goodsInfo = array_bind_key($goods, "skuId");
        

        $error = array();$success = array();
        foreach ($data as $key=>$value){
            if(empty($value['A']) || empty($value['B'])){
                $value['E'] = "物料编码、售价不能为空";
                $error[] = $value;
                continue;
            }
            if(empty($goodsInfo[$value['A']])){
                $value['E'] = "物料编码".$value['A']."在店管家中不存在";
                $error[] = $value;
                continue;
            }

            $success[] = $value;
        }
        
        //生成错误的EXECL
        if(count($error)){
            $filter_name = $this->importError($error,$header);
        }
        $this->db->trans_begin();
        $retailPriceList = $this->mysql_model->get_results(RETAILPRICE,'(1=1) and (sid='.$this->jxcsys['sid'].')');
        $retailPriceList = array_bind_key($retailPriceList, 'goodsId');
        foreach ($success as $k=>$v){
            $info = $retailPriceList[$goodsInfo[$v['A']]['id']];
            if($info){
                $retailPrice = json_decode($info['retailPrice'],true);
            }
            $arr1[$k]['sid'] = $this->jxcsys['sid'];
            $arr1[$k]['goodsId'] = $goodsInfo[$v['A']]['id'];
            $retailPrice[$categoryId] = floatval($v['B']);
            $arr1[$k]['retailPrice'] = json_encode($retailPrice);
        }
        if(count($arr1) > 0){
        	$this->mysql_model->delete(RETAILPRICE,'(sid='.$this->jxcsys['sid'].')');
        	$this->mysql_model->insert(RETAILPRICE,$arr1);
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $pagedata['status'] = "error";
            $pagedata['message'] = "导入失败！";
        } else {
            $this->db->trans_commit();
            $pagedata['status'] = "success";
            $pagedata['message'] = "导入完成！共导入".count($data)."条商品售价，成功".count($success)."条，失败".count($error)."条!";
        }
        $pagedata['file'] = $filter_name;
        die(json_encode($pagedata));
    }

    /**
     * 客户导入检验
     */

    public function saveCustomer(){
        $categoryId = $this->input->post("categoryId",true);
        $categoryName = $this->input->post('categoryName',true);
        $execl = $this->parseExcel('resume_file');

        $data = $execl['data'];
        $header = $execl['header'];

        //查询出客户集合
        $contact = $this->mysql_model->get_results(CONTACT,'(isDelete=0) and type = -10 and sid = '.$this->jxcsys['sid']);

        $ex_contact = array_column($contact, "number");

        $error = array();$success = array();
        foreach ($data as $key=>$value){
            if(empty($value['A']) || empty($value['B']) || empty($value['C'])){
                $value['I'] = "客户编号、客户名称、联系人不能为空";
                $error[] = $value;
                continue;
            }
            if(in_array($value['A'], $ex_contact)){
                $value['I'] = "客户编号".$value['A']."已存在";
                $error[] = $value;
                continue;
            }

            $success[] = $value;
        }
        //生成错误的EXECL
        if(count($error)){
            $filter_name = $this->importError($error,$header);
        }
        $this->db->trans_begin();
        foreach($success as $k=>$v){
            $arr[$k]['sid'] = $this->jxcsys['sid'];
            $arr[$k]['number'] = $v['A'];
            $arr[$k]['name'] = $v['B'];
            $arr[$k]['cCategory'] = $categoryId;
            $arr[$k]['cCategoryName'] = $categoryName;
            $customer['linkName']   =    $v['C'];
            $customer['linkMobile'] = $v['D'];
            $customer['linkPhone']  = $v['E'];
            $customer['linklm']     = $v['F'];
            $customer['linkAddress']= $v['H'];
            $arr[$k]['linkMans']    = json_encode($customer);
            $arr[$k]['difMoney']    = $v['G'];
            $arr[$k]['cLevel']      = 1;
        }
        $this->mysql_model->insert(CONTACT,$arr);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $pagedata['status'] = "error";
            $pagedata['message'] = "导入失败！";
        } else {
            $this->db->trans_commit();
            $pagedata['status'] = "success";
            $pagedata['message'] = "导入完成！共导入".count($data)."条客户信息，成功".count($success)."条，失败".count($error)."条!";
        }
        $pagedata['file'] = $filter_name;
        die(json_encode($pagedata));

    }

	
	/**
	 * 上传货位检验
	 */
	public function saveImport(){
		
		$storageId = $this->input->post("storageId",true);
		$execl = $this->parseExcel('resume_file');
		
		$data = $execl['data'];
		$header = $execl['header'];
		
		$data = array_values(array_bind_key($data, "A"));//去重
		$ex_areas = $this->mysql_model->get_results(STORAGE_AREA,'(1=1) and str_id='.$storageId.' and isDelete = 0');
		$ex_areas = array_column($ex_areas, "area_code");
		
		//检验是否表中是否重复，生成EXEC输出。
		$error = array();$success = array();
		foreach ($data as $key=>$value){
			if(in_array($value['A'], $ex_areas)){
				$value['D'] = "货位编码".$value['A']."已存在";
				$error[] = $value;
				continue;
			}
			$success[] = $value;
		}
		//生成错误的EXECL
		if(count($error)){
			$filter_name = $this->importError($error,$header);
		}
		
		if(count($success)){
			//新增货位
			foreach ($success as $key => $value){
				$insert[$key]['sid'] = $this->jxcsys['sid'];
				$insert[$key]['str_id'] = $storageId;
				$insert[$key]['area_code'] = $value['A'];
				$insert[$key]['area_name'] = $value['B'];
				$insert[$key]['area_desc'] = $value['C'];
			}
		}
		
		$this->db->trans_begin();
		$rs = $this->mysql_model->insert(STORAGE_AREA,$insert);
			
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$pagedata['status'] = "error";
			$pagedata['message'] = "导入失败！";
		} else {
			$this->db->trans_commit();
			$pagedata['status'] = "success";
			$pagedata['message'] = "导入完成！共导入".count($data)."条货位，成功".count($insert)."条，失败".count($error)."条!";
		}
		$pagedata['file'] = $filter_name;
		die(json_encode($pagedata));
	}



	
	function downError(){
		$filename = $this->input->get("filename",true);
		$dir = './data/download/import/error/'.$this->jxcsys['sid'];
		$info = read_file($dir.'/'.$filename);
		$this->common_model->logs('货位错误列表'.$filename);
		force_download('货位导入错误列表_'.time().'.xls', $info,true);
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
	
	
	//上传文件
	function upload_file($fieldname){
		
		$config['upload_path']="./data/upfile/excels";
	
		if(!is_dir($config['upload_path'])){
			$old = umask(0);
			mkdir($config['upload_path'],0775,true);
			umask($old);
		}
		$config['allowed_types']= 'xls|xlsx';
		$config['file_name'] = uniqid();
		$this->load->library('upload', $config);
		
		if (!$this->upload->do_upload($fieldname)) {
			$error = array('error' =>$this->upload->display_errors());
			$rfile['error'] = $error;
		}
		else {	
			$data = array('upload_data' => $this->upload->data());
		}
		
		$d = $this->upload->data();
		$rfile['file_name'] = $d['file_name'];
		$file_url  = $config['upload_path']."/".$d['file_name'];
		chmod($file_url, 0775);
		$rfile['file_url'] = $file_url;
		return $rfile;
	}
	
	
	function parseExcel($filename){
		
		$filePath = $this->upload_file($filename);
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
	
		
	
	
	//公共验证
	private function validform($data) {
        !isset($data['name']) && strlen($data['name']) < 1 && str_alert(-1,'仓库名称不能为空');
		!isset($data['locationNo']) && strlen($data['locationNo']) < 1 && str_alert(-1,'编号不能为空');
		$data['sid'] = $this->jxcsys['sid'];
		$where = isset($data['locationId']) ? ' and (id<>'.$data['locationId'].')' :'';
		$where .= " and (isDelete = 0) and sid = ".$this->jxcsys['sid'];
		$this->mysql_model->get_count(STORAGE,'(name="'.$data['name'].'") '.$where) > 0 && str_alert(-1,'名称重复');
		$this->mysql_model->get_count(STORAGE,'(locationNo="'.$data['locationNo'].'") '.$where) > 0 && str_alert(-1,'编号重复'.$this->db->last_query());
		return $data;
	}
	
	//公共验证
	private function validform_area($data) {
		!isset($data['area_name']) && strlen($data['area_name']) < 1 && str_alert(-1,'货位名称不能为空');
		!isset($data['area_code']) && strlen($data['area_code']) < 1 && str_alert(-1,'货位编号不能为空');
		$data['sid'] = $this->jxcsys['sid'];
		$where = isset($data['id']) ? ' and (id <> '.$data['id'].')' :'';
		$where .= " and (isDelete = 0) and sid = ".$this->jxcsys['sid'];
		$this->mysql_model->get_count(STORAGE_AREA,'(area_name="'.$data['area_name'].'") and str_id='.$data['str_id'].$where) > 0 && str_alert(-1,'货位名称重复');
		$this->mysql_model->get_count(STORAGE_AREA,'(area_code="'.$data['area_code'].'") and str_id='.$data['str_id'].$where) > 0 && str_alert(-1,'货位编号重复');
		return $data;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */