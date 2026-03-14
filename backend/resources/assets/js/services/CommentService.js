/** @flow */

import axios from 'axios';
import Comment from '~/services/models/comment';
import ServiceProvider from '@libs/ServiceProvider';

export class CommentService extends ServiceProvider {
    async create({ message }: Object): Promise<Comment> {
        const url = this.route('/comments');
        const { data } = await axios.post(url, { message }, {});
        return Comment.create(data.data);
    }
}

export default new CommentService;