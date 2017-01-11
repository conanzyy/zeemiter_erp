<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class OrderType extends CI_Controller {
	
    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
    }
    
    
    
    
    
   public function index(){
    	$action = $this->input->get('action',TRUE);
    	switch ($action) {
    		case 'orderType':
    			$this->getOrderType();
    			break;
    		default:
    			$this->getOrderType();
    	}
    }



    public function getOrderType(){
    	
    	$data['status']  = 200;
    	$data['data']['items']   = $this->data_model->getOrderType();
    	die(json_encode($data));
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */