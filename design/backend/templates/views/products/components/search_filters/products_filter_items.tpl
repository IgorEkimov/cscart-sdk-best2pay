{if $filter_items}
    <div class="group form-horizontal">
        <div class="control-group">
            {include file="views/products/components/advanced_search_form.tpl"
                filter_features=$filter_items
                prefix="`$form_id`_filter_"
                data_name="filter_variants"
            }
        </div>
    </div>
{/if}