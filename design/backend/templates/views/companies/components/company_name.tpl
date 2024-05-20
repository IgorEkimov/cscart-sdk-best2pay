{$type = $type|default:"default"}
{$show_company_name = false}
{hook name="companies:company_name"}
{if !$runtime.simple_ultimate && ($object.company_id || $object.company_name)}
    {if !$object.company_name}
        {$_company_name = $object.company_id|fn_get_company_name}
    {/if}

    {if $show_hidden_input}
        {$show_company_name = true}
        <input type="hidden" id="company_id_{$object.product_id}" value="{$object.company_id}" />
        <input type="hidden" id="company_name_{$object.product_id}" value="{$object.company_name|default:$_company_name}" />
    {/if}

    {if $auth.user_type !== "UserTypes::VENDOR"|enum}
        {$show_company_name = true}
        {if $type === "basic"}
            <span class="company-name {$class}">{$object.company_name|default:$_company_name}</span>
        {else if $type === "simple" || $simple}
            <small class="muted company-name {$class}">{$object.company_name|default:$_company_name}</small>
        {else}
            {* default *}
            <p class="muted company-name {$class}"><small>{$object.company_name|default:$_company_name}</small></p>
        {/if}
    {/if}
{/if}
{/hook}
{* Export *}
{$show_company_name = $show_company_name scope=parent}