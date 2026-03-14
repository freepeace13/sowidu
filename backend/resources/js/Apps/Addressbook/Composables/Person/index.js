import PersonService from '../../Services/PersonService'

/**
 * Search existing person type in the addressbook
 *
 * @param text - The text keyword for searching
 * @param size - The max no. of data of result
 */
export function usePersonSearch(text, size) {
    return new PersonService().personSearch(text, size)
}

/**
 * Search new person that is not added to the addressbook
 *
 * @param text - The text keyword for searching
 * @param size - The max no. of data of result
 */
export function useNewPersonSearch(text, size) {
    return new PersonService().newPersonSearch(text, size)
}

/**
 * Search new organization type of addressbook that person is not belong to
 *
 * @param id - The id of person type addressbook
 * @param text - The text keyword for searching
 * @param size - The max no. of data of result
 */
export function useNewPersonOrganizationSearch(id, text, size) {
    return new PersonService().newPersonOrganizationSearch(id, text, size)
}
