{capture name="get_items"}
    {$additional_items = []}
    {* Create main menu *}
    {$navigation_home = [
        home => [
            title => __("home"),
            position => 10,
            icon => "icon-home",
            href => "index.index",
            id_path => "home",
            active => ($runtime.controller === "index" && $runtime.mode === "index")
        ]
    ]}

    {* Get additional menu items: $additional_items *}
    {include file="components/menu/get_additional_items.tpl"}

    {if $smarty.const.BLOCK_MANAGER_MODE}
        {$items = $navigation.static.central}
    {else}
        {$items = $navigation_home|array_merge:
            ($navigation.static.central|default:[])|array_merge:
            $additional_items
        }
    {/if}

    {* Get block manager data for $items and $attrs_wrapper *}
    {include file="components/menu/get_block_manager_data.tpl"}

    {$primary_items = $items scope=parent}
    {$attrs_wrapper = $attrs_wrapper scope=parent}
    {$show_collapse_default = $show_collapse_default scope=parent}
    {$main_menu_primary_class = $main_menu_primary_class scope=parent}
{/capture}