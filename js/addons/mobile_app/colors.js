(function (_, $) {
  $(function () {
    var url = fn_url('addons.update.rebuild?addon=mobile_app');
    $(_.doc).on('change', '.js-mobile-app-input', function () {
      var stored_colors = {};
      $('.js-mobile-app-input').each(function (index, element) {
        if (!stored_colors[element.dataset.targetInputName]) {
          stored_colors[element.dataset.targetInputName] = {};
        }
        stored_colors[element.dataset.targetInputName][element.dataset.target] = {
          'value': element.value
        };
      });
      $.ceAjax('request', url, {
        method: "get",
        data: {
          colors: stored_colors
        },
        result_ids: "colors_variables,color_presets"
      });
    });
    $(_.doc).on('change', '.js-mobile-app-color-preset-input', function () {
      let selected_preset = $(this).val(),
        request_data = {
          selected_preset: selected_preset
        };
      if (selected_preset === 'C') {
        let stored_colors = {};
        $('.js-mobile-app-input').each(function () {
          const $self = $(this),
            inputVal = $self.val(),
            inputName = $self.data('targetInputName'),
            target = $self.data('target');
          stored_colors[inputName] = {
            ...stored_colors[inputName],
            ...{
              [target]: {
                'value': inputVal
              }
            }
          };
        });
        request_data = {
          ...request_data,
          colors: stored_colors
        };
      }
      $.ceAjax('request', url, {
        result_ids: 'colors_variables,color_presets',
        method: 'get',
        data: request_data,
        scroll: selected_preset === 'C' ? 'mobile_app_custom_settings' : '',
        callback: function (data) {
          const $collapseBlock = $('#mobile_app_screens_app');
          if (selected_preset === 'C' || selected_preset !== 'C' && $collapseBlock.hasClass('in')) {
            $collapseBlock.collapse();
          }
        }
      });
    });
  });
})(Tygh, Tygh.$);