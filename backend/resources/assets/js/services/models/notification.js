/** @flow */

import { ModelCollection as Collection } from '~/support/wrappers';
import { Model } from '~/support/wrappers';
import { camelKeys } from '~/support/helpers';

type DataTypes = {
    avatar: string,
    title: string,
    subtitle: string
}

type PropTypes = {
    id: number,
    type: string,
    isUnread: boolean,
    data: DataTypes,
    createdAt: string
}

export default class Notification extends Model {
    type: string;
    isUnread: boolean;
    data: DataTypes;

    constructor(props: PropTypes): void {
        super(props);

        this.type = props.type;
        this.isUnread = props.isUnread || false;
        this.data = props.data;
    }

    static broadcasts(attrs: Object): Notification {
        const { id, type, ...data } = attrs;
        return Notification.create({ id, type, data });
    }

    static create(attrs: Object): Notification {
        const props: PropTypes = camelKeys(attrs);
        return new Notification(props);
    }

    static collection(collection: Array<Object>): Collection<Notification> {
        return new Collection<Notification>(
            collection.map((v) => Notification.create(v))
        );
    }
}