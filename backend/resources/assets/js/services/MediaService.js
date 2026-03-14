/** @flow */

import axios from 'axios';
import { Media } from './models';
import * as apiCalls from './api/media';
import { callAsync } from '~/support/helpers';
import ServiceProvider from '@libs/ServiceProvider';

export class MediaService extends ServiceProvider {
    async all(): Promise<Array<Media>> {
        const url = this.route(`/media`);

        const result = await callAsync(async () => {
            const { data } = await axios.get(url);
            return data.data;
        });

        return result.map((v) => Media.create(v));
    }

    async retrieve(mediaId: number): Promise<Media> {
        const url = this.route(`/media/${mediaId}`);
        const { data } = await axios.get(url, {});
        return Media.create(data.data);
    }

    async update(instance: Media): Promise<Media> {
        const url = this.route(`/media/${instance.id}`);

        const { data } = await axios.patch(url, {
            name: instance.name,
            description: instance.description
        });

        return Media.create(data.data);
    }

    async create(files: Array<File>): Promise<Media> {
        const result: Object = await apiCalls.createMedia(files);
        return Media.create(result);
    }
    
    async reupload(mediaId: number, files: Array<File>): Promise<Media> {
        const result: Object = await apiCalls.reuploadMedia(mediaId, files);
        return Media.create(result);
    }

    async setAsAvatar(instance: Media): Promise<Media> {
        const url = this.route(`/media/${instance.id}/set-as-avatar`);
        const { data } = await axios.patch(url, {});
        return Media.create(data.data);
    }
}

export default new MediaService