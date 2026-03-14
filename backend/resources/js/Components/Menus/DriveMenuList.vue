<template>
    <v-list>
        <!-- <menu-list-item icon="folder" :disabled="!media.policies.can_create_folder" @click="$emit('click:new-folder')">
            {{ $t('headings.new-folder') }}
        </menu-list-item> -->

        <menu-list-item
            icon="upload"
            :disabled="!media.policies.can_upload_file"
            @click="$refs.files.click()"
        >
            {{ $t('buttons.upload-file') }}
        </menu-list-item>

        <v-divider />

        <menu-list-item
            icon="person_add"
            :disabled="!media.policies.can_share"
            @click="$emit('click:share')"
        >
            {{ $t('buttons.share') }}
        </menu-list-item>

        <!-- <menu-list-item icon="drive_file_rename_outline" :disabled="!media.policies.can_rename" @click="$emit('click:rename')">
            {{ $t('buttons.rename') }}
        </menu-list-item> -->

        <!-- <menu-list-item icon="drive_file_move" :disabled="!media.policies.can_move" @click="$emit('click:move')">
            {{ $t('buttons.move-to') }}
        </menu-list-item> -->

        <!-- <menu-list-item icon="delete_outline" @click="$inertia.visit($route('media.trash', { type: $page.props.type }))">
            {{ $t('headings.trash') }}
        </menu-list-item> -->

        <!-- <input
            ref="files"
            type="file"
            accept="video/mp4,image/png,image/jpeg,application/pdf"
            class="d-none"
            multiple
            @change="uploadFiles($event.target.files)"
        /> -->
        <input
            ref="files"
            type="file"
            :accept="$page.props.allowedTypes"
            class="d-none"
            multiple
            @change="uploadFiles($event.target.files)"
        />
    </v-list>
</template>

<script>
import MenuListItem from '../MenuListItem.vue'

export default {
    components: { MenuListItem },

    props: {
        media: {
            type: Object,
            required: true,
        },
    },

    methods: {
        async uploadFiles(files) {
            this.$emit('click:upload-file', files)
            this.$refs.files.value = null
        },
    },
}
</script>
