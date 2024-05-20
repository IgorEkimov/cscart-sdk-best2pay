{$themes = $data.themes}
{$theme_logo_id = $data.logos.theme.logo_id}
{$mail_logo_id = $data.logos.mail.logo_id}

{include file="common/previewer.tpl"}

<div id="og-design">
    <form id="og-design-form" class="form_container onboarding_content_margin--bottom_x3" onsubmit="event.preventDefault();">
        <div class="form_container__form_control">
                <label for="og-logo-file">
                    <div class="form_container__label">{__("onboarding_guide.upload_logo")}</div>
                    <input class="form_container__input hidden_input upload_image_input" id="og-logo-file" type="file" name="file_logo_image_icon[theme]"/>

                    <input type="text" class="hidden" name="logo_image_data[theme][type]" value="M">
                    <input type="text" class="hidden" name="logo_image_data[theme][object_id]" value="{$theme_logo_id}">
                    <input type="text" class="hidden" name="type_logo_image_icon[theme]" value="local">

                    <input type="text" class="hidden" name="logo_image_data[mail][type]" value="M">
                    <input type="text" class="hidden" name="logo_image_data[mail][object_id]" value="{$mail_logo_id}">
                    <input type="text" class="hidden" name="type_logo_image_icon[mail]" value="local">

                    <div class="upload_input">
                        {$logo_path = $data.logos.theme.image.image_path}
                        <div class="upload_input__image active">
                            <div class="upload_input__image_cover">
                            <div class="upload_input__image_circle">
                                {include_ext file="common/icon.tpl" class="icon-plus"}
                            </div>
                            </div>
                            <img id="og-logo-img" src="{$logo_path}">
                        </div>
                        <div class="upload_circle {($logo_path) ? 'hide' : ''}">
                                {include_ext file="common/icon.tpl" class="icon-camera"}
                        </div>
                    </div>
                </label>
        </div>
        <div class="form_container__form_control">
                <label class="form_container__label" for="og-logo-alt">{__("alternative_text")}</label>
                <input class="form_container__input" id="og-logo-alt" type="text" name="logo_image_data[theme][image_alt]" value="{$data.logos.theme.image.alt}"/>
        </div>
    </form>

    <div class="onboarding_section__progress_title_text onboarding_content_margin--bottom_x2">{__("onboarding_guide.select_theme")}</div>
    <div class="theme_form onboarding_content_margin--bottom_x3">
        <div class="theme_form__grid">
            {foreach $themes as $theme_id => $theme}
                {$theme_image = $theme.image|default:"`$images_dir`/user_styles.png"}
                <div class="theme_form__grid_item og-theme" id="{($theme.is_current) ? "og-current-theme" : ""}" data-og-theme-id="{$theme_id}">
                    <div class="og-theme-custom">{__("onboarding_guide.custom_theme")}</div>
                    <a
                        id="det_{$theme_id}"
                        href="{$theme_image}"
                        data-ca-image-id="img_{$theme_id}"
                        style="background-image: url({$theme_image});"
                        class="theme_form__grid_item_image onboarding_content_margin--bottom_x2 og-theme-image cm-previewer og_theme_image"
                    >
                        <img class="theme_form__grid_item_image_img" src="{$theme_image}" />
                        <div class="theme_form__grid_item_image_cover og_cover">
                            <div class="theme_form__grid_item_image_circle og_circle">
                                {include_ext file="common/icon.tpl" class="icon-eye-open"}
                            </div>
                        </div>
                    </a>
                    <div class="theme_form__grid_item_actions onboarding_content_margin--bottom_x2">
                        <div class="theme_form__grid_item_actions_info">
                            <span class="onboarding_section__progress_title_text">{$theme.title}</span>
                            <span class="onboarding_section__progress_text og-style-name">{$theme.style_id}</span>
                        </div>
                        <div class="theme_form__grid_item_actions_input">
                            <label>
                                <input class="theme-input og-theme-activator" name="theme_name" type="radio" value="{$theme_id}" {($theme.is_current) ? "checked" : ""} />
                                <div class="buttons">
                                    <div class="btn disabled button-checked">{__("onboarding_guide.current_theme")}</div>
                                    <div class="btn btn-primary button-unchecked">{__("onboarding_guide.activate")}</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="theme_variants">
                        {foreach $theme.styles as $style_id => $style}
                            {$style_color = $style.main_color|default:"#ccc"}
                            {$style_image = $style.image|default:"`$images_dir`/user_styles.png"}
                            <label>
                                <input
                                    data-og-theme-color="{$style.main_color}"
                                    class="theme_original_radio og-theme-style"
                                    type="radio"
                                    data-og-theme-id="{$theme_id}"
                                    name="{$theme_id}_style"
                                    value="{$style_id}" data-og-style-image="{$style_image}" {($style_id === $theme.style_id) ? "checked" : ""} data-og-is-default="{($style.is_default) ? 1 : 0}"/>
                                <div class="theme_custom_radio" style="background-color: {$style_color}"></div>
                            </label>
                        {/foreach}
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
    <div class="onboarding_section__action_block">
        <button data-og-tab-navigation="2" type="button" class="btn btn-primary tab-navigation tab-navigation-scroll">{__("onboarding_guide.next")} {include_ext file="common/icon.tpl" class="icon-long-arrow-right"}</button>
    </div>
<!--og-design--></div>