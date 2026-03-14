import { usePage } from '@inertiajs/vue2'

/**
 * Retrieves a nested property from the page props using a dot-notation key.
 *
 * @param {string} key - The dot-separated key to access the nested property.
 * @param {*} [defaultValue=null] - The default value to return if the property is not found.
 * @returns {*} - The value of the nested property or the default value if not found.
 */
export function useGetPageProps(key, defaultValue = null) {
    return (
        key.split('.').reduce((a, b) => a[b], usePage().props) ?? defaultValue
    )
}
