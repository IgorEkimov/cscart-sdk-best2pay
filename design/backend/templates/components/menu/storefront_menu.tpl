{strip}
    {$storefront_status_icon = ""}
    {$storefront_url = ""}

    {if "ULTIMATE"|fn_allowed_for}
        {if $runtime.is_current_storefront_closed || $runtime.are_all_storefronts_closed}
            {$storefront_status_icon = "icon-lock"}
        {elseif $runtime.have_closed_storefronts}
            {$storefront_status_icon = "icon-unlock-alt"}
        {/if}
        {$storefront_url = "companies.manage"}
    {/if}

    {if "MULTIVENDOR"|fn_allowed_for && !$runtime.simple_ultimate}
        {if $runtime.are_all_storefronts_closed}
            {$storefront_status_icon = "icon-lock"}
        {elseif $runtime.have_closed_storefronts}
            {$storefront_status_icon = "icon-unlock-alt"}
        {/if}
        {$storefront_url = "storefronts.manage"}
    {/if}

    {if $storefront_status_icon && fn_check_view_permissions($storefront_url, "GET")}
        <a href="{$storefront_url|fn_url}" class="top-bar__btn mobile-hidden">
            <span class="top-bar__btn-inner storefront-menu__btn-inner">
                {include_ext file="common/icon.tpl" class="`$storefront_status_icon` dropdown-menu__icon"}
            </span>
        </a>
    {/if}
{/strip}