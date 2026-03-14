/** @flow */

import type { RelationsType } from 'auth-prop-types';

type PropTypes = {
    relations?: ?RelationsType
}

export default (Base: Function) => class Relational extends Base {
    relations: ?RelationsType;

    constructor(props: PropTypes): void {
        super(props);

        this.relations = props.relations;
    }
}