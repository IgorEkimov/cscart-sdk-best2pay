{if $auth.user_id
    && $auth.user_type === "UserTypes::ADMIN"|enum
    && $auth.is_root === "YesNo::YES"|enum
    && (
        fn_onboarding_guide_has_progress()
        || fn_onboarding_guide_is_dismissed()
    )
}
<a class="bp-dropdown-menu__item cm-no-ajax" href="{"onboarding_guide.restart"|fn_url}">{__("onboarding_guide.reset_onboarding")}</a>
{/if}