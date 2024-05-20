<div class="onboarding_content_margin--bottom">
    <span class="onboarding_section__progress_text">
        {if (!empty($onboarding_guide_is_store_builder))}
            {__("onboarding_guide.sb_manage_products_description")}
        {else}
            {__("onboarding_guide.manage_products_description")}
        {/if}
    </span>
</div>

<div class="onboarding_section__action_block onboarding_content_margin--bottom_x2 {if !$data.products_available}og-step-complete{/if}">
    <a href="{"onboarding_guide.open_vendor_panel?redirect=products.add"|fn_url}" class="btn btn-primary" data-og-action="edit_style" target="_blank">{__("onboarding_guide.add_one_product")} ↗</a>
    <a href="{"onboarding_guide.open_vendor_panel?redirect={"import_presets.add&object_type=products"|urlencode}"|fn_url}" class="btn" data-og-action="edit_structure" target="_blank">{__("onboarding_guide.import_csv_or_xml")} ↗</a>
</div>

{if $data.products_available}
    <div class="onboarding_content_margin--bottom">
        <span class="onboarding_section__progress_text">{__("onboarding_guide.view_product_page_description")}</span>
    </div>

    <div class="onboarding_section__action_block onboarding_content_margin--bottom og-step-complete">
        <a href="{"onboarding_guide.preview_last_product"|fn_url}" class="btn btn-primary cm-tooltip" target="_blank" {if (empty($onboarding_guide_is_store_builder))}title="{__("onboarding_guide.premoderation")|unescape}"{/if} >{__("onboarding_guide.view_product_page")} ↗</a>
    </div>
{/if}
