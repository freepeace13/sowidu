<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import OrderLayout from '../OrderLayout.vue'
import JumboUploadButton from './Components/JumboUploadButton.vue'
import FileItem from '@/Features/Media/Components/FileItem.vue'
import MediaView from '@/Components/Viewer/MediaView.vue'
import FileMenuList from '@/Pages/Media/Partials/FileMenuList.vue'
import TagAddressForm from '@/Apps/Media/Components/TagAddressForm.vue'
import MediaTagCategory from '@/Apps/Media/Components/MediaTagCategory.vue'
import SendFileForm from '@/Apps/Media/Components/SendFileForm.vue'
import RenameMediaForm from '@/Apps/Media/Components/RenameMediaForm.vue'
import ShareFileForm from '@/Components/Forms/ShareFileForm.vue'
import OrderListenerMixin from '../Mixins/OrderListenerMixin'
import AddMediaForm from './Components/AddMediaForm.vue'
import MediaFilters from '@/Components/MediaFilters.vue'
import { useInfiniteScroll } from '@vueuse/core'
import FileListingSkeleton from '@/Pages/Media/Partials/FileListingSkeleton.vue'

export default {
    components: {
        JumboUploadButton,
        MediaView,
        FileItem,
        FileMenuList,
        TagAddressForm,
        MediaTagCategory,
        SendFileForm,
        RenameMediaForm,
        ShareFileForm,
        AddMediaForm,
        MediaFilters,
        FileListingSkeleton,
    },

    mixins: [OrderListenerMixin],

    layout: [AuthLayout, OrderLayout],

    props: {
        order: {
            required: true,
            type: Object,
        },
        medias: {
            required: true,
            type: Array,
            default: () => [],
        },
        allowedTypes: {
            required: true,
            type: Array,
        },
        categories: {
            required: false,
            type: Array,
            default: () => [],
        },
        permissionTypes: {
            required: true,
            type: Object,
        },
        paginator: {
            required: true,
            type: Object,
        },
        filters: {
            required: false,
            type: [Object, Array],
            default: () => ({}),
        },
    },

    data: (vm) => ({
        items: [],
        initialUrl: vm.$page.url,
        isLoading: true,
    }),

    mounted() {
        this.items = this.medias

        this.$inertia.reload({ only: ['categories', 'medias'] })
        if (!this.paginator.has_more_pages) {
            this.isLoading = false
        }
        useInfiniteScroll(
            window,
            () => {
                if (!this.paginator.has_more_pages) {
                    this.isLoading = false
                    return
                }

                this.fetch({ page: this.paginator.next_page })
            },
            { distance: 10 },
        )
    },

    methods: {
        fetch({ page = 1, filters = {} }) {
            this.isLoading = true

            if (page === 1) {
                this.items = []
            }

            this.$inertia.reload({
                only: ['medias', 'paginator'],
                data: {
                    page,
                    ...filters,
                },
                onSuccess: ({ props: { medias } }) => {
                    this.items = this.items.concat(medias)
                    window.history.replaceState(
                        {},
                        this.$page.title,
                        this.initialUrl,
                    )
                },
                onStart: () => {
                    this.isLoading = true
                },
                onFinish: () => {
                    this.isLoading = false
                },
            })
        },

        filterChanged(filters) {
            const cleanedFilters = Object.entries(filters).reduce(
                (acc, [key, value]) => {
                    if ((value !== null && value !== '') || value) {
                        if (
                            (typeof value == 'boolean' && !value) ||
                            value == 'false'
                        ) {
                            return acc
                        }

                        if (typeof value == 'object' && !value.length) {
                            return acc
                        }

                        acc[key] = value
                    }
                    return acc
                },
                {},
            )

            this.fetch({ filters: cleanedFilters })
        },

        viewMedia(media) {
            this.$refs.mediaViewer.view(media.uuid)
        },

        refresh() {
            this.$inertia.reload({
                only: ['medias'],
                preserveState: true,
                preserveScroll: true,
            })
        },

        renameFileCompleted() {
            this.$refs.renameMedia.close()
            this.refresh()
        },

        renameFile(file) {
            this.$refs.renameMedia.start(file.uuid)
        },

        removeFile(media) {
            const order = this.order

            this.$confirm.ask({
                title: 'Delete',
                question: 'Do you want to delete this media?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('orders.show.files.medias.destroy', {
                            order,
                            media: media.uuid,
                        }),
                        {
                            preserveState: false,
                            preserveScroll: true,
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'Media has been deleted and detached.',
                                )
                            },
                        },
                    )
                },
            })
        },

        detachToOrder(media) {
            const order = this.order

            this.$confirm.ask({
                title: 'Detach',
                question: 'Do you want to detach this media from this order?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('orders.show.files.medias.detach', {
                            order,
                            media: media.uuid,
                        }),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['medias'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'Media has been detached to this order.',
                                )
                            },
                        },
                    )
                },
            })
        },

        confirmSharingToOppositeParty({ uuid: media }) {
            const order = this.order

            this.$confirm.ask({
                title: 'Share',
                question: this.$t(
                    'order.labels.confirm-sharing-to-opposite-party',
                ),
                type: 'warning',
                confirm: () => {
                    this.$inertia.post(
                        this.$route(
                            'orders.orders.files.share_to_opposite_party',
                            {
                                order,
                                media,
                            },
                        ),
                        {},
                        {
                            only: ['medias'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'File has been shared to the opposite party.',
                                )
                            },
                            onError: (errors) => {
                                this.$root.$emit('flash.validation', errors)
                            },
                        },
                    )
                },
            })
        },
    },
}
</script>

<template>
    <div class="fill-height tw-w-full">
        <portal
            to="toolbar"
            tag="span"
        >
            <v-toolbar
                id="dropdown-example"
                absolute
                top
                flat
                color="white"
            >
                <v-btn
                    v-tooltip.top="'Go to order details'"
                    icon
                    class="hidden-xs-only"
                    @click="$inertia.get($route('orders.show', { order }))"
                >
                    <v-icon>arrow_back</v-icon>
                </v-btn>
                <v-toolbar-title>
                    {{ $t('order.labels.order-media') }}
                </v-toolbar-title>

                <v-spacer />
                <MediaFilters
                    :media-categories="categories"
                    @filter="filterChanged"
                />
            </v-toolbar>
        </portal>
        <JumboUploadButton
            v-if="!medias.length"
            title="Add Media"
            @click:card="(e) => $refs.addMediaForm.show(order)"
        />
        <v-container
            v-else
            grid-list-lg
            text-xs-center
            class="!tw-max-w-full"
        >
            <v-layout
                row
                wrap
            >
                <v-flex
                    v-for="(media, idx) in items"
                    :key="media?.uuid + idx"
                    md4
                    sm6
                    xs12
                >
                    <FileItem
                        :id="`media_file_${media.id}`"
                        :media-id="media.id"
                        :type="media.file_type"
                        :thumbnail="media.conversions.thumbnail"
                        :name="media.file_name"
                        :tag-address="media?.address_tag?.full"
                        :uploaded-by="media.owner?.name"
                        :uploaded-at="media.created_at | toDateTimeLocal"
                        :last-modified="media.custom_properties.lastModified"
                        :category="media.category"
                        @preview="viewMedia(media)"
                        @rename="renameFile(media)"
                        @click:more="(e) => $refs.fileMenuList.show(e, media)"
                    />
                </v-flex>
                <v-flex
                    v-for="skeleton in 6"
                    :key="`skeleton-${skeleton}`"
                    md4
                    sm6
                    xs12
                >
                    <FileListingSkeleton
                        v-show="isLoading"
                        :type="filters?.type"
                    />
                </v-flex>
            </v-layout>
        </v-container>

        <v-btn
            color="primary"
            dark
            fixed
            bottom
            right
            icon
            large
            @click="(e) => $refs.addMediaForm.show(order)"
        >
            <v-icon>add</v-icon>
        </v-btn>

        <MediaView ref="mediaViewer" />

        <FileMenuList
            ref="fileMenuList"
            :options="[
                'open',
                'tag-to-address',
                'tag-to-category',
                'rename',
                'download',
                'detach-to-order',
                'remove',
                'share',
                'share-to-opposite',
            ]"
            @click:open="(media) => viewMedia(media)"
            @click:rename="(media) => renameFile(media)"
            @click:share="(media) => $refs.shareMedia.start(media)"
            @click:remove="(media) => removeFile(media)"
            @click:detach-to-order="(media) => detachToOrder(media)"
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
            @click:share-to-opposite="
                (media) => confirmSharingToOppositeParty(media)
            "
            @click:tag-to-category="
                (media) => $refs.tagCategoryForm.show(media)
            "
        />
        <TagAddressForm
            ref="tagAddressForm"
            @refresh-media="refresh"
        />

        <MediaTagCategory
            ref="tagCategoryForm"
            :categories="categories"
            @refresh-media="refresh"
        />

        <SendFileForm
            ref="sendFileForm"
            title="Send to"
        />

        <RenameMediaForm
            ref="renameMedia"
            @success="renameFileCompleted"
        />

        <ShareFileForm
            ref="shareMedia"
            :permission-types="permissionTypes"
        />

        <AddMediaForm
            ref="addMediaForm"
            @click:view-media="(media) => viewMedia(media)"
        />
    </div>
</template>
