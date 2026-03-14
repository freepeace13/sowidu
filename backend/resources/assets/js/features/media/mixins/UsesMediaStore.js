/** @flow */

import { mapState, mapActions } from 'vuex';
import { createContext } from '~/support/factories';
import { Media } from '~/services/models';
import MediaService from '~/services/MediaService';

export default () => ({
    computed: {
        ...mapState({
            media: (state) => state.media.media
        }),

        timeline() {
            return Media
                .collection(this.media)
                .groupBy(({ formattedDates: { monthDayUploaded } }) => monthDayUploaded)
                .all();
        }
    },

    created() {
        const { $resumable } = this;

        this.$media = createContext({
            update(instance: Media) {
               return MediaService.update(instance);
            },
            create(files: Array<File>) {
                return $resumable.upload('/api/media', files)();
            },
            reupload(mediaId: number, files: Array<File>) {
                return $resumable.upload(`/api/media/${mediaId}/upload`, files)();
            }
        })
    }
});