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
	rowData.id && ($("#number").val(rowData.area_code), $("#name").val(rowData.area_name),$("#desc").val(rowData.area_desc));
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
		addr = $.trim($("#desc").val()),
		isDefault = $.trim($("input[name=isDefault]:checked").val()),
		e = "add" == a ? "新增货位" : "修改货位";
	var inputData = [];
	$(".area").each(function(){
		inputData.push($(this).data('params'));
	})
	params = rowData.id ? {
		id: b,
		area_code: c,
		area_name: d,
		area_desc: addr,
		str_id   : str_id,
		isDefault: isDefault,
		inputData: inputData,
		isDelete : rowData["delete"]
	} : {
		area_code: c,
		area_name: d,
		area_desc: addr,
		str_id	 : str_id,
		isDefault: isDefault,
		inputData: inputData,
		isDelete: !1
	}, Public.ajaxPost("../basedata/invlocation/" + ("add" == a ? "add_area" : "update_area"), params, function(b) {
		200 == b.status ? (parent.parent.Public.tips({
			content: e + "成功！"
		}),b.data['delete'] = 0, callback && "function" == typeof callback && callback(b.data, a, window)) : parent.parent.Public.tips({
			type: 1,
			content: e + "失败！" + b.msg
		})
	})
}
function resetForm(a) {
	$("#manage-form").validate().resetForm(), $("#name").val(""), $("#number").val(Public.getSuggestNum(a.locationNo)).focus().select(),$("#desc").val("")
}
var api = frameElement.api,
	oper = api.data.oper,
	str_id = api.data.str_id,
	rowData = api.data.rowData || {},
	callback = api.data.callback;
initPopBtns(), initField(), initEvent();