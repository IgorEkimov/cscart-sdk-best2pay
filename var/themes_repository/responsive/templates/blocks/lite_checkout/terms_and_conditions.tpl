<div class="litecheckout__container">
    {$const_suffix = $suffix|default:""}
    {if !$suffix}
        {$suffix = ""|uniqid}
    {/if}

    <div class="litecheckout__item">
        <div class="litecheckout__terms" id="litecheckout_terms">
            {include file="views/checkout/components/terms_and_conditions.tpl"
                suffix=$suffix
                const_suffix=$const_suffix
            }
        <!--litecheckout_terms--></div>
    </div>
</div>
