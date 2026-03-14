export function sanitizeFileName(fileName, replacement = '-') {
    return fileName.replaceAll(/[\s_]/g, replacement);
}

export function renameNativeFile(originalFile, newFileName) {
    return new File([originalFile], newFileName, {
        type: originalFile.type,
        lastModified: originalFile.lastModified,
    })
}
