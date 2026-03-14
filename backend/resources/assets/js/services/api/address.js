/** @flow */

import { get, post, patch } from 'axios';
import type { APIPayload } from 'address-api-payload';

export const fetchActive = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/address/active', options);
    return data.data;
}

export const fetchAddresses = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/address', options);
    return data.data;
}

export const createAddress = async (
    payload: APIPayload,
    options: Object = {}
): Promise<any> => {
    const { data } = await post('/api/address', payload, options);
    return data.data;
}

export const updateAddress = async (
    addressId: number,
    payload: APIPayload,
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/address/${addressId}`, payload, options);
    return data.data;
}

export const remindLater = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await patch('/api/address/skip', {}, options);
    return data.data;
}

export const setActiveAddress = async (
    addressId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/address/${addressId}/set-as-active`, options);
    return data.data;
}

export const fetchCountries = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/misc/countries');
    return data.data;
}

export const createCountry = async (
    name: string,
    options: Object = {}
): Promise<any> => {
    const { data } = await post('/api/misc/countries', { name }, options);
    return data.data;
}

export const fetchZipcodes = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/misc/zipcodes', options);
    return data.data;
}

export const createZipcode = async (
    name: string | number,
    options: Object = {}
): Promise<any> => {
    const { data } = await post('/api/misc/zipcodes', { code: name }, options);
    return data.data;
}

export const fetchHouseNumbers = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/misc/house-numbers', options);
    return data.data;
}

export const createHouseNumber = async (
    name: string,
    options: Object = {}
): Promise<any> => {
    const { data } = await post('/api/misc/house-numbers', {
        house_number: name
    }, options);

    return data.data;
}

export const fetchStreets = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/misc/streets', options);
    return data.data;
}

export const createStreet = async (
    name: string,
    options: Object = {}
): Promise<any> => {
    const { data } = await post('/api/misc/streets', {
        street_address: name
    }, options);

    return data.data;
}

export const fetchStates = async (
    countryId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/misc/countries/${countryId}/states`, options);
    return data.data;
}


export const createState = async (
    countryId: number,
    name: string ,
    options: Object = {}
): Promise<any> => {
    const { data } = await post(`/api/misc/countries/${countryId}/states`, { name }, options);
    return data.data;
}


export const fetchCities = async (
    countryId: number,
    stateId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/misc/countries/${countryId}/states/${stateId}/cities`, options);
    return data.data;
}


export const createCity = async (
    countryId: number,
    stateId: number,
    name: string,
    options: Object = {}
): Promise<any> => {
    const { data } = await post(
        `/api/misc/countries/${countryId}/states/${stateId}/cities`,
        { name },
        options
    );

    return data.data;
}
