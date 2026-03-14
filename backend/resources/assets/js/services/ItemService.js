/** @flow */

import * as apiCalls from './api/items';
import { Unit, Type } from './models/fundamentals';
import { camelKeys } from '../support/helpers';
import type { ServicePayload } from 'item-service-payload';
import type { APIPayload } from 'item-api-payload';
import { Item } from './models';
import * as utils from '~/support/helpers';
import ServiceProvider from '@libs/ServiceProvider';
import axios from 'axios';

export class ItemService extends ServiceProvider {
    async all(contractor?: ?Authenticatable = null): Promise<Array<Item>> {
        let url = this.route('/items');

        if (contractor) {
            url = this.route(`/${contractor.entity}/${contractor.id}/items`);
        }

        const { data } = await axios.get(url);

        return data.data.map((v) => Item.create(v));
    }

    async fetchTypes(): Promise<Array<Type>> {
        const url = this.route(`/misc/item-types`);
        const { data } = await axios.get(url);
        return data.data.map((v) => Type.create(v));
    }

    async fetchUnits(): Promise<Array<Unit>> {
        const { data } = await axios.get(this.route(`/misc/units`))
        return data.data.map((v) => Unit.create(v));
    }

    async retrieve(itemId: number): Promise<Item> {
        const { data } = await axios.get(this.route(`/items/${itemId}`));
        return Item.create(data.data);
    }

    async create(instance: Item): Promise<Item> {
        const { data } = await axios.post(this.route('/items'), {
            name: instance.name,
            long_description: instance.longDescription,
            offered_price: instance.offeredPrice,
            fix_traded_price: instance.fixTradedPrice,
            retail_price: instance.retailPrice,
            item_type_id: instance.reference.typeId,
            unit_id: instance.reference.unitId,
            media: instance.media.map((v) => v.id)
        })

        return Item.create(data.data);
    }

    async update(instance: Item): Promise<Item> {
        const { data } = await axios.patch(this.route(`/items/${instance.id}`), {
            name: instance.name,
            long_description: instance.longDescription,
            offered_price: instance.offeredPrice,
            fix_traded_price: instance.fixTradedPrice,
            retail_price: instance.retailPrice,
            item_type_id: instance.reference.typeId,
            unit_id: instance.reference.unitId,
            media: instance.media.map((v) => v.id)
        })

        return Item.create(data.data);
    }
}

export default new ItemService();