{if $context_search}{strip}
    {*
        Import
        ---
        $context_search

        Local
        ---
        $label
        $form_id
        $name
    *}
    {$label = $context_search.label|default:__("search")}
    {$form_id = $context_search.form_id|default:"search_form"}
    {$name = $context_search.name|default:$context_search.id}

    <div class="context-search">
        <label class="context-search__label input-prepend">
            <input type="search" {""}
                name="{$name}" {""}
                id="{$context_search.id}" {""}
                form="{$form_id}" {""}
                class="input-fill context-search__input" {""}
                {if $context_search.value}value="{$context_search.value}" {""}{/if}
                placeholder="{$label}"
            />
            <span class="add-on context-search__addon">
                {include_ext file="common/icon.tpl" source="search"}
            </span>
        </label>
    </div>
{/strip}{/if}