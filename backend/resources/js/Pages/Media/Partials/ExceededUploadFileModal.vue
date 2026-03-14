<template>
    <v-dialog
        v-model="visible"
        max-width="500"
        persistent
    >
        <v-card>
            <v-card-title class="headline white--text red lighten-1">
                Exceeded File Size Limit
            </v-card-title>

            <v-card-text class="pt-4">
                <p>
                    The following files exceeded the 20MB size limit and were
                    not uploaded:
                </p>
                <ul
                    v-if="exceededFiles.length"
                    class="tw-list-disc tw-pl-5"
                >
                    <li
                        v-for="(file, index) in exceededFiles"
                        :key="index"
                        class="tw-text-base"
                    >
                        {{ file }}
                    </li>
                </ul>
                <p
                    v-else
                    class="red--text"
                >
                    No files listed.
                </p>
            </v-card-text>

            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="primary"
                    text
                    @click="close"
                >
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'ExceededFilesModal',
    props: {
        exceededFiles: {
            type: Array,
            required: true,
            default: () => [],
        },
        value: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        visible: {
            get() {
                return this.value
            },
            set(value) {
                this.$emit('input', value)
            },
        },
    },
    methods: {
        close() {
            this.visible = false
        },
    },
}
</script>
