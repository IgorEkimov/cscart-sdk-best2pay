{if $field === "fiscal_data_1212"}
    <label>{__("payment_object")}:
        <select
                id="field_{$field}__"
                name="override_products_data[{$field}]"
                disabled="disabled"
        >
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
                id="field_{$field}__h"
                type="hidden"
                name="override_products_data[{$field}]"
                value=""
                disabled="disabled"
        />
        <input
                id="field_{$field}__"
                type="checkbox"
                name="override_products_data[{$field}]"
                value="{$marking_code_format_fur}"
                {if $product.$field === $marking_code_format_fur}checked="checked"{/if}
                disabled="disabled"
        >
    </label>
{/if}
