import { usePage } from '@inertiajs/vue2'

export function isNull(obj) {
    return obj === null
}

export function isNotNull(obj) {
    return !isNull(obj)
}

export function isUndefined(obj) {
    return obj === void 0 || obj === undefined
}

export function isNil(obj) {
    return isNull(obj) || isUndefined(obj)
}

export function isNotNil(obj) {
    return isNull(obj) === false && isUndefined(obj) === false
}

export function isString(str) {
    return typeof str === 'string' || str instanceof String
}

export function isEmpty(obj) {
    return (
        obj == null ||
        (typeof obj === 'object' && Object.keys(obj).length === 0) ||
        (typeof obj === 'string' && obj.trim().length === 0)
    )
}

export function isNotEmpty(obj) {
    return isEmpty(obj) === false
}

export function isCurrentRoute(routeName) {
    return window.route(routeName, null, false) === usePage().url
}

export function getPageProps(key, defaultValue = null) {
    return (
        key.split('.').reduce((a, b) => a[b], usePage().props) ?? defaultValue
    )
}

export function truncate(string, length, separator = '...') {
    if (string.length <= length) return string
    return `${string.substring(0, length)}${separator}`
}

export function padLeft(str, pad = '0') {
    return str.toString().padStart(3, pad)
}

export function roundToTwoDecimals(num) {
    return Math.round(num * 100) / 100
}

export function numberFormat(
    number,
    decimals = 2,
    dec_point = '.',
    thousands_sep = ',',
) {
    number = parseFloat(number)
    let fixedNumber = number.toFixed(decimals)

    let parts = fixedNumber.split('.')
    let integerPart = parts[0]
    let decimalPart = parts.length > 1 ? parts[1] : ''

    // Add thousands separators to the integer part
    let regex = /\B(?=(\d{3})+(?!\d))/g
    integerPart = integerPart.replace(regex, thousands_sep)

    // Return the formatted number
    return integerPart + (decimals ? dec_point + decimalPart : '')
}

export function numberFormatToFloat(number) {
    if (isNil(number)) return 0

    return parseFloat(number.replace(/,/g, ''))
}

export function useStrBefore(str, separator = ' ') {
    const words = str.split(separator)
    return words.length > 1 ? words[0] : ''
}

export function formatText(text) {
    if (isNil(text)) return ''

    return text
        .replace(/&/g, '&amp;') // escape &
        .replace(/</g, '&lt;') // escape <
        .replace(/>/g, '&gt;') // escape >
        .replace(/\n/g, '<br>')
}
