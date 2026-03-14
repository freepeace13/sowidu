/** @flow */

import { Role, Permission } from '~/services/models/fundamentals';
import ServiceProvider from '@libs/ServiceProvider';
import axios from 'axios';

class RoleServiceProvider extends ServiceProvider {
    async all() {
        const { data } = await axios.get(this.route('/settings/roles'));
        return data.data.map((v) => Role.create(v));
    }

    async create(name: string) {
        const { data } = await axios.post(this.route('/settings/roles'), { name })
        return Role.create(data.data);
    }

    async delete(roleId: number) {
        return await axios.delete(this.route(`/settings/roles/${roleId}`));
    }

    async getPermissions(roleId: number) {
        const { data } = await axios.get(this.route(`/settings/roles/${roleId}/permissions`));
        return data.data.map((v) => Permission.create(v));
    }

    async setPermission(roleId: number, permissionId: number) {
        const endpoint = `/settings/roles/${roleId}/permissions/${permissionId}`;

        const { data } = await axios.post(this.route(endpoint));
        return Role.create(data.data);
    }

    async unsetPermission(roleId: number, permissionId: number) {
        const endpoint = `/settings/roles/${roleId}/permissions/${permissionId}`;

        const { data } = await axios.delete(this.route(endpoint));
        return Role.create(data.data);
    }
}

export const RoleService = new RoleServiceProvider;