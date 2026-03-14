<template>
    <v-menu
        v-model="isShow"
        :position-x="x"
        :position-y="y"
        absolute
        :close-on-content-click="true"
    >
        <v-card>
            <v-list
                subheader
                dense
            >
                <v-subheader class="text-right">Add file from... </v-subheader>
                <v-divider />
                <v-list-tile @click="$refs.file.click()">
                    <v-list-tile-action>
                        <v-icon>computer</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>Your computer</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile @click="$emit('attach:from-media')">
                    <v-list-tile-action>
                        <v-icon>perm_media</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>Your media</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-card>

        <input
            ref="file"
            type="file"
            accept="image/*,.pdf,video/*"
            class="d-none"
            @change="$emit('attach:from-file', $event.target.files)"
        />
    </v-menu>
</template>
<script>
// TODO: Check on `@/Components/Fields/File/FileAttachmentFormMenu.vue - if you can use the component instead of this one
import IsDynamicMenu from '@/Mixins/IsDynamicMenu'

export default {
    mixins: [IsDynamicMenu],
}
</script>
