/** @flow */

import { Queue } from '~/support/wrappers';

export default () => ({
    methods: {
        dispatchQueue(handler: Function) {
            this.$store.dispatch('queue/add', Queue.action(handler));
        }
    }
})