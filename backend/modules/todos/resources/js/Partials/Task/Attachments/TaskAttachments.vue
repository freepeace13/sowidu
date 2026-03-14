<template>
    <v-layout
        column
        justify-start
    >
        <v-flex class="font-weight-bold title mb-1 tw-flex tw-items-center">
            Attachments
            <v-spacer />
            <v-btn
                icon
                small
                @click="(e) => $refs.menuForm.show(e)"
            >
                <v-icon>add</v-icon>
            </v-btn>
        </v-flex>
        <v-layout
            v-if="attachments?.length"
            row
            wrap
            px-1
        >
            <v-flex
                v-for="attachment in attachments"
                :key="attachment.id"
                md2
                xs12
            >
                <TaskAttachment
                    :attachment="attachment"
                    @view="viewAttachment"
                    @click:option="(e) => $refs.taskAttachmentMenu.show(e)"
                />
            </v-flex>
        </v-layout>

        <TaskAttachmentMenu
            ref="taskAttachmentMenu"
            @click:delete="deleteAttachment"
            @click:view="viewAttachment"
        />

        <AttachmentViewer ref="attachmentViewer" />

        <MediaDrawer
            ref="mediaDrawerRef"
            right
            absolute
            width="320"
            clipped
            @attach="(media) => attachMedia(media)"
        />

        <TaskAttachmentFormMenu
            ref="menuForm"
            @attach:from-file="(files) => attachFile(files)"
            @attach:from-media="$refs.mediaDrawerRef.show()"
        />

        <FileUploader
            ref="fileUploader"
            :timeout="1000"
            @success="uploadSuccess"
            @error="(payload) => uploadError(payload)"
        />
    </v-layout>
</template>
<script>
import FileUploader from '@/Components/FileUploader/FileUploader.vue'
import MediaDrawer from '@/Components/Media/MediaDrawer.vue'
import { socketIdHeader } from '@/Composables/useSocketId'
import AttachmentViewer from '~Chatly/Partials/Attachment/Viewer/AttachmentViewer.vue'
import TaskAttachment from './TaskAttachment.vue'
import TaskAttachmentFormMenu from './TaskAttachmentFormMenu.vue'
import TaskAttachmentMenu from './TaskAttachmentMenu.vue'

export default {
    components: {
        TaskAttachment,
        AttachmentViewer,
        TaskAttachmentMenu,
        MediaDrawer,
        TaskAttachmentFormMenu,
        FileUploader,
    },
    props: {
        attachments: {
            required: false,
            type: Array,
            default: () => [],
        },
    },

    mounted() {
        this.$root.$on('task_attachments.refresh', this.fetch)
    },

    beforeDestroy() {
        this.$root.$off('task_attachments.refresh', this.fetch)
    },

    methods: {
        fetch() {
            this.$inertia.reload({
                only: ['attachments'],
                onFinish: () => {
                    this.$root.$emit('task_activities.refresh')
                },
            })
        },

        viewAttachment({ properties }) {
            this.$refs.attachmentViewer.view(properties)
        },

        deleteAttachment(attachment) {
            this.$confirm.ask({
                title: 'Delete',
                question: 'Do you want to delete this attachment?',
                type: 'delete',
                confirm: () => {
                    const { task, board } = this.$page.props
                    this.$inertia.delete(
                        this.$route('todos.boards.tasks.attachments.destroy', {
                            task,
                            board,
                            attachment,
                        }),
                        {
                            ...socketIdHeader,
                            preserveScroll: true,
                            preserveState: true,
                            only: ['errors', 'attachments'],
                        },
                    )
                },
            })
        },

        openMediaDrawer() {
            this.$refs.mediaDrawerRef.show()
        },

        attachMedia({ id }) {
            const { task, board } = this.$page.props
            this.$inertia.post(
                this.$route('todos.boards.tasks.attachments.store', {
                    task,
                    board,
                }),
                {
                    media_id: id,
                },
                {
                    headers: {
                        Accept: 'application/json',
                        ...socketIdHeader,
                    },
                    only: ['errors', 'attachments'],
                    preserveScroll: true,
                    preserveState: true,
                    onFinish: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Finish attaching media.',
                        )
                    },
                },
            )
        },

        attachFile(files) {
            const { task, board } = this.$page.props
            this.$refs.fileUploader.upload(
                this.$route('todos.boards.tasks.attachments.store', {
                    task,
                    board,
                }),
                files,
                {
                    headers: {
                        Accept: 'application/json',
                        ...socketIdHeader,
                    },
                },
            )
        },

        uploadSuccess() {
            try {
                this.$inertia.reload({ only: ['attachments', 'errors'] })
                this.$root.$emit('flash.success', 'Finish uploading file.')
            } catch (error) {
                console.error(error)
            }
        },

        uploadError(payload) {
            try {
                const { errors } = JSON.parse(payload)
                Object.values(errors).forEach((bag) => {
                    bag.forEach((err) => this.$root.$emit('flash.error', err))
                })
            } catch (error) {
                console.error(error)
            }
        },
    },
}
</script>
<style scoped>
.add-task-button {
    height: 80px;
}
</style>
