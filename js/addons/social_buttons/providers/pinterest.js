(function (_, $) {
  const $pinterest = $('[data-ca-social-buttons="pinterest"]');
  if (!$pinterest.length || typeof $pinterest.data() === 'undefined' || !$pinterest.data('caSocialButtonsSrc')) {
    return;
  }
  $.ceLazyLoader({
    src: $pinterest.data('caSocialButtonsSrc'),
    event_suffix: 'pinterest',
    callback: function () {
      if (!window.PinUtils) {
        return;
      }
      window.PinUtils.build();
    }
  });
})(Tygh, Tygh.$);