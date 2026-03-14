<template>
    <div class="mt-5">
        <EmptyMessage v-if="!requests.length" message="Contact request is empty"/>
        <v-list two-line v-else>
            <v-list-tile avatar v-for="request in requests" :key="request.id">
                <v-list-tile-avatar>
                  <img :src="request.requester.avatar">
                </v-list-tile-avatar>

                <v-list-tile-content>
                    <v-list-tile-title>
                        <strong>{{ request.requester.fullName }}</strong>

                        <template v-if="request.requester.is_employee">
                            of {{ request.requester.employer.fullName }}
                        </template>

                        sent you a contact request.
                        <small class="ml-3">{{ request.requested_last }}</small>
                    </v-list-tile-title>
                </v-list-tile-content>
                <v-list-tile-action>
                    <v-btn color="success" @click="onAccept(request)">Accept</v-btn>
                </v-list-tile-action>
            </v-list-tile>
        </v-list>
    </div>
</template>

<script>
    import axios from 'axios'
    import { mapGetters } from 'vuex'
    import store from '~/services/store'

    export default {
        data: () => ({
            requests: []
        }),

        computed: {
            ...mapGetters('auth/user', [
                'account'
            ])
        },

        methods: {
            async fetchRequests() {
                try {
                    const { uuid } = this.account
                    const { data } = await axios.get(`/api/users/${uuid}/contact-requests`)
                    this.requests = data.data
                } catch (e) {}
            },

            async onAccept({ id }) {
                try {
                    const { uuid } = this.account
                    await axios.patch(`/api/users/${uuid}/contact-requests/${id}/accept`)
                    await this.fetchRequests()
                } catch (e) {}
            }
        }
    }
</script>
