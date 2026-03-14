/** @flow */

import hexColors from '~/enums/hexColors';
import State from '../../fundamentals/state';

class FinalState extends State {
    key: string = 'final';
    color: string = hexColors.GREEN;
}

export default FinalState;