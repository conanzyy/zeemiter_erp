<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class DataRight extends CI_Controller {
	
	
	public function __construct(){
		parent::__construct();
		$this->common_model->checkpurview(82);
		$this->jxcsys = $this->session->userdata('jxcsys');
	
	}
	
	//详细权限设置
	public function queryall() {
		
	    $userName = str_enhtml($this->input->get_post('userName',TRUE));
		$uid = $this->jxcsys['uid'];
		
		if (strlen($userName)>0) {
		    $lever = $this->mysql_model->get_row(ADMIN,'(username="'.$userName.'")','datalever');
			if(strlen($lever)>0){
				$detail = explode(';',$lever);
				foreach ($detail as $v){
					$levers = explode(',', $v);
					$name = $this->mysql_model->get_row(MENU_DATA,'(status = 1) and id = '.$v[0],'surname');
					$leverdata[$name] = $levers;
				}
			}
		}else {
		    $leverdata = array();	
		}
// 		echo '<pre>';print_r($leverdata);die;

		$v = array();
		$data['status'] = 200;
		$data['msg']    = 'success';
		$list = $this->mysql_model->get_results(MENU_DATA,'(status=1) order by id');
		
		// 登录账号的子仓库信息
		$depot = $this->mysql_model->get_results(STORAGE,'(isDelete=0) and sid='.$this->jxcsys['sid'],'id,name');

		
		foreach ($list as $arr=>$row) {
			if($row['surname'] == 'ZCKKJ'){
				$v[$arr]['name'] 	= $row['name'];
				$v[$arr]['id'] 		= $row['id'];
				
				if(!empty($leverdata[$row['surname']])){
					foreach ($depot as $key => $value){
						if(in_array($value['id'], $leverdata[$row['surname']])){
							$depot[$key]['ischeck'] = 1;
						}else{
							$depot[$key]['ischeck'] = 0;
						}
					}
				}
				$v[$arr]['detail'] 	= $depot;
			}
		}
		
		$data['item'] = $v;
	    die(json_encode($data));
	}
	
	public function save() {
		$data = str_enhtml($this->input->get_post(NULL,TRUE));
		if (is_array($data)&&count($data)>0) {
			!isset($data['userName']) || strlen($data['userName'])<1 && str_alert(-1,'用户名不能为空');
			!isset($data['rightid']) && str_alert(-1,'参数错误');
			$sql = $this->mysql_model->update(ADMIN,array('datalever'=>$data['rightid']),'(username="'.$data['userName'].'")');
			if ($sql) {
				$this->common_model->logs('更新数据权限:'.$data['userName']);
				str_alert(200,'操作成功');
			}
			str_alert(-1,'操作失败');
		}
		str_alert(-1,'添加失败');
	}
	
}