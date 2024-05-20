{* Slot: [data-ca-help-center="articles"] *}

<script type="text/template" data-ca-help-center="section" data-no-defer="true" data-no-execute="§"
    >{literal}<div class="help-center-section ${data.isShow ? '' : 'hidden'} ${data.class ? data.class : ''} help-center-section--${data.id}"
        id="content_help_center_${data.id}_${data.suffix}">
        <div class="help-center-section__header">
            <div class="help-center-section__header-title-wrapper">
                <h3 class="help-center-section__header-title">
                    ${data.name}
                </h3>
                ${data.dispatch_name
                    ? `<button type="button" class="help-center-section__header-reset" data-ca-help-center="resetDispatch">
                        <span class="help-center-section__header-reset-text">${data.dispatch_name}</span>
                        <span class="cs-icon help-center-section__header-reset-icon cs-icon--type-remove"><span class="hidden-accessible"></span><svg fill="currentColor" class="cs-icon__svg" focusable="false" aria-hidden="true" viewBox="0 0 20 20"><path d="m4.46967 4.46967c.29289-.29289.76777-.29289 1.06066 0l4.46967 4.46967 4.4697-4.46967c.2929-.29289.7677-.29289 1.0606 0s.2929.76777 0 1.06066l-4.4696 4.46967 4.4696 4.4697c.2929.2929.2929.7677 0 1.0606s-.7677.2929-1.0606 0l-4.4697-4.4696-4.46967 4.4696c-.29289.2929-.76777.2929-1.06066 0s-.29289-.7677 0-1.0606l4.46967-4.4697-4.46967-4.46967c-.29289-.29289-.29289-.76777 0-1.06066z"></path></svg></span>
                    </button>`
                    : ''
                }
            </div>
            ${data.action_url
                ? `<a href="${data.action_url}" target="_blank" class="help-center-btn help-center-btn--primary help-center-section__header-action">
                    ${data.action_name}
                </a>`
                : ''
            }
        </div>

        <div class="help-center-section__body">
            ${data.html ? `<div class="help-center-section__html">${data.html}</div>` : ''}

            <div class="help-center-section__articles help-center-section__articles--columns-${data.columns} ${data.columns > 1 ? 'help-center-section__articles--columns' : ''}"
                data-ca-help-center="articles"
                style="--help-center-section-columns: ${data.columns}"
                data-ca-help-center-no-data="${data.no_data_text}"
            ></div>

            ${data.all_items_url
                ? `<div class="help-center-section__bottom">
                    <a href="${data.all_items_url}" target="_blank" class="help-center-btn help-center-section__more">
                        ${data.all_items_name}
                    </a>
                </div>`
                : ''
            }
            <div class="help-center-section__footer">
                <div class="help-center-section__product-release-info">
                    ${data.product_release_info}
                </div>
            </div>
        </div>
</div>{/literal}</script>
