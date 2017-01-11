<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Inventory extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
        $this->load->library('lib_pinyin');
        $this->jxcsys  = $this->session->userdata('jxcsys');
        $this->system  = $this->common_model->get_option('system');
    }
	
    //库存查询
	public function index() {
		
		$action = $this->input->get('action',TRUE);
		switch ($action) {
			case 'listAll':
				$this->load->view('inventoryAll');	
				break;
			case 'orderList':
				$this->load->view('scm/invPu/queryOrder');
			break;
			default:
				$this->load->view('inventory');	
		}
	}

	public function detail() {
        $res = array();
        $invId = $_GET['invId'];
        
        $inventory = $this->data_model->get_invoice_info_inventory();
        $goods = $this->db->query('select * from '.GOODS.' where id = '.$invId)->row_array();
        
        
        
        
       	
        if($inventory[$invId]){
        	foreach($inventory[$invId] as $k=>$item) {
        		$temp['goodsName'] = $goods['productCode'] . " " . $goods['brand_name'] . " " . $goods['skuId']. " " . $goods['name'] . " " . $goods['spec'];
        		$sInfo = $this->db->query('select * from ' . STORAGE . ' where id =' . $k)->row_array();
        		$temp['storageName'] = $sInfo['name'];
        		$temp['num'] = $inventory[$invId][$k];
        		$res[] = $temp;
        	}
        }else{
        	$storage = $this->mysql_model->get_results(STORAGE,'sid =' . $this->jxcsys['sid']);
        	foreach($storage as $k=>$item) {
        		$temp['goodsName'] = $goods['productCode'] . " " . $goods['brand_name'] . " " . $goods['skuId']. " " . $goods['name'] . " " . $goods['spec'];
        		$temp['storageName'] = $item['name'];
        		$temp['num'] = 0;
        		$res[] = $temp;
        	}
        }
        
        
        $data['res'] = $res;
        $data['goods'] = $goods;
        $result = $this->db->query('select * from '.INVOICE_INFO.' where (isDelete=0) and transType = "150601" and invId = '.$invId.' order by id desc limit 0,8')->result_array();
        $result1 = $this->db->query('select * from '.INVOICE_INFO.' where (isDelete=0) and transType = "150501" and invId = '.$invId.' order by id desc limit 0,8')->result_array();
        if($result) {
            foreach ($result as $arr => $v) {
                $contact = $this->db->query('select * from ' . CONTACT . ' where id = ' . $v['buId'])->row_array();
                $result[$arr]['contactName'] = $contact['name'];
                $v1 = $v['billDate'];
                $time[] = "'$v1'";
                $price[] =$v['price'];
            }
            
            $data['time'] = implode(",",$time);
            $data['price'] = implode(",",$price);
            $data['result'] = $result;
        }
        if($result1) {
            foreach ($result1 as $key => $vo) {
                $contact = $this->db->query('select * from ' . CONTACT . ' where id = ' . $vo['buId'])->row_array();
                $result1[$key]['contactName'] = $contact['name'];
                $v2 = $vo['billDate'];
                $time1[] = "'$v2'";
                $price1[] =$vo['price'];
            }
            $data['times'] = implode(",",$time1);
            $data['prices'] = implode(",",$price1);
            $data['result1'] = $result1;
        }
        $data['name'] = $_GET['name'];
        $this->load->view('detail',$data);
    }
}
