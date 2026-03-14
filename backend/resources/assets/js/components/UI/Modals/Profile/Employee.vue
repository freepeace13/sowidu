<template>
    <ModalLayout>
        <div class="px-3">
           <v-subheader>Employee Information</v-subheader>
        <v-container fluid grid-list-lg>
            <v-layout row>
                <v-flex>
                    <div>Name: {{ employee.name }}</div>
                    <div>Status: <strong>{{ employee.status.confirmed ? 'VERIFIED' : 'UNVERIFIED' }} </strong></div>
                    <div>Email: {{ employee.email }}</div>
                </v-flex>
                <v-flex justify-end>
                    <v-img
                        :lazy-src="employee.avatar.url || ''"
                        :src="employee.avatar.url || ''"
                        height="100%"
                        max-height="150"
                        max-width="150"
                    />
                </v-flex>
            </v-layout>
            <v-divider></v-divider>
        </v-container>
        <v-subheader>Employment Details</v-subheader>
        <v-container fluid grid-list-lg>
            <v-layout row>
                <v-flex>
                    <div>Company: <strong>{{ employer.name }} </strong></div>
                    <div>Position: {{ employee.specialization }}</div>
                    <div>Employment date: {{ employee.createdAt }}</div>
                </v-flex>
                <v-flex>
                    <v-img
                        :lazy-src="employer.avatar.url"
                        :src="employer.avatar.url"
                        height="100%"
                        max-height="100"
                        max-width="100"
                    />
                    
                </v-flex>
            </v-layout>
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
            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">Okay</v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import axios from "axios"
import EmployeeService from '~/services/EmployeeService'
import Employee from '~/services/models/employee'
import ActionButton from './Mixins/ActionButton'

/**
 * How to use
 * this.$modal.show({
 * size: 'md',
 * title: 'Employee information',
 * attrs: {
 * id: 2001, - employee id
 * },
 * modal: require('~/components/UI/Modals/Profile/Employee').default
 * });
 */
export default {

    mixins: [ ActionButton ],

    data: () => ({
        employee: Employee.create({}),
    }),

    methods: {
        async fetch() {
            this.employee = await EmployeeService.retrieve(this.$attrs.id)
        },
    },

    mounted() {
        this.fetch()
    },

    computed: {

        employer() {
            return this.employee.employer
        },

        relations() {
            return this.employee.relations
        }
    }
};
</script>
