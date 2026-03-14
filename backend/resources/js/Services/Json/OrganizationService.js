import BaseService from './BaseService';
import { parseUrn } from '../../Utils/Urn';

export default class OrganizationService extends BaseService {
    show(urn) {
        const [, id] = parseUrn(urn);
        return this.client.get(this.route('json.organization.show', { id }));
    }

    search(keyword) {
        return this.client.get(this.route('json.organization.index', { keyword }));
    }
}