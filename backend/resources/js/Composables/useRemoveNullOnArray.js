export default function (data) {
    Object.keys(data).forEach((key) => {
        if (data[key] === null) {
            delete data[key]
        }
    })
    const value = Object.values(data)
    if (!value.length) return ['Not set']
    return value
}
