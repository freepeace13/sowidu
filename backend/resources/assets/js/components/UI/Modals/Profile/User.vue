<template>
    <ModalLayout>
        <div class="px-3">
            <v-subheader>User Information</v-subheader>

            <v-container fluid grid-list-lg>
                <v-layout row>
                    <v-flex>
                        <div>Name: {{ user.name }}</div>
                        <div>Status: <strong>{{ user.status.confirmed ? 'VERIFIED' : 'UNVERIFIED' }} </strong></div>
                    </v-flex>
                    <v-flex justify-end>
                        <v-img
                            :lazy-src="user.avatar.url || ''"
                            :src="user.avatar.url || ''"
                            height="100%"
                            max-height="150"
                            max-width="150"
                        />
                    </v-flex>
                </v-layout>
                <v-divider></v-divider>
            </v-container>
            <v-subheader>Contact Details</v-subheader>
            <v-container fluid grid-list-lg>
                <v-flex>
                        <div>Address: {{ user.address.label || 'Not Set' }}</div>
                    </v-flex>
                <v-layout row>
                    <v-flex>
                        <div>Email: {{ user.email }}</div>
                    </v-flex>
                    <v-flex>
                        <div>Contact number: {{ user.mobile || 'Not set' }}</div>
                    </v-flex>
                </v-layout>
                <v-divider></v-divider>
            </v-container>
            <v-subheader>Employment History</v-subheader>
            <v-container fluid grid-list-lg>
                <v-flex v-if="isBusy">
                    <AppLoader :visible="isBusy" :isFullPage="false"/>
                </v-flex>
                <v-flex v-else>
                    <v-list flat two-line>
                        <template v-for="employment in employments">
                            <v-divider></v-divider>

                            <v-list-tile>
                                <v-list-tile-content>
                                    <v-list-tile-title>{{ employment.employer.name }}</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ employment.specialization }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </template>
                    </v-list>
                </v-flex>
            </v-container>
            
        </div>

        <template v-slot:actions>
            
            <v-btn
                color="primary"
                block
                v-if="addressbookable"
            >
                Add to addressbook
            </v-btn>
            <v-btn
                color="primary"
                block
                v-if="employeable"
            >
                Assign to company
            </v-btn>
            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">Okay</v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import axios from "axios"
import UserService from '~/services/UserService'
import AppLoader from '~/components/UI/Loaders/AppLoader'
import User from '~/services/models/user'
import ActionButton from './Mixins/ActionButton'

/**
 * How to use
 * this.$modal.show({
 * size: 'md',
 * title: 'User information',
 * attrs: {
 * id: 2001, - user id
 * },
 * modal: require('~/components/UI/Modals/Profile/User').default
 * });
 */
export default {

    mixins: [ ActionButton ],

    components: { AppLoader },

    data: () => ({
        user: User.create({}),
        isBusy: true,
        employments: []
    }),

    computed: {
        relations() {
            return this.user.relations
        }
    },

    methods: {
        async fetch() {
            this.user = await UserService.retrieve(this.$attrs.id)
            this.fetchEmploymentHistory()
        },

        async fetchEmploymentHistory() {
            this.isBusy = true
            this.employments = await UserService.employments(this.$attrs.id)
            this.isBusy = false
        }
    },

    mounted() {
        this.fetch()
    },
};
</script>
