!
function($) {
    Function.prototype.ligerExtend = function(a, b) {
        if ("function" != typeof a) return this;
        this.base = a.prototype,
        this.base.constructor = a;
        var c = function() {};
        c.prototype = a.prototype,
        this.prototype = new c,
        this.prototype.constructor = this,
        b && $.extend(this.prototype, b)
    },
    Function.prototype.ligerDefer = function(a, b, c) {
        var d = this;
        return setTimeout(function() {
            d.apply(a, c || [])
        },
        b)
    },
    window.liger = $.ligerui = {
        version: "V1.2.0",
        managerCount: 0,
        managers: {},
        managerIdPrev: "ligerui",
        autoNewId: !0,
        error: {
            managerIsExist: "管理器id已经存在"
        },
        pluginPrev: "liger",
        getId: function(a) {
            a = a || this.managerIdPrev;
            var b = a + (1e3 + this.managerCount);
            return this.managerCount++,
            b
        },
        add: function(a) {
            if (2 == arguments.length) {
                var b = arguments[1];
                return b.id = b.id || b.options.id || arguments[0].id,
                void this.addManager(b)
            }
            if (a.id || (a.id = this.getId(a.__idPrev())), this.managers[a.id] && (a.id = this.getId(a.__idPrev())), this.managers[a.id]) throw new Error(this.error.managerIsExist);
            this.managers[a.id] = a
        },
        remove: function(a) {
            if ("string" == typeof a || "number" == typeof a) delete liger.managers[a];
            else if ("object" == typeof a) if (a instanceof liger.core.Component) delete liger.managers[a.id];
            else {
                if (!$(a).attr(this.idAttrName)) return ! 1;
                delete liger.managers[$(a).attr(this.idAttrName)]
            }
        },
        get: function(a, b) {
            if (b = b || "ligeruiid", "string" == typeof a || "number" == typeof a) return liger.managers[a];
            if ("object" == typeof a) {
                var c = a.length ? a[0] : a,
                d = c[b] || $(c).attr(b);
                return d ? liger.managers[d] : null
            }
            return null
        },
        find: function(a) {
            var b = [];
            for (var c in this.managers) {
                var d = this.managers[c];
                a instanceof Function ? d instanceof a && b.push(d) : a instanceof Array ? -1 != $.inArray(d.__getType(), a) && b.push(d) : d.__getType() == a && b.push(d)
            }
            return b
        },
        run: function(a, b, c) {
            if (a) {
                if (c = $.extend({
                    defaultsNamespace: "ligerDefaults",
                    methodsNamespace: "ligerMethods",
                    controlNamespace: "controls",
                    idAttrName: "ligeruiid",
                    isStatic: !1,
                    hasElement: !0,
                    propertyToElemnt: null
                },
                c || {}), a = a.replace(/^ligerGet/, ""), a = a.replace(/^liger/, ""), null == this || this == window || c.isStatic) return liger.plugins[a] || (liger.plugins[a] = {
                    fn: $[liger.pluginPrev + a],
                    isStatic: !0
                }),
                new $.ligerui[c.controlNamespace][a]($.extend({},
                $[c.defaultsNamespace][a] || {},
                $[c.defaultsNamespace][a + "String"] || {},
                b.length > 0 ? b[0] : {}));
                if (liger.plugins[a] || (liger.plugins[a] = {
                    fn: $.fn[liger.pluginPrev + a],
                    isStatic: !1
                }), /Manager$/.test(a)) return liger.get(this, c.idAttrName);
                if (this.each(function() {
                    if (this[c.idAttrName] || $(this).attr(c.idAttrName)) {
                        var d = liger.get(this[c.idAttrName] || $(this).attr(c.idAttrName));
                        return void(d && b.length > 0 && d.set(b[0]))
                    }
                    if (! (b.length >= 1 && "string" == typeof b[0])) {
                        var e = b.length > 0 ? b[0] : null,
                        f = $.extend({},
                        $[c.defaultsNamespace][a], $[c.defaultsNamespace][a + "String"], e);
                        c.propertyToElemnt && (f[c.propertyToElemnt] = this),
                        c.hasElement ? new $.ligerui[c.controlNamespace][a](this, f) : new $.ligerui[c.controlNamespace][a](f)
                    }
                }), 0 == this.length) return null;
                if (0 == b.length) return liger.get(this, c.idAttrName);
                if ("object" == typeof b[0]) return liger.get(this, c.idAttrName);
                if ("string" == typeof b[0]) {
                    var d = liger.get(this, c.idAttrName);
                    if (null == d) return;
                    if ("option" != b[0]) {
                        var e = b[0];
                        if (!d[e]) return;
                        var f = Array.apply(null, b);
                        return f.shift(),
                        d[e].apply(d, f)
                    }
                    if (2 == b.length) return d.get(b[1]);
                    if (b.length >= 3) return d.set(b[1], b[2])
                }
                return null
            }
        },
        defaults: {},
        methods: {},
        core: {},
        controls: {},
        plugins: {}
    },
    $.ligerDefaults = {},
    $.ligerMethos = {},
    liger.defaults = $.ligerDefaults,
    liger.methods = $.ligerMethos,
    $.fn.liger = function(a) {
        return a ? liger.run.call(this, a, arguments) : liger.get(this)
    },
    liger.core.Component = function(a) {
        this.events = this.events || {},
        this.options = a || {},
        this.children = {}
    },
    $.extend(liger.core.Component.prototype, {
        __getType: function() {
            return "liger.core.Component"
        },
        __idPrev: function() {
            return "ligerui"
        },
        set: function(a, b) {
            if (a) if ("object" != typeof a) {
                var c = a;
                if (0 == c.indexOf("on")) return void("function" == typeof b && this.bind(c.substr(2), b));
                if (this.options || (this.options = {}), 0 != this.trigger("propertychange", [a, b])) {
                    this.options[c] = b;
                    var d = "_set" + c.substr(0, 1).toUpperCase() + c.substr(1);
                    this[d] && this[d].call(this, b),
                    this.trigger("propertychanged", [a, b])
                }
            } else {
                var e;
                if (this.options != a ? ($.extend(this.options, a), e = a) : e = $.extend({},
                a), void 0 == b || 1 == b) for (var f in e) 0 == f.indexOf("on") && this.set(f, e[f]);
                if (void 0 == b || 0 == b) for (var f in e) 0 != f.indexOf("on") && this.set(f, e[f])
            }
        },
        get: function(a) {
            var b = "_get" + a.substr(0, 1).toUpperCase() + a.substr(1);
            return this[b] ? this[b].call(this, a) : this.options[a]
        },
        hasBind: function(a) {
            var b = a.toLowerCase(),
            c = this.events[b];
            return c && c.length ? !0 : !1
        },
        trigger: function(a, b) {
            if (a) {
                var c = a.toLowerCase(),
                d = this.events[c];
                if (d) {
                    b = b || [],
                    b instanceof Array == 0 && (b = [b]);
                    for (var e = 0; e < d.length; e++) {
                        var f = d[e];
                        if (0 == f.handler.apply(f.context, b)) return ! 1
                    }
                }
            }
        },
        bind: function(a, b, c) {
            if ("object" != typeof a) {
                if ("function" != typeof b) return ! 1;
                var d = a.toLowerCase(),
                e = this.events[d] || [];
                c = c || this,
                e.push({
                    handler: b,
                    context: c
                }),
                this.events[d] = e
            } else for (var f in a) this.bind(f, a[f])
        },
        unbind: function(a, b) {
            if (!a) return void(this.events = {});
            var c = a.toLowerCase(),
            d = this.events[c];
            if (d && d.length) if (b) {
                for (var e = 0,
                f = d.length; f > e; e++) if (d[e].handler == b) {
                    d.splice(e, 1);
                    break
                }
            } else delete this.events[c]
        },
        destroy: function() {
            liger.remove(this)
        }
    }),
    liger.core.UIComponent = function(a, b) {
        liger.core.UIComponent.base.constructor.call(this, b);
        var c = this._extendMethods();
        c && $.extend(this, c),
        this.element = a,
        this._init(),
        this._preRender(),
        this.trigger("render"),
        this._render(),
        this.trigger("rendered"),
        this._rendered()
    },
    liger.core.UIComponent.ligerExtend(liger.core.Component, {
        __getType: function() {
            return "liger.core.UIComponent"
        },
        _extendMethods: function() {},
        _init: function() {
            if (this.type = this.__getType(), this.id = this.element ? this.options.id || this.element.id || liger.getId(this.__idPrev()) : this.options.id || liger.getId(this.__idPrev()), liger.add(this), this.element) {
                var attributes = this.attr();
                if (attributes && attributes instanceof Array) for (var i = 0; i < attributes.length; i++) {
                    var name = attributes[i];
                    this.options[name] = $(this.element).attr(name)
                }
                var p = this.options;
                if ($(this.element).attr("ligerui")) try {
                    var attroptions = $(this.element).attr("ligerui");
                    0 != attroptions.indexOf("{") && (attroptions = "{" + attroptions + "}"),
                    eval("attroptions = " + attroptions + ";"),
                    attroptions && $.extend(p, attroptions)
                } catch(e) {}
            }
        },
        _preRender: function() {},
        _render: function() {},
        _rendered: function() {
            this.element && $(this.element).attr("ligeruiid", this.id)
        },
        attr: function() {
            return []
        },
        destroy: function() {
            this.element && $(this.element).remove(),
            this.options = null,
            liger.remove(this)
        }
    }),
    liger.controls.Input = function(a, b) {
        liger.controls.Input.base.constructor.call(this, a, b)
    },
    liger.controls.Input.ligerExtend(liger.core.UIComponent, {
        __getType: function() {
            return "liger.controls.Input"
        },
        attr: function() {
            return ["nullText"]
        },
        setValue: function(a) {
            return this.set("value", a)
        },
        getValue: function() {
            return this.get("value")
        },
        _setReadonly: function(a) {
            var b = this.wrapper || this.text;
            if (b && b.hasClass("l-text")) {
                var c = this.inputText;
                a ? (c && c.attr("readonly", "readonly"), b.addClass("l-text-readonly")) : (c && c.removeAttr("readonly"), b.removeClass("l-text-readonly"))
            }
        },
        setEnabled: function() {
            return this.set("disabled", !1)
        },
        setDisabled: function() {
            return this.set("disabled", !0)
        },
        updateStyle: function() {},
        resize: function(a, b) {
            this.set({
                width: a,
                height: b
            })
        }
    }),
    liger.win = {
        top: !1,
        mask: function() {
            function a() {
                if (liger.win.windowMask) {
                    var a = $(window).height() + $(window).scrollTop();
                    liger.win.windowMask.height(a)
                }
            }
            this.windowMask || (this.windowMask = $("<div class='l-window-mask' style='display: block;'></div>").appendTo("body"), $(window).bind("resize.ligeruiwin", a), $(window).bind("scroll", a)),
            this.windowMask.show(),
            a(),
            this.masking = !0
        },
        unmask: function(a) {
            for (var b = $("body > .l-dialog:visible,body > .l-window:visible"), c = 0, d = b.length; d > c; c++) {
                var e = b.eq(c).attr("ligeruiid");
                if (!a || a.id != e) {
                    var f = liger.get(e);
                    if (f) {
                        var g = f.get("modal");
                        if (g) return
                    }
                }
            }
            this.windowMask && this.windowMask.hide(),
            this.masking = !1
        },
        createTaskbar: function() {
            return this.taskbar || (this.taskbar = $('<div class="l-taskbar"><div class="l-taskbar-tasks"></div><div class="l-clear"></div></div>').appendTo("body"), this.top && this.taskbar.addClass("l-taskbar-top"), this.taskbar.tasks = $(".l-taskbar-tasks:first", this.taskbar), this.tasks = {}),
            this.taskbar.show(),
            this.taskbar.animate({
                bottom: 0
            }),
            this.taskbar
        },
        removeTaskbar: function() {
            var a = this;
            a.taskbar.animate({
                bottom: -32
            },
            function() {
                a.taskbar.remove(),
                a.taskbar = null
            })
        },
        activeTask: function(a) {
            for (var b in this.tasks) {
                var c = this.tasks[b];
                b == a.id ? c.addClass("l-taskbar-task-active") : c.removeClass("l-taskbar-task-active")
            }
        },
        getTask: function(a) {
            var b = this;
            if (b.taskbar) return b.tasks[a.id] ? b.tasks[a.id] : null
        },
        addTask: function(a) {
            var b = this;
            if (b.taskbar || b.createTaskbar(), b.tasks[a.id]) return b.tasks[a.id];
            var c = a.get("title"),
            d = b.tasks[a.id] = $('<div class="l-taskbar-task"><div class="l-taskbar-task-icon"></div><div class="l-taskbar-task-content">' + c + "</div></div>");
            return b.taskbar.tasks.append(d),
            b.activeTask(a),
            d.bind("click",
            function() {
                b.activeTask(a),
                a.actived ? a.min() : a.active()
            }).hover(function() {
                $(this).addClass("l-taskbar-task-over")
            },
            function() {
                $(this).removeClass("l-taskbar-task-over")
            }),
            d
        },
        hasTask: function() {
            for (var a in this.tasks) if (this.tasks[a]) return ! 0;
            return ! 1
        },
        removeTask: function(a) {
            var b = this;
            b.taskbar && (b.tasks[a.id] && (b.tasks[a.id].unbind(), b.tasks[a.id].remove(), delete b.tasks[a.id]), b.hasTask() || b.removeTaskbar())
        },
        setFront: function(a) {
            var b = liger.find(liger.core.Win);
            for (var c in b) {
                var d = b[c];
                d == a ? ($(d.element).css("z-index", "9200"), this.activeTask(d)) : $(d.element).css("z-index", "9100")
            }
        }
    },
    liger.core.Win = function(a, b) {
        liger.core.Win.base.constructor.call(this, a, b)
    },
    liger.core.Win.ligerExtend(liger.core.UIComponent, {
        __getType: function() {
            return "liger.controls.Win"
        },
        mask: function() {
            this.options.modal && liger.win.mask(this)
        },
        unmask: function() {
            this.options.modal && liger.win.unmask(this)
        },
        min: function() {},
        max: function() {},
        active: function() {}
    }),
    liger.draggable = {
        dragging: !1
    },
    liger.resizable = {
        reszing: !1
    },
    liger.toJSON = "object" == typeof JSON && JSON.stringify ? JSON.stringify: function(a) {
        var b = function(a) {
            return 10 > a ? "0" + a: a
        },
        c = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        d = function(a) {
            return c.lastIndex = 0,
            c.test(a) ? '"' + a.replace(c,
            function(a) {
                var b = meta[a];
                return "string" == typeof b ? b: "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice( - 4)
            }) + '"': '"' + a + '"'
        };
        if (null === a) return "null";
        var e = typeof a;
        if ("undefined" === e) return void 0;
        if ("string" === e) return d(a);
        if ("number" === e || "boolean" === e) return "" + a;
        if ("object" === e) {
            if ("function" == typeof a.toJSON) return liger.toJSON(a.toJSON());
            if (a.constructor === Date) return isFinite(this.valueOf()) ? this.getUTCFullYear() + "-" + b(this.getUTCMonth() + 1) + "-" + b(this.getUTCDate()) + "T" + b(this.getUTCHours()) + ":" + b(this.getUTCMinutes()) + ":" + b(this.getUTCSeconds()) + "Z": null;
            var f = [];
            if (a.constructor === Array) {
                for (var g = 0,
                h = a.length; h > g; g++) f.push(liger.toJSON(a[g]) || "null");
                return "[" + f.join(",") + "]"
            }
            var i, j;
            for (var k in a) {
                if (e = typeof k, "number" === e) i = '"' + k + '"';
                else {
                    if ("string" !== e) continue;
                    i = d(k)
                }
                e = typeof a[k],
                "function" !== e && "undefined" !== e && (j = liger.toJSON(a[k]), f.push(i + ":" + j))
            }
            return "{" + f.join(",") + "}"
        }
    }
} (jQuery),
function(a) {
    a.fn.ligerTab = function() {
        return a.ligerui.run.call(this, "ligerTab", arguments)
    },
    a.fn.ligerGetTabManager = function() {
        return a.ligerui.run.call(this, "ligerGetTabManager", arguments)
    },
    a.ligerDefaults.Tab = {
        height: null,
        heightDiff: 0,
        changeHeightOnResize: !1,
        contextmenu: !0,
        dblClickToClose: !1,
        dragToMove: !0,
        onBeforeOverrideTabItem: null,
        onAfterOverrideTabItem: null,
        onBeforeRemoveTabItem: null,
        onAfterRemoveTabItem: null,
        onBeforeAddTabItem: null,
        onAfterAddTabItem: null,
        onBeforeSelectTabItem: null,
        onAfterSelectTabItem: null,
        onAfterLeaveTabItem: null
    },
    a.ligerDefaults.TabString = {
        closeMessage: "关闭当前页",
        closeOtherMessage: "关闭其他",
        closeAllMessage: "关闭所有",
        reloadMessage: "刷新"
    },
    a.ligerMethos.Tab = {},
    a.ligerui.controls.Tab = function(b, c) {
        a.ligerui.controls.Tab.base.constructor.call(this, b, c)
    },
    a.ligerui.controls.Tab.ligerExtend(a.ligerui.core.UIComponent, {
        __getType: function() {
            return "Tab"
        },
        __idPrev: function() {
            return "Tab"
        },
        _extendMethods: function() {
            return a.ligerMethos.Tab
        },
        _render: function() {
            var b = this,
            c = this.options;
            c.height && (b.makeFullHeight = !0),
            b.tab = a(this.element),
            b.tab.addClass("l-tab"),
            c.contextmenu && a.ligerMenu && (b.tab.menu = a.ligerMenu({
                width: 100,
                items: [{
                    text: c.closeMessage,
                    id: "close",
                    click: function() {
                        b._menuItemClick.apply(b, arguments)
                    }
                },
                {
                    text: c.closeOtherMessage,
                    id: "closeother",
                    click: function() {
                        b._menuItemClick.apply(b, arguments)
                    }
                },
                {
                    text: c.closeAllMessage,
                    id: "closeall",
                    click: function() {
                        b._menuItemClick.apply(b, arguments)
                    }
                },
                {
                    text: c.reloadMessage,
                    id: "reload",
                    click: function() {
                        b._menuItemClick.apply(b, arguments)
                    }
                }]
            })),
            b.tab.content = a('<div class="l-tab-content"></div>'),
            a("> div", b.tab).appendTo(b.tab.content),
            b.tab.content.appendTo(b.tab),
            b.tab.links = a('<div class="l-tab-links"><ul style="left: 0px; "></ul></div>'),
            b.tab.links.prependTo(b.tab),
            b.tab.links.ul = a("ul", b.tab.links);
            var d = a("> div[lselected=true]", b.tab.content),
            e = d.length > 0;
            b.selectedTabId = d.attr("tabid"),
            a("> div", b.tab.content).each(function(c) {
                var d = a('<li class=""><a></a><div class="l-tab-links-item-left"></div><div class="l-tab-links-item-right"></div></li>'),
                f = a(this);
                f.attr("title") && (a("> a", d).html(f.attr("title")), f.attr("title", ""));
                var g = f.attr("tabid");
                void 0 == g && (g = b.getNewTabid(), f.attr("tabid", g), f.attr("lselected") && (b.selectedTabId = g)),
                d.attr("tabid", g),
                e || 0 != c || (b.selectedTabId = g);
                var h = f.attr("showClose");
                if (h && d.append("<div class='l-tab-links-item-close'></div>"), a("> ul", b.tab.links).append(d), f.hasClass("l-tab-content-item") || f.addClass("l-tab-content-item"), f.find("iframe").length > 0) {
                    var i = a("iframe:first", f);
                    if ("complete" != i[0].readyState) {
                        0 == f.find(".l-tab-loading:first").length && f.prepend("<div class='l-tab-loading' style='display:block;'></div>");
                        var j = a(".l-tab-loading:first", f);
                        i.bind("load.tab",
                        function() {
                            j.hide()
                        })
                    }
                }
            }),
            b.selectTabItem(b.selectedTabId),
            c.height && ("string" == typeof c.height && c.height.indexOf("%") > 0 ? (b.onResize(), c.changeHeightOnResize && a(window).resize(function() {
                b.onResize.call(b)
            })) : b.setHeight(c.height)),
            b.makeFullHeight && b.setContentHeight(),
            a("li", b.tab.links).each(function() {
                b._addTabItemEvent(a(this))
            }),
            b.tab.bind("dblclick.tab",
            function(d) {
                if (c.dblClickToClose) {
                    b.dblclicking = !0;
                    var e = d.target || d.srcElement,
                    f = e.tagName.toLowerCase();
                    if ("a" == f) {
                        var g = a(e).parent().attr("tabid"),
                        h = a(e).parent().find("div.l-tab-links-item-close").length ? !0 : !1;
                        h && b.removeTabItem(g)
                    }
                    b.dblclicking = !1
                }
            }),
            b.set(c)
        },
        _applyDrag: function(b) {
            {
                var c = this;
                this.options
            }
            c.droptip = c.droptip || a("<div class='l-tab-drag-droptip' style='display:none'><div class='l-drop-move-up'></div><div class='l-drop-move-down'></div></div>").appendTo("body");
            var d = a(b).ligerDrag({
                revert: !0,
                animate: !1,
                proxy: function() {
                    var b = a(this).find("a").html();
                    return c.dragproxy = a("<div class='l-tab-drag-proxy' style='display:none'><div class='l-drop-icon l-drop-no'></div></div>").appendTo("body"),
                    c.dragproxy.append(b),
                    c.dragproxy
                },
                onRendered: function() {
                    this.set("cursor", "pointer")
                },
                onStartDrag: function(c, d) {
                    if (!a(b).hasClass("l-selected")) return ! 1;
                    if (2 == d.button) return ! 1;
                    var e = d.srcElement || d.target;
                    return a(e).hasClass("l-tab-links-item-close") ? !1 : void 0
                },
                onDrag: function(b, d) {
                    null == c.dropIn && (c.dropIn = -1);
                    var e = c.tab.links.ul.find(">li"),
                    f = e.index(b.target);
                    e.each(function(b) {
                        if (f != b) {
                            var e = b > f;
                            if ( - 1 == c.dropIn || c.dropIn == b) {
                                var g = a(this).offset(),
                                h = {
                                    top: g.top,
                                    bottom: g.top + a(this).height(),
                                    left: g.left - 10,
                                    right: g.left + 10
                                };
                                e && (h.left += a(this).width(), h.right += a(this).width());
                                var i = d.pageX || d.screenX,
                                j = d.pageY || d.screenY;
                                i > h.left && i < h.right && j > h.top && j < h.bottom ? (c.droptip.css({
                                    left: h.left + 5,
                                    top: h.top - 9
                                }).show(), c.dropIn = b, c.dragproxy.find(".l-drop-icon").removeClass("l-drop-no").addClass("l-drop-yes")) : (c.dropIn = -1, c.droptip.hide(), c.dragproxy.find(".l-drop-icon").removeClass("l-drop-yes").addClass("l-drop-no"))
                            }
                        }
                    })
                },
                onStopDrag: function(b) {
                    if (c.dropIn > -1) {
                        var d = c.tab.links.ul.find(">li:eq(" + c.dropIn + ")").attr("tabid"),
                        e = a(b.target).attr("tabid");
                        setTimeout(function() {
                            c.moveTabItem(e, d)
                        },
                        0),
                        c.dropIn = -1,
                        c.dragproxy.remove()
                    }
                    c.droptip.hide(),
                    this.set("cursor", "default")
                }
            });
            return d
        },
        _setDragToMove: function(b) {
            if (a.fn.ligerDrag) {
                {
                    var c = this;
                    this.options
                }
                if (b) {
                    if (c.drags) return;
                    c.drags = c.drags || [],
                    c.tab.links.ul.find(">li").each(function() {
                        c.drags.push(c._applyDrag(this))
                    })
                }
            }
        },
        moveTabItem: function(a, b) {
            var c = this,
            d = c.tab.links.ul.find(">li[tabid=" + a + "]"),
            e = c.tab.links.ul.find(">li[tabid=" + b + "]"),
            f = c.tab.links.ul.find(">li").index(d),
            g = c.tab.links.ul.find(">li").index(e);
            g > f ? e.after(d) : e.before(d)
        },
        setTabManageEven: function() {
            {
                var b = this;
                this.options
            }
            a("#tabManage").click(function() {
                var c = a(this).offset();
                if (0 === a(".l-tab-menu").length) {
                    var d = '<div class="l-tab-menu"><p id="tabCloseAll" data-opt="closeall"><b></b>关闭全部</p><p id="tabCloseCur" data-opt="closecur"><b></b>关闭当前页</p><p id="tabRefCur" data-opt="reloadcur"><b></b>刷新当前页</p>';
                    a("#page-tab").append(d),
                    a(".l-tab-menu").css({
                        top: c.top + 30 + "px",
                        left: c.left - a(".l-tab-menu").outerWidth() + a("#tabManage").outerWidth() + "px"
                    }),
                    a(".l-tab-menu p").each(function() {
                        a(this).click(function() {
                            b._menuItemClick({
                                id: a(this).data("opt")
                            }),
                            a(".l-tab-menu").hide()
                        })
                    })
                } else a(".l-tab-menu").css({
                    top: c.top + 30 + "px",
                    left: c.left - a(".l-tab-menu").outerWidth() + a("#tabManage").outerWidth() + "px"
                }).show()
            }),
            a(document).click(function(b) {
                a(b.target).isChildAndSelfOf(".l-tab-menu") || a(b.target).isChildAndSelfOf("#tabManage") || a(".l-tab-menu").hide()
            })
        },
        setTabButton: function() {
            var b = this,
            c = (this.options, 0);
            a("li", b.tab.links.ul).each(function() {
                c += a(this).width() + 2
            });
            var d = b.tab.width();
            return c > d ? (b.tab.links.append('<div class="l-tab-links-left"><i></i></div><div class="l-tab-links-right"><i></i></div>'), b.setTabButtonEven(), !0) : (b.tab.links.ul.animate({
                left: 0
            }), a(".l-tab-links-left,.l-tab-links-right", b.tab.links).remove(), !1)
        },
        setTabButtonEven: function() {
            {
                var b = this;
                this.options
            }
            a(".l-tab-links-left", b.tab.links).hover(function() {
                a(this).addClass("l-tab-links-left-over")
            },
            function() {
                a(this).removeClass("l-tab-links-left-over")
            }).click(function() {
                b.moveToPrevTabItem()
            }),
            a(".l-tab-links-right", b.tab.links).hover(function() {
                a(this).addClass("l-tab-links-right-over")
            },
            function() {
                a(this).removeClass("l-tab-links-right-over")
            }).click(function() {
                b.moveToNextTabItem()
            })
        },
        moveToPrevTabItem: function() {
            var b = this,
            c = (this.options, a(".l-tab-links-left", b.tab.links).width()),
            d = new Array;
            a("li", b.tab.links).each(function(b) {
                var e = -1 * c;
                b > 0 && (e = parseInt(d[b - 1]) + a(this).prev().width() + 2),
                d.push(e)
            });
            for (var e = -1 * parseInt(b.tab.links.ul.css("left")), f = 0; f < d.length - 1; f++) if (d[f] < e && d[f + 1] >= e) return void b.tab.links.ul.animate({
                left: -1 * parseInt(d[f])
            })
        },
        moveToNextTabItem: function() {
            var b = this,
            c = (this.options, a(".l-tab-links-right", b.tab).width()),
            d = 0,
            e = a("li", b.tab.links.ul);
            e.each(function() {
                d += a(this).width() + 2
            });
            for (var f = b.tab.width(), g = new Array, h = e.length - 1; h >= 0; h--) {
                var i = d - f + c + 2;
                h != e.length - 1 && (i = parseInt(g[e.length - 2 - h]) - a(e[h + 1]).width() - 2),
                g.push(i)
            }
            for (var j = -1 * parseInt(b.tab.links.ul.css("left")), k = 1; k < g.length; k++) if (g[k] <= j && g[k - 1] > j) return void b.tab.links.ul.animate({
                left: -1 * parseInt(g[k - 1])
            })
        },
        getTabItemCount: function() {
            {
                var b = this;
                this.options
            }
            return a("li", b.tab.links.ul).length
        },
        getSelectedTabItemID: function() {
            {
                var b = this;
                this.options
            }
            return a("li.l-selected", b.tab.links.ul).attr("tabid")
        },
        removeSelectedTabItem: function() {
            {
                var a = this;
                this.options
            }
            a.removeTabItem(a.getSelectedTabItemID())
        },
        overrideSelectedTabItem: function(a) {
            {
                var b = this;
                this.options
            }
            b.overrideTabItem(b.getSelectedTabItemID(), a)
        },
        overrideTabItem: function(b, c) {
            {
                var d = this;
                this.options
            }
            if (0 == d.trigger("beforeOverrideTabItem", [b])) return ! 1;
            var e = c.tabid;
            void 0 == e && (e = d.getNewTabid());
            var f = c.url,
            g = c.content,
            h = (c.target, c.text),
            i = c.showClose,
            j = c.height,
            k = a("li[tabid=" + b + "]", d.tab.links.ul),
            l = a(".l-tab-content-item[tabid=" + b + "]", d.tab.content),
            m = a("div:first", l).show();
            k && l && (k.attr("tabid", e), l.attr("tabid", e), 0 == a("iframe", l).length && f ? l.html("<iframe frameborder='0'></iframe>") : g && l.html(g), a("iframe", l).attr("name", e), void 0 == i && (i = !0), 0 == i ? a(".l-tab-links-item-close", k).remove() : 0 == a(".l-tab-links-item-close", k).length && k.append("<div class='l-tab-links-item-close'></div>"), void 0 == h && (h = e), j && l.height(j), a("a", k).text(h), a("iframe", l).attr("src", f).bind("load.tab",
            function() {
                m.hide(),
                c.callback && c.callback()
            }), d.trigger("afterOverrideTabItem", [b]))
        },
        setHeader: function(b, c) {
            a("li[tabid=" + b + "] a", this.tab.links.ul).text(c)
        },
        selectTabItem: function(b,t) {
            {
                var c = this;
                this.options
            }
            return 0 == c.trigger("beforeSelectTabItem", [b]) ? !1 : (c.trigger("afterLeaveTabItem", [c.selectedTabId]), c.selectedTabId = b, a("> .l-tab-content-item[tabid=" + b + "]", c.tab.content).show().siblings().hide(), a("li[tabid=" + b + "]", c.tab.links.ul).addClass("l-selected").siblings().removeClass("l-selected"), void c.trigger("afterSelectTabItem", [b,t]));
        },
        moveToLastTabItem: function() {
            var b = this,
            c = (this.options, 0);
            a("li", b.tab.links.ul).each(function() {
                c += a(this).width() + 2
            });
            var d = b.tab.width();
            if (c > d) {
                var e = a(".l-tab-links-right", b.tab.links).width();
                b.tab.links.ul.animate({
                    left: -1 * (c - d + e + 2)
                })
            }
        },
        isTabItemExist: function(b) {
            {
                var c = this;
                this.options
            }
            return a("li[tabid=" + b + "]", c.tab.links.ul).length > 0
        },
        addTabItem: function(b) {
            var c = this,
            d = this.options;
            if (0 == c.trigger("beforeAddTabItem", [e])) return ! 1;
            var e = b.tabid;
            void 0 == e && (e = c.getNewTabid());
            var f = b.url,
            g = b.content,
            h = b.text,
            i = b.showClose,
            j = b.height;
            if (c.isTabItemExist(e)) {
                var k = a(".l-tab-content-item[tabid=" + e + "]").find("iframe").attr("src");
                if (c.selectTabItem(e), k != f) return void c.overrideTabItem(e, b)
            } else {
                var l = a("<li><a></a><div class='l-tab-links-item-left'></div><div class='l-tab-links-item-right'></div><div class='l-tab-links-item-close'><i class='fa fa-close'></i></div></li>"),
                m = a("<div class='l-tab-content-item'><div class='l-tab-loading' style='display:block;'></div><iframe frameborder='0'></iframe></div>"),
                n = a("div:first", m),
                o = a("iframe:first", m);
                if (c.makeFullHeight) {
                    var p = c.tab.height() - c.tab.links.height();
                    m.height(p)
                }
                l.attr("tabid", e),
                m.attr("tabid", e),
                f ? o.attr("name", e).attr("id", e).attr("src", f).bind("load.tab",
                function() {
                    n.hide(),
                    b.callback && b.callback()
                }) : (o.remove(), n.remove()),
                g ? m.html(g) : b.target && m.append(b.target),
                void 0 == i && (i = !0),
                0 == i && a(".l-tab-links-item-close", l).remove(),
                void 0 == h && (h = e),
                j && m.height(j),
                a("a", l).text(h),
                0 === a("#tabManage").length ? (c.tab.links.ul.append(l), c.tab.links.ul.append('<li id="tabManage"><i></i></li>'), c.setTabManageEven()) : l.insertBefore("#tabManage"),
                c.tab.content.append(m),
                c.selectTabItem(e),
                c.setTabButton() && c.moveToLastTabItem(),
                c._addTabItemEvent(l),
                d.dragToMove && a.fn.ligerDrag && (c.drags = c.drags || [], l.each(function() {
                    c.drags.push(c._applyDrag(this))
                })),
                c.trigger("afterAddTabItem", [e])
            }
        },
        _addTabItemEvent: function(b) {
            {
                var c = this;
                this.options
            }
            b.click(function() {
                var b = a(this).attr("tabid");
                c.selectTabItem(b)
            }),
            c.tab.menu && c._addTabItemContextMenuEven(b),
            a(".l-tab-links-item-close", b).hover(function() {
                a(this).addClass("l-tab-links-item-close-over")
            },
            function() {
                a(this).removeClass("l-tab-links-item-close-over")
            }).click(function() {
                var b = a(this).parent().attr("tabid");
                c.removeTabItem(b)
            })
        },
        removeTabItem: function(b) {
            {
                var c = this;
                this.options
            }
            if (0 == c.trigger("beforeRemoveTabItem", [b])) return ! 1;
            var d = a("li[tabid=" + b + "]", c.tab.links.ul).hasClass("l-selected");
            if (d) {
                var e = a(".l-tab-content-item[tabid=" + b + "]", c.tab.content).prev().attr("tabid");
                c.selectTabItem(e)
            }
            var f = a(".l-tab-content-item[tabid=" + b + "]", c.tab.content),
            g = a("iframe", f);
            if (g.length) {
                var h = g[0];
                h.src = "about:blank";
                try {
                    h.contentWindow.document.write("")
                } catch(i) {}
                "Microsoft Internet Explorer" === navigator.appName && CollectGarbage(),
                g.remove()
            }
            f.remove(),
            a("li[tabid=" + b + "]", c.tab.links.ul).remove(),
            c.setTabButton(),
            c.trigger("afterRemoveTabItem", [b])
        },
        addHeight: function(a) {
            var b = this,
            c = (this.options, b.tab.height() + a);
            b.setHeight(c)
        },
        setHeight: function(a) {
            {
                var b = this;
                this.options
            }
            b.tab.height(a),
            b.setContentHeight()
        },
        setContentHeight: function() {
            var b = this,
            c = (this.options, b.tab.height() - b.tab.links.height());
            b.tab.content.height(c),
            a("> .l-tab-content-item", b.tab.content).height(c)
        },
        getNewTabid: function() {
            {
                var a = this;
                this.options
            }
            return a.getnewidcount = a.getnewidcount || 0,
            "tabitem" + ++a.getnewidcount
        },
        getTabidList: function(b, c) {
            var d = this,
            e = (this.options, []);
            return a("> li", d.tab.links.ul).each(function() {
                a(this).attr("tabid") && a(this).attr("tabid") != b && (!c || a(".l-tab-links-item-close", this).length > 0) && e.push(a(this).attr("tabid"))
            }),
            e
        },
        removeOther: function(b) {
            var c = this,
            d = (this.options, c.getTabidList(b, !0));
            a(d).each(function() {
                c.removeTabItem(this)
            })
        },
        reload: function(b) {
            var c = (this.options, a(".l-tab-content-item[tabid=" + b + "]")),
            d = a(".l-tab-loading:first", c),
            e = a("iframe:first", c),
            f = a(e).attr("src");
            d.show(),
            e.attr("src", f).unbind("load.tab").bind("load.tab",
            function() {
                d.hide()
            })
        },
        removeAll: function() {
            var b = this,
            c = (this.options, b.getTabidList(null, !0));
            a(c).each(function() {
                b.removeTabItem(this)
            })
        },
        onResize: function() {
            var b = this,
            c = this.options;
            if (!c.height || "string" != typeof c.height || -1 == c.height.indexOf("%")) return ! 1;
            if ("body" == b.tab.parent()[0].tagName.toLowerCase()) {
                var d = a(window).height();
                d -= parseInt(b.tab.parent().css("paddingTop")),
                d -= parseInt(b.tab.parent().css("paddingBottom")),
                b.height = c.heightDiff + d * parseFloat(b.height) * .01
            } else b.height = c.heightDiff + b.tab.parent().height() * parseFloat(c.height) * .01;
            b.tab.height(b.height),
            b.setContentHeight()
        },
        _menuItemClick: function(b) {
            {
                var c = this;
                this.options
            }
            if (c.actionTabid = c.actionTabid || c.getSelectedTabItemID(), b.id && c.actionTabid) switch (b.id) {
            case "close":
                c.removeTabItem(c.actionTabid),
                c.actionTabid = null;
                break;
            case "closecur":
                if ("index" === c.getSelectedTabItemID()) break;
                c.removeTabItem(c.getSelectedTabItemID());
                break;
            case "closeother":
                c.removeOther(c.actionTabid);
                break;
            case "closeall":
                c.removeAll(),
                c.actionTabid = null;
                break;
            case "reload":
                c.selectTabItem(c.actionTabid,"reload"),
                c.reload(c.actionTabid);
                break;
            case "reloadcur":
                c.reload(c.getSelectedTabItemID());
                break;
            case "reloadall":
                var d = c.getTabidList(null, !1);
                a(d).each(function() {
                    c.reload(this)
                })
            }
        },
        _addTabItemContextMenuEven: function(b) {
            {
                var c = this;
                this.options
            }
            b.bind("contextmenu",
            function(d) {
                return c.tab.menu ? (c.actionTabid = b.attr("tabid"), c.tab.menu.show({
                    top: d.pageY,
                    left: d.pageX
                }), 0 == a(".l-tab-links-item-close", this).length ? c.tab.menu.setDisabled("close") : c.tab.menu.setEnabled("close"), !1) : void 0
            })
        }
    })
} (jQuery),
function(a) {
    a.ligerMenu = function() {
        return a.ligerui.run.call(null, "ligerMenu", arguments)
    },
    a.ligerDefaults.Menu = {
        width: 120,
        top: 0,
        left: 0,
        items: null,
        shadow: !0
    },
    a.ligerMethos.Menu = {},
    a.ligerui.controls.Menu = function(b) {
        a.ligerui.controls.Menu.base.constructor.call(this, null, b)
    },
    a.ligerui.controls.Menu.ligerExtend(a.ligerui.core.UIComponent, {
        __getType: function() {
            return "Menu"
        },
        __idPrev: function() {
            return "Menu"
        },
        _extendMethods: function() {
            return a.ligerMethos.Menu
        },
        _render: function() {
            var b = this,
            c = this.options;
            b.menuItemCount = 0,
            b.menus = {},
            b.menu = b.createMenu(),
            b.element = b.menu[0],
            b.menu.css({
                top: c.top,
                left: c.left,
                width: c.width
            }),
            c.items && a(c.items).each(function(a, c) {
                b.addItem(c)
            }),
            a(document).bind("click.menu",
            function() {
                for (var a in b.menus) {
                    var c = b.menus[a];
                    if (!c) return;
                    c.hide(),
                    c.shadow && c.shadow.hide()
                }
            }),
            b.set(c)
        },
        show: function(a, b) {
            {
                var c = this;
                this.options
            }
            void 0 == b && (b = c.menu),
            a && void 0 != a.left && b.css({
                left: a.left
            }),
            a && void 0 != a.top && b.css({
                top: a.top
            }),
            b.show(),
            c.updateShadow(b)
        },
        updateShadow: function(a) {
            var b = this.options;
            b.shadow && (a.shadow.css({
                left: a.css("left"),
                top: a.css("top"),
                width: a.outerWidth(),
                height: a.outerHeight()
            }), a.is(":visible") ? a.shadow.show() : a.shadow.hide())
        },
        hide: function(a) {
            {
                var b = this;
                this.options
            }
            void 0 == a && (a = b.menu),
            b.hideAllSubMenu(a),
            a.hide(),
            b.updateShadow(a)
        },
        toggle: function() {
            {
                var a = this;
                this.options
            }
            a.menu.toggle(),
            a.updateShadow(a.menu)
        },
        removeItem: function(b) {
            {
                var c = this;
                this.options
            }
            a("> .l-menu-item[menuitemid=" + b + "]", c.menu.items).remove()
        },
        setEnabled: function(b) {
            {
                var c = this;
                this.options
            }
            a("> .l-menu-item[menuitemid=" + b + "]", c.menu.items).removeClass("l-menu-item-disable")
        },
        setDisabled: function(b) {
            {
                var c = this;
                this.options
            }
            a("> .l-menu-item[menuitemid=" + b + "]", c.menu.items).addClass("l-menu-item-disable")
        },
        isEnable: function(b) {
            {
                var c = this;
                this.options
            }
            return ! a("> .l-menu-item[menuitemid=" + b + "]", c.menu.items).hasClass("l-menu-item-disable")
        },
        getItemCount: function() {
            {
                var b = this;
                this.options
            }
            return a("> .l-menu-item", b.menu.items).length
        },
        addItem: function(b, c) {
            var d = this,
            e = this.options;
            if (b) {
                if (void 0 == c && (c = d.menu), b.line) return void c.items.append('<div class="l-menu-item-line"></div>');
                var f = a('<div class="l-menu-item"><div class="l-menu-item-text"></div> </div>'),
                g = a("> .l-menu-item", c.items).length;
                if (c.items.append(f), f.attr("ligeruimenutemid", ++d.menuItemCount), b.id && f.attr("menuitemid", b.id), b.text && a(">.l-menu-item-text:first", f).html(b.text), b.icon && f.prepend('<div class="l-menu-item-icon l-icon-' + b.icon + '"></div>'), (b.disable || b.disabled) && f.addClass("l-menu-item-disable"), b.children) {
                    f.append('<div class="l-menu-item-arrow"></div>');
                    var h = d.createMenu(f.attr("ligeruimenutemid"));
                    d.menus[f.attr("ligeruimenutemid")] = h,
                    h.width(e.width),
                    h.hover(null,
                    function() {
                        h.showedSubMenu || d.hide(h)
                    }),
                    a(b.children).each(function() {
                        d.addItem(this, h)
                    })
                }
                b.click && f.click(function() {
                    a(this).hasClass("l-menu-item-disable") || b.click(b, g)
                }),
                b.dblclick && f.dblclick(function() {
                    a(this).hasClass("l-menu-item-disable") || b.dblclick(b, g)
                });
                var i = a("> .l-menu-over:first", c);
                f.hover(function() {
                    if (!a(this).hasClass("l-menu-item-disable")) {
                        var e = a(this).offset().top,
                        f = e - c.offset().top;
                        if (i.css({
                            top: f
                        }), d.hideAllSubMenu(c), b.children) {
                            var g = a(this).attr("ligeruimenutemid");
                            if (!g) return;
                            d.menus[g] && (d.show({
                                top: e,
                                left: a(this).offset().left + a(this).width() - 5
                            },
                            d.menus[g]), c.showedSubMenu = !0)
                        }
                    }
                },
                function() {
                    if (!a(this).hasClass("l-menu-item-disable")) {
                        var c = a(this).attr("ligeruimenutemid");
                        if (b.children) {
                            var c = a(this).attr("ligeruimenutemid");
                            if (!c) return
                        }
                    }
                })
            }
        },
        hideAllSubMenu: function(b) {
            {
                var c = this;
                this.options
            }
            void 0 == b && (b = c.menu),
            a("> .l-menu-item", b.items).each(function() {
                if (a("> .l-menu-item-arrow", this).length > 0) {
                    var b = a(this).attr("ligeruimenutemid");
                    if (!b) return;
                    c.menus[b] && c.hide(c.menus[b])
                }
            }),
            b.showedSubMenu = !1
        },
        createMenu: function(b) {
            var c = this,
            d = this.options,
            e = a('<div class="l-menu" style="display:none"><div class="l-menu-yline"></div><div class="l-menu-over"><div class="l-menu-over-l"></div> <div class="l-menu-over-r"></div></div><div class="l-menu-inner"></div></div>');
            return b && e.attr("ligeruiparentmenuitemid", b),
            e.items = a("> .l-menu-inner:first", e),
            e.appendTo("body"),
            d.shadow && (e.shadow = a('<div class="l-menu-shadow"></div>').insertAfter(e), c.updateShadow(e)),
            e.hover(null,
            function() {
                e.showedSubMenu || a("> .l-menu-over:first", e).css({
                    top: -24
                })
            }),
            b ? c.menus[b] = e: c.menus[0] = e,
            e
        }
    }),
    a.ligerui.controls.Menu.prototype.setEnable = a.ligerui.controls.Menu.prototype.setEnabled,
    a.ligerui.controls.Menu.prototype.setDisable = a.ligerui.controls.Menu.prototype.setDisabled
} (jQuery);