import OrganizationService from '../../Services/OrganizationService'

/**
 * Search existing organization type in the addressbook
 *
 * @param text - The text keyword for searching
 * @param size - The max no. of data of result
 */
export function useOrganizationSearch(text, size) {
    return new OrganizationService().organizationSearch(text, size)
}

/**
 * Search new organization that is not added to the addressbook
 *
 * @param text - The text keyword for searching
 * @param size - The max no. of data of result
 */
export function useNewOrganizationSearch(text, size) {
    return new OrganizationService().newOrganizationSearch(text, size)
}

/**
 * Search person addressbook not belong to the organization
 *
 * @param id - The id of organization type addressbook
 * @param text - The text keyword for searching
 * @param size - The max no. of data of result
 */
export function useNewOrganizationMemberSearch(id, text, size) {
    return new OrganizationService().newOrganizationMemberSearch(id, text, size)
}

/**
 * Show organization information
 *
 * @param {int} id Organization id
 * @returns {object}
 */
export function useShowOrganization(id) {
    return new OrganizationService().show(id)
}
