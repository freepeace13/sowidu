<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import AccountPageLayout from './AccountPageLayout.vue'
import { useForm } from '@inertiajs/vue2'
import { computed, watch } from 'vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'

export default {
    layout: [AuthLayout, AccountPageLayout],
}
</script>
<script setup>
import InputSelect from '../../Components/InputSelect.vue'

const props = defineProps({
    role: {
        required: false,
        type: String,
        default: null,
    },
    roles: {
        type: Array,
        default: () => [],
    },
    permissions: {
        type: [Object, Array],
        required: false,
        default: () => ({
            'add member': false,
            'change avatar': false,
            'manage permissions': false,
            'update settings': false,
        }),
    },
    rolePermissions: {
        type: [Object, Array],
        required: false,
        default: () => ({}),
    },
})

const { $route } = useGlobalVariables()

const form = useForm({
    role: props.role,
    permissions: props.rolePermissions,
})

const isFounder = computed(() => form.role?.toLocaleLowerCase() === 'founder')

watch(props.permissions, (permissions) => {
    form.permissions = permissions
})

function toggle(role) {
    form.put(
        $route('account.access.roles.permissions.update', {
            role,
        }),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['rolePermissions'],
        },
    )
}
</script>
<template>
    <div>
        <div class="mb-5">
            <v-layout
                align-center
                justify-space-between
            >
                <v-flex class="headline shrink font-weight-bold">
                    {{ $t('buttons.manage-access') }}
                </v-flex>
            </v-layout>

            <v-divider class="mb-3" />

            <div class="tw-text-sm mb-3">
                {{ $t('messages.select-role-and-assign-permissions') }}
            </div>

            <v-card
                color="transparent"
                flat
            >
                <v-layout
                    row
                    wrap
                >
                    <v-flex
                        xs12
                        sm6
                    >
                        <input-select
                            v-model="form.role"
                            :label="$t('labels.inputs.role')"
                            :error-messages="form.errors.role"
                            :items="roles"
                            class="text-capitalize"
                            clearable
                            required
                            hide-details
                            :menu-props="{
                                contentClass: 'text-capitalize',
                            }"
                            @change="
                                $inertia.reload({
                                    only: ['rolePermissions'],
                                    data: {
                                        role: $event,
                                    },
                                    onSuccess: ({
                                        props: { rolePermissions },
                                    }) => {
                                        form.permissions = rolePermissions
                                    },
                                })
                            "
                        />
                    </v-flex>
                </v-layout>

                <v-layout
                    v-if="form.role"
                    row
                    wrap
                >
                    <v-flex
                        v-if="isFounder"
                        xs12
                        sm12
                    >
                        <v-alert
                            :value="true"
                            type="warning"
                        >
                            {{ $t('messages.default-role-note') }}
                        </v-alert>
                    </v-flex>
                    <v-flex
                        xs12
                        mt-2
                    >
                        <VList subheader>
                            <v-list-group
                                v-for="groupPermission in permissions"
                                :key="groupPermission.label"
                                no-action
                            >
                                <template #activator>
                                    <v-list-tile>
                                        <v-list-tile-content>
                                            <v-list-tile-title>
                                                <div
                                                    class="tw-flex tw-items-center"
                                                >
                                                    <v-icon
                                                        class="mr-2"
                                                        color="primary"
                                                    >
                                                        {{
                                                            groupPermission.icon
                                                        }}
                                                    </v-icon>
                                                    <div>
                                                        {{
                                                            groupPermission.label
                                                        }}
                                                    </div>
                                                </div>
                                            </v-list-tile-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </template>

                                <v-list-tile
                                    v-for="(
                                        permission, permissionKey
                                    ) in groupPermission.permissions"
                                    :key="permissionKey"
                                >
                                    <v-list-tile-content>
                                        <v-list-tile-title class="">
                                            {{ permission }}
                                        </v-list-tile-title>
                                    </v-list-tile-content>

                                    <v-list-tile-action>
                                        <v-switch
                                            v-model="
                                                form.permissions[permissionKey]
                                            "
                                            hide-details
                                            single-line
                                            :disabled="isFounder"
                                            :loading="form.processing"
                                            @change="
                                                toggle(permissionKey, $event)
                                            "
                                        />
                                    </v-list-tile-action>
                                </v-list-tile>
                            </v-list-group>
                        </VList>

                        <!-- <v-list
                            v-for="groupPermission in permissions"
                            :key="groupPermission.name"
                            subheader
                        >
                            <v-subheader class="!tw-px-0 !tw-text-lg">
                                <div class="tw-flex tw-items-start">
                                    <v-icon
                                        class="mr-2"
                                        :color="
                                            groupPermission?.color ??
                                            'blue-info'
                                        "
                                    >
                                        {{ groupPermission.icon }}
                                    </v-icon>
                                    <div>
                                        {{ groupPermission.label }}
                                    </div>
                                </div>
                            </v-subheader>
                            <VListTile
                                v-for="(
                                    permission, permissionKey
                                ) in groupPermission.permissions"
                                :key="permissionKey"
                            >
                                <v-list-tile-content>
                                    <v-list-tile-title class="tw-text-base">
                                        {{ permission }}
                                    </v-list-tile-title>
                                </v-list-tile-content>

                                <v-list-tile-action>
                                    <v-switch
                                        v-model="
                                            form.permissions[permissionKey]
                                        "
                                        class="mt-0"
                                        hide-details
                                        single-line
                                        :disabled="isFounder"
                                        @change="toggle(permissionKey, $event)"
                                    />
                                </v-list-tile-action>
                            </VListTile>
                            <v-divider class="mt-2 mb-1" />
                        </v-list> -->
                    </v-flex>
                </v-layout>
            </v-card>
        </div>
    </div>
</template>
