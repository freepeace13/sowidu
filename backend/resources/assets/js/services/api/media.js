/** @flow */

import { get, patch } from 'axios';
import { $uploader } from '~/services/events/chunkUploader';

const parseUploadError = (message: any) => {
    const response = JSON.parse(message)
    let error = {}

    if (response.errors.file)
        error = response.errors.file[0]
    else if (response.errors.resumableType)
        error = response.errors.resumableType[0]
    else if (response.errors.resumableTotalSize)
        error = response.errors.resumableTotalSize[0];

    return error;
}

export const fetchMedia = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/media`, options);
    return data.data;
}

export const fetchMediaById = async (
    mediaId: number,
    options: Object = {}
): Promise<any> => {
   const { data } = await get(`/api/media/${mediaId}`, options);
   return data.data;
}

export const updateMedia = async (
    mediaId: number, 
    payload: { name: string, description: string },
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/media/${mediaId}`, {
        name: payload.name,
        description: payload.description
    }, options);

    return data.data;
}

export const createMedia = (
    files: Array<File>,
    options: Object = {}
): Promise<any> => {
    return new Promise((resolve, reject) => {
        $uploader.upload({
            url: '/api/media',
            files: files,
            onError: (_, message) => {
                reject(parseUploadError(message));
            },
            onCompleted: () => $uploader.close(),
            onFileSuccess: (_, message) => {
                const response = JSON.parse(message);
                if (typeof (response.data) === 'object') {
                    resolve(response.data);
                }
            },
        });
    });
}

export const reuploadMedia = async (
    mediaId: number,
    files: Array<File>,
    options: Object = {}
): Promise<any> => {
    return new Promise((resolve, reject) => {
        $uploader.upload({
            url: `/api/media/${mediaId}/upload`,
            files: files,
            onError: (_, message) => {
                reject(parseUploadError(message));
            },
            onCompleted: () => $uploader.close(),
            onFileSuccess: (_, message) => {
                const response = JSON.parse(message);
                resolve(response.data);
            }
        });
    });
}

export const setAsAvatar = async (
    mediaId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/media/${mediaId}/set-as-avatar`, {}, options);
    return data.data;
}