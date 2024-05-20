{$redirect_url=$config.current_url|escape:url}
{$description_characters_threshold = 80}

{function menu_attrs attrs=[]}
    {foreach $attrs as $attr => $value}
        {$attr}="{$value}"
    {/foreach}
{/function}

{capture name="mainbox"}{strip}
    <div class="administration-page">
        {foreach $sections as $section_key => $section}
            {if $section.type === "title" && !$section.subitems && !$section.href}
                {continue}
            {/if}
            {$title = $section.title|default:__($section.id)|default:__($section_key)}
            {$a_tag = ($section.href && $section.subitems) ? "a" : "div"}
            {$a_wrapper_tag = ($section.href && !$section.subitems) ? "a" : "div"}
            {$section_identifier = (__("`$section.id`_menu_description") === "_`$section.id`_menu_description"|lower)
                ? $section_key
                : $section.id
            }

            {capture name="administration_item"}
                {* Icon *}
                <{$a_tag} {""}
                    {if $section.href && $section.subitems}href="{$section.href|fn_url}"{/if} {""}
                    class="administration-page__block-icon-wrapper {""}
                        {if $section.attrs.class === "is-addon" && !$section.icon}
                            administration-page__block-icon-wrapper--no-icon {""}
                        {/if}
                    " {""}
                >
                    {if $section.attrs.class === "is-addon" && !$section.icon}
                        {include file="views/addons/components/addons/addon_icon.tpl"
                            addon=[
                                name => $title
                            ]
                            show_description=false
                        }
                    {else}
                        {include_ext file="common/icon.tpl"
                            source=$section.icon|default:"gear"
                            class="`$section.icon` administration-page__block-icon"
                        }
                    {/if}
                </{$a_tag}>
                {* Description *}
                <div class="administration-page__block-description">
                    <{$a_tag} {""}
                        {if $section.href && $section.subitems}href="{$section.href|fn_url}"{/if} {""}
                        class="administration-page__block-description-main" {""}
                    >
                        <div class="administration-page__block-title">{$title}</div>
                        {if __("`$section_identifier`_menu_description") !== "_`$section_identifier`_menu_description"}
                            <div>
                                <span class="muted">
                                    {__("`$section_identifier`_menu_description")|truncate:$description_characters_threshold}
                                </span>
                            </div>
                        {/if}
                    </{$a_tag}>
                    {* Subitems *}
                    {if $section.subitems}
                        <div class="administration-page__block-subitems">
                            {foreach $section.subitems as $section_item_key => $section_item}
                                {if !$section_item.href}
                                    {continue}
                                {/if}
                                <a href="{$section_item.href|fn_url}" class="administration-page_subitems-item">
                                    {$section_item.title|default:__($section_item_key)}
                                </a>
                            {/foreach}
                        </div>
                    {/if}
                </div>
            {/capture}

            {* Wrapper *}
            <{$a_wrapper_tag} {""}
                {if $section.href && !$section.subitems}href="{$section.href|fn_url}"{/if} {""}
                class="administration-page__block {if $section.attrs.class_href}{$section.attrs.class_href}{/if}" {""}
                {menu_attrs attrs=$section.attrs.href} {""}
            >
                {$smarty.capture.administration_item nofilter}
            </{$a_wrapper_tag}>
        {/foreach}
    </div>
{/strip}{/capture}

{include file="common/mainbox.tpl"
    title=__("settings")
    buttons=$smarty.capture.buttons
    content=$smarty.capture.mainbox
    select_storefront=$select_storefront
    show_all_storefront=true
    storefront_switcher_param_name="storefront_id"
}
