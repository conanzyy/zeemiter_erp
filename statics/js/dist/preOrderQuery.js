function initDom() {
	function a(a, b, c) {
		conditions.typeNumber = a, conditions.name = b, c || $("#grid").setGridParam({
			postData: conditions
		}).trigger("reloadGrid"), parent.$("li.l-selected a").eq(0).text("预订单查询")
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
			allStatus: "全部",
			pending: "待审核",
			reviewPassed: "审核通过",
			reviewUnPassed: "审核未通过",
			hasPurchase: "已采购",
		};
	for (var f in e) d.push('<li data-id="' + f + '">' + e[f] + "</li>");
	c.append(d.join(""));
	var g = $("#assisting-category-select li[data-id=" + typeNumber + "]");
	1 == g.length ? (g.addClass("cur"), b = 0) : 
		(b = ["number", typeNumber], $("#custom-assisting").parent().addClass("cur")),a(typeNumber, e[typeNumber], !0), 
		$("#custom-assisting").combo({
		data: "../basedata/assist/getPreType?action=getPreType",
		text: "name",
		value: "number",
		width: 170,
		ajaxOptions: {
			formatData: function(a) {
				var a = a.data.items;
				a.unshift({
					number: "",
					name: "选择其他类别"
				});
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
	$("#grid").on("click", ".operating .ui-icon-detail", function(a) {
		var b = $(this).parent().data('id');
		parent.cacheList.purchaseId = $("#grid").jqGrid("getDataIDs"), parent.tab.addTabItem({
			tabid: 'preorder-preDetail',
			text: '预订单详情',
			url: "../scm/invPre?action=preDetail&id=" + b,
		})
	}), $("#grid").on("click", ".operating .ui-icon-pencil", function(a) {
		a.stopPropagation(),a.preventDefault();
		var b = $(this).parent().data("id"),
			e = ($("#grid").jqGrid("getDataIDs"), "预订单申请"),
			f = "preorder-preOrder";
		parent.cacheList.purchaseId = $("#grid").jqGrid("getDataIDs"), parent.tab.addTabItem({
			tabid: f,
			text: e,
			url: "../scm/invPre?action=editPre&id=" + b
		})
	}),$("#grid").on("click", ".operating .ui-icon-tihuo", function(a) {
		a.stopPropagation(),a.preventDefault();
		var b = $(this).parent().data("id"),
			e = ($("#grid").jqGrid("getDataIDs"), "预订单提货"),
			f = "preorder-delivery";
		parent.cacheList.purchaseId = $("#grid").jqGrid("getDataIDs"), parent.tab.addTabItem({
			tabid: f,
			text: e,
			url: "../scm/invPre?action=delivery&id=" + b
		})
	}), $("#btn-refresh").click(function(a) {
		a.preventDefault(), $("#grid").trigger("reloadGrid")
	}), $("#search").click(function(a) {
		a.preventDefault();
		var b = $.trim($("#matchCon").val());
		conditions.skey = "输入类别名称查询" == b ? "" : b, $("#grid").setGridParam({
			postData: conditions
		}).trigger("reloadGrid")
	}), $("#matchCon").placeholder(), $(window).resize(function() {
		Public.resizeGrid()
	})
}
function operFmatter(val, opt, row){
	if(row.isSelf == 1) return "";
	var isChange = '';
	if (row.status == 1) {
		isChange = '<span class="min-btn btn-success ui-icon-pencil" title="修改"><span class="fa fa-edit"></span>修改</span> ';
	}else if( row.status == 3 ||  row.status == 4){
		isChange = '<span class="min-btn btn-info ui-icon-tihuo" title="提货"><span class="fa fa-shopping-cart"></span>提货</span> ';
	}
	var html_con = '<div class="operating" data-id="' + row.id + '">'+
				   '<span class="min-btn btn-primary ui-icon-detail" title="详情"><span class="fa fa-sign-out"></span>详情</span> '+
				   isChange +
				   '</div>';
	return html_con;
}
function initGrid() {
	var a = [{
		name: "operate",
		label: "操作",
		width: 120,
		fixed: !0,
		align: "center",
		formatter:operFmatter
	}, {
		name: "billNo",
		label: "预订单单号",
		width: 170,
		align: 'center' 
	}, {
		name: "status_cn",
		label: "状态",
		width: 100,
		align: 'center' 
	}, {
		name: "quota",
		label: "预订单额度",
		width: 100,
		align: 'center' 
	}, {
		name: "laveQuota",
		label: "预订单额度剩余",
		width: 100,
		align: 'center' 
	}, {
		name: "billDate",
		label: "单据日期",
		width: 130,
		align: 'center' 
	},{
		name: "desc",
		label: "审核意见",
		width: 200,
		align: 'center' 
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
		label: "是否叶",
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
			sortable: !1,
			title: !1
		},
		page: 1,
		pager: "#page",
		rowNum: 2e3,
		shrinkToFit: !1,
		scroll: 1,
		jsonReader: {
			root: "data.items",
			records: "data.totalsize",
			repeatitems: !1,
			id: "id"
		},
		loadComplete: function(a) {
			if (a && 200 == a.status) {
				var b = {};
				a = a.data;
				for (var c = 0; c < a.items.length; c++) {
					var d = a.items[c];
					b[d.id] = d
				}
				showParentCategory = "trade" === conditions.typeNumber ? !0 : !1;
				for (var c = 0; c < a.items.length; c++) {
					var d = a.items[c],
						e = b[d.parentId] || {};
					e.name && (showParentCategory = !0, b[d.id].parentName = e.name)
				}
				parent.SYSTEM.categoryInfo = parent.SYSTEM.categoryInfo || {},
				parent.SYSTEM.categoryInfo[conditions.typeNumber] = a.items, 
				$("#grid").data("gridData", b)
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
		}
	})
}
function resetForm() {
	$("#manage-form").validate().resetForm(), 
	$("#ParentCategory").val(""), 
	$("#category").val("").focus().select()
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
function refirsh(){
	$('#btn-refresh').click();
}
var typeNumber, showParentCategory, url = "../scm/invPre?action=preQueryData",
	urlParam = Public.urlParam();
urlParam.typeNumber && (typeNumber = urlParam.typeNumber);
var conditions = {
	typeNumber: typeNumber,
	skey: "",
	name: ""
},
	rightsType = {
		allStatus: "ALLSTATUS",
		pending: "PENDING",
		reviewPassed: "REVIEWPASSED",
		reviewUnPassed: "REVIEWUNPASSED",
		hasPurchase: "HASPURCHASE",
	},
	rightsAction = {
		query: "_QUERY",
		add: "_ADD",
		del: "_DELETE",
		update: "_UPDATE"
	};
initDom(), initEvent(), initGrid();