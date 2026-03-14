<template>
    <RootView title="Media" icon="image">
        <router-view></router-view>
        
        <UploadButton
            color="primary"
            large fixed right bottom fab
            @upload="$media.create($event.target.files)"
            multiple
        >
           <v-icon dark>cloud_upload</v-icon>
           <v-icon dark>cloud_upload</v-icon>
        </UploadButton>
    </RootView>
</template>

<script>
import UploadButton from '~/components/UI/Buttons/UploadButton';
import { UsesMediaStore } from '~/components/Mixins';

export default {
    mixins: [UsesMediaStore()],
    components: { UploadButton }
}
</script>