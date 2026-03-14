import { helpers } from '@libs/core';
import { Model, Collection } from '@libs/core';
import router from '../router';

export default class Notification extends Model {
    type: string;
    isUnread: boolean;
    message: string;
    avatar: string;
    meta: Map;

    constructor(props: Object = {}) {
        super(props);

        this.meta = new Map;

        this.type = props.type;
        this.isUnread = !!props.isUnread;
        this.message = props.message;
        this.avatar = props.avatar;

        this.__setMeta(props.meta);
    }

    __setMeta(meta) {
        if (meta instanceof Map) {
            return this.meta = meta;
        }

        Object.keys(Object.assign({}, meta)).map((key) => {
            this.meta.set(key, meta[key]);
        });
    }

    route() {
        return router[this.type](this);
    }

    static create(attrs: Object = {}) {
        return new Notification(helpers.camelKeys(attrs));
    }

    static collection(collection: Array<Object> = []) {
        return new Collection(collection.map((v) => Notification.create(v)));
    }
}