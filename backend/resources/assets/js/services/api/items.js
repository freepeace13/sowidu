/** @flow */

import { get, post, patch } from 'axios';
import { camelKeys } from '../../support/helpers';
import type { APIPayload } from 'item-api-payload';

export const fetchItemTypes = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/misc/item-types`, options);
    return data.data;
}

export const fetchItemUnits = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/misc/units`, options);
    return data.data;
}

export const fetchItem = async (
    itemId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/items/${itemId}`, options);
    return data.data;
}

export const createItem = async (
    payload: APIPayload,
    options: Object = {}
): Promise<any> => {
    const { data } = await post(`/api/items`, payload, options);
    return data.data;
}

export const fetchContractorItems = async (
    alias: string,
    id: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/${alias}/${id}/items`, options);
    return data.data;
}

export const fetchItems = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/items`, options);
    return data.data;
}

export const updateItem = async (
    itemId: number,
    payload: APIPayload,
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/items/${itemId}`, payload, options);
    return data.data;
}