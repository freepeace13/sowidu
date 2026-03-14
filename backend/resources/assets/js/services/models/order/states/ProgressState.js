/** @flow */

import PreparationState from './PreparationState';
import CompletedState from './CompletedState';
import PendingState from './PendingState';
import FinalState from './FinalState';
import DoneState from './DoneState';
import CancelledState from './CancelledState';
import ModelState from '~/services/models/fundamentals/state';

export default class ProgressState {
    default: ModelState = new PreparationState;

    states: Array<ModelState> = [
        new PreparationState,
        new CompletedState,
        new PendingState,
        new FinalState,
        new DoneState,
        new CancelledState
    ];

    steps: Array<ModelState> = [
        new PreparationState,
        new CompletedState,
        new PendingState,
        new FinalState,
        new DoneState,
    ];
}