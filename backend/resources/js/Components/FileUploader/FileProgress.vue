<template>
    <v-list-tile avatar>
        <v-list-tile-avatar>
            <v-icon color="red">{{ icon }}</v-icon>
        </v-list-tile-avatar>

        <v-list-tile-content>
            <v-list-tile-title>{{ file.fileName }}</v-list-tile-title>
        </v-list-tile-content>

        <v-list-tile-action>
            <template v-if="file.isUploading">
                <v-progress-circular
                    :size="18"
                    :rotate="360"
                    :width="3"
                    :value="file.progress"
                    :indeterminate="!file.progress"
                />
            </template>

            <template v-else-if="file.isComplete && !file.isFail">
                <v-icon color="success darken-2">check_circle</v-icon>
            </template>

            <template v-else-if="file.isComplete && file.isFail">
                <v-btn
                    color="error"
                    class="pa-0"
                    small
                    flat
                    icon
                    :disabled="!canRetry"
                    @click="$emit('retry')"
                >
                    <v-icon>replay</v-icon>
                </v-btn>
            </template>
        </v-list-tile-action>
    </v-list-tile>
</template>

<script>
export default {
    props: {
        file: {
            type: Object,
            required: true,
            default: () => ({}),
        },

        canRetry: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        icon() {
            const lookups = {
                picture_as_pdf: ['application/pdf'],
                image: ['image/jpeg', 'image/png'],
                movie: ['video/mp4'],
            }

            let mappedIcon

            for (let [icon, mimes] of Object.entries(lookups)) {
                if (mimes.some((item) => item == this.file.type)) {
                    mappedIcon = icon
                }
            }

            return mappedIcon
        },
    },
}
</script>
