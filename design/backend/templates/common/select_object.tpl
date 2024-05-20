{if $items|sizeof > 1}{strip}

{/strip}{$is_submenu = $is_submenu|default:false}
{$button_style = ($button_style === false) ? "" : $button_style|default:"btn-link link--monochrome"}
{$show_button_text = $show_button_text|default:true}
{$show_button_symbol = $show_button_symbol|default:true}
{$show_caret = $show_caret|default:true}
{$pull_right = $pull_right|default:true}
{if $style == "graphic"}{strip}
{/strip}<div class="btn-group {$class}" {if $select_container_id}id="{$select_container_id}"{/if}>
    <a class="btn dropdown-toggle {$button_style} {$button_class}" id="sw_select_{$selected_id}_wrap_{$suffix}" data-toggle="dropdown">{strip}
        {if $display_icons}
            {$icon_class=$items.$selected_id.icon_class|default:"flag flag-{$items.$selected_id.country_code|lower}"}
            {if $icon_class}
                {if $icon_deprecated !== false && $icon_class|strpos:"flag" !== false}
                    {$icon_deprecated = true}
                {/if}
                {if $icon_deprecated}
                    {include_ext file="common/icon_deprecated.tpl"
                        class=$icon_class
                        data=[
                            "data-ca-target-id" => "sw_select_`$selected_id`_wrap_`$suffix`"
                        ]
                    }
                {else}
                    {include_ext file="common/icon.tpl"
                        class=$icon_class
                        data=[
                            "data-ca-target-id" => "sw_select_`$selected_id`_wrap_`$suffix`"
                        ]
                    }
                {/if}
            {/if}
        {/if}
        {if $show_button_text}{$items.$selected_id.$key_name}{/if}{if $show_button_symbol && $items.$selected_id.symbol}&nbsp;({$items.$selected_id.symbol nofilter}){/if}{if $show_caret}
        &nbsp;<span class="caret"></span>{/if}
    {/strip}</a>
        {if $key_name == "company"}
            <input id="filter" class="input-text cm-filter" type="text" style="width: 85%"/>
        {/if}
        <ul class="dropdown-menu cm-select-list {if $display_icons}popup-icons{/if} {$dropdown_menu_class}">
            {if $extra_pre}{$extra_pre nofilter}{/if}
            {foreach $items as $id => $item}
                <li class="{if $id == $selected_id}active{/if} {$dropdown_menu_item_class}">
                    <a name="{$id}"
                       href="{"`$link_tpl``$id`"|fn_url}"
                       class="{$dropdown_menu_item_link_class} {if $target_id}cm-ajax{/if}"
                       {if $target_id}
                           data-ca-target-id="{$target_id}"
                       {/if}
                       {if $item.symbol}
                           data-ca-list-item-symbol="{$item.symbol}"
                       {/if}
                    >
                        {if $display_icons}
                            {$icon_class=$item.icon_class|default:"flag flag-{$item.country_code|lower}"}
                            {if $icon_class}
                                {if $icon_deprecated !== false && $icon_class|strpos:"flag" !== false}
                                    {$icon_deprecated = true}
                                {/if}
                                {if $icon_deprecated}
                                    {include_ext file="common/icon_deprecated.tpl" class=$icon_class}
                                {else}
                                    {include_ext file="common/icon.tpl" class=$icon_class}
                                {/if}
                            {/if}
                        {/if}
                        {$item.$key_name}{if $item.symbol}&nbsp;({$item.symbol nofilter}){/if}
                    </a>
                </li>
            {/foreach}
            {if $extra}{$extra nofilter}{/if}
        </ul>
</div>{strip}
{/strip}{elseif $style == "dropdown"}
    <li class="{if $is_submenu}dropdown-submenu{else}dropdown dropdown-top-menu-item{/if} {$class}" {if $select_container_id}id="{$select_container_id}"{/if}>
        <a class="{if $is_submenu}dropdown-submenu__link{else}dropdown-toggle{/if} cm-combination {$button_class}"
           data-toggle="dropdown"
           id="sw_select_{$selected_id}_wrap_{$suffix}"
           {if $disable_dropdown_processing}data-disable-dropdown-processing="true"{/if}
        >
            {if $plain_name}
                {$plain_name nofilter}
            {else}
                {if $key_selected}
                    {if $is_submenu}
                        {if $items.$selected_id.name}
                            {$items.$selected_id.name}
                        {elseif $items.$selected_id.description}
                            {$items.$selected_id.description}
                        {/if}
                        {if $items.$selected_id.symbol}&nbsp;({$items.$selected_id.symbol nofilter}){/if}
                    {else}
                        {if $items.$selected_id.symbol}
                            {$items.$selected_id.symbol nofilter}
                        {else}
                            {$items.$selected_id.$key_selected|upper nofilter}
                        {/if}
                    {/if}
                {else}
                    {$items.$selected_id.$key_name nofilter}
                {/if}
            {/if}

            {if !$is_submenu}
                <b class="caret"></b>
            {/if}
        </a>
        <ul class="dropdown-menu cm-select-list {if $pull_right}pull-right{/if} {$dropdown_menu_class}">
            {foreach $items as $id => $item}

                {* Link and suffix with the same identifier. Example: UI and content languages *}
                {$link = "`$link_tpl``$id`"|fn_url}
                {if $link_suffix}
                    {$link = $link|fn_link_attach:"`$link_suffix``$id`"}
                {/if}

                <li class="{if $id == $selected_id}active{/if} {$dropdown_menu_item_class}">
                    <a name="{$id}" href="{$link}" class="{$dropdown_menu_item_link_class}">
                        {if $display_icons}
                            {$icon_class=$item.icon_class|default:"flag flag-{$item.country_code|lower}"}
                            {if $icon_class}
                                {if $icon_deprecated !== false && $icon_class|strpos:"flag" !== false}
                                    {$icon_deprecated = true}
                                {/if}
                                {if $icon_deprecated}
                                    {include_ext file="common/icon_deprecated.tpl" class=$icon_class}
                                {else}
                                    {include_ext file="common/icon.tpl" class=$icon_class}
                                {/if}
                            {/if}
                        {/if}
                        {$item.$key_name}{if $item.symbol}&nbsp;({$item.symbol nofilter}){/if}
                    </a>
                </li>
            {/foreach}
        </ul>
    </li>
{elseif $style == "field"}
<div class="cm-popup-box btn-group {if $class}{$class}{/if}">
    {if !$selected_key}
        {$selected_key = $items|key}
    {/if}
    {if !$selected_name}
        {$selected_name = $items[$selected_key]}
    {/if}
    <input type="hidden"
           name="{$select_container_name}"
           {if $select_container_id}
               id="{$select_container_id}"
           {/if}
           value="{$selected_key}"
    />
    <a id="sw_{$select_container_name}" class="dropdown-toggle btn {if $text_wrap}dropdown-toggle--text-wrap{/if} {$button_style} {$button_class}" data-toggle="dropdown">
    {$selected_name}
        <span class="caret"></span>
    </a>
    <ul class="dropdown-menu cm-select {$dropdown_menu_class}">
        {foreach $items as $key => $value}
            <li class="{if $selected_key == $key}disabled{/if} {$dropdown_menu_item_class}">
                <a class="{if $selected_key == $key}active{/if} cm-select-option {if $text_wrap}dropdown--text-wrap{/if} {$dropdown_menu_item_link_class}"
                   data-ca-list-item="{$key}" title="{$value}"
                >{$value nofilter}</a></li>
        {/foreach}
    </ul>
</div>
{/if}{strip}

{/strip}{/if}