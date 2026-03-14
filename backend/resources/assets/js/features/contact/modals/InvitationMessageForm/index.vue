<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-container grid-list-lg fluid>
            <TextAreaField
                placeholder="Tell something..."
                rows="15"
                v-model="value"
            />
        </v-container>

        <template v-slot:actions>
            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">
                Cancel
            </v-btn>
            <v-btn color="primary" block @click="proceed">
                Proceed
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import { isNullOrUndefined, isString } from '~/support/helpers';

export default {
    name: 'NoteFormModal',

    props: {
        onProceed: {
            type: Function,
            required: true
        },

        message: {
            default: null,
            validator(prop) {
                return isNullOrUndefined(prop) || isString(prop);
            }
        }
    },

    data: () => ({
        value: null
    }),

    methods: {
        proceed() {
            this.onProceed(this.value);
            this.$modal.close(this.$vnode.key);
        }
    },

    created() {
        if (this.message) {
            this.value = this.message;
        }
    }
}
</script>