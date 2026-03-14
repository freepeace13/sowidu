import { AuthorizationService } from '@features/auth/api';

export const can = (permission) =>
    function (to, from, next) {
        AuthorizationService
            .can(permission)
            .then(() => next())
            .catch((errors) => next(errors));
    }

export const any = (permissions) =>
    function (to, from, next) {
        AuthorizationService
            .any(permissions)
            .then(() => next())
            .catch((errors) => next(errors));
    }

export const all = (permissions) =>
    function (to, from, next) {
        AuthorizationService
            .all(permissions)
            .then(() => next())
            .catch((errors) => next(errors));
    }

export const allow = (app) =>
    function (to, from, next) {
        AuthorizationService
            .allow(app)
            .then(() => next())
            .catch((errors) => next(errors));
    }

export const allowCan = (app, permission) =>
    function (to, from, next) {
        AuthorizationService
            .allowCan([app, permission])
            .then(() => next())
            .catch((errors) => next(errors));
    }