import Vue from 'vue';
import { camelCase } from 'lodash';
import { singular } from 'pluralize';

export const capitalize = (value) => {
    if (!value) return ''
    value = value.toString()
    return value.charAt(0).toUpperCase() + value.slice(1)
}

export const lowercase = (value) => {
    if (!value) return ''
    return value.toString().toLowerCase()
}

export const uppercase = (value) => {
    if (!value) return ''
    return value.toString().toUpperCase()
}

export const truncate = (value, length) => {
    if (!value) return ''

    if (value.length < length) {
        return value
    }

    const ending = '...'

    return value.substring(0, length - ending.length) + ending
}

Vue.filter('lowercase', lowercase);
Vue.filter('capitalize', capitalize);
Vue.filter('uppercase', uppercase);
Vue.filter('truncate', truncate);
Vue.filter('singular', singular);
Vue.filter('camelcase', camelCase);