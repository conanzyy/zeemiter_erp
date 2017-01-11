<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	class servicetype extends MY_Service {
			public function __construct(){
				parent::__construct();
		}


	public function service($params){
		 
		//业务逻辑
		if(empty($params['servicetypeId'])){
	   	
	   		die("{status:error,message:服务商类型id不能为空}");
	   		
	   }else{	   	   		   	
		   	    ////////////服务商类型删除////////////
		   	if($params['update_type']){
		   		$this->db->trans_begin();
		   		$this->mysql_model->delete("zee_service_type",'(servicetypeId='.$params['servicetypeId'].')');
		   		if($this->db->trans_status() === FALSE){
		   			$this->db->trans_rollback();
		   			die("{status:error,message:删除失败}");
		   		}else{
		   			$this->db->trans_commit();
		   			die("{status:success,message:删除成功}");
		   		}
		   	}else{
		   		//////////服务商类型添加,修改/////////
		   		$brandId = $params['brandId'];
		   		$data['servicetypeId'] = $params['servicetypeId'];
		   		$count=$this->mysql_model->get_count("zee_service_type",'(servicetypeId='.$params['servicetypeId'].')');
		   		//print_r($count);
		   		//die();
		   		if(empty($count)){		   					   			
		   			$this->db->trans_begin();
		   			foreach($brandId as $v){
		   				$data['brandId']=$v;
		   				//print_r($v);
		   				//die();
	   					$this->mysql_model->insert("zee_service_type",$data);
		   			}
	   				if($this->db->trans_status() === FALSE){
	   					$this->db->trans_rollback();
	   					die("{status:error,message:添加失败}");
	   				}else{
	   					$this->db->trans_commit();
	   					die("{status:success,message:添加成功}");
	   				}
		   		}else{
		   			$this->db->trans_begin();
		   			$this->mysql_model->delete("zee_service_type",'(servicetypeId='.$params['servicetypeId'].')');
		   			foreach($brandId as $v){
		   				$data['brandId']=$v;
		   				$this->mysql_model->insert("zee_service_type",$data);
		   			}
		   			if($this->db->trans_status() === FALSE){
		   				$this->db->trans_rollback();
		   				die("{status:error,message:修改失败}");
		   			}else{
		   				$this->db->trans_commit();
		   				die("{status:success,message:修改成功}");
		   			}
		   		}
		   	}

		}
		$this->_success($params);
	}
}