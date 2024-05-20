{$search_filters.data.sales_amount = [
    id => "sales_amount",
    type => "range",
    category => "secondary",
    label => __("sales_amount"),
    data => [
        name_from => "sales_amount_from",
        value_from => $search.sales_amount_from,
        name_to => "sales_amount_to",
        value_to => $search.sales_amount_to
    ]
]}

{* Export *}
{$search_filters = $search_filters scope=parent}

