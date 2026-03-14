import { compact } from 'lodash'
import { Prop, Types } from '~/support/decorators';

export class AuthCollection {
    @Prop(Types.Primitives(Array)) _collection = [];

    constructor(values) {
        this._collection = compact(values)
    }

    toggle(resource) {
        if (this.has(resource)) {
            this.remove(resource)
        } else {
            this.add(resource)
        }
    }

    remove(resource) {
        if (this.has(resource)) {
            this._collection.splice(this.indexOf(resource), 1)
        }
    }

    add(resource) {
        if (!this.has(resource)) {
            this._collection.push(resource)
        }
    }

    has(resource) {
        return this.indexOf(resource) !== -1
    }

    indexOf(resource) {
        return this._collection.findIndex(e => e.id === resource.id)
    }

    values() {
        return this._collection
    }

    pluckIds() {
        return this._collection.map(e => e.id)
    }
}

export class AuthCollections {
    @Prop(Types.Resource(() => RoleCollection)) rules = [];
    @Prop(Types.Resource(() => PermissionCollection)) permissions = [];

    constructor(roles, permissions) {
        this.replaceRoles(roles)
        this.replacePermissions(permissions)
    }

    replaceRoles(roles) {
        this.roles = new RoleCollection(roles)
    }

    replacePermissions(permissions) {
        this.permissions = new PermissionCollection(permissions)
    }

    isPermissionViaRoles(permission) {
        this.roles.values().some(e => e.permissions.some(i => i.id === permission.id))
    }
}

export class PermissionCollection extends AuthCollection {
    constructor(permissions) {
        super(permissions)
    }
}

export class RoleCollection extends AuthCollection {
    constructor(roles) {
        super(roles)
    }
}
