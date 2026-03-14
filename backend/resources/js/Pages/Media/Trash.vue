<template>
    <div>
        <v-toolbar
            color="transparent"
            flat
        >
            <v-toolbar-title class="title tw-flex tw-items-center">
                <v-icon
                    color="grey darken-2"
                    :medium="$vuetify.breakpoint.mdAndUp"
                    left
                >
                    delete_outline
                </v-icon>
                <span class="md:tw-text-xl tw-text-lg">
                    {{ $t('headings.trash') }}
                </span>
            </v-toolbar-title>
            <v-spacer />
        </v-toolbar>

        <v-divider />

        <v-container
            class="py-0"
            fluid
            grid-list-lg
        >
            <v-subheader>
                <div
                    class="tw-cursor-pointer tw-text-info hover:tw-underline"
                    @click="$inertia.get($route('media.drive.index'))"
                >
                    {{ $t('labels.media') }}
                </div>
            </v-subheader>

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
                    <FileItem
                        :id="`trashed_file_${media.id}`"
                        :media-id="media.id"
                        :type="media.file_type"
                        :thumbnail="media.conversions.thumbnail"
                        :name="media.file_name"
                        :tag-address="media.address_tag?.full"
                        :uploaded-by="media.owner?.name"
                        :uploaded-at="media.created_at"
                        :category="media.category"
                        :shared="media.is_shared"
                    >
                        <template #menu>
                            <trash-menu-list
                                :media="media"
                                @click:delete="deleteForever(media)"
                                @click:restore="restoreFile(media)"
                            />
                        </template>
                    </FileItem>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>

<script>
import FileItem from '@/Features/Media/Components/FileItem.vue'
import TrashMenuList from '../../Components/Menus/TrashMenuList.vue'

export default {
    components: {
        FileItem,
        TrashMenuList,
    },

    props: {
        files: {
            required: true,
            type: Array,
            default: () => [],
        },
    },

    methods: {
        restoreFile(file) {
            this.$inertia.put(
                this.$route('media.trash.restore', { media: file.uuid }),
                {},
                {
                    only: ['folders', 'files', 'root_folders'],
                },
            )
        },

        deleteForever(file) {
            this.$inertia.delete(
                this.$route('media.trash.destroy', { media: file.uuid }),
                {
                    only: ['folders', 'files'],
                },
            )
        },

        emptyTrash() {
            this.$inertia.delete(this.$route('media.trash.empty'), {
                only: ['folders', 'files'],
            })
        },
    },
}
</script>
