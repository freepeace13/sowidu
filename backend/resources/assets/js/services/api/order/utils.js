/** @flow */

import { ORDER_STATES_ROUTE_MAP } from '~/support/constants';

export const getStateRoute = (state: string) => {
    return ORDER_STATES_ROUTE_MAP[state];
}