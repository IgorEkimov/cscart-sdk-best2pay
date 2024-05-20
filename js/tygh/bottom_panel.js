(() => {
  const t = {
      bottomPanelSelector: "#bp_bottom_panel",
      offBottomPanelSelector: "#bp_off_bottom_panel",
      bottomButtonsContainerSelector: "#bp_bottom_buttons",
      bottomButtonsSelector: "[data-bp-bottom-buttons]",
      bottomButtonsActiveClass: "bp-bottom-buttons--active",
      bottomButtonDisabledClass: "bp-bottom-button--disabled",
      onBottomPanelSelector: "#bp_on_bottom_panel",
      navItemSpecificSelector: '[data-bp-nav-item="{placeholder}"]',
      navItemSelector: "[data-bp-nav-item]",
      navItemActiveClass: "bp-nav__item--active",
      navActiveSelector: "#bp-nav__active",
      navActiveActivatedClass: "bp-nav__active--activated",
      modesItemSpecificSelector: '[data-bp-modes-item="{placeholder}"]',
      modesItemSelector: "[data-bp-modes-item]",
      modesItemNotDisabledSelector: "[data-bp-modes-item]:not(.bp-modes__item--disabled)",
      modesItemActiveClass: "bp-modes__item--active",
      modesActiveSelector: "#bp-modes__active",
      modesActiveClass: "bp-modes__active--{placeholder}",
      modesActiveClasses: ["bp-modes__active--preview", "bp-modes__active--build", "bp-modes__active--text", "bp-modes__active--theme"],
      dropdownSelector: '[data-bp-toggle="dropdown"]',
      dropdownMenuClass: "bp-dropdown-menu",
      dropdownMenuOpenClass: "bp-dropdown-menu--open",
      dropdownMenuItemClass: "bp-dropdown-menu__item",
      htmlSelector: "html",
      htmlActiveClass: "bp-panel-active"
    },
    e = {
      html: {},
      isAdminPanel: !0,
      bottomPanel: {},
      bottomButtonsContainer: {},
      mode: "default",
      isBottomPanelOpen: !0,
      navActive: "customer",
      modesActive: "preview",
      bottomButtons: [],
      dropdowns: [],
      nav: [],
      modes: []
    },
    o = function () {
      e.html.addClass(t.htmlActiveClass);
    },
    n = function () {
      e.html.removeClass(t.htmlActiveClass);
    },
    s = function () {
      $(e.bottomButtonsContainer).addClass(t.bottomButtonsActiveClass), $(e.bottomButtons).each(function () {
        $(this).removeClass(t.bottomButtonDisabledClass + " " + t.bottomButtonDisabledClass + "-" + $(this).data("bpBottomButtons"));
      });
    },
    a = function () {
      $(e.bottomButtonsContainer).removeClass(t.bottomButtonsActiveClass), $(e.bottomButtons).each(function () {
        $(this).addClass(t.bottomButtonDisabledClass + " " + t.bottomButtonDisabledClass + "-" + $(this).data("bpBottomButtons"));
      });
    },
    i = {
      _activate: function () {
        e.isBottomPanelOpen = !0, o(), a(), i._setOpenCookie(!0);
      },
      _deactivate: function () {
        e.isBottomPanelOpen = !1, n(), s(), i._setOpenCookie(!1);
      },
      _setOpenCookie: function (t) {
        $.cookie.set("pb_is_bottom_panel_open", +t);
      },
      _getCookie: function () {
        var t = $.cookie.get("pb_is_bottom_panel_open");
        e.isBottomPanelOpen = t || !0;
      },
      _addActivateListeners: function () {
        $(Tygh.doc).on("click", t.onBottomPanelSelector, function () {
          return i._activate();
        });
      },
      _addDeactivateListeners: function () {
        $(Tygh.doc).on("click", t.offBottomPanelSelector, function () {
          return i._deactivate();
        });
      }
    },
    c = {
      _setActive: function (t) {
        t && (e.navActive = t.data("bpNavItem")), $(e.bottomPanel).data("navActive", e.navActive), c._setWidth(), c._setPosition(), c._setClass(t);
      },
      _getNav: function () {
        $(e.bottomPanel).find(t.navItemSelector).each(function () {
          e.nav.push($(this));
        });
      },
      _setWidth: function () {
        $(t.navActiveSelector).width($(e.bottomPanel).find(t.navItemSpecificSelector.replace("{placeholder}", e.navActive)).outerWidth());
      },
      _setPosition: function () {
        let o = $(e.bottomPanel).find(t.navItemSpecificSelector.replace("{placeholder}", e.navActive)),
          n = $(o).position().left;
        "rtl" === Tygh.language_direction && e.nav.length > 0 && (n = -Math.ceil($(e.nav[e.nav.length - 1 - $(o).index()]).position().left), 0 != n && (n += $(o).outerWidth() - $(o).width())), $(t.navActiveSelector).css("transform", "translate(" + n + "px)");
      },
      _setClass: function (o) {
        $(t.navActiveSelector).addClass(t.navActiveActivatedClass), o && ($(e.nav).each(function () {
          $(this).removeClass(t.navItemActiveClass);
        }), $(o).addClass(t.navItemActiveClass));
      },
      _addSetActiveListeners: function () {
        $(Tygh.doc).on("click", t.navItemSelector, function (t) {
          return c._setActive($(this));
        });
      }
    },
    l = {
      _setActive: function (t) {
        t && (e.modesActive = t.data("bpModesItem")), $(e.bottomPanel).data("modesActive", e.modesActive), l._setPosition(), l._setClass(t);
      },
      _getButtons: function () {
        $(e.bottomPanel).find(t.modesItemSelector).each(function () {
          e.modes.push($(this));
        });
      },
      _setPosition: function () {
        let o = $(e.bottomPanel).find(t.modesItemSpecificSelector.replace("{placeholder}", e.modesActive)).position().left;
        "rtl" === Tygh.language_direction && e.modes.length > 0 && (o -= $(e.modes[0]).position().left), $(t.modesActiveSelector).css("transform", "translate(" + o + "px)");
      },
      _setClass: function (o) {
        $(t.modesActiveSelector).removeClass(t.modesActiveClasses.join(" ")).addClass(t.modesActiveClass.replace("{placeholder}", e.modesActive)), o && ($(e.modes).each(function () {
          $(this).removeClass(t.modesItemActiveClass);
        }), $(o).addClass(t.modesItemActiveClass));
      },
      _addSetActiveListeners: function () {
        $(Tygh.doc).on("click", t.modesItemNotDisabledSelector, function (t) {
          return l._setActive($(this));
        });
      }
    },
    d = function () {
      $(e.bottomPanel).find(t.dropdownSelector).each(function () {
        e.dropdowns.push($(this).parent()), $(this).on("click", function () {
          var o = $(this);
          $(e.dropdowns).each(function () {
            $(this)[0] !== o.parent()[0] && $(this).children("div").removeClass(t.dropdownMenuOpenClass);
          }), $(this).parent().children("div").toggleClass(t.dropdownMenuOpenClass);
        }), $(this).on("focusout", function (o) {
          $(o.relatedTarget).length && $(o.relatedTarget).hasClass(t.dropdownMenuItemClass) || $(e.dropdowns).each(function () {
            $(this).children("div").removeClass(t.dropdownMenuOpenClass);
          });
        }), $(Tygh.doc).on("click", "." + t.dropdownMenuItemClass, function () {
          $(e.dropdowns).each(function () {
            $(this).children("." + t.dropdownMenuClass).removeClass(t.dropdownMenuOpenClass);
          });
        });
      });
    };
  let m;
  const r = {
    init: {
      init: function () {
        m || (e.html = $(t.htmlSelector), e.bottomPanel = $(t.bottomPanelSelector), e.bottomButtonsContainer = $(t.bottomButtonsContainerSelector), e.mode = e.bottomPanel.data("bpMode"), e.isBottomPanelOpen = e.bottomPanel.data("bpIsBottomPanelOpen"), e.navActive = e.bottomPanel.data("bpNavActive"), e.modesActive = e.bottomPanel.data("bpModesActive"), e.bottomButtons = e.bottomButtonsContainer.find(t.bottomButtonsSelector), e.dropdowns = [], e.modes = [], i._getCookie(), i._addActivateListeners(), i._addDeactivateListeners(), c._getNav(), c._setActive(), c._addSetActiveListeners(), d(), $(e.bottomPanel).find(t.modesItemSelector).length && (l._getButtons(), l._setActive(), l._addSetActiveListeners()), m = !0);
      }
    }.init,
    defaults: t
  };
  $.fn.ceBottomPanel = function (t) {
    return r[t] ? r[t].apply(this, Array.prototype.slice.call(arguments, 1)) : "object" != typeof t && t ? void $.error("ty.bottom_panel: method " + t + " does not exist") : r.init.apply(this, arguments);
  }, $.ceEvent("one", "ce.commoninit", function (t) {
    t = $(t || _.doc);
    var e = $("[data-ca-bottom-pannel]", t);
    e.length && e.ceBottomPanel();
  });
})();