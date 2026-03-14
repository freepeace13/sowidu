/** @flow */
import AuthService from '~/services/AuthService';
import { User } from '~/services/models';
import * as types from './constants';
import { AUTH_GUARDS } from '~/support/constants';
import type { LoginCredentials } from 'auth-login-payload';
import { jsonParse } from '~/support/helpers';

const { USER_PROFILE_COOKIE, USER_TOKEN_COOKIE } = types;

type LoginPayload = {
    credentials: LoginCredentials
}

export default {
    namespaced: true,

    state: {
        profile: User.create({}),
        accessToken: null
    },

    actions: {
        async fetchProfile({ commit, dispatch, rootGetters }: Object): Promise<void> {
            try {
                const accessToken = rootGetters['auth/token'](AUTH_GUARDS.USER);
                const profile: User = await AuthService.profile(accessToken);
                commit(types.SET_PROFILE, profile);
            } catch (error) {
                console.error(error);
                commit(types.LOGOUT);
            }
        },

        async login(
            { commit }: Object,
            credentials: LoginCredentials
        ): Promise<void> {
            const accessToken: string = await AuthService.loginUser(credentials);
            commit(types.SET_TOKEN, accessToken);
        },

        async logout({ commit, dispatch, rootGetters }: Object): Promise<void> {
            if (! rootGetters['auth/check']('user')) return;

            // Forcely logout the authenticated company
            try {
                await dispatch('auth/company/logout', {}, { root: true });
            } catch (error) {
                console.log(error);
            }

            commit(types.LOGOUT);
            commit(types.SET_PROFILE, User.create({}));
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
            state.profile = User.create({});
            state.accessToken = null;
        },

        [types.SET_TOKEN] (state: Object, accessToken: string) {
            state.accessToken = accessToken;
        },

        [types.SET_PROFILE] (state: Object, profile: User) {
            state.profile = !(profile instanceof User)
                ? User.create(profile)
                : profile;
        },

        [types.UPDATE_PROFILE] (state: Object, attributes: Object) {
            state.profile = User.create({
                ...state.profile,
                ...attributes
            });
        }
    }
}