/** @flow */

import { camelCase } from 'lodash';
import { isString, capitalize } from '~/support/helpers';

export default class State {
    key: string;

    get title() {
        return capitalize(this.key.replace(/_/i, ' '));
    }

    equals(state: any) {
        if (state instanceof State) {
            return state.key === this.key;
        }

        if (isString(state)) {
            return state === this.key;
        }

        return false;
    }
}