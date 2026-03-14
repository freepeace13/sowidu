/** @flow */

import * as utils from '~/support/helpers';
import ModelState from '../fundamentals/state';

type StatesConfig = {
    [key: string]: { default: ModelState, states: Array<ModelState> }
} 

type StatesPropType = {
    [key: string]: StateResource
}

type StateResource = {
    current: string,
    completedStates: Array<string>,
    completionRate: number,
    transitionableStates: Array<string>,
}

const value = (state: any) => (state instanceof ModelState) ? state.key : state;

export default (config: StatesConfig) => {
    const symsConfig = Symbol.for('states-config');
    const symsHydrator = Symbol.for('states-hydrator');

    const serializeState = (prop: string, resource: StateResource = {}) => ({
        current: value(resource.current) || value(config[prop].default),
        completionRate: resource.completionRate || 0,
        completedStates: utils.arrwrap(resource.completedStates).map(value),
        transitionableStates: utils.arrwrap(resource.transitionableStates).map(value)
    });

    const mapStatesToProp = (props: StatesPropType = {}) => Object.keys(config)
        .reduce((result, prop) => {
            result[prop] = serializeState(prop, props[prop]);
            return result;
        }, {});

    return (Base: Function) => class HasStates extends Base {
        states: StatesPropType;

        constructor(props: { states: StatesPropType }) {
            super(props);

            this.setConfig('states', config);

            this.states = mapStatesToProp(props.states);
        }
    }
}