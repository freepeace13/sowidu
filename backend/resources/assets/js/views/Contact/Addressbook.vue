<template>
    <v-layout row wrap>
        <v-flex
            v-for="contact in contacts"
            :key="contact.id"
            xs12 md6 xl4 lg4
        >
            <ContactCard
                :name="contact.name"
                :type="contact.entity"
                :employer="contact.employer"
                :address="contact.address.label"
                :registered="contact.status.confirmed"
                :relations="contact.relations"
                :avatar="contact.avatar.url"

                @click:title="view(contact)"
                @click:photo="view(contact)"
                @menu:edit="view(contact)"
                @menu:remove="view(contact)"
            />
        </v-flex>
    </v-layout>
</template>

<script>
import {
    showCompanyContact, 
    showUserContact,
    showEmployeeContact
} from '~/services/events/modal';

import ContactCard from '~/components/UI/Cards/ContactCard';
import UsesContactStore from '~/components/Mixins/UsesContactStore';
import { Company, User, Employee } from '~/services/models';

export default {
    components: {
        ContactCard
    },

    mixins: [UsesContactStore()],

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
    },
}
</script>
