/** @flow */

import Vuex from 'vuex';
import Invitation from '~/services/models/invitation';

export default (store: Vuex) => ({
    InvitationUpdate: (event: any) => {
        const invitation = Invitation.create(event)
        store.commit('invitation/INVITATION_UPDATE', invitation);
    },
});