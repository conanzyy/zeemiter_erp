/*
 * 获取客户类别 并添加相关客户类别的 零售价
 */

function init_category(rowData){
	$("#salePrice").text("￥"+Public.numToCurrency(rowData['salePrice']));
	$(".lsj").val(Public.numToCurrency(rowData['salePrice']));
	var retailPrice = $.parseJSON(rowData.retailPrice);
	for(var o in retailPrice){
		var price = retailPrice[o];
		$("#lsj_"+o).val(Public.numToCurrency(retailPrice[o]));
	}
}
/*
 * 获取 客户类别 零售价
 */
function get_khdata(){
	var lsj_data={};
	$('.lsj').each(function(i){
		var lsj_each = $(this).val();
		var lsj_khid = $(this).attr('kh_id');
		lsj_data[lsj_khid] = Public.currencyToNum(lsj_each);
	})	
	return lsj_data;
}

function init() {
	void 0 !== cRowId ? Public.ajaxPost("../basedata/inventory/query?action=query", {
		id: cRowId
	}, function(a) {
		200 === a.status ? (rowData = a.data, initEvent(),init_category(rowData)) : parent.parent.Public.tips({
			type: 1,
			content: a.msg
		})
	}) : (initEvent())
}
function initPopBtns() {
	var a = "add" == oper ? ["保存", "关闭"] : ["确定", "取消"];
	api.button({
		id: "confirm",
		name: a[0],
		focus: !0,
		callback: function() {
			return $form.trigger("validate"), !1
		}
	}, {
		id: "cancel",
		name: a[1]
	})
}
function postCustomerData() {
	if ("add" == oper) {
		cancleGridEdit();
		var a = $("#name").val();
		Public.ajaxPost("../basedata/inventory/checkName?action=checkName", {
			name: a
		}, function(b) {
			-1 == b.status ? $.dialog.confirm('商品名称 "' + a + '" 已经存在！是否继续？', function() {
				postData()
			}) : postData()
		})
	} else postData()
}
function postData() {
	var khdata = get_khdata();
	var khdatas = JSON.stringify(khdata);
	var data = {id: rowData.id , retailPrice : khdatas};
	
	Public.ajaxPost("../basedata/inventory/updata_price", data, function(d) {
		if (200 == d.status) {
			parent.parent.Public.tips({
				content: "配置价格成功！"
			});
			if (callback && "function" == typeof callback) {
				callback(window)
			}
		} else parent.parent.Public.tips({
			type: 1,
			content: "配置价格失败！" + d.msg
		})
	})
}

function getTempData(a) {
	var b, c, d, e, f = 0,
		g = 0,
		h = a.propertys;
	c = categoryTree.getText() || "", unitData[a.baseUnitId] && (d = unitData[a.baseUnitId].name || "");
	for (var i = 0; i < h.length; i++) h[i].quantity && (f += h[i].quantity), h[i].amount && (g += h[i].amount);
	return f && g && (e = g / f), b = $.extend({}, a, {
		categoryName: c,
		unitName: d,
		quantity: f,
		unitCost: e,
		amount: g
	})
}

function initValidator() {
	var a = /[^\\<\\>\\&\\\\\']+/;
	$form.validator({
		valid: function() {
			postCustomerData();
		}
	})
}


function initEvent() {
	$(".money").keypress(Public.numerical).focus(function() {
		var a = $(this);
		this.value = Public.currencyToNum(this.value), setTimeout(function() {
			a.select()
		}, 10)
	}).blur(function() {
		this.value = Public.numToCurrency(this.value, pricePlaces).replace("-", "")
	});
	initValidator()
}



var curRow, curCol, curArrears, api = frameElement.api,
	oper = api.data.oper,
	cRowId = api.data.rowId,
	rowData = {},
	propertysIds = [],
	deleteRow = [],
	callback = api.data.callback,
	defaultPage = Public.getDefaultPage(),
	siType = defaultPage.SYSTEM.siType,
	categoryTree, storageCombo, unitCombo, gridStoCombo, jianxingCombo, comboWidth = 167,
	gridWidth = 970,
	$grid = $("#grid"),
	$itemList = $("#itemList"),
	$form = $("#manage-form"),
	$storage = $("#storage")
	$category = $("#category"),
	$isSerNum = $("#isSerNum"),
	$isWarranty = $("#isWarranty "),
	$safeDays = $("#safeDays"),
	$advanceDay = $("#advanceDay"),
	categoryData = {},
	unitData = {},
	tempAssistPropGroupInfo = {},
	SYSTEM = parent.parent.SYSTEM,
	qtyPlaces = Number(SYSTEM.qtyPlaces) || 4,
	pricePlaces = Number(SYSTEM.pricePlaces) || 4,
	amountPlaces = Number(SYSTEM.amountPlaces) || 2,
	format = {
		quantity: function(a) {
			var b = parseFloat(a);
			return isNaN(b) ? "&#160;" : a
		},
		money: function(a) {
			var a = Public.numToCurrency(a, pricePlaces);
			return a || "&#160;"
		}
	},
	THISPAGE = {
		newId: 5
	},
	rights = api.opener.parent.SYSTEM.rights;
initPopBtns(), init()
