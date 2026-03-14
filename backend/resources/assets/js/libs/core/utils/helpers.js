/** @flow */

import * as Comlink from 'comlink';
import Worker from '~/services/workers/async.worker';
import { isEmail, isNumeric } from 'validator';
import { objectToFormData } from 'object-to-formdata';
import { singular, plural } from 'pluralize';

import {
    isObject,
    mapKeys,
    camelCase,
    snakeCase,
    transform,
    cloneDeep,
    set,
    get,
    pick
} from 'lodash';

export { isEmail, isObject, singular, plural, cloneDeep, set, get, pick };

export function formdata(v: Object) {
    return objectToFormData(v, { nullsAsUndefineds: true });
}

export function isJson(v: any) {
    try {
        JSON.parse(v);
    } catch (e) {
        return false;
    }

    return true;
}

export function jsonParse(v: any) {
    return isJson(v) ? JSON.parse(v) : null;
}

export function getConstructor(v: Object) {
    const prototype = Object.getPrototypeOf(v);
    return prototype.constructor;
}

export function postdata(v: Object) {
    return formdata(v);
}

export function patchdata(v: Object) {
    const form = formdata(v);
    form.append('_method', 'PATCH');
    return form;
}

export function toRawType(v: any) {
    return Object.prototype.toString.call(v).slice(8, -1);
}

export function isFunction(v: any) {
    return typeof(v) === 'function';
}

export function isGenerator(v: any) {
    return toRawType(v) === 'Generator';
}

export function isNumber(v: any) {
    return isNumeric(new String(v));
}

export function isArray(v: any) {
    return Array.isArray(v);
}

export function capitalize(v: any) {
    if (typeof v !== 'string') return '';
    return v.charAt(0).toUpperCase() + v.slice(1);
}

export function keyHas(obj: Object, v: string) {
    return Object.keys(obj).some((i) => i.includes(v));
}

export function hasKey(obj: Object, v: string) {
    return Object.keys(obj).includes(v);
}

export function nullify(v: any) {
    return v === undefined ? null : v;
}

export function camelKeys(v: Object) {
    const value = Object.assign({}, v);
    return transform(value, (r, v, k) => {
        const key = !k.includes(':') ? camelCase(k) : k;

        r[key] = !(v instanceof Map)
            ? ((!isArray(v) && isObject(v)) ? camelKeys(v) : v)
            : v;

        return r;
    }, {});
}

export function snakeKeys(v: Object) {
    const value = Object.assign({}, v);
    return transform(value, (r, v, k) => {
        const key = !k.includes(':') ? snakeCase(k) : k;

        r[key] = !(v instanceof Map)
            ? ((!isArray(v) && isObject(v)) ? camelKeys(v) : v)
            : v;

        return r;
    }, {});
}

export function isNullOrUndefined(v: any) {
    return v === null || v === undefined;
}

export function def(v: any, def: any = null) {
    return isNullOrUndefined(v) ? def : v;
}

export function otherwise(v: any, d: any) {
    return v || d;
}

export function isString(v: any) {
    return typeof(v) === 'string' || v instanceof String;
}

export function isBoolean(v: any) {
    return typeof(v) === 'boolean';
}

export function isOneOf(refs: Array<mixed>, v: any) {
    return refs.some(item => item === v)
        || refs.some(item => isObject(item) && item instanceof v);
}

export function parseConstructorNames(...rules: Array<any>) {
    return rules.reduce((acc, val) => {
        let name = Object.getPrototypeOf(val).constructor.name;
        return !acc.length ? name : `${acc}, ${name}`;
    }, "");
}

export function arrwrap(v: any) {
    return Array.from<any>(new Set(v));
}

export function base64ToFile(v: string) {
    let ext = v.split(";")[0].split("/")[1],
        byteString = atob(v.split(",")[1]),
        ab = new ArrayBuffer(byteString.length),
        ia = new Uint8Array(ab)

    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i)
    }

    let blob = new Blob([ab])

    return new File([blob], "avatar", { type: "image/" + ext })
}

export const callAsync = (asyncFunction: Function): Promise<any> => {
    return new Promise((resolve, reject) => {
        Comlink.wrap(new Worker())(
            Comlink.proxy(asyncFunction),
            Comlink.proxy(resolve),
            Comlink.proxy(reject)
        );
    });
}

export const strUntil = (str: string, char: string): string => {
    return str.substr(0, str.indexOf(char));
}