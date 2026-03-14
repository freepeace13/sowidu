<template>
    <v-card color="grey darken-4">
        <div class="pa-5">
            <v-layout row v-if="!isShowRoleForm">
                <v-flex xs12>
                    <v-subheader class="px-0 headline">Set permissions</v-subheader>
                    <v-select
                        :items="roles"
                        label="Choose role"
                        item-text="name"
                        class="text-capitalize"
                        v-model="role"
                        return-object
                    >
                        <template v-slot:prepend-item>
                            <v-list-tile
                                ripple
                                @click="willCreateRole"
                            >
                                <v-list-tile-content>
                                    <v-list-tile-title class="blue--text">
                                        <v-icon class="blue--text">add</v-icon> Add new role
                                    </v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider class="mt-2"></v-divider>
                        </template>
                        <template v-slot:item="{ item }">
                            <span>{{ item.name }}</span>
                            <a class="action-icons pr-5" @click="updateRole(item)"><v-icon small>edit</v-icon></a>
                            <a class="action-icons" @click="deleteRole(item)"><v-icon color="red" small>delete</v-icon></a>
                        </template>
                    </v-select>
                </v-flex>
            </v-layout>

            <v-layout row v-else>
                <v-flex xs12>
                    <v-subheader class="px-0 headline">{{ isUpdating ? 'Update' : 'Create New'}} Role</v-subheader>
                    <TextField v-model="form.name" label="Name" :errors="errors.name"></TextField>
                </v-flex>
            </v-layout>

            <span v-show="!!role.id && !isShowRoleForm">
                <v-divider></v-divider>
                <v-subheader class="px-0">Set permissions</v-subheader>
                <v-layout row wrap>
                    <v-flex xs4 v-for="permission of permissions" :key="permission.id">
                        <v-switch
                            :input-value="roleHasPermissionTo(role, permission)"
                            class="text-capitalize"
                            @change="toggleRolePermission(role, permission)"
                            :label="permission.name">
                        </v-switch>
                    </v-flex>
                </v-layout>
            </span>

            <v-layout row class="mt-5" v-show="isShowRoleForm">
                <v-flex>
                    <v-btn :loading="loading" block color="primary" @click="submit">
                        {{ isUpdating ? 'Update' : 'Save'}}
                    </v-btn>
                </v-flex>
                <v-flex>
                    <v-btn block color="grey darken-3" @click="isShowRoleForm = false">
                        Cancel
                    </v-btn>
                </v-flex>
            </v-layout>
        </div>
    </v-card>

</template>

<script>

    import axios from 'axios'
    import { merge, some } from 'lodash'

    export default {
        data: () => ({
            roles: [],
            permissions: [],
            role: {
                id: null,
                name: null,
                permissions: []
            },
            errors: {},
            loading: false,
            isShowRoleForm: false,
            form: {
                name: null
            },
            isCreatingRole: false
        }),

        computed: {

            isUpdating() {
                return !this.isCreatingRole
            }
        },

        methods: {
            roleHasPermissionTo(role, permission) {
                if (this.isShowRoleForm)
                    return false
                return role.permissions.findIndex(e => e.id === permission.id) !== -1
            },

            async submit() {

                try {
                    this.loading = true
                    this.errors = {}

                    let form = { name: this.form.name }
                    let url = `/api/settings/roles`
                    let method = 'post'
                    if (this.isUpdating) {
                        url = `/api/settings/roles/${this.form.id}`
                        method = 'patch'
                    }
                    const { data } = await axios({
                        method: method,
                        url: url,
                        data: form
                    })

                    this.fetchRoles()
                    this.role = data.data
                    this.$events.$emit('alert', this.isUpdating ? 'Role has been updated.' : 'New role has been added.')

                    this.isShowRoleForm = false
                    this.isCreatingRole = false
                } catch (e) {
                    this.errors = e.errors || {}
                } finally {
                    this.loading = false
                }
            },

            async fetchRoles() {
                const { data: { data } } = await axios.get(`/api/settings/roles`)
                this.roles = data
            },

            async fetchPermissions() {
                const { data: { data } } = await axios.get(`/api/settings/permissions`)
                this.permissions = data
            },

            toggleRolePermission(role, permission) {
                if (this.roleHasPermissionTo(role, permission)) {
                    this.revokeRolePermissionTo(role, permission)
                } else {
                    this.giveRolePermissionTo(role, permission)
                }
            },

            async giveRolePermissionTo(role, permission) {
                if (role.id && !this.roleHasPermissionTo(role, permission)) {
                    const { data } = await axios.post(`/api/settings/roles/${role.id}/permissions/${permission.id}`)
                    this.role = data.data
                }
            },

            async revokeRolePermissionTo(role, permission) {
                if (role.id && this.roleHasPermissionTo(role, permission)) {
                    const { data } = await axios.delete(`/api/settings/roles/${role.id}/permissions/${permission.id}`)
                    this.role = data.data
                }
            },

            async updateRole(role) {
                this.form = role
                this.isShowRoleForm = true
            },

            async deleteRole(role) {
                await this.$confirm.ask({
                        question: `Are you sure you want to delete this role?`,
                        confirm: () => {
                            this.delete(role)
                        },
                    })

            },

            async delete({ id }) {
                const { data } = await axios.delete(`/api/settings/roles/${id}`)
                this.$events.$emit('alert', 'Role has been deleted.')
                this.fetchRoles()
                this.role = {
                    id: null,
                    name: null,
                    permissions: []
                }
            },

            willCreateRole() {
                this.isShowRoleForm = true
                this.isCreatingRole = true
                this.form.name = null
            }
        },

        mounted() {
            this.fetchRoles()
            this.fetchPermissions()
        }
    }

</script>

<style lang="scss" scoped>
    .action-icons {
        position: absolute;
        right: 15px;
    }
</style>
