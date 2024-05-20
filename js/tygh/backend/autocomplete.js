(function (_, $) {
  _.doc.addEventListener('DOMContentLoaded', () => {
    $('form:not(.autocomplete-on)').each((i, elem) => {
      let $hiddenInput = '<input type="password" class="hidden" autocomplete="new-password"/> <!-- turn off Chrome autocomplete -->';
      $(elem).prepend($hiddenInput);
    });
  });
})(Tygh, Tygh.$);