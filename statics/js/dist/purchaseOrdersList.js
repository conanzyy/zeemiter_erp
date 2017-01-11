function initDom() {
	function a(a, b, c) {
		conditions.typeNumber = a, conditions.name = b, c || $("#grid").setGridParam({
			postData: conditions
		}).trigger("reloadGrid"), parent.$("li.l-selected a").eq(0).text("采购订单查询")
	}
	var b, c = $(".ui-tab").on("click", "li", function() {
		var b = $(this),
			c = b.data("id"),
			d = b.html(),
			e = conditions.typeNumber,
			f = conditions.name;
		return conditions.typeNumber = c, conditions.name = d, true ? ($(".cur").removeClass("cur"), b.addClass("cur"), $("#custom-assisting").getCombo().selectByIndex(0, !1), void a(c, d)) : (conditions.typeNumber = e, void(conditions.name = f))
	}),
		d = [],
		e = {
			allStatus		: "全部",
			draft			: "草稿",
			pending			: "待审核",
			underReview		: "审核中",
			reviewUnPassed	: "审核未通过",
			reviewPassed	: "待出库",
			outku			: "待入库",
			ruku			: "全部入库",
			
		};
	for (var f in e) d.push('<li data-id="' + f + '">' + e[f] + "</li>");
	c.append(d.join(""));
	var g = $("#assisting-category-select li[data-id=" + typeNumber + "]");
	1 == g.length ? (g.addClass("cur"), b = 0) : (b = ["number", typeNumber], $("#custom-assisting").parent().addClass("cur")), a(typeNumber, e[typeNumber], !0), $("#custom-assisting").combo({
		data: "../basedata/assist/getOrderType?action=getOrderType",
		text: "name",
		value: "number",
		width: 170,
		/*ajaxOptions: {
			formatData: function(a) {
				var a = a.data.rows;
				a.unshift({
					number: "",
					name: "选择其他类别"
				});
				for (var b = 0, c = a.length; c > b; b++) a[b].name = a[b].name.replace("类别", ""), e[a[b].number] && (a.splice(b, 1), b--, c--);
				return a.length > 1 && $("#custom-assisting").parent().show(), a
			}
		},*/
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
	})
}
function initEvent() {
	$(".grid-wrap").on("click", ".ui-icon-pencil", function(a) {
		if (a.stopPropagation(),a.preventDefault(), Business.verifyRight("PO_UPDATE")) {
			var b = $(this).parent().data("id"),
				c = $("#grid").jqGrid("getRowData", b),
				d = 1 == c.disEditable ? "&disEditable=true" : "",
				e = ($("#grid").jqGrid("getDataIDs"), "采购订单"),
				f = "purchaseOrder-purchaseOrders";
				parent.cacheList.purchaseId = $("#grid").jqGrid("getDataIDs"), parent.tab.addTabItem({
				tabid: f,
				text: e,
				url: "../scm/invOd?action=editPur&status=edit&id=" + b + "&flag=list" + d + "&transType=150501"
			})
		}
	}),$('.grid-wrap').on('click','.ui-icon-detail',function(a){
		if (a.preventDefault(), Business.verifyRight("PO_INSTORAGE")) {
			var b = $(this).parent().data('id');
			parent.closeTab("purchaseOrdersQuery"), 
			parent.tab.addTabItem({
				tabid: 'purchaseOrdersQuery',
				text: '采购订单详情',
				url: "../scm/invOd?action=orderList&id=" + b + "&flag=list",
			})
		}
	}),$('.grid-wrap').on('click','.ui-icon-returnG',function(a){
		if (a.preventDefault(), Business.verifyRight("PO_BACKSTORAGE")) {
			var b = $(this).parent().data('id');
			parent.cacheList.purchaseId = $("#grid").jqGrid("getDataIDs"), parent.tab.addTabItem({
				tabid: 'a',
				text: '退货',
				url: "../scm/invOd?action=returnGoods&id=" + b + "&flag=list",
			})
		}
	}),	$("#btn-refresh").click(function(a) {
		a.preventDefault(), $("#grid").trigger("reloadGrid")
	}), $("#search").click(function(a) {
		a.preventDefault();
		var b = $.trim($("#matchCon").val());
		conditions.skey = "单据编号/商品编码/厂商产品码" == b ? "" : b, $("#grid").setGridParam({
			postData: conditions
		}).trigger("reloadGrid")
	}), $("#matchCon").placeholder(), $(window).resize(function() {
		Public.resizeGrid()
	}), $("#add").click(function(a) {
		if (a.preventDefault(), Business.verifyRight("PO_ADD")) {
			var b = "采购订单",
				c = "purchaseOrder-purchaseOrders";
			parent.tab.addTabItem({
				tabid: c,
				text: b,
				url: "../scm/invOd?action=initPur&transType=150501"
			})
		}
	})
}
function operFmatter(a, b, c) {
	var isChange = '';
	var detail = '<span class="min-btn btn-info ui-icon-detail" title="详情"><span class="fa fa-sign-out"></span> 详情</span>';
	if (c.orderStatusNum == 0 || c.orderStatusNum == 1) {
		isChange = '<span class="min-btn btn-success ui-icon-pencil" title="修改"><span class="fa fa-edit "></span>修改</span> ';
	}else if(c.orderStatusNum == 5 || c.orderStatusNum == 6){
		
		detail = '<span class="min-btn btn-info ui-icon-detail" title="入库"><span class="fa fa-sign-out"></span> 入库</span>';
		if(c.orderStatusNum == 6){
			detail = '<span class="min-btn btn-info ui-icon-detail" title="详情"><span class="fa fa-sign-out"></span> 详情</span>';
		}
		isChange = '<span class="min-btn btn-warning ui-icon-returnG" title="退货"><span class="fa fa-edit "></span>退货</span> ';
	}
	var d = '<div class="operating" data-id="' + c.id + '" >'+ detail +' '+ isChange +'</div>';
	return d
}

function initGrid() {
	var a = [{
		name: "operate",
		label: "操作",
		width: 105,
		fixed: !0,
		align: "left",
		sortable: !1,
		formatter:operFmatter
	}, {
		name: "nid",
		label: "单据编号",
		index: "billNo",
		width: 170,
		align: "center"
	},{
		name: "nid",
		label: "单据编号",
		index: "billNo",
		width: 170,
		hidden:!0,
		align: "center"
	},{
		name: "contactName",
		label: "供应商",
		index: "contactName",
		width: 120
	},{
		name: "billDate",
		label: "单据日期",
		index: "billDate",
		width: 100,
		align: "center"
	}, {
		name: "leibie",
		label: "订单类型",
		index: "leibie",
		width: 100,
		fixed: !0,
		align: "left",
		title: !0,
	},{
		name: "totalAmount",
		label: "采购金额",
		index: "totalAmount",
		hidden: !1,
		width: 100,
		align: "right",
		formatter: "currency"
	}, {
		name: "amount",
		label: "优惠后金额",
		index: "amount",
		hidden: true,
		width: 100,
		align: "right",
		formatter: "currency"
	}, {
		name: "orderStatus",
		label: "订单状态",
		index: "orderStatus",
		width: 100,
		fixed: !0,
		align: "center",
		title: !0,
		classes: "ui-ellipsis",
	},{
		name: "userName",
		label: "制单人",
		index: "userName",
		width: 120,
		fixed: !0,
		align: "center",
		title: !0,
		classes: "ui-ellipsis"
	}, {
		name: "description",
		label: "备注",
		index: "description",
		width: 200,
		classes: "ui-ellipsis",
		sortable: !1
	}, {
		name: "disEditable",
		label: "订单来源",
		label: "不可编辑",
		index: "disEditable",
		hidden: !0
	}, {
		name: "id",
		label: "id",
		hidden: !0
	}, {
		name: "level",
		label: "level",
		hidden: !0
	}, {
		name: "parentId",
		label: "parentId",
		hidden: !0
	}, {
		name: "parentName",
		label: "parentName",
		hidden: !0
	}, {
		name: "detail",
		label: "是否",
		hidden: !0
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
				parent.SYSTEM.categoryInfo = parent.SYSTEM.categoryInfo || {}, parent.SYSTEM.categoryInfo[conditions.typeNumber] = a.rows, $("#grid").data("gridData", b)
			} else {
				var f = 250 == a.status ? "没有" + conditions.name + "类别数据！" : "获取" + conditions.name + "类别数据失败！" + a.msg;
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
		},
		ondblClickRow: function(a) {
			$("#" + a).find(".ui-icon-detail").trigger("click")
		}
	})
}
function resetForm() {
	$("#manage-form").validate().resetForm(), $("#ParentCategory").val(""), $("#category").val("").focus().select()
}
function refirsh(){
	$('#btn-refresh').click();
}
function verifyRight(a) {
	var b = rightsType[conditions.typeNumber];
	if (!b) return !0;
	switch (a) {
	case rightsAction.query:
		break;
	default:
		return !1
	}
	return Business.verifyRight(b += a)
}
var typeNumber, showParentCategory, url = "../scm/invOd?action=list",
	urlParam = Public.urlParam();
urlParam.typeNumber && (typeNumber = urlParam.typeNumber);
var conditions = {
	typeNumber: typeNumber,
	skey: "",
	name: ""
},
	rightsType = {
		allStatus		: "ALLSTATUS",
		draft			: "DRAFT",
		underReview		: "UNDERREVIEW",
		reviewPassed	: "REVIEWPASSED",
		reviewUnPassed	: "REVIEWUNPASSED",
		outku			: "OUTKU",
		ruky			: "RUKU"
	},
	rightsAction = {
		query: "_QUERY",
		add: "_ADD",
		del: "_DELETE",
		update: "_UPDATE"
	};
initDom(), initEvent(), initGrid();

setInterval("refirsh()",1000 * 60);


 