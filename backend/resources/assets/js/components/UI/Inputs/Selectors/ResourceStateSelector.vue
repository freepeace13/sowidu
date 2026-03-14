<template>
    <v-menu offset-y nudge-bottom="5">
        <template v-slot:activator="{ on }">
            <v-btn
                :disabled="disabled"
                class="ma-0"
                :color="selected.color"
                v-on="on"
                v-bind="$attrs"
                small
            >
                {{ selected.title }}
                <v-icon size="15">arrow_drop_down</v-icon>
            </v-btn>
        </template>

        <v-list class="grey darken-3" dense>
            <v-list-tile
                v-for="state in items"
                :key="state.key"
                :class="{ 'grey darken-2': state.equals(selected) }"
                @click="selected = state"
            >
                <v-list-tile-avatar>
                    <v-icon :color="state.color">fiber_manual_record</v-icon>
                </v-list-tile-avatar>
                <v-list-tile-title>
                    {{ state.title }}
                </v-list-tile-title>
            </v-list-tile>
        </v-list>
    </v-menu>
</template>

<script>
import ModelState from '~/services/models/fundamentals/state';

export default {
    name: 'ResourceStateSelector',

    props: {
        disabled: {
            type: Boolean,
            default: false
        },

        items: {
            type: Array,
            default: () => ([]),
            validtaor(prop) {
                return prop.every((v) => v instanceof ModelState);
            }
        },

        value: {
            type: String,
            required: true
        }
    },

    computed: {
        states() {
            return Order.states('all.state');
        },

        selected: {
            get() { return this.items.find((v) => v.equals(this.value)); },
            set(state) {
                this.$emit('input', state.key);
                this.$emit('change', state.key);
            }
        }
    }
}
</script>