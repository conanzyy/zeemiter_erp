<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Import extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
        $this->load->library('excel/excel');
		$this->load->helper('download');
        $this->jxcsys  = $this->session->userdata('jxcsys');
    }
	
	//客户
	public function downloadtemplate1() {
		$info = read_file('./data/download/customer.xls');
		$this->common_model->logs('下载文件名:customer.xls');
		force_download('customer.xls', $info); 
	}
	
	//供应商
	public function downloadtemplate2() {
		$info = read_file('./data/download/vendor.xls');
		$this->common_model->logs('下载文件名:vendor.xls');
		force_download('vendor.xls', $info); 
	}
	
	//商品
	public function downloadtemplate3() {
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
        $objPHPExcel = new PHPExcel();
        $iofactory = new IOFactory();
        $this->excel->setOutputEncoding('utf-8');
        $this->excel->read('./data/download/goods.xls');
        $list = $this->excel->sheets[0]['cells'];
        $contact = $this->mysql_model->get_results(CATEGORY,'(isDelete=0) and typeNumber = "customertype" and status = 1 and sid = '.$this->jxcsys['sid']);
        foreach($contact as $k=>$v){
            $list[1][] = $v['name'];
        }
//        print_R($list[1]);die;
        $list = $list[1];
        foreach ($list as $key=>$value){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(64 + $key) . 1,$value);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(chr(64 + $key))->setWidth(20);
        }
        $objPHPExcel->getActiveSheet() -> setTitle('goods');
        $objPHPExcel-> setActiveSheetIndex(0);
        $objWriter = $iofactory -> createWriter($objPHPExcel, 'Excel5');
        $dir = './data/download/'.$this->jxcsys['sid'];
        $filename = 'goods_'.$this->jxcsys['sid'].time().'.xls';
        if (!file_exists($dir)){ mkdir ($dir,0775); }
        $objWriter -> save($dir.'/'.$filename);
        $info = read_file($dir.'/'.$filename);
		$this->common_model->logs('下载文件名:'.$filename);
		force_download('goods_'.time().'.xls', $info);
	}

    public function downloadtemplate4() {
        $info = read_file('./data/download/goodsPrice.xls');
        $this->common_model->logs('下载文件名:goodsPrice.xls');
        force_download('goodsPrice.xls', $info);
    }

    //客户
    public function downloadtemplate5() {
        $info = read_file('./data/download/customer.xls');
        $this->common_model->logs('下载文件名:customer.xls');
        force_download('customer.xls', $info);
    }

	
	public function downloadtemplate(){
		
		$temp = $this->input->get_post("t",null);
		
		switch ($temp) {
			case "areaImport":
				$t_name = 'allocation';
				break;
			case "goodsInitImport":
				$t_name = 'goodsInit';
				break;
			case "storageRelImport":
				$t_name = 'storageRel';
				break;
			default:
				;
			break;
		}
		
		$info = read_file('./data/download/'.$t_name.'.xls');
		$this->common_model->logs('下载文件名:'.$t_name.'.xls');
		force_download($t_name.'.xls', $info);
	}
	
	//客户导入
	public function findDataImporter() {
	    die('{"status":200,"msg":"success"}');  
	}
	
	//上传文件
	public function upload() {
		die('{"status":200,"msg":"success","data":{"items":[{"id":1294598139109696,"date":"2015-04-25 14:41:35","uploadPath"
:"customer_20150425024011.xls","uploadName":"customer_20150425024011.xls","resultPath":"uploadfiles/88887901
/customer_20150425024011.xls","resultName":"customer_20150425024011.xls","resultInfo":"商品导入完毕。<br/>商
品一共：0条数据，成功导入：0条数据，失败：0条数据。<br/>供应商导入完毕。<br/>供应商一共：0条数据，成功导入：0条数据，失败：0条数据。<br/>客户导入完毕。<br/>客户一共：10条数
据，成功导入：10条数据，失败：0条数据。<br/>","status":2,"spendTime":0},{"id":1294598139109659,"date":"2015-04-25 14:40
:49","uploadPath":"customer_20150425024011.xls","uploadName":"customer_20150425024011.xls","resultPath"
:"uploadfiles/88887901/customer_20150425024011.xls","resultName":"customer_20150425024011.xls","resultInfo"
:"商品导入完毕。<br/>商品一共：0条数据，成功导入：0条数据，失败：0条数据。<br/>供应商导入完毕。<br/>供应商一共：0条数据，成功导入：0条数据，失败：0条数据。<br/>客户导入完毕
。<br/>客户一共：10条数据，成功导入：10条数据，失败：0条数据。<br/>","status":2,"spendTime":0},{"id":1294597559113847,"date":"2015-04-17
 16:54:39","uploadPath":"蓝港新系统xls.xls","uploadName":"蓝港新系统xls.xls","resultPath":"uploadfiles/88887901
/蓝港新系统xls.xls","resultName":"蓝港新系统xls.xls","resultInfo":"商品导入完毕。<br/>商品一共：557条数据，成功导入：0条数据，失败：557条数据
。<br/>(请检查模板是否匹配，建议重新下载模板导入)<br/>供应商导入完毕。<br/>供应商一共：0条数据，成功导入：0条数据，失败：0条数据。<br/>客户导入完毕。<br/>客户一共：0条数
据，成功导入：0条数据，失败：0条数据。<br/>","status":2,"spendTime":0}],"totalsize":3}}');  
	}
	
 
	
	 
	
	

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */