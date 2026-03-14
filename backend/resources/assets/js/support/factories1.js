import { CONTACT_TYPES } from '~/support/constants';
import User from '~/services/models/user';
import Company from '~/services/models/user';
import Employee from '~/services/models/user';

export const createContactableFromApi = (apiObject, fallback = null) => {
    if (typeof(apiObject) !== 'object') {
        return fallback;
    }

    else if (apiObject.contactable_type === CONTACT_TYPES.USER) {
        return new User(apiObject);
    }

    else if (apiObject.contactable_type === CONTACT_TYPES.COMPANY) {
        return new Company(apiObject);
    }

    else if (apiObject.contactable_type === CONTACT_TYPES.EMPLOYEE) {
        return new Employee(apiObject);
    }

    return apiObject;
}