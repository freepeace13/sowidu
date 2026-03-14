/** @flow */

import Vuex from 'vuex';

const mutations = {
    company: {
        SET_PROFILE: `auth/company/SET_PROFILE`,
        LOGOUT: `auth/company/LOGOUT`,
        SET_TOKEN: `auth/company/SET_TOKEN`
    },
    user: {
        SET_PROFILE: `auth/user/SET_PROFILE`,
        LOGOUT: `auth/user/LOGOUT`,
        SET_TOKEN: `auth/user/SET_TOKEN`
    }
}

export class MutationPolicy {
    store: Vuex;

    constructor(store: Vuex) {
        this.store = store;
    }

    isProfileReloadAllowed(mutation: { type: string }) {
        return [
            mutations.user.SET_TOKEN,
            mutations.company.SET_TOKEN
        ].includes(mutation.type);
    }

    isResetStateAllowed(mutation: { type: string }) {
        return [mutations.user.LOGOUT].includes(mutation.type);
    }

    isModulesReloadAllowed(mutation: { type: string }) {
        return [
            mutations.company.LOGOUT,
            mutations.user.SET_PROFILE,
            mutations.company.SET_PROFILE,
        ].includes(mutation.type);
    }
}

export default (store: Vuex) => new MutationPolicy(store);