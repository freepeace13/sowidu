import Notification from '../models/notification';
import { mutations, actions } from './constants';
import { helpers } from '@libs/core';
import NotificationService from '../service';

export default (options = {}) => ({
    namespaced: true,

    state: {
        isProcessing: false,
        toasts: [],
        notifications: [],
    },

    actions: {
        async [actions.ALL] (context) {
            const result = await NotificationService.all(options);
            
            context.commit(mutations.SET_NOTIFICATIONS, result);
            
            return result;
        },

        async [actions.READ] (context, payload) {
            const id = (payload instanceof Notification)
                ? notification.id
                : payload;

            const result = await NotificationService.read(id, options);

            context.commit(mutations.UPDATE_NOTIFICATION, result);

            return result;
        }
    },

    mutations: {
        [mutations.REMOVE_TOAST] (state, payload) {
            if (state.toasts[payload] instanceof Notification) {
                state.toasts.splice(payload, 1);
            }
        },

        [mutations.SET_NOTIFICATIONS] (state, payload) {
            state.notifications = helpers.arrwrap(payload).map((v) => {
                return Notification.create(v);
            });
        },

        [mutations.INSERT_NOTIFICATION] (state, payload) {
            const notification = Notification.create(payload);
            const collection = Notification.collection(state.notifications);

            if (! collection.search((item) => item.equals(notification))) {
                state.notifications = collection
                    .prepend(notification)
                    .all();

                state.toasts.push(notification);
            }
        },

        [mutations.UPDATE_NOTIFICATION] (state, payload) {
            const notification = Notification.create(payload);
            const collection = Notification.collection(state.notifications);

            const index = collection.search((item) => item.equals(notification));

            if (index !== false) {
                collection.splice(index, 1, [notification]);

                state.notifications = collection.all();
            }
        }
    }
});