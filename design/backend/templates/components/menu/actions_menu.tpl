{if $items}{strip}
    {$actions_count_threshold = $config.actions_menu.count_threshold|default:3}
    {$icon_prefix = "icon-"}
    {$icon_prefix_length = $icon_prefix|strlen}
    {$button_characters_threshold = 30}
    {$button_characters_mobile_threshold = 20}

    {foreach $items as $item_key => $item}
        {if $item@iteration > $actions_count_threshold}
            {continue}
        {/if}
        {$item_text = $item.text|default:__($item_key)}
        {$item_title = $item.title|default:""}
        {capture name="item_text" assign="item_text_html"}
            {if $item_text|count_characters:true > $button_characters_mobile_threshold}
                <span class="mobile-hidden">{$item_text}</span>
                <span class="mobile-visible">{($item.text_mobile|truncate
                    :$button_characters_mobile_threshold:'...':true:true)|default
                    :($item_text|truncate:$button_characters_mobile_threshold:'...':true:true)
                }</span>
            {else}
                <span>{$item_text}</span>
            {/if}
        {/capture}
        {if $item_text|count_characters:true > $button_characters_threshold}
            {$item_title = $item_text}
            {$item_text = "`$item_text|substr:0:$button_characters_threshold`..."}
        {/if}
        {$item_icon = ""}
        {if $item.icon}
            {$item_icon = $item.icon|trim}
            {* Source without "icon-" prefix *}
            {if $item_icon|substr:0:$icon_prefix_length !== $icon_prefix}
                {$item_icon = "`$icon_prefix``$item_icon`"}
            {/if}
        {/if}

        {if $item.wrapper_class}<span class="shift-left shift-right {$item.wrapper_class}">{/if}
            {btn type=$item.type|default:"text"
                href=$item.href|default:""
                text=$item_text_html
                title=$item_title
                id=$item.id|default:""
                class="btn actions-menu__btn `$item.class` `$item.meta`"
                dispatch=$item.dispatch|default:""
                form=$item.form|default:""
                method=$item.method|default:""
                target=$item.target|default:""
                target_id=$item.target_id|default:""
                process=$item.process|default:""
                onclick=$item.onclick|default:""
                raw=true
                icon=$item_icon
                icon_first=true
                data=$item.data|default:[]
            }
        {if $item.wrapper_class}</span>{/if}
    {/foreach}

    {if $items|@count > $actions_count_threshold}
        {capture name="tools_list"}
            {foreach $items as $item_key => $item name="actions"}
                {if $item@iteration <= $actions_count_threshold}
                    {continue}
                {/if}
                {$item_text = $item.text|default:__($item_key)}
                {capture name="item_text" assign="item_text_html"}
                    <span>{$item_text}</span>
                {/capture}
                {$item_icon = ""}
                {if $item.icon}
                    {$item_icon = $item.icon|trim}
                    {* Source without "icon-" prefix *}
                    {if $item_icon|substr:0:$icon_prefix_length !== $icon_prefix}
                        {$item_icon = "`$icon_prefix``$item_icon`"}
                    {/if}
                {/if}

                <li {if $item.wrapper_class}class="{$item.wrapper_class}"{/if}>
                    {btn type=$item.type|default:"text"
                        href=$item.href|default:""
                        text=$item_text_html
                        title=$item.title|default:""
                        id=$item.id|default:""
                        class="actions-menu__dropdown-item `$item.class` `$item.meta`"
                        dispatch=$item.dispatch|default:""
                        form=$item.form|default:""
                        method=$item.method|default:""
                        target=$item.target|default:""
                        target_id=$item.target_id|default:""
                        process=$item.process|default:""
                        onclick=$item.onclick|default:""
                        raw=true
                        icon=$item_icon
                        icon_first=true
                        data=$item.data|default:[]
                    }
                </li>
            {/foreach}
        {/capture}
        {include file="common/tools.tpl"
            hide_actions=true
            tools_list=$smarty.capture.tools_list
            link_text=__("actions.more")
            caret=true
            prefix="actions_menu"
            tool_meta="actions-menu__btn-group"
            override_meta="btn actions-menu__dropdown-toggle"
        }
    {/if}
{/strip}{/if}