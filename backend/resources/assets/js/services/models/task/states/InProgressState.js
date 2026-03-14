/** @flow */

import hexColors from '~/enums/hexColors';
import State from '../../fundamentals/state';

class InProgressState extends State {
    key: string = 'in_progress';
    color: string = hexColors.LIGHT_BLUE;
}

export default InProgressState;