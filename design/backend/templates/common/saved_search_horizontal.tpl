{if $saved_search && $saved_search.dispatch && $view_type}
    {* 
        Import
        ---
        $saved_search
        $view_type
        $enable_fill
        $is_compact_view
        $search
        $last_view_current_object_schema

        Local
        ---
        $saved_search_count_min_threshold
        $saved_search_count_max_threshold
        $views
        $return_current_url
        $redirect_current_url
        $saved_search_count_threshold
        $views_active_before_threshold
        $temp_views
        $new_search
        $views_count
        $active_view
        $view

        Global
        ---
        $config
    *}

    {script src="js/tygh/saved_search_horizontal.js"}

    {$saved_search_count_min_threshold = $config.saved_search.count_min_threshold|default:5}
    {$saved_search_count_max_threshold = $config.saved_search.count_max_threshold|default:10}
    {$new_search = $saved_search.allow_new_search|default:true}
    {$views = $view_type|fn_get_views}
    {$return_current_url = $config.current_url|fn_query_remove:"view_id":"new_view"}
    {$redirect_current_url = $config.current_url|escape:url}
    {$saved_search_count_threshold = ($is_compact_view)
        ? $saved_search_count_min_threshold
        : $saved_search_count_max_threshold
    }
    {$saved_search_count_threshold = $saved_search_count_threshold - 1} {* "All" view *}
    {$views_active_before_threshold = []}
    {$temp_views = $views}

    {* Fill pills if there are fewer than 7 *}
    {$enable_fill = $enable_fill|default:true}

    {* Add "All" and "Save this search as" tabs *}
    {$views_count = ($new_search) ? $views|@count + 2 : $views|@count + 1}

    {if $views}
        {capture name="move_active_before_threshold"}
            {foreach $temp_views as $view}
                {if $view@iteration <= $saved_search_count_threshold || $view.view_id|intval !== $search.view_id|intval}
                    {continue}
                {/if}
                {$active_view = $temp_views|array_splice:($view@index):1}
                {$temp_views|array_splice:($saved_search_count_threshold - 1):0:$active_view}
                {break}
            {/foreach}
            {foreach $temp_views as $view}
                {$views_active_before_threshold[$view.view_id|intval] = $view}
            {/foreach}
        {/capture}
    {/if}

    <div class="pills {if $enable_fill}pills--enable-fill{/if} pills--count-{$views_count}">
        <ul class="nav nav-pills saved-search-horizontal">
            <li class="saved-search__item saved-search__item--horizontal
                {if !$search.view_id|intval && !$search.temp_view}active{/if}">
                <a href="{"`$saved_search.dispatch`.reset_view?`$view_suffix`"|fn_url}" class="saved-search__item-name saved-search__item-name--horizontal">{__("all")}</a>
            </li>
            {if $views}
                {foreach $views as $view name=views}
                    {$saved_search_item_class = ""}
                    {foreach $views_active_before_threshold as $views_active_before_threshold_view}
                        {if $views_active_before_threshold_view@iteration <= $saved_search_count_threshold}
                            {continue}
                        {/if}
                        {if $view.view_id|intval === $views_active_before_threshold_view.view_id|intval}
                            {$saved_search_item_class = "saved-search__item--desktop-hidden"}
                        {/if}
                    {/foreach}

                    {* Active saved search with allow default view or create new search *}
                    {if $view.view_id|intval === $search.view_id|intval
                        && (
                            $last_view_current_object_schema.allow_default_view
                            || $new_search
                        )
                    }
                    <li class="dropdown active saved-search__item saved-search__item--horizontal {$saved_search_item_class}">
                        <a class="cm-view-name dropdown-toggle saved-search__item-name saved-search__item-name--horizontal"
                            href="#"
                            data-toggle="dropdown"
                            data-ca-saved-search-horizontal-view-id="{$view.view_id}"
                            data-ca-saved-search-horizontal-view-name="{$view.name}"
                        >
                            {$view.name} <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            {if $last_view_current_object_schema.allow_default_view}
                                <li>
                                    {if $view.is_default === "YesNo::YES"|enum}
                                        <a href="{"`$saved_search.dispatch`.unset_default_view?view_id=`$view.view_id`&redirect_url=`$redirect_current_url`"|fn_url}"
                                            class="cm-confirm"
                                            {([
                                                "data-ca-confirm-text" => __("saved_search.set_as_non_default_confirm", [
                                                        "[name]" => $view.name
                                                    ])
                                            ])|render_tag_attrs nofilter}
                                        >
                                            <span class="flex-inline top">
                                                {include_ext file="common/icon.tpl" class="icon-pushpin"}
                                            </span>
                                            {__("saved_search.set_as_non_default")}
                                        </a>
                                    {else}
                                        <a href="{"`$saved_search.dispatch`.set_default_view?view_id=`$view.view_id`&redirect_url=`$redirect_current_url`"|fn_url}"
                                            class="cm-confirm"
                                            {([
                                                "data-ca-confirm-text" => __("saved_search.set_as_default_confirm", [
                                                        "[name]" => $view.name
                                                    ])
                                            ])|render_tag_attrs nofilter}
                                        >
                                            <span class="flex-inline top">
                                                {include_ext file="common/icon.tpl" class="icon-pushpin"}
                                            </span>
                                            {__("saved_search.set_as_default")}
                                        </a>
                                    {/if}
                                </li>
                            {/if}
                            {if $new_search}
                                <li>
                                    <a href="{"`$saved_search.dispatch`.delete_view?view_id=`$view.view_id`&redirect_url=`$redirect_current_url`"|fn_url}"
                                        class="cm-confirm text-error"
                                    >
                                        <span class="flex-inline top">
                                            {include_ext file="common/icon.tpl" class="icon-trash"}
                                        </span>
                                        {__("delete")}
                                    </a>
                                </li>
                            {/if}
                        </ul>
                    </li>
                    {* Active saved search without permissions *}
                    {elseif $view.view_id|intval === $search.view_id|intval}
                    <li class="active saved-search__item saved-search__item--horizontal {$saved_search_item_class}">
                        <a class="cm-view-name saved-search__item-name"
                            href="#"
                            data-ca-saved-search-horizontal-view-id="{$view.view_id}"
                            data-ca-saved-search-horizontal-view-name="{$view.name}"
                        >
                            {$view.name}
                        </a>
                    </li>
                    {else}
                    {* Saved search item *}
                    <li class="saved-search__item saved-search__item--horizontal {$saved_search_item_class}">
                        <a class="cm-view-name saved-search__item-name saved-search__item-name--horizontal
                        {if $last_view_current_object_schema.allow_default_view}
                            saved-search__item-name--default-view
                        {/if}
                        "
                            href="{"`$saved_search.dispatch`?view_id=`$view.view_id``$view_additional_parameters`&`$view_suffix`"|fn_url}"
                            data-ca-saved-search-horizontal-view-id="{$view.view_id}"
                            data-ca-saved-search-horizontal-view-name="{$view.name}"
                        >
                            {$view.name}
                        </a>
                    </li>
                    {/if}
                {/foreach}
            {/if}

            {* Custom saved search *}
            {if $search.temp_view}
                <li class="saved-search__item saved-search__item--horizontal active {$saved_search_item_class}">
                    <a href="#">{__("custom_search")}</a>
                </li>
            {/if}

            {* More saved search dropdown *}
            {if $views && $views_active_before_threshold|@count > $saved_search_count_threshold}
                <li class="btn-group saved-search__item-more mobile-hidden">
                    <a class="saved-search__item-name--horizontal saved-search__item-name--more dropdown-toggle" data-toggle="dropdown">
                        {__("saved_search.more")}
                        <span class="caret"></span>
                    </a>
                    <ul id="tools_list_saved_search_horizontal" class="dropdown-menu cm-smart-position">
                        {foreach $views_active_before_threshold as $view}
                            {if $view@iteration <= $saved_search_count_threshold}
                                {continue}
                            {/if}
                            <li class="saved-search__dropdown-item {if $view.view_id|intval === $search.view_id|intval}active{/if} {if $view.wrapper_class}{$view.wrapper_class}{/if}">
                                {btn type=$view.type|default:"list"
                                    href="`$saved_search.dispatch`?view_id=`$view.view_id``$view_additional_parameters`&`$view_suffix`"
                                    text=$view.name
                                    title=$view.description
                                    id=$view.id
                                    method=$view.method
                                    target=$view.target
                                    process=$view.process
                                    class=($view.meta) ? "saved-search__dropdown-item-name `$view.meta`" : "saved-search__dropdown-item-name"
                                    form=$view.form
                                    dispatch=$view.dispatch
                                    target=$view.target
                                    data=[
                                        "data-ca-saved-search-horizontal-view-id" => $view.view_id,
                                        "data-ca-saved-search-horizontal-view-name" => $view.name
                                    ]
                                    onclick=$view.onclick
                                    raw=$view.raw
                                    icon=$view.icon
                                }
                            </li>
                        {/foreach}
                    </ul>
                </li>
            {/if}

            {* Save this search as button *}
            {if $new_search}
                <li class="saved-search__item saved-search__item--horizontal saved-search__item--new">
                    <button type="button"
                        class="saved-search__item-name saved-search__item-name--horizontal saved-search__item-name--new"
                        data-ca-saved-search-horizontal="searchSave"
                    >
                        {include_ext file="common/icon.tpl" class="icon-plus"}
                    </button>
                </li>
            {/if}
        </ul>
    </div>
{/if}
