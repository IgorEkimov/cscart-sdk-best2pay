{$sort_by_content = []}
{hook name="products:sort_by_content"}
{$sort_by_content = $sort_by_content|array_merge:[
    product => [
        id => "product",
        label => __("name"),
        selected => ($search.sort_by === "product")
    ],
    code => [
        id => "code",
        label => __("sku"),
        selected => ($search.sort_by === "code")
    ],
    price => [
        id => "price",
        label => __("price"),
        selected => ($search.sort_by === "price")
    ],
    list_price => [
        id => "list_price",
        label => __("list_price"),
        selected => ($search.sort_by === "list_price")
    ],
    amount => [
        id => "amount",
        label => __("quantity"),
        selected => ($search.sort_by === "amount")
    ],
    status => [
        id => "status",
        label => __("status"),
        selected => ($search.sort_by === "status")
    ]
]}
{/hook}
<div>
    <label for="sort_by">{__("sort_by")}</label>
    <select name="sort_by" id="{$form_id}_sort_by" class="input-fill">
        {foreach $sort_by_content as $sort_by_item}
            <option {if $sort_by_item.selected}selected="selected"{/if} value="{$sort_by_item.id}">{$sort_by_item.label}</option>
        {/foreach}
    </select>
</div>
<div>
    <select name="sort_order" id="{$form_id}_sort_order" class="input-fill">
        <option {if $search.sort_order_rev === "desc"}selected="selected"{/if} value="asc">{__("asc")}</option>
        <option {if $search.sort_order_rev === "asc"}selected="selected"{/if} value="desc">{__("desc")}</option>
    </select>
</div>