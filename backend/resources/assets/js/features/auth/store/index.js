/** @flow */

import user from './user';
import company from './company';

import * as types from './constants';
import { isNullOrUndefined } from '~/support/helpers';
import { User, Company, Address } from '~/services/models';
import AuthService from '~/services/AuthService';
import CompanyService from '~/services/CompanyService';
import AddressService from '~/services/AddressService';
import { Permission } from '~/services/models/fundamentals';
import { AUTH_GUARDS } from '~/support/constants';
import { callAsync } from '~/support/helpers';
import Cache from '~/services/cache';

import type { LoginCredentials } from 'auth-login-payload';

type LoginPayload = {
    guard?: Guard,
    credentials: number | LoginCredentials,
}

type FetchProfilePayload = {
    guard?: Guard,
}

type UpdateAvatarPayload = {
    guard?: Guard,
    mediaId: number,
}

export default {
    namespaced: true,

    modules: { user, company },

    state: {
        locked: false,
        permissions: [],
        companies: [],
    },

    getters: {
        token(state: Object, getters: Object): Function {
            return function (guard: Guard = getters.guardName): string {
                return state[guard]['accessToken'];
            }
        },

        profile(state: Object, getters: Object): Function {
            return function (guard: Guard = getters.guardName): User | Company {
                return state[guard]['profile'];
            }
        },

        check(state: Object, getters: Object): Function {
            return function (guard: Guard = getters.guardName): boolean {
                return !isNullOrUndefined(getters.token(guard));
            }
        },

        isGuest(state: Object, getters: Object) {
            return !getters.check('company') && !getters.check('user');
        },

        isAuth(state: Object, getters: Object) {
            return !getters.isGuest;
        },

        isProperlyAuthenticated(state: Object, getters: Object) {
            if (getters.check('user') && getters.check('company')) {
                return getters.profile('user').exists()
                    && getters.profile('company').exists();
            }

            if (getters.check('user') && ! getters.check('company')) {
                return getters.profile('user').exists();
            }

            return false;
        },

        guardName(state: Object, getters: Object) {
            return getters.check(AUTH_GUARDS.COMPANY)
                ? AUTH_GUARDS.COMPANY
                : AUTH_GUARDS.USER;
        },
        
        authorize({ permissions }: Object, getters: Object) {
            return function(...names: Array<string>): boolean {
                return names.every((v) => 
                    permissions.find((p) => p.name === v) !== undefined
                );
            }
        }
    },

    actions: {
        async fetchProfile(
            { dispatch, getters }: Object,
            guard: Guard
        ): Promise<void> {
            await dispatch(`${guard || getters.guardName}/fetchProfile`);
        },

        async login(
            { dispatch, getters }: Object,
            { guard, credentials }: LoginPayload & ActionPayload
        ): Promise<void> {
            await dispatch(`${guard || getters.guardName}/login`, credentials);
        },

        logout({ dispatch, getters, commit }: Object, guard: Guard) {
            dispatch(`${guard || getters.guardName}/logout`);
            commit(types.SET_COMPANIES, []);
        },

        async fetchCompanies({ commit, state }: Object): Promise<any> {
            const result: Array<Company> = await AuthService.companies();
            commit(types.SET_COMPANIES, result);
            return result;
        },

        async fetchPermissions({ commit, state }: Object): Promise<any> {
            const result: Array<Permission> = await AuthService.permissions();
            commit(types.SET_PERMISSIONS, result);
            return result;
        },

        changeAvatar({ dispatch, getters }: Object, media: Media): void {
            dispatch(`${getters.guardName}/changeProfileAvatar`, media);
        },

        async createCompany(
            { commit, dispatch }: Object,
            { company, autoLogin }: { company: Company, autoLogin: boolean }
        ): Promise<void> {
            const result: Company = await CompanyService.create(company);

            commit(types.INSERT_COMPANY, result);

            if (autoLogin) {
                dispatch(`${AUTH_GUARDS.COMPANY}/login`, result.id);
            }
        }
    },

    mutations: {
        [types.INSERT_COMPANY] (state: Object, company: Company): void {
            state.companies = Company
                .collection(state.companies)
                .insert(company)
                .all();
        },

        [types.SET_PERMISSIONS] (state: Object, permissions: Array<Permission>) {
            state.permissions = permissions;
        },

        [types.SET_COMPANIES] (state: Object, companies: Array<Company>) {
            state.companies = companies;
        }
    }
}