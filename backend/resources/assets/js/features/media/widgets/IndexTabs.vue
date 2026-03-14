<template>
    <v-toolbar height="64" card>
        <v-spacer></v-spacer>
        <UploadButton
            v-can="allowCreateMedia"
            fab small
            @upload="$media.create($event.target.files)"
            multiple
        >
           <v-icon dark>cloud_upload</v-icon>
           <v-icon dark>cloud_upload</v-icon>
        </UploadButton>
    </v-toolbar>
</template>

<script>
import * as MediaEnums from '../enums';
import UploadButton from '@common/components/UploadButton';
import UsesMediaStore from '../mixins/UsesMediaStore';

export default {
    name: 'IndexTabs',

    mixins: [UsesMediaStore()],

    components: {
        UploadButton
    },

    computed: {
        allowCreateMedia() {
            return MediaEnums.PERMISSIONS.CREATE_MEDIA;
        }
    }
}
</script>