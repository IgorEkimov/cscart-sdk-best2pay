{include file="common/critical_css.tpl"}
{styles}
    {style src="ui/jqueryui.css"}
    {style src="lib/select2/select2.min.css"}
    {hook name="index:styles"}
        {style src="styles.less"}
        {if $smarty.const.ACCOUNT_TYPE === "vendor"}
            {style src="config_vendor.less"}
        {/if}
        {style src="tygh/supports.css"}
        {style src="glyphs.css"}

        {include file="views/statuses/components/styles.tpl" type=$smarty.const.STATUSES_ORDER}

        {if $language_direction == 'rtl'}
            {style src="rtl.less"}
        {/if}
    {/hook}

    {if $runtime.customization_mode.block_manager}
        {style src="../../themes/responsive/css/tygh/components/block_manager.less"}
    {/if}
{/styles}