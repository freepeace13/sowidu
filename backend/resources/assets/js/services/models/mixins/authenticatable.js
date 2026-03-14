/** @flow */

import Relational from './relational';
import type { AuthGroupStatus, RelationsType } from 'auth-prop-types';
import Avatarable from './avatarable';
import { Employee } from '~/services/models';

type PropTypes = {
    avatar?: any,
    status?: ?AuthGroupStatus,
    relations?: ?RelationsType
}

export default (
    Base: Function
) => class Authenticatable extends Avatarable(Relational(Base)) {
    status: AuthGroupStatus = {
        confirmed: false,
        authStatus: 'offline',
        skippedAddressAt: null
    };

    constructor(props: PropTypes): void {
        super(props);
        this.status = Object.assign(this.status, props.status);
    }

    notifiable() {
        return this.authEmployeeId
            ? Employee.create({ id: this.authEmployeeId })
            : this;
    }
}