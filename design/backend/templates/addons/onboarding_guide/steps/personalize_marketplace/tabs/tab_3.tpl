<div class="onboarding_content_margin--bottom_x2">
    <div class="onboarding_section__progress_title_text onboarding-guide__mb4">{__("onboarding_guide.set_default_currency")}</div>
    <a href="{"currencies.manage"|fn_url}" class="btn" target="_blank">{__("onboarding_guide.manage_currencies")} ↗</a>
</div>
<div class="onboarding_content_margin--bottom_x3">
    <div class="onboarding_section__progress_title_text onboarding-guide__mb4">{__("onboarding_guide.set_default_languages")}</div>
    <a href="{"languages.manage"|fn_url}" class="btn" target="_blank">{__("onboarding_guide.manage_languages")} ↗</a>
</div>

<form action="{""|fn_url}" method="post" enctype="multipart/form-data" name="email_template_form" class="onboarding_content_margin--bottom_x3">
    <input type="hidden" name="result_ids" value="preview_dialog" />
    <input type="hidden" name="template_id" value="{$data.preview_email_template_id}" />
    <p class="onboarding_section__progress_text onboarding-guide__mb4">
        {if (!empty($onboarding_guide_is_store_builder))}
            {__("onboarding_guide.sb_preview_an_email_description")}
        {else}
            {__("onboarding_guide.preview_an_email_description")}
        {/if}
    </p>
    <a class="og-step-complete btn btn-primary cm-process-items cm-submit cm-ajax cm-form-dialog-opener" data-ca-target-form="email_template_form" data-ca-dispatch="dispatch[email_templates.preview]">{__("onboarding_guide.preview_an_email")}</a>
</form>

<div class="onboarding_section__action_block">
    <button data-og-tab-navigation="2" type="button" class="btn btn-primary tab-navigation">{include_ext file="common/icon.tpl" class="icon-long-arrow-left"} {__("onboarding_guide.back")}</button>
</div>
{include file="views/email_templates/preview.tpl" preview=[]}
