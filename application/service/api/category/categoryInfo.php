<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class categoryInfo extends MY_Service {
	
	
	public function __construct(){
		parent::__construct();
	}


	public function service($params){
		 
		///////////业务逻辑//////////////
		if(empty($params['cat_id'])){
			die("{status:error,message:分类编号不能为空}");
		}else{
			switch ($params['update_type']) {
							///////////////分类修改////////////
				case "1" :
						$id = $params['cat_id'];
						$data['name'] = $params['cate_name'];
						$this->db->trans_begin();
						$this->mysql_model->update("zee_category",$data,'(id='.$id.')');
						/////////编辑三级分类名时同步到zee_goods表中//////
						if($params['level']==3){
							$id2 = $params['cat_id'];
							$data2['categoryName'] = $params['cate_name'];
							$this->mysql_model->update("zee_goods",$data2,'(categoryId='.$id2.')');
						}
						//////////////////////////////////////////
						if ($this->db->trans_status() === FALSE) {
							$this->db->trans_rollback();
							die("{status:error,message:修改失败}");
						}else{
							$this->db->trans_commit();
							die("{status:success,message:修改成功}");
						}
						//////////////////////////////////
					break;
				  
							/////////////分类新增/////////////
				case "2" :
					$data['id'] = $params['cat_id'];
					$data['sid'] = "1";
					$data['name'] = $params['cate_name'];
					$data['level'] = $params['level'];
					$data['typeNumber'] = "trade";
					$data['isSelf'] = "1";
					
					if($params['level']==1){
						$data['path']=$params['cat_id'];
						$data['parentId']=0;
					}else{
						$data['path']=substr($params['path'].$params['cat_id'],1);
						$data['parentId']=$params['parent_id'];
					}
					$this->db->trans_begin();
					$this->mysql_model->insert("zee_category",$data);
					if ($this->db->trans_status() === FALSE) {
						$this->db->trans_rollback();
						die("{status:error,message:添加失败}");
					}else{
						$this->db->trans_commit();
						die("{status:success,message:添加成功}");
					}
					break;
			   
							//////////////分类删除///////////////
				case "3" :
					$id = $params['cat_id'];
					$this->db->trans_begin();
					$this->mysql_model->delete("zee_category",'(id='.$id.')');
					if ($this->db->trans_status() === FALSE) {
						$this->db->trans_rollback();
						die("{status:error,message:删除失败}");
					}else{
						$this->db->trans_commit();
						die("{status:success,message:删除成功}");
					}
					break;
		
			}
		
		}
		 
		$this->_success($params);
	}
}
	
