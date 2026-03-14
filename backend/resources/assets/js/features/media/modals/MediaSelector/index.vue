<template>
    <ModalLayout
        v-bind="$attrs"
        :title="$attrs.modal.options.title"
        :id="$attrs.modal.id"
    >
        <v-container grid-list-lg fluid>
            <v-layout
                v-for="[date, items] of Object.entries(timeline)"
                :key="date"
                row
            >
                <v-flex xs1>
                    <v-subheader>{{ date }}</v-subheader>
                </v-flex>
                <v-flex>
                    <v-layout row wrap fill-height>
                        <v-flex xs4 lg2 v-for="image in items.all()" :key="image.id">
                            <ImageCard
                                @click.native="handleSelect(image)"
                                :url="image.type !== 'video' ? image.url : null"
                                :highlighted="choices.findIndex((v) => v.equals(image)) != -1"
                                :progress="image.progress || 0"
                            />
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>
        </v-container>

        <template v-slot:actions>
            <UploadButton
                color="success"
                @upload="(event) => $media.create(event.target.files)"
                multiple
            >
                Upload File(s)
            </UploadButton>

            <v-spacer></v-spacer>

            <v-btn color="primary" @click="handleSave">
                Done
            </v-btn>

            <v-btn color="secondary" @click="$modal.close($vnode.key)">
                Close
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import { Response } from '~/services/events/modal';
import ImageCard from '~/components/UI/Cards/ImageCard';
import UploadButton from '~/components/UI/Buttons/UploadButton';
import { Media } from '~/services/models';
import UsesMediaStore from '../../mixins/UsesMediaStore';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    name: 'MediaSelectorModal',

    mixins: [UsesMediaStore(), DispatchWhenTokenChanges('media/all')],

    components: {
        ImageCard,
        UploadButton
    },

    props: {
        selected: {
            type: Array,
            default: () => ([])
        },

        onSelect: {
            type: Function,
            required: true
        }
    },

    data: () => ({
        choices: []
    }),

    methods: {
        handleSelect(media) {
            const index = this.choices.findIndex((v) => v.equals(media));

            if (index === -1) {
                this.choices.push(media);
            } else {
                this.choices.splice(index, 1);
            }
        },

        handleSave() {
            this.onSelect(new Response(this, this.choices));
        }
    },

    created() {
        this.choices = [...this.selected];
    }
}
</script>
