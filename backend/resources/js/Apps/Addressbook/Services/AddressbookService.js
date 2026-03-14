import JsonService from '@/Modules/JsonService'

export default class AddressbookService extends JsonService {
    store(urn) {
        return this.client.post(this.route('json.addressbook.store'), {
            urn,
        })
    }

    update(id, params) {
        return this.client.put(
            this.route('json.addressbook.update', { id }),
            params,
        )
    }

    show(id, params = {}) {
        return this.client.get(
            this.route('json.addressbook.show', { id, ...params }),
        )
    }

    search(text, params = {}) {
        return this.client.get(
            this.route('json.addressbook.index', {
                text,
                ...params,
            }),
        )
    }
}
