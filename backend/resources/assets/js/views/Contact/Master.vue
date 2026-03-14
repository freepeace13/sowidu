<template>
    <RootView title="Contacts" icon="library_books">
        <TwoColumnLayout>
            <SidebarLayout slot="sidebar">
                <v-list-tile avatar tag="div" :to="{ name: 'contacts.everyone' }">
                    <v-list-tile-avatar>
                        <v-icon>people</v-icon>
                    </v-list-tile-avatar>
                    <v-list-tile-title>Everyone</v-list-tile-title>
                </v-list-tile>

                <v-list-tile avatar tag="div" :to="{ name: 'contacts.addressbook' }">
                    <v-list-tile-avatar>
                        <v-icon>how_to_reg</v-icon>
                    </v-list-tile-avatar>
                    <v-list-tile-title>Addressbook</v-list-tile-title>
                </v-list-tile>

                <v-list-tile avatar tag="div" :to="{ name: 'contacts.invitations' }">
                    <v-list-tile-avatar>
                        <v-icon>group_add</v-icon>
                    </v-list-tile-avatar>
                    <v-list-tile-title>Pending Invitations</v-list-tile-title>
                </v-list-tile>
            </SidebarLayout>
            
            <FilterToolbar
                v-if="$route.name !== 'contacts.requests'"
                :filters="filters"
                @update:search="handleSearch"
                @update:filter="handleFilter"
            />

            <Suspense>
                <Loader class="loader" slot="fallback" />
                <router-view ref="view"></router-view>
            </Suspense>
        </TwoColumnLayout>

        <v-speed-dial
            :fixed="true"
            direction="left"
            :right="true"
            :bottom="true"
            transition="scale"
        >
            <v-btn color="primary" slot="activator" fab>
                <v-icon>add</v-icon>
            </v-btn>

            <v-btn @click="$modals.showUserContact()">ADD USER</v-btn>
            <v-btn @click="$modals.showCompanyContact()">ADD COMPANY</v-btn>
            <v-btn @click="$modals.showEmployeeContact()">ADD EMPLOYEE</v-btn>
        </v-speed-dial>
    </RootView>
</template>

<script>
import {
    showCompanyContact,
    showUserContact,
    showEmployeeContact
} from '~/services/events/modal'
import TwoColumnLayout from '~/components/layouts/TwoColumn';
import SidebarLayout from '~/components/layouts/Sidebar';
import FilterToolbar from '~/components/toolbars/contact/FilterToolbar';
import Loader from '~/components/UI/Loader';
import { UsesContactStore } from '~/components/Mixins';

export default {
    mixins: [UsesContactStore()],

    components: {
        TwoColumnLayout,
        SidebarLayout,
        FilterToolbar,
        Loader
    },

    computed: {
        filters() {
            return [
                { key: 'online', text: 'active' },
                { key: 'users', text: 'user' },
                { key: 'companies', text: 'company' },
                { key: 'employees', text: 'employee' },
            ];
        }
    },

    methods: {
        handleSearch(search) {
            if (search !== null && search.length) {
                this.$filters = { ...this.$filters, search };
            } else {
                delete this.$filters.search;
            }

            this.$refs.view.$rm.read(this.$filters);
        },

        handleFilter(filter) {
            if (['companies', 'employees', 'users'].indexOf(filter) !== -1) {
                this.$filters = { ...this.$filters, type: filter };
                delete this.$filters.online;
            } else {
                delete this.$filters.type;

                if (['online'].indexOf(filter) !== -1) {
                    this.$filters = { ...this.$filters, online: true };
                } else {
                    delete this.$filters.online;
                }
            }

            this.$refs.view.$rm.read(this.$filters);
        }
    },

    created() {
        this.$modals = {
            showCompanyContact,
            showUserContact,
            showEmployeeContact
        }
    }
}
</script>

<style scoped>
.loader {
    padding: 250px 0;
}
</style>