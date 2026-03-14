import BaseService from './BaseService'
import { parseUrn } from '../../Utils/Urn'

export default class PersonService extends BaseService {
    show(urn) {
        const [, id] = parseUrn(urn)
        return this.client.get(this.route('json.person.show', { id }))
    }

    search(keyword, params = {}) {
        return this.client.get(
            this.route('json.person.index', { ...params, keyword }),
        )
    }

    store(urn) {
        return this.client.post(this.route('json.addressbook.store'), { urn })
    }
}
