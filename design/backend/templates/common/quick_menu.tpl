<!-- quick menu -->
{if "tools.show_quick_menu"|fn_check_view_permissions}

<script>
    Tygh.tr('editing_quick_menu_section', '{__("editing_quick_menu_section")|escape:"javascript"}');
    Tygh.tr('new_section', '{__("new_section")|escape:"javascript"}');
    Tygh.tr('editing_quick_menu_link', '{__("editing_quick_menu_link")|escape:"javascript"}');
    Tygh.tr('new_link', '{__("new_link")|escape:"javascript"}');
</script>

<div class="hidden" id="content_quick_menu_edit">
    <div class="quick-menu-container" id="quick_menu">
        <div class="quick-menu quick-menu-show-on-hover">
            <div id="quick_menu_content" class="quick-menu-content cm-popup-box">
                <div class="menu-container table-responsive-wrapper">
                    <div class="table-wrapper">
                        <table width="100%" class="table table-middle">
                            {foreach from=$quick_menu key=sect_id item=sect}
                                <tr data-ca-qm-item="{$sect_id}"
                                    data-ca-qm-parent-id="0"
                                    data-ca-qm-position="{$sect.section.position}">
                                    <td class="section-header">
                                        <strong>
                                            <a class="cm-qm-name cm-update-item wrap link--monochrome" href="#">{$sect.section.name}</a>
                                        </strong>
                                    </td>
                                    <td class="hidden-tools right">
                                        {include file="buttons/button.tpl"
                                            but_role="button-icon"
                                            title=__("remove_this_item")
                                            but_meta="btn cm-delete-section"
                                            but_icon="icon-trash"
                                        }
                                    </td>
                                </tr>
                                {foreach from=$sect.subsection item=subsect}
                                    <tr data-ca-qm-item="{$subsect.menu_id}"
                                        data-ca-qm-parent-id="{$subsect.parent_id}"
                                        data-ca-qm-position="{$subsect.position}">
                                        <td>
                                            <a class="cm-qm-name cm-update-item wrap shift-left link--monochrome"
                                                data-href="{$subsect.url|fn_url}">{$subsect.name}</a>
                                        </td>
                                        <td class="hidden-tools right">
                                            {include file="buttons/button.tpl"
                                                but_role="button-icon"
                                                title=__("remove_this_item")
                                                but_meta="btn cm-delete-section"
                                                but_icon="icon-trash"
                                            }
                                        </td>
                                    </tr>
                                {/foreach}
                                <tr data-ca-qm-item="{$sect_id}" data-ca-qm-parent-id="0"
                                    data-ca-qm-position="{$sect.section.position}">
                                    <td colspan="2">
                                        <a class="edit cm-add-link link--monochrome">
                                            <span class="flex-inline top">
                                                {include_ext file="common/icon.tpl" class="icon-plus"}
                                            </span>
                                            {__("add_link")}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                    </div>
                </div>
                <div class="quick-menu-edit">
                    <a class="btn edit cm-add-section">{__("add_section")}</a>
                </div>
            </div>
        </div>
    <!--quick_menu--></div>
    <div class="buttons-container">
	    <a class="cm-dialog-closer cm-cancel tool-link btn btn-primary">{__("close")}</a>
    </div>
<!--content_quick_menu_edit--></div>

{* Content of editing quick menu link *}
<div id="quick_box" class="hidden quick-menu-popup cm-dialog-auto-size" data-ca-target-id="quick_box">

    <div id="quick_menu_language_selector">
        {include file="common/select_object.tpl"
            style="graphic"
            link_tpl="tools.get_quick_menu_variant"|fn_link_attach:"descr_sl="
            items=$languages
            selected_id=$smarty.const.DESCR_SL
            key_name="name"
            suffix="quick_menu"
            display_icons=true
            select_container_id="quick_menu_language_selector"
        }
    </div>

    <form class="cm-ajax form-horizontal form-edit" name="quick_menu_form" action="{""|fn_url}" method="post">
        <input id="qm_item_id" type="hidden" name="item[id]" value=""/>
        <input id="qm_item_parent" type="hidden" name="item[parent_id]" value="0"/>
        <input id="qm_descr_sl" type="hidden" name="descr_sl" value=""/>
        <input type="hidden" name="result_ids" value="quick_menu"/>

        <div class="control-group">
            <label class="cm-required control-label" for="qm_item_name">{__("name")}:</label>

            <div class="controls">
                <input id="qm_item_name" name="item[name]" type="text" value="" size="40"/>
            </div>
        </div>

        <div class="control-group">
            <label class="cm-required control-label" for="qm_item_link">{__("link")}:</label>

            <div class="controls">
                <input id="qm_item_link" name="item[url]" class="input-fill" type="text" value=""
                        size="40"/>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="qm_item_position">{__("position")}:</label>

            <div class="controls">
                <input id="qm_item_position" name="item[position]" type="text" value="" size="6"/>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <a id="qm_current_link">{__("use_current_link")}</a>
            </div>
        </div>

        <div class="buttons-container">
            {include file="buttons/save_cancel.tpl"
                but_name="dispatch[tools.update_quick_menu_item.edit]"
                cancel_action="close"
                save=true
            }
        </div>

    </form>
</div>
{/if}
<!-- end quick menu -->
