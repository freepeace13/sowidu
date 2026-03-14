/** @flow */

import Order from './Model';
import StatePolicies from '../policies/StatePolicies';

export default class Policies {
    model: Order;

    constructor(model: Order): void {
        this.model = model;
    }

    states(prop: string): StatePolicies<Order> {
        return new StatePolicies<Order>(this.model, prop);
    }

    isType(type: string): boolean {
        return this.model.type === type;
    }

    isConfirmable() {
        return this.isType('outgoing') && this.states('state').isTransitionableTo('pending');
    }

    isModifiable() {
        if (this.states('state').isNot('cancelled')) {

            if (this.isType('outgoing')) {
                return ! Boolean(this.model.contractor);
            }

            return this.states('state').isNot('completed');
        }

        return false;
    }
}