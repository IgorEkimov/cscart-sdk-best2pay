<div class="onboarding_content_margin--bottom">
    <span class="onboarding_section__progress_text">{__("onboarding_guide.create_vendor_description")}</span>
</div>
<div class="onboarding_section__action_block onboarding_content_margin--bottom_x2">
    <a href="{"companies.add"|fn_url}" class="btn btn-primary" target="_blank">{__("onboarding_guide.create_one_vendor")} ↗</a>
</div>

<div class="onboarding_section__action_block onboarding_content_margin--bottom">
    <a href="{fn_url("profiles.act_as_user?user_id={$auth.user_id}&area=C&redirect_url=companies.catalog", $auth.user_type)}" class="og-step-complete btn btn-primary" target="_blank">{__("onboarding_guide.view_storefront")} ↗</a>
</div>