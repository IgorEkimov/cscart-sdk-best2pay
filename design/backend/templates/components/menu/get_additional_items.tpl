{if $smarty.const.ACCOUNT_TYPE !== "vendor"}
    {* Quick menu *}
    {$enable_quick_menu = $config.enable_quick_menu|default:false}

    {if $enable_quick_menu}
        {$quick_menu_items = [
            quick_menu => [
                type => "divider",
                position => 900,
                id_path => "quick_menu_divider"
            ],
            quick_menu_title => [
                title => __("quick_menu"),
                type => "title_divider",
                position => 901,
                icon => "star_empty",
                id_path => "quick_menu_title"
            ]
        ]}

        {foreach $quick_menu as $sect}
            {$quick_menu_items[$sect.section.name|lower] = [
                type => "title_divider",
                position => $sect.section.id,
                id_path => "quick_menu_divider",
                title => $sect.section.name,
                icon => 'angle_right'
            ]}
            {foreach $sect.subsection as $subsect}
                {$quick_menu_items["`$sect.section.name|lower`_`$subsect.menu_id`"] = [
                    title => $subsect.name,
                    position => $subsect.position,
                    href => $subsect.url,
                    id_path => "quick_menu_`$subsect.menu_id`",
                    attrs => [
                        class_href => "main-menu-1__link--regular main-menu-1__link--quick-link"
                    ],
                    icon => 'minus'
                ]}
            {/foreach}
        {/foreach}
        {$quick_menu_items[] = [
            position => 999,
            id_path => "opener_quick_menu_edit",
            title => __("edit"),
            attrs => [
                class_href => "main-menu-1__link--regular cm-dialog-opener",
                href => [
                    "data-ca-target-id" => "content_quick_menu_edit",
                    "data-ca-dialog-title" => __("quick_menu")
                ]
            ],
            icon => 'edit'
        ]}

        {$additional_items = $quick_menu_items scope=parent}
    {/if}
{/if}