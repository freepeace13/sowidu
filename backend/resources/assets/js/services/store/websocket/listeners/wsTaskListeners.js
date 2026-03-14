/** @flow */

import Vuex from 'vuex';
import Task from '~/services/models/task';

export default (store: Vuex) => ({
    InsertTask: (event: any) => {
        store.commit('task/INSERT_TASK', Task.create(event));
    },

    TaskUpdate: (event: any) => {
        store.commit('task/TASK_UPDATE', Task.create(event));
    },
});