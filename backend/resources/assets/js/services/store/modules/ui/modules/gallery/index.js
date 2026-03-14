/** @flow */

import { Media } from '~/services/models';
import * as types from './constants';
import type { SourceType } from 'media-source-type';

type GallerySource = {
    sources: Array<SourceType>,
    current: number
}

export default {
    namespaced: true,

    state: {
        sources: [],
        current: -1 // Array index of the current image displayed
    },

    actions: {
        open(
            { commit }: Object,
            { mediaId, sources }: { mediaId: number, sources: Array<Media> }
        ) {
            commit(types.OPEN_GALLERY, {
                sources: sources.map((source) => ({
                    title: source.name,
                    description: source.description,
                    type: source.mimetype,
                    src: source.url,
                    id: source.id
                })),
                current: sources.findIndex((source) => source.id === mediaId)
            });
        }
    },

    mutations: {
        [types.OPEN_GALLERY]: (state: Object, { sources, current }: GallerySource) => {
            state.sources = Array.from(new Set(sources));
            state.current = current;
        },

        [types.CLOSE_GALLERY]: (state: Object) => {
            state.sources = [];
            state.current = -1;
        },

        [types.UPDATE_SOURCE]: (state: Object, media: Media) => {
            const source: SourceType = {
                title: media.name,
                description: media.description,
                type: media.mimetype,
                src: media.url,
                id: media.id
            }

            if (state.current !== -1) {
                state.sources.splice(
                    state.current, 1, source
                );
            }
        }
    }
}