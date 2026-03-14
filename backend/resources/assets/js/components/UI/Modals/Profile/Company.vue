<template>
    <ModalLayout>
        <div class="px-3">
            <v-subheader>Company Information</v-subheader>
        <v-container fluid grid-list-lg>
            <v-layout row>
                <v-flex>
                    <div>Name: <strong>{{ company.name }} </strong></div>
                    <div>Address: {{ company.address.label || 'Not Set' }}</div>
                    <div>Founding date: {{ company.createdAt }}</div>
                    
                </v-flex>
                <v-flex justify-end>
                    <v-img
                        :lazy-src="company.avatar.url || ''"
                        :src="company.avatar.url || ''"
                        height="100%"
                        max-height="100"
                        max-width="100"
                    />
                </v-flex>
            </v-layout>
            <v-divider></v-divider>
        </v-container>
        <v-subheader>Company Details</v-subheader>
        <v-container fluid grid-list-lg>
            <v-layout row>
                <v-flex>
                    <div>Institution type: {{ company.institutionType || 'Not set' }}</div>
                    <div>No. of employees: <strong>{{ employees.length }} </strong></div>
                </v-flex>
                <v-flex>
                    <div>Legal form: {{ company.legal_form || 'Not set' }}</div>
                </v-flex>
            </v-layout>
            <v-layout row>
                <v-flex v-if="isBusy">
                    <AppLoader :visible="isBusy" :isFullPage="false"/>
                </v-flex>
                <v-flex v-else>
                    <div>Employees</div>
                    <v-list flat two-line>
                    <template v-for="employee in employees">
                        <v-divider></v-divider>

                        <v-list-tile>
                            <v-list-tile-content>
                                <v-list-tile-title>{{ employee.name }}</v-list-tile-title>
                                <v-list-tile-sub-title>{{ employee.specialization }}</v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </template>
                </v-list>
                </v-flex>
            </v-layout>
            <v-divider></v-divider>
        </v-container>
        <v-subheader>Founder Details</v-subheader>
        <v-container fluid grid-list-lg>
            <v-layout row>
                <v-flex>
                    <div>Name: {{ founder.name }}</div>
                    <div>Email: {{ founder.email }}</div>
                    <div>Status: <strong>{{ founder.status.confirmed ? 'VERIFIED' : 'UNVERIFIED' }} </strong></div>
                </v-flex>
                <v-flex>
                    <v-img
                        :lazy-src="founder.avatar.url"
                        :src="founder.avatar.url"
                        height="100%"
                        max-height="100"
                        max-width="100"
                    />
                </v-flex>
            </v-layout>
        </v-container>
            
        </div>

        <template v-slot:actions>
            <v-btn color="primary" block v-if="addressbookable">Add to addressbook</v-btn>
            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">Okay</v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import axios from 'axios'
import CompanyService from '~/services/CompanyService'
import AppLoader from '~/components/UI/Loaders/AppLoader'
import Company from '~/services/models/company'
import Employee from '~/services/models/employee'
import ActionButton from './Mixins/ActionButton'

/**
 * How to use
 * this.$modal.show({
 * size: 'md',
 * title: 'Company information',
 * attrs: {
 * id: 2001, - the id of the company
 * },
 * modal: require('~/components/UI/Modals/Profile/Company').default
 * });
 */
export default {

    mixins: [ ActionButton ],

    components: { AppLoader },

    data: () => ({
        company: Company.create({}),
        isBusy: true,
        employees: Employee.collection([])
    }),

    methods: {

        async fetchEmployess() {
            this.isBusy = true
            this.employees = await CompanyService.employees(this.$attrs.id)
            this.isBusy = false
        },

        async fetch() {
            this.company = await CompanyService.retrieve(this.$attrs.id)
            this.fetchEmployess()
        },
    },

    computed: {

        founder() {
            return this.company.founder
        },

        relations() {
            return this.company.relations
        }
    },

    mounted() {
        this.fetch()
    },
};
</script>
