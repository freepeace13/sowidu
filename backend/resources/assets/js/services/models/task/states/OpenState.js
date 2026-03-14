/** @flow */

import hexColors from '~/enums/hexColors';
import State from '../../fundamentals/state';

class OpenState extends State {
    key: string = 'open';
    color: string = hexColors.BLUE_GREY;
}

export default OpenState;