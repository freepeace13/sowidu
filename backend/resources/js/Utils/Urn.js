export const isUrn = (value) => {
    return typeof value === 'string' && value.startsWith('urn:')
}

export const parseUrn = (value) => {
    return isUrn(value)
        ? value.replace('urn:', '').split(':')
        : [undefined, value]
}
