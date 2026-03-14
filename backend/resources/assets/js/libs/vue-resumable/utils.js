
export function nodeIsDebug() {
    return process.env.NODE_ENV !== 'production';
}

export function castArray(value) {
    return Array.from(new Set(value));
}

export function search(files, file) {
    return castArray(files).findIndex((v) => v.key === file.uniqueIdentifier);
}

export function isArray(value) {
    return Array.isArray(value);
}

export function randomId(length = 5) {
    return Math.random().toString(36).substring(length);
}

export function bytesSum(files) {
    return files.reduce((a, b) => a + b.size, 0);
}

export function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
