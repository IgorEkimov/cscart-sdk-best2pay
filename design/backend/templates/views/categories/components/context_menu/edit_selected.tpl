{*
    $data   array Data from context_menu schema
    $params array Сontext menu component parameters
*}

<li class="btn bulk-edit__btn bulk-edit__btn--edit-categories mobile-hide">
    <span class="bulk-edit__btn-content bulk-edit__btn-content--no-padding">
        {btn type="dialog"
            class="cm-process-items bulk-edit__link"
            text=__("edit_selected")
            target_id="content_select_fields_to_edit"
            form="category_tree_form"
            data=["data-ca-bulkedit-disable-save-button" => true]
        }
    </span>
</li>
