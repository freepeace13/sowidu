/** @flow */

import axios from 'axios';
import * as utils from '../support/helpers';
import Address from '~/services/models/address';
import ServiceProvider from '@libs/ServiceProvider';

export class AddressService extends ServiceProvider {
    async active(): Promise<Address> {
        const { data } = await axios.get(this.route('/address/active'));
        return Address.create(data.data);
    }

    async skip(): Promise<string> {
        const { data } = await axios.patch(this.route('/address/skip'));
        return data.data;
    }

    async all(): Promise<Array<Address>> {
        const { data } = await axios.get(this.route('/address'));
        return data.data.map((v) => Address.create(v));
    }

    async create(address: Address): Promise<Address> {
        const url = this.route('/address');

        const { data } = await axios.post(url, {
            ...utils.snakeKeys(address.reference),
            set_active: address.isActive
        });

        return Address.create(data.data);
    }

    async activate(addressId: number): Promise<Address> {
        const url = this.route(`/address/${addressId}/set-as-active`);
        const { data } = await axios.patch(url, {});
        return Address.create(data.data);
    }
}

export default new AddressService;