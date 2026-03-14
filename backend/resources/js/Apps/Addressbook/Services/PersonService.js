import JsonService from '@/Modules/JsonService'

export default class PersonService extends JsonService {
    /**
     * Search existing person type in the addressbook
     *
     * @param text - The text keyword for searching
     * @param size - The max no. of data of result
     */
    personSearch(text, size) {
        return this.client.get(
            this.route('json.addressbook.index', {
                text,
                size,
                resource: 'person',
            }),
        )
    }

    /**
     * Search new person that is not added to the addressbook
     *
     * @param text - The text keyword for searching
     * @param size - The max no. of data of result
     */
    newPersonSearch(text, size) {
        return this.client.get(
            this.route('json.addressbook.person.new', { text, size }),
        )
    }

    /**
     * Search new organization type of addressbook that person is not belong to
     *
     * @param id - The id of person type addressbook
     * @param text - The text keyword for searching
     * @param size - The max no. of data of result
     */
    newPersonOrganizationSearch(id, text, size) {
        return this.client.get(
            this.route('json.addressbook.person.organization.new', {
                id,
                text,
                size,
            }),
        )
    }
}
