<template>
    <v-navigation-drawer
        class="transparent"
        stateless
        permanent
        floating
    >
        <v-list class="py-0 fill-height transparent" three-line>
            <v-list-tile
                :to="{ name: 'settings.account-info' }"
                active-class="grey darken-4"
                exact
            >
                <v-list-tile-content>
                    <v-list-tile-title class="subheading">
                        Account Information
                    </v-list-tile-title>
                    <v-list-tile-sub-title class="caption">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </v-list-tile>
            <v-list-tile
                :to="{ name: 'settings.address' }"
                active-class="grey darken-4"
                exact
            >
                <v-list-tile-content>
                    <v-list-tile-title class="subheading">
                        Address Settings
                    </v-list-tile-title>
                    <v-list-tile-sub-title class="caption">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </v-list-tile>
            <v-list-tile
                v-can:allow-can="allowAccessControl"
                :to="{ name: 'settings.access-control' }"
                active-class="grey darken-4"
                exact
            >
                <v-list-tile-content>
                    <v-list-tile-title class="subheading">
                        Access Control
                    </v-list-tile-title>
                    <v-list-tile-sub-title class="caption">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
        
    </v-navigation-drawer>
</template>

<script>
import * as EmployeeEnums from '@features/employee/enums';

export default {
    computed: {
        allowAccessControl() {
            return [
                EmployeeEnums.APP,
                EmployeeEnums.PERMISSIONS.VIEW_EMPLOYEES
            ];
        }
    }
}
</script>