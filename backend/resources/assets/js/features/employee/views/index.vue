<template>
    <v-list style="width:100%;">
        <v-list-tile avatar v-for="employee in employees" :key="employee.id">
            <v-list-tile-avatar>
                <img :src="employee.avatar.url">
            </v-list-tile-avatar>

            <v-list-tile-content>
                <v-list-tile-title>{{ employee.name }}</v-list-tile-title>
            </v-list-tile-content>
            <v-list-tile-action>
                <!-- <v-btn
                    flat
                    color="primary"
                    :to="{ name: 'employees.edit', params: { uuid: employee.uuid } }"
                >
                    <v-icon>edit</v-icon>
                </v-btn> -->

                <v-btn
                    flat
                    color="primary"
                    v-if="canChangePermissions"
                    @click="assignRolesPermissions(employee)"
                >
                    <v-icon>settings</v-icon>
                </v-btn>
            </v-list-tile-action>
        </v-list-tile>
    </v-list>
</template>

<script>
import { showPermissionsManagerModal } from '~/services/events/modal';
import { UsesEmployeeStore } from '~/components/Mixins';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    mixins: [UsesEmployeeStore(), DispatchWhenTokenChanges('employee/all')],

    computed: {
        canChangePermissions() {
            const authorizeTo = this.$store.getters['auth/authorize'];
            return authorizeTo('update employee permissions');
        }
    },

    methods: {
        assignRolesPermissions(employee) {
            showPermissionsManagerModal(employee.id);
        }
    }
}
</script>
