(function (_, $) {
  let prevNameText = '';
  function setNameFieldFromSelect($elem) {
    const $name = $('[data-ca-payment="name"]', $elem.closest('[data-ca-payment="tabDetails"]'));
    const nameText = $name.val();
    const selectFieldText = $(':selected', $elem).text();
    if (nameText === '' || prevNameText === nameText) {
      $name.val(selectFieldText);
    }
    prevNameText = selectFieldText;
  }
  function updateIcon($elem) {
    const $tabDetails = $elem.closest('[data-ca-payment="tabDetails"]');
    const $iconNoImage = $('[data-ca-payment="iconWrapper"] .no-image', $tabDetails);
    const processorAddon = $(':selected', $elem).data('caPaymentAddon');
    if ($('[name="payment_id"]', $tabDetails.closest('[data-ca-payment="paymentsForm"]')).val() === '0') {
      $iconNoImage.removeClass('no-image--' + $iconNoImage.data('prevProcessorAddon')).addClass('no-image--' + processorAddon).data('prevProcessorAddon', processorAddon);
    }
  }
  $.ceEvent('on', 'ce.commoninit', function (context) {
    const $templateField = $('[data-ca-payment="template"]', context);
    const $processorId = $('[data-ca-payment="processor_id"]', context);
    if (!$templateField.length || !$processorId.length) {
      return;
    }
    $processorId.on('change', function () {
      setNameFieldFromSelect($(this));
      updateIcon($(this));
    });
    $templateField.on('change', function () {
      setNameFieldFromSelect($(this));
    });
  });
})(Tygh, Tygh.$);