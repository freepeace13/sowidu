<template>
    <v-toolbar height="64" card prominent>
        <v-toolbar-items>
            <v-tabs
                height="64"
                slider-color="grey"
                color="transparent"
                active-class="font-weight-bold"
            >
                <v-tab exact replace :to="{
                    name: 'contacts.public',
                    query: $route.query
                }">
                    Public
                </v-tab>

                <v-tab exact replace :to="{
                    name: 'contacts.addressbook',
                    query: $route.query
                }">
                    Addressbook
                </v-tab>
                
                <v-tab exact replace :to="{
                    name: 'contacts.invitations',
                    query: $route.query,
                }">
                    Invitations
                </v-tab>
            </v-tabs>
        </v-toolbar-items>

        <v-spacer></v-spacer>

        <template v-if="$route.name !== 'contacts.invitations'">
            <TypeMenu
                @click:user="$modals.createUser()"
                @click:company="$modals.createCompany()"
                @click:employee="$modals.createEmployee()"
            />

            <v-divider vertical inset class="mx-3"></v-divider>

            <TypeButtonGroup
                v-can="allowCreateContact"
                :value="typeQuery"
                @change="$router.replace({
                    name: $router.currentRoute.name,
                    query: { type: $event }
                })"
            />
        </template>
    </v-toolbar>
</template>

<script>
import TypeMenu from '../components/TypeMenu';
import TypeButtonGroup from '../components/TypeButtonGroup';
import AcceptsTypeQueryProps from '../mixins/AcceptsTypeQueryProps';
import * as ContactEnums from '../enums';

import {
    showCompanyContact,
    showUserContact,
    showEmployeeContact
} from '~/services/events/modal';


export default {
    name: 'IndexTabs',

    mixins: [AcceptsTypeQueryProps()],

    components: {
        TypeMenu,
        TypeButtonGroup
    },

    computed: {
        allowCreateContact() {
            return ContactEnums.PERMISSIONS.CREATE_CONTACT;
        }
    },

    created() {
        this.$modals = {
            createCompany: showCompanyContact,
            createUser: showUserContact,
            createEmployee: showEmployeeContact
        };
    }
}
</script>