var SYSTEM = system = parent.SYSTEM,
	urlParam = Public.urlParam();
	url = "../scm/invOd/orderDetailOut";

//初始化对象
function initDom() {
	this.$_matchCon = $("#matchCon"); 
	this.$_beginDate = $("#beginDate").val(system.beginDate);
	this.$_endDate = $("#endDate").val(system.endDate);
	this.$_beginDate.datepicker();
	this.$_endDate.datepicker();
}
//初始化事件
function initEvent() {
	$_this = this;
	$("#search").click(function() {
		conditions.skey = $_this.$_matchCon.val(),
		conditions.beginDate = $_this.$_beginDate.val(),
		conditions.endDate = $_this.$_endDate.val()
		reloadData(conditions);
		//console.log(conditions);
	})
	
}


//查询方法
function reloadData(conditions){
	$("#grid").jqGrid("setGridParam", { datatype: "json",postData: conditions }).trigger("reloadGrid")
}

//操作列按钮
function operFmatter(val, opt, row){}

function initGrid() {
	var col = [{
		name: "SaleOrgCode",
		label: "发货组织",
		width: 100,
		align: 'center' 
	}, {
		name: "PoCode",
		label: "采购订单号",
		index: "PoCode",
		width: 150,
		align: 'center' 
	}, {
		name: "PoType",
		label: "订单类型",
		width: 100,
		align: 'center' 
	},{
		name: "PoDate",
		label: "单据日期",
		width: 140,
		align: 'center' 
	}, {
		name: "SaleOutCode",
		label: "出库单号",
		width: 100,
		align: 'center' 
	},{
		name: "SaleOutType",
		label: "出库类型",
		width: 100,
		align: 'center' 
	},{
		name: "SaleOutDate",
		label: "出库日期",
		width: 140,
		align: 'center' 
	},{
		name: "InvCode",
		label: "商品编码",
		width: 100,
		align: 'center' 
	},{
		name: "InvName",
		label: "商品名称",
		width: 100,
		align: 'center' 
	},{
		name: "InvProcCode",
		label: "厂家产品码",
		width: 100,
		align: 'center' 
	},{
		name: "InvSpec",
		label: "规格",
		width: 100,
		align: 'center' 
	},{
		name: "InvBrand",
		label: "品牌",
		width: 100,
		align: 'center' 
	},{
		name: "IsInvFree",
		label: "是否有赠品",
		width: 60,
		align: 'center' 
	},{
		name: "SignDate",
		label: "签字日期",
		width: 140,
		align: 'center' 
	},];
	
	$("#grid").jqGrid({
		url: url,
		postData: conditions,
		datatype: "json",
		height: Public.setGrid().h,
		autowidth: !0,
		altRows: !0,
		rownumbers: !0,
		gridview: !0,
		colModel: col,
		viewrecords: !0,
		cmTemplate: {
			sortable: !0,
			title: !1
		},
		page: 1,
		pager: "#page",
		rowNum: 20,
		shrinkToFit: !1,
		forceFit: !1,
		jsonReader: {
			root: "data.rows",
			records: "data.records",
			total: "data.total",
			repeatitems: !1,
			id: "id"
		},
		loadComplete: function(a) {
			if (a && 200 == a.status) {
				var b = {};
				a = a.data;
			}
		},
		loadError: function() {
			parent.Public.tips({
				type: 1,
				content: "操作失败了哦，请检查您的网络链接！"
			})
		}
	})
}

function resetForm() {
}
function verifyRight(a) {}

var conditions = {
	skey: "",
};

initDom(), initEvent(), initGrid();



 