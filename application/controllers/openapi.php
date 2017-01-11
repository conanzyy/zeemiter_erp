<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Openapi extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->service('open_service');
		//$this->common_model->checkpurview();
		//$this->jxcsys = $this->session->userdata('jxcsys');
    }

    
	public function index(){
		$data = str_enhtml($this->input->post(NULL,TRUE));
		$this->open_service->parse($data);
	}
	
	
	/**
	 * 请求示例
	 */
	public function clientTest(){
		
		$result = http_client();
		
		echo json_encode($result);		
		
		
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */