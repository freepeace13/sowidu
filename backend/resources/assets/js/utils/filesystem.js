export const Base64ToFile = (source) => {
    let ext = source.split(";")[0].split("/")[1],
        byteString = atob(source.split(",")[1]),
        ab = new ArrayBuffer(byteString.length),
        ia = new Uint8Array(ab)

    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i)
    }

    let blob = new Blob([ab])

    return new File([blob], "avatar", { type: "image/" + ext })
}

export const bytesToSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'

    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']

    const i = Math.floor(Math.log(bytes) / Math.log(k))

    return parseFloat((bytes / Math.pow(k, i)).toFixed(0)) + sizes[i]
}
