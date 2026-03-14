/** @flow */

import { mapGetters, mapState } from 'vuex';
import { User, Company, Employee } from '~/services/models';
import { createContext } from '~/support/factories';
import type { Contactable } from '../api';
import ContactService from '../api';

export default () => ({
    computed: {
        ...mapGetters({
            verified: 'contact/verified',
            contacts: 'contact/addressbook'
        }),
        ...mapState({
            everyone: (state) => state.contact.contacts
        })
    },

    created() {
        this.$contacts = createContext({
            add(instance: Contactable) {
                return ContactService.add(instance);
            },
            create(instance: Contactable) {
                return ContactService.create(instance);
            },
            update(instance: Contactable) {
                return ContactService.update(instance);
            }
        });
    }
})