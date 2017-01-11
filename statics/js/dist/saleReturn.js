function initDom() {
	function a(a, b, c) {
		conditions.typeNumber = a, conditions.name = b, c || $("#grid").setGridParam({
			postData: conditions
		}).trigger("reloadGrid"), parent.$("li.l-selected a").eq(0).text(conditions.name)
	}
	var b, c = $(".ui-tab").on("click", "li", function() {
		var b = $(this),
			c = b.data("id"),
			d = b.html(),
			e = conditions.typeNumber,
			f = conditions.name;
		return conditions.typeNumber = c, conditions.name = d, verifyRight(rightsAction.query) ? ($(".cur").removeClass("cur"), b.addClass("cur"), $("#custom-assisting").getCombo().selectByIndex(0, !1), void a(c, d)) : (conditions.typeNumber = e, void(conditions.name = f))
	}),
		d = [],
		e = {
//			customertype: "客户",
//			supplytype: "供应商",
//			trade: "商品",
//			paccttype: "支出",
//			raccttype: "收入"
			all: "全部退货申请",
			readyReturn: "待处理",
			hasReturned: "已处理",
			hasRejected: "已驳回"
		};
	for (var f in e) d.push('<li data-id="' + f + '">' + e[f] + "</li>");
	c.append(d.join(""));
	var g = $("#assisting-category-select li[data-id=" + typeNumber + "]");
	1 == g.length ? (g.addClass("cur"), b = 0) : (b = ["number", typeNumber], $("#custom-assisting").parent().addClass("cur")), a(typeNumber, e[typeNumber], !0), $("#custom-assisting").combo({
		data: "../basedata/assist/getSalesReturn?action=getSalesReturn",
		text: "name",
		value: "number",
		width: 170,
		defaultSelected: b,
		defaultFlag: !1,
		callback: {
			onChange: function(b) {
				if (b.number) {
					var c = b.number,
						d = b.name;
					$("#assisting-category-select li").removeClass("cur"), $("#custom-assisting").parent().addClass("cur"), a(c, d)
				} else $("#custom-assisting").getCombo().selectByValue(conditions.typeNumber, !1)
			},
			beforeChange: function(a) {
				var b = a.number,
					c = a.name;
				return _oType = conditions.typeNumber, _oName = conditions.name, conditions.typeNumber = b, conditions.name = c, verifyRight(rightsAction.query) ? !0 : (conditions.typeNumber = _oType, conditions.name = _oName, !1)
			}
		}
	});
}
function initEvent() {
	$("#btn-add").click(function(a) {
		a.preventDefault(), verifyRight(rightsAction.add) && handle.operate("add")
	}), $("#grid").on("click", ".operating .ui-icon-detail", function(a) {
		a.stopPropagation(),a.preventDefault();
		var b = $(this).parent().data("id"),
			c = $("#grid").jqGrid("getRowData", b),
			e = ($("#grid").jqGrid("getDataIDs"), "退货订单详情"),
			f = "saleReturnDetail-saleReturnDetail";
		//alert(b);
		//console.log(c);
		parent.tab.addTabItem({
			tabid: f,
			text: e,
			url: "../scm/invSa/salesReturnDetail?aftersales_bn=" + b
		})
	}),$(".grid-wrap").on("click", ".ui-icon-returnG", function(a) {
		a.preventDefault();
		var b = $(this).parent().data("id"),
			c = $("#grid").jqGrid("getRowData", b),
			d = 1 == c.disEditable ? "&disEditable=true" : "",
			e = "销售退货单",
			f = "sales-salesBack";
			parent.cacheList.salesBackId = $("#grid").jqGrid("getDataIDs");
		parent.tab.addTabItem({
			tabid: f,
			text: e,
			url: "../scm/invSa?action=editSale&id=" + b + "&from=th&flag=list&transType=105602"
		})
	}), $("#btn-refresh").click(function(a) {
		a.preventDefault(), $("#grid").trigger("reloadGrid")
	}), $("#search").click(function(a) {
		a.preventDefault();
		var b = $.trim($("#matchCon").val());
		conditions.skey = "输入退换货编号查询" == b ? "" : b, $("#grid").setGridParam({
			postData: conditions
		}).trigger("reloadGrid");
		//alert(conditions.skey);
	}), $("#matchCon").placeholder(), $(window).resize(function() {
		Public.resizeGrid()
	})
}
function operFmatter(val, opt, row){
	if(row.isSelf == 1) return "";
	//alert(row.billReturnNo);
	if(row.status == 1 && row.isReturn == 0){
		var html_con = '<div class="operating" data-id="' + row.billReturnNo + '"><span class="min-btn btn-success ui-icon-detail" title="查看详情"><span class="fa fa-sign-out"></span>查看详情</span>&nbsp;<span class="min-btn btn-warning ui-icon-returnG" title="退货"><span class="fa fa-edit"></span>退货</span></div>';
	}else {
		var html_con = '<div class="operating" data-id="' + row.billReturnNo + '"><span class="min-btn btn-success ui-icon-detail" title="查看详情"><span class="fa fa-sign-out"></span>查看详情</span></div>';
	}
		return html_con;
}
function refirsh(){
	$("#grid").trigger("reloadGrid")
}
function initGrid() {
	var a = [ {
		name: "operation",
		label: "操作",
		width: 130,
		align: "left",
		formatter:operFmatter
	},{
		name: "billReturnNo",
		label: "退换货编号",
		width: 130,
		fixed: !0,
		align: "center",
		sortable: !0
	}, {
		name: "billNo",
		label: "订单号",
		width: 130,
		align: "center",
		sortable: !0
	}, {
		name: "goods",
		label: "商品",
		width: 549,
		align: "center"
		//sortable: !1
	}, {
		name: "handleProgress",
		label: "处理进度",
		width: 120,
		align: "center"
		//sortable: !1
	}, {
		name: "applyDate",
		label: "申请时间",
		align: "center",
		width: 140,
		sortable: !0
	}];
	$("#grid").jqGrid({
		url: url,
		postData: conditions,
		datatype: "json",
		height: Public.setGrid().h,
		autowidth: !0,
		altRows: !0,
		gridview: !0,
		colModel: a,
		viewrecords: !0,
		cmTemplate: {
			sortable: !1,
			title: !1
		},
		page: 1,
		pager: "#page",
		rowNum: 20,
		viewrecords: !0,
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
				for (var c = 0; c < a.rows.length; c++) {
					var d = a.rows[c];
					b[d.id] = d
				}
				showParentCategory = "trade" === conditions.typeNumber ? !0 : !1;
				for (var c = 0; c < a.rows.length; c++) {
					var d = a.rows[c],
						e = b[d.parentId] || {};
					e.name && (showParentCategory = !0, b[d.id].parentName = e.name)
				}
				parent.SYSTEM.categoryInfo = parent.SYSTEM.categoryInfo || {}, parent.SYSTEM.categoryInfo[conditions.typeNumber] = a.items, $("#grid").data("gridData", b)
			} else {
				var f = 250 == a.status ? "没有" + conditions.name + "数据！" : "获取" + conditions.name + "数据失败！" + a.msg;
				parent.Public.tips({
					type: 2,
					content: f
				})
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
function initValidator() {
	$("#manage-form").validate({
		rules: {
			category: {
				required: !0
			}
		},
		messages: {
			category: {
				required: "输入不能为空"
			}
		},
		errorClass: "valid-error"
	})
}
function resetForm() {
	$("#manage-form").validate().resetForm(), $("#ParentCategory").val(""), $("#category").val("").focus().select()
}
function verifyRight(a) {
	var b = rightsType[conditions.typeNumber];
	if (!b) return !0;
	switch (a) {
	case rightsAction.query:
		break;
	case rightsAction.add:
		break;
	case rightsAction.del:
		break;
	case rightsAction.update:
		break;
	default:
		return !1
	}
	return Business.verifyRight(b += a)
}
var typeNumber, showParentCategory, url = "../scm/invSa?action=salesReturnMethod",
	urlParam = Public.urlParam();
urlParam.typeNumber && (typeNumber = urlParam.typeNumber);
var conditions = {
	typeNumber: typeNumber,
	skey: "",
	name: ""
},
	rightsType = {
		all: "ALL",
		readyReturn: "READYRETURN",
		hasReturned: "HASRETURNED",
		hasRejected: "HASREJECTED",
	},
	rightsAction = {
		query: "_QUERY",
		add: "_ADD",
		del: "_DELETE",
		update: "_UPDATE"
	},
	handle = {
		callback: function(a, b) {
			var c = $("#grid").data("gridData");
			c || (c = {}, $("#grid").data("gridData", c));
			for (var d = parent.SYSTEM.categoryInfo[conditions.typeNumber].length, e = !0, f = 0; d > f; f++) parent.SYSTEM.categoryInfo[conditions.typeNumber][f].id === a.id && (parent.SYSTEM.categoryInfo[conditions.typeNumber][f] = a, e = !1);
			e && parent.SYSTEM.categoryInfo[conditions.typeNumber].push(a), c[a.id] = a, a.parentId && (c[a.id].parentName = c[a.parentId].name), "add" != b ? ($("#grid").jqGrid("setRowData", a.id, a), this.dialog.close()) : ($("#grid").jqGrid("addRowData", a.id, a, "last"), this.dialog.close()), $("#grid").setGridParam({
				postData: conditions
			}).trigger("reloadGrid")
		}
	};
initDom(), initEvent(), initGrid();



