{if $notes}
    {include file="common/help.tpl" content=$notes id=$notes_id}
{/if}
<h4 class="subheader {$meta} {if $target} hand{/if}" {if $target}data-toggle="collapse" data-target="{$target}"{/if}>
    {$title}
    {if $additional_id}<small class="muted"> #{$additional_id}</small>{/if}
    {if $target}<span class="flex-inline top">{include_ext file="common/icon.tpl" source="caret_down" class="subheader__icon"}</span>{/if}
</h4>
