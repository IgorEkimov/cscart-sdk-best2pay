{if !$not_saved && $smarty.request.dispatch|strpos:".picker" === false}
    {capture name="search_save"}
        <div data-ca-search-filters="searchSave">
            <div class="form-horizontal" data-ca-search-filters="searchSaveContent">
                <div class="control-group">
                    <label for="{$form_id}_view_name" class="control-label cm-required">{__("name")}</label>
                    <div class="controls">
                        <input type="text"
                            id="{$form_id}_view_name"
                            name="new_view"
                            class="input-xlarge"
                            value="{if $search.view_id && $views[$search.view_id]}{$views[$search.view_id].name}{/if}"
                            form="{$form_id}"
                            data-ca-search-filters="viewName"
                            data-ca-search-filters-update="ignore"
                        />
                    </div>
                </div>
            </div>
            <div class="buttons-container">
                <a class="cm-dialog-closer cm-inline-dialog-closer tool-link btn">{__("cancel")}</a>
                {if ""|fn_check_view_permissions}
                    {include file="buttons/button.tpl"
                        but_text=__("save")
                        but_role="submit"
                        but_name="dispatch[`$search_filters.dispatch`.save_view]"
                        but_meta="btn-primary cm-search-filters-save-view"
                        method="GET"
                        but_id="`$form_id`_search_filters_save_view"
                    }
                {/if}
            </div>
        </div>
    {/capture}
    {include file="common/popupbox.tpl"
        id="`$form_id`_search_save"
        text=__("save_this_search_as")
        content=$smarty.capture.search_save
        link_class="cm-dialog-auto-size hidden cm-search-filters-search-save"
        title=__("save_this_search_as")
        act="general"
        link_text=__("save_this_search_as")
        meta="hidden"
    }
{/if}