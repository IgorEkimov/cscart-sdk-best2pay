(function (_, $) {
  $(document).ready(function () {
    var $mobileMenuToggler = $('.mobile-menu-toggler');
    if (!$mobileMenuToggler.length) {
      return;
    }

    // Toggle mobile navbar
    $(document).on('click', '.mobile-menu-toggler', toggleMobileMenu);
    $(document).on('click', '.mobile-menu-closer', toggleMobileMenu);
  });
  $.ceEvent('on', 'ce.dialogshow', function ($context) {
    toggleMobileMenu(undefined, false);
  });
  function toggleMobileMenu(e, isOpen) {
    $('.cs-main-menu').toggleClass('open', isOpen);
    $('body').toggleClass('noscrolling', isOpen);
  }

  // Expand the search field when clicked
  $(document).on('focus blur', '[data-ca-search-menu="input"]', function (e) {
    // Ignore clicks on the search button
    if ($(e.relatedTarget).closest('[data-ca-search-menu="form"]').length) {
      return;
    }
    const isFocus = e.type === 'focusin';
    $('#top_bar_left').toggleClass('top-bar__left--mobile-hidden', isFocus);
    $('#top_bar_right').toggleClass('top-bar__right--mobile-hidden', isFocus);
    $('[data-ca-top-bar="inner"]').toggleClass('top-bar__inner--one-column', isFocus);
  });
})(Tygh, Tygh.$);