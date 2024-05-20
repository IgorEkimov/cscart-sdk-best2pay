{$show_supplier_link = $show_supplier_link|default:true}
{if $a.supplier}
    <div class="addons-addon-supplier">
        {if $show_supplier_link}
            <a href="{"addons.manage&supplier={$a.supplier}"|fn_url}" class="link--monochrome addons-addon-supplier__name addons-addon-supplier__name--link row-status">
                {$a.supplier}
            </a>
        {else}
            <span class="addons-addon-supplier__name row-status">
                {$a.supplier}
            </span>
        {/if}
        {if $a.identified || $a.is_core_addon}
            <span class="flex-inline">
                {include_ext file="common/icon.tpl"
                    class="icon-ok addons-addon-supplier__identified addons-addon-supplier__identified--`$a.status|lower`"
                    title=__("verified_developer")
                }
            </span>
        {/if}
        {if $a.personal_review}
            <span class="flex-inline">
                {include_ext file="common/icon.tpl"
                    class="icon-comment addons-addon-supplier__has-admin-review"
                    title=__("addon_has_admin_review")
                }
            </span>
        {/if}
    </div>
{/if}
