<!--quick search-->
<div class="search nav__search">
    {hook name="index:global_search"}
        <form id="global_search" method="get" action="{""|fn_url}" class="search__form" data-ca-search-menu="form">
            <input type="hidden" name="dispatch" value="search.results" />
            <input type="hidden" name="compact" value="Y" />
            <label for="gs_text" class="search__group">
                <input type="text"
                    class="cm-autocomplete-off search__input"
                        id="gs_text"
                        name="q"
                        placeholder="{__("admin_search_general")}"
                        value="{$smarty.request.q}"
                        data-ca-search-menu="input"
                    />
                <button class="btn search__button" type="submit" id="search_button"></button>
            </label>
        </form>
    {/hook}

</div>
<!--end quick search-->