/** @flow */

import * as types from './constants';
import { Media } from '~/services/models';
import MediaService from '~/services/MediaService';
import Cache from '~/services/cache';

export default {
    namespaced: true,
    state: {
        media: [],
        types: {
            images: [
                'image/svg+xml',
                'image/jpeg',
                'image/png'
            ],
            videos: [
                'video/mpeg',
                'video/x-msvideo',
                'video/3gpp',
                'video/mp4'
            ]
        }
    },
    getters: {
        isVideo(state: Object) {
            return (type: string) => {
                return state.types.videos.indexOf(type) !== -1;
            }
        },

        findById(state: Object) {
            return (mediaId: number) => {
                return state.media.find(i => i.id === mediaId);
            }
        }
    },

    actions: {
        async all({ commit, state }: Object): Promise<any> {
            const result: Array<Media> = await MediaService.all();
            commit(types.SET_MEDIA, result);
            return result;
            // await Cache.remember('media', 3600, async () => {
            //     const result: Array<Media> = await MediaService.all();
            //     commit(types.SET_MEDIA, result);
            // });
        },

        async update({ commit }: Object, instance: Media): Promise<void> {
            const result: Media = await MediaService.update(instance);
            commit(types.MEDIA_UPDATE, result);
        },

        async create({ commit }: Object, files: Array<File>): Promise<void> {
            const result: Media = await MediaService.create(files);
            commit(types.INSERT_MEDIA, result);
        },

        async reupload(
            { commit, dispatch }: Object,
            { mediaId, files }: { mediaId: number, files: Array<File> }
        ): Promise<Media> {
            const result: Media = await MediaService.reupload(mediaId, files);
            commit(types.MEDIA_UPDATE, result);
            return result;
        },

        async setAsAvatar({ dispatch }: Object, instance: Media): Promise<void> {
            const result: Media = await MediaService.setAsAvatar(instance);
            dispatch('auth/changeAvatar', result, { root: true });
        }
    },

    mutations: {
        [types.SET_MEDIA] (state: Object, media: Array<Media>): void {
            state.media = media;
        },

        [types.MEDIA_UPDATE] (state: Object, media: Media): void {
            state.media = Media
                .collection(state.media)
                .updateOrInsert(media)
                .all();
        },

        [types.INSERT_MEDIA] (state: Object, media: Media): void {
            state.media = Media
                .collection(state.media)
                .insert(media)
                .all();
        }
    }
}
