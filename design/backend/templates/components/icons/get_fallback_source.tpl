{capture name="get_fallback_source"}{strip}
{*
    design/backend/templates/common/icon.tpl
    ---

    $class                  string                      Icon class (with fallback to source)
    $source                 string                      Icon name, path or "<svg>...</svg>" source

    Example #4: deprecated. Use "class" props
    ---
    {include_ext file="common/icon.tpl" class="icon-warning-sign"}
*}

{$icon_prefix = "icon-"}
{$icon_prefix_length = $icon_prefix|strlen}
{$config = \Tygh\Registry::get('config')}
{$design_backend = $config.dir.design_backend|fn_get_rel_dir}

{* Get the source of icon *}
{if $class}
    {$class_array = " "|explode:$class}
    {foreach $class_array as $class_index => $class_item}
        {$icon_path = "icons/icon-`$class_item|replace:'_':'-'`.svg"}
        {* Extract a source from a class and remove it from the class *}
        {if $class_item|substr:0:$icon_prefix_length === $icon_prefix}
            {$class_array = $class_array|unset_key:$class_index}
            {$source = $class_item|substr:$icon_prefix_length|replace:"-":"_"}
        {* Extract icon source from class and remove it from the class *}
        {else if !$class_item|strpos:"-" && "`$design_backend`templates/`$icon_path`"|file_exists}
            {$class_array = $class_array|unset_key:$class_index}
            {$source = $class_item}
        {/if}
        {if $class_item@last}
            {$class = " "|implode:($class_array)}
        {/if}
    {/foreach}
{/if}

{* Source with "icon-" prefix *}
{if $source && ($source|substr:0:$icon_prefix_length) === $icon_prefix}
    {$source = $source|substr:$icon_prefix_length}
{/if}

{* Export *}
{$source = $source scope=parent}
{$class = $class scope=parent}
{/strip}{/capture}