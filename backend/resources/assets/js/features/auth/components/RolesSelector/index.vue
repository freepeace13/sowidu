<template>
    <v-menu
        offset-y
        min-width="300"
        :disabled="disabled"
        :close-on-content-click="false"
    >
        <template v-slot:activator="{ on }">
            <v-btn :color="color" :flat="flat" v-on="on" class="px-3">
                {{ placeholder }} <v-icon>arrow_drop_down</v-icon>
            </v-btn>
        </template>

        <v-card min-width="300">
            <v-card-title class="justify-center body-2">
                Change Permissions
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pt-0">
                <v-checkbox
                    v-for="role in items"
                    :key="`checkbox_role_${role.id}`"
                    v-model="selected"
                    :value="role"
                    off-icon=""
                    on-icon="check"
                    hide-details
                    full-width
                >
                    <template v-slot:label>
                        <div class="d-block">{{ role.name }}</div>
                    </template>
                </v-checkbox>
            </v-card-text>
        </v-card>
    </v-menu>
</template>

<script>
import { Role } from '~/services/models/fundamentals';

export default {
    props: {
        color: String,

        disabled: {
            type: Boolean,
            default: false
        },

        flat: {
            type: Boolean,
            default: false
        },

        placeholder: {
            type: String,
            default: 'Select Role'
        },

        value: {
            type: Array,
            default: () => ([]),
            validator(prop) {
                return prop.every((v) => v instanceof Role);
            }
        },

        items: {
            type: Array,
            validator(prop) {
                return prop.every((v) => v instanceof Role);
            }
        }
    },

    computed: {
        selected: {
            get() {
                return this.value;
            },
            set(value) {
                this.$emit('input', value);
            }
        },
    }
}
</script>