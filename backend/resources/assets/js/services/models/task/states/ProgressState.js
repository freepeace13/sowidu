
/** @flow */

import OpenState from './OpenState';
import ArchivedState from './ArchivedState';
import FinishedState from './FinishedState';
import InProgressState from './InProgressState';
import ModelState from '~/services/models/fundamentals/state';

export default class ProgressState {
    default: ModelState = new OpenState;

    states: Array<ModelState> = [
        new OpenState,
        new InProgressState,
        new FinishedState,
        new ArchivedState
    ];

    steps: Array<ModelState> = [
        new OpenState,
        new InProgressState,
        new FinishedState,
    ];
}