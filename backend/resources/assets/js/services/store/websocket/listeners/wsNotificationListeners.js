/** @flow */

import Vuex from 'vuex';
import Notification from '~/services/models/notification';

export default (store: Vuex) => ({
    InsertNotification: (event: any) => {
        const notification = Notification.broadcasts(event);
        store.commit('notification/INSERT_NOTIFICATION', notification);
    },
});