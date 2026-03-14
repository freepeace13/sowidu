/** @flow */

import Task from './Model';
import StatePolicies from '../policies/StatePolicies';

export default class Policies {
    model: Task;

    constructor(model: Task) {
        this.model = model;
    }

    states(prop: string): StatePolicies<Task> {
        return new StatePolicies<Task>(this.model, prop);
    }
}