/** @flow */

import Comment from '../comment';
import * as utils from '~/support/helpers';

type PropTypes = {
    comments: Array<Comment>
}

export default (Base: Function) => class HasComments extends Base {
    comments: Array<Comment> = [];

    constructor(props: PropTypes) {
        super(props);
        this.comments = utils.arrwrap(props.comments).map((v) => Comment.create(v));
    }
}