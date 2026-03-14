<template>
    <v-layout row wrap>
        <v-flex xs12 md6 xl4 lg4 v-for="contact in verified" :key="contact.id">
            <ContactCard
                :name="contact.name"
                :type="contact.entity"
                :employer="contact.employer"
                :address="contact.address.label"
                :registered="contact.status.confirmed"
                :relations="contact.relations"
                :avatar="contact.avatar.url"
                :key="contact.id"
                @click:title="titleClick(contact)"
                @menu:employment="$invitations.invite(contact, 'employment')"
                @menu:add="$invitations.invite(contact, 'contact')"
            />
        </v-flex>
    </v-layout>
</template>

<script>

import axios from 'axios'
import { merge } from 'lodash'
import * as apiCalls from '~/services/api/contact'
import ContactCard from '~/components/UI/Cards/ContactCard';
import { createResource } from 'vue-async-manager';
import { User } from '~/services/models';
import { UsesContactStore, HandlesInvitations } from '~/components/Mixins';

export default {
    components: { ContactCard },

    mixins: [
        UsesContactStore(),
        HandlesInvitations()
    ],

    methods: {

        titleClick({ id, entity }) {
            let view = 'User'
            if (entity == 'companies') 
                view = 'Company'
            else if (entity == 'employees')
                view = 'Employee'

            this.$modal.show({
                size: 'md',
                attrs: { id },
                modal: () => import(`~/components/UI/Modals/Profile/${view}`)
            })
        }
    }
}
</script>
