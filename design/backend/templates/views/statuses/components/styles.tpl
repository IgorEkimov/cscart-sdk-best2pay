{$statuses=$type|fn_get_statuses}
{*
    Example:
    .o-status-p,
    .o-status-c,
    .o-status-o,
    .o-status-f,
    .o-status-d,
    .o-status-b,
    .o-status-i,
    .o-status-y {
        
    }
*}
{if $statuses}
{capture name="styles"}
    {foreach from=$statuses key="status" item="status_data"}
        {if !$status_data || !$status_data.params || !$status_data.params.color}
            {continue}
        {/if}
        .{$type|lower}-status-{$status|lower} {
            .buttonBackground(lighten({$status_data.params.color}, 5%), darken({$status_data.params.color}, 5%));
        }
        .{$type|lower}-status-dropdown-{$status|lower}:before,
        .{$type|lower}-status-link-{$status|lower}:before, {
            color: {$status_data.params.color};
        }
    {/foreach}
{/capture}
{style content=$smarty.capture.styles type="less"}
{/if}
