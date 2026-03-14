/** @flow */

import Registrar from '~/services/models/registrar';
import { snakeCase } from 'lodash';

const symsConfig = Symbol.for('model-config');

type PropTypes = {
    id?: ?number,
    createdAt?: ?string,
    updatedAt?: ?string,
    alias?: ?string
}

export interface Model {
    id?: ?number;
    createdAt?: ?string;
    updatedAt?: ?string;
    $alias?: ?string;
    constructor(props: PropTypes): void;
    equals(v: any): boolean;
    exists(): boolean;
    getKey(): any;
}

export default class ServiceModel implements Model {
    id: ?number;
    createdAt: ?string;
    updatedAt: ?string;
    alias: ?string;

    constructor(props: PropTypes): void {
        this.id = props.id;
        this.updatedAt = props.updatedAt;
        this.createdAt = props.createdAt;
        this.alias = props.alias || Registrar.getQualifiedName(this);

        // $FlowFixMe
        this[symsConfig] = new Map;
    }

    get entity() {
        // $FlowFixMe
        return this.alias || this.$alias;
    }

    setConfig(key: string, value: any): void {
        // $FlowFixMe
        this[symsConfig].set(key, value);
    }

    getConfig(key: string): any {
        // $FlowFixMe
        return this[symsConfig].get(key);
    }

    toMorphs(morph: string = "") {
        return {
            [snakeCase(morph.concat('Id'))]: this.getKey(),
            [snakeCase(morph.concat('Type'))]: this.entity
        };
    }

    exists(): boolean {
        return Boolean(this.getKey());
    }

    getKey(): any {
        //$FlowFixMe
        return this.id;
    }

    equals(v: any): boolean {
        const __prototype = Object.getPrototypeOf(this);

        if (v instanceof __prototype.constructor) {
            return this.exists() && v.exists()
                && this.getKey() === v.getKey();
        }

        return false;
    }
}