/** @flow */

import * as types from './constants';
import { Role, Permission } from '~/services/models/fundamentals';
import SettingService from '~/services/SettingService';

export default {
    namespaced: true,

    state: {
        permissions: [],
        roles: []
    },

    actions: {
        async fetchPermissions({ commit }: Object): Promise<void> {
            const result: Array<Permission> = await SettingService.getPermissions();
            commit(types.SET_PERMISSIONS, result);
        },

        async fetchRoles({ commit }: Object): Promise<void> {
            const result: Array<Role> = await SettingService.getRoles();
            commit(types.SET_ROLES, result);
        },
    },

    mutations: {
        [types.SET_ROLES] (state: Object, roles: Array<Role>): void {
            state.roles = roles;
        },

        [types.SET_PERMISSIONS] (state: Object, permissions: Array<Permission>): void {
            state.permissions = permissions;
        }
    }
}