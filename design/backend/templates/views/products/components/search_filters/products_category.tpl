{if $search.cid|is_array && $search.cid|count === 1}
    {$s_cid = $search.cid|reset}
{elseif $search.cid|is_array && $search.cid|count !== 1}
    {$s_cid = 0}
{else}
    {$s_cid = $search.cid}
{/if}

{if "categories"|fn_show_picker:$smarty.const.CATEGORY_THRESHOLD}
    <div class="controls">
        {include file="views/categories/components/picker/picker.tpl"
            input_name="cid"
            show_advanced=true
            multiple=false
            show_empty_variant=true
            item_ids=[$s_cid]
            empty_variant_text=__("all_categories")
            dropdown_css_class="object-picker__dropdown--categories"
            object_picker_advanced_btn_class="cm-dialog-destroy-on-close"
            attributes=[
                "data-ca-search-filters" => "field"
            ]
        }
    </div>
{else}
    {include file="views/categories/components/picker/picker.tpl"
        input_name="cid"
        show_advanced=true
        multiple=false
        show_empty_variant=true
        item_ids=[$s_cid]
        empty_variant_text=__("all_categories")
        dropdown_css_class="object-picker__dropdown--categories"
        object_picker_advanced_btn_class="cm-dialog-destroy-on-close"
        attributes=[
            "data-ca-search-filters" => "field"
        ]
    }
{/if}