/** @flow */

import * as utils from '~/support/helpers';
import * as Authorization from '~/services/models/fundamentals/authorization';
import * as Address from '~/services/models/fundamentals/address';
import * as Organization from '~/services/models/fundamentals/company';
import * as Product from '~/services/models/fundamentals/items';

const mapModels = {
    countries: Address.Country,
    zipcodes: Address.Zipcode,
    streets: Address.Street,
    houseNumbers: Address.HouseNumber,
    institutionTypes: Organization.InstitutionType,
    legalForms: Organization.LegalForm,
    specializations: Organization.Specialization,
    units: Product.Unit,
    itemTypes: Product.Type,
    permissions: Authorization.Permission
}

export default (data: Object) => {
    if (utils.isObject(data)) {
        data = utils.camelKeys(data);

        return Object.keys(data).reduce((values: Object, key: string) => {
            if (utils.isArray(data[key]) && Object.keys(mapModels).includes(key)) {
                values[key] = data[key].map((value) => mapModels[key].create(value));
            }

            return values;
        }, {});
    }

    return null;
}