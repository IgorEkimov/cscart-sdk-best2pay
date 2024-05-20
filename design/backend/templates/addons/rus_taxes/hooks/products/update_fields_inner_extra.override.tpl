{if $field === "fiscal_data_1212"}
    <label>{__("payment_object")}:
        <select name="products_data[{$product.product_id}][{$field}]>
            <optgroup label="{__("rus_taxes.available_starting_ffd_1.05")}">
                {foreach $fiscal_data_1212_objects as $fiscal_data_1212_type => $fiscal_data_1212_name}
                    {if
                        $fiscal_data_1212_type !== "FiscalData1212Objects::WITH_MARKING_CODE"|enum
                        && $fiscal_data_1212_type !== "FiscalData1212Objects::EXCISABLE_WITH_MARKING_CODE"|enum
                    }
                        <option value="{$fiscal_data_1212_type}" {if $product.$field|intval === $fiscal_data_1212_type}selected="selected"{/if}>
                            {__($fiscal_data_1212_name)}
                        </option>
                    {/if}
                {/foreach}
            </optgroup>
            <optgroup label="{__("rus_taxes.available_starting_ffd_1.2")}">
                {foreach $fiscal_data_1212_objects as $fiscal_data_1212_type => $fiscal_data_1212_name}
                    {if
                        $fiscal_data_1212_type === "FiscalData1212Objects::WITH_MARKING_CODE"|enum
                        || $fiscal_data_1212_type === "FiscalData1212Objects::EXCISABLE_WITH_MARKING_CODE"|enum
                    }
                        <option value="{$fiscal_data_1212_type}" {if $product.$field|intval === $fiscal_data_1212_type}selected="selected"{/if}>
                            {__($fiscal_data_1212_name)}
                        </option>
                    {/if}
                {/foreach}
            </optgroup>
        </select>
    </label>
{elseif $field === "mark_code_type"}
    {$marking_code_format_fur = "MarkingCodeFormats::FUR"|enum}

    <label>{__("is_fur_ware")}:
        <input
            type="hidden"
            name="products_data[{$product.product_id}][{$field}]"
            value=""
        />
        <input
            type="checkbox"
            name="products_data[{$product.product_id}][{$field}]"
            value="{$marking_code_format_fur}"
            {if $product.$field === $marking_code_format_fur}checked="checked"{/if}
        >
    </label>
{/if}
