/** @flow */

export default class QueueMaxRetryExceeded extends Error {
    constructor(key: string, maxRetry: number = 3) {
        super(`Queue type of [${key}] has reached maximum retry process of ${maxRetry}`);
    }
}