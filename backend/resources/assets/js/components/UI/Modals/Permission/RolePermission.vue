<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-tabs
            v-model="tabs"
            centered
            color="grey darken-4"
            slider-color="grey"
            grow
        >
            <v-tab href="#tab-roles">Roles</v-tab>
            <v-tab href="#tab-direct-permissions">Direct Permissions</v-tab>
        </v-tabs>

        <v-container grid-list-lg fluid>
            <v-tabs-items v-model="tabs">
                <v-tab-item value="tab-roles">
                    <v-list class="grey darken-4">
                        <v-list-tile
                            class="grey darken-3 mb-2"
                            v-for="role in roles"
                            :key="role.id"
                        >
                            <v-list-tile-content>
                                <v-list-tile-title>{{ role.name }}</v-list-tile-title>
                            </v-list-tile-content>
                            <v-list-tile-action>
                                <v-switch
                                    :input-value="employee.hasRole(role)"
                                    @change="employee.toggleRole(role)"
                                    hide-details>
                                </v-switch>
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-list>
                </v-tab-item>
                <v-tab-item value="tab-direct-permissions">
                    <v-alert type="info" :value="true">
                        Disabled permissions are given via roles and cannot be updated here, go to permission settings instead.
                    </v-alert>
                    <v-list class="grey darken-4">
                        <v-list-tile
                            class="grey darken-3 mb-2"
                            v-for="permission in permissions"
                            :key="permission.id"
                        >
                            <v-list-tile-content>
                                <v-list-tile-title>{{ permission.name }}</v-list-tile-title>
                            </v-list-tile-content>
                            <v-list-tile-action>
                                <v-switch
                                    @change="employee.togglePermission(permission)"
                                    :input-value="employee.hasPermission(permission)"
                                    :disabled="employee.isPermissionViaRoles(permission)"
                                    hide-details>
                                </v-switch>
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-list>
                </v-tab-item>
            </v-tabs-items>
        </v-container>

        <template v-slot:actions>
            <v-btn
                color="primary"
                @click="save"
                block
            >
                Save
            </v-btn>

            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">
                Cancel
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import EmployeeService from '~/services/EmployeeService';
import { UsesSettingStore } from '~/components/Mixins';
import { Employee } from '~/services/models';

export default {
    mixins: [UsesSettingStore()],

    props: {
        employeeId: {
            type: Number,
            required: true
        }
    },

    data: () => ({
        tabs: 'tab-roles',
        employee: Employee.create({
            abilities: {
                roles: [],
                permissions: []
            }
        })
    }),

    methods: {
        async save() {
            await this.$store.dispatch('employee/update', {
                employeeId: this.employee.id,
                roles: this.employee.abilities.roles,
                permissions: this.employee.abilities.permissions
            });

            this.$modal.close(this.$vnode.key);
        }
    },

    async created() {
        this.employee = await EmployeeService.retrieve(this.employeeId);
    },
}
</script>
