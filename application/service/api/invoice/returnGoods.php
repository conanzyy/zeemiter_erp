<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 采购订单退货申请处理 更改状态
 */
class returnGoods extends MY_Service {

	
    public function __construct(){
        parent::__construct();
    }
    
    
    /**
     *  NC  1：审核中   2：审核不通过     3：审核通过
     * 
     *  店管家  1待审核 2审核中 3审核未通过 4审核通过 
     */
    private $orderStatus = array (
    		'1' => '2',
    		'2' => '3',
    		'3' => '4',
    		'5' => '1'
    );
    
    public function service($params){
//     	echo '<pre>';print_r($params);die;
    	if(empty($params['nid']))  $this->_error('E0001','缺少参数：订单编号');
    	if(empty($params['type'])) $this->_error('E0001','缺少参数：订单状态');
    	
        $data['status']	= $this->orderStatus[$params['type']];
        $data['desc']	= $params['desc'];
        $this->db->trans_begin();

        $this->mysql_model->update(RETURN_GOODS,$data,'(rtOrderId="'.$params['nid'].'")');
        if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
        	$this->_error('E0001','更改订单状态失败');
        }else{
			$this->db->trans_commit();
        	$this->_success($params,'更改订单状态成功');
        }
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */