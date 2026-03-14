/** @flow */

import { User, Invitation } from '~/services/models';
import { InvitationMessage } from '~/support/wrappers';
import { showNote } from '~/services/events/modal';
import { createContext } from '~/support/factories';
import type { Contactable } from '~/services/ContactService';
import UsesContactStore from '@features/contact/mixins/UsesContactStore';

export default () => ({
    mixins: [UsesContactStore()],

    created() {
        const { dispatch } = this.$store;
        const { $contacts } = this;

        this.$invitations = createContext({
            invite(contactable: Contactable, type: InvitationType) {
                if ((contactable instanceof User) === false) {
                    return $contacts.add(contactable);
                }

                showNote(null, (note) => dispatch('invitation/invite',
                    InvitationMessage[type]({ recipient: contactable, message: note })
                ));
            },

            async accept(instance: Invitation) {
                await dispatch('invitation/accept', instance);
            }
        })
    }
})