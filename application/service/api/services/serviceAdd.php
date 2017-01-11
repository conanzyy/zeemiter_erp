<?php 
header("Content-type: text/html; charset=utf-8"); 
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 服务商信息新增
 *
 */
class serviceAdd extends MY_Service {

	
    public function __construct(){
        parent::__construct();
    }
    
    public function service($params){
      
    	//业务逻辑
        //服务商增加 
        $admin['sid']		  	 = $params['login_account'];
        $admin['username']	  	 = $params['login_account'];
        $admin['userpwd']	  	 = md5($params['login_password']);
        $admin['service_type']	 = $params['service_type'];
        $admin['name']			 = $params['account_name'];
        $admin['roleid']		 = 0;
        $admin['rightids']       = '';
        
        $options['sid']			 = $params['login_account'];
        $options['option_name']  = 'system';
        $options['autoload']     = 'yes';
        $options['option_value'] = '{"companyName":"'.$params['companyName'].'","companyAddr":"'.$params['companyAddr'].'","companyBank":"'.$params['companyBank'].'","companyCode":"'.$params['companyCode'].'","companyAccount":"'.$params['companyAccount'].'","phone":"'.$params['phone'].'","fax":"'.$params['fax'].'","postcode":"","settlePlaces":"100","qtyPlaces":"1","pricePlaces":"2","amountPlaces":"2","valMethods":"movingAverage","requiredCheckStore":"1"}';
        $this->db->trans_begin();
        //向zee_admin 中新增一条数据
        $iid = $this->mysql_model->insert("zee_admin",$admin);
        //向zee_options中新增一条数据
        $iid = $this->mysql_model->insert("zee_options",$options);
		
        //创建仓库;
        $this->addStorage($params);
        
        //创建新表
        //$iid=$this->db->query("call goods_storage_add($sid)" );
        
        if ($this->db->trans_status() === FALSE) {
        	$this->db->trans_rollback();
        	$this->_error("E10001",'创建店管家账号失败！');
        } else {
        	$this->db->trans_commit();
        }
    
    	$this->_success($params,"修改成功");
    }
    
    /**
     * 创建仓库
     */
    private  function addStorage($params){
    	
    	$storage['name'] = '快准仓';
    	$storage['locationNo'] = 'KZ001';
    	$storage['sid'] = $params['login_account'];
    	$storage['isArea'] = 0;
    	$storage['sid'] = $params['login_account'];
    	$storage['isDefault'] = 1;
    	$storage['type'] = 0;
    	
    	$sql = $this->mysql_model->insert(STORAGE,elements(array('name','locationNo','sid','type','isArea','address','isDefault'),$storage));
    	$defarea = array( "str_id"=>$sql, "sid" => $params['login_account'], 'area_code' => 'IN-00-01', 'area_name' => '默认');
    	$aid = $this->mysql_model->insert(STORAGE_AREA,$defarea);
    	
    	
    	$storage2['name'] = '第三方仓';
    	$storage2['locationNo'] = 'S001';
    	$storage2['sid'] = $params['login_account'];
    	$storage2['isArea'] = 0;
    	$storage2['sid'] = $params['login_account'];
    	$storage2['isDefault'] = 0;
    	$storage2['type'] = 1;
    	
    	$sql = $this->mysql_model->insert(STORAGE,elements(array('name','locationNo','sid','type','isArea','address','isDefault'),$storage2));
    	$defarea = array( "str_id"=>$sql, "sid" => $params['login_account'], 'area_code' => 'IN-00-01', 'area_name' => '默认');
    	$aid = $this->mysql_model->insert(STORAGE_AREA,$defarea);
    	
    	
    }
}
    
    

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */