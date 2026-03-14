/** @flow */

import hexColors from '~/enums/hexColors';
import State from '../../fundamentals/state';

class PendingState extends State {
    key: string = 'pending';
    color: string = hexColors.LIGHT_GREEN;
}

export default PendingState;