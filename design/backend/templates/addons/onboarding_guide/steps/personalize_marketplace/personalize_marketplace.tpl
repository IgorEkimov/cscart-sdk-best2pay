{script src="js/addons/onboarding_guide/steps/personalize_marketplace.js"}

<div class="onboarding_accordion__item_body--padding">
    <div class="onboarding_content_margin--bottom_x2">
        <span class="onboarding_section__progress_text">
            {if (!empty($onboarding_guide_is_store_builder))}
                {__("onboarding_guide.sb_make_changes_on_the_fly_description")}
            {else}
                {__("onboarding_guide.make_changes_on_the_fly_description")}
            {/if}
        </span>
    </div>

    <div class="tabs">
        <div class="tab-header">
            <button data-og-tab="button" class="tab-button active" >{__("onboarding_guide.theme_and_logo")}</button>
            <button data-og-tab="button" class="tab-button" >{__("onboarding_guide.business_details")}</button>
            <button data-og-tab="button" class="tab-button" >{__("onboarding_guide.languages_and_currency")}</button>
        </div>
        <div class="tab-content">
            <div data-og-tab="item" class="tab-item active">
                {include file="addons/onboarding_guide/steps/personalize_marketplace/tabs/tab_1.tpl"}
            </div>
            <div data-og-tab="item" class="tab-item">
                {include file="addons/onboarding_guide/steps/personalize_marketplace/tabs/tab_2.tpl"}
            </div>
            <div data-og-tab="item" class="tab-item">
                {include file="addons/onboarding_guide/steps/personalize_marketplace/tabs/tab_3.tpl"}
            </div>
        </div>
    </div>
</div>

<script>
    (function (_, $) {
        _.tr({
            'onboarding_guide.logos_updated': '{__("onboarding_guide.logos_updated")|escape:"javascript"}'
        })
    })(Tygh, Tygh.$);
</script>