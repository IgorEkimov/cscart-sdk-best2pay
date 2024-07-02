<div class="control-group">
	{if fn_best2pay_order_can_be_complete($order_info)}
		<a class="btn" href="{"best2pay.complete?order_id=`$order_info.order_id`"|fn_url}" data-ca-dialog-title="Complete">{__("best2pay.complete")}</a>
	{/if}

	{if fn_best2pay_order_can_be_refund($order_info)}
		<a class="btn" href="{"best2pay.refund?order_id=`$order_info.order_id`"|fn_url}" data-ca-dialog-title="Refund">{__("best2pay.refund")}</a>
	{/if}
</div>