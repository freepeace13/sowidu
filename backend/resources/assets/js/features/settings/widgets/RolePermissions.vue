<template>
    <v-card>
        <v-card-text>
            <v-layout row wrap>
                <v-flex
                    xs3
                    v-for="permission in permissions"
                    :key="`role_${role.id}_permission_${permission.id}`"
                >
                    <v-list>
                        <v-list-tile>
                            <v-list-tile-content>
                                <v-list-tile-title class="text-capitalize">
                                    {{ permission.name }}
                                </v-list-tile-title>
                            </v-list-tile-content>
                            <v-list-tile-action>
                                <v-switch
                                    :input-value="role.permissions.some((v) => v.id === permission.id)"
                                    class="text-capitalize"
                                    @change="togglePermission(role, permission)"
                                    label="">
                                </v-switch>
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-list>
                </v-flex>
            </v-layout>
        </v-card-text>

        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn flat color="red" @click="handleDeleteRole(role)">
                <v-icon left>delete</v-icon> Delete
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import UsesMiscStore from '@common/mixins/UsesMiscStore';
import { RoleService } from '../api';
import { Role } from '~/services/models/fundamentals';

export default {
    mixins: [UsesMiscStore()],

    props: {
        role: {
            type: Role,
            required: true
        }
    },

    methods: {
        async handleDeleteRole(role) {
            try {
                await RoleService.delete(role.id);
                this.$events.$emit('alert', 'Role Deleted');
                this.$emit('deleted', role);
            } catch (error) {
                this.$events.$emit('alert', error.message);
                console.error(error);
            }
        },

        async togglePermission(role, permission) {
            const action = role.permissions.some((v) => v.id === permission.id)
                ? 'unsetPermission'
                : 'setPermission';
            
            try {
                const result = await RoleService[action](role.id, permission.id);
                this.$emit('update:permissions', result.permissions);
            } catch (error) {
                this.$events.$emit('alert', error.message);
                console.error(error);
            }
        },
    }
}
</script>