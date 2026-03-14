
export const today = (separator = ".") => {
    const today = new Date()
    const { month, day, year } = {
        month: ("0" + (today.getMonth() + 1)).slice(-2),
        day: ("0" + today.getDate()).slice(-2),
        year: today.getFullYear()
    }

    return [month, day, year].join(separator)
}

export default { today }
