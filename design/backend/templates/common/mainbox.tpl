{$show_back_button = true}
{foreach $navigation.static.central as $back_button_central}
    {foreach $back_button_central.items as $back_button_central_item}
        {if !$back_button_central_item || !$back_button_central_item.href}
            {continue}
        {/if}
        {$back_button_dispatch = "."|explode:$back_button_central_item.href}
        {$back_button_mode_pre = "?"|explode:$back_button_dispatch[1]}
        {$back_button_mode = $back_button_mode_pre[0]}        

        {if ($runtime.controller === $back_button_dispatch[0] && $runtime.mode === $back_button_mode)
            || ($runtime.controller === "index" && $runtime.mode === "index")
        }
            {$show_back_button = false}
        {/if}
    {/foreach}
{/foreach}
{foreach $navigation.static.top.administration.items as $back_button_top_item}
    {if !$back_button_top_item || !$back_button_top_item.href}
        {continue}
    {/if}
    {$back_button_dispatch = "."|explode:$back_button_top_item.href}
    {$back_button_mode_pre = "?"|explode:$back_button_dispatch[1]}
    {$back_button_mode = $back_button_mode_pre[0]}        

    {if ($runtime.controller === $back_button_dispatch[0] && $runtime.mode === $back_button_mode)}
        {$show_back_button = true}
    {/if}
{/foreach}

{if !$sidebar_position}
    {$sidebar_position = "right"}
{/if}

{if !$sidebar_icon}
    {$sidebar_icon = "icon-chevron-left"}
{/if}

{if (fn_allowed_for('MULTIVENDOR:ULTIMATE'))}
    {if !$runtime.is_multiple_storefronts}
        {$select_storefront = false}
    {/if}
    {$selected_storefront_id = $selected_storefront_id|default:$app["storefront"]->storefront_id}
{elseif (!isset($select_storefront))}
    {$select_storefront = $runtime.is_multiple_storefronts}
{/if}

{if $anchor}
<a name="{$anchor}"></a>
{/if}

{if "THEMES_PANEL"|defined}
    {$sticky_padding_on_actions_panel = 80}
    {$sticky_top_on_actions_panel = 80}
{else}
    {$sticky_padding_on_actions_panel = 45}
    {$sticky_top_on_actions_panel = 45}
{/if}

{* Maximum length of product name / 2 *}
{$title_long_length = 127}
{if isset($title_start) && isset($title_end)}
    {$is_title_long_length = "`$title_start` `$title_end`"|default:"&nbsp;"|strip_tags|strip|sanitize_html|count_characters:true > $title_long_length}
{else}
    {$is_title_long_length = $title|default:"&nbsp;"|strip_tags|strip|sanitize_html|count_characters:true > $title_long_length}
{/if}

{$scroll_header = $config.scroll_header|default:false}

<script>
// Init ajax callback (rebuild)
var menu_content = {$convertible_data|default:"''"|unescape nofilter};
</script>

{capture name="sidebar_content" assign="sidebar_content"}
    {if $navigation && $navigation.dynamic && $navigation.dynamic.sections}
        {include file="common/dynamic_navigation.tpl"
            navigation=$navigation
        }
    {/if}
    {$sidebar nofilter}

    {notes assign="notes"}{/notes}
    {if $notes}
        {foreach from=$notes item="note" key="sidebox_title"}
            {capture name="note_title"}
                {if $title == "_note_"}{__("notes")}{else}{$title}{/if}
            {/capture}
            {include file="common/sidebox.tpl" content=$note title=$smarty.capture.note_title}
        {/foreach}
    {/if}
{/capture}

<!-- Actions -->
{hook name="index:actions_wrapper"}
    <div class="actions nav__actions {if $scroll_header}nav__actions--scroll-header{/if}"
        data-ca-stick-on-screens="sm-large,md,md-large,lg,uhd"
        data-ca-top="{$sticky_top_on_actions_panel}"
        data-ca-padding="{$sticky_padding_on_actions_panel}"
        id="actions_panel">
        <div class="actions__wrapper {if !$show_back_button}actions__wrapper--no-back{/if} {if $runtime.is_current_storefront_closed || $runtime.are_all_storefronts_closed}actions__wrapper--disabled{/if}">
        {hook name="index:actions"}
            {if $show_back_button}
                <div class="btn-bar-left nav__actions-back">
                    {include file="common/last_viewed_items.tpl"}
                </div>
            {/if}
            <div class="title nav__actions-title {if $select_storefront}title--storefronts{/if}">
                {if isset($title_start) && isset($title_end)}
                    <h2 class="title__heading {if $is_title_long_length}title__heading--long{/if}
                        {if $select_storefront}title__heading--storefronts{/if}">
                        <span class="title__part-start mobile-hidden">{$title_start|default:"&nbsp;"|sanitize_html nofilter}{strip}
                        {/strip}{if $title_end|strip_tags|strip|sanitize_html !== ""}:{/if}
                         </span>
                        <span class="title__part-end">{$title_end|default:"&nbsp;"|sanitize_html nofilter}</span>
                    </h2>
                {else}
                    <h2 class="title__heading {if $is_title_long_length}title__heading--long{/if}
                        {if $select_storefront}title__heading--storefronts{/if}"
                    >
                        {$title|default:"&nbsp;"|sanitize_html nofilter}
                    </h2>
                {/if}
            </div>

            <div class="{$main_buttons_meta} btn-bar btn-toolbar nav__actions-bar" {if $content_id}id="tools_{$content_id}_buttons"{/if}>
                {hook name="index:toolbar"}
                {/hook}
                {if $select_storefront}
                    {include file="views/storefronts/components/picker/presets.tpl"
                        input_name=$storefronts_picker_name
                        item_ids=[$runtime.company_data.company_id]
                        show_empty_variant=$show_empty_variant
                        empty_variant_text=__("all_vendors")
                        select_storefront=$select_storefront
                        show_all_storefront=$show_all_storefront
                    }
                {/if}

                {include_ext file="components/menu/actions_menu.tpl"
                    items=$navigation.dynamic.actions
                }

                {$buttons nofilter}

                {if $adv_buttons}
                <div class="nav__actions-adv-buttons adv-buttons" {if $content_id}id="tools_{$content_id}_adv_buttons"{/if}>
                {$adv_buttons nofilter}
                {if $content_id}<!--tools_{$content_id}_adv_buttons-->{/if}</div>
                {/if}

            {if $content_id}<!--tools_{$content_id}_buttons-->{/if}</div>
            {/hook}
        </div>
    <!--actions_panel--></div>
{/hook}

<div class="admin-content-wrapper {$mainbox_content_wrapper_class|default:""}">

<!-- Sidebar left -->
{if !$no_sidebar && $sidebar_content|trim != "" && $sidebar_position == "left"}
<div class="sidebar sidebar-left cm-sidebar {$sidebar_meta}" id="elm_sidebar">
    <div class="sidebar-toggle">
        <span class="sidebar-text">{__("sidebar")}</span>
        {include_ext file="common/icon.tpl"
            class="`$sidebar_icon` sidebar-icon"
        }
    </div>
    <div class="sidebar-wrapper">
    {$sidebar_content nofilter}
    </div>
<!--elm_sidebar--></div>
{/if}

{* DO NOT REMOVE HTML comment below *}
<!--Content-->
<div class="content page-content {if $no_sidebar} content-no-sidebar{/if}{if $sidebar_content && $sidebar_content|trim == ""} no-sidebar{/if} {if "ULTIMATE"|fn_allowed_for}ufa{/if}" {if $box_id}id="{$box_id}"{/if}>
    <div class="content-wrap">
    {hook name="index:content_top"}

        {if ($saved_search)
            || ($tabs_navigation && $tabs_navigation|trim)
            || ($context_search)
            || ($select_languages && $languages|sizeof > 1)
            || ($search_filters)
        }
            {*
                content__top-navigation
                ├── content__top-navigation-main
                │   ├── content__top-navigation-primary
                │   │   ├── content__saved-search
                │   │   └── content__tabs-navigation
                │   │
                │   └── content__top-navigation-secondary
                │       ├── content__context-search
                │       └── content-variant-wrap (language)
                │
                └── content__search-filters
            *}

            {* Get $context_search, and $search_filters_content *}
            {if $search_filters}
                {include file="components/search_filters/search_filters.tpl"
                    search_filters=$search_filters
                    dispatch=$dispatch
                    assign="search_filters_content"
                }
            {/if}

            <div class="content__top-navigation" id="content_top_navigation">
                {if $saved_search
                    || $tabs_navigation && $tabs_navigation|trim
                    || $context_search
                    || $select_languages && $languages|sizeof > 1
                }
                    <div class="content__top-navigation-main {if $context_search}content__top-navigation-main--wrap{/if}" id="content_top_navigation_main">
                        {if $saved_search
                            || $tabs_navigation && $tabs_navigation|trim
                        }
                            <div class="content__top-navigation-primary">
                                {if $saved_search}
                                    <div class="content__saved-search" id="saved_search_horizontal_views">
                                        {include file="common/saved_search_horizontal.tpl"
                                            dispatch=$saved_search.dispatch
                                            view_type=$saved_search.view_type
                                            is_compact_view=(!!$context_search)
                                        }
                                    </div>
                                {/if}

                                {if $tabs_navigation && $tabs_navigation|trim}
                                    <div class="content__tabs-navigation">
                                        {$tabs_navigation nofilter}
                                    </div>
                                {/if}
                            </div>
                        {/if}

                        {if $context_search
                            || $select_languages && $languages|sizeof > 1
                        }
                            <div class="content__top-navigation-secondary {if $context_search}content__top-navigation-secondary--fill{/if}">
                                {if $context_search}
                                    <div class="content__context-search">
                                        {include file="components/search_filters/context_search.tpl"
                                            context_search=$context_search
                                        }
                                    </div>
                                {/if}

                                {if $select_languages && $languages|sizeof > 1}
                                    {capture name="languages_select_extra"}
                                        <li class="disabled">
                                            <span>{__("language_content")}</span>
                                        </li>
                                    {/capture}
                                    <div class="content-variant-wrap content-variant-wrap--language language-wrap">
                                        {include file="common/select_object.tpl"
                                            style="graphic"
                                            link_tpl=$config.current_url|fn_link_attach:"descr_sl="
                                            items=$languages
                                            selected_id=$smarty.const.DESCR_SL
                                            key_name="name"
                                            suffix="content"
                                            extra_pre=$smarty.capture.languages_select_extra
                                            display_icons=true
                                            show_button_text=false
                                            show_caret=false
                                        }
                                    </div>
                                {/if}
                            </div>
                        {/if}
                    <!--content_top_navigation_main--></div>
                {/if}

                {if $search_filters_content && search_filters_content|trim}
                    <div class="content__search-filters" id="content_search_filters">
                        {$search_filters_content nofilter}
                    <!--content_search_filters--></div>
                {/if}
            <!--content_top_navigation--></div>
        {/if}

        {if $tools}{$tools nofilter}{/if}

        {if $title_extra}<div class="title">-&nbsp;</div>
            {$title_extra nofilter}
        {/if}

        {if $extra_tools && $extra_tools|trim}
            <div class="extra-tools">
                {$extra_tools nofilter}
            </div>
        {/if}
    {/hook}

    {if $content_id}<div id="content_{$content_id}">{/if}
        {$content|default:"&nbsp;" nofilter}
    {if $content_id}<!--content_{$content_id}--></div>{/if}

    {if $box_id}<!--{$box_id}-->{/if}</div>
</div>
{* DO NOT REMOVE HTML comment below *}
<!--/Content-->


<!-- Sidebar -->
{if !$no_sidebar && $sidebar_content|trim != "" && $sidebar_position == "right"}
{hook name="index:right_sidebar"}
{$is_open_state_sidebar_save = $is_open_state_sidebar_save|default:false}

<div class="sidebar cm-sidebar{if $is_open_state_sidebar_save} cm-sidebar-open-state-save{/if} {$sidebar_meta}" id="elm_sidebar">
    <div class="sidebar-toggle">
        <span class="sidebar-text">{__("sidebar")}</span>
        {include_ext file="common/icon.tpl"
            class="`$sidebar_icon` sidebar-icon"
        }
    </div>
    <div class="sidebar-wrapper">
    {$sidebar_content nofilter}
    </div>
<!--elm_sidebar--></div>
{/hook}
{/if}

</div>

<script>
    var ajax_callback_data = menu_content;
</script>
{script src="js/tygh/sidebar.js"}
