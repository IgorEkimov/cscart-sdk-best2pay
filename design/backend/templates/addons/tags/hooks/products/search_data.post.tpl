{if $runtime.company_id && "ULTIMATE"|fn_allowed_for || "MULTIVENDOR"|fn_allowed_for}
    {$search_filters.data.tag = [
        id => "tag",
        type => "input",
        category => "secondary",
        label => __("tag"),
        value => $search.tag
    ]}

    {* Export *}
    {$search_filters = $search_filters scope=parent}
{/if}
