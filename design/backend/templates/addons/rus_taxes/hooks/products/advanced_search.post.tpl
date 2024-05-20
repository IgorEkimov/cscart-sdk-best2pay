<div class="row-fluid">
    <div class="group span6 form-horizontal">
        <div class="control-group">
            <label class="control-label" for="fiscal_data_1212">{__("payment_object")}:</label>
            <select name="fiscal_data_1212" id="fiscal_data_1212">
                <option value=""> -- </option>
                <optgroup label="{__("rus_taxes.available_starting_ffd_1.05")}">
                    {foreach $fiscal_data_1212_objects as $fiscal_data_1212_type => $fiscal_data_1212_name}
                        {if
                            $fiscal_data_1212_type !== "FiscalData1212Objects::WITH_MARKING_CODE"|enum
                            && $fiscal_data_1212_type !== "FiscalData1212Objects::EXCISABLE_WITH_MARKING_CODE"|enum
                        }
                            <option value="{$fiscal_data_1212_type}" {if $search.fiscal_data_1212|intval === $fiscal_data_1212_type}selected="selected"{/if}>
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
                            <option value="{$fiscal_data_1212_type}" {if $search.fiscal_data_1212|intval === $fiscal_data_1212_type}selected="selected"{/if}>
                                {__($fiscal_data_1212_name)}
                            </option>
                        {/if}
                    {/foreach}
                </optgroup>
            </select>
        </div>
        <div class="control-group">
            <label for="mark_code_type" class="control-label">{__("is_fur_ware")}:</label>
            <select name="mark_code_type" id="mark_code_type">
                <option value=""> -- </option>
                <option value="{"YesNo::YES"|enum}" {if $search.mark_code_type === "YesNo::YES"|enum}selected="selected"{/if}>
                    {__('yes')}
                </option>
                <option value="{"YesNo::NO"|enum}" {if $search.mark_code_type === "YesNo::NO"|enum}selected="selected"{/if}>
                    {__('no')}
                </option>
            </select>
        </div>
    </div>
</div>
