<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Area extends CI_Controller {

	
	
    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
		$this->jxcsys  = $this->session->userdata('jxcsys');
    }
	
	//货位列表
	public function index(){
		$v = array();
		$str_id = $this->input->get_post('locationId',true);
		$area_code = $this->input->get_post('area_code',true);
		$page = max(intval($this->input->get_post('page', TRUE)), 1);
		$rows = max(intval($this->input->get_post('rows', TRUE)), 10);
		
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$where = " and sid = ".$this->jxcsys['sid'];
		$where .= ' and str_id = '.$str_id;
		$where .= (!empty($area_code) ? ' and (area_code like "%'.$area_code.'%" or area_name like "%'.$area_code.'%")'  : '');
		$offset = $rows * ($page - 1);
		$list = $this->mysql_model->get_results(STORAGE_AREA,'(isDelete=0) '.$where.' order by id  limit ' . $offset . ',' . $rows . '');  
		$data['data']['rows']      = $list;
		$data['data']['page'] = $page;                                    //当前页
		$data['data']['records'] = $this->mysql_model->get_count(STORAGE_AREA,'(isDelete=0) '.$where.' order by id');
		$data['data']['total'] = ceil($data['data']['records'] / $rows);
		die(json_encode($data)); 
	}
	
	
	public function getRel(){
		$params = $this->input->get(NULL,TRUE);
		$where = "sid = ".$this->jxcsys['sid'];
		$where .= " and storage_id = ".$params['storage_id'];
		$where .= " and item_id = ".$params['item_id'];
		
		$data = $this->mysql_model->get_results(GOODS_AREA_REL,$where);
		if(empty($data)){
			$where = "sid = ".$this->jxcsys['sid'];
			$where .= " and str_id = ".$params['storage_id'];
			$where .= " and area_code = 'IN-00-01'";
			$data = $this->mysql_model->get_results(STORAGE_AREA,$where);
			foreach ($data as $key => $value){
				$data[$key]['area_id'] = $value['id'];
			}
		}
		$rs = responseJSON(true,"查询成功！",url,$data);
		die($rs);
	}
	
	
	public function saveRel(){
		
		$params = $this->input->post(NULL,TRUE);
		
		$where = "sid = ".$this->jxcsys['sid'];
		$where .= " and storage_id = ".$params['storage_id'];
		$where .= " and item_id = ".$params['item_id'];
		
		
		$this->db->trans_begin();
		$params['sid'] = $this->jxcsys['sid'];
		$this->mysql_model->delete(GOODS_AREA_REL,$where);
		$rs = $this->mysql_model->insert(GOODS_AREA_REL,$params);
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			str_alert(-1,'添加默认货位失败！');
		} else {
			$this->db->trans_commit();
			str_alert(200,'success',$data);
		}	
		
	}
	
	
	//公共验证
	private function validform($data) {
	    $data['amount'] = (float)$data['amount'];
		$data['type']   = intval($data['type']);
		$data['sid'] = $this->jxcsys['sid'];
        !isset($data['name']) || strlen($data['name']) < 1 && str_alert(-1,'名称不能为空');
		!isset($data['number']) || strlen($data['number']) < 1 && str_alert(-1,'编号不能为空');
		$where = isset($data['id']) ? ' and (id<>'.$data['id'].')' :'';
		$where .= " and sid = ".$this->jxcsys['sid'];
		$this->mysql_model->get_count(STORAGE_AREA,'(isDelete=0) and (name="'.$data['name'].'") '.$where) > 0 && str_alert(-1,'名称重复');
		$this->mysql_model->get_count(STORAGE_AREA,'(isDelete=0) and (number="'.$data['number'].'") '.$where) > 0 && str_alert(-1,'编号重复');
		return $data;
	}  
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */