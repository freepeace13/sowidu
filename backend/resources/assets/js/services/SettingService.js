/** @flow */

import { Role, Permission } from './models/fundamentals';
import * as apiCalls from './api/setting';
import ServiceProvider from '@libs/ServiceProvider';
import axios from 'axios';

export class SettingService extends ServiceProvider {
    async getPermissions(): Promise<Array<Permission>> {
        const url = this.route('/settings/permissions');
        const { data } = await axios.get(url);
        return data.data.map((v) => Permission.create(v));
    }

    async getRoles(): Promise<Array<Role>> {
        const url = this.route('/settings/roles');
        const { data } = await axios.get(url);
        return data.data.map((v) => Role.create(v));
    }
}

export default new SettingService;