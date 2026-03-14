<template>
    <v-list
        v-if="!permissions.members?.length"
        three-line
        subheader
    >
        <v-subheader class="tw-grid tw-grid-cols-1">
            <div class="mt-2">Manage Members permissions</div>
            <div
                v-if="!policies.can_update_permissions"
                class="caption red--text"
            >
                Only board admin can update this settings.
            </div>
        </v-subheader>
        <PermissionSwitch
            v-for="(value, permission) in permissions"
            :key="permission"
            :label="labels[permission]?.label"
            :details="labels[permission]?.details"
            :value="value"
            :permission="permission"
            :disabled="!policies.can_update_permissions"
            role="members"
        />
    </v-list>
</template>
<script>
import PermissionSwitch from './PermissionSwitch.vue'
export default {
    components: { PermissionSwitch },
    props: {
        permissions: {
            type: Object,
            default: () => ({}),
        },
        policies: {
            required: false,
            type: Object,
        },
    },

    computed: {
        labels() {
            return {
                can_manage_task: {
                    label: 'Manage task',
                    details: 'Members can create, update or delete task.',
                },
                can_comment: {
                    label: 'Commenting',
                    details:
                        'Members can comment on tasks that they are not assigned.',
                },
                can_manage_subscriber: {
                    label: 'Manage member',
                    details: 'Members can add or remove board subscribers.',
                },
                can_manage_group: {
                    label: 'Manage group',
                    details: 'Members can create or delete board groups.',
                },
                can_manage_label: {
                    label: 'Manage label',
                    details: 'Members can manage board labels.',
                },
            }
        },
    },
}
</script>
