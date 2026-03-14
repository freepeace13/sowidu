/** @flow */

import Vuex from 'vuex';
import { Company, Employee } from '~/services/models';
import { isNullOrUndefined } from '~/support/helpers';
import Events from './events';
import createListeners from './listeners';

const notifiable = (profile: Authenticatable) => {
    if (profile instanceof Company) {
        return Employee.create({ id: profile.authEmployeeId });
    }

    return profile;
}

export default (Echo: Object, store: Vuex) => {
    if (! Echo) throw new Error('Laravel echo client is not initialized.');

    const Listeners: Object = createListeners(store);
    const model: Authorizable = notifiable(store.getters['auth/profile']());

    const channel: Object = Echo.private(`${model.entity}.${model.id}`)
        .stopListening(Events.Task.TaskUpdate)
        .listen(Events.Task.TaskUpdate, Listeners.TaskUpdate);
}