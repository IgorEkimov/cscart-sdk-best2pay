(function (_, $) {
  function toggleSendReceiptContent() {
    const $container = this.closest('[data-ca-rus-taxes="onlineCashRegistrySettings"]');
    const sendReceiptValue = $('[data-ca-rus-taxes="sendReceipt"]:checked', $container).val();
    const isDontSend = sendReceiptValue === 'dont_send';
    $('[data-ca-rus-taxes="sendReceiptDescription"]', $container).addClass('hidden');
    $("[data-ca-rus-taxes-send-receipt=\"".concat(sendReceiptValue, "\"]"), $container).removeClass('hidden');
    $('[data-ca-rus-taxes="onlineCashRegistrySettingsParams"]', $container).toggleClass('hidden', isDontSend);
    $('[data-ca-rus-taxes="sendType"]', $container).prop('checked', !isDontSend);
  }
  $.ceEvent('on', 'ce.commoninit', function (context) {
    if (!$('[data-ca-rus-taxes="sendReceipt"]', context).length) {
      return;
    }
    $('[data-ca-rus-taxes="sendReceipt"]').on('change', toggleSendReceiptContent);
  });
})(Tygh, Tygh.$);