/** @flow */

import Vuex from 'vuex';
import Media from '~/services/models/media';

export default (store: Vuex) => ({
    MediaUpdate: (event: any) => {
        store.commit('media/MEDIA_UPDATE', Media.create(event));
    },

    InsertMedia: (event: any) => {
        store.commit('media/INSERT_MEDIA', Media.create(event));
    },
});