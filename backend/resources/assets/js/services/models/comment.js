/** @flow */

import { resolveFromRaw } from '.';
import Model from '~/support/wrappers/Model';
import Collection from '~/support/wrappers/Collection';
import * as utils from '~/support/helpers';

type PropTypes = BaseModelProps & {
    message: string,
    author: Authorizable,
    isDeleted: boolean,
    postedAt: string,
};

export default class Comment extends Model {
    message: string;
    author: Authorizable;
    isDeleted: boolean;
    postedAt: string;

    constructor(props: PropTypes) {
        super(props);

        this.message = props.message;
        this.author = props.author;
        this.isDeleted = Boolean(props.isDeleted);
        this.postedAt = props.postedAt;
    }

    static create(attrs: Object): Comment {
        const props: PropTypes = utils.camelKeys(attrs);
        return new Comment({
            ...props,
            author: props.author && resolveFromRaw(props.author)
        });
    }

    static collection(collection: Array<Object>): Collection<Comment> {
        return new Collection(collection.map((v) => Comment.create(v)));
    }
}