<template>
    <div class="prompt-boundary">
        <slot></slot>
    </div>
</template>

<script>
import { delay } from 'lodash';
import { mapState } from 'vuex';
import {
    PREPEND_PROMPT,
    REMOVE_PROMPT,
    CLEAR_PROMPTS
} from '~/services/store/modules/ui/modules/prompt/constants';

const MUTATION_TYPES = [
    `ui/prompt/${PREPEND_PROMPT}`,
    `ui/prompt/${REMOVE_PROMPT}`,
    `ui/prompt/${CLEAR_PROMPTS}`
];

// after 10 seconds start showing prompts
const DELAY_DURATION = 10000;

export default {
    data: () => ({
        showing: false
    }),

    computed: {
        ...mapState({
            prompts: (state) => state.ui.prompt.prompts
        }),

        currentPrompt() {
            if (this.prompts.length) {
                return this.prompts[this.prompts.length - 1];
            }

            return null;
        }
    },

    methods: {
        showPrompt() {
            if (this.showing || this.currentPrompt === null) return;

            this.showing = true;

            this.$modal.show({
                fullscreen: true,
                key: this.currentPrompt.id,
                modal: this.currentPrompt.component,
                listeners: {
                    afterClose: (modalId) => {
                        this.$store.dispatch('ui/prompt/close', modalId);
                        this.showing = false;
                    }
                }
            });
        },

        registerPrompts() {
            if (this.$store.getters['address/promptAddress']) {
                this.$store.dispatch(
                    'ui/prompt/create',
                    require('~/components/prompt/BillingAddress').default
                );
            }
        }
    },

    mounted() {
        this.$store.subscribe((mutation) => {
            if (MUTATION_TYPES.indexOf(mutation.type) !== -1) {
                this.showPrompt();
            }
        });

        // Call register prompts after 10 seconds once app loaded.
        delay(this.registerPrompts, DELAY_DURATION);
    }
}
</script>