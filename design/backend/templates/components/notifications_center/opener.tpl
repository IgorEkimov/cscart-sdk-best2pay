<div class="top-bar__btn-wrapper btn-group dropdown-top-menu-item notifications-center__opener-wrapper cm-dropdown-skip-processing">
    <button class="dropdown-toggle dropdown-top-menu-item-link top-bar__btn notifications-center__opener-btn"
        data-toggle="dropdown"
        type="button"
        title="{__("notifications_center.notifications")}"
    >
        <span class="top-bar__btn-inner notifications-center__opener-btn-inner">
            <span>
                {include_ext file="common/icon.tpl" source="bell"}
                <span data-ca-notifications-center-counter></span>
            </span>
        </span>
    </button>
    <ul class="dropdown-menu pull-right notifications-center__root" data-ca-notifications-center-root>
        <div class="cc-dropdown">
            <div class="cc-dropdown__title-wrapper" text="{__("notifications_center.notifications")}">
                <span class="cc-dropdown__title">{__("notifications_center.notifications")}</span>
            </div>
            <div class="cc-all-read">
                <div class="cc-all-read--inner">{__("loading")}</div>
            </div>
        </div>
    </ul>
</div>

<script>
(function (_, $) {
    _.tr({
        loading: '{__("loading")|escape:"javascript"}',
        showMore: '{__("show_more")|escape:"javascript"}',
        showLess: '{__("show_less")|escape:"javascript"}',
        "notifications_center.noData": '{__("notifications_center.no_notifications")|escape:"javascript"}',
        "notifications_center.notifications": '{__("notifications_center.notifications")|escape:"javascript"}',
        "notifications_center.markAllAsRead": '{__("notifications_center.mark_all_as_read")|escape:"javascript"}'
    });

    $.ceEvent('one', 'ce.commoninit', function (context) {
        $(context).find('.notifications-center__opener-wrapper .notifications-center__opener-btn').on('click', function () {
            if (Tygh.ceNotificationsCenterInited) {
                $.ceEvent('trigger', 'ce.notifications_center.enabled');
            }
        });
    });
})(Tygh, Tygh.$);
</script>
