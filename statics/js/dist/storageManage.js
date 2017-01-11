//var inputData = [];
//var n = 0;
$('.jia').on('click',function(){
	if($(".add_input .ui-input").length > 0){
		$(".add_input .ui-input").focus();
		return;
	} 
	var inputHtml = $('<input style=width:88px;float:left;margin-right:10px;margin-top:3px; class="ui-input end_input" type=text >');
	inputHtml.on('blur',function(){
		var inputVal =$(this).val();
		if(inputVal == ''){
			$('.add_input').empty();
			return;
		}
		if(isExists(inputVal)) {
			inputHtml.focus();
			return;
		}  
		
		//inputData.push({'area_name':inputVal});
		$wapper = $('<div class="l_label area" data-params='+JSON.stringify({'name':inputVal})+'></div>');
		$close = $('<em style="position: absolute;display: inline-block;width: 20px;height: 20px;background:url(../../statics/css/img/cha.png) no-repeat center center;cursor:pointer;top: -3px;right: -5px;"></em>');
		$close.on("click",function(){
			$_parent = $(this).parent();
			$_parent.remove();
		});
		
		$wapper.append($close).append(inputVal);
		
		$('.add_input').before($wapper);
		$('.add_input').empty();

	});
	$('.add_input').prepend(inputHtml);
	inputHtml.focus();

})
function isExists(inputVal){
	$area = $(".area");
	$bool = false;
	$area.each(function(){
		$data = $(this).data('params');
		if($data.name == inputVal){
			parent.Public.tips({
				type: 1,
				content: "仓库区域名称重复！"
			})
			$bool = true;
		}
	})
	
	return $bool;
}
function initField() {
	if(rowData['arealist']){
		var area = rowData['arealist'];
		for(var i=0;i<area.length;i++){
			
			$wapper = $('<div class="l_label area" data-params='+JSON.stringify(area[i])+'></div>');
			$close = $('<em style="position: absolute;display: inline-block;width: 20px;height: 20px;background:url(../../statics/css/img/cha.png) no-repeat center center;cursor:pointer;top: -3px;right: -5px;"></em>');
			//div += '<div class="l_label" id="l_label_'+i+'"><em style="position: absolute;display: inline-block;width: 20px;height: 20px;background:url(../../statics/css/img/cha.png) no-repeat center center;cursor:pointer;top: -3px;right: -5px;" index='+i+' cid='+area[i]['id']+' str_id='+area[i]['str_id']+'></em>'+area[i]['name']+'</div>';
			$close.on("click",function(){
				$_parent = $(this).parent();
				$params = $_parent.data("params");
				
				if(!$params.cid){
					$_parent.remove();
				}
				
				Public.ajaxPost("../basedata/invlocation/area_check", {
					locationId: $params.str_id,
					area_id: $params.id,
				}, function(b) {
					b && 200 == b.status 
					? (
//						parent.Public.tips({
//							content: "仓库区域删除成功！"
//						}),
						
						$_parent.remove(),callback && "function" == typeof callback && callback(b.data, 'edit')
						
						
					) 
					: parent.Public.tips({
						type: 1,
						content: "仓库区域删除失败！" + b.msg
					})
				})			
			});
			$wapper.append($close).append(area[i].name);
			
			$('#click_monitor').prepend($wapper);
		}
	}
	rowData.id && ($("#number").val(rowData.locationNo), $("#name").val(rowData.name),$("#address").val(rowData.address));
	$("input[name=isDefault][value="+(rowData.isDefault || 0)+"]").prop('checked',"checked")
}
function initEvent() {
	var a = $("#number");
	Public.limitInput(a, /^[a-zA-Z0-9\-_]*$/), Public.bindEnterSkip($("#manage-wrap"), postData, oper, rowData.id), initValidator(), a.focus().select()
}
function initPopBtns() {
	$("#number").val(Public.getSuggestNum($("#preNum").val()));
	var a = "add" == oper ? ["保存", "关闭"] : ["确定", "取消"];
	api.button({
		id: "confirm",
		name: a[0],
		focus: !0,
		callback: function() {
			return postData(oper, rowData.id), !1
		}
	}, {
		id: "cancel",
		name: a[1]
	})
}
function initValidator() {
	$.validator.addMethod("number", function(a) {
		return /^[a-zA-Z0-9\-_]*$/.test(a)
	}), 
	$("#manage-form").validate({
		rules: {
			number: {
				required: !0,
				number: !0
			},
			name: {
				required: !0
			}
		},
		messages: {
			number: {
				required: "仓库编号不能为空",
				number: "仓库编号只能由数字、字母、-或_等字符组成"
			},
			name: {
				required: "仓库名称不能为空"
			}
		},
		errorClass: "valid-error"
	})
}
function postData(a, b) {
	if (!$("#manage-form").validate().form()) return void $("#manage-form").find("input.valid-error").eq(0).focus();
	var c = $.trim($("#number ").val()),
		d = $.trim($("#name").val()),
		addr = $.trim($("#address").val()),
		isDefault = $.trim($("input[name=isDefault]:checked").val()),
		e = "add" == a ? "新增仓库" : "修改仓库";
	var inputData = [];
	$(".area").each(function(){
		inputData.push($(this).data('params'));
	})
	params = rowData.id ? {
		locationId: b,
		locationNo: c,
		name: d,
		address:addr,
		isDefault:isDefault,
		inputData : inputData,
		isDelete: rowData["delete"]
	} : {
		locationNo: c,
		name: d,
		address:addr,
		isDefault:isDefault,
		inputData : inputData,
		isDelete: !1
	}, Public.ajaxPost("../basedata/invlocation/" + ("add" == a ? "add" : "update"), params, function(b) {
		200 == b.status ? (parent.parent.Public.tips({
			content: e + "成功！"
		}),b.data['delete'] = 0, callback && "function" == typeof callback && callback(b.data, a, window)) : parent.parent.Public.tips({
			type: 1,
			content: e + "失败！" + b.msg
		})
	})
}
function resetForm(a) {
	$("#manage-form").validate().resetForm(), $("#name").val(""), $("#number").val(Public.getSuggestNum(a.locationNo)).focus().select(),$('.area').remove()
}
var api = frameElement.api,
	oper = api.data.oper,
	rowData = api.data.rowData || {},
	callback = api.data.callback;
initPopBtns(), initField(), initEvent();