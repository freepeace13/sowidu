/** @flow */

import {  ModelCollection as Collection } from '~/support/wrappers';
import { Model } from '~/support/wrappers';
import { camelKeys } from '~/support/helpers';
import type { PropTypes } from 'media-prop-types';

const mimetypes = {
    images: ['image/svg+xml', 'image/jpeg', 'image/png'],
    videos: ['video/mpeg', 'video/x-msvideo', 'video/3gpp', 'video/mp4']
}

export default class Media extends Model {
    name: string;
    format: string;
    url: string;
    mimetype: MimeType;
    description: string;
    formattedDates: {
        uploadedOn: string,
        yearUploaded: string,
        monthDayUploaded: string,
        uploadedAt: string,
    };

    static mimetypes = mimetypes;

    constructor(props: PropTypes) {
        super(props);

        this.id = props.id;
        this.name = props.name;
        this.format = props.format;
        this.url = props.url;
        this.mimetype = props.mimetype;
        this.description = props.description;
        this.formattedDates = props.formattedDates;
    }

    isImage() {
        return Media.isImage(this);
    }

    isVideo() {
        return Media.isVideo(this);
    }

    static isVideo(media: Media) {
        return ! this.isImage(media);
    }

    static isImage(media: Media) {
        return this.mimetypes.images.includes(media.mimetype);
    }

    static create(attrs: Object): Media {
        const props: PropTypes = camelKeys(attrs);
        return new Media(props);
    }

    static collection(collection: Array<Object>): Collection<Media> {
        return new Collection(collection.map((v) => Media.create(v)));
    }
}