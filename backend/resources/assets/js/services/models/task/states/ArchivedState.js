/** @flow */

import hexColors from '~/enums/hexColors';
import State from '../../fundamentals/state';

class ArchivedState extends State {
    key: string = 'archived';
    color: string = hexColors.RED;
}

export default ArchivedState;