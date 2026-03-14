/** @flow */

import { clone } from 'lodash';
import Vuex from 'vuex';
import Queue from '../../../support/wrappers/Queue';
import { first, debounce } from 'lodash';
import QueueMaxRetryExceeded from '~/exceptions/QueueMaxRetryExceeded';

type OptionTypes = {
    sleep: number,
    tries: number,
    logger: boolean
}

export let defaultOptions: OptionTypes = {
    sleep: 10, // In Seconds
    tries: 3, // Queue process attempt limit
    logger: true // Enable/Disable logger
}

export default function QueueProcessor(
    options: Object = defaultOptions
): Function {
    // Iterate empty stack limit before put processor into sleep
    let invalidQueueCounter = 0;

    defaultOptions = Object.assign(defaultOptions, options);

    return (store: Vuex) => {
        const createQueuedGenerator = (function* () {
            defaultOptions.logger && console.info(
                `[Queue Processor] Initialized generator...`
            );

            while(true) {
                const { queue } = store.state.queue;
                const current = first([...queue]);

                yield current && Queue.create(current);
            }
        });
        
        let generator = createQueuedGenerator();

        const run = async () => {
            const { value } = generator.next();

            defaultOptions.logger && console.info(
                '[Queue Processor] Processing! ' +
                'Invalid queue attempts (' + invalidQueueCounter + ')...'
            );
            
            /**
             * Putting iterator asleep after 10 executive attempts of
             * nullified queued values to lessen resource consumptions.
             */
            if ((value instanceof Queue) === false) {
                invalidQueueCounter++;

                if (invalidQueueCounter > 10) {
                    invalidQueueCounter = 0;

                    defaultOptions.logger && console.info(
                        '[Queue Processor] Stopped! ' +
                        'Re-run after ' + defaultOptions.sleep + ' seconds...'
                    );
                }

                return debounce(run,
                    !invalidQueueCounter ? defaultOptions.sleep * 1000 : 500
                )();
            }

            try {
                /**
                 * Queue rejected once configured retry limit exceeded
                 * the queue process attempts.
                 */
                //$FlowFixMe
                if (value.exceeded(defaultOptions.tries) || (value.exceeded(0) && value.once)) {
                    store.commit('queue/REJECT_QUEUE', value);
                    //$FlowFixMe
                    throw new QueueMaxRetryExceeded(value.key, defaultOptions.tries);
                }

                await store.dispatch('queue/process', value);
            } catch (error) {
                if (error instanceof QueueMaxRetryExceeded) {
                    defaultOptions.logger && console.error(
                        '[Queue Processor] Rejected!'
                    , error);
                }
            } finally {
                invalidQueueCounter = 0;
                run();
            }
        }

        // Start running the processor...
        run();
    }
}