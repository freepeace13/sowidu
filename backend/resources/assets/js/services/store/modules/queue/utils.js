/** @flow */

import Queue from '../../../../support/wrappers/Queue';

export const getQueueIndex = (state: Object, queue: Queue) => {
    return state.queue.findIndex((v) => v.key === queue.key);
}

export const getRejectedQueueIndex = (state: Object, queue: Queue) => {
    return state.rejected.findIndex((v) => v.key === queue.key);
}

export const removeQueue = (state: Object, queue: Queue) => {
    const index = getQueueIndex(state, queue);

    if (index !== -1) {
        state.queue.splice(index, 1);
    }
}

export const removeRejectedQueue = (state: Object, queue: Queue) => {
    const index = getRejectedQueueIndex(state, queue);

    if (index !== -1) {
        state.rejected.splice(index, 1);
    }
}