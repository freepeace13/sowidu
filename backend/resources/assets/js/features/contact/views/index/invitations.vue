<template>
    <v-list three-line>
        <v-list-tile v-for="invitation in invitations" :key="invitation.id">
            <v-list-tile-avatar>
                <v-img :src="invitation.sender.avatar.url" />
            </v-list-tile-avatar>
            <v-list-tile-content>
                <v-list-tile-title>
                    <a href="#">{{ invitation.sender.name }}</a>
                    sents you {{ invitation.type }} invitation.
                </v-list-tile-title>
                <v-list-tile-sub-title v-if="invitation.sender.employer">
                    {{ invitation.sender.specialization }}
                    &middot;
                    {{ invitation.sender.employer.name }}
                </v-list-tile-sub-title>
                <v-list-tile-sub-title class="grey--text">
                    "{{ invitation.note }}"
                </v-list-tile-sub-title>
            </v-list-tile-content>
            <v-list-tile-action>
                <v-list-tile-action-text>
                    {{ invitation.createdAt | humanize }}
                </v-list-tile-action-text>

                <v-btn
                    :disabled="!invitation.isPending()"
                    color="primary"
                    @click="$invitations.accept(invitation)"
                >
                    {{ invitation.isPending() ? 'Accept' : 'Accepted' }}
                </v-btn>
            </v-list-tile-action>
        </v-list-tile>
    </v-list>
</template>

<script>
import moment from 'moment';
import { HandlesInvitations } from '~/components/Mixins';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    name: 'PendingInvitations',

    mixins: [HandlesInvitations(), DispatchWhenTokenChanges('invitation/all')],

    filters: {
        humanize(value) {
            if (! value) return null;
            const momentObj = moment(value, 'YYYY-MM-DD HH:mm:ss');
            return moment.duration(momentObj.minutes(), 'minutes').humanize();
        }
    },

    computed: {
        invitations() {
            return this.$store.state.invitation.invitations;
        }
    }
}
</script>