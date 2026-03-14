<template>
    <v-card color="transparent" flat class="mb-3">
        <v-card-text>
            <v-layout row>
                <v-flex shrink>
                    <v-avatar size="36px">
                        <v-img
                            class="elevation-6"
                            :lazy-src="avatar"
                            :src="avatar"
                        />
                    </v-avatar>
                </v-flex>
                <v-flex class="px-3">
                    <TextAreaField
                        append-outer-icon="send"
                        hide-details
                        rows="2"
                        v-model="message"
                        @click:append-outer="postMessage"
                        placeholder="Write a comment..."
                    />
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>

<script>
import * as utils from '~/support/helpers';
import TextAreaField from '~/components/UI/Inputs/TextAreaField';

export default {
    name: 'CommentForm',

    components: {
        TextAreaField
    },

    props: {
        avatar: {
            type: String,
            required: true
        }
    },

    data: () => ({
        message: null
    }),

    methods: {
        postMessage() {
            if (! this.message) return;

            this.$emit('send', this.message);
            this.message = null;
        }
    }
}
</script>