<template>
    <ModalLayout>
        <v-img
            :src="instance.url"
            :lazy-src="instance.url"
            aspect-ratio="2.75"
        />

        <v-subheader>Media Description</v-subheader>

        <v-container grid-list-lg fluid>
            <TextField
                label="Title"
                v-model="instance.name"
                :errors="$media.$errors.get('name', [])"
            />

            <TextAreaField
                label="Additional Information"
                v-model="instance.description"
            />
        </v-container>

        <template v-slot:actions>
            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">
                Close
            </v-btn>
            <v-btn color="primary" block @click="save" :loading="$media.$loading">
                Save Changes
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import { Media } from '~/services/models';
import MediaService from '~/services/MediaService';
import UsesMediaStore from '../../mixins/UsesMediaStore';

export default {
    name: 'MediaFormModal',

    mixins: [UsesMediaStore()],

    props: {
        mediaId: {
            type: Number,
            required: true
        }
    },

    data: () => ({
        instance: Media.create({}),
    }),

    methods: {
        async save() {
            await this.$media.update(this.instance);
            this.$modal.close(this.$vnode.key);
        }
    },

    async created() {
        this.instance = await MediaService.retrieve(this.mediaId);
    }
}
</script>