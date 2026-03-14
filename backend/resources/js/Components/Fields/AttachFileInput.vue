<template>
    <v-menu :nudge-width="200" offset-x>
        <template #activator="{ on }">
            <v-btn flat icon v-on="on">
                <v-icon>attachment</v-icon>
            </v-btn>
        </template>
        <v-card>
            <v-list subheader dense>
                <v-subheader class="text-right"> Add file from... </v-subheader>
                <v-divider />
                <v-list-tile @click="$refs.files.click()">
                    <v-list-tile-action>
                        <v-icon>computer</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>Your computer</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile @click="$emit('show:media-drawer')">
                    <v-list-tile-action>
                        <v-icon>perm_media</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>Your files</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-card>

        <input
            ref="files"
            type="file"
            accept="video/*,image/*,.pdf"
            class="d-none"
            @change="uploadFile($event.target.files)"
        />
    </v-menu>
</template>
<script>
export default {
    methods: {
        uploadFile(files) {
            this.$emit('file-selected', files)
        },
    },
}
</script>
