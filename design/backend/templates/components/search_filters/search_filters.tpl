{*
    $search_filters
    $not_saved
*}

{if $search_filters}
    {script src="js/tygh/search_filters.js"}

    {$form_id = $search_filters.form_id|default:"search_filters_form"}
    {$add_filter_variant_item_state = []}
    {if $search_filters.page_part}
        {$form_action_suffix="#`$page_part`"}
    {/if}

    <div class="search-filters-wrapper__form-wrapper">
        <form action="{""|fn_url}{$form_action_suffix}"
            name="{$search_filters.search_form_prefix}search_filters_form"
            method="get"
            class="cm-disable-empty-all search-filters-wrapper__form" 
            data-ca-target-id="pagination_contents,content_top_navigation"
            data-ca-search-filters="form"
        id="{$form_id}">
            {* Hidden inputs *}
            {include file="components/search_filters/hidden_inputs.tpl"
                search_filters=$search_filters
                search=$search
                put_request_vars=$put_request_vars
            }

            {* Submit button *}
            {if $search_filters.dispatch}
                {include file="buttons/search.tpl"
                    but_name="dispatch[`$search_filters.dispatch`]"
                    method="GET"
                    but_meta="hidden"
                    but_id="`$form_id`_search_filters_submit"
                }
            {/if}

            {* Get $context_search, and $search_filters_without_context_search *}
            {include file="components/search_filters/context_search_prepare.tpl"
                search_filters=$search_filters
            }

            {* Save this search as popup *}
            {include file="components/search_filters/search_save.tpl"
                not_saved=$not_saved
                form_id=$form_id
                views=$views
                search=$search
                search_filters=$search_filters
            }

            {* Search filter fields *}
            <div class="search-filters" data-ca-search-filters="main">
                {foreach $search_filters_without_context_search.data as $search_filter}
                    {if isset($search_filter.is_enabled) && !$search_filter.is_enabled
                        || !$search_filter.id
                    }
                        {continue}
                    {/if}
                    {$search_filter_item_class = ""}
                    {$is_show = false}
                    {$is_has_value = false}
                    {$is_show_clear_filter_btn = false}
                    {$field_id = "`$form_id`_`$search_filter.id`"}
                    {$add_filter_variant_item_state[$field_id] = [
                        show => false
                    ]}
                    {capture name="search_filters_item"}
                    {* Hidden *}
                    {if $search_filter.is_hidden}
                        {$search_filter_item_attributes = [
                            "data-ca-search-filters-is-hidden" => "true"
                        ]}
                        {if $search_filter.type === "hidden"}
                            <input type="hidden"
                                name="{$search_filter.id}"
                                id="{$field_id}"
                                value="{$search_filter.value}"
                                data-ca-search-filters="field"
                            />
                        {/if}
                        {$search_filter.content nofilter}
                    {* Popup for big content *}
                    {elseif $search_filter.type === "popup"}
                        {if $search_filter.content && $search_filter.content|trim !== ""}
                            {$search_filter_item_class = "search-filters__popup"}
                            {$is_has_value = true}
                            {capture name="popup_content"}
                                {$search_filter.content nofilter}
                                <div class="modal-footer buttons-container">
                                    <div class="pull-right">
                                        <a class="cm-dialog-closer btn btn-primary"
                                            data-dismiss="modal"
                                            data-ca-search-filters="closePopup"
                                        >{__("close")}</a>
                                    </div>
                                </div>
                            {/capture}
                            {include file="common/popupbox.tpl"
                                id=$field_id
                                text=$search_filter.label
                                link_text=$search_filter.label
                                act="button"
                                but_meta="search-filters__button-popup cm-search-filters-button"
                                content=$smarty.capture.popup_content
                                assign="popupbox_content"
                            }
                            {if !$popupbox_content || $popupbox_content && $popupbox_content|trim === ""}
                                {*
                                    FIXME: Smarty bug: {capture} breaks {continue} in the {foreach}.
                                    When {continue} you must explicitly close the {сapture}.
                                    For {capture name="search_filters_item"}
                                *}
                                {/capture}
                                {continue}
                            {/if}

                            {$popupbox_content nofilter}
                        {/if}
                    {else}
                        {btn type="button"
                            raw=true
                            text="`$search_filter.label` <span class=\"caret\"></span>"
                            class="btn dropdown-toggle search-filters__dropdown-toggle cm-search-filters-button"
                            data=[
                                "data-toggle" => "dropdown"
                            ]
                        }
                        <ul class="dropdown-menu search-filters__dropdown-menu
                                {if $search_filter.size === "large"}search-filters__dropdown-menu--large{/if}"
                            data-ca-search-filters="dropdownMenu">
                            {* Checkbox list: select multiple items *}
                            {if $search_filter.type === "checkbox"}
                                {$search_filter_item_class = "btn-group search-filters__dropdown"}
                                {if $search_filter.nested_data}
                                    {$is_show_clear_filter_btn = true}
                                    {foreach $search_filter.nested_data as $item}
                                        {$item_id = "`$field_id`_`$item.key`"}
                                        {if $_REQUEST[$item.key]}
                                            {$is_show = true}
                                            {$add_filter_variant_item_state[$field_id] = [
                                                show => true
                                            ]}
                                        {/if}
                                        {if $item.is_checked}
                                            {$is_has_value = true}
                                        {/if}
                                        <li>
                                            <div class="search-filters__variant-item">
                                                <label for="{$item_id}" class="checkbox">
                                                    <input type="checkbox"
                                                        value="{($item === false)
                                                            ? ""
                                                            : $item.value|default:("YesNo::YES"|enum)
                                                        }"
                                                        {if $item.is_checked}checked="checked"{/if}
                                                        name="{$item.key}"
                                                        id="{$item_id}"
                                                        data-ca-search-filters="field"
                                                    />
                                                    {$item.label}
                                                </label>
                                            </div>
                                        </li>
                                    {/foreach}
                                {/if}
                            {* Radio list: select one item *}
                            {elseif $search_filter.type === "radio"}
                                {$search_filter_item_class = "btn-group search-filters__dropdown"}
                                {if $search_filter.nested_data}
                                    {$is_show_clear_filter_btn = true}
                                    {foreach $search_filter.nested_data as $item}
                                        {$item_id = "`$field_id`_`$item.key`"}
                                        {if $_REQUEST[$search_filter.id]}
                                            {$is_show = true}
                                            {$add_filter_variant_item_state[$field_id] = [
                                                show => true
                                            ]}
                                        {/if}
                                        {if $item.is_checked && $item@index !== 0}
                                            {$is_has_value = true}
                                        {/if}
                                        <li>
                                            <div class="search-filters__variant-item">
                                                <label for="{$item_id}" class="radio">
                                                    <input type="radio"
                                                        value="{($item.value === false)
                                                            ? "" :
                                                            $item.value|default:("YesNo::YES"|enum)
                                                        }"
                                                        {if $item.is_checked}checked="checked"{/if}
                                                        name="{$search_filter.id}"
                                                        id="{$item_id}"
                                                        data-ca-search-filters="field"
                                                    />
                                                    {$item.label}
                                                </label>
                                            </div>
                                        </li>
                                    {/foreach}
                                {/if}
                            {* Range: select range of two fields *}
                            {elseif $search_filter.type === "range"}
                                {$search_filter_item_class = "btn-group search-filters__dropdown"}
                                {if $search_filter.data}
                                    {$item_id_from = "`$field_id`_`$search_filter.data.name_from`"}
                                    {$item_id_to = "`$field_id`_`$search_filter.data.name_to`"}
                                    {$is_show_clear_filter_btn = true}
                                    {if $_REQUEST[$search_filter.data.name_from]
                                        || $_REQUEST[$search_filter.data.name_to]
                                    }
                                        {$is_show = true}
                                        {$add_filter_variant_item_state[$field_id] = [
                                            show => true
                                        ]}
                                    {/if}
                                    {if $search_filter.data.value_from || $search_filter.data.value_to}
                                        {$is_has_value = true}
                                    {/if}
                                    <li>
                                        <div class="search-filters__item">
                                            <div>
                                                <label for="{$item_id_from}">
                                                    {$search_filter.data.label_from|default:__("search_range_from")}
                                                </label>
                                                <input type="text"
                                                    class="input-fill"
                                                    name="{$search_filter.data.name_from}"
                                                    id="{$item_id_from}"
                                                    value="{$search_filter.data.value_from}"
                                                    {if $search_filter.data.placeholder_from}
                                                        placeholder={$search_filter.data.placeholder_from}
                                                    {/if}
                                                    data-ca-search-filters="field"
                                                />
                                            </div>
                                            <div>
                                                <label for="{$item_id_to}">
                                                    {$search_filter.data.label_to|default:__("search_range_to")}
                                                </label>
                                                <input type="text"
                                                    class="input-fill"
                                                    name="{$search_filter.data.name_to}"
                                                    id="{$item_id_to}"
                                                    value="{$search_filter.data.value_to}"
                                                    {if $search_filter.data.placeholder_to}
                                                        placeholder={$search_filter.data.placeholder_to}
                                                    {/if}
                                                    data-ca-search-filters="field"
                                                />
                                            </div>
                                        </div>
                                    </li>
                                {/if}
                            {* Dropdown with raw content *}
                            {elseif $search_filter.type === "dropdown"}
                                {$search_filter_item_class = "btn-group search-filters__dropdown"}
                                {if $search_filter.content && $search_filter.content|trim !== ""}
                                    <li>
                                        <div class="search-filters__item-raw">
                                            {$search_filter.content nofilter}
                                        </div>
                                    </li>
                                {/if}
                            {* Default: Simple input *}
                            {else}
                                {$search_filter_item_class = "btn-group search-filters__dropdown"}
                                {$is_show_clear_filter_btn = true}
                                {if $_REQUEST[$search_filter.id]}
                                    {$is_show = true}
                                    {$add_filter_variant_item_state[$field_id] = [
                                        show => true
                                    ]}
                                {/if}
                                {if $search_filter.value}
                                    {$is_has_value = true}
                                {/if}
                                <li>
                                    <div class="search-filters__item">
                                        <label for="{$field_id}">{$search_filter.label}</label>
                                        <input type="text"
                                            class="input-fill"
                                            name="{$search_filter.id}"
                                            id="{$field_id}"
                                            value="{$search_filter.value}"
                                            placeholder="{$search_filter.placeholder}"
                                            data-ca-search-filters="field"
                                        />
                                    </div>
                                </li>
                            {/if}
                        </ul>
                    {/if}
                    {/capture}

                    {* Render search filters item *}
                    {$clear_filter_btn_class = ($is_has_value) ? "" : "hidden"}
                    <div class="{$search_filter_item_class}
                        {if !$is_show && $search_filter.category !== "primary"}hidden{/if}"
                        data-ca-search-filters="item"
                        data-ca-search-filters-item-type="{$search_filter.type}"
                        data-ca-search-filters-id="{$field_id}"
                        {if $search_filter_item_attributes}
                            {$search_filter_item_attributes|render_tag_attrs nofilter}
                        {/if}
                    >
                        {if $is_show_clear_filter_btn}
                            {btn type="button"
                                icon="icon-remove"
                                class="btn search-filters__clear-filter-btn `$clear_filter_btn_class`"
                                data=[
                                    "data-ca-search-filters" => "clearFilterBtn"
                                ]
                            }
                        {/if}
                        {$smarty.capture.search_filters_item nofilter}
                    </div>
                {/foreach}

                {* Add filter dropdown *}
                {include_ext file="common/icon.tpl"
                    source="plus"
                    class="search-filters__add-filter-icon"
                    assign="add_filter_icon"
                }
                <div class="dropdown search-filters__add-filter"
                    data-ca-search-filters="addFilter"
                >
                    {btn type="button"
                        raw=true
                        text="<span class=\"mobile-hidden\">{__("search_add_filter")}</span>`$add_filter_icon`"
                        class="btn dropdown-toggle search-filters__add-filter-dropdown-toggle"
                        data=[
                            "data-toggle" => "dropdown",
                            "data-ca-search-filters" => "addFilterBtn"
                        ]
                    }
                    <ul class="dropdown-menu search-filters__add-filter-dropdown-menu"
                        data-ca-search-filters="addFilterDropdownMenu"
                    >
                        {foreach $search_filters_without_context_search.data as $search_filter}
                            {if $search_filter.category === "primary"
                                || isset($search_filter.is_enabled) && !$search_filter.is_enabled
                                || isset($search_filter.is_hidden) && $search_filter.is_hidden
                            }
                                {continue}
                            {/if}
                            {$field_id = "`$form_id`_`$search_filter.id`"}
                            {$is_show = $add_filter_variant_item_state[$field_id].show}
                            <li data-ca-search-filters="addFilterVariantItem" {if $is_show}class="hidden"{/if}>
                                <label for="search_filters_add_filter_{$field_id}"
                                    class="checkbox search-filters__add-filter-content-variant-item-label"
                                >
                                    <input type="checkbox"
                                        form="search_filters_add_filter"
                                        value="{($is_show) ? ("YesNo::YES"|enum) : ("YesNo::NO"|enum)}"
                                        {if $is_show}checked="checked"{/if}
                                        name="search_filters_add_filter_{$field_id}"
                                        id="search_filters_add_filter_{$field_id}"
                                        data-ca-search-filters-add-filter-id="{$field_id}"
                                        data-ca-search-filters-add-filter-type="{$search_filter.type}"
                                        data-ca-search-filters-update="ignore"
                                        class="hidden"
                                    />
                                    {$search_filter.label nofilter}
                                </label>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            </div>

        <!--{$form_id}--></form>
    </div>

    {* Advanced search popup *}
    {if $search_filters.advanced_search}
        <div class="search-filters-advanced-search">
            {$search_filters.advanced_search nofilter}
        </div>
    {/if}

    {* Export *}
    {$context_search = $context_search scope=parent}
{/if}
