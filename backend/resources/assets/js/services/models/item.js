/** @flow */

import { camelKeys, arrwrap } from '~/support/helpers';
import { Model, ModelCollection as Collection } from '~/support/wrappers';
import type { PropTypes, ReferenceType } from 'item-prop-types';
import ReferenceMutator from './mixins/referenceMutator';
import { Media } from '.';

export default class Item extends ReferenceMutator(Model) {
    name: ?string;
    longDescription: ?string;
    offeredPrice: ?number;
    fixTradedPrice: ?number;
    retailPrice: ?number;
    type: ?string;
    unit: ?string;
    media: ?Array<Media> = [];
    quantity: number = 0;
    reference: ?ReferenceType = {
        unitId: null,
        typeId: null
    }

    constructor(props: PropTypes) {
        super(props);

        this.name = props.name;
        this.longDescription = props.longDescription;
        this.offeredPrice = props.offeredPrice;
        this.fixTradedPrice = props.fixTradedPrice;
        this.retailPrice = props.retailPrice;
        this.type = props.type;
        this.unit = props.unit;
        this.media = props.media;
        this.quantity = Number(props.quantity || this.quantity);
        this.reference = {
            ...this.reference,
            ...Object.assign({}, props.reference)
        };
    }

    get subtotal() {
        return this.quantity * Number(this.retailPrice);
    }

    toString() {
        return this.name;
    }

    static create(attrs: Object): Item {
        const props: PropTypes = camelKeys(attrs);

        return new Item({
            ...props,
            media: arrwrap(props.media).map((v) => Media.create(v)),
        });
    }

    static collection(collection: Array<Object>): Collection<Item> {
        return new Collection<Item>(collection.map((v) => Item.create(v)));
    }
}


