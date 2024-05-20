<div class="cs-main-menu">
    <div class="cs-main-menu__outer" id="header_navbar">
        <div class="cs-main-menu__header-mobile mobile-visible">
            <button type="button" class="btn mobile-menu-closer mobile-menu-closer-btn">
                {include_ext file="common/icon.tpl"
                    class="icon icon-remove overlay-navbar-open"
                }
            </button>
        </div>
        <div class="cs-main-menu__inner">
            {if $auth.user_id}
                {* Get main menu content: $primary_items, $attrs_wrapper, $show_collapse_default *}
                {include file="components/menu/get_primary_items.tpl"
                    navigation=$navigation
                    quick_menu=$quick_menu
                }
                <div class="cs-main-menu__primary {$main_menu_primary_class}">
                    {include file="components/menu/main_menu.tpl"
                        items=$primary_items
                        prefix="primary"
                        show_collapse_default=$show_collapse_default
                    }
                </div>

                {* Get secondary menu content: $secondary_items, $attrs_wrapper, $show_collapse_default *}
                {include file="components/menu/get_secondary_items.tpl"
                    navigation=$navigation
                }
                {if $secondary_items}
                    <div class="cs-main-menu__secondary">
                        {include file="components/menu/main_menu.tpl"
                            items=$secondary_items
                            prefix="secondary"
                            show_collapse_default=$show_collapse_default
                        }
                    </div>
                {/if}
            {/if}
        </div>
    <!--header_navbar--></div>
</div>
<div class="cs-main-menu__backdrop mobile-menu-closer">
</div>

{* Content of quick menu *}
{include file="common/quick_menu.tpl"
    quick_menu=$quick_menu
}
