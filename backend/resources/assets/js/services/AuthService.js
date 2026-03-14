/** @flow */

import axios from 'axios';
import * as apiCalls from './api/auth';
import { Permission } from './models/fundamentals';
import type { LoginCredentials } from 'auth-login-payload';
import User from './models/user';
import Company from './models/company';
import { resolveFromRaw } from './models';
import * as utils from '~/support/helpers';
import ServiceProvider from '@libs/ServiceProvider';

export type RegistrationPayload = {
    firstName: string,
    lastName: string,
    password: any,
    passwordConfirmation: any,
    agreement: boolean,
    email?: ?string,
    phone?: ?string
}

export class AuthService extends ServiceProvider {
    async register(payload: RegistrationPayload): Promise<string> {
        const url = this.route('/register');
        const { data } = await axios.post(url, utils.snakeKeys(payload));
        return utils.camelKeys(data.data).verificationToken;
    }

    async profile(accessToken: string): Promise<Company | User> {
        const url = this.route('/auth/profile');

        const { data } = await axios.get(url, {
            headers: {
                'Authorization': `Bearer ${accessToken}`
            }
        });

        return resolveFromRaw(data.data);
    }

    async loginUser(credentials: LoginCredentials): Promise<string> {
        const url = this.route('/auth/login');
        const { data } = await axios.post(url, credentials);
        return utils.camelKeys(data.data).accessToken;
    }

    async loginCompany(companyId: number): Promise<string> {
        const { data } = await axios.post(this.route(`/companies/${companyId}/login`));
        return utils.camelKeys(data.data).accessToken;
    }

    async companies(): Promise<Array<Company>> {
        const { data } = await axios.get(this.route('/auth/companies'));
        return data.data.map((v) => Company.create(v));
    }

    async permissions(): Promise<Array<Permission>> {
        const { data } = await axios.get(this.route('/auth/permissions'));
        return data.data.map((v) => Permission.create(v));
    }

    async confirmPassword(password: string): Promise<void> {
        const url = this.route('/password/unlock');
        await axios.post(url, { password });
    }
}

export default new AuthService;