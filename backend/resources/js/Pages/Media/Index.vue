<template>
    <div class="tw-h-full tw-bg-bg-grey">
        <v-toolbar
            color="transparent"
            flat
        >
            <v-toolbar-title
                class="title tw-flex tw-items-center tw-relative tw-pr-8 tw-h-full"
            >
                <v-icon
                    color="red darken-2"
                    :medium="$vuetify.breakpoint.mdAndUp"
                    left
                >
                    perm_media
                </v-icon>
                <span class="md:tw-text-xl tw-text-lg">
                    {{ $t('headings.media-library') }}
                </span>
                <v-chip
                    v-show="totalMediaCount"
                    color="red darken-2 white--text"
                    class="tw-self-start"
                >
                    {{ totalMediaCount }}
                </v-chip>
            </v-toolbar-title>
            <v-spacer />
            <!-- Filter -->
            <MediaFilters
                :media-categories="categories"
                @filter="filterChanged"
            />
            <v-btn
                depressed
                class="tw-min-w-[32px]"
                @click="$inertia.visit($route('media.trash'))"
            >
                <v-icon :left="$vuetify.breakpoint.smAndUp">
                    delete_outline
                </v-icon>
                {{ $vuetify.breakpoint.xs ? '' : $t('headings.trash') }}
            </v-btn>
        </v-toolbar>

        <v-divider />

        <v-container
            class="py-0 !tw-max-w-full"
            grid-list-lg
        >
            <v-subheader>
                {{ $t('labels.files') }}
            </v-subheader>
            <div class="tw-flex tw-flex-row tw-relative tw-h-full">
                <v-layout
                    ref="fileListingContainer"
                    row
                    wrap
                    class="tw-flex tw-items-start tw-content-start"
                >
                    <!-- class="tw-absolute tw-inset-0 tw-overflow-auto" -->
                    <v-flex
                        v-for="media in files"
                        :key="media.uuid"
                        xs12
                        sm6
                        md4
                        lg3
                        xl2
                    >
                        <FileItem
                            :id="`media_file_${media.id}`"
                            :media-id="media.id"
                            :type="media.file_type"
                            :thumbnail="media.conversions.thumbnail"
                            :name="media.file_name"
                            :shared="media.is_shared"
                            :tag-address="media?.address_tag?.full"
                            :uploaded-by="media.owner?.name"
                            :uploaded-at="media.created_at | toDateTimeLocal"
                            :last-modified="
                                media.custom_properties.lastModified
                            "
                            :category="media.category"
                            :orders="media.attached_to_orders"
                            @preview="openFile(media)"
                            @rename="renameFile(media)"
                            @click:more="
                                (e) => $refs.fileMenuList.show(e, media)
                            "
                        />
                    </v-flex>

                    <div
                        v-show="!files.length && filters"
                        class="tw-flex tw-w-full"
                    >
                        <v-alert
                            type="info"
                            :value="true"
                            class="tw-self-start tw-w-full"
                            outline
                        >
                            {{
                                $t('media.message.no-results-on-given-filters')
                            }}
                        </v-alert>
                    </div>

                    <v-flex
                        v-for="skeleton in skeletonCount"
                        :key="`skeleton-${skeleton}`"
                        xs12
                        sm6
                        md4
                        lg3
                        xl2
                    >
                        <FileListingSkeleton
                            v-show="pagination.isLoading"
                            :type="filters?.type"
                        />
                    </v-flex>
                </v-layout>
            </div>

            <FileMenuList
                ref="fileMenuList"
                @click:move="(media) => moveFile(media)"
                @click:open="(media) => openFile(media)"
                @click:rename="(media) => renameFile(media)"
                @click:share="(media) => shareFile(media)"
                @click:add-starred="(media) => addToStarred(media)"
                @click:remove-starred="(media) => removeFromStarred(media)"
                @click:remove="(media) => removeFile(media)"
                @click:download="(media) => downloadFile(media)"
                @click:details="(media) => $refs.details.start(media)"
                @click:send-to="(media) => $refs.sendFileForm.show(media)"
                @click:tag-to-address="
                    (media) =>
                        $refs.tagAddressForm.show({
                            uuid: media.uuid,
                            type: 'media',
                        })
                "
                @click:tag-to-category="
                    (media) => $refs.tagCategoryForm.show(media)
                "
            />

            <RenameMediaForm
                ref="renameMedia"
                @success="renameFileCompleted"
            />

            <ShareFileForm
                ref="shareMedia"
                :permission-types="permissionTypes"
            />

            <RemoveFileConfirmation
                ref="removeMedia"
                @success="removeFileSuccess"
            />

            <!-- <MediaFileDetails
                ref="details"
                :category-types="categoryTypes"
            /> -->

            <FileUploader
                ref="uploadFile"
                @success="uploadFileCompleted"
            />

            <SendFileForm
                ref="sendFileForm"
                title="Send to"
            />
            <Viewer ref="viewer" />

            <UploadFileButton
                :mimes="allowedTypes"
                @upload="uploadFile"
            />
        </v-container>

        <TagAddressForm
            ref="tagAddressForm"
            @refresh-media="(media) => refreshFile(media)"
        />

        <MediaTagCategory
            ref="tagCategoryForm"
            :categories="categories"
            @refresh-media="(media) => refreshFile(media)"
        />
        <ExceededFilesModal
            v-model="showExceededModal"
            :exceeded-files="exceededFiles"
        />
    </div>
</template>

<script>
import FileItem from '@/Features/Media/Components/FileItem.vue'
import TagAddressForm from '@/Apps/Media/Components/TagAddressForm.vue'
import { useInfiniteScroll } from '@vueuse/core'
import axios from 'axios'
import FileUploader from '../../Components/FileUploader/FileUploader.vue'
import RemoveFileConfirmation from '../../Components/Forms/RemoveFileConfirmation.vue'
import RenameMediaForm from '@/Apps/Media/Components/RenameMediaForm.vue'
import SendFileForm from '@/Apps/Media/Components/SendFileForm.vue'
import ShareFileForm from '../../Components/Forms/ShareFileForm.vue'
import MediaFilters from '../../Components/MediaFilters.vue'
import Viewer from '../../Components/Viewer/MediaView.vue'
import FileListingSkeleton from './Partials/FileListingSkeleton.vue'
import FileMenuList from './Partials/FileMenuList.vue'
import ExceededFilesModal from './Partials/ExceededUploadFileModal.vue'
import MediaTagCategory from '@/Apps/Media/Components/MediaTagCategory.vue'
import UploadFileButton from '@/Features/Media/Components/UploadFileButton.vue'
import '@/../css/views/media.css'

export default {
    components: {
        UploadFileButton,
        Viewer,
        FileItem,
        FileUploader,
        RenameMediaForm,
        FileMenuList,
        ShareFileForm,
        RemoveFileConfirmation,
        SendFileForm,
        FileListingSkeleton,
        TagAddressForm,
        MediaFilters,
        MediaTagCategory,
        ExceededFilesModal,
    },

    props: {
        allowedTypes: {
            required: true,
            type: Array,
        },
        permissionTypes: {
            required: true,
            type: Object,
        },
        user: {
            type: Object,
            required: true,
        },
        categories: {
            required: true,
            type: Array,
        },
        totalCount: {
            required: true,
            type: Number,
        },
    },

    data: () => ({
        files: [],
        pagination: {
            current_page: 0,
            next_page_url: null,
            isLoading: true,
        },
        filters: null,
        isShowBadge: true,
        showExceededModal: false,
        exceededFiles: [],
    }),

    computed: {
        totalMediaCount() {
            return this.totalCount
        },

        skeletonCount() {
            if (this.$vuetify.breakpoint.xl) {
                return 12
            } else if (this.$vuetify.breakpoint.lg) {
                return 8
            } else if (this.$vuetify.breakpoint.mdAndUp) {
                return 6
            } else if (this.$vuetify.breakpoint.sm) {
                return 4
            }

            return 2
        },

        countOfItems() {
            if (this.$vuetify.breakpoint.xl) {
                return 24
            } else if (this.$vuetify.breakpoint.lg) {
                return 12
            } else if (this.$vuetify.breakpoint.md) {
                return 6
            } else if (this.$vuetify.breakpoint.sm) {
                return 4
            }

            return 15
        },
        hasExceededFiles() {
            return this.exceededFiles.length > 0 || this.showExceededModal
        },
    },

    mounted() {
        this.attachInfiniteScroll()
        this.fetch()
        this.$root.$on('media.files.refresh', this.fetch)
    },

    beforeDestroy() {
        this.$root.$off('media.files.refresh', this.fetch)
    },

    methods: {
        onAddFiles() {
            this.$refs.uploadSourceList.open()
        },

        removeFromStarred(file) {
            this.$inertia.delete(
                this.$route('media.starred.destroy', { media: file.uuid }),
                {
                    only: ['files'],
                },
            )
        },

        addToStarred(file) {
            this.$inertia.post(
                this.$route('media.starred.store'),
                { media: file.uuid },
                {
                    only: ['files'],
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

        removeFileSuccess(fileId) {
            const index = this.files.findIndex(({ id }) => id == fileId)
            this.$delete(this.files, index)
        },

        shareFile(file) {
            this.$refs.shareMedia.start(file)
        },

        renameFile(file) {
            this.$refs.renameMedia.start(file.uuid)
        },

        renameFileCompleted(media) {
            this.$refs.renameMedia.close()
            this.refreshFile(media)
        },

        openFile(file) {
            if (!file.is_dir) {
                return this.$refs.viewer.view(file.uuid)
            }

            // this.$inertia.visit(
            //     this.$route('media.drive.folders.show', {
            //         folder: file.uuid,
            //     }),
            // )
        },

        uploadFile({ files, source }) {
            const MAX_SIZE_MB = 20
            const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024
            const fileArray = Array.isArray(files) ? files : Array.from(files)

            const exceededNames = []
            const validFiles = []

            fileArray.forEach((file) => {
                if (file.size > MAX_SIZE_BYTES) {
                    exceededNames.push(file.name)
                } else {
                    validFiles.push(file)
                }
            })

            this.$set(this, 'exceededFiles', exceededNames)
            this.$set(this, 'showExceededModal', exceededNames.length > 0)

            if (validFiles.length > 0) {
                const target = this.$route('media.files.upload')
                this.$refs.uploadFile.upload(target, validFiles, {
                    query: { source },
                })
            }
        },

        uploadFileCompleted(payload) {
            const { file } = JSON.parse(payload)
            this.files.unshift(file)
        },

        attachInfiniteScroll() {
            useInfiniteScroll(
                // this.$refs.fileListingContainer,
                window,
                () => {
                    if (
                        this.pagination.isLoading ||
                        !this.pagination.next_page_url
                    )
                        return
                    this.fetch(this.pagination.current_page + 1)
                },
                { distance: 30 },
            )
        },

        filterChanged(filter) {
            this.filters = filter

            this.$nextTick(() => {
                this.fetch(1)
            })
        },

        async fetch(page = 1) {
            try {
                this.pagination.isLoading = true

                const filters = this.filters

                if (page == 1) {
                    this.files = []
                    this.pagination = {
                        current_page: 0,
                        next_page_url: null,
                        isLoading: true,
                    }
                }

                const {
                    data: { data, current_page, next_page_url },
                } = await axios.get(
                    this.$route('media.drive.files.index', {
                        page,
                        ...filters,
                        count: this.countOfItems,
                    }),
                )
                this.files = [...this.files, ...data]
                this.pagination = {
                    current_page,
                    next_page_url,
                    isLoading: false,
                }
            } catch (errors) {
                console.error(errors)
            }
        },

        async refreshFile(media) {
            const {
                data: { file },
            } = await axios.get(this.$route('media.show', { media }))
            const index = this.files.findIndex(({ id }) => id == file.id)
            this.$set(this.files, index, file)
        },
    },
}
</script>
