/** @flow */

import axios from 'axios';
import { Customer, Company, Employee, User } from './models';
import * as apiCalls from './api/customer';
import { callAsync } from '~/support/helpers';
import ServiceProvider from '@libs/ServiceProvider';

export class CustomerService extends ServiceProvider {
    async all(): Promise<Array<Customer>> {
        const url = this.route('/customers');

        const result = await callAsync(async () => {
            const { data } = await axios.get(url);
            return data.data;
        });

        return result.map((v) => Customer.create(v));
    }

    async retrieve(customerId: number): Promise<Customer> {
        const url = this.route(`/customers/${customerId}`);
        const { data } = await axios.get(url);
        return Customer.create(data.data);
    }

    async create(anonymous: User | Company | Employee): Promise<Customer> {
        const url = this.route(`/customers`);

        const { data } = await axios.post(url, {
            billerable_type: anonymous.entity,
            billerable_id: anonymous.id
        });

        return Customer.create(data.data);
    }
}

export default new CustomerService;