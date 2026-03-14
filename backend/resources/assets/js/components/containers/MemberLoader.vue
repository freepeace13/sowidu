<template>
    <div class="members">
        <v-progress-circular
            v-if="$rm.$loading"
            indeterminate
            color="primary"
        />

        <template v-else>
            <UserIcon
                v-for="member in $rm.$result"
                class="mr-2"
                :key="member.id"
                :url="member.avatar"
                :status="member.authStatus"
            >
                {{ member.fullName }}
            </UserIcon>
        </template>
    </div>
</template>

<script>
import { createResource } from 'vue-async-manager';
import { isEqual } from 'lodash';

export default {
    name: 'MemberLoader',

    props: {
        apiCall: {
            type: Function,
            required: true
        },

        reloader: {
            type: Function,
            required: true
        }
    },

    created() {
        this.$rm = createResource(() => this.apiCall());
        this.reloader(this.$rm.read);
    },

    mounted() {
        this.$rm.read();
    },
}
</script>