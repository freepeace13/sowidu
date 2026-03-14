<script>
export default {
    name: 'FileUploadMenu',

    data: () => ({
        isOpen: false,
    }),
}
</script>

<template>
    <v-bottom-sheet
        v-model="isOpen"
        :inset="$vuetify.breakpoint.mdAndUp"
    >
        <v-list>
            <v-subheader>{{ $t('Upload from') }}</v-subheader>

            <v-list-tile @click="() => $emit('camera')">
                <v-list-tile-avatar>
                    <v-icon medium>photo_camera</v-icon>
                </v-list-tile-avatar>
                <v-list-tile-title>{{ $t('Capture Photo') }}</v-list-tile-title>
            </v-list-tile>

            <v-list-tile @click="() => $emit('browse')">
                <v-list-tile-avatar>
                    <v-icon medium>computer</v-icon>
                </v-list-tile-avatar>
                <v-list-tile-title>
                    {{ $t('Choose from computer') }}
                </v-list-tile-title>
            </v-list-tile>
        </v-list>
    </v-bottom-sheet>
</template>
