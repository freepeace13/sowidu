
export const random = (length = 10) => {
    return (new Date())
        .getTime()
        .toString()
        .substr(0, length)
}

export default { random }
