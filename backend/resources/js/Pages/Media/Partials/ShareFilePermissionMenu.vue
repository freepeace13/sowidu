<template>
    <v-menu
        v-model="isShow"
        :position-x="x"
        :position-y="y"
    >
        <v-list class="py-0">
            <v-list-tile
                v-for="[text, key] of Object.entries(permissionTypes)"
                :key="`permission_${member?.id}_type_${key}`"
                @click="$emit('click:modify-permission', { member, key })"
            >
                <v-list-tile-avatar>
                    <v-icon>
                        {{
                            member?.sharing?.permission == key
                                ? 'check_circle'
                                : 'radio_button_unchecked'
                        }}
                    </v-icon>
                </v-list-tile-avatar>

                <v-list-tile-content>
                    {{ text }}
                </v-list-tile-content>
            </v-list-tile>

            <v-divider />

            <v-list-tile @click="$emit('click:remove', member)">
                <v-list-tile-content>
                    {{ $t('buttons.remove') }}
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
    </v-menu>
</template>
<script>
import IsDynamicMenu from '@/Mixins/IsDynamicMenu'

export default {
    mixins: [IsDynamicMenu],

    props: {
        permissionTypes: {
            type: Object,
            required: true,
        },
        policies: {
            required: true,
            type: Object,
        },
    },

    data: () => ({
        member: null,
    }),

    computed: {
        isDisabled() {
            return (
                this.isAuthenticator(this.member) ||
                !this.policies.can_modify_permission ||
                this.member?.is_company_owner
            )
        },
    },

    watch: {
        isShow(newVal) {
            if (!newVal) {
                this.member = null
            }
        },
    },

    methods: {
        show(e, member) {
            e.preventDefault()
            this.isShow = false
            this.member = member
            this.x = e.clientX
            this.y = e.clientY
            this.$nextTick(() => {
                this.isShow = true
            })
        },

        isAuthenticator() {
            const user = this.member
            if (!user) return
            const { authenticator } = this.$page.props.user

            return (
                authenticator.id === user.id &&
                authenticator.user_type === user.user_type
            )
        },
    },
}
</script>
