{$is_open = $step.status === "Addons\OnboardingGuide\StepStatusEnum::OPEN"|enum}
{$is_completed = $step.status === "Addons\OnboardingGuide\StepStatusEnum::COMPLETED"|enum}
{$data=$step_id|fn_onboarding_guide_load_step_data}

<div data-og-accordion="item" class="og-step onboarding_accordion__item {($is_open) ? "onboarding_accordion__item--active" : ""}" data-step-id="{$step_id}">
    <div class="onboarding_accordion__item-col">
        <div class="cm-tooltip" data-og-status-tooltip-step="{$step_id}" title="{($is_completed) ? {__('onboarding_guide.step_close')} : {__('onboarding_guide.step_complete')}}">
            <div class="onboarding_accordion__item_checkbox">
                <label>
                    <input class="originalCheckbox og-step-status" type="checkbox" {($is_completed) ? checked : ""}/>
                    <div class="customCheckbox">
                        <span class="customCheckbox_text">{$step.number}</span>
                        {include_ext file="common/icon.tpl" source="ok" class="customCheckbox_icon customCheckbox_icon--ok"}
                        {include_ext file="common/icon.tpl" source="remove" class="customCheckbox_icon customCheckbox_icon--remove"}
                    </div>
                </label>
            </div>
        </div>
    </div>
    <div class="onboarding_accordion__item-col">
        <div data-og-accordion="item-head" class="onboarding_accordion__item_head" >
            <span class="onboarding_accordion__item_title">{__($step.title)}</span>
            <span>
                {include_ext file="common/icon.tpl" source="chevron_down" class="onboarding_accordion__item_icon"}
            </span>
        </div>

        <div data-og-accordion="item-body" class="onboarding_accordion__item_body" style="{(!$is_open) ? "display: none;" : ""}">
            {include file=$step.template step_id=$step_id step=$step}
        </div>
    </div>
</div>
