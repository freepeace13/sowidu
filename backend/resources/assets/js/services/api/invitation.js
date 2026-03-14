/** @flow */

import { get, patch, post } from 'axios';

export const fetchInvitations = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/invitations', options);
    return data.data;
}

export const acceptInvitation = async(
    invitationId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/invitations/${invitationId}/accept`, options);
    return data.data; 
}

export const sendInvitation = async(
    payload: {
        user_id: number,
        note: string,
        invitation_type: InvitationType
    },
    options: Object = {}
): Promise<any> => {
    return await post(`/api/invitations/send`, payload, options);
}
