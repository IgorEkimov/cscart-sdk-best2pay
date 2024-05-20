{* Select vendor *}
{$is_enabled_select_vendor = false}
{$is_hidden_select_vendor = false}
{$select_vendor_label = ("MULTIVENDOR"|fn_allowed_for) ? __("vendor") : __("owner")}
{capture name="select_vendor"}
    {if $picker_selected_company|default:""|fn_string_not_empty}
        {$is_hidden_select_vendor = true}
        <input type="hidden" name="company_id" value="{$picker_selected_company}" />
    {else}
        {include file="common/select_vendor.tpl"
            id="`$form_id`_company_id"
        }
    {/if}
{/capture}
{if $smarty.capture.select_vendor && $smarty.capture.select_vendor|trim !== ""}
    {$is_enabled_select_vendor = true}
{/if}

{* Export *}
{$is_enabled_select_vendor = $is_enabled_select_vendor scope=parent}
{$is_hidden_select_vendor = $is_hidden_select_vendor scope=parent}
{$select_vendor_label = $select_vendor_label scope=parent}