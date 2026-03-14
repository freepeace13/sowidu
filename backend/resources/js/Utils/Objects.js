export function extractInObject(obj, arrProps) {
    let extracted = {}
    let remaining = {}

    for (const prop in obj) {
        if (arrProps.includes(prop)) {
            extracted[prop] = obj[prop]
        } else {
            remaining[prop] = obj[prop]
        }
    }

    return [extracted, remaining]
}

export function isEmpty(obj) {
    return (
        obj == null ||
        (typeof obj === 'object' && Object.keys(obj).length === 0) ||
        (typeof obj === 'string' && obj.trim().length === 0)
    )
}
