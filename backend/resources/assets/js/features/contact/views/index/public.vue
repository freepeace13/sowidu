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
                @click:title="titleClick(contact)"
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
import HandlesInvitations from '@common/mixins/HandlesInvitations';

export default {
    name: 'PublicIndexView',

    mixins: [
        UsesContactStore(),
        AcceptsTypeQueryProps(),
        HandlesInvitations()
    ],

    components: {
        ContactCard
    },

    computed: {
        collection() {
            if (this.typeQuery === FILTER_TYPES.RESOURCE.DEFAULT) {
                return this.verified;
            }
            
            return this.verified.filter(({ entity }) => entity === this.typeQuery);
        }
    },

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
                modal: require(`~/components/UI/Modals/Profile/${view}`).default
            })
        }
    }
}
</script>