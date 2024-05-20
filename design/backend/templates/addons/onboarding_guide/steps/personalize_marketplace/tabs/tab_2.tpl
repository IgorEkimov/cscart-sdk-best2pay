{$company = $data.company_settings}

{include file="views/profiles/components/profiles_scripts.tpl" states=1|fn_get_all_states}

<div id="onboarding_guide_company_settings">
    <form class="cm-ajax cm-ajax-force" action="{""|fn_url}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="dispatch" value="onboarding_guide.update_company_settings" />
        <input type="hidden" name="result_ids" value="onboarding_guide_company_settings" />

        <div class="form_container onboarding_content_margin--bottom_x3">
            <div class="form_container__form_control">
                <label class="form_container__label" for="og-company-settings-name">{$company.company_name.description}</label>
                <input class="form_container__input" id="og-company-settings-name" type="text" value="{$company.company_name.value}" name="company_settings[company_name]"/>
            </div>
            <div class="form_container__form_control">
                <label class="form_container__label" for="og-company-settings-address">{$company.company_address.description}</label>
                <input class="form_container__input" id="og-company-settings-address" type="text" value="{$company.company_address.value}" name="company_settings[company_address]"/>
            </div>
            <div class="form_container__form_control">
                <label class="form_container__label" for="og-company-settings-website">{$company.company_website.description}</label>
                <input class="form_container__input" id="og-company-settings-website" type="text" placeholder="http://example.com" value="{$company.company_website.value}" name="company_settings[company_website]"/>
            </div>
            <div class="form_container__form_control">
                <label class="form_container__label" for="og-company-settings-city">{$company.company_city.description}</label>
                <input class="form_container__input" id="og-company-settings-city" type="text" value="{$company.company_city.value}" name="company_settings[company_city]"/>
            </div>
            <div class="form_container__form_control">
                <label class="form_container__label" for="og-company-settings-site-administrator">{$company.company_site_administrator.description}</label>
                <input class="form_container__input" id="og-company-settings-site-administrator" type="text" value="{$company.company_site_administrator.value}" name="company_settings[company_site_administrator]"/>
            </div>
            {* COUNTRY *}
            <div class="form_container__form_control">
                <label class="form_container__label" for="og-company-settings-country">{$company.company_country.description}</label>
                <div class="form_container__select_cover">
                    <select class="form_container__select cm-country cm-location-og" id="og-company-settings-country" name="company_settings[company_country]">
                        <option value="">- {__("select_country")} -</option>
                        {$countries=""|fn_get_simple_countries}
                        {foreach $countries as $code => $country}
                            <option value="{$code}" {if $code === $company.company_country.value}selected="selected"{/if}>{$country}</option>
                        {/foreach}
                    </select>
                    {include_ext file="common/icon.tpl" class="icon-chevron-down form_container__select_chevron"}
                </div>
            </div>
            <div class="form_container__form_control">
                <label class="form_container__label" for="og-company-settings-phone">{$company.company_phone.description}</label>
                <input class="form_container__input cm-mask-phone js-mask-phone-inited" id="og-company-settings-phone" type="text" value="{$company.company_phone.value}" inputmode="numeric" name="company_settings[company_phone]"/>
            </div>
            {* STATE *}
            <div class="form_container__form_control">
                <label class="form_container__label" for="og-company-settings-state">{$company.company_state.description}</label>
                <div class="form_container__select_cover">
                    <select class="form_container__select cm-state cm-location-og" id="og-company-settings-state" name="company_settings[company_state]">
                        <option value="">- {__("select_state")} -</option>
                    </select>
                    <input type="text" id="og-company-settings-state_d" name="company_settings[company_state]" value="{$company.company_state.value}" size="32" maxlength="64" disabled="disabled" class="cm-state cm-location-og hidden" />
                    {include_ext file="common/icon.tpl" class="icon-chevron-down form_container__select_chevron"}
                </div>
            </div>
            <div class="form_container__form_control">
                <label class="form_container__label" for="og-company-settings-year">{$company.company_start_year.description}</label>
                <div class="form_container__select_cover">
                    <input class="form_container__input" id="og-company-settings-year" type="text" value="{$company.company_start_year.value}" name="company_settings[company_start_year]"/>
                </div>
            </div>
            <div class="form_container__form_control">
                <label class="form_container__label" for="og-company-settings-zip">{$company.company_zipcode.description}</label>
                <input class="form_container__input" id="og-company-settings-zip" type="text" value="{$company.company_zipcode.value}" name="company_settings[company_zipcode]"/>
            </div>
        </div>
        <div class="onboarding_section__action_block">
            <button data-og-tab-navigation="1" type="button" class="btn btn-primary tab-navigation">{include_ext file="common/icon.tpl" class="icon-long-arrow-left"} {__("onboarding_guide.back")}</button>
            <button data-og-tab-navigation="3" type="button" class="btn btn-primary tab-navigation">{__("onboarding_guide.next")} {include_ext file="common/icon.tpl" class="icon-long-arrow-right"}</button>
            <button class="btn">{__("onboarding_guide.save")}</button>
        </div>
    </form>
<!--onboarding_guide_company_settings--></div>

