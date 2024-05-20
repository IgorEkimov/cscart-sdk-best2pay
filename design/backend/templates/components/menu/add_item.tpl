{strip}
{if $runtime.customization_mode.block_manager && $location_data.is_frontend_editing_allowed}
{*
    $menu_name
    $id
    $has_subitems

    $popup_title
*}
    {$has_subitems = $has_subitems|default:false}
    {$is_subitem = $is_subitem|default:false}
    {$level = $level|default:1}
    {$extra_params = $extra_params|default:[]}
    {$extra_params_query = ""}
    {foreach $extra_params as $key=>$value}
        {$temp = "$key=$value"}
        {$extra_params_query = "$extra_params_query&$temp"}
    {/foreach}

    <div class="accordion-group main-menu-{$level}__item bm-block-manager__block-add" data-ca-sortable-is-active="0">
        <div>
            {if $is_subitem}
                {$popup_title = "{__("admin_menu.add_item_for_menu_title")}: `$menu_name`"}
            {else}
                {$popup_title = __("admin_menu.add_item_title")}
            {/if}

            {include file="common/popupbox.tpl"
                act="edit"
                text=$popup_title
                link_text="{__("admin_menu.add_item")}..."
                href="block_manager.update_custom_block?object_type=menu_item&block_id=0&return_url=`$current_dispatch``$extra_params_query`"
                content=""
                id="`$id`_second_level_add"
                link_class="main-menu-`$level`__link"
                icon="icon-plus"
                no_icon_link=true
            }
        </div>
    </div>
{/if}
{/strip}