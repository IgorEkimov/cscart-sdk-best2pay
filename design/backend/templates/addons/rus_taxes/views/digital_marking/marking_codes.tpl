{script src="js/addons/rus_taxes/mark_codes_form_validation.js"}

{if $order_id && $order_marked_items}
    {$product_name_characters_threshold = 60}
    <div class="digital-marking-marking-codes">
        <form action="{""|fn_url}"
            method="post"
            enctype="multipart/form-data"
            class="form-horizontal form-edit cm-ajax cm-form-dialog-closer digital-marking-marking-codes__form"
            name="marking_codes_form"
        >
            <input type="hidden" name="return_url" value="{"orders.details&order_id=`$order_id`"}" />
            <input type="hidden" name="order_id" value="{$order_id}" />
            <input type="hidden" name="result_ids" value="content_general" />

            <div class="table-responsive-wrapper">
                <table width="100%" class="table table-middle table--relative table-responsive">
                    <thead>
                        <tr>
                            <th width="3%"></th>
                            <th width="7%"></th>
                            <th width="50%">{__("product")}</th>
                            <th width="10%">{__("price")}</th>
                            <th class="center" width="10%">{__("quantity")}</th>
                            <th width="10%" class="right">&nbsp;{__("subtotal")}</th>
                        </tr>
                    </thead>
                    {foreach $order_marked_items as $item_id => $order_marked_item}
                        {$product_characters = $order_marked_item.product|count_characters:true}
                        <tbody id="order_marked_item_{$item_id}">
                            <tr class="digital-marking-marking-codes__product">
                                <td>
                                    {include file="buttons/button.tpl"
                                        but_role="button-icon"
                                        but_id="on_order_marked_item_`$item_id`_data"
                                        but_meta="btn cm-combination hand hidden"
                                        but_icon="icon-caret-right"
                                        title=__("expand_sublist_of_items")
                                        tabindex="-1"
                                    }
                                    {include file="buttons/button.tpl"
                                        but_role="button-icon"
                                        but_id="off_order_marked_item_`$item_id`_data"
                                        but_meta="btn cm-combination hand"
                                        but_icon="icon-caret-down"
                                        title=__("collapse_sublist_of_items")
                                        tabindex="-1"
                                    }
                                </td>
                                <td>
                                    {include file="common/image.tpl"
                                        image=$order_marked_item.main_pair.icon|default:$order_marked_item.main_pair.detailed
                                        image_id=$order_marked_item.main_pair.image_id
                                        image_width=$settings.Thumbnails.product_admin_mini_icon_width
                                        image_height=$settings.Thumbnails.product_admin_mini_icon_height
                                        show_detailed_link=false
                                    }
                                </td>
                                <td data-th="{__("product")}">
                                    <strong>#{$order_marked_item@iteration}</strong>
                                    <span>•<span>
                                    {if !$order_marked_item.deleted_product}
                                    <a href="{"products.update?product_id=`$order_marked_item.product_id`"|fn_url}"
                                        tabindex="-1"
                                        {if $product_characters > $product_name_characters_threshold}
                                            title="{$order_marked_item.product}"
                                        {/if}
                                    >{/if}
                                        {$order_marked_item.product|truncate:
                                            $product_name_characters_threshold:
                                            "...":true:true nofilter}
                                    {if !$order_marked_item.deleted_product}</a>{/if}
                                    {if $order_marked_item.product_code}
                                        <div class="products-hint">
                                            <p class="products-hint__code">
                                                {__("sku")}:{$order_marked_item.product_code}
                                            </p>
                                        </div>
                                    {/if}
                                </td>
                                <td class="nowrap" data-th="{__("price")}">
                                    {if $order_marked_item.extra.exclude_from_calculate}
                                        {__("free")}
                                    {else}
                                        {include file="common/price.tpl" value=$order_marked_item.original_price}
                                    {/if}
                                </td>
                                <td class="center" data-th="{__("quantity")}">
                                    {$order_marked_item.amount}
                                </td>
                                <td class="right" data-th="{__("subtotal")}">
                                    <span>
                                        {if $order_marked_item.extra.exclude_from_calculate}
                                            {__("free")}
                                        {else}
                                            {include file="common/price.tpl" value=$order_marked_item.display_subtotal}
                                        {/if}
                                    </span>
                                </td>
                            </tr>
                            {if $order_marked_item.product_options}
                                <tr class="digital-marking-marking-codes__row-description">
                                    <td colspan="2" class="mobile-hide digital-marking-marking-codes__cell-description"></td>
                                    <td colspan="4" class="digital-marking-marking-codes__cell-description">
                                        {include file="common/options_info.tpl"
                                            product_options=$order_marked_item.product_options
                                        }
                                    </td>
                                </tr>
                            {/if}
                        </tbody>
                        <tbody id="order_marked_item_{$item_id}_data">
                            {foreach $order_marked_item.marking_codes_data as $data}
                                <tr>
                                    <td colspan="6"
                                        data-th="{__("rus_taxes.item_n", [$data@iteration])}"
                                        class="digital-marking-marking-codes__cell-code"
                                    >
                                        {$marking_code_format_gs1m = "MarkingCodeFormats::GS1M"|enum}
                                        <input type="hidden"
                                            name="marking_codes_data[{$item_id}][{$data@index}][marking_code_format]"
                                            value="{if $order_marked_item.mark_code_type}{$order_marked_item.mark_code_type}{else}{$marking_code_format_gs1m}{/if}"
                                        />
                                        <div class="control-group digital-marking-marking-codes__control-group">
                                            <label for="marking_code_{$item_id}_{$data@iteration}"
                                                class="control-label cm-trim {if $order_marked_item.mark_code_type === "MarkingCodeFormats::FUR"|enum}cm-check-fur-code{/if}"
                                            >
                                                {__("rus_taxes.item_n", [$data@iteration])}
                                            </label>
                                            <div class="controls">
                                                <input
                                                    type="text"
                                                    class="input-fill"
                                                    id="marking_code_{$item_id}_{$data@iteration}"
                                                    name="marking_codes_data[{$item_id}][{$data@index}][marking_code]"
                                                    value="{$data.marking_code}"
                                                >
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    {/foreach}
                </table>
            </div>

            <div class="well">
                {__("rus_taxes.marking_codes_tip",
                    [
                        "[tab]" => "<code><kbd>Tab ↹</kbd></code>",
                        "[shift_tab]" => "<code><kbd><kbd>Shift</kbd> + <kbd>Tab ↹</kbd></kbd></code>"
                    ]
                )}
            </div>

            <div class="buttons-container">
                {include file="buttons/save_cancel.tpl"
                    but_name="dispatch[digital_marking.save_codes]"
                    cancel_action="close"
                    save=true
                }
            </div>
        </form>
    </div>
{else}
    {__("no_data")}
{/if}
