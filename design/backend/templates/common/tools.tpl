{if $skip_check_permissions || $tools_list|fn_check_html_view_permissions}
{$icon_first_tools = $icon_first_tools|default:false}
{$icon_first = $icon_first|default:true}
<div class="btn-group {$tool_meta}">
    {if !$hide_tools && $tools_list}
    <a class="{if $override_meta}{$override_meta}{else}btn{/if} btn dropdown-toggle" data-toggle="dropdown">
        {if $icon && $icon_first_tools}
            <span class="btn__icon {if $link_text}btn__icon--with-text{/if}">
                {include_ext file="common/icon.tpl" class=$icon}
            </span>
        {/if}
        {if $link_text}
            {$link_text nofilter}
        {/if}
        {if $icon && !$icon_first_tools}
            <span class="btn__icon btn__icon--last {if $link_text}btn__icon--with-text{/if}">
                {include_ext file="common/icon.tpl" class=$icon}
            </span>
        {/if}
        {if $caret}<span class="caret"></span>{/if}
    </a>
    <ul id="tools_list_{$prefix}" class="dropdown-menu cm-smart-position">
        {$tools_list nofilter}
    </ul>
    {/if}
    {if !$hide_actions}
        {if $tool_href|fn_check_view_permissions}
            <a class="{if $tool_override_meta}{$tool_override_meta}{else}btn{/if} cm-tooltip" {if $tool_id} id="{$tool_id}"{/if}{if $tool_href} href="{$tool_href|fn_url}"{/if}{if $tool_onclick} onclick="{$tool_onclick}; return false;"{/if} {if $title}title="{$title}"{/if} {if $meta_data}{$meta_data nofilter}{/if}>
                {if $icon_first}
                    <span class="btn__icon {if $link_text}btn__icon--with-text{/if}">
                        {if $icon}
                            {include_ext file="common/icon.tpl" class=$icon}
                        {else}
                            {include_ext file="common/icon.tpl" class="icon-plus"}
                        {/if}
                    </span>
                {/if}
                {$link_text nofilter}
                {if !$icon_first}
                    <span class="btn__icon btn__icon--last {if $link_text}btn__icon--with-text{/if}">
                        {if $icon}
                            {include_ext file="common/icon.tpl" class=$icon}
                        {else}
                            {include_ext file="common/icon.tpl" class="icon-plus"}
                        {/if}
                    </span>
                {/if}
            </a>
        {/if}
    {/if}
</div>
{/if}
