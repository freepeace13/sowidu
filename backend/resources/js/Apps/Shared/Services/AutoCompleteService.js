import JsonService from '@/Modules/JsonService'

export default class AutoCompleteService extends JsonService {
    jobTitle(text, size = 10) {
        return this.client.get(
            this.route('json.autocomplete.jobtitle', { text, size }),
        )
    }

    houseNumber(text, size) {
        return this._placeField('house_number', text, size)
    }

    street(text, size) {
        return this._placeField('street', text, size)
    }

    zipcode(text, size) {
        return this._placeField('zipcode', text, size)
    }

    _placeField(field, text, size = 10) {
        return this.client.get(
            this.route('json.autocomplete.place', { field, text, size }),
        )
    }

    address(text, size) {
        return this.client.get(
            this.route('json.autocomplete.address', { text, size }),
        )
    }
}
