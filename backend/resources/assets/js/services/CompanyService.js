/** @flow */

import axios from 'axios';
import ServiceProvider from '@libs/ServiceProvider';
import { Company, Employee } from './models';
import * as apiCalls from './api/company';

export class CompanyService extends ServiceProvider {
    async retrieve(companyId: number): Promise<Company> {
        const { data } = await axios.get(this.route(`/companies/${companyId}`));
        return Company.create(data.data);
    }

    async create(company: Company): Promise<Company> {
        const { data } = await axios.post(this.route('/companies'), {
            name: company.name,
            legal_form: company.reference.legalFormId,
            institution_type: company.reference.institutionTypeId,
            house_number: company.address.reference.houseNumberId,
            street: company.address.reference.streetId,
            city: company.address.reference.cityId,
            zipcode: company.address.reference.zipcodeId,
            country: company.address.reference.countryId,
            state: company.address.reference.stateId
        });

        return Company.create(data.data);
    }

    async employees(companyId: number): Promise<Array<Employee>> {
        const url = this.route(`/companies/${companyId}/employees`);
        const { data } = await axios.get(url);
        return data.data.map((employee) => Employee.create(employee));
    }
}

export default new CompanyService;