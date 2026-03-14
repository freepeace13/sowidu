import axios from 'axios';
import { isObject } from './support/helpers';
import { MessageBag } from './support/wrappers';
import wsAuthorizer from './services/store/websocket/authorizer';
import Pusher from 'pusher-js';
import Echo from 'laravel-echo';

import ServerError from './exceptions/ServerError'
import NotFoundError from './exceptions/NotFoundError'
import ConflictError from './exceptions/ConflictError'
import ValidationError from './exceptions/ValidationError'
import UnauthorizedError from './exceptions/UnauthorizedError'
import ForbiddenError from './exceptions/ForbiddenError'
import BadRequestError from './exceptions/BadRequestError';
import store from './services/store';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

axios.interceptors.request.use(function (config) {
    delete config.headers['Authorization'];
    delete config.headers['X-Primary-Id'];

    if (store.getters['auth/check']()) {
        const accessToken = store.getters['auth/token']();
        config.headers.common['Authorization'] = `Bearer ${accessToken}`;

        if (store.getters['auth/check']('company')) {
            const profile = store.getters['auth/profile']('user');
            config.headers.common['X-Primary-Id'] = profile.id;
        }
    }

    return config;
});

axios.interceptors.response.use((response) => {
        return response;
    }, function({ response }) {
        const { message, errors } = response.data;

        switch(response.status) {
            case 404:
                throw new NotFoundError(message)
            case 422:
                return Promise.reject(new MessageBag(message, errors));
            case 401:
                return Promise.reject(new UnauthorizedError(message));
            case 403:
                throw new ForbiddenError(message)
            case 400:
                throw new BadRequestError(message)
            case 409:
                throw new ConflictError(message)
            case 500:
                throw new ServerError('Internal Server Error')
        }
    }
);

window.axios = axios;
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: process.env.MIX_PUSHER_APP_PORT,
    wsPath: process.env.MIX_PUSHER_APP_PATH,
    disableStats: true,
    authorizer: wsAuthorizer,
    forceTLS: false,
    enabledTransports: ['ws']
});


if ((String.prototype.isEqual instanceof Function) === false) {
    String.prototype.isEqual = function(comparison) {
        return this === comparison
    }
}

if ((String.prototype.capitalize instanceof Function) === false) {
    String.prototype.capitalize = function() {
        return this.charAt(0).toUpperCase() + this.slice(1)
    }
}
