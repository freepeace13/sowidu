import { AUTH_GUARDS, STORE_MODULES } from '~/support/constants';
import { readonlyPropertyDesc } from '~/services/utils';
import { createNamespace } from '~/services/store/utils';
import store from '~/services/store';
import User from '~/services/models/user';
import * as apiCalls from '~/services/api/auth';

const MODULE_NAME = STORE_MODULES.AUTH

class Authorization {
    constructor(guard) {
        this._guard = guard
    }

    can(permissions = null) {
        const user = this._guard.authorizable()

        return user.permissions.some(p => {
            return Array.isArray(permissions)
                ? permissions.some(i => i === p.name)
                : p.name === permissions
        })
    }
}

class Guard {
    constructor(auth, guardName = null) {
        this._auth = auth
        this.guardName = guardName
        this._authorization = new Authorization(this)
    }

    can(permission = null) {
        return this._authorization.can(permission)
    }

    getGuardName() {
        return this.guardName || this.currentGuardName()
    }

    fetchAccount() {
        let action = 'fetchUser';

        if (this.getGuardName() === AUTH_GUARDS.COMPANY) {
            action = 'fetchCompany';
        }

        return this._auth.dispatch(this.getGuardName())(action);
    }

    async changeAvatar(mediaId) {
        const { data: { data: media } } = await apiCalls.updateAvatar(mediaId);
        await this.fetchAccount();
        return media;
    }

    login(payload) {
        const action = this._auth.dispatch(this.getGuardName());
        return action('login', payload);
    }

    logout() {
        if (this.check()) {
            const action = this._auth.dispatch(this.getGuardName());
            return action('logout', { reload: true });
        }
    }

    check() {
        return !!this.token()
    }

    token() {
        return this._auth.getters[
            this._auth.namespaceOf()('getTokenByGuard')
        ](this.getGuardName())
    }

    currentGuardName() {
        return this._auth.getters[
            this._auth.namespaceOf()('currentGuardName')
        ]
    }

    user() {
        return this._auth.getters[
            this._auth.namespaceOf()('getAuthByGuard')
        ](this.getGuardName())
    }

    authorizable() {
        if (!(this.user() instanceof User)) {
            return this._auth.getters[
                this._auth.namespaceOf()('employee')
            ]
        }

        return this.user()
    }

    guard(guardName = null) {
        return new Guard(this._auth, guardName)
    }
}


class Auth {
    constructor(_store) {
        Object.defineProperty(this, '_store', readonlyPropertyDesc(store))

        return new Guard(this)
    }

    namespaceOf(...modules) {
        return createNamespace(MODULE_NAME, ...modules)
    }

    get getters() {
        return this._store.getters
    }

    get dispatcher() {
        return this._store.dispatch
    }

    dispatch(...namespace) {
        const fromBase = this.namespaceOf(...namespace)

        return (action, payload) => this.dispatcher(
            fromBase(action), payload
        )
    }
}

export default new Auth(store)
