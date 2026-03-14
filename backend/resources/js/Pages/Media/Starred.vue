<template>
    <div>
        <v-toolbar
            color="transparent"
            flat
        >
            <v-toolbar-title>{{ $t('headings.starred') }}</v-toolbar-title>
        </v-toolbar>

        <v-divider />

        <v-container
            class="py-0"
            fluid
            grid-list-lg
        >
            <template v-if="$page.props.folders.length">
                <v-subheader>{{ $t('labels.folders') }}</v-subheader>

                <v-layout
                    row
                    wrap
                >
                    <v-flex
                        v-for="folder in $page.props.folders"
                        :key="folder.uuid"
                        xs12
                        sm6
                        md4
                        lg3
                        xl2
                    >
                        <FileItem
                            :id="`starred_folder_${folder.id}`"
                            :media-id="folder.id"
                            type="folder"
                            :shared="folder.members.length > 0"
                            :name="folder.name"
                            @dblclick.native.stop.prevent="openFile(folder)"
                        >
                            <template #menu>
                                <file-menu-list
                                    :media="folder"
                                    @click:open="openFile(folder)"
                                    @click:move="moveFile(folder)"
                                    @click:rename="renameFile(folder)"
                                    @click:share="shareFile(folder)"
                                    @click:remove-starred="
                                        removeFromStarred(folder)
                                    "
                                    @click:remove="removeFile(folder)"
                                    @click:download="downloadFile(folder)"
                                />
                            </template>
                        </FileItem>
                    </v-flex>
                </v-layout>
            </template>

            <template v-if="$page.props.files.length">
                <v-subheader>{{ $t('labels.files') }}</v-subheader>

                <v-layout
                    row
                    wrap
                >
                    <v-flex
                        v-for="media in $page.props.files"
                        :key="media.uuid"
                        xs12
                        sm6
                        md4
                        lg3
                        xl2
                    >
                        <FileListing
                            :id="`starred_file_${media.id}`"
                            :media-id="media.id"
                            :type="media.file_type"
                            :thumbnail="media.conversions.thumbnail"
                            :name="media.file_name"
                            @dblclick.native.stop.prevent="openFile(media)"
                        >
                            <template #menu>
                                <file-menu-list
                                    :media="media"
                                    @click:move="moveFile(media)"
                                    @click:open="openFile(media)"
                                    @click:rename="renameFile(media)"
                                    @click:share="shareFile(media)"
                                    @click:remove-starred="
                                        removeFromStarred(media)
                                    "
                                    @click:remove="removeFile(media)"
                                    @click:download="downloadFile(media)"
                                />
                            </template>
                        </FileListing>
                    </v-flex>
                </v-layout>
            </template>

            <rename-media-form
                ref="renameMedia"
                @success="renameFileCompleted"
            />

            <move-file-form
                ref="moveMedia"
                prefix="starred"
                @success="moveFileCompleted"
            />

            <share-file-form ref="shareMedia" />

            <remove-file-confirmation ref="removeMedia" />

            <media-viewer
                ref="viewer"
                @share="shareFile"
            />
        </v-container>
    </div>
</template>

<script>
import FileItem from '@/Features/Media/Components/FileItem.vue'
import MoveFileForm from '../../Components/Forms/MoveFileForm.vue'
import RemoveFileConfirmation from '../../Components/Forms/RemoveFileConfirmation.vue'
import RenameMediaForm from '../../Apps/Media/Components/RenameMediaForm.vue'
import ShareFileForm from '../../Components/Forms/ShareFileForm.vue'
import MediaViewer from '../../Components/Viewer/MediaView.vue'
import FileMenuList from './Partials/FileMenuList.vue'

/**
 * @todo not used
 */
export default {
    components: {
        FileItem,
        MediaViewer,
        RenameMediaForm,
        MoveFileForm,
        ShareFileForm,
        RemoveFileConfirmation,
        FileMenuList,
    },

    methods: {
        removeFromStarred(file) {
            this.$inertia.delete(
                this.$route('media.starred.destroy', { media: file.uuid }),
                {
                    only: ['files', 'folders'],
                },
            )
        },

        downloadFile(file) {
            location.href = this.$route('media.files.download', {
                media: file.uuid,
            })
        },

        removeFile(file) {
            this.$refs.removeMedia.start(file)
        },

        shareFile(file) {
            this.$refs.shareMedia.start(file)
        },

        moveFile(file) {
            this.$refs.moveMedia.start(file)
        },

        moveFileCompleted() {
            this.$refs.moveMedia.close()
            this.$inertia.reload({ only: ['files', 'folders'] })
        },

        createFolder() {
            const tree = this.$page.props.tree
            const parent = tree.length ? tree[tree.length - 1] : null

            this.$refs.createFolder.start(parent ? parent.uuid : null)
        },

        createFolderCompleted() {
            this.$refs.createFolder.close()
        },

        renameFile(file) {
            console.log(file)
            this.$refs.renameMedia.start(file.uuid)
        },

        renameFileCompleted() {
            this.$inertia.reload({ only: ['files', 'folders'] })
            this.$refs.renameMedia.close()
        },

        openFile(file) {
            if (!file.is_dir) {
                return this.$refs.viewer.view(file)
            }

            this.$inertia.visit(
                this.$route('media.drive.folders.show', {
                    folder: file.uuid,
                }),
            )
        },
    },
}
</script>
