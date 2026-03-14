import axios from 'axios';
import ServiceProvider from '@libs/ServiceProvider';
import Arr from '@common/utils/Arr';
import { Permission } from '~/services/models/fundamentals';

const value = (v) => (v instanceof Permission) ? v.name : v;
const names = (permissions) => permissions.map(value);

class AuthorizationServiceProvider extends ServiceProvider {
    async can(permission, options = {}) {
        const query = value(permission);
        return await axios.get(this.route(`/auth/access/?can=${query}`), options);
    }

    async any(permissions, options = {}) {
        const queries = Arr.query('any', names(permissions));
        return await axios.get(this.route(`/auth/access/?${queries}`), options); 
    }

    async all(permissions, options = {}) {
        const queries = Arr.query('all', names(permissions));
        return await axios.get(this.route(`/auth/access/?${queries}`), options); 
    }

    async allow(app, options = {}) {
        return await axios.get(this.route(`/auth/access/?allow=${app}`), options);
    }

    async allowCan(values, options = {}) {
        const [app, permission] = values;
        const route = this.route(`/auth/access/?allow=${app}&can=${value(permission)}`);

        return await axios.get(route, options);
    }
}

export const AuthorizationService = new AuthorizationServiceProvider;