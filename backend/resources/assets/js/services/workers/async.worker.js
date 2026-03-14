import * as Comlink from 'comlink';

const isFunction = (v) => typeof(v) === 'function';

function callAsync(asyncFunction, resolver = null, rejecter = null) {
    if (!isFunction(asyncFunction)) {
        asyncFunction = () => asyncFunction;
    }

    if (!isFunction(resolver)) {
        resolver = () => {};
    }

    if (!isFunction(rejecter)) {
        rejecter = () => {};
    }

    return Promise.resolve(asyncFunction())
        .then((result) => resolver(result))
        .catch((error) => rejecter(error));
}

Comlink.expose(callAsync);