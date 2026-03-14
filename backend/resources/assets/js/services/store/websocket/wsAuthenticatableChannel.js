/** @flow */

import Vuex from 'vuex';
import { resolveFromRaw } from '~/services/models';
import Events from './events';
import createListeners from './listeners';

export default (Echo: Object, store: Vuex) => {
    if (! Echo) throw new Error('Laravel echo client is not initialized.');

    const { getters, commit } = store;

    const Listeners: Object = createListeners(store);
    const profile: Authenticatable = getters['auth/profile']();

    const channel = Echo.private(`${profile.entity}.${profile.id}`)
        .stopListening(Events.Contact.RelationshipUpdate)
        .listen(Events.Contact.RelationshipUpdate, Listeners.ContactUpdate)

        .stopListening(Events.Product.ProductUpdate)
        .listen(Events.Product.ProductUpdate, Listeners.ProductUpdate)

        .stopListening(Events.Media.MediaUpdate)
        .listen(Events.Media.MediaUpdate, Listeners.MediaUpdate)

        .stopListening(Events.Delivery.DeliveryUpdate)
        .listen(Events.Delivery.DeliveryUpdate, Listeners.DeliveryUpdate)

        .stopListening(Events.Invitation.InvitationSent)
        .listen(Events.Invitation.InvitationSent, Listeners.InvitationUpdate)
        
        .stopListening(Events.Order.ProgressUpdated)
        .listen(Events.Order.ProgressUpdated, Listeners.UpdateOrderProgress)
        
        .stopListening(Events.Employment.PushedEmployee)
        .listen(Events.Employment.PushedEmployee, Listeners.InsertEmployee);
}