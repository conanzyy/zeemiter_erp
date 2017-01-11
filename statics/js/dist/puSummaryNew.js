define(["jquery", "print"], function(a) {
	function b() {
		Business.filterSupplier(), 
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
		i("#filter-fromDate, #filter-toDate").datepicker(), 
		chkboxes = i("#chk-wrap").cssCheckbox(), 
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
				showSku: ""
			}, i("#selected-period").text(b + "至" + c + (sup && " " + sup) + (sto && " " + sto) ), i("div.grid-subtitle").text("日期: " + b + " 至 " + c), chkVals = chkboxes.chkVal();
			for (var d = 0, e = chkVals.length; e > d; d++) k[chkVals[d]] = 1;
			h(), i("#filter-menu").removeClass("ui-btn-menu-cur")
		}), i("#filter-reset").on("click", function(a) {
			a.preventDefault(), 
			i("#filter-fromDate").val(k.beginDate), 
			i("#filter-toDate").val(k.endDate), 
			i("#filter-customer input").data('numbers',"").val(""), 
			i("#filter-goods input").data('numbers',"").val(""), 
			i("#filter-storage input").data('numbers',"").val(""),
			k.customerNo = "", k.goodsNo = "", k.storageNo = "", chkboxes.chkNot()
		})
	}
	function c() {
		i("#refresh").on("click", function(a) {
			a.preventDefault(), i("#filter-submit").click()
		}), i("#btn-print").click(function(a) {
			a.preventDefault(), Business.verifyRight("PUREPORTINV_PRINT") && i("div.ui-print").printTable()
		}), i("#btn-export").click(function(a) {
			a.preventDefault(), Business.verifyRight("PUREPORTINV_EXPORT") && Business.getFile(l, k)
		})
	}
	function d() {
		var a = !1,
			b = !1,
			c = !1;
		j.isAdmin !== !1 || j.rights.AMOUNT_COSTAMOUNT || (a = !0), j.isAdmin !== !1 || j.rights.AMOUNT_OUTAMOUNT || (b = !0), j.isAdmin !== !1 || j.rights.AMOUNT_INAMOUNT || (c = !0);
		var d = [{
			name: "invNo",
			label: "商品编号",
			width: 100,
			align: "center"
		}, {
			name: "invName",
			label: "商品名称",
			width: 150,
			classes: "ui-ellipsis",
			align: "left"
		}, {
			name: "spec",
			label: "规格型号",
			width: 80,
			align: "left"
		}, {
			name: "unit",
			label: "单位",
			width: 50,
			align: "center"
		}, {
			name: "minNum",
			label: "包装规格",
			width: 60,
			align: "center"
		}, {
			name: "location",
			label: "仓库",
			width: 50,
			align: "center"
		}, {
			name: "qty",
			label: "数量",
			width: 50,
			align: "center",
			formatter: "number",
			formatoptions: {
				thousandsSeparator: ",",
				decimalPlaces: Number(j.qtyPlaces)
			}
		}, {
			name: "unitPrice",
			label: "单价",
			width: 50,
			align: "right",
			hidden: c,
			formatter: "currency",
			formatoptions: {
				thousandsSeparator: ",",
				decimalPlaces: Number(j.pricePlaces)
			}
		}, {
			name: "amount",
			label: "采购金额",
			width: 50,
			align: "right",
			hidden: c,
			formatter: "currency",
			formatoptions: {
				thousandsSeparator: ",",
				decimalPlaces: Number(j.amountPlaces)
			}
		}, {
			name: "locationNo",
			label: "",
			width: 0,
			hidden: !0
		}],
			f = "local",
			g = "#";
		k.autoSearch && (f = "json", g = m), i("#grid").jqGrid({
			url: g,
			postData: k,
			datatype: f,
//			autowidth: !0,
			gridview: !0,
			colModel: d,
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
				root: "data.list",
				userdata: "data.total",
				repeatitems: !1,
				id: "0"
			},
			onCellSelect: function(a) {
				if (Business.verifyRight("PU_QUERY")) {
					var b = i("#grid").getRowData(a),
						c = i("#supplierAuto").val(),
						d = b.invNo,
						e = b.locationNo;
					parent.tab.addTabItem({
						tabid: "report-puDetail",
						text: "采购明细表",
						url: "../report/pu_detail_new?autoSearch=true&beginDate=" + k.beginDate + "&endDate=" + k.endDate + "&customerNo=" + c + "&goodsNo=" + d + "&storageNo=" + e + "&showSku=" + k.showSku
					})
				}
			},
			loadComplete: function(a) {
				resize();
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
	function h() {
		i(".no-query").remove(), i(".ui-print").show(), i("#grid").clearGridData(!0), i("#grid").jqGrid("setGridParam", {
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
			showSku: ""
		}, Public.urlParam()),
		l = "../report/puDetail_invExporter?action=invExporter",
		m = "../report/puDetail_inv?action=inv";
	a("print"), b(), c(), d();
	var n;
	i(window).on("resize", function() {
		n || (n = setTimeout(function() {
			resize(), n = null
		}, 50))
	})
});