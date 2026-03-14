import { isEmpty, isNil } from './useUtils'

export function nullSafe(string, fallback = '--') {
    return string === null ||
        string === undefined ||
        (typeof string === 'string' && string.length === 0)
        ? fallback
        : string
}

export function removeNull(obj) {
    return Object.keys(obj)
        .filter((k) => obj[k] != null)
        .reduce((a, k) => ({ ...a, [k]: obj[k] }), {})
}

export function removeNullsAndEmptyObjects(obj) {
    Object.keys(obj).forEach((key) => {
        if (obj[key] && typeof obj[key] === 'object') {
            removeNullsAndEmptyObjects(obj[key])
            if (Object.keys(obj[key]).length === 0) {
                delete obj[key]
            }
        } else if (obj[key] === null) {
            delete obj[key]
        }
    })
    return obj
}

export function removeNullValuesFromObject(obj) {
    Object.keys(obj).forEach((key) => {
        if (obj[key] && typeof obj[key] === 'object') {
            const newVal = removeNull(obj[key])

            if (isNil(newVal) || isEmpty(newVal)) {
                delete obj[key]
            }
        }
        if (obj[key] === null) {
            delete obj[key]
        }
    })
    return obj
}
