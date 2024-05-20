{strip}
{include file="components/icons/get_fallback_source.tpl"
    class=$class
    source=$source
}
{$show_icon = ($show_icon === false) ? false : $show_icon|default:true}
{/strip}{if $show_icon && $source}{capture name="icon_process"}{strip}
{*
    $source                 string                      Icon name, path or "<svg>...</svg>" source
    $tone                   enum (see $available_tones) Icon tone
    $color                  string                      Custom icon color
    $accessibility_label    string                      Accessibility label
    $show_icon              bool                        Show icon
    $class                  string                      Icon class
    $id                     string                      Icon unique ID
    $render                 enum (inline/img_data)      Render icon as <svg> or <img> tags.
    $data                   array                       List of data attributes
    $title                  string                      Deprecated: icon title
    $icon_text              string                      Deprecated: accessibility label

    Example #1: default
    ---
    {include_ext file="common/icon.tpl" source="warning_sign"}

    Example #2: with params
    ---
    {include_ext file="common/icon.tpl"
        source="warning_sign"
        tone="warning"
        color="#f00"
        accessibility_label="No user"
        show_icon=$is_show_user_require_warning_icon
        class="user-require-warning"
        id="user_warning_icon"
        render="inline"
        data=[
            "data-ca-param-1" => "value_1",
            "data-ca-param-2" => "value_2"
        ]
    }

    Example #3: custom icon (icons should fit in a 20x20 pixel viewBox))
    ---
    {include_ext file="common/icon.tpl" source="addons/my_changes/icons/my_icon.svg"}
    ---
    <svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="m0 0h20v20h-20z"/></svg>
*}
{$accessibility_label = $accessibility_label|default:$icon_text}
{$render = $render|default:"inline"}
{$icon_path = ""}
{$type = ""}
{$svg_open_tag = "<svg "}
{$svg_close_tag = "</svg>"}
{$addons_path = "addons/"}
{$available_tones = [
    "base", "muted", "warning", "error", "interactive", "info", "success",
    "primary", "text_warning", "text_error", "text_info", "text_success"
]}
{$tone = ($tone|in_array:$available_tones) ? $tone : false}
{$is_source_is_type = (!($source|strstr:$svg_open_tag) || !($source|strstr:$svg_close_tag))}

{$config = \Tygh\Registry::get('config')}
{$design_backend = $config.dir.design_backend|fn_get_rel_dir}

{if $is_source_is_type}
    {$type = $source}

    {* Use add-on or core icon path *}
    {$icon_path = ($type|substr:0:($addons_path|count_characters:true) === $addons_path) ? $type : "icons/`$type`.svg"}

    {if "`$design_backend`templates/`$icon_path`"|file_exists}
        {* Assign the icon file to variable *}
        {include_ext file="`$design_backend`templates/`$icon_path`" assign="source"}
    {else}
        {$show_icon = false}
    {/if}
{/if}

{if $render !== "img_data"}
    {* Remove svg meta attributes *}
    {$source = $source|replace:" xmlns=\"http://www.w3.org/2000/svg\"":""}

    {* Add class and html attributes *}
    {$source = $source|replace:"<svg ":"<svg class=\"cs-icon__svg\" focusable=\"false\" aria-hidden=\"true\" "}

    {* Add colors *}
    {if $color}
        {$source = $source|replace:"<svg ":"<svg fill=\"`$color`\" "}
    {elseif $color !== false}
        {* Default: inherit the color from the parent's "color" CSS property *}
        {$source = $source|replace:"<svg ":"<svg fill=\"currentColor\" "}
    {/if}
{/if}

{/strip}{/capture}{strip}{if $show_icon}<span {""}
    class="cs-icon
        {if $type} cs-icon--type-{$type|replace:"_":"-"}{/if}
        {if $tone} cs-icon--tone-{$tone|replace:"_":"-"}{/if}
        {if $tone || $color} cs-icon--apply-color{/if}
        {if $class} {$class}{/if}" {""}
    {if $id}
        id="{$id}" {""}
    {/if}
    {if $title}
        title="{$title}" {""}
    {/if}
    {if $data}
        {foreach $data as $data_name => $data_value}
            {$data_name}="{$data_value}" {""}
        {/foreach}
    {/if}
>
    <span class="cs-icon__hidden-accessible">{if $accessibility_label}{$accessibility_label nofilter}{/if}</span>
    {if $render === "img_data"}
        {* Render icon as <img src="data:..."> tag *}
        <img class="cs-icon__img" src="data:image/svg+xml;utf8,{$source|replace:"\"":"'" nofilter}" alt="" aria-hidden="true">
    {else}
        {* Render icon as <svg> tag *}
        {$source nofilter}
    {/if}
</span>
{/strip}{/if}{/if}