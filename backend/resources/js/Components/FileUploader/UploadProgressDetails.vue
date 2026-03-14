<template>
    <v-snackbar
        class="file-upload-progress-summary"
        :value="show"
        :timeout="timeout"
        auto-height
        bottom
        right
    >
        <v-card width="100%">
            <upload-toolbar
                :expand="expand"
                :confirm-on-close="isCurrentlyUploading"
                @close="$emit('close', { cancel: false })"
                @cancel="$emit('close', { cancel: true })"
                @update:expand="$emit('update:expand', $event)"
            >
                <span v-if="isCurrentlyUploading">
                    {{
                        $t('media.message.uploading-count-items', {
                            count: counts.inprogress,
                        })
                    }}
                </span>

                <span v-else>{{
                    $t('media.message.count-uploads-complete', {
                        count: counts.completed,
                    })
                }}</span>
            </upload-toolbar>

            <v-expand-transition>
                <div
                    v-show="expand"
                    style="overflow-y: auto; max-height: 200px"
                >
                    <v-list class="py-0">
                        <template v-for="file in files">
                            <file-progress
                                :key="file.uniqueIdentifier"
                                :file="file"
                                :can-retry="!isCurrentlyUploading"
                                @retry="$emit('retry', file)"
                            />

                            <v-divider
                                v-if="files.length > 1"
                                :key="file.uniqueIdentifier + 'divider'"
                            />
                        </template>
                    </v-list>
                </div>
            </v-expand-transition>
        </v-card>
    </v-snackbar>
</template>

<script>
import FileProgress from './FileProgress.vue'
import UploadToolbar from './UploadToolbar.vue'

export default {
    components: {
        UploadToolbar,
        FileProgress,
    },

    filters: {
        completed(files) {
            return files.filter((f) => f.isComplete && !f.isFail)
        },

        inprogress(files) {
            return files.filter((f) => !f.isComplete)
        },
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },

        files: {
            type: Array,
            default: () => [],
        },

        expand: {
            type: Boolean,
            required: true,
        },

        timeout: {
            type: Number,
            required: false,
            default: 0,
        },
    },

    computed: {
        counts() {
            const { filters } = this.$options

            return {
                completed: filters.completed(this.files).length,
                inprogress: filters.inprogress(this.files).length,
            }
        },

        isCurrentlyUploading() {
            return this.files.some((f) => f.isUploading)
        },
    },
}
</script>

<style lang="scss">
.file-upload-progress-summary {
    .v-snack__wrapper > .v-snack__content {
        height: auto;
        padding: 0px;
        width: 430px;
    }
}
</style>
