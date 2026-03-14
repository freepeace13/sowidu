/** @flow */

import axios from 'axios';
import * as apiCalls from './api/invitation';
import { Invitation, User } from './models';
import { InvitationMessage } from '~/support/wrappers';
import { callAsync } from '~/support/helpers';
import ServiceProvider from '@libs/ServiceProvider';

export class InvitationService extends ServiceProvider {
    async all(): Promise<Array<Invitation>> {
        const url = this.route('/invitations');

        const result = await callAsync(async () => {
            const { data } = await axios.get(url);
            return data.data;
        });

        return result.map((v) => Invitation.create(v));
    }

    async accept(invitationId: number): Promise<Invitation> {
        const url = this.route(`/invitations/${invitationId}/accept`);
        const { data } = await axios.patch(url);
        return Invitation.create(data.data);
    }

    invite(invitation: InvitationMessage): Promise<void> {
        const url = this.route(`/invitations/send`);
        
        return axios.post(url, {
            user_id: invitation.recipient.id,
            invitation_type: invitation.type,
            note: invitation.message
        });
    }
}

export default new InvitationService;