<template>
    <v-card flat color="transparent">
        <GroupSettingsWrapper
            title="Access Roles"
            subtitle="Lorem ipsum, dolor sit amet consectetur adipisicing elit. Possimus impedit commodi quis animi libero asperiores iure, quasi odio adipisci sunt distinctio et veritatis corporis itaque quo quam molestias blanditiis dolorem?"
        >
            <CreateRoleFormWidget
                v-can="allowCreateRolePermission"
                @created="roles = [$event, ...roles]"
            />

            <v-expansion-panel v-can="allowViewRolesPermission" expand>
                <v-expansion-panel-content
                    v-for="role in roles"
                    :key="`role_${role.id}`"
                >
                    <template v-slot:header>
                        <div class="subtitle">{{ role.name }}</div>
                    </template>

                    <RolePermissionsWidget
                        :role="role"
                        v-can="allowUpdateEmployeeRolesPermission"
                        @update:permissions="updateRolePermissions(role, $event)"
                        @deleted="roles = roles.filter(v => v.id !== $event.id)"
                    />
                </v-expansion-panel-content>
            </v-expansion-panel>
        </GroupSettingsWrapper>

        <GroupSettingsWrapper
            title="Members Permissions"
            subtitle="Lorem ipsum, dolor sit amet consectetur adipisicing elit. Possimus impedit commodi quis animi libero asperiores iure, "
            v-can="allowViewEmployeesPermission"
        >
            <v-list three-line>
                <v-list-tile v-for="member in employees" :key="`members_${member.id}`">
                    <v-list-tile-avatar>
                        <img :src="member.avatar.url" :lazy-src="member.avatar.url">
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title class="body-2" v-html="member.name" />
                        <v-list-tile-sub-title class="subtitle" v-html="member.email" />
                        <v-list-tile-sub-title class="caption">
                            Since {{ (new Date(member.createdAt)).toDateString() }}
                        </v-list-tile-sub-title>
                    </v-list-tile-content>
                    <v-list-tile-action
                        v-can="allowUpdateEmployeeRolesPermission"
                        v-if="!member.isFounder()"
                    >
                        <RolesSelector
                            placeholder="Roles"
                            :value="member.reference.accessRoles"
                            @input="updateMemberRoles(member, $event)"
                            color="primary"
                            :items="roles"
                        />
                    </v-list-tile-action>
                </v-list-tile>
            </v-list>
        </GroupSettingsWrapper>
    </v-card>
</template>

<script>
import UsesMiscStore from '@common/mixins/UsesMiscStore';
import InputTouchField from '@common/components/InputTouchField';
import InputField from '@common/components/InputField';
import { RoleService } from '../api';
import { createResource } from 'vue-async-manager';
import createContext from '@common/utils/ContextManager';
import CreateRoleFormWidget from '../widgets/CreateRoleForm.vue';
import RolePermissionsWidget from '../widgets/RolePermissions.vue';
import GroupSettingsWrapper from '../components/GroupSettingsWrapper';
import EmployeeService from '@features/employee/api';
import RolesSelector from '@features/auth/components/RolesSelector';
import UsesEmployeeStore from '@features/employee/mixins/UsesEmployeeStore';
import UsesAuthStore from '@features/auth/mixins/UsesAuthStore';
import { PERMISSIONS as RolePermissions } from '../enums';
import { PERMISSIONS as EmployeePermissions } from '@features/employee/enums';

export default {
    mixins: [
        UsesMiscStore(),
        UsesEmployeeStore(),
        UsesAuthStore()
    ],

    components: {
        GroupSettingsWrapper,
        InputTouchField,
        RolesSelector,
        InputField,
        CreateRoleFormWidget,
        RolePermissionsWidget
    },

    data: () => ({
        roles: [],
    }),

    computed: {
        allowCreateRolePermission() {
            return RolePermissions.CREATE_ROLE;
        },

        allowViewRolesPermission() {
            return RolePermissions.VIEW_ROLES;
        },

        allowViewEmployeesPermission() {
            return EmployeePermissions.VIEW_EMPLOYEES;
        },

        allowUpdateEmployeeRolesPermission() {
            return EmployeePermissions.UPDATE_EMPLOYEE_ROLES;
        }
    },

    methods: {
        async updateMemberRoles(member, roles) {
            const index = this.employees.findIndex((v) => v.id === member.id);

            if (index !== -1) {
                try {
                    await this.$store.dispatch('employee/updateAccessRoles', {
                        employeeId: member.id,
                        roles: roles
                    });
                } catch (error) {
                    this.$events.$emit('alert', 'Changing Permissions Failed!');
                }
            }
        },

        updateRolePermissions(role, permissions) {
            const index = this.roles.findIndex((v) => v.id === role.id);

            if (index !== -1) {
                role.permissions = permissions;
                this.roles.splice(index, 1, role);
            }
        }
    },

    created() {
        this.$rm = createResource(async () => {
            await this.$store.dispatch('employee/all');

            this.roles = await RoleService.all();
        });

        this.$watch((vm) => [
            vm.$store.getters['auth/token']('user'),
            vm.$store.getters['auth/token']('company')
        ], (value) => {
            this.$rm.read();
        }, { immediate: true });
    }
}
</script>