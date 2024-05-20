{if $show_marking_interface && $show_marking_codes_notification}
    {$final_success_status = $order_info.payment_method.processor_params.final_success_status}
    {$statuses = $smarty.const.STATUSES_ORDER|fn_get_simple_statuses}
    <div class="alert alert-block">
        <p>{__("rus_taxes.enter_marking_codes_notice", [$marked_products_count, "[status]" => $statuses.$final_success_status])}</p>
        <p>
            {if true}
                {include file="common/popupbox.tpl"
                    id="marking_codes"
                    text="{__("marking_codes")}: {__("order")} &lrm;#`$order_info.order_id`"
                    href="digital_marking.marking_codes?order_id={$order_info.order_id}"
                    link_text=__("rus_taxes.enter_marking_codes_button")
                    link_class="btn cm-dialog-destroy-on-close"
                    act="link"
                }
            {/if}
        </p>
    </div>
{/if}
