<?php $this->load->view('header');?>
 
<script type="text/javascript">
var DOMAIN = document.domain;
var WDURL = "";
var SCHEME= "<?php echo sys_skin()?>";
try{
	document.domain = '<?php echo base_url()?>';
}catch(e){
}
//ctrl+F5 增加版本号来清空iframe的缓存的
$(document).keydown(function(event) {
	/* Act on the event */
	if(event.keyCode === 116 && event.ctrlKey){
		var defaultPage = Public.getDefaultPage();
		var href = defaultPage.location.href.split('?')[0] + '?';
		var params = Public.urlParam();
		params['version'] = Date.parse((new Date()));
		for(i in params){
			if(i && typeof i != 'function'){
				href += i + '=' + params[i] + '&';
			}
		}
		defaultPage.location.href = href;
		event.preventDefault();
	}
});
</script>
<style>
	*{ box-sizing: border-box;-moz-box-sizing: border-box;-webkit-box-sizing: border-box; }
	.red { color: red; }
	.modal_content .bt{ border-bottom:1px solid #ccc; }
	.modal_content{ width: 95%;height: 380px;border: 1px solid #999;margin: 8px auto;background-color: #fff;border-radius: 6px;background-clip: padding-box;outline: 0;box-shadow: 0 5px 15px rgba(0,0,0,0.5);position: relative; }
	.modal_header{ padding: 12px;border-bottom: 1px solid #e5e5e5;font-size: 18px;text-align: center; }
	.modal_body_header{ padding:4px 20px; }
	.modal_body_header span{ line-height:35px;margin-right:10px;font-size:14px; }
	
	.cat_select{ overflow: hidden;border: 1px solid #ccc;color: #555; }
	.list_group{ overflow: hidden;float: left;border-right: 1px solid #ccc; }
	.list_group_box{ display: block;overflow: hidden; }
	
	.list_group_title{ text-align:center;display:block;padding: 4px 0;height: 30px;cursor: pointer; line-height: 22px;}
	.list_group_title:hover { background-color: #f5f5f5; }
	.list_group_select{ height: 270px;overflow: auto; }

</style>
<link href="<?php echo base_url()?>statics/css/<?php echo sys_skin()?>/bills.css?ver=20150522" rel="stylesheet" type="text/css">
</head>
<body>
	<div class="pct100 fl">
		<div class="modal_content" style="height: auto;width:97%;">
			<div class="modal_header" style="padding: 7px;">订单详情</div>
			<div class="modal_body_header">
				<span>预订单单号：<?php echo $billNo ?></span>
				<span>制单人：<?php echo $name ?></span>
				<span>状态：<?php echo $status ?></span>
				<br>
				<span>下单时间：<?php echo $billDate ?></span>
				<?php if($auditDate) {?>
				<span>审核时间：<?php echo $auditDate ?></span>
				<?php } ?>
				<span>预存金额：<em class="red"><?php echo $quota ?></em></span>
				<span>剩余金额：<em class="red"><?php echo $laveQuota ?></em></span>
			</div>
		</div>
	</div>
	<div class="pct45 fl">
		<div class="modal_content">
			<div class="modal_header">预订单商品列表</div>
			<div class="modal_body p15">
				<div class="cat_select">
					<div class="pct100 list_group" style="border-right: none;">
						<div class="list_group_select hasSelect" style="height: 300px">
							<span class="list_group_box">
								<em class="list_group_title bt pct75 fl">商 品</em>
								<em class="list_group_title bt pct25 fl">拍 照 价</em>
							</span>
							<?php foreach ($goodsList as $k => $v) {?>
							<span class="list_group_box">
								<em class="list_group_title pct75 fl" title="<?php echo $v['name'] ?>"><?php echo $v['name'] ?></em>
								<em class="list_group_title pct25 fl">￥ <?php echo $v['price'] ?></em>
							</span>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="pct55 fl">
		<div class="modal_content">
			<div class="modal_header">消费明细</div>
			<div class="modal_body p15">
				<div class="cat_select">
					<div class="pct100 list_group" style="border-right: none;">
						<div class="list_group_select hasSelect" style="height: 300px">
							<span class="list_group_box">
								<em class="list_group_title bt pct30 fl">采购单号</em>
								<em class="list_group_title bt pct45 fl">商 品</em>
								<em class="list_group_title bt pct10 fl">数 量</em>	
								<em class="list_group_title bt pct15 fl">采购时间</em>
							</span>
							<?php foreach ($cgList as $k => $v) {?>
							<span class="list_group_box">
								<em class="list_group_title pct30 fl"><?php echo $v['cgoId'] ?></em>
								<em class="list_group_title pct45 fl" title="<?php echo $v['name'] ?>"><?php echo $v['name'] ?></em>
								<em class="list_group_title pct10 fl"><?php echo $v['num'] ?></em>
								<em class="list_group_title pct15 fl" title="<?php echo $v['cgtime'] ?>"><?php echo $v['cgtime'] ?></em>
							</span>
							<?php } ?>
						</div>
					</div>
				</div>  
			</div>
		</div>
	</div>
</body>
</html>