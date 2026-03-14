/** @flow */

import hexColors from '~/enums/hexColors';
import State from '../../fundamentals/state';

class DoneState extends State {
    key: string = 'done';
    color: string = hexColors.LIGHT_BLUE;
}

export default DoneState;