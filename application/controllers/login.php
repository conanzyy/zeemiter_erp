<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }
    
    
    public function checkSession(){
    	if($this->common_model->is_login()){
    		die(responseJSON("success"));
    	}else{
    		die(responseJSON("error","",site_url('login')));
    	}
    }
	 
	public function index(){
		
		/* $data = array(
				"InventoryCode"=>'1002000026'
		);//接口参数
		$post_data['data'] = json_encode($data);
		$post_data['method'] = 'settlePrice';//接口方法
		$result = http_client($post_data,'NC');
		
		
		echo json_encode($result,JSON_UNESCAPED_UNICODE);
		 */
		
	    $data = str_enhtml($this->input->post(NULL,TRUE));
		if (is_array($data)&&count($data)>0) {
			
			
			if(!token($data['token'])){
				die(responseJSON("error","token验证错误"));
			}
			  
			!isset($data['username']) || strlen($data['username']) < 1 && die('用户名不能为空'); 
			!isset($data['userpwd'])  || strlen($data['userpwd']) < 1  && die('密码不能为空'); 
// 			if ($data['username']=='test1') {
// 			    $user = $this->mysql_model->get_row(ADMIN,'(username="'.$data['username'].'")');
// 				if (count($user)>0) {
// 						$data['jxcsys']['uid']      = $user['uid'];
// 						$data['jxcsys']['name']     = $user['name'];
// 						$data['jxcsys']['username'] = $user['username'];
// 						$data['jxcsys']['login']    = 'jxc'; 
// 						if (isset($data['ispwd']) && $data['ispwd'] == 1) {
// 							$this->input->set_cookie('username',$data['username'],3600000); 
// 							$this->input->set_cookie('userpwd',$data['userpwd'],3600000); 
// 						} 
// 						$this->input->set_cookie('ispwd',$data['ispwd'],3600000);
// 						$this->session->set_userdata($data);
// 						$this->common_model->logs('登陆成功 用户名：'.$data['username']);
// 						die('1'); 		
// 			   }
// 			}
			$user = $this->mysql_model->get_row(ADMIN,'(username="'.$data['username'].'") or (mobile="'.$data['username'].'") ');
			if (count($user)>0) {
			    $user['status']!=1 && die('账号被锁定'); 
				if ($user['userpwd'] == md5($data['userpwd'])) {
					$data['jxcsys']['uid']      = $user['uid'];
					$data['jxcsys']['name']     = $user['name'];
					$data['jxcsys']['username'] = $user['username'];
					$data['jxcsys']['roleid']   = $user['roleid'];
					$data['jxcsys']['sid']      = $user['sid'];
					$data['jxcsys']['stype']    = $user['service_type'];
					$data['jxcsys']['login']    = 'jxc';					
					
					$c_name = "";
					$c_pwd = "";
					if (isset($data['ispwd']) && $data['ispwd'] == 1) {
					    $c_name = $data['username'];
						$c_pwd =$data['userpwd'];
					}
					
					$this->input->set_cookie('username',$c_name,3600000);
					$this->input->set_cookie('userpwd',$c_pwd,3600000);
					$this->input->set_cookie('ispwd',$data['ispwd'],3600000);
					$this->session->set_userdata($data);
					$this->common_model->logs('登陆成功 用户名：'.$data['username']);
					die(responseJSON("success","登陆成功",site_url('home/index')));
			   }		
			}
			die(responseJSON("error","账号和密码不匹配，请重新输入！"));
		} else {
		    $this->load->view('login',$data);
		}
	}
	
	public function out(){
	    $this->session->sess_destroy();
		redirect(site_url('login'));
	}
	
	public function code(){
	    $this->load->library('lib_code');
		$this->lib_code->image();
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */