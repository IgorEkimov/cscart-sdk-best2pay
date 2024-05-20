<div class="onboarding_content_margin--bottom">
    <span class="onboarding_section__progress_text">{__("onboarding_guide.set_up_seller_plans_description")}</span>
</div>

<div class="onboarding_section__action_block onboarding_content_margin--bottom_x2">
    <a href="{"vendor_plans.manage"|fn_url}" class="btn btn-primary" target="_blank">{__("onboarding_guide.set_up_seller_plans")}</a>
</div>

<div class="onboarding_content_margin--bottom">
    <span class="onboarding_section__progress_text">{__("onboarding_guide.view_seller_plans_description")}</span>
</div>

<div class="onboarding_section__action_block onboarding_content_margin--bottom">
    <a href="{fn_url("profiles.act_as_user?user_id={$auth.user_id}&area=C&redirect_url=companies.vendor_plans", $auth.user_type)}" class="og-step-complete btn btn-primary" target="_blank">{__("onboarding_guide.view_seller_plans")} ↗</a>
</div>

