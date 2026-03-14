<template>
    <v-menu
        :close-on-content-click="false"
        offset-y
        bottom
        origin="top left"
        :nudge-left="$vuetify.breakpoint.xsOnly ? 0 : nudgeLeftValue"
        z-index="12"
    >
        <template #activator="{ on }">
            <div ref="accountButtonActivator">
                <v-btn
                    :small="$vuetify.breakpoint.xsOnly"
                    :icon="$vuetify.breakpoint.xsOnly"
                    flat
                    style="max-width: 383px"
                    v-on="on"
                >
                    <v-avatar :size="$vuetify.breakpoint.mdAndUp ? 30 : 26">
                        <v-img
                            :src="photo"
                            :lazy-src="photo"
                        />
                    </v-avatar>

                    <span class="ml-3 hidden-xs-only">{{ name }}</span>

                    <v-icon
                        v-if="$vuetify.breakpoint.smAndUp"
                        right
                    >
                        arrow_drop_down
                    </v-icon>
                </v-btn>
            </div>
        </template>
        <v-card
            :width="$vuetify.breakpoint.xs ? 'auto' : 350"
            :max-width="$vuetify.breakpoint.xs ? 'auto' : 350"
        >
            <UsersAccount v-bind="$props" />
        </v-card>
    </v-menu>
</template>

<script>
import UsersAccount from '../../Components/UsersAccount.vue'

export default {
    components: {
        UsersAccount,
    },

    props: {
        user: {
            type: Object,
            required: false,
            default: () => ({
                impersonating: false,
                photo: null,
                tenant: {
                    name: '',
                    photo: null,
                },
            }),
        },
        companies: {
            type: Array,
        },
        locale: {
            type: String,
        },
        locales: {
            type: Object,
        },
    },

    data: () => ({
        nudgeLeftValue: 0,
    }),

    computed: {
        photo() {
            return this.user.impersonating
                ? this.user.tenant.photo
                : this.user.photo
        },

        name() {
            return this.user.impersonating
                ? this.user.tenant.name
                : this.user.name
        },
    },

    mounted() {
        // Hack to center menu
        this.$nextTick(() => {
            const nudgeValue = (383 - 350) * 2
            this.nudgeLeftValue = nudgeValue < 0 ? 0 : nudgeValue
        })
    },
}
</script>
