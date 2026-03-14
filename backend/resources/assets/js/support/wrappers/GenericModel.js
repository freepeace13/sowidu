/** @flow */

import { camelKeys } from '../helpers';

type GenericProps = {
    id: number,
    name: string,
    reference?: any
}

export default class GenericModel {
    id: number;
    name: string;
    reference: any;

    constructor(props: GenericProps): void {
        this.id = props.id;
        this.name = props.name;
        this.reference = props.reference;
    }

    static create(attrs: Object): self {
        const props: GenericProps = camelKeys(attrs);
        return new this.prototype.constructor(props);
    }
}