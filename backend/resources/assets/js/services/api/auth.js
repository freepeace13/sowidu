/** @flow */

import { post, get, patch } from 'axios';
import type { LoginCredentials } from 'auth-login-payload';

export const confirmPassword = async (
    password: string,
    options: Object = {}
): Promise<any> => {
    return await post('/api/password/unlock', { password }, options);
}

export const userLogin = async (
    credentials: LoginCredentials,
    options: Object = {}
): Promise<any> => {
    const { data } = await post('/api/auth/login', credentials, options);
    return data.data;
}

export const companyLogin = async (
    companyId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await post(`/api/companies/${companyId}/login`, {}, options);
    return data.data;
}

export const companyLogout = async (
    options: Object = {}
): Promise<any> => {
    return await get('/api/company/logout', options);
}

export const register = async (
    payload: Object,
    options: Object = {}
): Promise<any> => {
    const { data } = await post('/api/register', payload, options);
    return data.data;
}

export const fetchProfile = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/auth/profile', options);
    return data.data;
}

export const fetchCompanies = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/auth/companies', options);
    return data.data;
}

export const fetchPermissions = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/auth/permissions', options);
    return data.data;
}