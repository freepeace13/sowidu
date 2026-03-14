declare module 'user-prop-types' {
    import type { AuthGroupStatus, RelationsType } from 'auth-prop-types';

    declare export type PropTypes = {
        id?: ?number,
        uuid?: ?string,
        email?: ?string,
        avatar?: ?string,
        mobile?: ?string,
        lastName?: ?string,
        firstName?: ?string,
        address: Address,
        status?: ?AuthGroupStatus,
        createdAt?: ?string,
        updatedAt?: ?string,
        $alias?: ?string,
        relations?: ?RelationsType
    }
}