{$have_amount_filter=0}
{foreach $filter_items as $ff}
    {if $ff.field_type eq "A"}
        {$have_amount_filter=1}
    {/if}
{/foreach}

{* Export *}
{$have_amount_filter = $have_amount_filter scope=parent}