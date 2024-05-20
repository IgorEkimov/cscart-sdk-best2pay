(function (_, $) {
  $.ceEvent('on', 'ce.commoninit', context => {
    var $browserSettings = $('input[name="browser_settings[user_agent]"]', context);
    if (!$browserSettings.length) {
      return;
    }
    var browserUserAgent = function () {
      return navigator.userAgent || null;
    };
    var browserLanguage = function () {
      return navigator.language || navigator.userLanguage || navigator.browserLanguage || navigator.systemLanguage || null;
    };
    var browserColorDepth = function () {
      if (screen.colorDepth || window.screen.colorDepth) {
        return String(screen.colorDepth || window.screen.colorDepth);
      }
      return null;
    };
    var browserScreenHeight = function () {
      if (window.screen.height) {
        return String(window.screen.height);
      }
      return null;
    };
    var browserScreenWidth = function () {
      if (window.screen.width) {
        return String(window.screen.width);
      }
      return null;
    };
    var browserTZ = function () {
      return String(new Date().getTimezoneOffset());
    };
    var browserJavaEnabled = function () {
      return navigator.javaEnabled() || null;
    };
    var browserJavascriptEnabled = function () {
      return true;
    };
    $('input[name="browser_settings[user_agent]"]', context).val(browserUserAgent);
    $('input[name="browser_settings[language]"]', context).val(browserLanguage);
    $('input[name="browser_settings[color_depth]"]', context).val(browserColorDepth);
    $('input[name="browser_settings[screen_height]"]', context).val(browserScreenHeight);
    $('input[name="browser_settings[screen_width]"]', context).val(browserScreenWidth);
    $('input[name="browser_settings[timezone]"]', context).val(browserTZ);
    $('input[name="browser_settings[java_enabled]"]', context).val(browserJavaEnabled);
    $('input[name="browser_settings[js_enabled]"]', context).val(browserJavascriptEnabled);
  });
})(Tygh, Tygh.$);