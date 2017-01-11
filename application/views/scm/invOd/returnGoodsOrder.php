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

<link href="<?php echo base_url()?>statics/css/<?php echo sys_skin()?>/bills.css?ver=20150522" rel="stylesheet" type="text/css">
<style>
	em{ olor: #C81623;font-size: 19px}
	.fl{float: left;}.fr{float: right;}
	html,body{height: 100%;}
	.box{width: 100%;height: 100%;color: #999;background-color: #fff; }
	.main { padding: 50px 0 0;background-color: #fff;overflow: hidden;padding-bottom: 40px; }
	.main table{ border-collapse: collapse;width: 90%;margin: 10px auto;text-align: center; }
	.table-a thead td{ background-color: #e8e8e8; }
	.table-a td{ padding: 4px 3px; }
	.table-a td:nth-child(1){ width: 50%; }
	.table-a td:nth-child(2){ width: 15%; }
	.table-a td:nth-child(3){ width: 15%; }
	.table-a td:nth-child(3){ width: 20%; }
	.table-a,.table-a tr,.table-a td{ border:1px solid #ddd; }
	.btn{ padding: 8px 20px;border-radius: 3px;cursor:pointer;color: #fff;background-color: #F8AC59;display: inline-block;float: right;margin-right: 5%;margin-top: 20px; }
	.num_change{overflow: hidden;margin:0 auto;width: 120px;}
	.change_btn{width: 25px;height: 25px;line-height: 23px;border:1px solid #ddd;border-radius: 50%;cursor:pointer;-moz-user-select:none;}
	.change{width: 50px;height: 25px;border:1px solid #ddd;border-radius: 4px;margin-left: 7px;line-height: 25px;text-align: center;}
</style>
</head>
<body>
	<div class="box">
		<div class="main">
			<span>
				<div style="width: 90%;margin:0 auto;overflow: hidden;">
					<em class="fl" style="margin-left: 6%;">退货商品详情</em>
					<sapn class="fr">退货状态：<?php echo $status?></sapn> 
					<sapn class="fr">退货单号：<?php echo $rtOrderId?> &nbsp;&nbsp;&nbsp;&nbsp;</sapn>
				</div>

				<table class="table-a">
					<thead>
						<tr>
							<td>退货商品</td>
							<td>退货数量</td>
							<td>退货时间</td>
							<td>退货仓库</td>
						</tr>					
					</thead>
					<tbody>
						<?php  
							foreach ($detail as $key => $value) {
						?>
						<tr>
							<td style="display: none;">
								<label>
									<input type="checkbox" checked="checked" name="into_goods" invId=<?php echo $value['invId']?>>
									<span class="text"></span>
								</label>
							</td>
							<td><?php echo $value['number'] ." ".$value['brand_name']." ".$value['skuId']." ". $value['name']." ".$value['spec']?></td>
							<td><?php echo $value['rtNum']?></td>
							<td><?php echo dateTime($value['rttime'])?></td>
							<?php if($isOnle === 1){?>
								<td id='depot_<?php echo $value['invId']?>' value='<?php echo $depot['id']?>'>
									<?php echo $depot['name']?>
								</td>
							<?php }else if($isOnle === 2){?>
								<td id='depot_<?php echo $value['invId']?>'>
									请添加仓库
								</td>
							<?php }else{?>
								<td>
									<select id='depot_<?php echo $value['invId']?>'>
										<?php foreach ($depot as $v){?>
											<?php if($v['isDefault'] == 1){?>
											<option value="<?php echo $v['id']?>" selected><?php echo $v['name']?></option>
											<?php }else{?>
											<option value="<?php echo $v['id']?>"><?php echo $v['name']?></option>
											<?php }?>
										<?php }?>
									</select>
								</td>
							<?php }?>
						</tr>
						<?php 
							}
						?>
					</tbody>
				</table>
				<?php if($statusNum == 4 && $isRt == 0){?>
					<div class="btn close">确认退货</div>
				<?php }?>
			</span>
		</div>
	</div>
</body>
<script type="text/javascript">
	$(function(){
		$('.close').on('click',function(){
			var data=[];
			$('input[name="into_goods"]:checked').each(function (){
				var into_goods = {};
				var id = $(this).attr('invId');
				into_goods.id=id;
				var tagName = $('#depot_'+id).get(0).tagName;
				if(tagName == 'TD'){
					if($('#depot_'+id).attr('value')){
						into_goods.ck=$('#depot_'+id).attr('value');
					}
				}else{
					into_goods.ck=$('#depot_'+id+' option:selected').val();
				}
				data.push(into_goods);
			})
			if(data.length != 0){
				for (var i=0;i<data.length;i++){
					if(!data[i].ck) {
						parent.Public.tips({type:1,content:'请先添加仓库'});
						return;
					}
				}
				$.dialog.confirm("您确定退货",
					function (){
						Public.ajaxGet("../scm/invOd/returnForm?action=returnForm",{
							data: data,rtId:<?php echo $rtId?>
						},function (data){
							200 === data.status ?  parent.Public.tips({content: "退货成功！"}):parent.Public.tips({type:1,content:data.msg});
							window.location.reload();
						})
					}
				)
			}

		})
	})
</script>
</html>


