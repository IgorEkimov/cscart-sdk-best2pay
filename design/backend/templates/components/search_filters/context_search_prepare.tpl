{if $search_filters}
    {* Get context search field from search filters *}
    {$context_search = []}
    {foreach $search_filters.data as $search_filter_key => $search_filter}
        {if $search_filter.priority
            && $search_filter.type === "input"
            && (empty($context_search) || $search_filter.priority >= $context_search.priority)
        }
            {$context_search = $search_filter}
            {$context_search.form_id = $form_id}
        {/if}
    {/foreach}

    {* Remove context search field from search filters *}
    {if $context_search && $context_search.id}
        {$search_filters_without_context_search = $search_filters}
        {$search_filters_without_context_search.data = $search_filters.data|unset_key:$context_search.id}
    {/if}

    {* Export *}
    {if $context_search}
        {$search_filters_without_context_search = $search_filters_without_context_search scope=parent}
        {$context_search = $context_search scope=parent}
    {/if}
{/if}