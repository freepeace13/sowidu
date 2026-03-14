/** @flow */
import * as types from './constants';
import TaskService from '../api';
import { Task } from '~/services/models';
import Cache from '~/services/cache';

export default {
    namespaced: true,

    state: {
        tasks: []
    },

    actions: {
        async all({ commit, state }: Object): Promise<Array<Task>> {
            const result: Array<Task> = await TaskService.all();
            commit(types.SET_TASKS, result);
            return result;
        }
    },

    mutations: {
        [types.SET_TASKS] (state: Object, payload: Array<Task>): void {
            state.tasks = payload;
        },

        [types.TASK_UPDATE] (state: Object, payload: Task): void {
            state.tasks = Task
                .collection(state.tasks)
                .updateOrInsert(payload)
                .all();
        },

        [types.INSERT_TASK] (state: Object, payload: Task): void {
            state.tasks = Task
                .collection(state.tasks)
                .insert(payload)
                .all();
        }
    }
}
