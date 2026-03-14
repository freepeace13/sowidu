/** @flow */

import * as types from './constants';
import { Invitation, User } from '~/services/models';
import { InvitationMessage } from '~/support/wrappers';
import InvitationService from '~/services/InvitationService';
import Cache from '~/services/cache';

export default {
    namespaced: true,

    state: {
        invitations: []
    },

    actions: {
        async all({ commit, state }: Object): Promise<any> {
            // await Cache.remember('invitations', 3600, async () => {
            //     const result = await InvitationService.all();
            //     commit(types.SET_INVITATIONS, result);
            // });
            const result = await InvitationService.all();
            commit(types.SET_INVITATIONS, result);
            return result;
        },

        async accept({ commit, dispatch }: Object, instance: Invitation): Promise<void> {
            try {
                const result: Invitation = await InvitationService.accept(instance.id);
                commit(types.INVITATION_UPDATE, result);
                Cache.forget('auth.companies');
                dispatch('auth/fetchCompanies', null, { root: true });
            } catch (error) {
                throw error;
            }
        },

        async invite({ commit }: Object, invitation: InvitationMessage): Promise<void> {
            await InvitationService.invite(invitation);
        }
    },

    mutations: {
        [types.SET_INVITATIONS] (state: Object, invitations: Array<Invitation>): void {
            state.invitations = invitations;
        },

        [types.INVITATION_UPDATE] (state: Object, invitation: Invitation): void {
            state.invitations = Invitation
                .collection(state.invitations)
                .updateOrInsert(invitation)
                .all();
        }
    }
}