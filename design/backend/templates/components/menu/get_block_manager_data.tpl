{if $smarty.const.BLOCK_MANAGER_MODE}
    {$block_manager_attrs = [
        "data-ca-block-manager" => "main",
        "data-ca-block-manager-has-layout" => 0,
        "data-ca-block-manager-dispatch" => "custom_menu",
        "data-ca-block-manager-blocks-place" => "true",
        "data-ca-block-manager-grid-id" => "header_main_menu"
    ]}
    {$attrs_wrapper = $block_manager_attrs}

    {$show_collapse_default = false}
    {$main_menu_primary_class = "cs-main-menu__primary--block-manager"}

    {foreach $items as $item_key => $item}
        {$items[$item_key].attrs.wrapper["data-ca-block-manager-blocks-place"] = "true"}
        {$items[$item_key].attrs.wrapper["data-ca-block-manager-grid-id"] = "main_menu_1_`$item@iteration`"}
        {$items[$item_key].is_show = true}

        {if $item.type !== "title_divider" && $item.type !== "divider"}
            {$items[$item_key].attrs.href["data-ca-menu"] = "mainMenuLink"}
            {$items[$item_key].attrs.href["data-ca-menu-id-path"] = $item.id_path}
            {if $auth.user_type === "UserTypes::VENDOR"|enum && $item.root_hidden}
                {$items[$item_key].attrs.class_href = "`$items[$item_key].attrs.class_href` main-menu-1__link--root-hidden"}
            {/if}
            {if $auth.user_type === "UserTypes::VENDOR"|enum && $item.hidden_by_permissions}
                {$items[$item_key].attrs.class_href = "`$items[$item_key].attrs.class_href` main-menu-1__link--hidden-by-permissions"}
            {/if}
            {if $item.is_custom}
                {$items[$item_key].is_accordion = true}
            {/if}
        {/if}

        {foreach $item.items as $item_key_2 => $item_2}
            {$items[$item_key].items[$item_key_2].is_show = true}

            {if $item_2.type !== "title_divider" && $item_2.type !== "divider"}
                {if $auth.user_type === "UserTypes::VENDOR"|enum && $item_2.root_hidden}
                    {$items[$item_key].items[$item_key_2].attrs.class_href = "`$items[$item_key].items[$item_key_2].attrs.class_href` main-menu-2__link--root-hidden"}
                {/if}
                {if $auth.user_type === "UserTypes::VENDOR"|enum && $item_2.hidden_by_permissions}
                    {$items[$item_key].items[$item_key_2].attrs.class_href = "`$items[$item_key].items[$item_key_2].attrs.class_href` main-menu-2__link--hidden-by-permissions"}
                {/if}
            {/if}
        {/foreach}
    {/foreach}

    {$items = $items scope=parent}
    {$attrs_wrapper = $attrs_wrapper scope=parent}
    {$show_collapse_default = $show_collapse_default scope=parent}
    {$main_menu_primary_class = $main_menu_primary_class scope=parent}
{/if}