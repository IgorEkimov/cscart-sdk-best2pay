{$scroll_header = $config.scroll_header|default:false}
<div class="top-bar {if $scroll_header}top-bar--scroll-header{/if}" id="top_bar">
    <div class="top-bar__inner
        {if $runtime.is_current_storefront_closed || $runtime.are_all_storefronts_closed}top-bar__inner--disabled{/if}
        {if $scroll_header}top-bar__inner--scroll-header{/if}"
        data-ca-top-bar="inner"
    >
        <div class="top-bar__left" id="top_bar_left">
            {include file="components/menu/mobile_menu.tpl"}
            {include file="components/menu/logo_menu.tpl"}
            {include file="components/menu/storefront_menu.tpl"}
        <!--top_bar_left--></div>

        <div class="top-bar__search" id="top_bar_search">
            {include file="components/menu/search_menu.tpl"}
        <!--top_bar_search--></div>

        <div class="top-bar__right" id="top_bar_right">
            {hook name="menu:top_bar_right"}
                {if $auth.user_id}
                    {include file="components/menu/notification_menu.tpl"}
                    {include file="components/menu/user_menu.tpl"}
                {/if}
            {/hook}
        <!--top_bar_right--></div>
    </div>
<!--top_bar--></div>
