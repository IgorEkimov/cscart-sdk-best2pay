<input type="hidden" name="is_search" value="{"YesNo::YES"|enum}" />
<input type="hidden" name="type" value="{$search_filters.search_type|default:"simple"}" />
<input type="hidden" name="update_view_id" value="{$search.view_id}" disabled data-ca-search-filters="updateView" />
{if $smarty.request.redirect_url}
    <input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
{/if}
{if $search_filters.selected_section !== ""}
    <input type="hidden" id="selected_section" name="selected_section" value="{$search_filters.selected_section}" />
{/if}
{if $put_request_vars}
    <div class="hidden" data-ca-search-filters="requestVars">
        {array_to_fields data=$smarty.request skip=["callback"] escape_all=true}
    </div>
{/if}