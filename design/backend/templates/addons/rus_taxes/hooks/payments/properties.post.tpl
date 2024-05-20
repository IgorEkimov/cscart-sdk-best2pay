{script src="js/addons/rus_taxes/func.js"}

{$statuses=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}

{$send_receipt_variants = $send_receipt_variants|default:["dont_send" => ["description" => __("rus_taxes.send_receipt_dont_send_description")]]}
{$send_types = $send_receipt_types|default:[]}

{$send_receipts_keys = $send_receipt_variants|array_keys}
{if "via_payment_method"|in_array:$send_receipts_keys}
    {$default_send_receipt = "via_payment_method"}
{else}
    {$default_send_receipt = "dont_send"}
{/if}

{if $payment.send_receipt && $payment.send_receipt|in_array:$send_receipts_keys}
    {$selected_send_receipt = $payment.send_receipt}
{else}
    {$selected_send_receipt = $default_send_receipt}
{/if}

{if $payment.processor}
    {$processor_name = $payment.processor}
{else}
    {$processor_name = $processor_data.processor|default:"payment method"}
{/if}

{$ffd_versions = [
"1.2" => __("rus_taxes.ffd_12"),
"1.05" => __("rus_taxes.ffd_105")
]}
{$sendReceiptDescription = ""}

{include file="common/subheader.tpl" title=__("online_cash_register_connection") target="#payment_online_cash_registry_settings_`$id`"}


<div data-ca-rus-taxes="onlineCashRegistrySettings" class="collapse in" id="payment_online_cash_registry_settings_{$id}">
    <div class="control-group {if $send_receipt_variants|count < 2}hidden{/if}">
        <label class="control-label cm-multiple-radios" for="elm_send_receipt_{$id}">{__("rus_taxes.connection_mechanism")}:</label>
        <div class="controls">
            <div class="row-fluid" id="elm_send_receipt_{$id}">
                <div class="span6">
                    {foreach $send_receipt_variants as $send_receipt_variant => $send_receipt}
                        <label class="radio" for="elm_send_receipt_{$send_receipt_variant}_{$id}">
                            <input type="radio"
                                   name="payment_data[send_receipt]"
                                   id="elm_send_receipt_{$send_receipt_variant}_{$id}"
                                   data-ca-rus-taxes="sendReceipt"
                                    {if $selected_send_receipt === $send_receipt_variant}
                                        checked="checked"
                                    {/if}
                                   value="{$send_receipt_variant}"
                            />
                            {__("rus_taxes.send_receipt_`$send_receipt_variant`", ['[payment_method]' => $processor_name])}
                        </label>

                        {* Save text to make two columns without duplicating the loop *}
                        {capture name="sendReceiptDescription"}
                            <div data-ca-rus-taxes="sendReceiptDescription"
                                 data-ca-rus-taxes-send-receipt="{$send_receipt_variant}"
                                 class="description {if $selected_send_receipt !== $send_receipt_variant}hidden{/if}"
                            >
                                <small>
                                    {$send_receipt.description}
                                </small>
                            </div>
                        {/capture}
                        {$sendReceiptDescription = "`$sendReceiptDescription``$smarty.capture.sendReceiptDescription`"}

                    {/foreach}
                </div>
                <div class="span6">
                    {$sendReceiptDescription nofilter}
                </div>
            </div>
        </div>
    </div>

    <div data-ca-rus-taxes="onlineCashRegistrySettingsParams" class="{if $selected_send_receipt === "dont_send"}hidden{/if}">
        <div class="control-group">
            <label class="control-label" for="elm_send_{$id}">
                {__("send")}:
            </label>
            <div class="controls">
                {foreach $send_types as $send_type => $send_type_options}
                    {* Turn off refund receipts functionality until logic remade *}
                    {if $send_type === "send_refund_receipt"}
                        <input type="hidden" name="payment_data[processor_params][{$send_type}]" value="{"YesNo::NO"|enum}" />
                    {else}
                        <label class="checkbox inline" for="elm_{$send_type}_{$id}">
                            <input type="hidden"
                                   name="payment_data[processor_params][{$send_type}]"
                                   value="{if $send_type === "send_prepayment_receipt" && $send_type_options.disabled}{"YesNo::YES"|enum}{else}{"YesNo::NO"|enum}{/if}"
                            />
                            <input type="checkbox"
                                   name="payment_data[processor_params][{$send_type}]"
                                   id="elm_{$send_type}_{$id}"
                                   data-ca-rus-taxes="sendType"
                                   value="{"YesNo::YES"|enum}"
                                    {if $payment.processor_params[$send_type]|default:("YesNo::YES"|enum) === "YesNo::YES"|enum
                                        && $payment.processor_params.currency|default:"RUB" === "RUB"
                                    }
                                        checked="checked"
                                    {/if}
                                    {if $send_type === "send_prepayment_receipt" && $send_type_options.disabled}
                                        disabled="disabled"
                                    {/if}
                            />
                            {__("rus_taxes.`$send_type`")}
                        </label>
                    {/if}
                {/foreach}
            </div>
            <div class="controls">
                <p class="muted description">{__("rus_taxes.send_receipt_notice")}</p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="confirmed_order_status_{$id}">
                {__("rus_taxes.order_status_for_final_receipt")}:
            </label>
            <div class="controls">
                <select name="payment_data[processor_params][final_success_status]"
                        id="confirmed_order_status_{$id}"
                >
                    {foreach $statuses as $key => $item}
                        <option value="{$key}"
                                {if $payment.processor_params.final_success_status|default:("OrderStatuses::COMPLETE"|enum) === $key}
                                    selected="selected"
                                {/if}
                        >{$item}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="ffd_version_{$id}">{__("rus_taxes.ffd_version")}:</label>
            <div class="controls">
                <select name="payment_data[processor_params][ffd_version]" id="ffd_version_{$id}">
                    {foreach $ffd_versions as $key => $item}
                        <option value="{$key}"
                                {if $payment.processor_params.ffd_version|default:"1.2" === $key}
                                    selected="selected"
                                {/if}
                        >{$item}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
    <div class="{if $send_receipt_variants|count > 1}hidden{/if}">
        {__("rus_taxes.no_send_receipt_variants", ["[payment_methods]" => "https://www.cs-cart.ru/docs/{$smarty.const.PRODUCT_VERSION}/user_guide/payment_methods/", "[atol_online]" => "https://www.cs-cart.ru/docs/{$smarty.const.PRODUCT_VERSION}/user_guide/addons/atol_online/", "[commerceml]" => "https://www.cs-cart.ru/docs/{$smarty.const.PRODUCT_VERSION}/user_guide/addons/commerceml/"])}
    </div>
</div>
