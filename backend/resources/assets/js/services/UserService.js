/** @flow */

import { User, Employee } from './models';
import * as apiCalls from './api/user';
import ServiceProvider from '@libs/ServiceProvider';
import axios from 'axios';

export class UserService extends ServiceProvider {
    async all(options: Object = {}): Promise<Array<User>> {
        const url = this.route('/users');
        const { data } = await axios.get(url, options);
        return data.data.map((v) => User.create(v));
    }

    async allExcept(value: string): Promise<Array<Employee>> {
        return await this.all({ params: { except: value } });
    }

    async retrieve(userId: number): Promise<User> {
        const { data } = await axios.get(this.route(`/users/${userId}`));
        return User.create(data.data);
    }

    async employments(userId: number): Promise<Array<Employee>> {
        const url = this.route(`/users/${userId}/employments`);
        const { data } = await axios.get(url);
        return data.data.map((employment) => Employee.create(employment));
    }
}

export default new UserService;