<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class NcTest extends CI_Controller {

    public function __construct(){
    	parent::__construct();
    }

    

	public function index(){
		
		$nid=$this->input->get_post('nid',true);
		
		if(empty($nid)){
			die("物料编码不给为空");
		}
		
		/* 
	 	 * 查询结算价接口
		 */
	    $data = array(
				"InventoryCode"=>$nid
		);//接口参数
		$post_data['data'] = json_encode($data);
		$post_data['method'] = 'settlePrice';//接口方法
		$result = http_client($post_data,'NC');
		echo $result->return;
		
		
		//return;
		//echo '==============<br/>';
		//return;
		/*
		 * 查询销售组织接口
		 */
		
		$data = array(
				"InventoryCode"=>$nid
		);//接口参数
		$post_data['data'] = json_encode($data);
		
		$post_data['method'] = 'saleOrg';//接口方法
		
		$result = http_client($post_data,'NC');
		
		echo $result->return;
		
		
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */