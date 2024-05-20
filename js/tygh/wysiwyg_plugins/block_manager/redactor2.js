(function (_, $) {
  let icon_magic_dark = '<span class="cs-icon cs-icon--type-magic redactor-custom-icon"><span class="hidden-accessible"></span><svg fill="#333" class="cs-icon__svg" focusable="false" aria-hidden="true" width="14" height="14" viewBox="0 0 20 20"><path d="m9.6318.710058c-.1799.05566-.34624.148152-.48844.271592-.14221.12344-.25717.27513-.33756.44541l-.088.19-.034 3.679-1.4.988c-.49757.34725-.99061.70095-1.479 1.061-.252.228-.427.688-.392 1.026.044.427.32.85.667 1.023.125.063 2.546.83904 2.616.83904.012 0-1.781 1.804-3.984 4.009-2.202 2.204-4.030003 4.059-4.061003 4.122-.043075.0944-.058338.1992-.044.302.017.226.15.434.387.604.191003.137.463003.171.643003.08.063-.031 1.918-1.859 4.122-4.061 1.32962-1.3351 2.66597-2.6635 4.009-3.985 0 .071.773 2.486.837 2.613.164.33.565.606.965.666.338.051.849-.13 1.074-.38.3592-.4854.7093-.9775 1.05-1.476l.943-1.357 3.647-.02.195-.079c.59-.237.92-.853.789-1.47204-.03-.1255-.0778-.24606-.142-.358-.349-.4897-.7054-.97409-1.069-1.453l-.97-1.296.545-1.604c.3-.883.567-1.702.593-1.821.114-.514-.14-1.077-.608-1.352-.2274-.12623-.4866-.18356-.746-.165-.159.02-.963.281-3.076 1l-.423.144-1.361-1.019c-.748-.561-1.44-1.056002-1.537-1.101002-.2686-.102421-.56106-.124655-.842-.064zm1.919 2.686002c.623.466 1.201.876 1.283.912.192.083.641.116.82.06.17-.054 2.846-.956 2.922-.986.045-.017-.066.345-.479 1.569l-.537 1.592.015.254c.009.159.042.313.087.416.04.089.485.714.989 1.388s.917 1.234.917 1.244c0 .011-.754.023-1.675.029l-1.675.009-.19.089c-.1241.06234-.2397.14024-.344.23204-.087.08-.529.681-1.008 1.37-.469.675-.864 1.216-.877 1.202-.014-.014-.232-.693-.485-1.508-.519-1.66904-.561-1.76104-.908-1.99204-.16-.107-.434-.204-1.673-.589-.5061-.15256-1.0092-.31493-1.509-.487-.015-.013.552-.434 1.261-.934 1.377-.971 1.474-1.056 1.621-1.424.077-.19.077-.21.088-1.818l.011-1.626.106.076c.059.042.617.457 1.24.922z"></path></svg></span>';
  $.Redactor.prototype.blockManager = function () {
    return {
      areHandlersSetup: false,
      isBlockSelected: false,
      $activeNewBlockDialogContainer: null,
      $activeBlockSelectionDialogContainer: null,
      selectBlock: function (uid) {
        $('.cm-select-bm-block').removeClass('select-block--active');
        $('[data-ca-block-uid="' + uid + '"]').addClass('select-block--active');
        this.blockManager.isBlockSelected = true;
        this.blockManager.insert();
      },
      loadBlocks: function () {
        var self = this.blockManager;
        if (self.areHandlersSetup) {
          return;
        }
        $.ceEvent('on', 'dispatch_event_pre', function (e, jelm, processed) {
          if (e.type !== 'click') {
            return;
          }

          // select existing block
          if (jelm.hasClass('cm-select-bm-block') || jelm.parents('.cm-select-bm-block').length) {
            var $selectedBlock = jelm.hasClass('cm-select-bm-block') ? jelm : jelm.parents('.cm-select-bm-block');
            self.selectBlock($selectedBlock.data('caBlockUid'));
            processed.status = processed.to_return = true;
          }

          // create new block
          if (jelm.hasClass('cm-create-bm-block') || jelm.parents('.cm-create-bm-block').length) {
            var $createdBlock = jelm.hasClass('cm-create-bm-block') ? jelm : jelm.parents('.cm-create-bm-block');
            var data = $createdBlock.data(),
              blockType = data.caBlockType,
              blockName = data.caBlockName;
            let newBlockHref = 'block_manager.update_block?' + 'block_data[type]=' + blockType + '&snapping_data[grid_id]=0' + '&ajax_update=1' + '&r_result_ids=block_selection' + '&r_url=' + encodeURIComponent(fn_url('block_manager.block_selection?purpose=wysiwyg'));
            let $newBlockDialogContainer = $('#new_block_' + blockType);
            if (!$newBlockDialogContainer.length) {
              $newBlockDialogContainer = $('<div id="new_block_' + blockType + '"></div>').appendTo('body');
            }
            $newBlockDialogContainer.ceDialog('open', {
              href: fn_url(newBlockHref),
              title: Tygh.tr('add_block') + ': ' + blockName,
              destroyOnClose: true
            });
            self.isBlockSelected = false;
            self.$activeNewBlockDialogContainer = $newBlockDialogContainer;
            processed.status = processed.to_return = true;
          }

          // hightlight selected block
          $.ceEvent('on', 'ce.formajaxpost_block_0_update_form', function (response) {
            if (!response.block_data || self.isBlockSelected) {
              return;
            }
            self.selectBlock(response.block_data.unique_id);
          });
        });
        self.areHandlersSetup = true;
      },
      init: function () {
        var toolbarButton = this.button.add('blockManager', Tygh.tr('block_manager'));
        this.button.setIcon(toolbarButton, icon_magic_dark);
        this.button.addCallback(toolbarButton, this.blockManager.openPicker);
      },
      openPicker: function () {
        var isBlockManagerAvailable = this.$element.data('caIsBlockManagerAvailable') === 1;
        if (!isBlockManagerAvailable) {
          $.ceNotification('show', {
            title: _.tr('warning'),
            message: _.tr('text_select_vendor'),
            type: 'W'
          });
          return;
        }
        var self = this.blockManager;
        this.selection.save();
        self.isBlockSelected = false;
        self.$activeBlockSelectionDialogContainer = null;
        self.$activeNewBlockDialogContainer = null;
        var $blockSelectionDialogContainer = $('#block_selection');
        if (!$blockSelectionDialogContainer.length) {
          $blockSelectionDialogContainer = $('<div id="block_selection" title="' + fn_strip_tags(Tygh.tr('select_block')) + '"></div>').appendTo('body');
        }
        $blockSelectionDialogContainer.ceDialog('open', {
          self: this,
          href: fn_url('block_manager.block_selection?purpose=wysiwyg'),
          onClose: function () {
            this.self.selection.restore();
          }
        });
        self.$activeBlockSelectionDialogContainer = $blockSelectionDialogContainer;
        $.ceEvent('on', 'ce.dialogshow', function (dialog) {
          if ($(dialog).attr('id') === 'block_selection') {
            $(dialog).dialog('option').self.blockManager.loadBlocks();
          }
        });
      },
      insert: function () {
        var self = this.blockManager,
          $block = $('.select-block--active', self.$activeBlockSelectionDialogContainer),
          blockData = $block.data();
        if (self.$activeNewBlockDialogContainer) {
          self.$activeNewBlockDialogContainer.ceDialog('close');
        }
        self.$activeBlockSelectionDialogContainer.ceDialog('close');
        this.placeholder.hide();
        this.buffer.set();
        this.air.collapsed();
        this.selection.restore();
        this.insert.html('<p><b' + ' title="' + blockData.caBlockName + '"' + ' class="wysiwyg-block-loader cm-block-loader cm-block-loader--' + blockData.caBlockUid + '"' + ' ></b></p>');
      }
    };
  };
})(Tygh, Tygh.$);