{*
    Import
    ---
    $search
    $dispatch
    $type
    $show_search_button
*}

{* Search filters params *}
{$search_filters = [
    form_id => $form_id,
    dispatch => $dispatch,
    page_part => $page_part,
    search_form_prefix => $product_search_form_prefix,
    search_type => $search_type,
    selected_section => $selected_section,
    advanced_search => $advanced_search,
    data => []
]}


{* Content for search forms *}

{* Get $have_amount_filter *}
{include file="views/products/components/search_filters/products_quantity.tpl"}

{* Get $is_enabled_select_vendor, $is_hidden_select_vendor, $select_vendor_label *}
{include file="views/products/components/search_filters/products_company_id.tpl"}

{* Get $all_product_statuses *}
{include file="views/products/components/search_filters/products_status.tpl"}


{* Search forms *}
{hook name="products:search_data"}

{$search_filters.data = $search_filters.data|array_merge:[
    q => [
        id => "q",
        type => "input",
        category => "primary",
        priority => 1000,
        label => __("searching_all_products"),
        value => $search.q
    ],
    price => [
        id => "price",
        type => "range",
        category => "primary",
        label => "{__("price")}&nbsp;({$currencies.$primary_currency.symbol nofilter})",
        data => [
            name_from => "price_from",
            value_from => $search.price_from,
            name_to => "price_to",
            value_to => $search.price_to
        ]
    ],
    category => [
        id => "category",
        type => "dropdown",
        category => "primary",
        label => __("category"),
        size => "large",
        content => {include file="views/products/components/search_filters/products_category.tpl"}
    ],
    status => [
        id => "status",
        type => "radio",
        category => "primary",
        label => __("status"),
        nested_data => $all_product_statuses
    ],
    subcats => [
        id => "subcats",
        type => "radio",
        category => "primary",
        label => __("subcategories"),
        nested_data => [
            subcats_yes => [
                key => "subcats_yes",
                label => __("yes"),
                value => "YesNo::YES"|enum,
                is_checked => (
                    ($search.subcats === "YesNo::YES"|enum)
                    || ($search.subcats !== "YesNo::YES"|enum && $search.subcats !== "YesNo::NO"|enum)
                )
            ],
            subcats_no => [
                key => "subcats_no",
                label => __("no"),
                value => "YesNo::NO"|enum,
                is_checked => ($search.subcats === "YesNo::NO"|enum)
            ]
        ]
    ],
    period => [
        id => "period",
        type => "dropdown",
        category => "primary",
        label => __("creation_date"),
        content => {include file="views/products/components/search_filters/products_period.tpl"}
    ],
    sort_by => [
        id => "sort_by",
        type => "dropdown",
        category => "primary",
        label => __("sort_by"),
        content => {include file="views/products/components/search_filters/products_sort_by.tpl"}
    ],
    search_in => [
        id => "search_in",
        type => "checkbox",
        category => "secondary",
        label => __("search_in"),
        nested_data => [
            pname => [
                key => "pname",
                label => __("product_name"),
                is_checked => ($search.pname === "YesNo::YES"|enum)
            ],
            pshort => [
                key => "pshort",
                label => __("short_description"),
                is_checked => ($search.pshort === "YesNo::YES"|enum)
            ],
            pfull => [
                key => "pfull",
                label => __("full_description"),
                is_checked => ($search.pfull === "YesNo::YES"|enum)
            ],
            pkeywords => [
                key => "pkeywords",
                label => __("keywords"),
                is_checked => ($search.pkeywords === "YesNo::YES"|enum)
            ]
        ]
    ],
    filter_items => [
        id => "filter_items",
        type => "popup",
        category => "secondary",
        is_enabled => isset($filter_items),
        label => __("filters"),
        content => {include file="views/products/components/search_filters/products_filter_items.tpl"}
    ],
    feature_items => [
        id => "feature_items",
        type => "popup",
        category => "secondary",
        is_enabled => isset($feature_items),
        label => __("features"),
        content => {include file="views/products/components/search_filters/products_feature_items.tpl"}
    ],
    pcode => [
        id => "pcode",
        type => "input",
        category => "secondary",
        label => __("sku"),
        value => $search.pcode
    ],
    pcode_from_q => [
        id => "pcode_from_q",
        type => "hidden",
        category => "secondary",
        is_hidden => true,
        value => "YesNo::YES"|enum
    ],
    popularity => [
        id => "popularity",
        type => "range",
        category => "secondary",
        label => __("popularity"),
        data => [
            name_from => "popularity_from",
            value_from => $search.popularity_from,
            name_to => "popularity_to",
            value_to => $search.popularity_to
        ]
    ],
    shipping_freight => [
        id => "shipping_freight",
        type => "range",
        category => "secondary",
        label => "{__("shipping_freight")}&nbsp;({$currencies.$primary_currency.symbol nofilter})",
        data => [
            name_from => "shipping_freight_from",
            value_from => $search.shipping_freight_from,
            name_to => "shipping_freight_to",
            value_to => $search.shipping_freight_to
        ]
    ],
    weight => [
        id => "weight",
        type => "range",
        category => "secondary",
        label => "{__("weight")}&nbsp;({$settings.General.weight_symbol})",
        data => [
            name_from => "weight_from",
            value_from => $search.weight_from,
            name_to => "weight_to",
            value_to => $search.weight_to
        ]
    ],
    amount => [
        id => "amount",
        type => "range",
        category => "secondary",
        is_enabled => !$have_amount_filter,
        label => __("quantity"),
        data => [
            name_from => "amount_from",
            value_from => $search.amount_from,
            name_to => "amount_to",
            value_to => $search.amount_to
        ]
    ],
    company_id => [
        id => "company_id",
        type => "popup",
        category => "secondary",
        is_enabled => $is_enabled_select_vendor,
        is_hidden => $is_hidden_select_vendor,
        label => $select_vendor_label,
        content => $smarty.capture.select_vendor
    ],
    free_shipping => [
        id => "free_shipping",
        type => "radio",
        category => "secondary",
        label => __("free_shipping"),
        nested_data => [
            free_shipping_empty => [
                key => "free_shipping_empty",
                label => "--",
                value => false,
                is_checked => ($search.free_shipping !== "YesNo::YES"|enum && $search.free_shipping !== "YesNo::NO"|enum)
            ],
            free_shipping_yes => [
                key => "free_shipping_yes",
                label => __("yes"),
                value => "YesNo::YES"|enum,
                is_checked => ($search.free_shipping === "YesNo::YES"|enum)
            ],
            free_shipping_no => [
                key => "free_shipping_no",
                label => __("no"),
                value => "YesNo::NO"|enum,
                is_checked => ($search.free_shipping === "YesNo::NO"|enum)
            ]
        ]
    ],
    order_ids => [
        id => "order_ids",
        type => "popup",
        category => "secondary",
        label => __("purchased_in_orders"),
        content => {include file="views/products/components/search_filters/products_order_ids.tpl"}
    ],
    updated_in_hours => [
        id => "updated_in_hours",
        type => "input",
        category => "secondary",
        label => __("updated_last"),
        value => $search.updated_in_hours,
        placeholder => __("hour_or_hours")
    ]
]}
{/hook}

{* Export *}
{$search_filters = $search_filters scope=parent}
