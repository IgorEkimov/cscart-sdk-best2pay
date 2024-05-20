{if $field != $order_by}
    {$direction = "none"}
    {$order_direction = "asc"}
{else}
    {if $direction == "asc"}
        {$order_direction = "desc"}
    {else}
        {$order_direction = "asc"}
    {/if}
{/if}
{strip}
<a class="cm-ajax cm-ajax-cache" href="{"`$url`?order_by=`$field`,`$order_direction`&debugger_hash=`$debugger_hash`"|fn_url}" data-ca-target-id="{$target_id}">
    {$text}
    {if $direction == "none"}
        {include_ext file="common/icon.tpl" source="icon-asc"}
        {include_ext file="common/icon.tpl"
            source="icon-desc"
            data=[
                style => "margin-left: -2px;"
            ]
        }
    {else}
        {include_ext file="common/icon.tpl" source=$order_direction}
    {/if}
</a>
{/strip}
