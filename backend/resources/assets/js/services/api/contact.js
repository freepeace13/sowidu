/** @flow */

import axios, { post, patch, get } from 'axios';
import { postdata, patchdata } from '~/support/helpers';
import type { APIPayload } from 'contact-api-payload';

export const createByType = async (
    type: ContactType,
    payload: APIPayload,
    options: Object = {}
): Promise<any> => {
    const { data } = await post(`/api/contacts/${type}`, postdata(payload), options);
    return data.data;
}

export const updateByType = async (
    contactId: number,
    type: ContactType,
    payload: APIPayload,
    options: Object = {}
): Promise<any> => {
    const { data } = await post(`/api/contacts/${type}/${contactId}`, patchdata(payload), options);
    return data.data;
}

export const fetchContacts = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/contacts`, options);
    return data.data;
}

export const fetchEveryone = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/contacts?src=everyone`, options);
    return data.data;
}

export const fetchContact = async (
    contactId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/contacts/${contactId}`, options);
    return data.data;
}

export const search = async (
    searchText: string,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/contacts/search?q=${searchText}`, options);
    return data.data;
}

export const deleteById = async (
    contactId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await axios.delete(`/api/contacts/${contactId}`, options);
    return data.data;
}

export const addByType = async (
    contactableId: number,
    contactableType: ContactableType,
    options: Object = {}
): Promise<any> => {
    return await axios.post(`/api/contacts/${contactableType}/${contactableId}/add-to-addressbook`, options);
}