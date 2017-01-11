function setTabHeight() {
	var a = $(window).height(),
		b = $("#main-bd"),
		c = a - b.offset().top;
	b.height(c)
}
function initDate() {
	var a = new Date,
		b = a.getFullYear(),
		c = ("0" + (a.getMonth() + 1)).slice(-2),
		d = ("0" + a.getDate()).slice(-2);
	SYSTEM.beginDate = b + "-" + c + "-01", SYSTEM.endDate = b + "-" + c + "-" + d
}
function addUrlParam() {
	var a = "beginDate=" + SYSTEM.beginDate + "&endDate=" + SYSTEM.endDate;
	$("#nav").find("li.item-report .nav-item a").each(function() {
		var b = this.href;
		b += -1 === this.href.lastIndexOf("?") ? "?" : "&", this.href = "商品库存余额表" === $(this).html() ? b + "beginDate=" + SYSTEM.startDate + "&endDate=" + SYSTEM.endDate : b + a
	})
}
function setCurrentNav(a) {
	if (a) {
		var b = a.match(/([a-zA-Z]+)[-]?/)[1];
		$("#nav > li").removeClass("current"), $("#nav > li.item-" + b).addClass("current")
	}
}
var dataReflush, list = {
	/*采购单*/
	purchase: {
		name: "采购入库单",
		href: WDURL + "/scm/invPu?action=initPur",
		dataRight: "PU_ADD",
		target: "purchase",
	},
	purchaseQuery: {
		name: "采购入库单查询",
		href: WDURL + "/scm/invPu?action=initPurList",
		dataRight: "PU_QUERY",
		target: "purchase",
	},
	purchaseOrders: {
		name: "采购订单",
		href: WDURL + "/scm/invOd?action=initPur",
		dataRight: "PO_ADD",
		target: "purchaseOrder",
//		list: WDURL + "/scm/invPo?action=initPoList"
	},
	purchaseOrdersQuery: {
		name: "采购订单查询",
		href: WDURL + "/scm/invOd?action=initPurList&typeNumber=allStatus",
		dataRight: "PO_QUERY",
		target: "purchaseOrder",
	},
	purchaseReturnQuery: {
		name: "退货订单查询",
		href: WDURL + "/scm/invOd?action=returnGoodsList&typeNumber=allStatus",
		dataRight: "PO_QUERY",
		target: "purchaseOrder",
	},
	purchaseDetailQuery: {
		name: "订单发货明细",
		href: WDURL + "/scm/invOd?action=orderDetailOut",
		dataRight: "PO_QUERY",
		target: "purchaseOrder",
	},
	purchaseBack: {
		name: "采购退货单",
		href: WDURL +  "/scm/invPu?action=initPur&transType=150502",
		dataRight: "PU_ADD",
		target: "purchaseBack",
	},
	purchaseBackQuery: {
		name: "采购退货单查询",
		href: WDURL + "/scm/invPu?action=initPurList&transType=150502",
		dataRight: "PU_QUERY",
		target: "purchaseBack",
	},
	preOrder: {
		name: "预订单申请",
		href: WDURL +  "/scm/invPre?action=preOrder",
		dataRight: "PO_ADD",
		target: "preorder",
	},
	preOrderQuery: {
		name: "预订单查询",
		href: WDURL + "/scm/invPre?action=preOrderQuery&typeNumber=allStatus",
		dataRight: "PO_QUERY",
		target: "preorder",
	},
	/*销售单*/
	salesOrder: {
		name: "销售订单",
		href: WDURL + "/scm/invSo?action=initSo",
		dataRight: "SO_ADD",
		target: "sales",
		list: WDURL + "/scm/invSo?action=initSoList"
	},
	sales: {
		name: "销售出库单",
		href: WDURL + "/scm/invSa?action=initSale",
		dataRight: "SA_ADD",
		target: "sales",
	},
	salesBack: {
		name: "销售退货单",
		href: WDURL + "/scm/invSa?action=initSale&transType=150602",
		dataRight: "SA_ADD",
		target: "salesBack",
	},
	salesQuery: {
		name: "销售出库单查询",
		href: WDURL + "/scm/invSa?action=initSaleList",
		dataRight: "SA_QUERY",
		target: "sales",
	},
	salesBackQuery: {
		name: "销售退货单查询",
		href: WDURL + "/scm/invSa?action=initSaleList&transType=150602",
		dataRight: "SA_QUERY",
		target: "salesBack",
	},
	
	shopSalesQueryALL: {
		name: "商城订单",
		href: WDURL + "/scm/invSa?action=initShopSaleList&typeNumber=all",
		dataRight: "ALL_QUERY",
		target: "shopSalesQuery",
	},
	salesReturn: {
		name: "退货申请",
		href: WDURL + "/scm/invSa?action=initSaleReturn&typeNumber=all",
		dataRight: "ALL_QUERY",
		target: "shopSalesQuery",
	},
	shopSales: {
		name: "手工订单",
		href: WDURL + "/scm/invSa?action=initShopSales",
		dataRight: "ALL_QUERY",
		target: "shopSalesQuery",
	},
	shopSalesQ: {
		name: "手工订单查询",
		href: WDURL + "/scm/invSa?action=initSales",
		dataRight: "ALL_QUERY",
		target: "shopSalesQuery",
	},
	/*仓库*/
	transfers: {
		name: "调拨单",
		href: WDURL + "/scm/invTf?action=initTf",
		dataRight: "TF_ADD",
		target: "storage",
	},
	otherWarehouse: {
		name: "其他入库单",
		href: WDURL + "/scm/invOi?action=initOi&type=in",
		dataRight: "IO_ADD",
		target: "storage",
	},
	otherOutbound: {
		name: "其他出库单",
		href: WDURL + "/scm/invOi?action=initOi&type=out",
		dataRight: "OO_ADD",
		target: "storage",
	},
	adjustment: {
		name: "成本调整单",
		href: WDURL + "/scm/invOi?action=initOi&type=cbtz",
		dataRight: "CADJ_ADD",
		target: "storage",
	},
	inventory: {
		name: "盘点",
		href: WDURL + "/storage/inventory",
		dataRight: "PD_GENPD",
		target: "storage"
	},

	transfersQuery: {
		name: "调拨单查询",
		dataRight: "TF_QUERY",
		target: "storageQuery",
		href: WDURL + "/scm/invTf?action=initTfList"
	},
	otherWarehouseQuery: {
		name: "其他入库单查询",
		dataRight: "IO_QUERY",
		target: "storageQuery",
		href: WDURL + "/scm/invOi?action=initOiList&type=in"
	},
	otherOutboundQuery: {
		name: "其他出库单查询",
		dataRight: "OO_QUERY",
		target: "storageQuery",
		href: WDURL + "/scm/invOi?action=initOiList&type=out"
	},
	adjustmentQuery: {
		name: "成本调整单查询",
		dataRight: "CADJ_QUERY",
		target: "storageQuery",
		href: WDURL + "/scm/invOi?action=initOiList&type=cbtz"
	},

	/*资金*/
	receipt: {
		name: "收款单",
		href: WDURL + "/scm/receipt?action=initReceipt",
		dataRight: "RECEIPT_ADD",
		target: "money",
	},
	payment: {
		name: "付款单",
		href: WDURL + "/scm/payment?action=initPay",
		dataRight: "PAYMENT_ADD",
		target: "money",
	},
	otherIncome: {
		name: "其他收入单",
		href: WDURL + "/scm/ori?action=initInc",
		dataRight: "QTSR_ADD",
		target: "money",
	},
	otherExpense: {
		name: "其他支出单",
		href: WDURL + "/scm/ori?action=initExp",
		dataRight: "QTZC_ADD",
		target: "money",
	},

	receiptQuery: {
		name: "收款单查询",
		dataRight: "RECEIPT_QUERY",
		target: "moneyQuery",
		href: WDURL + "/scm/receipt?action=initReceiptList"
	},
	paymentQuery: {
		name: "付款单查询",
		dataRight: "PAYMENT_QUERY",
		target: "moneyQuery",
		href: WDURL + "/scm/payment?action=initPayList"
	},
	otherIncomeQuery: {
		name: "其他收入单查询",
		dataRight: "QTSR_QUERY",
		target: "moneyQuery",
		href: WDURL + "/scm/ori?action=initIncList"
	},
	otherExpenseQuery: {
		name: "其他支出单查询",
		dataRight: "QTZC_QUERY",
		target: "moneyQuery",
		href: WDURL + "/scm/ori?action=initExpList"
	},

	/*报表*/
	puDetail: {
		name: "采购明细表",
		href: WDURL + "/report/pu_detail_new",
		dataRight: "PUREOORTDETAIL_QUERY",
		target: "report-purchase"
	},
    puSummary: {
		name: "采购汇总表（按商品）",
		href: WDURL + "/report/pu_summary_new",
		dataRight: "PUREPORTINV_QUERY",
		target: "report-purchase"
	},
	puSummarySupply: {
		name: "采购汇总表（按供应商）",
		href: WDURL + "/report/pu_summary_supply_new",
		dataRight: "PUREPORTPUR_QUERY",
		target: "report-purchase"
	},
	salesDetail: {
		name: "销售明细表",
		href: WDURL + "/report/sales_detail",
		dataRight: "SAREPORTDETAIL_QUERY",
		target: "report-sales"
	},
	salesSummary: {
		name: "销售汇总表（按商品）",
		href: WDURL + "/report/sales_summary",
		dataRight: "SAREPORTINV_QUERY",
		target: "report-sales"
	},
	salesSummaryCustomer: {
		name: "销售汇总表（按客户）",
		href: WDURL + "/report/sales_summary_customer_new",
		dataRight: "SAREPORTBU_QUERY",
		target: "report-sales"
	},
	contactDebt: {
		name: "往来单位明细表",
		href: WDURL + "/report/contact_debt_new",
		dataRight: "ContactDebtReport_QUERY",
		target: "report-sales"
	},
	initialBalance: {
		name: "商品库存余额表",
		href: WDURL + "/report/goods_balance",
		dataRight: "InvBalanceReport_QUERY",
		target: "report-storage"
	},
	goodsFlowDetail: {
		name: "商品收发明细表",
		href: WDURL + "/report/goods_flow_detail",
		dataRight: "DeliverDetailReport_QUERY",
		target: "report-storage"
	},
	goodsFlowSummary: {
		name: "商品收发汇总表",
		href: WDURL + "/report/goods_flow_summary",
		dataRight: "DeliverSummaryReport_QUERY",
		target: "report-storage"
	},
	cashBankJournal: {
		name: "现金银行报表",
		href: WDURL + "/report/cash_bank_journal_new",
		dataRight: "SettAcctReport_QUERY",
		target: "report-money"
	},
	accountPayDetail: {
		name: "应付账款明细表",
		href: WDURL + "/report/account_pay_detail_new?action=detailSupplier&type=10",
		dataRight: "PAYMENTDETAIL_QUERY",
		target: "report-money"
	},
	accountProceedsDetail: {
		name: "应收账款明细表",
		href: WDURL + "/report/account_proceeds_detail_new?action=detail",
		dataRight: "RECEIPTDETAIL_QUERY",
		target: "report-money"
	},
	customersReconciliation: {
		name: "客户对账单",
		href: WDURL + "/report/customers_reconciliation_new",
		dataRight: "CUSTOMERBALANCE_QUERY",
		target: "report-money"
	},
	suppliersReconciliation: {
		name: "供应商对账单",
		href: WDURL + "/report/suppliers_reconciliation_new",
		dataRight: "SUPPLIERBALANCE_QUERY",
		target: "report-money"
	},
	otherIncomeExpenseDetail: {
		name: "其他收支明细表",
		href: WDURL + "/report/other_income_expense_detail",
		dataRight: "ORIDETAIL_QUERY",
		target: "report-money"
	},
	customerList: {
		name: "客户管理",
		href: WDURL + "/settings/customer_list",
		dataRight: "BU_QUERY",
		target: "setting-base"
	},
	vendorList: {
		name: "供应商管理",
		href: WDURL + "/settings/vendor_list",
		dataRight: "PUR_QUERY",
		target: "setting-base"
	},
	goodsList: {
		name: "第三方商品管理",
		href: WDURL + "/settings/goods_list",
		dataRight: "INVENTORY_QUERY",
		target: "setting-base"
	},
	goodsListKz: {
		name: "快准商品管理",
		href: WDURL + "/settings/goods_list_kz",
		dataRight: "INVENTORY_QUERY",
		target: "setting-base"
	},
	storageList: {
		name: "仓库管理",
		href: WDURL + "/settings/storage_list",
		dataRight: "INVLOCTION_QUERY",
		target: "setting-base"
	},
	staffList: {
		name: "职员管理",
		href: WDURL + "/settings/staff_list",
		dataRight: "STAFF_MANAGE",
		target: "setting-base"
	},
	settlementaccount: {
		name: "账户管理",
		href: WDURL + "/settings/settlement_account",
		dataRight: "SettAcct_QUERY",
		target: "setting-base"
	},
	customerCategoryList: {
		name: "客户类别",
		href: WDURL + "/settings/category_list?typeNumber=customertype",
		dataRight: "BUTYPE_QUERY",
		target: "setting-auxiliary"
	},
	vendorCategoryList: {
		name: "供应商类别",
		href: WDURL + "/settings/category_list?typeNumber=supplytype",
		dataRight: "SUPPLYTYPE_QUERY",
		target: "setting-auxiliary"
	},
	goodsCategoryList: {
		name: "商品类别",
		href: WDURL + "/settings/category_list?typeNumber=trade",
		dataRight: "TRADETYPE_QUERY",
		target: "setting-auxiliary"
	},
	payCategoryList: {
		name: "支出类别",
		href: WDURL + "/settings/category_list?typeNumber=paccttype",
		dataRight: "TRADETYPE_QUERY",
		target: "setting-auxiliary"
	},
	recCategoryList: {
		name: "收入类别",
		href: WDURL + "/settings/category_list?typeNumber=raccttype",
		dataRight: "TRADETYPE_QUERY",
		target: "setting-auxiliary"
	},
	unitList: {
		name: "计量单位",
		href: WDURL + "/settings/unit_list",
		dataRight: "UNIT_QUERY",
		target: "setting-auxiliary"
	},
	settlementCL: {
		name: "结算方式",
		href: WDURL + "/settings/settlement_category_list",
		dataRight: "Assist_QUERY",
		target: "setting-auxiliary"
	},
//	assistingProp: {
//		name: "辅助属性",
//		href: WDURL + "/settings/assistingprop",
//		dataRight: "FZSX_QUERY",
//		target: "setting-auxiliary"
//	},
	parameter: {
		name: "系统参数",
		href: WDURL + "/settings/system_parameter",
		dataRight: "SYSTEM",
		target: "setting-advancedSetting"
	},
	authority: {
		name: "权限设置",
		href: WDURL + "/settings/authority",
		dataRight: "RIGHT_SET",
		target: "setting-advancedSetting"
	},
	operationLog: {
		name: "操作日志",
		href: WDURL + "/settings/log",
		dataRight: "OPERATE_QUERY",
		target: "setting-advancedSetting"
	}
},
	menu = {
		init: function(a, b) {
			var c = {
				callback: {}
			};
			this.obj = a, this.opts = $.extend(!0, {}, c, b), this.sublist = this.opts.sublist, this.sublist || this._getMenuData(), this._menuControl(), this._initDom()
		},
		_display: function(a, b) {
			for (var c = a.length - 1; c >= 0; c--) this.sublist[a[c]] && (this.sublist[a[c]].disable = !b);
			return this
		},
		_show: function(a) {
			return this._display(a, !0)
		},
		_hide: function(a) {
			return this._display(a, !1)
		},
		_getMenuData: function() {
			this.sublist = list
		},
		_menuControl: function() {
			var a = SYSTEM.siType,
				b = SYSTEM.isAdmin,
				c = SYSTEM.siVersion;
			this._hide(["purchaseOrder", "salesOrder"]);
			b && (this._show(["authority"]));
		},
		_getDom: function() {
			this.objCopy = this.obj.clone(!0), this.container = this.obj.closest("div")
		},
		_setDom: function() {
			this.obj.remove(), this.container.append(this.objCopy)
		},
		_initDom: function() {
			if (this.sublist && this.obj) {
				this.obj.find("li:not(.item)").remove(), this._getDom();
				var a = this.sublist,
					b = {};
				b.target = {};
				for (var c in a) if (!a[c].disable) {
					var d = a[c],
						e = b.target[d.target],
						f = d.id ? "id=" + d.id : "",
						g = d.id ? "" : "rel=pageTab",
						h = "";
					if (d.list) {
						var i = d.name + "记录";
						h = "<i " + f + ' tabTxt="' + i + '" tabid="' + d.target.split("-")[0] + "-" + c + 'List" ' + g + ' href="' + d.list + '" data-right="' + d.dataRight.split("_")[0] + '_QUERY">查询</i>'
					}
					var j = "<li><a " + f + ' tabTxt="' + d.name + '" tabid="' + d.target.split("-")[0] + "-" + c + '" ' + g + ' href="' + d.href + '" data-right="' + d.dataRight + '">' + d.name + h + "</a></li>";
					e ? e.append(j) : (b.target[d.target] = this.objCopy.find("#" + d.target), b.target[d.target] && b.target[d.target].append(j))
				}
				this.objCopy.find("li.item").each(function() {
					var a = $(this);
					a.find("li").length || a.remove(), a.find(".nav-item").each(function() {
						var a = $(this);
						a.find("li").length || (a.hasClass("last") && a.prev().addClass("last"), a.remove())
					})
				}), this._setDom()
			}
		}
	};
$(function() {
	$("#tit").click(function(){
		$("#page-tab li[tabid=index]").click();
	});
	$("#companyName").text(SYSTEM.companyName).prop("title", SYSTEM.companyName)
}), setTabHeight(), $(window).bind("resize", function() {
	setTabHeight()
}), function(a) {
	menu.init(a("#nav")), initDate(), addUrlParam();
	var b = a("#nav"),
		c = a("#nav > li");
	a.each(c, function() {
		var c = a(this).find(".sub-nav-wrap");
		if (a(this).on("mouseenter", function() {
			b.removeClass("static"), a(this).addClass("on"), c.find("i:eq(0)").closest("li").addClass("on"), c.stop(!0, !0).fadeIn(250)
		}).on("mouseleave", function() {
			b.addClass("static"), a(this).removeClass("on"), c.stop(!0, !0).hide()
		}), 0 != c.length && "auto" == c.css("top") && "auto" == c.css("bottom")) {
			var d = (a(this).outerHeight() - c.outerHeight()) / 2;
			c.css({
				top: d
			})
		}
	}), a(".sub-nav-wrap a").bind("click", function() {
		a(this).parents(".sub-nav-wrap").hide()
	}), a(".sub-nav").each(function() {
		a(this).on("mouseover", "li", function() {
			var b = a(this);
			b.siblings().removeClass("on"), b.addClass("on")
		}).on("mouseleave", "li", function() {
			var b = a(this);
			b.removeClass("on")
		})
	})
}(jQuery), $("#page-tab").ligerTab({
	height: "100%",
	changeHeightOnResize: !0,
	onBeforeAddTabItem: function(a) {
		setCurrentNav(a)
	},
	onAfterAddTabItem: function() {},
	onAfterSelectTabItem: function(a,b) {
		switch (a) {
		case "index":
			b != 'reload' && dataReflush && dataReflush()
		}
		setCurrentNav(a)
	},
	onBeforeRemoveTabItem: function() {},
	onAfterLeaveTabItem: function(a) {
		switch (a) {
		case "setting-vendorList":
			getSupplier();
			break;
		case "setting-customerList":
			getCustomer();
			break;
		case "setting-storageList":
			getStorage();
			break;
		case "setting-goodsList":
			getGoods();
			break;
		case "setting-settlementaccount":
			getAccounts();
			break;
		case "setting-settlementCL":
			getPayments();
			getInfospty();
            getInfosfiles();
			break;
		case "onlineStore-onlineStoreList":
			break;
		case "onlineStore-logisticsList":
			break;
		case "setting-staffList":
			getStaff()
		}
	},
});
var tab = $("#page-tab").ligerGetTabManager();
$("#index").on("click", "[rel=pageTab]", function(a) {
	a.preventDefault();
	var b = $(this).data("right");
	if (b && !Business.verifyRight(b)) return !1;
	var c = $(this).attr("tabid"),
		d = $(this).attr("href"),
		e = $(this).attr("showClose"),
		f = $(this).attr("tabTxt") || $(this).text().replace(">", ""),
		g = $(this).attr("parentOpen");
	return g ? parent.tab.addTabItem({
		tabid: c,
		text: f,
		url: d,
		showClose: e
	}) : tab.addTabItem({
		tabid: c,
		text: f,
		url: d,
		showClose: e
	}), !1
}), tab.addTabItem({
	tabid: "index",
	text: "首页",
	url: WDURL + "/home/main",
	showClose: !1
}),
$("#nav").on("click", "[rel=pageTab]", function(a) {
	a.preventDefault();
	var b = $(this).data("right");
	if (b && !Business.verifyRight(b)) return !1;
	var c = $(this).attr("tabid"),
		d = $(this).attr("href"),
		e = $(this).attr("showClose"),
		f = $(this).attr("tabTxt") || $(this).text().replace(">", ""),
		g = $(this).attr("parentOpen");
	return g ? parent.tab.addTabItem({
		tabid: c,
		text: f,
		url: d,
		showClose: e
	}) : tab.addTabItem({
		tabid: c,
		text: f,
		url: d,
		showClose: e
	}), !1
}), tab.addTabItem({
	tabid: "index",
	text: "首页",
	url: WDURL + "/home/main",
	showClose: !1
}),
(jQuery), $(window).load(function() {
	function a() {
		var a;
		switch (SYSTEM.siVersion) {
		case 3:
			a = "1";
			break;
		case 4:
			a = "3";
			break;
		default:
			a = "2"
		}
		/*$.getJSON("home/Services?callback=?", {
			coid: SYSTEM.DBID,
			loginuserno: SYSTEM.UserName,
			version: a,
			type: "getallunreadcount" + SYSTEM.servicePro
		}, function(a) {
			if (0 != a.count) {
				{
					var b = $("#SysNews a");
					b.attr("href")
				}
				b.append("<span>" + a.count + "</span>"), 0 == a.syscount && b.data("tab", 2)
			}
		})*/
	}
	a(), $("#skin-" + SYSTEM.skin).addClass("select").append("<i></i>"), $("#sysSkin").powerFloat({
		eventType: "click",
		reverseSharp: !0,
		target: function() {
			return $("#selectSkin")
		},
		position: "5-7"
	}), $("#selectSkin li a").click(function() {
		var a = this.id.split("-")[1];
		Public.ajaxPost(WDURL + "/basedata/systemProfile/changeSysSkin?action=changeSysSkin", {
			skin: a
		}, function(a) {
			200 === a.status && window.location.reload()
		})
	});
	var b = $("#nav .item");
	if ($("#scollUp").click(function() {
		var a = b.filter(":visible");
		a.first().prev().length > 0 && (a.first().prev().show(500), a.last().hide())
	}), $("#scollDown").click(function() {
		var a = b.filter(":visible");
		a.last().next().length > 0 && (a.first().hide(), a.last().next().show(500))
	}), $.cookie("ReloadTips") && (Public.tips({
		content: $.cookie("ReloadTips")
	}), $.cookie("ReloadTips", null)), $("#nav").on("click", "#reInitial", function(a) {})) {
	}
});
	