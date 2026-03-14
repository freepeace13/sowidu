import JsonService from '@/Modules/JsonService'

export default class PlaceService extends JsonService {
    getCountries(keyword = null) {
        return this.client.get(this.route('json.place.country', { keyword }))
    }

    getCountryStates(country, keyword = null) {
        return this.client.get(
            this.route('json.place.country.state', { country, keyword }),
        )
    }

    getCountryCities(country, keyword = null) {
        return this.client.get(
            this.route('json.place.country.city', { country, keyword }),
        )
    }
}
