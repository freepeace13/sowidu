/** @flow */

import * as apiCalls from './api/contact';
import { User, Company, Employee, resolveFromRaw, Address } from './models';
import { ContactRequest } from './models/fundamentals';
import { CONTACT_TYPES } from '~/support/constants';
import * as utils from '~/support/helpers';
import axios from 'axios';
import ServiceProvider from '@libs/ServiceProvider';

export type Contactable = Class<User> | Class<Company> | Class<Employee>;

const redirectUrl = window.location.origin + '/account/reset-password';


export const transformers = {
    address: {
        $store: (instance: Address) => utils.snakeKeys(instance.reference),
        $patch: (instance: Address) => ({
            street: instance.street,
            city: instance.city,
            house_number: instance.houseNumber,
            zipcode: instance.zipcode,
            country: instance.country,
            state: instance.state,
            reference: utils.snakeKeys(instance.reference)
        })
    },
    users: {
        $store: (instance: Class<User>) => ({
            first_name: instance.firstName,
            last_name: instance.lastName,
            email: instance.email,
            mobile: instance.mobile,
            address: transformers.address.$store(instance.address),
            redirect_url: redirectUrl
        }),
        $patch: (instance: Class<User>) => ({
            ...transformers.users.$store(instance),
            address: transformers.address.$patch(instance.address)
        })
    },
    employees: {
        $store: (instance: Class<Employee>) => ({
            ...transformers.users.$store(instance),
            company_id: instance.employer.id,
            specialization_id: instance.reference.specializationId,
            address: transformers.address.$store(instance.address),
            redirect_url: redirectUrl
        }),
        $patch: (instance: Class<Employee>) => ({
            ...transformers.users.$patch(instance),
            specialization: instance.specialization,
            address: transformers.address.$patch(instance.address),
            reference: {
                specialization_id: instance.reference.specializationId
            }
        })
    },
    companies: {
        $store: (instance: Class<Company>) => ({
            name: instance.name,
            email: instance.founder.email,
            mobile: instance.founder.mobile,
            institution_type_id: instance.reference.institutionTypeId,
            legal_form_id: instance.reference.legalFormId,
            address: transformers.address.$store(instance.address),
            redirect_url: redirectUrl
        }),
        $patch: (instance: Class<Company>) => ({
            name: instance.name,
            legal_form: instance.legalForm,
            institution_type: instance.institutionType,
            address: transformers.address.$patch(instance.address),
            reference: {
                institution_type_id: instance.reference.institutionTypeId,
                legal_form_id: instance.reference.legalFormId,
            },
            founder: {
                email: instance.founder.email,
                mobile: instance.founder.mobile
            }
        })
    }
}

export class ContactService extends ServiceProvider {
    async all(): Promise<Array<Contactable>> {
        const url = this.route('/contacts');

        const result = await utils.callAsync(async () => {
            const { data } = await axios.get(url);
            return data.data;
        });

        return result.map((v) => resolveFromRaw(v));
    }

    async add({ entity, id }: Contactable): Promise<void> {
        const url = this.route(`/contacts/${entity}/${id}/add-to-addressbook`);
       return await axios.post(url);
    }

    async retrieve(contactId: number): Promise<Contactable> {
        const { data } = await axios.get(this.route(`/contacts/${contactId}`));
        return resolveFromRaw(data.data);
    }

    async delete(contactId: number): Promise<Contactable> {
        const { data } = await axios.delete(this.route(`/contacts/${contactId}`));
        return resolveFromRaw(data.data);
    }

    async update(instance: Contactable): Promise<Contactable> {
        const { entity, relations } = instance;

        const url = this.route(`/contacts/${entity}/${relations['contact:id']}`);
        const payload = transformers[entity].$patch(instance);

        const { data } = axios.post(url, utils.patchdata(payload));

        return resolveFromRaw(data.data);
    }

    async create(instance: Contactable): Promise<Contactable> {
        const url = this.route(`/contacts/${instance.entity}`);
        const payload = transformers[instance.entity].$store(instance);

        const { data } = await axios.post(url, utils.postdata(payload));

        return resolveFromRaw(data.data);
    }
}

export default new ContactService;