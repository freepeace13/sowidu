/** @flow */

class ValidationError extends Error {
    errors: Object = {};
    name: string = 'ValidationError';
    status: number = 422;

    constructor(message: string, errors: Object) {
        super(message);
        this.errors = errors || {};
    }
}

export default ValidationError