(function (_, $) {
  $(document).ready(function () {
    $.ceFormValidator('registerValidator', {
      class_name: 'cm-check-fur-code',
      message: _.tr('fur_marking_code_incorrect_format_alert'),
      func: function (id) {
        var regexp = /^.{2}-[0-9]{6}-.{10}$/,
          field_value = $('#' + id).val();
        if (field_value === "") {
          return true;
        }
        return $.trim(field_value).match(regexp) !== null;
      }
    });
  });
})(Tygh, Tygh.$);