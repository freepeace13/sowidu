import { mapKeys, camelCase } from 'lodash'
import { objectToFormData } from 'object-to-formdata'

// TODO: Transfor utility functions or helpers to "support" directory

export const camelCaseKeys = (object) => (
    mapKeys({ ...object }, (v, k) => camelCase(k))
)

export const resolveResponseData = (response, resolver) => {
    return {
        ...response,
        data: {
            ...response.data,
            data: Array.isArray(response.data.data)
                ? response.data.data.map(e => resolver(e))
                : resolver(response.data.data)
        }
    }
}

export const transformResponseInto = (response) =>
    (model) => resolveResponseData(response, (resource) =>
        new model(resource)
    )

export const callWhenExists = (array = [], comparator = null, callback) => {
    let prop, propVal;

    if (Array.isArray(comparator) && comparator.length === 2) {
        prop = comparator[0]
        propVal = comparator[1]
    }

    if (! Array.isArray(comparator)) {
        propVal = comparator
    }

    let index = array.findIndex(entry => (prop ? entry[prop] : entry) === propVal)
    if (index !== -1) return callback(index)
}

export const callWhenNotExists = (array = [], comparator = null, callback) => {
    let prop, propVal;

    if (Array.isArray(comparator) && comparator.length === 2) {
        prop = comparator[0]
        propVal = comparator[1]
    }

    if (! Array.isArray(comparator)) {
        propVal = comparator
    }

    let index = array.findIndex(entry => (prop ? entry[prop] : entry) === propVal)
    if (index === -1) return callback(index)
}

export const formdata = (args) => {
    return objectToFormData(args, {
        nullsAsUndefineds: true
    })
}

export const postdata = (obj) => {
    return formdata(obj)
}

export const patchdata = (obj) => {
    const form = formdata(obj)
    form.append('_method', 'PATCH')
    return form
}

export const readonlyPropertyDesc = (value) => ({
    value, writable: false, enumerable: false, configurable: false
})
