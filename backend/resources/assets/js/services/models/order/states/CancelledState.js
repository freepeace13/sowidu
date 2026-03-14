/** @flow */

import hexColors from '~/enums/hexColors';
import State from '../../fundamentals/state';

class CancelledState extends State {
    key: string = 'cancelled';
    color: string = hexColors.RED;
}

export default CancelledState;