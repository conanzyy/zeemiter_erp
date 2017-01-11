<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
		$this->jxcsys = $this->session->userdata('jxcsys');
    }

    //客户列表
	public function customer_list() {
	    $this->common_model->checkpurview(58);
		$this->load->view('settings/customer-list');
	}


	//客户新增修改
	public function customer_manage() {
        $data = $this->db->query('select number from zee_contact where isDelete=0 and type=-10 and sid = "'.$this->jxcsys['sid'].'" ORDER BY CAST(number AS UNSIGNED INTEGER) desc limit 0,1')->row_array();
		if(!$data){
		    $data['number'] = $this->jxcsys['sid']."0001";
        }
        $this->load->view('settings/customer-manage',$data);
	}

	//供应商列表
	public function vendor_list() {
	    $this->common_model->checkpurview(63);
		$this->load->view('settings/vendor-list');
	}


	//供应商新增修改
	public function vendor_manage() {
		//$info = $this->db->query('select * from zee_contact where isDelete=0 and type=10 and sid = "'.$this->jxcsys['sid'].'"')->row_array();
        $data = $this->db->query('select number from zee_contact where isDelete=0 and type=10 and sid = "'.$this->jxcsys['sid'].'" ORDER BY CAST(number AS UNSIGNED INTEGER) desc limit 0,1')->row_array();
        if(!$data){
            $data['number'] = "000";
        }
        
       // echo "==".json_encode($info);
        $this->load->view('settings/vendor-manage',$data);
	}


	//联系地址
	public function addressmanage() {
		$this->load->view('settings/addressmanage');
	}

	//商品列表
	public function goods_list() {
	    $this->common_model->checkpurview(68);
		$this->load->view('settings/goods-list');
	}
	//快准商品列表
	public function goods_list_kz() {
		$this->common_model->checkpurview(68);
		$bussType = $this->input->get('bussType',TRUE);
		$data['bussType'] = $bussType;
		$post_data['method'] = "modelsmatch.api.brand";
		$list = http_client($post_data);
		$data['list'] = $list['result'];
		$this->load->view('settings/goods-list-kz',$data);
	}


	//仓库列表
	public function storage_list() {
	    $this->common_model->checkpurview(155);
		$this->load->view('settings/storage-list');
	}

	//仓库新增
	public function storage_manage() {
        $data = $this->db->query('select locationNo from zee_storage where isDelete=0 and disable=0 and sid = "'.$this->jxcsys['sid'].'" ORDER BY CAST(locationNo AS UNSIGNED INTEGER) desc limit 0,1')->row_array();
        if(!$data){
            $data['locationNo'] = "000";
        }
        $this->load->view('settings/storage-manage',$data);
	}
	
	//货位新增
	public function area_manage() {
		$str_id = $this->input->get("str_id",true);
		$data = $this->db->query('select area_code from '.STORAGE_AREA.' where isDelete=0 and str_id='.$str_id.' and sid = "'.$this->jxcsys['sid'].'" ORDER BY CAST(area_code AS UNSIGNED INTEGER) desc limit 0,1')->row_array();
		if(!$data){
			$data['area_code'] = "000";
		}
		$this->load->view('settings/area-manage',$data);
	}
	
	//货位
	public function area_list() {
		$str_id = $this->input->get("str_id",true);
		$storage = $this->mysql_model->get_row(STORAGE,'(id='.$str_id.') and (isDelete=0)');
		$this->load->view('settings/area-list',$storage);
	}

	//职员列表
	public function staff_list() {
	    $this->common_model->checkpurview(97);
		$this->load->view('settings/staff-list');
	}

	//职员新增
	public function staff_manage() {
        $data = $this->db->query('select number from zee_staff where isDelete=0 and disable=0 and sid = "'.$this->jxcsys['sid'].'" ORDER BY CAST(number AS UNSIGNED INTEGER) desc limit 0,1')->row_array();
		if(!$data){
		    $data['number'] = "000";
        }
        $this->load->view('settings/staff-manage',$data);
	}

	//发货地址管理
	public function shippingaddress() {
		$this->load->view('settings/shippingaddress');
	}

	//发货地址新增修改
	public function shippingaddressmanage() {
		$this->load->view('settings/shippingaddressmanage');
	}

	//账号管理
	public function settlement_account() {
	    $this->common_model->checkpurview(98);
		$this->load->view('settings/settlement-account');
	}

	//账号管理新增修改
	public function settlementaccount_manager() {
        $data = $this->db->query('select number from zee_account where isDelete=0 and sid = "'.$this->jxcsys['sid'].'" ORDER BY CAST(number AS UNSIGNED INTEGER) desc limit 0,1')->row_array();
		if(!$data){
		    $data['number'] = "000";
        }
        $this->load->view('settings/settlementaccount-manager',$data);
	}

	//系统参数
	public function system_parameter() {
	    $this->common_model->checkpurview(81);
		$this->load->view('settings/system-parameter');
	}

	//计量单位
	public function unit_list() {
	    $this->common_model->checkpurview(77);
		$this->load->view('settings/unit-list');
	}

	//计量单位新增修改
	public function unit_manage() {
		$this->load->view('settings/unit-manage');
	}


	//计量单位新增修改
	public function unitgroup_manage() {
		$this->load->view('settings/unitgroup-manage');
	}


	//数据备份
	public function backup() {
	    $this->common_model->checkpurview(84);
		$this->load->view('settings/backup');
	}

	//结算方式
	public function settlement_category_list() {
	    $this->common_model->checkpurview(159);
		$this->load->view('settings/settlement-category-list');
	}

	//结算方式新增修改
	public function settlement_category_manager() {
		$this->load->view('settings/settlement-category-manage');
	}

	//客户类别、商品类别、供应商类别、支出、收入
	public function category_list() {
	    $type = str_enhtml($this->input->get('typeNumber',TRUE));
		$info = array('customertype'=>73,'supplytype'=>163,'trade'=>167,'paccttype'=>171,'raccttype'=>175);
		$this->common_model->checkpurview($info[$type]);
		$this->load->view('settings/category-list');
	}

	//多账户结算
	public function choose_account() {
		$this->load->view('settings/choose-account');
	}


	//库存预警
	public function inventory_warning() {
		$this->load->view('settings/inventory-warning');
	}

	//日志
	public function log() {
	    $this->common_model->checkpurview(83);
		$this->load->view('settings/log-initloglist');
	}

	//权限
	public function authority() {
	    $this->common_model->checkpurview(82);
		$this->load->view('settings/authority');
	}

	//权限新增
	public function authority_new() {
	    $this->common_model->checkpurview(82);
		$this->load->view('settings/authority-new');
	}

	//功能权限设置
	public function authority_setting() {
	    $this->common_model->checkpurview(82);
		$this->load->view('settings/authority-setting');
	}

	//数据权限设置
	public function authority_setting_data() {
	    $this->common_model->checkpurview(82);
		$this->load->view('settings/authority-setting-data');
	}

	//商品新增修改
	public function goods_manage() {
		$userdata  = $this->session->userdata('jxcsys');
		$data = $this->mysql_model->get_results(CATEGORY,'(typeNumber="customertype" and sid='.$userdata['sid'].' and isDelete=0)','id , name');
		$param['priceList'] = $data;
		$this->load->view('settings/goods-manage',$param);
	}

	//商品起初设置
    public function goodsKz_set(){
        $userdata = $this->session->userdata('jxcsys');
//        $data = $this->mysql_model->get_results(CATEGORY,'(typeNumber="customertype" and sid='.$userdata['sid'].' and isDelete=0)','id , name');
//        $param['priceList'] = $data;
//        print_R($data);die;
        $this->load->view('settings/goodsKz-init');
    }

	//商品价格修改
	public function goodsKz_manage() {
		$userdata  = $this->session->userdata('jxcsys');
		$data = $this->mysql_model->get_results(CATEGORY,'(typeNumber="customertype" and sid='.$userdata['sid'].' and isDelete=0)','id , name');
		$param['priceList'] = $data;
		$this->load->view('settings/goodsKz-manage',$param);
	}
	
	//商品价格修改
	public function goodsKz_storage() {
		$param['item_id'] = $this->input->get("item_id",true);
		$this->load->view('settings/goodsKz-storage',$param);
	}

	//商品图片上传
	public function fileupload() {
		$this->load->view('settings/fileupload');
	}

	//辅助资料
	public function assistingprop() {
		$this->load->view('settings/assistingprop');
	}

	//辅助资料
	public function prop_list() {
		$this->load->view('settings/prop-list');
	}

	//辅助资料
	public function propmanage() {
		$this->load->view('settings/propmanage');
	}

	//导入
	public function import() {
		$this->load->view('settings/import');
	}

	//选择客户
	public function select_customer() {
		$this->load->view('settings/select-customer');
	}

	//选择商品
	public function goods_batch() {
		$this->load->view('settings/goods-batch');
	}

	//选择快准商品
	public function goods_batch_kz() {
		$bussType = $this->input->get('bussType',TRUE);
		$data['bussType'] = $bussType;
        $post_data['method'] = "modelsmatch.api.brand";
        $list = http_client($post_data);
        $data['list'] = $list['result'];
		$this->load->view('settings/goods-batch-kz',$data);
	}
	//选择所有商品
	public function goods_batch_all() {
		$supType = $this->input->get('supType',TRUE);
		$bussType = $this->input->get('bussType',TRUE,"1");
		$data['bussType'] = $bussType;
        $post_data['method'] = "modelsmatch.api.brand";
        $list = http_client($post_data);
        $data['list'] = $list['result'];
		if($supType == 'other'){
			$this->load->view('settings/goods-batch',$data);
		}else{
			$this->load->view('settings/goods-batch-kz',$data);
		}
	}

	//增值服务
	public function addedServiceList() {
		$this->load->view('settings/addedServiceList');
	}

	//属性
	public function assistingProp_batch() {
		$this->load->view('settings/assistingProp-batch');
	}

	//属性组合
	public function assistingPropGroupManage() {
		$this->load->view('settings/assistingPropGroupManage');
	}

	//批量选择仓库
	public function storage_batch() {
		$this->load->view('settings/storage-batch');
	}

	//批量选择销售人员
	public function saler_batch() {
		$this->load->view('settings/saler-batch');
	}

	//批量选择客户
	public function customer_batch() {
		$this->load->view('settings/customer-batch');
	}

	//批量选择供应商
	public function supplier_batch() {
		$this->load->view('settings/supplier-batch');
	}

	//批量选择账户
	public function settlementAccount_batch() {
		$this->load->view('settings/settlementAccount-batch');
	}


	//套打模板
	public function print_templates() {
		$this->load->view('settings/print-templates');
	}

	//套打模板新增修改
	public function print_templates_manage() {
		$this->load->view('settings/print-templates-manage');
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */