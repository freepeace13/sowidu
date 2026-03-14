const number_format = (
    number,
    decimals = 0,
    decPoint = '.',
    thousandsSep = ',',
) => {
    if (isNaN(number) || number === null) return '0'

    const fixedNumber = number.toFixed(decimals)
    const parts = fixedNumber.split('.')

    // Format thousands separator
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep)

    return parts.join(decPoint)
}

export default number_format
