(function (_, $) {
  // Open the save search popup by clicking the "Save this search as" button in the saved searches panel
  $(_.doc).on('click', '[data-ca-saved-search-horizontal="searchSave"]', openSearchSavePopup);

  // Open search save popup
  function openSearchSavePopup() {
    const $searchSaveButton = $('#content_search_filters [data-ca-search-filters="form"] .cm-search-filters-search-save', $(this).closest('#content_top_navigation'));
    if (!$searchSaveButton.length) {
      return;
    }
    $(document.getElementById($searchSaveButton.data('caTargetId'))).ceDialog('open', {
      width: 'auto',
      height: 'auto',
      dialogClass: 'dialog-auto-sized'
    });
  }
})(Tygh, Tygh.$);