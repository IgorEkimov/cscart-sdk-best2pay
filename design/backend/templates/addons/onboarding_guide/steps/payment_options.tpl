{if (empty($onboarding_guide_is_store_builder))}
    <div class="onboarding_content_margin--bottom_x2">
        <span class="onboarding_section__progress_text">{__("onboarding_guide.select_payment_options")}:</span>
    </div>

    <div id="og-payment-option-form">
        <form name="og-payment-option-form" class="onboarding_content_margin--bottom_x2 cm-ajax cm-ajax-force" action="{""|fn_url}" method="post">
            <input type="hidden" name="dispatch" value="onboarding_guide.change_payment_option" />
            <input type="hidden" name="result_ids" value="og-payment-option-form" />

        {foreach $data.payment_options as $payment_option_type => $payment_option}
            <label class="onboarding_section__label_block onboarding_content_margin--bottom_x2">
                <div>
                    <input type="radio"
                                name="payment_option"
                                id="radio_{$payment_option_type}"
                                class="cm-submit"
                                data-ca-target-form="og-payment-option-form"
                                value="{$payment_option_type}"
                                {if $data.current_payment_option_type === $payment_option_type}checked{/if}
                    />
                </div>
                <div>
                    <div class="onboarding_section__progress_title_text">{__("{$payment_option.name}")}</div>
                    <p class="onboarding_section__progress_text">{__("{$payment_option.description}")}</p>
                </div>
                <div>
                    <img src="{$images_dir}/{$payment_option.image}" />
                </div>
            </label>
        {/foreach}
        </form>
    <!--og-payment-option-form--></div>
{/if}


<div class="onboarding_content_margin--bottom">
    <span class="onboarding_section__progress_text">{__("onboarding_guide.configure_payment_methods_description")}</span>
</div>

<div class="onboarding_section__action_block onboarding_content_margin--bottom_x2 {if !$data.products_available}og-step-complete{/if}">
    <a href="{"payments.manage"|fn_url}" class="btn btn-primary" target="_blank">{__("onboarding_guide.configure_payment_methods")}</a>
</div>

{if $data.products_available}
    <div class="onboarding_content_margin--bottom">
        <span class="onboarding_section__progress_text">{__("onboarding_guide.view_checkout_page_description")}</span>
    </div>

    <div class="onboarding_section__action_block onboarding_content_margin--bottom_x2 og-step-complete">
        <a href="{"onboarding_guide.view_checkout"|fn_url}" class="btn" target="_blank">{__("onboarding_guide.view_checkout_page")} ↗</a>
    </div>
{/if}
