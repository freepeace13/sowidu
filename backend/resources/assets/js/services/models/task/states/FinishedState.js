/** @flow */

import hexColors from '~/enums/hexColors';
import State from '../../fundamentals/state';

class FinishedState extends State {
    key: string = 'finished';
    color: string = hexColors.GREEN;
}

export default FinishedState;