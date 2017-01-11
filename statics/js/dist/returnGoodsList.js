function initDom() {
	function a(a, b, c) {
		conditions.typeNumber = a, conditions.name = b, c || $("#grid").setGridParam({
			postData: conditions
		}).trigger("reloadGrid"), parent.$("li.l-selected a").eq(0).text("退货订单详情")
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
			underReview: "审核中",
			reviewPassed: "审核通过",
			reviewUnPassed: "审核未通过"
		};
	for (var f in e) d.push('<li data-id="' + f + '">' + e[f] + "</li>");
	c.append(d.join(""));
	var g = $("#assisting-category-select li[data-id=" + typeNumber + "]");
	1 == g.length ? (g.addClass("cur"), b = 0) : (b = ["number", typeNumber], $("#custom-assisting").parent().addClass("cur")), a(typeNumber, e[typeNumber], !0), $("#custom-assisting").combo({
		data: "../basedata/assist/getReturnType?action=getReturnType",
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
	$("#grid").on("click", ".operating .ui-icon-pencil", function(a) {
		var b = $(this).parent().data('id');
		parent.cacheList.purchaseId = $("#grid").jqGrid("getDataIDs"), parent.tab.addTabItem({
			tabid: 'a',
			text: '退货商品详情',
			url: "../scm/invOd?action=returnGoodsOrder&id=" + b + "&flag=list",
		})



	}), $("#btn-refresh").click(function(a) {
		a.preventDefault(), $("#grid").trigger("reloadGrid")
	}), $("#search").click(function(a) {
		a.preventDefault();
		var b = $.trim($("#matchCon").val());alert(b)
		conditions.skey = "输入类别名称查询" == b ? "" : b, $("#grid").setGridParam({
			postData: conditions
		}).trigger("reloadGrid")
	}), $("#matchCon").placeholder(), $(window).resize(function() {
		Public.resizeGrid()
	})
}
function operFmatter(val, opt, row){
	if(row.isSelf == 1) return "";
	var html_con = '<div class="operating" data-id="' + row.id + '"><span class="min-btn btn-success ui-icon-pencil" title="详情"><span class="fa fa-edit"></span>详情</span></div>';
	return html_con;
}
function initGrid() {
	var a = [{
		name: "operate",
		label: "操作",
		width: 80,
		fixed: !0,
		align: "center",
		formatter:operFmatter
	}, {
		name: "nid",
		label: "退货单号",
		width: 200,
		align: 'center' 
	}, {
		name: "status",
		label: "状态",
		width: 100,
		align: 'center' 
	}, {
		name: "rttime",
		label: "单据日期",
		width: 130,
		align: 'center' 
	},{
		name: "desc",
		label: "退货意见",
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
				parent.SYSTEM.categoryInfo = parent.SYSTEM.categoryInfo || {}, parent.SYSTEM.categoryInfo[conditions.typeNumber] = a.items, $("#grid").data("gridData", b)
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
/*function initValidator() {
	$("#manage-form").validate({
		rules: {
			category: {
				required: !0
			}
		},
		messages: {
			category: {
				required: "类别不能为空"
			}
		},
		errorClass: "valid-error"
	})
}
function postData(a) {
	if (!$("#manage-form").validate().form()) return void $("#manage-form").find("input.valid-error").eq(0).focus();
	var b = $.trim($("#category").val()),
		c = $.trim($("#ParentCategory").val()),
		d = a ? "update" : "add",
		e = c ? $("#ParentCategory").data("PID") : "";
	if (e === a) return void parent.parent.Public.tips({
		type: 2,
		content: "当前分类和上级分类不能相同！"
	});
	var f = {
		parentId: e,
		id: a,
		name: b
	},
		g = "add" == d ? "新增" + conditions.name + "类别" : "修改" + conditions.name + "类别";
	f.typeNumber = conditions.typeNumber, Public.ajaxPost("../basedata/assist/" + d, f, function(a) {
		200 == a.status ? (parent.parent.Public.tips({
			content: g + "成功！"
		}), handle.callback(a.data, d)) : parent.parent.Public.tips({
			type: 1,
			content: g + "失败！" + a.msg
		})
	})
}*/
function resetForm() {
	$("#manage-form").validate().resetForm(), $("#ParentCategory").val(""), $("#category").val("").focus().select()
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
var typeNumber, showParentCategory, url = "../scm/invOd?action=returnGoodsData&isDelete=0",
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
		underReview: "UNDERREVIEW",
		reviewPassed: "REVIEWPASSED",
		reviewUnPassed: "REVIEWUNPASSED"
	},
	rightsAction = {
		query: "_QUERY",
		add: "_ADD",
		del: "_DELETE",
		update: "_UPDATE"
	};
initDom(), initEvent(), initGrid();



 