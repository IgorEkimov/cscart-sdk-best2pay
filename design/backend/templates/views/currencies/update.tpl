{if $currency.currency_code}
    {assign var="id" value=$currency.currency_id}
{else}
    {assign var="id" value="0"}    
{/if}
{$tabs_count = (fn_allowed_for("MULTIVENDOR:ULTIMATE") || $is_sharing_enabled) ? 2 : 1}

<div id="content_group{$id}">

<form action="{""|fn_url}" enctype="multipart/form-data" name="update_currency_form_{$id}" method="post" class=" form-horizontal{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">

    <input type="hidden" name="currency_id" value="{$id}" />
<input type="hidden" name="redirect_url" value="{$smarty.request.return_url}" />

<div class="tabs cm-j-tabs tabs--enable-fill tabs--count-{$tabs_count}">
    <ul class="nav nav-tabs">
        <li id="tab_general_{$id}" class="cm-js active"><a>{__("general")}</a></li>
        {if fn_allowed_for("MULTIVENDOR:ULTIMATE") || $is_sharing_enabled}
            <li id="tab_storefronts_{$id}" class="cm-js"><a>{__("storefronts")}</a></li>
        {/if}
    </ul>
</div>

<div class="cm-tabs-content" id="content_tab_general_{$id}">
<fieldset>
    <div class="control-group">
        <label class="control-label cm-required" for="description_{$id}">{__("name")}:</label>
        <div class="controls">
            <input type="text" name="currency_data[description]" value="{$currency.description}" id="description_{$id}" size="18">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label cm-required" for="currency_code_{$id}">{__("code")}:</label>
        <div class="controls">
            <input type="text" name="currency_data[currency_code]" size="8" value="{$currency.currency_code}" id="currency_code_{$id}" onkeyup="var matches = this.value.match(/^(\w*)/gi);  if (matches) this.value = matches;">
        </div>
    </div>
    
    
    {if $id}
    <div class="control-group">
        <label class="control-label" for="is_primary_currency_{$id}">{__("primary_currency")}:</label>
        <div class="controls">
            <input type="hidden" name="currency_data[coefficient]" value="1" />
            <input type="checkbox" name="currency_data[is_primary]" value="Y" {if $currency.is_primary == "Y"}checked="checked"{/if} onclick="Tygh.$('.cm-coefficient').prop('disabled', Tygh.$(this).prop('checked'))" id="is_primary_currency_{$id}">
        </div>
    </div>
    {/if}

    <div class="control-group">
        <label class="control-label cm-required" for="coefficient_{$id}">{__("currency_rate")}:</label>
        <div class="controls">
            <input type="text" name="currency_data[coefficient]" size="7" value="{$currency.coefficient}" id="coefficient_{$id}" class="cm-coefficient" {if $currency.is_primary == "Y"}disabled="disabled"{/if}>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="symbol_{$id}">{__("currency_sign")}:</label>
        <div class="controls">
            <input type="text" name="currency_data[symbol]" size="6" value="{$currency.symbol}" id="symbol_{$id}">
        </div>
    </div>
    
    {hook name="currencies:autoupdate"}{/hook}

    <div class="control-group">
        <label class="control-label" for="after_{$id}">{__("after_sum")}:</label>
        <div class="controls">
            <input type="hidden" name="currency_data[after]" value="N" />
            <input type="checkbox" name="currency_data[after]" value="Y" {if $currency.after == "Y"}checked="checked"{/if} id="after_{$id}">
            <p class="muted description">{__("tt_views_currencies_update_after_sum")}</p>
        </div>
    </div>

    {if !$id}
        {include file="common/select_status.tpl" input_name="currency_data[status]" id="add_currency" hidden=true}
    {/if}

    <div class="control-group">
        <label class="control-label" for="thousands_separator_{$id}">{__("ths_sign")}:</label>
        <div class="controls">
            <input type="text" name="currency_data[thousands_separator]" size="6" maxlength="6" value="{$currency.thousands_separator}" id="thousands_separator_{$id}">
            <p class="muted description">{__("tt_views_currencies_update_ths_sign")}</p>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="decimal_separator_{$id}">{__("dec_sign")}:</label>
        <div class="controls">
            <input type="text" name="currency_data[decimals_separator]" size="6" maxlength="6" value="{$currency.decimals_separator}" id="decimal_separator_{$id}">
            <p class="muted description">{__("tt_views_currencies_update_dec_sign")}</p>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="decimals_{$id}">{__("decimals")}:</label>
       <div class="controls">
            <input type="text" name="currency_data[decimals]" size="1" maxlength="2" value="{$currency.decimals|default:2}" id="decimals_{$id}">
           <p class="muted description">{__("tt_views_currencies_update_decimals")}</p>
       </div>
    </div>
    </fieldset>
<!--content_tab_general_{$id}--></div>

    {if fn_allowed_for("MULTIVENDOR:ULTIMATE")|| $is_sharing_enabled}
        <div class="hidden" id="content_tab_storefronts_{$id}">
            {$add_storefront_text = __("add_storefronts")}
            {include file="pickers/storefronts/picker.tpl"
                multiple=true
                input_name="currency_data[storefront_ids]"
                item_ids=$currency.storefront_ids
                data_id="storefront_ids"
                but_meta="pull-right"
                no_item_text=__("all_storefronts")
                but_text=$add_storefront_text
                view_only=($is_sharing_enabled && $runtime.company_id)
            }
        <!--content_tab_storefronts_{$id}--></div>
    {/if}

{hook name="currencies:tabs_content"}
{/hook}

{if ""|fn_allow_save_object:"":true}
    <div class="buttons-container">
        {include file="buttons/save_cancel.tpl" but_name="dispatch[currencies.update]" cancel_action="close" save=$id}
    </div>
{/if}

</form>

{hook name="currencies:tabs_extra"}
{/hook}

<!--content_group{$id}--></div>
