/** @flow */

import { isFunction } from '~/support/helpers';

export class DataModelRegistrar {
    static models: Array<{ key: string, model: Function }> = [];

    boot(models: Object) {
        Object.keys(models).forEach((alias) => {
            DataModelRegistrar.models.push({
                key: alias,
                model: models[alias]
            });
        });
    }

    getModels() {
        return DataModelRegistrar.models;
    }

    getQualifiedClass(v: { alias: string } & { entity: string }) {
        const models = this.getModels();
        const value = models.find(({ key }) => key === (v.alias || v.entity));

        return Object.create(value || null).model;
    }

    getQualifiedName(v: Object | Function) {
        const models = this.getModels();

        const constructor = ! isFunction(v)
            ? Object.getPrototypeOf(v).constructor
            : v;

        const value = models.find(({ model }) => model === constructor);

        return Object.create(value || null).key;
    }
}

export default new DataModelRegistrar;