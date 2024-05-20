{* Imports *}

{* Constants *}
{$LABEL_LENGTH = 2}

{* Icon size *}
{$icon_width = 60}

{* Image attributes *}
{$image_dir_path = "`$images_dir`/addons/`$package.id`"}
{$icon_class = "packages-package-icon__image packages-package-icon__image--medium"}

{capture name="icon"}
    {if $package.icon_path}
        <picture>
            <img src="{$images_dir}/{$package.icon_path}"
                width="{$icon_width}"
                height="{$icon_height}"
                class="{$icon_class}"
            />
        </picture>
    {elseif $type == "core" || $type == "hotfix"}
        <picture>
            <img src="{$images_dir}/cart_logo_short.svg"
                width="{$icon_width}"
                height="{$icon_height}"
                class="{$icon_class}"
            />
        </picture>
    {elseif $package.has_icon}
        <picture>
            {if $package.has_svg_icon}
                <source srcset="{$image_dir_path}/icon.svg" type="image/svg+xml">
            {/if}
            {if $package.has_avif_icon}
                <source srcset="{$image_dir_path}/icon.avif" type="image/avif">
            {/if}
            {if $package.has_webp_icon}
                <source srcset="{$image_dir_path}/icon.webp" type="image/webp">
            {/if}
            <source srcset="{$image_dir_path}/icon.png" type="image/png"> 
            <img src="{$image_dir_path}/icon.png"
                width="{$icon_width}"
                height="{$icon_width}"
                class="{$icon_class}"
            />
        </picture>
    {else}
        <div class="{$icon_class} packages-package-icon__image--label">
            {$package.name|upper|truncate:$LABEL_LENGTH:""}
        </div>
    {/if}
{/capture}

<div class="packages-package-icon__wrapper packages-package-icon__wrapper--medium
    {if $package.status}packages-package-icon__wrapper--{$package.status|lower}{/if}"
>
    {$smarty.capture.icon nofilter}
</div>
