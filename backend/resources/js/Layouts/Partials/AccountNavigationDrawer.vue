<template>
    <v-navigation-drawer
        class="border"
        floating
        permanent
        app
        stateless
        :mobile-break-point="$vuetify.breakpoint.thresholds.xs"
        :mini-variant="$vuetify.breakpoint.smAndDown"
        :class="{
            'mt-5': $vuetify.breakpoint.smAndDown,
            'mt-6': $vuetify.breakpoint.mdAndUp,
        }"
    >
        <v-list class="py-0">
            <SidebarItem
                v-if="canUpdateProfile"
                :label="
                    isImpersonating
                        ? $t('account.labels.organizations-profile')
                        : $t('account.labels.profile')
                "
                :icon="isImpersonating ? 'business' : 'account_circle'"
                :is-active="$route().current('account.profile.index')"
                :url="$route('account.profile.index')"
            />

            <v-divider />

            <template v-if="!user.impersonating">
                <SidebarItem
                    :label="$t('account.labels.organizations')"
                    icon="corporate_fare"
                    :is-active="$route().current('account.organizations.index')"
                    :url="$route('account.organizations.index')"
                />
                <v-divider />
            </template>

            <template v-if="user.can.access_employees">
                <SidebarItem
                    :label="$t('account.labels.employees')"
                    :icon="$vuetify.icons.employees"
                    :is-active="$route().current('account.employees.index')"
                    :url="$route('account.employees.index')"
                />
                <v-divider />
            </template>

            <template v-if="user.can['manage permissions'] && isImpersonating">
                <SidebarItem
                    :label="$t('account.buttons.manage-access')"
                    icon="vpn_key"
                    :is-active="$route().current('account.access')"
                    :url="$route('account.access')"
                />
                <v-divider />
            </template>

            <template
                v-if="
                    user.can['can manage organization settings'] &&
                    isImpersonating
                "
            >
                <SidebarItem
                    :label="$t('account.labels.media-settings')"
                    icon="video_settings"
                    :is-active="
                        $route().current('account.settings.media.index')
                    "
                    :url="$route('account.settings.media.index')"
                />
                <v-divider />
            </template>

            <template
                v-if="
                    user.can['can manage organization categories'] &&
                    isImpersonating
                "
            >
                <v-list-group
                    no-action
                    :value="$route().current('account.categories.*')"
                    class="category-list-items"
                >
                    <template #activator>
                        <v-list-tile>
                            <v-list-tile-avatar>
                                <v-icon> category </v-icon>
                            </v-list-tile-avatar>
                            <v-list-tile-content
                                v-if="$vuetify.breakpoint.smAndUp"
                            >
                                {{ $t('account.labels.categories') }}
                            </v-list-tile-content>
                        </v-list-tile>
                    </template>
                    <v-list-tile
                        v-for="(category, idx) in categories"
                        :key="`category-${idx}`"
                        active-class="grey lighten-4"
                        :value="
                            $route().current('account.categories.show', {
                                category,
                            })
                        "
                        @click.stop="
                            $inertia.get(
                                $route('account.categories.show', {
                                    category,
                                }),
                                {},
                            )
                        "
                    >
                        <v-list-tile-title class="tw-capitalize ml-4">
                            {{ category }}
                        </v-list-tile-title>
                    </v-list-tile>
                    <v-list-tile
                        v-if="isImpersonating"
                        pl-1
                        ripple
                        color="primary"
                        class="tw-bg-gray-100"
                        :disabled="
                            !user.can['can manage organization categories']
                        "
                        @click.stop="$root.$emit('category-form.show')"
                    >
                        <v-list-tile-title
                            class="tw-capitalize ml-4 tw-flex tw-items-center"
                        >
                            <v-icon>add</v-icon>
                            {{ $t('account.labels.create-category') }}
                        </v-list-tile-title>
                    </v-list-tile>
                </v-list-group>
                <v-divider />
            </template>
            <template
                v-if="
                    user.can['can manage organization settings'] &&
                    isImpersonating
                "
            >
                <SidebarItem
                    :label="$t('account.labels.invoice-settings')"
                    icon="payments"
                    :is-active="
                        $route().current('account.settings.invoice.index')
                    "
                    :url="$route('account.settings.invoice.index')"
                />
                <v-divider />
            </template>
            <template
                v-if="
                    user.can['can manage offer configuration'] &&
                    isImpersonating
                "
            >
                <SidebarItem
                    :label="$t('account.labels.offer-configuration')"
                    icon="request_quote"
                    :is-active="
                        $route().current(
                            'account.settings.offer-configuration.index',
                        )
                    "
                    :url="$route('account.settings.offer-configuration.index')"
                />
                <v-divider />
            </template>
        </v-list>
    </v-navigation-drawer>
</template>
<script>
import SidebarItem from '@/Components/Sidebar/SidebarItem.vue'

export default {
    components: { SidebarItem },

    props: {
        user: {
            type: Object,
            required: true,
            default: () => ({}),
        },
        categories: {
            type: Array,
            default: () => [],
        },
    },

    computed: {
        canUpdateProfile() {
            const user = this.user
            return (
                !user.impersonating ||
                (user.impersonating && user.can['update settings'])
            )
        },

        isImpersonating() {
            return this.user.impersonating
        },

        organizationSettingsLinks() {
            return [
                {
                    name: 'category',
                    icon: 'tune',
                    label: this.$t('account.categories'),
                },
            ]
        },
    },
}
</script>
<style lang="scss">
.category-list-items {
    .v-list__group__items {
        height: 500px;
        max-height: 500px;
        overflow: auto;
    }
}
</style>
