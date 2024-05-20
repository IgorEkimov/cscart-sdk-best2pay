{if $onboarding_guide_steps}

{script src="js/addons/onboarding_guide/core.js"}

<section class="onboarding_section" id="onboarding-guide">
    <div class="onboarding_section__container">
        <h3 class="onboarding_section__title">
            {if (!empty($onboarding_guide_is_store_builder))}
                {__("onboarding_guide.sb_guide_title")}
            {else}
                {__("onboarding_guide.guide_title")}
            {/if}
        </h3>
        {include file="addons/onboarding_guide/components/progress.tpl"}
        <div class="onboarding_accordion__content">
            {foreach $onboarding_guide_steps as $step_id => $step}
                {include file="addons/onboarding_guide/components/step.tpl" step_id=$step_id step=$step}
            {/foreach}
        </div>
    </div>
</section>
{/if}

<script>
    (function (_, $) {
        _.tr({
            'onboarding_guide.completed_steps_progress': '{__("onboarding_guide.completed_steps_progress")|escape:"javascript"}',
            'onboarding_guide.step_complete': '{__("onboarding_guide.step_complete")|escape:"javascript"}',
            'onboarding_guide.step_close': '{__("onboarding_guide.step_close")|escape:"javascript"}',
        })
    })(Tygh, Tygh.$);
</script>