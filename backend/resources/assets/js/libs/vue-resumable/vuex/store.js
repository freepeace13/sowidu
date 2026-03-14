/** @flow */

import Resumable from 'resumablejs';
import { castArray, formatBytes, bytesSum, isArray, search } from '../utils';
import { actions, mutations } from './constants';

type ResumableFile = File & {
    fileName: string,
    uniqueIdentifier: string,
    isComplete: Function,
    isUploading: Function,
    progress: Function
};

let resumableObj: Resumable;

export default {
    namespaced: true,

    state: {
        files: [],
        minimized: true,
    },

    getters: {
        uploading: (state: Object) => () => resumableObj && resumableObj.isUploading(),
        progress: (state: Object) => () => resumableObj ? resumableObj.progress() : 0
    },

    actions: {
        [actions.CLOSE]: (context: Object) => {
            context.dispatch(actions.CLEAR);
            context.commit(mutations.RESET_STATE);
        },

        [actions.UPLOAD]: (context: Object, payload: Object) => {
            resumableObj = createResumable(payload.target, payload.headers);
        
            resumableObj.on('filesAdded', files => 
                context.commit(mutations.ADD_FILES, files)
            );

            resumableObj.on('fileProgress', file =>
                context.commit(mutations.UPLOAD_PROGRESS, file)
            );

            resumableObj.on('fileSuccess', (file, message) => {
                context.commit(mutations.UPLOAD_PROGRESS, file);

                if (typeof payload.successCallback === 'function') {
                    payload.successCallback(JSON.parse(message));
                }
            });

            resumableObj.on('fileError', (file, message) => {
                context.commit(mutations.UPLOAD_ERROR, { file, message });

                if (typeof payload.errorCallback === 'function') {
                    payload.errorCallback(JSON.parse(message));
                }
            });

            resumableObj.on('complete', () => {
                console.log(context.state.files.filter((v) => !v.isPending));
            });

            resumableObj.addFiles(castArray(payload.files));

            window.setTimeout(() => resumableObj.upload(), 200);
        },

        [actions.CLEAR]: (context: Object) => {
            if (resumableObj && resumableObj.isUploading()) {
                resumableObj.cancel();
            }

            resumableObj = undefined;
        }
    },

    mutations: {
        [mutations.MINIMIZE]: (state: Object) => state.minimized = true,
        [mutations.MAXIMIZE]: (state: Object) => state.minimized = false,

        [mutations.RESET_STATE]: (state: Object) => {
            state.files = [];
            state.minimized = true;
        },

        [mutations.UPLOAD_ERROR]: (state: Object, payload) => {
            const index = search(state.files, payload.file);

            if (index >= 0) {
                const instance = state.files[index].withErrors(payload.message);
                state.files.splice(index, 1, instance);
            }
        },

        [mutations.ADD_FILES]: (state: Object, files: mixed) => {
            state.files = castArray(files).map((v) => new FileUpload(v));
        },

        [mutations.UPLOAD_PROGRESS]: (state: Object, file: ResumableFile) => {
            const index = search(state.files, file);

            if (index >= 0) {
                const instance = state.files[index];
                state.files.splice(index, 1, instance.withProgress(file));
            }
        }
    }
}

export class FileUpload {
    key: string;
    name: string;
    errors: string;
    size: number;
    progress: number = 0;
    isUploading: boolean = false;
    isComplete: boolean = false;

    constructor(file: ResumableFile) {
        this.key = file.uniqueIdentifier;
        this.name = file.fileName;
        this.size = file.size;

        this.withProgress(file);
    }

    withErrors(message: string) {
        const response = JSON.parse(message);

        if (typeof response.errors === 'undefined') {
            this.errors = response.message;
        }
        
        else if (isArray(response.errors.file)) {
            this.errors = response.errors.file[0];
        }

        else if (isArray(response.errors.resumableType)) {
            this.errors = response.errors.resumableType[0];
        }

        return this;
    }

    withProgress(file: ResumableFile) {
        this.progress = file.progress();
        this.isUploading = file.isUploading();
        this.isComplete = file.isComplete(); 

        return this;
    }

    isPending() {
        return !this.isComplete && !this.isUploading && !this.errors;
    }

    isSuccess() {
        return this.isComplete && !this.errors;
    }

    isFail() {
        return !this.isComplete && !!this.errors;
    }
}

function createResumable(target, headers = {}): Resumable {
    const instance = new Resumable({
        target: target,
        chunkSize: 1 * 1024 * 1024,
        testChunks: false,
        simultaneousUploads: 1,
        headers: headers,
        maxChunkRetries: 3,
        chunkRetryInterval: 300,
        permanentErrors: [400, 404, 409, 415, 500, 501, 422],
    });

    if (! instance.support) {
        throw new Error('Your browser, unfortunately, is not supported by Resumable.js');
    }

    return instance;
}