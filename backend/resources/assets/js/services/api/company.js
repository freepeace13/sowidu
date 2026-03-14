/** @flow */

import { get, post } from 'axios';

export const fetchCompany = async (
    companyId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/companies/${companyId}`, options);
    return data.data;
}

export const fetchCompanyEmployees = async (
    companyId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/companies/${companyId}/employees`, options);
    return data.data;
}

export const createCompany = async (
    payload: Object,
    options: Object = {}
): Promise<any> => {
    const { data } = await post('/api/companies', payload, options);
    return data.data;
}

export const fetchInstitutionTypes = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/misc/institution-types`, options);
    return data.data;
}

export const fetchLegalForms = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/misc/legal-forms`, options);
    return data.data;
}

export const fetchSpecializations = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/misc/specializations`, options);
    return data.data;
}