{$sort_by_content.bestsellers = [
    id => "bestsellers",
    label => __("bestsellers"),
    selected => ($search.sort_by == "bestsellers")
]}

{* Export *}
{$sort_by_content = $sort_by_content scope=parent}

