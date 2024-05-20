<!-- user menu -->
{$user_info_text = "`$user_info.firstname` `$user_info.lastname`
`$user_info.email`
{($menu_languages|sizeof > 1) ? $menu_languages[$smarty.const.CART_LANGUAGE].name : ""}
{($currencies|sizeof > 1) ? "`$currencies[$secondary_currency].description` (`$currencies[$secondary_currency].symbol`)" : ""}"}
<div class="top-bar__btn-wrapper btn-group dropdown-top-menu-item nav__user-menu">
    <button class="dropdown-toggle dropdown-top-menu-item-link top-bar__btn nav__user-menu-link"
        data-toggle="dropdown"
        type="button"
        title="{$user_info_text}"
    >
        <span class="top-bar__btn-inner nav__user-menu-inner">
            <span class="nav__profile-icon">
                {$user_info.firstname|upper|truncate:1:""}{$user_info.lastname|upper|truncate:1:""}
            </span>
        </span>
    </button>
    <ul class="dropdown-menu nav__user-menu-dropdown">
        <li class="disabled">
            <a><strong>{__("signed_in_as")}</strong><br>{$user_info.email}</a>
        </li>
        <li class="divider"></li>
        {hook name="menu:profile"}

        <!--language-->
        {if $menu_languages|sizeof > 1}
            {include file="common/select_object.tpl"
                style="dropdown"
                link_tpl=$config.current_url|fn_link_attach:"sl="
                items=$menu_languages
                selected_id=$smarty.const.CART_LANGUAGE
                display_icons=true
                key_name="name"
                key_selected="lang_code"
                class="languages cm-dropdown-skip-processing"
                is_submenu=true
                button_class="languages-select-list-button"
                dropdown_menu_class="languages-select-list"
                dropdown_menu_item_link_class="languages-select-list-item-link"
                pull_right=false
            }
        {/if}
        <!--end language-->

        <!--Curriencies-->
        {if $currencies|sizeof > 1}
            {include file="common/select_object.tpl"
                style="dropdown"
                link_tpl=$config.current_url|fn_link_attach:"currency="
                items=$currencies
                selected_id=$secondary_currency
                display_icons=false
                key_name="description"
                key_selected="currency_code"
                class="curriencies cm-dropdown-skip-processing"
                button_class="curriencies-button"
                dropdown_menu_item_link_class="curriencies-item-link"
                is_submenu=true
                dropdown_menu_class="currencies-select-list"
                pull_right=false
            }
        {/if}
        <!--end curriencies-->

        <li><a href="{"profiles.update?user_id=`$auth.user_id`"|fn_url}">{__("edit_profile")}</a></li>
        {if "MULTIVENDOR"|fn_allowed_for && !$runtime.simple_ultimate && $auth.user_type == "UserTypes::ADMIN"|enum && fn_check_view_permissions("companies.get_companies_list", "GET") && fn_check_view_permissions("profiles.login_as_vendor", "POST")}
            <li id="company_picker_dropdown_menu"
                class="js-company-switcher"
                data-ca-switcher-param-name="company_id"
                data-ca-switcher-data-name="company_id">
                {include file="views/companies/components/picker/picker.tpl"
                    input_name=$companies_picker_name
                    item_ids=[$runtime.company_data.company_id]
                    type="list"
                    show_advanced=false
                    selection_title_pre=__("log_in_as_vendor")
                    dropdown_parent_selector="#company_picker_dropdown_menu"
                }
            </li>
        {/if}
        {if "MULTIVENDOR"|fn_allowed_for
            && $auth.user_type === "UserTypes::VENDOR"|enum
            && fn_check_view_permissions("companies.update", "GET")
        }
            <li><a href="{"companies.update&company_id=`$runtime.company_id`"|fn_url}">{__("seller_info")}</a></li>
        {/if}
        {hook name="menu:profile_menu_extra_item"}
        {/hook}
        <li><a href="{"auth.logout"|fn_url}">{__("sign_out")}</a></li>
        {if !$runtime.company_id}
            <li class="divider"></li>
            {if fn_check_view_permissions("upgrade_center.manage", "POST")}
                <li class="disabled">
                    <a>{include file="common/product_release_info.tpl" is_time_shown=false}</a>
                </li>
            {/if}
            <li>
                {include file="common/popupbox.tpl" id="group`$id_prefix`feedback" edit_onclick=$onclick text=__("feedback_values") act="link" picker_meta="cm-clear-content" link_text=__("send_feedback", ["[product]" => $smarty.const.PRODUCT_NAME]) content=$smarty.capture.update_block href="feedback.prepare" no_icon_link=true but_name="dispatch[feedback.send]" opener_ajax_class="cm-ajax"}
            </li>
        {/if}
        {/hook}
    </ul>
</div>
<!--end user menu -->