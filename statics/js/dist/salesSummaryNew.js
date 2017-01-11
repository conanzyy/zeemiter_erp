define(["jquery", "print"], function(a) {
	function b() {
		Business.filterCustomer(), 
		Business.filterGoods(), 
		Business.filterStorage(), 
		Business.moreFilterEvent(), 
		i("#conditions-trigger").trigger("click"), 
		i("#filter-fromDate").val(k.beginDate || ""), 
		i("#filter-toDate").val(k.endDate || ""), 
		/*i("#filter-customer input").val(k.customerNo || ""), */
		i("#filter-goods input").val(k.goodsNo || ""), 
		i("#filter-storage input").val(k.storageNo || ""), 
		k.beginDate && k.endDate && (i("#selected-period").text(k.beginDate + "至" + k.endDate + " " + i("#filter-customer input").val()), 
		i("div.grid-subtitle").text("日期: " + k.beginDate + " 至 " + k.endDate)), 
		i("#filter-fromDate, #filter-toDate").datepicker(), Public.dateCheck(), 
		j.rights.SAREPORTBU_COST || j.isAdmin ? (i("#profit-wrap").show(),"1" === k.profit && i("#profit-wrap input").attr("checked", !0)) : i("#profit-wrap").hide(), chkboxes = i("#profit-wrap").cssCheckbox(),
		i("#filter-submit").on("click", function(a) {
			a.preventDefault();
			var b = i("#filter-fromDate").val(),
				c = i("#filter-toDate").val(),
				sup = i("#filter-customer input").val(),
				sto = i("#filter-storage input").val();
			if (b && c && new Date(b).getTime() > new Date(c).getTime()) return void parent.Public.tips({
				type: 1,
				content: "开始日期不能大于结束日期"
			});
			k = {
				beginDate: b,
				endDate: c,
				customerNo: i("#filter-customer input").data('numbers') || "",
				goodsNo: i("#filter-goods input").data('numbers') || "",
				storageNo: i("#filter-storage input").data('numbers') || "",
				profit: ""
			}, i("#selected-period").text(b + "至" + c), i("div.grid-subtitle").text("日期: " + b + " 至 " + c), chkVals = chkboxes.chkVal();
			for (var d = 0, e = chkVals.length; e > d; d++) k[chkVals[d]] = 1;
			var f = k.profit;
			h(f), i("#filter-menu").removeClass("ui-btn-menu-cur")
		}), i("#filter-reset").on("click", function(a) {
			a.preventDefault(),
			i("#filter-fromDate").val(k.beginDate), 
			i("#filter-toDate").val(k.endDate),
			i("#filter-customer input").data('numbers',"").val(""), 
			i("#filter-goods input").data('numbers',"").val(""), 
			i("#filter-storage input").data('numbers',"").val(""),
			k.customerNo = "", k.goodsNo = "", k.storageNo = ""
		})
	}
	function c() {
		i("#refresh").on("click", function(a) {
			a.preventDefault(), i("#filter-submit").click()
		}), i("#btn-print").click(function(a) {
			a.preventDefault(), Business.verifyRight("SAREPORTBU_PRINT") && i("div.ui-print").printTable()
		}), i("#btn-export").click(function(a) {
			a.preventDefault(), Business.verifyRight("SAREPORTBU_EXPORT") && Business.getFile(l, k)
		})
	}
	function d() {
		var a = !1,
			b = !1,
			c = !1;
		j.isAdmin !== !1 || j.rights.AMOUNT_COSTAMOUNT || (a = !0), j.isAdmin !== !1 || j.rights.AMOUNT_OUTAMOUNT || (b = !0), j.isAdmin !== !1 || j.rights.AMOUNT_INAMOUNT || (c = !0);
		var d = !0;
		1 == k.profit && (d = !1);
		var f = [{
			name: "buNo",
			label: "客户编码",
			width: 0,
			hidden: !0
		}, {
			name: "invNo",
			label: "商品编号",
			fixed: !0,
			width: 120
		}, {
			name: "locationNo",
			label: "仓库编码",
			width: 0,
			hidden: !0
		}, {
			name: "invName",
			label: "商品名称",
			width: 250,
			fixed: !0,
			classes: "ui-ellipsis",
			title: !0
		}, {
			name: "spec",
			label: "规格型号",
			fixed: !0,
			width: 100
		}, {
			name: "unit",
			label: "单位",
			width: 80,
			fixed: !0,
			align: "center"
		}, {
			name: "minNum",
			label: "包装规格",
			width: 60,
			align: "center"
		}, {
			name: "location",
			label: "仓库",
			width: 100,
			fixed: !0,
			classes: "ui-ellipsis",
			title: !0
		}, {
			name: "qty",
			label: "数量",
			width: 100,
			fixed: !0,
			align: "right"
		}, {
			name: "unitPrice",
			label: "单价",
			width: 100,
			fixed: !0,
			hidden: a,
			align: "right"
		}, {
			name: "amount",
			label: "销售收入",
			width: 100,
			fixed: !0,
			hidden: a,
			align: "right"
		}, {
			name: "unitCost",
			label: "单位成本",
			width: 100,
			fixed: !0,
			hidden: !0,
			align: "right"
		}, {
			name: "cost",
			label: "销售成本",
			width: 100,
			fixed: !0,
			hidden: !0,
			align: "right"
		}, {
			name: "saleProfit",
			label: "销售毛利",
			width: 100,
			fixed: !0,
			hidden: !0,
			align: "right"
		}, {
			name: "salepPofitRate",
			label: "毛利率",
			width: 100,
			fixed: !0,
			hidden: !0,
			align: "right"
		}];
		i("#grid").jqGrid({
			url: m,
			postData: k,
			datatype: 'json',
			autowidth: !0,
			gridview: !0,
			colModel: f,
			cmTemplate: {
				sortable: !1,
				title: !1
			},
			page: 1,
			sortname: "date",
			sortorder: "desc",
			rowNum: 1e6,
			loadonce: !0,
			viewrecords: !0,
			shrinkToFit: !0,
			footerrow: !0,
			userDataOnFooter: !0,
			jsonReader: {
				root: "data.rows",
				records: "data.records",
				total: "data.total",
				userdata: "data.userdata",
				repeatitems: !1,
				id: "0"
			},
			ondblClickRow: function(a) {
				if (Business.verifyRight("SAREPORTDETAIL_QUERY")) {
					var b = i("#grid").getRowData(a),
						c = b.buNo,
						d = b.invNo,
						e = b.locationNo;
					parent.tab.addTabItem({
						tabid: "report-salesDetail",
						text: "销售明细表",
						url: "../report/sales_detail?autoSearch=true&beginDate=" + k.beginDate + "&endDate=" + k.endDate + "&customerNo=" + c + "&goodsNo=" + d + "&storageNo=" + e + "&profit=" + k.profit + "&showSku=" + k.showSku
					})
				}
			},
			loadComplete: function(a) {
				resize()
			},
			gridComplete: function() {
				i("#grid").footerData("set", {
					location: "合计:"
				}), i("table.ui-jqgrid-ftable").find('td[aria-describedby="grid_location"]').prevUntil().css("border-right-color", "#fff")
			}
		}), k.autoSearch ? (i(".no-query").remove(), i(".ui-print").show()) : i(".ui-print").hide()
	}
	function resize() {
		Public.resizeReport(36*2)
	}
	function h(a) {
		i(".no-query").remove(), i(".ui-print").show(), "undefined" != typeof a && (i("#grid").jqGrid(a ? "showCol" : "hideCol", ["unitCost", "cost", "saleProfit", "salepPofitRate"]), resize()), i("#grid").clearGridData(!0), i("#grid").jqGrid("setGridParam", {
			datatype: "json",
			postData: k,
			url: m
		}).trigger("reloadGrid")
	}
	var i = a("jquery"),
		j = parent.SYSTEM,
		k = i.extend({
			beginDate: "",
			endDate: "",
			customerNo: "",
			goodsNo: "",
			storageNo: "",
			profit: ""
		}, Public.urlParam()),
		l = "../report/salesDetail_invExporter?action=invExporter",
		m = "../report/salesDetail_inv?action=inv";
	a("print"), b(), c(), d();
	var n;
	i(window).on("resize", function() {
		n || (n = setTimeout(function() {
			resize(), n = null
		}, 50))
	})
});