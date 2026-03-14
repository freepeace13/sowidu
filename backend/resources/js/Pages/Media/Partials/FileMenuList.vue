<template>
    <v-menu
        v-model="isShow"
        lazy
        offset-overflow
        close-on-content-click
        min-width="260"
        :position-x="x"
        :position-y="y"
        absolute
        offset-y
        z-index="12"
    >
        <v-list>
            <MenuListItem
                icon="remove_red_eye"
                @click="$emit('click:open', media)"
            >
                {{ $t('buttons.open') }}
            </MenuListItem>

            <v-divider />

            <MenuListItem
                v-if="inOptions('share') && media.policies.can_share"
                icon="person_add"
                @click="$emit('click:share', media)"
            >
                {{ $t('buttons.share') }}
            </MenuListItem>
            <MenuListItem
                v-if="
                    inOptions('share-to-opposite') && media.policies.can_share
                "
                icon="insights"
                @click="$emit('click:share-to-opposite', media)"
            >
                {{ $t('buttons.share-to-opposite-party') }}
            </MenuListItem>

            <v-divider />

            <MenuListItem
                v-if="inOptions('tag-to-address')"
                icon="place"
                @click="$emit('click:tag-to-address', media)"
            >
                {{ $t('buttons.tag_to_address') }}
            </MenuListItem>

            <MenuListItem
                v-if="inOptions('tag-to-category')"
                icon="category"
                @click="$emit('click:tag-to-category', media)"
            >
                {{ $t('media.labels.tag-to-category') }}
            </MenuListItem>

            <v-divider />

            <MenuListItem
                v-if="inOptions('rename') && media.policies.can_rename"
                icon="drive_file_rename_outline"
                @click="$emit('click:rename', media)"
            >
                {{ $t('buttons.rename') }}
            </MenuListItem>

            <MenuListItem
                v-if="inOptions('download') && media.policies.can_download"
                icon="download"
                @click="$emit('click:download', media)"
            >
                {{ $t('buttons.download') }}
            </MenuListItem>

            <MenuListItem
                v-if="inOptions('send-to')"
                icon="send"
                @click="$emit('click:send-to', media)"
            >
                {{ $t('buttons.send-to') }}
            </MenuListItem>

            <v-divider />

            <MenuListItem
                v-if="inOptions('detach-to-order')"
                icon="link_off"
                @click="$emit('click:detach-to-order', media)"
            >
                {{ $t('buttons.detach') }}
            </MenuListItem>

            <MenuListItem
                v-if="inOptions('remove') && media.policies.can_remove"
                icon="delete"
                @click="$emit('click:remove', media)"
            >
                {{ $t('buttons.remove') }}
            </MenuListItem>
        </v-list>
    </v-menu>
</template>

<script>
import MenuListItem from '../../../Components/MenuListItem.vue'

export default {
    components: { MenuListItem },

    props: {
        options: {
            required: false,
            type: Array,
            default: () => [
                'open',
                'share',
                'tag-to-address',
                'tag-to-category',
                'rename',
                'download',
                'send-to',
                'remove',
            ],
        },
    },

    data: () => ({
        isShow: false,
        x: 0,
        y: 0,
        media: {
            policies: {
                can_remove: false,
                can_download: false,
                can_rename: false,
                can_share: false,
            },
        },
    }),

    methods: {
        inOptions(name) {
            return this.options.includes(name)
        },
        show(e, media) {
            console.log('media', media)
            this.isShow = false

            if (!media) return

            e.preventDefault()

            this.media = media
            this.x = e.clientX
            this.y = e.clientY

            this.$nextTick(() => {
                this.isShow = true
            })
        },
    },
}
</script>
