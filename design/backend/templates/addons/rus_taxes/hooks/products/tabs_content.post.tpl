{$marking_code_format_fur = "MarkingCodeFormats::FUR"|enum}

<div class="{if $selected_section !== "online_cash_registers"}hidden{/if}" id="content_online_cash_registers">
    <h4 class="subheader">{__("settings")}</h4>
    <div class="control-group">
        <label for="fiscal_data_1212" class="control-label">{__("payment_object")}:</label>
        <div class="controls">
            <select name="product_data[fiscal_data_1212]" id="fiscal_data_1212">
                <optgroup label="{__("rus_taxes.available_starting_ffd_1.05")}">
                    {foreach $fiscal_data_1212_objects as $fiscal_data_1212_type => $fiscal_data_1212_name}
                        {if
                            $fiscal_data_1212_type !== "FiscalData1212Objects::WITH_MARKING_CODE"|enum
                            && $fiscal_data_1212_type !== "FiscalData1212Objects::EXCISABLE_WITH_MARKING_CODE"|enum
                        }
                            <option value="{$fiscal_data_1212_type}" {if $product_data.fiscal_data_1212|intval === $fiscal_data_1212_type}selected="selected"{/if}>
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
                            <option value="{$fiscal_data_1212_type}" {if $product_data.fiscal_data_1212|intval === $fiscal_data_1212_type}selected="selected"{/if}>
                                {__($fiscal_data_1212_name)}
                            </option>
                        {/if}
                    {/foreach}
                </optgroup>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label for="product_is_fur_ware" class="control-label">{__("is_fur_ware")}:</label>
        <div class="controls">
            <input type="hidden" name="product_data[mark_code_type]" value="" />
            <input type="checkbox" name="product_data[mark_code_type]" id="product_is_fur_ware" value="{$marking_code_format_fur}" {if $product_data.mark_code_type === $marking_code_format_fur}checked="checked"{/if} />
        </div>
    </div>
</div>