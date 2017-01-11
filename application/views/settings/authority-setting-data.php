<?php $this->load->view('header');?>

<script type="text/javascript">
var DOMAIN = document.domain;
var WDURL = "";
var SCHEME= "<?php echo sys_skin()?>";
try{
	document.domain = '<?php echo base_url()?>';
}catch(e){
}
</script>

<style type="text/css">
	.grid-wrap{
		background-color: #fff;
		box-shadow: 0 0 8px 1px #ccc;
		overflow: auto;
	}
	.grid-wrap .m-box{
		margin: 0 10px;
		padding-bottom: 10px;
		height: auto;
		overflow: hidden;
		border-bottom: 1px solid #ccc;
	}
	.grid-wrap .m-box:last-child{
		border: none;
	}
	.grid-wrap .m-box .m-head{
		padding: 10px;
	}
	.grid-wrap .m-head h3{
		display: inline-block;
		padding: 0 4px 5px;
		margin-left: 10px;
	}
	.grid-wrap .m-box .m-content-checkbox{
		height: 40px;
		line-height: 40px;
	}
	.grid-wrap .m-content-checkbox label{
		margin-left: 46px;
	}
	.grid-wrap .m-content-checkbox .u-depotname{
		margin-left: 10px;
	}

</style>
</head>

<body>
<div class="wrapper">
  <div class="mod-toolbar-top cf">
    <div class="fl"><h3 class="f14">详细权限设置<span class="fwn">（请勾选为账号： <b id="userName"></b>/<b id="realName"></b> 分配的权限）</span></h3></div>
    <div class="fr"><a class="ui-btn ui-btn-sp mrb" id="save">确定</a><a class="ui-btn" href="<?php echo site_url('settings/authority')?>">返回</a></div>
  </div>
  <div class="grid-wrap">
  </div>
</div>
<script>
  	var urlParam = Public.urlParam(), userName = urlParam.userName,realName = urlParam.realName;
    $('#userName').text(userName); $('#realName').text(realName);
  	var height = Public.setGrid().h , width = Public.setGrid().w; 
  	$('.grid-wrap').css({width:width,height:height});

  	// 获取页面信息 admin表 data-lever 以;分隔不同权限 , 为已选目标id 
  	// Look at this! there is a pit. 子仓已选默认仓库 点击不可关闭 或 其他处理 等等
	Public.ajaxPost('../dataright/queryall?userName=' + userName, {}, function(data){
		 if(data.status === 200) {
		 	for (var i = 0; i < data.item.length; i++) {
		 		// console.log(data.item[i].detail);
		 		var box = $('<div class="m-box"></div>');
		 		var head = $('<div class="m-head">'+
					'<label><input class="group _ckbox" type="checkbox" id="'+data.item[i].id+'"><span class="text"></span></label> '+
					'<h3>'+data.item[i].name+'</h3>'+
					'</div>');
		 		var detail = data.item[i].detail;
		 		var content = $('<div class="m-content"><input name="'+data.item[i].id+'" type="checkbox" style="display:hidden" class="group ckbox" checked data-id="'+data.item[i].id+'"></div>')
		 		for (var j = 0; j < detail.length; j++) {
		 			var checkbox = $('<div class="m-content-checkbox"></div>');
		 			if(detail[j].ischeck == 1){
		 				var content_checkbox = $('<label class="fl"><input name="'+data.item[i].id+'" class="group ckbox" type="checkbox" data-for="'+data.item[i].id+'" data-id="'+detail[j].id+'"checked><span class="text"></span></label>');
		 			}else{
		 				var content_checkbox = $('<label class="fl"><input name="'+data.item[i].id+'" class="group ckbox" type="checkbox" data-for="'+data.item[i].id+'" data-id="'+detail[j].id+'"><span class="text"></span></label>');
		 			}
					var depotname =$('<h3 class="fl u-depotname">'+detail[j].name+'</h3>');
		 			checkbox.append(content_checkbox);
		 			checkbox.append(depotname);
					content.append(checkbox);
		 		}
				box.append(head);
				box.append(content);
		 		$('.grid-wrap').append(box);

		 		$(box).on('click', '.group', function(){
					var groupId = $(this).attr('id');
					if(this.checked) {
						$('.ckbox[data-for=' + groupId + ']').each(function(){
							this.checked = true;
						});	
					} else {
						$('.ckbox[data-for=' + groupId + ']').removeAttr('checked');
					};
			  	});
			  	$(box).on('click', '.ckbox', function(){
			  	 	var groupId = $(this).data('for');
	 				var $_group = $('.ckbox[data-for=' + groupId + ']');
					if($_group.length === $_group.filter(':checked').length) {
						$('#' + groupId)[0].checked = true;
					} else {
						$('#' + groupId).removeAttr('checked');
					};
			  	})
			  	$('.ckbox').click().click()
		 	}
		 }else{
			parent.Public.tips({type: 1, content : '获取页面信息失败'});
		 }
	})


  	$('#save').click(function(e){
	  	var items = [];
	  	var name = $('._ckbox:eq(0)').attr('id');
	  	$('.ckbox').each(function(i){
	  		if(name != $(this).attr('name')) items.push(';');
	    	if(this.checked) {
			 	items.push($(this).data('id'));
	      	}
	  		name = $(this).attr('name');
	  	});
	  	Public.ajaxPost('../dataright/save?userName=' + userName + '&rightid='+ items.join(','), {}, function(data){
		  	if(data.status === 200) {
			  	parent.Public.tips({content : '保存成功！'});
		  	} else {
			  parent.Public.tips({type: 1, content : data.msg});
		  	}
	  	});
  	});
	$(function(){
		Public.resizeGrid();
	})
</script> 
</body>
</html>