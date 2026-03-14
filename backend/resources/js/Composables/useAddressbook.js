import AddressbookService from '@/Apps/Addressbook/Services/AddressbookService.js'
export * from '@/Apps/Addressbook/Composables'

export function useShowAddressbook(id, params = {}) {
    return new AddressbookService().show(id, params)
}

/**
 * Search on addressbook
 *
 * @param text - The text keyword for searching
 * @param size - The max no. of data of result
 */
export function useSearchAddressbook(text, size) {
    return new AddressbookService().search(text, size)
}
