import JsonService from '@/Modules/JsonService'

export default class OrganizationService extends JsonService {
    /**
     * Search existing organization type in the addressbook
     *
     * @param text - The text keyword for searching
     * @param size - The max no. of data of result
     */
    organizationSearch(text, size) {
        return this.client.get(
            this.route('json.addressbook.index', {
                text,
                size,
                resource: 'organization',
            }),
        )
    }

    /**
     * Search new organization that is not added to the addressbook
     *
     * @param text - The text keyword for searching
     * @param size - The max no. of data of result
     */
    newOrganizationSearch(text, size) {
        return this.client.get(
            this.route('json.addressbook.organization.new', { text, size }),
        )
    }

    /**
     * Search person addressbook not belong to the organization
     *
     * @param id - The id of organization type addressbook
     * @param text - The text keyword for searching
     * @param size - The max no. of data of result
     */
    newOrganizationMemberSearch(id, text, size) {
        return this.client.get(
            this.route('json.addressbook.organization.member.new', {
                id,
                text,
                size,
            }),
        )
    }

    /**
     * Show organization information
     *
     * @param {int} id Organization id
     * @returns {object}
     */
    show(id) {
        return this.client.get(this.route('json.organization.show', { id }))
    }
}
