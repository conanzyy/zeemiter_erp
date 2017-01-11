function refirsh(){
	$("#grid").trigger("reloadGrid")
}
var conditions = {
		matchCon: "",
		page : 1
	}
function initDom() {
	function a(a, b, c) {
		conditions.typeNumber = a, conditions.name = b, c || $("#grid").setGridParam({
			postData: conditions
		}).trigger("reloadGrid")
	}
	var b, c = $(".ui-tab").on("click", "li", function() {
			var b = $(this),
				c = b.data("id"),
				d = b.html(),
				e = conditions.typeNumber,
				f = conditions.name;
			return conditions.typeNumber = c, conditions.name = d, verifyRight(rightsAction.query) ? ($(".cur").removeClass("cur"), b.addClass("cur"),$("#grid").setGridParam({
				postData: conditions,
				page : 1
			}).trigger("reloadGrid"), $("#custom-assisting").getCombo().selectByIndex(0, !1), void a(c, d)) : (conditions.typeNumber = e, void(conditions.name = f))

	}),
		d = [],
		e = {
			all: "全部",
			daiqueren: "待确认",
			daifahuo :"待发货",
			yifahuo :"已发货",
			yishouhuo : "已收货",
			yiquxiao : "已取消",
			yiguanbi :"已关闭",
			yiwancheng : "已付款完成",
			weiwancheng : "未付款完成",
		};
	for (var f in e) d.push('<li data-id="' + f + '">' + e[f] + "</li>");
	c.append(d.join(""));
	var g = $("#assisting-category-select li[data-id=" + typeNumber + "]");
	1 == g.length ? (g.addClass("cur"), b = 0) : (b = ["number", typeNumber], $("#custom-assisting").parent().addClass("cur")), a(typeNumber, e[typeNumber], !0), $("#custom-assisting").combo({
		data: "../basedata/assist/shopSaleList",
		text: "name",
		value: "number",
		width: 170,
		ajaxOptions: {
			formatData: function(a) {
				var a = a.data.items;
				for (var b = 0, c = a.length; c > b; b++) a[b].name = a[b].name.replace("类别", ""), e[a[b].number] && (a.splice(b, 1), b--, c--);
				return a.length > 1 && $("#custom-assisting").parent().show(), a
			}
		},
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
		a.stopPropagation(),a.preventDefault();
		var b = $(this).parent().data("id"),
			c = $("#grid").jqGrid("getRowData", b),
			d = 1 == c.disEditable ? "&disEditable=true" : "",
			e = ($("#grid").jqGrid("getDataIDs"), "订单详情"),
			f = "shopSalesQuery-shopSalesQueryDetail";
		parent.tab.addTabItem({
			tabid: f,
			text: e,
			url: "../scm/invSa/shopSaleDetail?billNo=" + b
		})
	}),$(".grid-wrap").on("click", ".ui-icon-sp", function() {
		var b = $(this).parent().data("id");
		 $.dialog({
			lock: !0,
			width: 898.4,
			height: 366.4,
			min: !1,
			max: !1,
			title: "修改价格",
			resize: !1,
			content: "url:../scm/invSa/shopSalePrice?billNo="+b,
			data: conditions
		})
	}),
		/*$(".grid-wrap").on("click", ".ui-icon-sp", function(a) {
		 var b = $(this).parent().data("id");
		 window.location.href='../scm/invSa/shopSalePrice?billNo='+b;
		 }),*/ $(".grid-wrap").on("click", ".ui-icon-trash", function(a) {
		if (a.preventDefault(), Business.verifyRight("SA_DELETE")) {
			var b = $(this).parent().data("id");
			$.dialog.confirm("您确定要取消该订单吗？", function() {
				Public.ajaxGet("../scm/invSa/shopOrderCancel", {
					id: b
				}, function(a) {
					200 === a.status ? ($("#grid").jqGrid("setGridParam", {
						datatype: "json",
						postData: a
					}).trigger("reloadGrid"), parent.Public.tips({
						content: "取消成功！"
					})) : parent.Public.tips({
						type: 1,
						content: a.msg
					})
				})
			})
		}
	}),
		$(".grid-wrap").on("click", ".ui-icon-xg", function(a) {
			if (a.preventDefault(), Business.verifyRight("SA_DELETE")) {
				var b = $(this).parent().data("id");
				$.dialog.confirm("您确定要确认该订单吗？", function() {
					Public.ajaxGet("../scm/invSa/shopOrderConfirm", {
						id: b
					}, function(a) {
						200 === a.status ? ($("#grid").jqGrid("setGridParam", {
							datatype: "json",
							postData: a
						}).trigger("reloadGrid"), parent.Public.tips({
							content: "确认成功！"
						})) : parent.Public.tips({
							type: 1,
							content: a.msg
						})
					})
				})
			}
		}),
		$(".grid-wrap").on("click", ".ui-icon-wc", function(a) {
			var b = $(this).parent().data("id");
			$.dialog({
				lock: !0,
				width: 898.4,
				height: 366.4,
				min: !1,
				max: !1,
				title: "付款方式",
				resize: !1,
				content: "url:../scm/invSa/finish?tid="+b,
				data: conditions
			})
		}),
		$(".grid-wrap").on("click", ".ui-icon-wfk", function(a) {
			if (a.preventDefault(), Business.verifyRight("SA_DELETE")) {
				var b = $(this).parent().data("id");
				$.dialog.confirm("您确定要完成该订单吗？", function() {
					Public.ajaxGet("../scm/invSa/shopOrderUnFinish", {
						id: b
					}, function(a) {
						200 === a.status ? ($("#grid").jqGrid("setGridParam", {
							datatype: "json",
							postData: a
						}).trigger("reloadGrid"), parent.Public.tips({
							content: "订单已完成！"
						})) : parent.Public.tips({
							type: 1,
							content: a.msg
						})
					})
				})
			}
		}),
		$(".grid-wrap").on("click", ".ui-icon-qr", function(a) {
			a.preventDefault();
			var b = $(this).parent().data("id"),
				c = $("#grid").jqGrid("getRowData", b),
				d = 1 == c.disEditable ? "&disEditable=true" : "",
				e = ($("#grid").jqGrid("getDataIDs"), "销售出库单"),
				f = "sales-sales";
			 parent.cacheList.salesId = $("#grid").jqGrid("getDataIDs");
			parent.tab.addTabItem({
				tabid: f,
				text: e,
				url: "../scm/invSa?action=editSale&id=" + b + "&flag=list&from=qr&transType=150601"
			})
		}),
		$(".grid-wrap").on("click", ".ui-icon-sh", function(a) {
			if (a.preventDefault(), Business.verifyRight("SA_DELETE")) {
				var b = $(this).parent().data("id");
				$.dialog.confirm("您确定要收货？", function() {
					Public.ajaxGet("../scm/invSa/reciept", {
						id: b
					}, function(a) {
						200 === a.status ? ($("#grid").jqGrid("setGridParam", {
							datatype: "json",
							postData: a
						}).trigger("reloadGrid"), parent.Public.tips({
							content: "收货已完成！"
						})) : parent.Public.tips({
							type: 1,
							content: a.msg
						})
					})
				})
			}
		}),
	$("#btn-add").click(function(a) {
		a.preventDefault(), verifyRight(rightsAction.add) && handle.operate("add")
	}), $("#grid").on("click", ".operating .ui-icon-detail", function(a) {
		/*if (a.preventDefault(), verifyRight(rightsAction.update)) {
		 var b = $(this).parent().data("id");
		 handle.operate("edit", b)
		 }*/
		a.stopPropagation(),a.preventDefault();
		var b = $(this).parent().data("id"),
			c = $("#grid").jqGrid("getRowData", b),
			d = 1 == c.disEditable ? "&disEditable=true" : "",
			e = ($("#grid").jqGrid("getDataIDs"), "采购单"),
			f = "purchase-purchase";
		"150502" == queryConditions.transType ? (e = "采购退货单", f = "purchase-purchaseBack", parent.cacheList.purchaseBackId = $("#grid").jqGrid("getDataIDs")) : parent.cacheList.purchaseId = $("#grid").jqGrid("getDataIDs"), parent.tab.addTabItem({
			tabid: f,
			text: e,
			url: "../scm/invSa?action=initSaleReturnDetail&id=" + b + "&flag=list" + d + "&transType=" + queryConditions.transType
		})
		/*}), $("#grid").on("click", ".operating .ui-icon-trash", function(a) {
		 if (a.preventDefault(), verifyRight(rightsAction.del)) {
		 var b = $(this).parent().data("id");
		 handle.del(b)
		 }*/
	}), $("#btn-refresh").click(function(a) {
		a.preventDefault(), $("#grid").trigger("reloadGrid")
	}), $("#search").click(function(a) {
		a.preventDefault();
		var b = $.trim($("#matchCon").val());
		conditions.billNo = "输入订单编号查询" == b ? "" : b, $("#grid").setGridParam({
			postData: conditions
		}).trigger("reloadGrid")
	}), $("#matchCon").placeholder(), $(window).resize(function() {
		Public.resizeGrid()
	})
}
function operFmatter(val, opt, row){
	//console.log(row);
	if(row.isSelf == 1) return "";
	if(row.hxStateCode == "WAIT_CONFRIM"){
		var html_con = '<div class="operating" data-id="' + row.billNo + '"><span class="min-btn btn-info ui-icon-pencil" title="订单详情"><span class="fa fa-sign-out"></span>订单详情</span>&nbsp;<span class="min-btn btn-success ui-icon-sp" title="修改"><span class="fa fa-edit"></span>修改价格</span>&nbsp;<span class="min-btn btn-success ui-icon-xg" title="确认"><span class="fa fa-edit"></span>确认订单</span>&nbsp;<span class="min-btn btn-warning ui-icon-trash" title="删除"><span class="fa fa-trash"></span>取消订单</span></span></div>';
	}else if(row.hxStateCode == "DELIVER_GOODS"){
		var html_con = '<div class="operating" data-id="' + row.billNo + '"><span class="min-btn btn-info ui-icon-pencil" title="订单详情"><span class="fa fa-sign-out"></span>订单详情</span>&nbsp;<span class="min-btn btn-success ui-icon-qr" title="发货"><span class="fa fa-edit"></span>发货</span></div>';
	}
	else if(row.hxStateCode == "RECEIVE_GOODS"){
		var html_con = '<div class="operating" data-id="' + row.billNo + '"><span class="min-btn btn-info ui-icon-pencil" title="订单详情"><span class="fa fa-sign-out"></span>订单详情</span>&nbsp;<span class="min-btn btn-success ui-icon-wc" title="已付款完成"><span class="fa fa-edit"></span>已付款完成</span>&nbsp;<span class="min-btn btn-success ui-icon-wfk" title="未付款完成"><span class="fa fa-edit">&nbsp;</span>未付款完成</span></div>';
	}
	else if(row.isPayEnd == "1" && row.hxStateCode !== "TRADE_CLOSED"){
		var html_con = '<div class="operating" data-id="' + row.billNo + '"><span class="min-btn btn-info ui-icon-pencil" title="订单详情"><span class="fa fa-sign-out"></span>订单详情</span>&nbsp;<span class="min-btn btn-success ui-icon-wc" title="已付款完成"><span class="fa fa-edit"></span>已付款完成</span>&nbsp;</div>';
	}else if(row.hxStateCode == "TRADE_CLOSED"){
		var html_con = '<div class="operating" data-id="' + row.billNo + '"><span class="min-btn btn-info ui-icon-pencil" title="订单详情"><span class="fa fa-sign-out"></span>订单详情</span></div>';
	}else if(row.hxStateCode == "WAIT_GOODS"){
		var html_con = '<div class="operating" data-id="' + row.billNo + '"><span class="min-btn btn-info ui-icon-pencil" title="订单详情"><span class="fa fa-sign-out"></span>订单详情</span>&nbsp;<span class="min-btn btn-success ui-icon-sh" title="确认收货"><span class="fa fa-edit"></span>确认收货</span></div>';
	}else{
		var html_con = '<div class="operating" data-id="' + row.billNo + '"><span class="min-btn btn-info ui-icon-pencil" title="订单详情"><span class="fa fa-sign-out"></span>订单详情</span></div>';
	}
	//var html_con = '<div class="operating" data-id="' + row.billNo + '"><span class="min-btn btn-success ui-icon-pencil" title="订单详情">订单详情</span>&nbsp;<span class="min-btn btn-success ui-icon-sp" title="修改"><span class="fa fa-edit"></span>修改价格</span><span class="min-btn btn-warning ui-icon-trash" title="删除"><span class="fa fa-trash"></span>取消订单</span></span></div>';
	return html_con;
}
function initGrid() {
	var a = [{
		name: "operating",
		label: "操作",
		width: 300,
		fixed: !0,
		align: "left",
		sortable: !1,
		formatter:operFmatter
	}, {
		name: "billDate",
		label: "单据日期",
		index: "billDate",
		width: 150,
		fixed: !0,
		align: "center"
	}, {
		name: "billNo",
		label: "单据编号",
		index: "billNo",
		width: 150,
		fixed: !0,
		align: "center"
	},{
		name: "contactName",
		label: "客户",
		index: "contactName",
		width: 100,
		fixed: !0,
	}, {
		name: "totalAmount",
		label: "销售金额",
		index: "totalAmount",
		width: 80,
		fixed: !0,
		formatter: "currency",
		align: "right",
	},{
		name: "hxStateCode",
		label: "订单状态",
		width: 80,
		fixed: !0,
		align: "center",
		title: !0,
		classes: "ui-ellipsis",
		formatter: function(a) {
			switch (a) {
				case "WAIT_CONFRIM":
					return "待确认";
				case 'DELIVER_GOODS':
					return "待发货";
				case 'WAIT_APPROVE':
					return '待审核';
				case 'RECEIVE_GOODS':
					return '已收货';
				case 'TRADE_FINISHED':
					return  '已完成';
				case 'TRADE_CLOSED':
					return '已关闭';
				case 'TRADE_CANCEL':
					return  '已取消';
				case 'WAIT_GOODS':
					return '已发货';
				default:
					return "&#160"
			}
		}
	},{
		name: "description",
		label: "店家备注",
		index: "description",
		fixed: !0,
		width: 180,
		title: !0,
		classes: "ui-ellipsis",
		sortable: !1
	}, {
		name: "disEditable",
		label: "不可编辑",
		index: "disEditable",
		hidden: !0
	}];
	$("#grid").jqGrid({
		url: url,
		postData: conditions,
		datatype: "json",
		autowidth: !0,
		height: Public.setGrid().h,
		altRows: !0,
		gridview: !0,
		colModel: a,
		cmTemplate: {
			sortable: !0,
			title: !1
		},
		page: 1,
		pager: "#page",
		rowNum: 20,
		viewrecords: !0,
		shrinkToFit: !0,
		forceFit: !1,
		jsonReader: {
			root: "data.rows",
			records: "data.records",
			total: "data.total",
			repeatitems: !0,
			id: "id"
		},
		loadComplete: function(a) {
			for (var b = a.data.rows, c = 0; c < b.length; c++) {
				var d = b[c];
				d.checked || $("#" + d.id).addClass("gray")
			}
		},
		ondblClickRow: function(a) {
			$("#" + a).find(".ui-icon-pencil").trigger("click")
		},
		loadError: function(xhr,status,error) {
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
function postData() {
	 params = $('#updateprice_form').serialize(), Public.ajaxPost("../scm/invSa/priceEdit"), params, function(b) {
		200 == b.status ? (parent.parent.Public.tips({
			content: e + "成功！"
		})) : parent.parent.Public.tips({
			type: 1,
			content: e + "失败！" + b.msg
		})
	}
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
var typeNumber, showParentCategory, url = "../scm/invSa/ShopSaleList",
	urlParam = Public.urlParam();
urlParam.typeNumber && (typeNumber = urlParam.typeNumber);
var conditions = {
		typeNumber: typeNumber,
		skey: "",
		name: "",
		page: "1"
	},
	rightsType = {
		all: "ALL",
		daiqueren: "DAIQUEREN",
		daishenhe: "DAISHENHE",
		daifahuo: "DAIFAHUO",
		yifahuo: "YIFAHUO",
		yishouhuo: "YISHOUHUO",
		yiquxiao : "YIQUXIAO",
		yiguanbi : "YIGUANBI",
		yiwancheng : "YIWANCHENG",
		weiwancheng : "WEIWANCHENG"
	},
	rightsAction = {
		query: "_QUERY",
		add: "_ADD",
		del: "_DELETE",
		update: "_UPDATE"
	},
	handle = {
		operate: function(a, b) {
			if ("add" == a) {
				var c = "新增" + conditions.name + "类别";
				({
					oper: a,
					callback: this.callback
				})
			} else {
				var c = "修改" + conditions.name + "类别";
				({
					oper: a,
					rowData: $("#grid").data("gridData")[b],
					callback: this.callback
				})
			}
			var d = [''],
				e = 90;
			showParentCategory && (e = 150), this.dialog = $.dialog({
				title: c,
				content: d.join(""),
				width: 400,
				height: e,
				max: !1,
				min: !1,
				cache: !1,
				lock: !0,
				okVal: "确定",
				ok: function() {
					return postData(b), !1
				},
				cancelVal: "取消",
				cancel: function() {
					return !0
				},
				init: function() {
					var c = $(".hideFeild"),
						d = $("#ParentCategory"),
						e = $("#category");
					if (showParentCategory && (d.closest("li").show(), $("#ParentCategory").click(function() {
							c.show().data("hasInit") || (c.show().data("hasInit", !0), Public.zTree.init(c, {
								defaultClass: "ztreeDefault"
							}, {
								callback: {
									beforeClick: function(a, b) {
										d.val(b.name), d.data("PID", b.id), c.hide()
									}
								}
							}))
						}), $(".ui_dialog").click(function() {
							c.hide()
						}), $("#ParentCategory").closest(".row-item").click(function(a) {
							var b = a || window.event;
							b.stopPropagation ? b.stopPropagation() : window.event && (window.event.cancelBubble = !0)
						}), document.onclick = function() {
							c.hide()
						}), "add" != a) {
						var f = $("#grid").data("gridData")[b];
						e.val(f.name), d.val(f.parentName), d.data("PID", f.parentId)
					}
					initValidator()
				}
			})
		},
		/*del: function(a) {
		 $.dialog.confirm("删除的" + conditions.name + "类别将不能恢复，请确认是否删除？", function() {
		 Public.ajaxPost("../basedata/assist/delete?action=delete", {
		 id: a,
		 typeNumber: conditions.typeNumber
		 }, function(b) {
		 if (b && 200 == b.status) {
		 parent.Public.tips({
		 content: "删除" + conditions.name + "类别成功！"
		 }), $("#grid").jqGrid("delRowData", a);
		 for (var c = parent.SYSTEM.categoryInfo[conditions.typeNumber].length, d = 0; c > d; d++) parent.SYSTEM.categoryInfo[conditions.typeNumber][d].id === a && (parent.SYSTEM.categoryInfo[conditions.typeNumber].splice(d, 1), d--, c--)
		 } else parent.Public.tips({
		 type: 1,
		 content: "删除" + conditions.name + "类别失败！" + b.msg
		 })
		 })
		 })
		 },*/
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
setInterval("refirsh()",1000 * 60);


