/** @flow */

import Vuex from 'vuex';

import TaskListeners from './wsTaskListeners';
import ItemListeners from './wsItemListeners';
import OrderListeners from './wsOrderListeners';
import MediaListeners from './wsMediaListeners';
import ContactListeners from './wsContactListeners';
import EmployeeListeners from './wsEmployeeListeners';
import DeliveryListeners from './wsDeliveryListeners';
import InvitationListeners from './wsInvitationListeners';
import NotificationListeners from './wsNotificationListeners';

export default (store: Vuex) => ({
    ...ItemListeners(store),
    ...OrderListeners(store),
    ...TaskListeners(store),
    ...MediaListeners(store),
    ...ContactListeners(store),
    ...EmployeeListeners(store),
    ...DeliveryListeners(store),
    ...InvitationListeners(store),
    ...NotificationListeners(store),
})