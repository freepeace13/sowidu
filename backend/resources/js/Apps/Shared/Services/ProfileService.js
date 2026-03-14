import JsonService from '@/Modules/JsonService'

export default class ProfileService extends JsonService {
    show(urn) {
        return this.client.get(this.route('json.profile.show', { urn }))
    }
}
