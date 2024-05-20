(function (_, $) {
  // Horizontal position of dropdown menu relative to window
  $(_.doc).on('click mouseenter', '[data-toggle="dropdown"]', function (event) {
    const $btnWrapper = $(this).parent();
    const $dropdownMenu = $btnWrapper.children('.dropdown-menu');
    const isSubmenuBtnWrapper = $btnWrapper.hasClass('dropdown-submenu');
    if (!$dropdownMenu.length || event.type === 'mouseenter' && !isSubmenuBtnWrapper) {
      return;
    }
    ;
    const $container = $btnWrapper.closest('.object-container').length ? $btnWrapper.closest('.object-container') : $('[data-ca-element="mainContainer"]');
    const addedClass = isSubmenuBtnWrapper ? 'dropdown-menu-to-right' : 'pull-right';
    $dropdownMenu.removeClass(addedClass);
    if (_.language_direction === 'ltr' && $dropdownMenu.offset().left + $dropdownMenu.outerWidth() >= $container.offset().left + $container.outerWidth() || _.language_direction === 'rtl' && $dropdownMenu.offset().left <= $container.offset().left) {
      $dropdownMenu.addClass(addedClass);
    }
  });
})(Tygh, Tygh.$);