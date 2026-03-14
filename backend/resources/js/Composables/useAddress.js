export function getBaseAddress(address) {
    if (!address) return null

    const { country, zipcode, state } = address
    let countryName = country?.name

    return Object.values({
        zipcode,
        countryName,
        state,
    })
        .filter(Boolean)
        .join(', ')
}

export function getDistinctAddress(address) {
    if (!address) return null

    const { house_number, street, city } = address

    return Object.values({
        house_number,
        street,
        city,
    })
        .filter(Boolean)
        .join(', ')
}
