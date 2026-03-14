/** @flow */

import ModelState from '../fundamentals/state';

const value = (state: any) => (state instanceof ModelState) ? state.key : state;

export default class StatePolicies<M: Object> {
    model: M;
    state: StateResource;

    constructor(model: M, prop: string): void {
        this.model = model;
        this.state = this.model.states[prop];
    }

    is(state: any): boolean {
        return this.state.current === value(state);
    }

    isNot(state: any): boolean {
        return ! this.is(state);
    }

    isOneOf(...states: Array<any>): boolean {
        return states.some((v) => value(v) === this.state.current);
    }

    isTransitionableTo(state: any): boolean {
        return this.state
            .transitionableStates
            .some((v) => v === value(state));
    }

    hasCompleted(state: any): boolean {
        return this.state
            .completedStates
            .some((v) => v === value(state));
    }
}