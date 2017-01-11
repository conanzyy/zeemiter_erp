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
<style type="text/css">
	*{ box-sizing: border-box;-moz-box-sizing: border-box;-webkit-box-sizing: border-box; }
	.content{ width: 90%;height: 380px;border: 1px solid #999;margin: 8px auto;background-color: #fff;border-radius: 6px;background-clip: padding-box;outline: 0;box-shadow: 0 5px 15px rgba(0,0,0,0.5) }
	.header{ padding: 12px;border-bottom: 1px solid #e5e5e5;font-size: 18px;text-align: center; }
	
	.cat_select{ overflow: hidden;border: 1px solid #ccc;color: #555; }
	.list_group{ overflow: hidden;float: left; }


	.list_group_select{ height: 300px;border-right: 1px solid #ccc;overflow: auto; }
	.list_group_item{ display: block;padding: 10px 15px;font-size: 14px;cursor: pointer; }
	.list_group_item_haschild:before{ content: '';float: right;width: 6px;height: 6px;overflow: hidden;border-right: 1px solid #888888;border-top: 1px solid #888888;margin-top: calc(6px);transform: rotate(45deg); }
	.list_group_item:hover,.list_group_select .active{ background-color: #f5f5f5; }

	.goods_show{ padding: 10px 15px;font-size: 12px;overflow: hidden;cursor:pointer;border-bottom: 1px solid #f5f5f5}
	.goods_show span{ width: 75%;display: block;height: 21px;overflow: hidden;float: left; }
	.goods_show em{ float: right;line-height: 21px; }
	
	.list_group_show{ display: block;padding: 10px 15px;font-size: 14px;text-align: center;border-bottom: 1px solid #eee;line-height: 21px; }
	.list_group_show em{ float: right;margin-top: 3px;cursor: pointer; }

	.group_money{ padding: 10px 15px;margin-left: calc(50% - 190px) }
	.input{ border: none;border-bottom: 1px solid #888;width: 80px;font-size: 14px;outline: none;padding-left: 5px; }
	.btn{ width: 50px;height: 30px;border: 1px solid #eee;border-radius: 6px;text-align: center;line-height: 28px;cursor: pointer;margin-left: 100px;background-color: #f8ac59;color: #fff }
</style>
</head>
<body>
	<div class="pct75 fl">
		<div class="modal_content content">
			<div class="modal_header header">选择类目</div>
			<div class="modal_body p15">
				<div class="cat_select">
					<div class="pct22 list_group">
						<div class="list_group_select level_1">
							<?php foreach ($category as $k => $v) {?>
								<?php if($v['haschild'] == '0'){ ?>
									<em class="list_group_item" rel="<?php echo $v['id'] ?>"><?php echo $v['name'] ?></em>
								<?php }else{ ?>
									<em class="list_group_item list_group_item_haschild" rel="<?php echo $v['id'] ?>"><?php echo $v['name'] ?></em>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
					<div class="pct22 list_group">	
						<div class="list_group_select level_2">
						</div>
					</div>
					<div class="pct22 list_group">
						<div class="list_group_select level_3">							
						</div>							
					</div>
					<div class="pct34 list_group">
						<div class="list_group_select level_4" style="border:none;">
						</div>							
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="pct22 fl">
		<div class="content">
			<div class="header">已选择类目</div>
			<div class="modal_body">
				<div class="list_group pct100">
					<div class="list_group_showbox" style="overflow: auto;height: 300px;">
					</div>	
					
				</div>
			</div>
		</div>
	</div>
	<div class="pct100 fl">
		<div class="content" style="height: 115px;width: 93%;">
			<div class="header" style="padding: 7px;">预付款金额</div>
			<div class="modal_body" style="margin-top: 15px;overflow: hidden;">
				<div class="group_money fl">
					输入预付款金额：<input type="text" class="input"> 元
				</div>
				<div class="btn fl">确认</div>
			</div>
		</div>
	</div>
</body>
	<script type="text/javascript">
		var preMoney,preCategory=[];

		$(function(){
			<?php if($preMoney) {?>
				$('.input').val(<?php echo $preMoney ?>);
				preMoney = <?php echo $preMoney ?>;
			<?php } ?>
			<?php if($category_has) {?>
				<?php foreach ($category_has as $key => $value) {?>
					preCategory.push(String(<?php echo $value['id'] ?>));
					var showNodes = $('<span class="list_group_show" rel="<?php echo $value['id'] ?>"><?php echo $value['name'] ?><em class="close fa fa-close"></em></span>');
					$('.list_group_showbox').append(showNodes);
					showNodes.children().on('click',function(){ 
						var cateId = $(this).parent().attr('rel');
						var index = $.inArray(cateId,preCategory);
						preCategory.splice(index,1);
						$(this).parent().remove();
					});
				<?php } ?>
			<?php } ?>
		})

		$('.list_group_item').on('click',function(e){
			e.stopPropagation();
			var _this = $(this);
			getNode(_this);
		})

		function getNode (_this){
			_this.addClass('active').siblings().removeClass('active'); 
			var rel = _this.attr('rel');

			Public.ajaxPost("../scm/invPre/category?action=category",{
				rel: rel
			},function (data){
				if(data.level == 4){

					var Pclass = _this.parent().attr('class').split(' ')[1].split('');
					var list = parseInt(Pclass[Pclass.length-1]) + 1;
					$('.level_'+list).empty().parent().next().children().empty().parent().next().children().empty();

					for (var i = 0; i < data.goodsList.length; i++) {
						var node = $('<div class=goods_show><span title="'+data.goodsList[i].title+'">'+data.goodsList[i].title+'</span><em>￥'+data.goodsList[i].settlePrice+'</em></div>');
						$('.level_' + data.level).append(node);
					}

					var isHave = $.inArray(data.nextCategory.id,preCategory);
					if(isHave == -1){
						preCategory.push(data.nextCategory.id);
						var showNode = $('<span class="list_group_show" rel="'+data.nextCategory.id+'">'+data.nextCategory.name+'<em class="close fa fa-close"></em></span>');
						$('.list_group_showbox').append(showNode);
						showNode.children().on('click',function(){ 
							var cateId = $(this).parent().attr('rel');
							var index = $.inArray(cateId,preCategory);
							preCategory.splice(index,1);
							$(this).parent().remove();
						});
					}
				}else{
					for (var i = 0; i < data.nextCategory.length; i++) {
						if(data.nextCategory[i].haschild == '0'){
							var node = $('<em class="list_group_item" rel="'+data.nextCategory[i].id+'">'+data.nextCategory[i].name+'</em>');
						}else{
							var node = $('<em class="list_group_item list_group_item_haschild" rel="'+data.nextCategory[i].id+'">'+data.nextCategory[i].name+'</em>');
						}
						if(i == 0) {
							$('.level_' + data.level).empty();
							if( data.level == 2 || data.level == 3) {
								var newlevel = parseInt(data.level) + 1;
								$('.level_' + newlevel).empty().parent().next().children().empty()
							}
						}
						$('.level_' + data.level).append(node);
						node.click(function(){
							var _this = $(this);
							getNode(_this);
						})
					}
				}
			})
		}

		$('.close').on('click',function(){
			$(this).parent().remove();
		})

		$('.input').change(function(){
			var _this = $(this);
			var isNum = isNaN(_this.val());
			if(isNum){
				parent.Public.tips({type:1,content:'请输入正确的金额！'});
				_this.val('');
			}else if(_this.val() == '0'){
				parent.Public.tips({type:1,content:'输入的金额不能为0！'});
				_this.val('');
			}else{
				preMoney = parseInt(_this.val());
			}
		})

		$('.btn').on('click',function(){
			if(preCategory.length == 0) {parent.Public.tips({type:1,content:'请选择需要锁定商品价格的类别'});return;}

			if(!preMoney) {parent.Public.tips({type:1,content:'请输入预付款金额'});return}
			var data = {};
			data.preMoney 	 = preMoney;
			data.preCategory = preCategory;
			<?php if($id) {?>
				data.id = <?php echo $id ?>;
			<?php } ?>
			Public.ajaxPost("../scm/invPre/savePreOrder?action=savePreOrder",{
				data: data
			},function (a){
				200 === a.status ? (setTimeout(function(){ 
				parent.tab.addTabItem({
					tabid: 'preorder-preOrderQuery',
					text: '预订单查询',
					url: "../scm/invPre?action=preOrderQuery&typeNumber=allStatus",
				});parent.closeTab("preorder-preOrder");
				},500),parent.Public.tips({content: "确认预订单成功！"})) 
				: parent.Public.tips({type: 1,content: a.msg}),
				a.msg == '该预订单已审核完成，不能再更改' ? (parent.getIframe("preorder-preOrderQuery")[0].contentWindow.refirsh(),
				parent.tab.addTabItem({
					tabid: 'preorder-preOrderQuery',
					text: '预订单查询',
					url: "../scm/invPre?action=preOrderQuery&typeNumber=allStatus",
				}),parent.closeTab("preorder-preOrder")) : '';
			})
		})
	</script>
</html>


