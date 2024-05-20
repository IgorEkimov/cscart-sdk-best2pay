{if $navigation && $navigation.dynamic && $navigation.dynamic.sections}
    <div class="sidebar-row">
        <ul class="nav nav-list">
            {foreach from=$navigation.dynamic.sections item=m key="s_id" name="first_level"}
                {hook name="index:dynamic_menu_item"}
                    {if $m.type == "divider"}
                        <li class="divider"></li>
                    {elseif $m.href|fn_check_view_permissions:{$method|default:"GET"}}
                        <li class="{if $m.js == true}cm-js{/if}{if $smarty.foreach.first_level.last} last-item{/if}{if $navigation.dynamic.active_section == $s_id} active{/if}"><a href="{$m.href|fn_url}">{$m.title}</a></li>
                    {/if}
                {/hook}
            {/foreach}
        </ul>
    </div>
<hr>{/if}