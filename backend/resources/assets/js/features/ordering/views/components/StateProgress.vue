<template>
    <section id="order-progress-states">
        <v-stepper :value="currentStateIndex" class="grey darken-4">
            <v-stepper-header>
                <template v-for="(state, index) in states">
                    <v-stepper-step
                        :step="index + 1"
                        complete-icon="done"
                        :complete="policies.hasCompleted(state.key)"
                        :color="state.color"
                        :key="index"
                    >
                        {{ state.title }}
                    </v-stepper-step>

                    <v-divider
                        v-if="index < states.length - 1"
                        :key="`${state.key}_divider`"
                    />
                </template>
            </v-stepper-header>
        </v-stepper>

        <v-progress-linear
            :value="order.states.state.completionRate"
            class="mt-0"
        />
    </section>
</template>

<script>
import { size } from 'lodash';
import { ProgressState } from '~/services/models/order/states';
import Order from '~/services/models/order';

export default {
    name: 'OrderProgressStepper',

    props: {
        order: {
            type: Order,
            required: true
        }
    },

    computed: {
        states() {
            return (new ProgressState).steps;
        },

        policies() {
            return this.order.policies.states('state');
        },

        currentStateIndex() {
            const index = this.states.findIndex((v) =>
                v.equals(this.order.states.state.current)
            );

            return Math.max(1, index);
        }
    }
}
</script>
