(function (_, $) {
  const requestDelay = 300;
  const searchAddonsDebounce = $.debounce(function () {
    const $marketplaceSearch = $(this);
    const $marketplaceSearchForm = $marketplaceSearch.closest('[data-ca-addons-marketplace="marketplaceSearchForm"]');
    const resultIds = $('[name="result_ids"]', $marketplaceSearchForm).val();
    const $submitBtn = $('[type="submit"]', $marketplaceSearchForm);
    $.ceAjax('request', fn_url(''), {
      result_ids: resultIds,
      hidden: true,
      data: {
        q: $marketplaceSearch.val(),
        lang_code: _.cart_language,
        [$submitBtn.attr('name')]: $submitBtn.val()
      }
    });
  }, requestDelay);
  $.ceEvent('on', 'ce.commoninit', function ($context) {
    const $marketplaceSearch = $('[data-ca-addons-marketplace="marketplaceSearch"]', $context);
    if (!$marketplaceSearch.length) {
      return;
    }
    $marketplaceSearch.on('input', searchAddonsDebounce);
  });
})(Tygh, Tygh.$);