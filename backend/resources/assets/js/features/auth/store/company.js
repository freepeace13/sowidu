/** @flow */
import { Company } from '~/services/models';
import * as types from './constants';
import AuthService from '~/services/AuthService';
import * as apiCalls from '~/services/api/auth';
import { AUTH_GUARDS } from '~/support/constants';
import UnauthorizedError from '~/exceptions/UnauthorizedError';
import { jsonParse } from '~/support/helpers';
import config from '~/config';

export default {
    namespaced: true,

    state: {
        profile: Company.create({}),
        accessToken: null
    },

    actions: {
        async fetchProfile({ commit, rootGetters, dispatch }: Object): Promise<void> {
            const USER = config('auth.guards.user');
            const COMPANY = config('auth.guards.company');

            if (! rootGetters['auth/check'](USER)) {
                dispatch('auth/logout', USER, { root: true });
            } else {
                if (! rootGetters['auth/profile'](USER)) {
                    await dispatch('auth/fetchProfile', USER, { root: true });
                }

                try {
                    const accessToken = rootGetters['auth/token'](COMPANY);
                    const profile: Company = await AuthService.profile(accessToken);
                    commit(types.SET_PROFILE, profile);
                } catch (error) {
                    console.error(error)
                    commit(types.LOGOUT);
                }
            }
        },

        async login({ commit, dispatch }: Object, companyId: number): Promise<void> {
            const accessToken = await AuthService.loginCompany(companyId);
            commit(types.SET_TOKEN, accessToken);
        },

        async logout({ commit, rootGetters, dispatch }: Object): Promise<void> {
            const isAuthenticated = rootGetters['auth/check'](AUTH_GUARDS.COMPANY);

            if (isAuthenticated) {
                try {
                    await apiCalls.companyLogout();
                } catch (error) {
                    console.log(error);
                }

                commit(types.LOGOUT);
                commit(types.SET_PROFILE, Company.create({}));
            } 
        },

        changeProfileAvatar({ commit }: Object, media: Media): void {
            commit(types.UPDATE_PROFILE, {
                avatar: {
                    url: media.url,
                    reference: {
                        mediaId: media.id
                    }
                }
            });
        }
    },

    mutations: {
        [types.LOGOUT] (state: Object) {
            state.profile = Company.create({});
            state.accessToken = null;
        },

        [types.SET_TOKEN] (state: Object, accessToken: string) {
            state.accessToken = accessToken;
        },

        [types.SET_PROFILE] (state: Object, profile: Company) {
            state.profile = !(profile instanceof Company)
                ? Company.create(profile)
                : profile;
        },

        [types.UPDATE_PROFILE] (state: Object, attributes: Object) {
            state.profile = Company.create({
                ...state.profile,
                ...attributes
            });
        }
    }
}