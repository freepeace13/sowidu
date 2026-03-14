/** @flow */

import axios from 'axios';
import { resolveFromRaw } from './models';
import ServiceProvider from '@libs/ServiceProvider';

export class MemberService extends ServiceProvider {
    async all(): Promise<any> {
        const { data } = await axios.get(this.route('/members'), {});
        return data.data.map((v) => resolveFromRaw(v));
    }

    async update(members: Array<Authorizable>): Promise<any> {
        const url = this.route('/members');
        const { data } = await axios.patch(url, { members: members.map((v) => v.id) });
        return resolveFromRaw(data.data);
    }
}

export default new MemberService;