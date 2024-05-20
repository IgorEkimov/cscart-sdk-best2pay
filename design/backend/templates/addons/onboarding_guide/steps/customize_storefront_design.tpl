<div class="onboarding_content_margin--bottom_x2">
    <span class="onboarding_section__progress_text">
        {if (!empty($onboarding_guide_is_store_builder))}
            {__("onboarding_guide.sb_make_changes_on_the_fly_description")}
        {else}
            {__("onboarding_guide.make_changes_on_the_fly_description")}
        {/if}
    </span>
</div>

<div class="onboarding_section__action_block onboarding_content_margin--bottom_x2">
    <a href="{"onboarding_guide.customize_storefront?type=theme_editor"|fn_url}" class="btn btn-primary og-action" data-og-action="edit_style" target="_blank">{__("onboarding_guide.modify_colors_fonts_backgrounds")} ↗</a>
</div>

<div class="onboarding_section__action_block onboarding_content_margin--bottom_x2">
    <a href="{"onboarding_guide.customize_storefront?type=live_editor"|fn_url}" class="btn btn-primary og-action" data-og-action="edit_texts" target="_blank">{__("onboarding_guide.edit_texts")} ↗</a>
</div>

<div class="onboarding_section__action_block onboarding_content_margin--bottom_x2">
    <a href="{"onboarding_guide.customize_storefront?type=block_manager"|fn_url}" class="btn btn-primary og-action" data-og-action="edit_blocks" target="_blank">{__("onboarding_guide.edit_blocks")} ↗</a>
</div>
