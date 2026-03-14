/** @flow */

import { showMediaSelector } from '~/services/events/modal';
import { Media } from '~/services/models';

export default (propName: string) => ({
    methods: {
        removeMedia(media: Media) {
            this[propName].media = Media
                .collection(this[propName].media)
                .remove(media)
                .all();
        },

        browseMedia() {
            showMediaSelector({
                selected: this[propName].media,
                onSelect: (response) => {
                    this[propName].media = response.value;
                    response.close();
                }
            }, true);
        }
    }
});