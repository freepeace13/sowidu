/** @flow */

import { ModelCollection as Collection } from '~/support/wrappers';
import { Model } from '~/support/wrappers';
import { camelKeys } from '~/support/helpers';
import { resolveFromRaw, Employee } from '.';
import moment from 'moment';

type PropTypes = {
    id: number,
    sender: Authorizable,
    type: InvitationType,
    note: string,
    state: string,
    createdAt: string,
    updatedAt: string,
}

export default class Invitation extends Model {
    sender: Authorizable;
    type: InvitationType;
    note: string;

    constructor(props: PropTypes): void {
        super(props);

        this.sender = props.sender;
        this.type = props.type;
        this.note = props.note;
        this.state = props.state;
    }

    isPending() {
        return this.state === 'pending';
    }

    static create(attrs: Object): Invitation {
        const props: PropTypes = camelKeys(attrs);

        return new Invitation({
            ...props,
            sender: resolveFromRaw(props.sender)
        });
    }

    static collection(collection: Array<Object>): Collection<Invitation> {
        return new Collection<Invitation>(
            collection.map((v) => Invitation.create(v))
        );
    }
}