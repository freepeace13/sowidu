/** @flow */

import Queue from '~/support/wrappers/Queue';
import QueueMaxRetryExceeded from '~/exceptions/QueueMaxRetryExceeded';
import * as types from './constants';
import { isFunction } from '~/support/helpers';

import {
    getQueueIndex,
    getRejectedQueueIndex,
    removeQueue,
    removeRejectedQueue
} from './utils';

export default {
    namespaced: true,

    state: {
        queue: [],
        rejected: [],
        lastQueueProcess: null
    },

    getters: {
        has(state: Object) {
            return (key: string) => {
                return state.queue.some((v) => v.key === key);
            }
        },

        hasRejected(state: Object) {
            return (key: string) => {
                return state.queue.some((v) => v.key === key);
            }
        }
    },

    actions: {
        add({ commit, getters }: Object, queue: Queue): void {
            const { has, hasRejected } = getters;

            if (!has(queue.key) && !hasRejected(queue.key)) {
                commit(types.ADD_QUEUE, queue);
            }
        },

        process({ commit, dispatch }: Object, queue: Queue): Promise<any> {
            return new Promise(async (resolve, reject) => {
                if (! isFunction(queue.action)) {
                    commit(types.REJECT_QUEUE, queue);
                    resolve();
                }

                try {
                    await queue
                        .incrementProcess()
                        .action();

                    commit(types.PROCESS_DONE, queue);

                    resolve();
                } catch (error) {
                    commit(types.PROCESS_DONE, queue);
                    commit(types.ADD_QUEUE, queue);

                    reject(error);
                }
            });
        }
    },

    mutations: {
        [types.ADD_QUEUE] (state: Object, queue: Queue) {
            if (getQueueIndex(state, queue) === -1) {
                state.queue.push(queue);
            }
        },

        [types.REJECT_QUEUE] (state: Object, queue: Queue) {
            removeQueue(state, queue);

            if (getRejectedQueueIndex(state, queue) === -1) {
                state.rejected.push(queue);
            }
        },

        [types.PROCESS_DONE] (state: Object, queue: Queue) {
            this.lastQueueProcess = new Date;
            removeQueue(state, queue);
        },
    }
}