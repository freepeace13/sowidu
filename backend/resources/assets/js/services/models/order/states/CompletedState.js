/** @flow */

import hexColors from '~/enums/hexColors';
import State from '../../fundamentals/state';

class CompletedState extends State {
    key: string = 'completed';
    color: string = hexColors.LIME;
}

export default CompletedState;