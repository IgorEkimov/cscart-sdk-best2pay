{strip}
{*
    $show_analytics_section
    $analytics_data
    $order_statuses
    $orders

    $analytics_card
*}

{$show_analytics_section = $show_analytics_section|default:true}

{if $show_analytics_section}
    <section class="analytics-section">
        <h2 class="analytics-section__title">
            {__("dashboard.analytics_section_title")}
        </h2>
        <div class="analytics-section__content">
            {if $dashboard_blocks}
                {foreach $dashboard_blocks as $section_id => $blocks}
                    <div class="analytics-section__column analytics-section__column--{$section_id}">
                        {foreach $blocks as $block}
                            {include file="views/index/components/analytics_section/analytics_card/analytics_card.tpl" analytics_card=$block}
                        {/foreach}
                    </div>
                {/foreach}
            {else}
                <div class="analytics-section__no-items no-items">
                    {__("no_data")}
                </div>
            {/if}
        </div>
    </section>
{/if}
{/strip}