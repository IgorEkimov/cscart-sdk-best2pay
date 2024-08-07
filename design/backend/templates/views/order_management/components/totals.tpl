<div class="pull-right order-notes statistic">
    <div class="table-wrapper">
        <table>
            <tr>
                <td class="totals-label">&nbsp;</td>
                <td class="totals"><h4>{__("totals")}</h4></td>
            </tr>
            <tr>
                <td class="statistic-label" width="200px">{__("subtotal")}:</td>
                <td class="right" width="100px">{include file="common/price.tpl" value=$cart.display_subtotal}</td>
            </tr>

            {if ($cart.discount|floatval)}
                <tr>
                    <td class="statistic-label">{__("including_discount")}:</td>
                    <td class="right">{include file="common/price.tpl" value=$cart.discount}</td>
                </tr>
            {/if}

            <tr class="toggle-elm">
                <td class="statistic-label">
                <label>{__("order_discount")}
                    <input type="hidden" name="stored_subtotal_discount" value="N" />
                    <input type="checkbox" class="valign cm-combinations" name="stored_subtotal_discount" value="Y" {if $cart.stored_subtotal_discount == "Y" && $cart.order_id}checked="checked"{/if} {if !$cart.order_id}disabled="disabled"{/if} id="sw_manual_subtotal_discount" /></label>
                </td>
                <td class="right">
                <span {if $cart.stored_subtotal_discount == "Y"}style="display: none;"{/if} data-ca-switch-id="manual_subtotal_discount">
                {include file="common/price.tpl" value=$cart.subtotal_discount|default:$cart.original_subtotal_discount show_currency=false}</span>
                    <span {if $cart.stored_subtotal_discount != "Y"}style="display: none;"{/if} data-ca-switch-id="manual_subtotal_discount">
                        {include file="common/price.tpl" 
                            value=$cart.subtotal_discount|default:$cart.original_subtotal_discount 
                            view="input" 
                            input_name="subtotal_discount" 
                            input_val=$cart.subtotal_discount 
                            class="input-small" 
                        }
                    </span>
                </td>
            </tr>

            <tr>
                <td class="statistic-label">
                    <label>{__("manually_set_tax_rates")}
                    <input type="hidden" name="stored_taxes" value="N" />
                    <input type="checkbox" class="cm-combinations" name="stored_taxes" value="Y" {if $cart.stored_taxes == "Y"}checked="checked"{/if} id="sw_manual_taxes" {if !$cart.order_id}disabled="disabled"{/if} /></label>
                </td>
                <td class="right">&nbsp;</td>
            </tr>

            {foreach from=$cart.taxes item="tax" key=key name="fet"}
            <tr class="toggle-elm nowrap">
                <td class="statistic-label">&nbsp;<span>&middot;</span>&nbsp;{$tax.description}{if $tax.price_includes_tax == "Y" && $settings.Appearance.cart_prices_w_taxes != "Y"}&nbsp;{__("included")}{/if}{strip}(<span {if $cart.stored_taxes == "Y"}class="hidden"{/if} data-ca-switch-id="manual_taxes">{include file="common/modifier.tpl" mod_value=$tax.rate_value mod_type=$tax.rate_type}</span>
            <span {if $cart.stored_taxes != "Y"}class="hidden"{/if} data-ca-switch-id="manual_taxes">
                <input type="text" class="cm-numeric input-small" size="5" name="taxes[{$key}]" data-a-sign="% " data-m-dec="3" data-a-pad="false" data-p-sign="s" value="{$tax.rate_value}" /></span>){/strip}
                </td>
                <td class="right">{include file="common/price.tpl" value=$tax.tax_subtotal}</td>
            </tr>
            {/foreach}

            {if !empty($cart.product_groups)}
                {foreach from=$cart.product_groups item="group" key=group_key}
                    {if !empty($group.chosen_shippings)}
                        {foreach from=$group.chosen_shippings item="shipping" key=shipping_key}
                            {if isset($cart.stored_shipping.$group_key.$shipping_key)}
                                {$custom_ship_exists = true}
                            {else}
                                {$custom_ship_exists = false}
                            {/if}
                            <tr>
                                <td class="right nowrap">
                                    <label>{$shipping.shipping}
                                    <input type="hidden" name="stored_shipping[{$group_key}][{$shipping_key}]" value="N" />
                                    <input type="checkbox" class="valign cm-combinations" name="stored_shipping[{$group_key}][{$shipping_key}]" value="Y" {if $custom_ship_exists}checked="checked"{/if} id="sw_manual_shipping_{$group_key}_{$shipping_key}" /></label>
                                </td>
                                <td class="right">
                                    <span {if $custom_ship_exists}style="display: none;"{/if} data-ca-switch-id="manual_shipping_{$group_key}_{$shipping_key}">
                                        {include file="common/price.tpl" value=$cart.display_shipping_cost|default:0}
                                    </span>
                                    <span {if !$custom_ship_exists}style="display: none;"{/if} data-ca-switch-id="manual_shipping_{$group_key}_{$shipping_key}">
                                        {if isset($cart.stored_shipping.$group_key.$shipping_key)}
                                            {$stored_shipping_cost = $cart.stored_shipping.$group_key.$shipping_key|fn_format_price:$primary_currency:null:false}
                                        {else}
                                            {$stored_shipping_cost = $cart.display_shipping_cost|fn_format_price:$primary_currency:null:false}
                                        {/if}
                                        {include file="common/price.tpl" value=$stored_shipping_cost view="input" input_name="stored_shipping_cost[`$group_key`][`$shipping_key`]" class="input-small"}
                                    </span>
                                </td>
                            </tr>
                        {/foreach}
                    {/if}
                {/foreach}
            {/if}

        {if $cart.coupons && empty($cart.disable_promotions)}
            <input type="hidden" name="c_id" value="0" id="c_id" />
            <tr>
                <td class="statistic-label muted strong">{__("coupon")}:</td>
                <td>&nbsp;</td>
            </tr>
            {foreach from=$cart.coupons item="coupon" key="key"}
                <tr>
                    <td class="statistic-label"> {$key}&nbsp;
                    {include file="buttons/button.tpl" but_href="order_management.delete_coupon?c_id=`$key|escape:url`" but_icon="icon-trash" but_role="delete_item" but_meta="cm-ajax cm-post" but_target_id=$result_ids}</td>
                    <td class="right">&nbsp;</td>
                </tr>
            {/foreach}
        {/if}

        <tr id="payment_surcharge_line">
            <td class="statistic-label">{__("payment_surcharge")}</td>
            <td class="right">{include file="common/price.tpl" value=$cart.payment_surcharge span_id="payment_surcharge_value" class="list_price"}</td>
        </tr>

        {* FIXME: Order total should include surcharge when calculating whole order total *}
        {$cart.total = $cart.total + $cart.payment_surcharge}

        {hook name="order_management:totals"}
        {/hook}

            <tr class="total nowrap cm-om-totals-price">
                <td class="statistic-label"><h4>{__("total_cost")}</h4></td>
                <td class="right price">
                    {include file="common/price.tpl" value=$cart.total span_id="cart_total"}
                </td>
            </tr>

            <tr class="hidden cm-om-totals-recalculate">
                <td colspan="2">
                    <button class="btn cm-ajax" type="submit" name="dispatch[order_management.update_totals]" value="Recalculate" data-ca-check-filter="#om_ajax_update_totals">{include_ext file="common/icon.tpl" class="icon-refresh"}{__("recalculate_totals")}</button>
                </td>
            </tr>

        </table>
    </div>
</div>
