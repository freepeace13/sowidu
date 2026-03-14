<template>
    <v-layout row wrap>
        <v-flex xs12 md6 xl4 lg4 v-for="contact in collection" :key="contact.id">
            <ContactCard
                :name="contact.name"
                :type="contact.entity"
                :employer="contact.employer"
                :address="contact.address.label"
                :registered="contact.status.confirmed"
                :relations="contact.relations"
                :avatar="contact.avatar.url"
                :key="contact.id"
                @click:title="view(contact)"
                @menu:employment="$invitations.invite(contact, 'employment')"
                @menu:add="$invitations.invite(contact, 'contact')"
            />
        </v-flex>
    </v-layout>
</template>

<script>
import { FILTER_TYPES } from '../../enums';
import ContactCard from '../../components/ContactCard';
import UsesContactStore from '../../mixins/UsesContactStore';
import AcceptsTypeQueryProps from '../../mixins/AcceptsTypeQueryProps';
import { Company, User, Employee } from '~/services/models';

import {
    showCompanyContact, 
    showUserContact,
    showEmployeeContact
} from '~/services/events/modal';

export default {
    name: 'PublicIndexView',

    mixins: [
        UsesContactStore(),
        AcceptsTypeQueryProps()
    ],

    components: {
        ContactCard
    },

    computed: {
        collection() {
            if (this.typeQuery === FILTER_TYPES.RESOURCE.DEFAULT) {
                return this.contacts
            }
            
            return this.contacts.filter(({ entity }) => entity === this.typeQuery);
        }
    },

    methods: {
        view(instance) {
            if (instance instanceof Company) {
                showCompanyContact(instance.id);
            }

            else if (instance instanceof User) {
                showUserContact(instance.id);
            }

            else if (instance instanceof Employee) {
                showEmployeeContact(instance.id);
            }
        }
    }
}
</script>