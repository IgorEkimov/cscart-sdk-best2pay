{if $show_marking_interface && $marked_products && $oi.item_id|in_array:($marked_products|array_keys)}
    {btn type="dialog"
        id="opener_marking_codes"
        target_id="content_marking_codes"
        title="{__("marking_codes")}: {__("order")} #`$order_info.order_id`"
        href="digital_marking.marking_codes?order_id={$oi.order_id}&order_item_id={$oi.item_id}"
        text=__("marking_codes")
        class="cm-dialog-destroy-on-close"
        data=[
            "data-ca-scroll" => "#order_marked_item_`$oi.item_id`"
        ]
    }
{/if}