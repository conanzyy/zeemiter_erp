<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 采购订单状态修改
 *
 */
class statusUpdate extends MY_Service {

	
    public function __construct(){
        parent::__construct();
    }
    
    
    /**
     *  NC  1：审核中   2：审核不通过     3：审核通过
     * 
     *  店管家  1待审核 2审核中 3审核未通过 4审核通过 
     */
    private $orderStatus = array (
    		'0' => '1',
    		'1' => '2',
    		'2' => '3',
    		'3' => '4',
    		'5' => '1',
    );
    
    public function service($params){
    	
    	if(empty($params['nid']))  $this->_error('E0001','缺少参数：订单编号');
    	if(empty($params['type'])) $this->_error('E0001','缺少参数：订单状态');
    	
        //转 nc订单号 为 erp订单号
        $iid = $this->mysql_model->get_row(INVOICE_ORDER,'(nid="'.$params['nid'].'") and isDelete = 0','id');
        $orderStatus = $this->orderStatus[$params['type']];
        $auditOpinion = $params['desc'];

        $data['orderStatus']	= $orderStatus;
        $data['auditOpinion']	= $auditOpinion;

        $this->db->trans_begin();
        $this->mysql_model->update(INVOICE_ORDER,$data,'(id='.$iid.')');
        
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