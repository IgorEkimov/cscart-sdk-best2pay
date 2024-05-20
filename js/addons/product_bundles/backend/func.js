function fn_product_bundles_get_price_schema(bundle_id) {
  const $ = Tygh.$,
    result = {},
    prices = {},
    elms = $('div#content_tab_products_' + bundle_id);
  let total_price = 0;
  $('.cm-bundle-' + bundle_id, elms).each(function () {
    const elm_id = $(this).val();
    if (elm_id !== '{product_bundle_id}') {
      prices[elm_id] = {};
      prices[elm_id]['amount'] = $('[name*=amount]', $(this).parent().parent()).val();
      if (!isNaN(parseInt(prices[elm_id]['amount']))) {
        prices[elm_id]['amount'] = parseInt(prices[elm_id]['amount']);
      } else {
        prices[elm_id]['amount'] = 0;
      }
      prices[elm_id]['price'] = parseFloat($("#item_price_product_bundle_".concat(bundle_id, "_").concat(elm_id), elms).val());
      prices[elm_id]['modifier'] = parseFloat($("#item_modifier_product_bundle_".concat(bundle_id, "_").concat(elm_id), elms).val());
      if (isNaN(prices[elm_id]['modifier'])) {
        prices[elm_id]['modifier'] = 0;
      }
      prices[elm_id]['modifier_type'] = $("#item_modifier_type_product_bundle_".concat(bundle_id, "_").concat(elm_id), elms).val();
      total_price += prices[elm_id]['price'] * prices[elm_id]['amount'];
    }
  });
  result['price_schema'] = prices;
  result['total_price'] = total_price;
  return result;
}
function fn_product_bundles_apply_discount(bundle_id) {
  const $ = Tygh.$,
    elms = $('div#content_tab_products_' + bundle_id);
  let global_discount = 0,
    discounted_price = 0;
  global_discount = parseFloat($('#elm_product_bundle_global_discount_' + bundle_id, elms).val());
  if (isNaN(global_discount)) {
    return false;
  }
  price_schema = fn_product_bundles_get_price_schema(bundle_id);
  const {
    price_schema: prices,
    total_price
  } = price_schema;
  if (global_discount > total_price) {
    global_discount = total_price;
    $('#elm_product_bundle_global_discount_' + bundle_id, elms).val(total_price);
  }
  for (i in prices) {
    discount = prices[i]['price'] / total_price * global_discount;
    discount = discount.toFixed(2);
    item_price = prices[i]['price'] - discount;
    item_price = item_price.toFixed(2);
    $("#item_modifier_product_bundle_".concat(bundle_id, "_").concat(i), elms).val(discount);
    $("#item_modifier_type_product_bundle_".concat(bundle_id, "_").concat(i), elms).val('by_fixed');
    $("[id*=item_display_price_product_bundle_".concat(bundle_id, "_").concat(i, "_]"), elms).text(prices[i]['price'].toFixed(2));
    $("[id*=item_discounted_price_product_bundle_".concat(bundle_id, "_").concat(i, "_]"), elms).text(item_price);
    discounted_price += item_price * prices[i]['amount'];
  }
  $("[id*=total_price_".concat(bundle_id, "]"), elms).text(total_price.toFixed(2));
  $("[id*=price_for_all_".concat(bundle_id, "]"), elms).text(discounted_price.toFixed(2));
  $("#elm_product_bundle_price_for_all_".concat(bundle_id), elms).val(discounted_price.toFixed(2));
  $("#elm_product_bundle_total_price_".concat(bundle_id), elms).val(total_price.toFixed(2));
}
function fn_product_bundles_recalculate(bundle_id) {
  const $ = Tygh.$,
    elms = $('div#content_tab_products_' + bundle_id);
  let discounted_price = 0;
  price_schema = fn_product_bundles_get_price_schema(bundle_id);
  const {
    price_schema: prices,
    total_price
  } = price_schema;
  for (i in prices) {
    switch (prices[i]['modifier_type']) {
      case 'to_fixed':
        item_price = prices[i]['modifier'];
        break;
      case 'by_fixed':
        item_price = prices[i]['price'] - prices[i]['modifier'];
        break;
      case 'to_percentage':
        item_price = prices[i]['modifier'] / 100 * prices[i]['price'];
        break;
      case 'by_percentage':
        item_price = prices[i]['price'] - prices[i]['modifier'] / 100 * prices[i]['price'];
        break;
      default:
        item_price = prices[i]['price'];
    }
    item_price = item_price < 0 ? 0 : item_price;
    item_price = item_price.toFixed(2);
    discounted_price += item_price * prices[i]['amount'];
    $("[id*=item_display_price_product_bundle_".concat(bundle_id, "_").concat(i, "_]"), elms).text(prices[i]['price'].toFixed(2));
    $("[id*=item_discounted_price_product_bundle_".concat(bundle_id, "_").concat(i, "_]"), elms).text(item_price);
  }
  $("[id*=price_for_all_".concat(bundle_id, "]"), elms).text(discounted_price.toFixed(2));
  $("[id*=total_price_".concat(bundle_id, "]"), elms).text(total_price.toFixed(2));
  $('#elm_product_bundle_price_for_all_' + bundle_id, elms).val(discounted_price.toFixed(2));
  $('#elm_product_bundle_total_price_' + bundle_id, elms).val(total_price.toFixed(2));

  // Clear global discount field
  $('#elm_product_bundle_global_discount_' + bundle_id, elms).val('');
}
function fn_product_bundles_share_discount(evt, bundle_id) {
  if (evt.keyCode) {
    code = evt.keyCode;
  } else if (evt.which) {
    code = evt.which;
  }
  if (code == 13) {
    fn_product_bundles_apply_discount(bundle_id);
  }
  return false;
}
(function (_, $) {
  $(_.doc).on('change', '.product_bundle_feature_variation', function () {
    fn_product_bundles_change_variation($(this));
  });
  function fn_product_bundles_change_variation($container, callback) {
    const $option = $container.find('option:selected'),
      productId = $option.data('caProductId'),
      url = $option.data('caChangeUrl'),
      targetId = $option.data('caTargetId'),
      index = $option.data('caRowIndex'),
      isChecked = $("#checkbox_id_".concat(index)).prop('checked');
    if ($option.length) {
      $.ceAjax('request', url, {
        method: 'POST',
        full_render: true,
        result_ids: targetId,
        data: {
          redirect_url: _.current_url,
          product_id: productId,
          row_index: index
        },
        callback: function (data) {
          $("#".concat(targetId, " [name=\"add_products_ids[]\"]")).prop('checked', true);
        }
      });
    }
  }
  function fn_product_bundles_set_company_id(bundleId, picker) {
    const companyId = $('#product_bundle_company_id_' + bundleId).val(),
      $productPicker = $("#content_tab_products_".concat(bundleId, " .cm-object-product-add--product-bundles")),
      $advancedPickerButton = $("#opener_picker_product_bundle_".concat(bundleId, "_"));

    //add selected company id for object picker
    if (picker) {
      picker.extendSearchRequestData({
        company_ids: [companyId]
      });
    } else if ($productPicker.length) {
      $productPicker.ceObjectPicker('extendSearchRequestData', {
        company_ids: [companyId]
      });
    }

    //add selected company id for advanced picker
    if ($advancedPickerButton.length) {
      let href = $advancedPickerButton.attr('href');
      href = href.replace(/&company_id=(.*?)&/, "&company_id=".concat(companyId, "&"));
      $advancedPickerButton.attr('href', href);
    }
  }

  // Hook add_js_item
  $.ceEvent('on', 'ce.picker_add_js_item', function (data) {
    if (data['var_prefix'] !== 'p') {
      return;
    }
    if (data.append_obj_content.length) {
      let price = parseFloat(data.item_id.price);
      if (isNaN(price)) {
        price = 0;
      }
      data['append_obj_content'] = data['append_obj_content'].str_replace('{product_bundle_id}', data['item_id']['product_id']).str_replace('{price}', price);
      let content = $("<tr>".concat(data['append_obj_content'], "</tr>"));

      // Price replacement
      content.find('span[id*=\'price_product_bundle\']').each(function () {
        $(this).text(price.toFixed(2));
      });
      if (data.item_id.any_variation === 'Y') {
        content.find("td[name=\"product_picker_object_name\"]").append($('<input>').attr({
          type: 'hidden',
          name: "item_data[products][".concat(data.item_id.product_id, "][any_variation]"),
          value: data.item_id.any_variation
        }));

        // ID replacement for products with 'Any variation' setting enabled
        content.find("input[name=\"item_data[products][".concat(data.item_id.product_id, "][product_id]\"]")).val(parseInt(data['var_id']));
      }
      data['append_obj_content'] = content.html();
    } else {
      const $amountField = $("[name=\"item_data[products][".concat(data.item_id.product_id, "][amount]\"]"));
      let productCount = +$amountField.val();
      $amountField.val(++productCount);
    }
  });
  $.ceEvent('on', 'ce.picker_transfer_js_items', function (data, frm) {
    for (let id in data) {
      let item = {
        ...data[id]
      };
      item.id = id;
      item.price = parseFloat($('#price_' + id).val());
      if (data[id].option && data[id].option.path) {
        // We have options, try to find their price modifiers
        for (let option_id in data[id].option.path) {
          const variant_id = data[id].option.path[option_id];
          const modifier = parseFloat($('#product_bundle_option_modifier_' + option_id + '_' + variant_id).val());
          if (!isNaN(modifier)) {
            item.price += modifier;
          }
        }
      }
      item.test = true;

      //We have variation features, get value any variation checkbox
      const $featuresContainer = $("#features_".concat(id), frm);
      if ($featuresContainer.length) {
        const $anyVariationCheckbox = $("[name=\"item_data[products][".concat(id, "][any_variation]\"]"), $featuresContainer);
        const isAnyVariation = $anyVariationCheckbox.prop('checked');
        const anyVariationDescription = $anyVariationCheckbox.data('caAocText');
        item.any_variation = isAnyVariation ? 'Y' : 'N';
        if (isAnyVariation) {
          item.option.desc = item.option.desc + ' ' + anyVariationDescription;
        }

        // product with any variation setting enabled should be added to picker separately
        if (isAnyVariation) {
          delete data[id];
          id += '-any-variation';
        }
      }
      data[id] = item;
    }
  });
  $.ceEvent('on', 'ce.commoninit', function (context) {
    const available_period_checkbox = context.find('.use_avail_period');
    if (available_period_checkbox.length !== 0) {
      available_period_checkbox.on('click', function () {
        const $ = Tygh.$,
          elm_obj = $(this),
          checked = elm_obj.prop('checked'),
          bundle_id = $.trim(elm_obj.data('id'));
        $("input#elm_product_bundle_avail_from_".concat(bundle_id, ",input#elm_product_bundle_avail_till_").concat(bundle_id)).prop('disabled', !checked);
      });
      available_period_checkbox.closest('form').on('reset', function () {
        const bundle_id = $.trim(available_period_checkbox.data('id')),
          checked = available_period_checkbox.attr('checked') ? 1 : 0;
        $("input#elm_product_bundle_avail_from_".concat(bundle_id, ",input#elm_product_bundle_avail_till_").concat(bundle_id)).prop('disabled', !checked);
      });
    }
    const bundleId = $('[name="item_id"]', context).val();
    $.ceEvent('on', "ce.picker_js_action_product_bundle_company_id_".concat(bundleId, "_selector"), function (elm) {
      fn_product_bundles_set_company_id(bundleId);
    });
  });
  $.ceEvent('on', 'ce.object_picker.change', function (object, select) {
    if (object.$elem.hasClass('cm-object-product-add--product-bundles') && select.length > 0) {
      const products = {},
        id = select[0].id,
        caResultId = object.options.extendedPickerId;
      products[id] = {
        'price': select[0].price,
        'value': select[0].text,
        'option': {
          'path': {},
          'desc': select[0].data.has_options ? "<span>".concat(_.tr('options'), ":  </span>").concat(_.tr('any_option_combinations')) : select[0].data.any_variation === 'Y' ? _.tr('product_bundles_any_variation') : '',
          'aoc': select[0].data.aoc
        },
        'any_variation': select[0].data.any_variation === 'Y' ? 'Y' : 'N'
      };
      $.cePicker('add_js_item', caResultId, products, 'p', {});
    }
  });
  $.ceEvent('on', 'ce.object_picker.object_selected', function (object, select) {
    if (object.$elem.hasClass('cm-object-product-add--product-bundles')) {
      $(object.$elem).ceObjectPicker('unselectObjectId', select.id);
    }
  });
  $.ceEvent('on', 'ce.formpost_item_update_form_product_bundle', function ($frm, $elm) {
    const bundleId = $frm.find('[name="item_id"]').val(),
      confirmTextObj = $elm.data('caConfirmText');
    if (confirmTextObj) {
      let productCount = 0;
      $frm.find('.product-bundles-table .product-picker__amount:not(:disabled)').each(function () {
        const count = +$(this).val();
        productCount += count ? count : 0;
      });
      let confirmText = productCount === 0 ? confirmTextObj.emptyProductBundle : productCount === 1 ? confirmTextObj.withOneProductBundle : '';
      if (confirmText.length) {
        if (confirm(fn_strip_tags(confirmText)) === false) {
          return false;
        }
      }
    }
    fn_product_bundles_recalculate(bundleId);
    fn_product_bundles_apply_discount(bundleId);
  });
  $.ceEvent('on', 'ce.object_picker.inited', function (object) {
    if (!object.$elem.hasClass('cm-object-product-add--product-bundles')) {
      return;
    }
    const bundleId = object.$elem.closest('[name="item_update_form_product_bundle"]').find('[name="item_id"]').val();
    fn_product_bundles_set_company_id(bundleId, object);
  });
})(Tygh, Tygh.$);