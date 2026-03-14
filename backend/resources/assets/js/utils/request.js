
export const formdata = (obj) => {
    const body = new FormData()

    for (let key in obj) {
        if (obj[key]) {
            body.append(key, obj[key])
        }
    }

    return body
}

export const postdata = (obj) => {
    return formdata(obj)
}

export const patchdata = (obj) => {
    const form = formdata(obj)
    form.append('_method', 'PATCH')
    return form
}

export const serializeOptions = (options) => {
    return Object.keys(options).map(key => `${key}=${options[key]}`).join('&')
}
