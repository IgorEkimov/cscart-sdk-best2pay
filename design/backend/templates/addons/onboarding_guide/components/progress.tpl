{$total = $onboarding_guide_progress.total_steps}
{$completed = $onboarding_guide_progress.completed_steps}
{$percentage = $onboarding_guide_progress.percentage}

<div class="onboarding_section__progress" id="og-progress-container">
    <div class="onboarding_section__progress_header">
        <span class="onboarding_section__progress_text" id="og-progress-text">
            {__("onboarding_guide.completed_steps_progress", ["[completed]" => $completed, "[total]" => $total])}
        </span>
        {hook name="onboarding_guide:progress_menu"}
        {capture name="tools_list"}
            <li>
                <a href="#" id="og-dismiss-button">{__('onboarding_guide.dismiss')}</a>
            </li>
            {if $onboarding_guide_progress.completed_steps}
                <li>
                    <a href="{"onboarding_guide.restart"|fn_url}" id="og-reset-button">
                        {__('onboarding_guide.reset')}
                    </a>
                </li>
            {/if}
        </li>
        {/capture}
        {dropdown content=$smarty.capture.tools_list
            icon="icon-ellipsis-horizontal"
            no_caret=true
            class_toggle="btn-link muted"
            class="more-btn"
        }
        {/hook}
    </div>
    <div class="onboarding_section__progress_bar progress">
        <div class="onboarding_section__progress_bar_line bar bar-success"
            id="og-progressbar"
            style="width: {$percentage}%"></div>
    </div>
<!--og-progress-container--></div>