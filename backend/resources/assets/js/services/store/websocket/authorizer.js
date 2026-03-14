/** @flow */

import axios from 'axios';

export default (channel: Object, options: Object) => ({
    authorize: async (socketId: string, callback: Function) => {
        try {
            console.log(options.authEndpoint)
            const response = await axios.post(options.authEndpoint, {
                socket_id: socketId,
                channel_name: channel.name
            });

            callback(null, response.data)
        } catch (error) {
            callback(error, { auth: "" });
        }
    }
});