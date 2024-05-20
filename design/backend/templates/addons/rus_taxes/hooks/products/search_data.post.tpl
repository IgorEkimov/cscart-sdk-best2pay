{$search_filters.data.rus_taxes_payment_object_filter = [
    id          => "fiscal_data_1212",
    type        => "radio",
    category    => "secondary",
    label       => __("payment_object"),
    value       => $search.fiscal_data_1212,
    is_enabled  => true,
    is_hidden   => false,
    nested_data => [
        none => [
            key        => "none",
            label      => " -- ",
            value      => false,
            is_checked => !$search.fiscal_data_1212
        ]
    ]
]}

{foreach $fiscal_data_1212_objects as $fiscal_data_1212_type => $fiscal_data_1212_name}
    {$search_filters.data.rus_taxes_payment_object_filter.nested_data.$fiscal_data_1212_type = [
        key        => $fiscal_data_1212_type,
        label      => __($fiscal_data_1212_name),
        value      => $fiscal_data_1212_type,
        is_checked => ($search.fiscal_data_1212 == $fiscal_data_1212_type)
    ]}
{/foreach}

{$search_filters.data.rus_taxes_mark_code_type_filter = [
    id          => "mark_code_type",
    type        => "radio",
    category    => "secondary",
    label       => __("is_fur_ware"),
    value       => $search.mark_code_type,
    is_enabled  => true,
    is_hidden   => false,
    nested_data => [
        none => [
            key        => "none",
            label      => " -- ",
            value      => false,
            is_checked => !$search.mark_code_type
        ],
        yes => [
            key        => "mark_code_type_yes",
            label      => __("yes"),
            value      => "YesNo::YES"|enum,
            is_checked => ($search.mark_code_type === "YesNo::YES"|enum)
        ],
        no => [
            key        => "mark_code_type_no",
            label      => __("no"),
            value      => "YesNo::NO"|enum,
            is_checked => ($search.mark_code_type === "YesNo::NO"|enum)
        ]
    ]
]}

{$search_filters=$search_filters scope=parent}