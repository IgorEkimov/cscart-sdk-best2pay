{capture name="get_items"}
    {* Create secondary main menu *}
    {if $smarty.const.BLOCK_MANAGER_MODE}
        {$items = []}
    {else}
        {$items = $navigation.static.secondary}
    {/if}
    {$secondary_items = $items scope=parent}
{/capture}