(function (_, $) {
  $.ceEvent('on', 'ce.commoninit', function (context) {
    let inputs = $('input[name*="addon_data[options]"]', context);
    if (!inputs.length) {
      return;
    }
    inputs.each((index, input) => {
      if (input.id.indexOf('point_rate') > -1) {
        $(input).addClass('cm-numeric');
        $(input).attr({
          'data-m-dec': 3,
          'data-a-sep': ''
        });
      }
      if (input.id.indexOf('log_per_page') > -1) {
        $(input).addClass('cm-numeric');
        $(input).attr({
          'data-m-dec': 0,
          'data-a-sep': ''
        });
      }
    });
    $('.cm-numeric', context).autoNumeric('init');
  });
})(Tygh, Tygh.$);