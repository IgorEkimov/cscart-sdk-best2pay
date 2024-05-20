{$all_product_statuses = []}
{$is_checked_some_all_product_statuses = false}
{foreach fn_get_all_product_statuses() as $status_id => $status_name}
    {$is_search_status_equal_status_id = ($search.status === $status_id)}
    {if $is_search_status_equal_status_id}
        {$is_checked_some_all_product_statuses = true}
    {/if}
    {$all_product_statuses["status_`$status_id`"] = [
        key => "status_`$status_id|lower`",
        label => $status_name,
        value => $status_id,
        is_checked => $is_search_status_equal_status_id
    ]}
{/foreach}
{$all_product_statuses = [
    status_empty => [
        key => "status_empty",
        label => "--",
        value => false,
        is_checked => !$is_checked_some_all_product_statuses
    ]
]|array_merge:$all_product_statuses}

{* Export *}
{$all_product_statuses = $all_product_statuses scope=parent}