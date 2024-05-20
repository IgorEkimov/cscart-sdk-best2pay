{if $feature_items}
    <div class="group form-horizontal">
        <div class="control-group">
            {include file="views/products/components/advanced_search_form.tpl"
                filter_features=$feature_items
                prefix="`$form_id`_feature_"
                data_name="feature_variants"
            }
        </div>
    </div>
{elseif $feature_items_too_many}
    <div class="group form-horizontal">
        {__("error_features_too_many_variants")}
    </div>
{/if}