import NotificationOverview from '../components/NotificationOverview';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

const EnhancedNotificationOverview = {
    extends: NotificationOverview,
    mixins: [DispatchWhenTokenChanges('notification/all')]
}

export default {
    render(createElement) {
        return createElement(EnhancedNotificationOverview, {
            on: { ...this.$listeners },
            props: {
                ...this.$attrs,
                iconColor: 'white',
                unreadNotifications: this.$notification.items.filter((v) => v.isUnread)
            }
        })
    }
}