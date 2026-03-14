export default function (parent, needle, limit = 30) {
    let parents = parent
    Array(limit)
        .fill('')
        .some(() => {
            parents = parents?.$parent

            if (!parents) return false

            return Object.hasOwn(parents?.$refs, needle)
        })

    return parents?.$refs[needle]
}
