<template>
    <v-list-tile>
        <v-list-tile-avatar>
            <img :src="member.photo" />
        </v-list-tile-avatar>

        <v-list-tile-content>
            <v-list-tile-title>
                {{ member.name }}
            </v-list-tile-title>
            <v-list-tile-sub-title class="tw-flex">
                {{ member.email }}
                <span
                    v-if="member?.user_type != 'User'"
                    class="px-1"
                >
                    (
                    <span
                        v-for="role in member?.roles"
                        :key="`role-${member.id}-role-${role}`"
                        class="text-capitalize"
                    >
                        {{ role }}
                    </span>
                    )
                </span>
            </v-list-tile-sub-title>
        </v-list-tile-content>

        <v-list-tile-action>
            <v-btn
                flat
                :disabled="!canRemoveMembers"
                @click="(e) => $emit('click:permission-menu', { e, member })"
            >
                {{
                    getPermissionName(
                        member.sharing.permission,
                        member?.is_company_owner,
                    )
                }}
                <v-icon v-if="canRemoveMembers"> arrow_drop_down </v-icon>
            </v-btn>
        </v-list-tile-action>
    </v-list-tile>
</template>
<script>
import { findKey } from 'lodash'

export default {
    props: {
        member: {
            type: Object,
            required: true,
        },
        permissionTypes: {
            type: Object,
            required: true,
        },
        policies: {
            type: Object,
            required: true,
        },
    },
    computed: {
        canRemoveMembers() {
            return this.member.auth.can_remove
        },
    },
    methods: {
        getPermissionName(permission, isCompanyOwner) {
            if (isCompanyOwner) return 'Founder'

            return findKey(this.permissionTypes, (e) => e == permission)
        },
    },
}
</script>
