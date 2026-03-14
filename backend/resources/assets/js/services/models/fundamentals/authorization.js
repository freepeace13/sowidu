/** @flow */

import { camelKeys } from '~/support/helpers';
import { ModelCollection as Collection } from '~/support/wrappers';
import { GenericModel } from '~/support/wrappers';

export class Permission extends GenericModel {
    static collection(collection: Array<Object>): Collection<Permission> {
        return new Collection(collection.map((v) => Permission.create(v)));
    }
}

export class Role extends GenericModel {
    permissions: Array<Permission> = [];

    constructor(props: { permissions: Array<Permission> }): void {
        super(props);

        if (Array.isArray(props.permissions)) {
            this.permissions = props.permissions.map(item => Permission.create(item));
        }
    }

    static collection(collection: Array<Object>): Collection<Role> {
        return new Collection(collection.map((v) => Role.create(v)));
    }
}