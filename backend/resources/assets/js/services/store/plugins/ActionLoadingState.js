/** @flow */
import Vuex from 'vuex';
import matcher from 'matcher';
import { merge } from 'lodash';

const matchesIgnoredActions = (action: Object, only: Array<string>): boolean => {
    return only.some((patterns) => {
        return matcher.isMatch(action.type, patterns)
    });
}

export default function ActionLoadingState(
    ...allowedActionTypes: Array<string>
): Function {
    return (store: Vuex): void => {
        const loaderStart = (action: Object) => {
            if (matchesIgnoredActions(action, allowedActionTypes)) {
                store.dispatch('wait/start', action.type);
            }
        }

        const loaderEnd = (action: Object, state: Object, error?: ?Error) => {
            if (matchesIgnoredActions(action, allowedActionTypes)) {
                store.dispatch('wait/end', action.type);
            }
        }

        store.subscribeAction({
            before: loaderStart,
            after: loaderEnd,
            error: loaderEnd
        });
    }
}