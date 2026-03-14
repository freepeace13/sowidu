/** @flow */

import { camelKeys } from '../helpers';
import ValidationError from '~/exceptions/ValidationError';

export default class MessageBag {
    message: string;
    validations: Object = {};

    constructor(message: string = "", errors: Object = {}): void {
        this.message = message;
        this.validations = errors;
    }

    has(prop: string): boolean {
        return Object.keys(this.validations).includes(prop);
    }

    get(prop: string, defValue: ?string | ?Array<string> = []): any {
        return this.validations[prop] || defValue;
    }

    toString(): ?string {
        return this.message;
    }

    static from(instance: ?ValidationError): MessageBag {
        let message = "";
        let errors = {}

        if (instance instanceof ValidationError) {
            message = instance.message;
            errors = instance.errors;
        }
        
        return new MessageBag(message, errors);
    }
}